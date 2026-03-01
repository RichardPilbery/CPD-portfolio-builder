<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Document;
use Illuminate\Filesystem\Filesystem;
use App\Events\AuditEntryReadyForDownload;

use App\Models\Audit;
use App\Models\Audititem;
use App\Models\AirwayActivityType;
use App\Models\Capnography;
use App\Models\Ivsite;
use App\Models\Ivtype;
use Carbon\Carbon;

class ProcessAuditDownload implements ShouldQueue
{
    // Don't forget to install Supervisor to keep the job running !
    // ($dir, $user, $audit, $documents, $ampds, $call_type, $outcome, $airway_types, $cap_types, $int_codes, $cap_codes, $dev_codes, $iv_types, $iv_sites, $ai);

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;
    protected $user;
    protected $audit_id;
    private $pandoc_exec_command;
    private $owners;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dir, $user, $audit_id)
    {
        $this->dir = $dir;
        $this->user = $user;
        $this->audit_id = $audit_id;
        $this->pandoc_exec_command = env('PANDOC_PATH', 'PATH=/usr/local/bin:/usr/bin');
        $this->owners = env('FILE_OWNER', 'www-data:www-data');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // Log::debug('ProcessAuditDownaload');
        // Log::debug("Audit id is :".$this->audit_id);
        $audit = Audit::where('id', $this->audit_id)->first();
        // Log::debug($audit);

        $documents = $audit->documents->where('user_id', $this->user->id);
        // Log::debug($documents);

        $airway_types = AirwayActivityType::get()->pluck('name', 'id')->toArray();
        $cap_types = Capnography::get()->pluck('name', 'id')->toArray();

        // Airway types that involve intubation
        $int_codes = [7, 12];
        // Airway types that require capnography
        $cap_codes = [5, 6, 7, 10, 12, 13, 15];
        //dd($airway_types);
        $dev_codes = [1, 2, 5, 6, 7, 10, 11, 13, 15];

        $iv_types = Ivtype::get()->pluck('name','id')->toArray();
        $iv_sites = Ivsite::get()->pluck('name','id')->toArray();

        $ampds = $audit->audititems->where('name', 'AMPDS code')->pluck('name2')->toArray();
        // In case array is null as will get undefined offset error
        $ampds = $ampds == null? "Undefined" : $ampds[0];

        $call_type = $audit->audititems->where('name', 'Call type')->pluck('name2')->toArray();
        // In case array is null as will get undefined offset error
        $call_type = $call_type == null? "Undefined" : $call_type[0];

        $outcome = $audit->audititems->where('name', 'Outcome')->pluck('name2')->toArray();
        $outcome = $outcome == null? "Undefined" : $outcome[0];

        // Organise all the rest of the audititems (exclude airway/vascular for legacy entries)
        $ai = $audit->audititems->whereIn('name', ["Cardiac", "Cardiac - Advanced", "Patient Assessment", "Patient Assessment - Advanced", "Trauma", "Other", "Drugs", "Drugs - Advanced", "HART", "ECP", "Wound closure"])->sortBy(function($x) {
            return [$x->name, $x->name2];
        });

        // Log::debug('We are processing downloads');
        // Log::debug($this->document);

        $new_docs = [];

        $filesystem = new Filesystem; 

        $fulldir = storage_path().'/app/'.$this->dir;

        if($filesystem->isDirectory($fulldir)) {
            $prep_folder_cmd = 'rm -rf '.$fulldir.' 2>&1';
            exec($prep_folder_cmd);
        } 

        $prep_folder_cmd = 'mkdir '.$fulldir.'; chown -R '.$this->owners.' '.$fulldir.' 2>&1';
        exec($prep_folder_cmd);

        if(count($documents)) {
            // This will return an array of docs to send to the markdown file
            $new_docs = $this->prepDocsForDownload($this->dir, $documents);
        }

        $md = view('audit.print', [
            'user' => $this->user, 
            'audit' => $audit,
            'documents' => $documents,
            'ampds' =>      $ampds,
            'call_type' => $call_type,
            'outcome' =>    $outcome,
            'airway_types' => $airway_types,
            'cap_types' => $cap_types,
            'int_codes' => $int_codes,
            'cap_codes' => $cap_codes,
            'dev_codes' => $dev_codes,
            'iv_types' => $iv_types, 
            'iv_sites' => $iv_sites, 
            'ai' => $ai
        ])->render();

        $filename = Carbon::parse($audit->incdatetime)->format('Y-m-d_H-i').'-'.$audit->age.$audit->ageunit.'-'.$audit->sex.'.pdf';
        
        # Save the markdown file
        $path = Storage::put($this->dir.'/markdown.md', $md);

        $full_command = 'cd '.$fulldir.';'.$this->pandoc_exec_command.' pandoc markdown.md -o audit_entry.pdf --from markdown --template eisvogel; chmod -R 777 ./* 2>&1';
        // Log::debug($full_command);
        // Future reference, don't use new Process(['command here']) for
        // single string commands...it escapes them and then they do not work :<

        $process = Process::fromShellCommandline($full_command);
        $process->run();

        if ($process->isSuccessful()) {

            // Log::debug($process->getOutput());
            // Log::debug("Sending event now...");
            AuditEntryReadyForDownload::dispatch($audit);

            return true;

        }
        else {
            Log::debug('It failed sad face');
            throw new ProcessFailedException($process);
        }

    }


        // dir is the tmp directory of the use
    // docs are the documents which need processing and/or copying to tmp location
    public function prepDocsForDownload($dir, $documents) {
        $filesystem = new Filesystem; 

        $filesToReturn = [];
        $fulltmp = storage_path().'/app/'.$dir;

        //Log::debug('Inside prepDocsForDownload');
        // Log::debug($documents);

        foreach($documents as $doc) {
            // Check if not a PDF
            $extra_mimetypes = array(
                'image/png',
                'application/pdf',
                'image/jpeg'
            );

            $mimetype_array = array(
                'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/octet-stream',
                'application/vnd.ms-powerpoint',
                'application/rtf',
                'text/plain'
            );
            // Log::debug($doc);
            if(in_array($doc->mimetype, array_merge($extra_mimetypes, $mimetype_array))) {
                // Copy word files etc to convert folder 
                if(in_array($doc->mimetype, $mimetype_array)) {
                    Log::debug("Dispatching a document for conversion");
                    // ProcessUpload::dispatch($attributes)->delay(now()->addMinutes(2));
                    Log::debug("Dir is $dir");
                    $new_file_name = pathinfo($doc->filepath, PATHINFO_BASENAME);
                    $new_file_name_no_ext = pathinfo($doc->filepath, PATHINFO_FILENAME);
                    Storage::copy($doc->filepath, $dir.'/'.$new_file_name);
                    $command = "";

                    $root = env('APP_URL');
                    // Check if on test server:
                    if($root == "http://localhost" || $root == "http://127.0.0.1") {
                        Log::debug("Test server");
                        $command = "/Applications/LibreOffice.app/Contents/MacOS/soffice --headless --convert-to pdf";
                    } else {
                        Log::debug("We are not on the local server, you need to configure LibreOffice");
                        $command = "/usr/bin/soffice --headless --convert-to pdf";
                    }

                    //$fulldir = storage_path().'/app/'.$convert_dir;
                    

                    $full_command = "cd $fulltmp && $command --outdir $fulltmp *.*";
                    Log::debug($full_command);

                    $process = Process::fromShellCommandline($full_command);
                    $process->run();
            
                    if ($process->isSuccessful()) {
                        Log::debug('Successful conversion');
                        // Log::debug($process->getOutput());
                        array_push($filesToReturn, $fulltmp.'/'.$new_file_name_no_ext.'.pdf');
                    }
                    else {
                        throw new ProcessFailedException($process);
                    }
                } else {
                    Log::debug('No need for conversion');
                    $new_file_name = pathinfo($doc->filepath, PATHINFO_BASENAME);
                    Storage::copy($doc->filepath, $dir.'/'.$new_file_name);
                    array_push($filesToReturn, $fulltmp.'/'.$new_file_name);
                }
            } else {
                Log::debug('Not a supported mimtype: '. $doc->mimetype);
            }
        }

        return $filesToReturn;
    }
}
