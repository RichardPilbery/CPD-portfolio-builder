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
use App\Events\PortfolioEntryReadyForDownload;

class ProcessDownload implements ShouldQueue
{
    // Don't forget to install Supervisor to keep the job running !

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;
    protected $convert_dir;
    protected $document;
    protected $activity_name;
    protected $portfolio;
    protected $comps;
    protected $user;
    protected $swot;
    private $pandoc_exec_command;
    private $owners;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dir, $document, $portfolio, $activity_name , $comps, $user, $swot)
    {
        $this->dir = $dir;
        $this->document = $document;
        $this->convert_dir = $dir.'/convert/';
        $this->activity_name = $activity_name;
        $this->portfolio = $portfolio;
        $this->comps = $comps;
        $this->user = $user;
        $this->swot = $swot;
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

        Log::debug('We are processing downloads');
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

        if(count($this->document)) {
            // This will return an array of docs to send to the markdown file
            $new_docs = $this->prepDocsForDownload($this->dir, $this->convert_dir, $this->document);
        }

        // Log::debug($this->portfolio);
        Log::debug('New docs is');
        Log::debug($new_docs);

        // $md = view('portfolio.print', compact(['portfolio','new_docs', 'activity_name', 'user', 'comps', 'swot']))->render();
        $md = view('portfolio.print', [
            'portfolio' => $this->portfolio,
            'documents'=>$new_docs,
            'activity_name'=>$this->activity_name,
            'user'=>$this->user,
            'comps'=> $this->comps,
            'swot'=>$this->swot
        ])->render();
        # Save the markdown file
        $path = Storage::put($this->dir.'/markdown.md', $md);

        # Run the exec command.
        #Log::debug(exec('cd '.$fulldir.'; pandoc markdown.md -o cpd_profile.pdf --from markdown --template eisvogel 2>&1'));
        

        // Seems to be a permissions issue when ProcessDownload creates the convert folder.
        // See if this sorts - it didn't
        $full_command = 'cd '.$fulldir.';'.$this->pandoc_exec_command.' pandoc markdown.md -o portfolio_entry.pdf --from markdown --template eisvogel; chmod -R 777 ./* 2>&1';
        Log::debug($full_command);
        // Future reference, don't use new Process(['command here']) for
        // single string commands...it escapes them and then they do not work :<


        $process = Process::fromShellCommandline($full_command);
        $process->run();

        if ($process->isSuccessful()) {

            // Log::debug($process->getOutput());
            Log::debug("Sending event now...");
            PortfolioEntryReadyForDownload::dispatch($this->portfolio);

            return true;

        }
        else {
            Log::debug('It failed sad face');
            throw new ProcessFailedException($process);
        }

    }


        // dir is the tmp directory of the use
    // docs are the documents which need processing and/or copying to tmp location
    public function prepDocsForDownload($dir, $convert_dir, $documents) {
        $filesystem = new Filesystem; 

        $filesToReturn = [];
        $fulltmp = storage_path().'/app/'.$dir;

        Log::debug('Inside prepDocsForDownload');
        Log::debug($documents);

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
                    Log::debug("Root is ".$root);
                    if($root == "http://localhost:8000" || $root == "http://127.0.0.1:8000") {
                        Log::debug("Test server");
                        $command = "/Applications/LibreOffice.app/Contents/MacOS/soffice --headless --convert-to pdf";
                    } else {
                        Log::debug("We are not on the local server, you need to configure LibreOffice");
                        $command = "/usr/bin/soffice --headless --convert-to pdf";
                    }

                    //$fulldir = storage_path().'/app/'.$convert_dir;
                    

                    $full_command = "cd $fulltmp && $command $new_file_name --outdir $fulltmp";
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
