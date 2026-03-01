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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\Filesystem;
use App\Events\CpdProfileReadyForDownload;
use App\Models\Portfolio;
use App\Models\Document;

class ProcessCpdProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $audit;
    protected $pandoc_exec_command;
    protected $owners;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($audit, $user)
    {
        $this->audit = $audit;
        $this->user = $user;
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
        Log::debug('We are preparing a CPD profile');
        $twoyears = Carbon::now()->subYears(2);
        // Log::debug($this->user['id']);

        $portfolios = Portfolio::with('documents')
            ->where('user_id', $this->user['id'])
            ->where('actdate','>=', $twoyears)
            ->orderBy('actdate', 'desc')->get();

        // # Delete tmp dir
        $dir = 'store/'.$this->user['id'].'/tmp';
        # Delete the tmp directory first
        $filesystem = new Filesystem;
        $fulldir = storage_path().'/app/'.$dir;

        if($filesystem->isDirectory($fulldir)) {
            $prep_folder_cmd = 'rm -rf '.$fulldir.' 2>&1';
            exec($prep_folder_cmd);
        } 

        $prep_folder_cmd = 'mkdir '.$fulldir.'; chown -R '.$this->owners.' '.$fulldir.' 2>&1';
        exec($prep_folder_cmd);

        $result = [];

        // Loop through every eligible portfolio entry, process portfolio and documents
        foreach($portfolios as $p) {
            // Special treatment just for those selected to be in the profile
            if($p->profile == 1) {
                $result[$p->id] = $this->processportfolio($p);
            //dd($result);
                if(isset($p->swot)) {
                    $result[$p->id]['swot'] = $p->swot;
                }

                //dd($p->documents()->get());
                // Need to save copy of attached docs to tmp file too.
                if(count($p['documents'])) {
                    Log::debug($p->documents()->get());
                    $docs = $this->prepDocsForDownload($dir, $dir, $p['documents'], 1);
                    $result[$p->id]['docs'] = $docs;
                } else {
                    $result[$p->id]['docs'] = null;
                }
            }
        }

        // Log::debug($result);

        // Get user details and summary
        $summary = $this->user->summary;
        $hcpcaudit = $this->audit; 

        // Fix summary multiple newlines
        $summary->job_description = str_replace("\r\n","\n", $summary->job_description);
        $summary->job_description = str_replace("\n\n","\n", $summary->job_description);
        if($hcpcaudit == 1) {
            $summary->job_description = str_replace("\n","\n    +  ", $summary->job_description);
        } else {
            $summary->job_description = str_replace("\n","\n+  ", $summary->job_description);
        }

        $fulldir = storage_path().'/app/'.$dir;


        // Process and generate full profile

        # Put logo
        if(!Storage::exists($dir.'/logo.png')) {
            Storage::copy('public/logo.png', $dir.'/logo.png');
        }

        $md = view('portfolio.profilesection3text', ['portfolios'=>$portfolios,'result'=>$result, 'user'=>$this->user, 'summary'=>$summary, 'hcpcaudit'=>$this->audit])->render();
        # Save the markdown file
        $path = Storage::put($dir.'/section3.md', $md);
        $full_command = 'cd '.$fulldir.';'.$this->pandoc_exec_command.' pandoc section3.md -o section3.txt --from markdown 2>&1';
        // Log::debug($full_command);
        // Future reference, don't use new Process(['command here']) for
        // single string commands...it escapes them and then they do not work :<
        exec($full_command);


        $md = view('portfolio.profilepdf', ['portfolios'=>$portfolios,'result'=>$result, 'user'=>$this->user, 'summary'=>$summary, 'hcpcaudit'=>$this->audit])->render();
        # Save the markdown file
        $path = Storage::put($dir.'/markdown.md', $md);

        # Run the exec command.
        #Log::debug(exec('cd '.$fulldir.'; pandoc markdown.md -o cpd_profile.pdf --from markdown --template eisvogel 2>&1'));

        $full_command = 'cd '.$fulldir.';'.$this->pandoc_exec_command.' pandoc markdown.md -o cpd_profile.pdf --from markdown --template eisvogel 2>&1';
        // Log::debug($full_command);
        // Future reference, don't use new Process(['command here']) for
        // single string commands...it escapes them and then they do not work :<
        exec($full_command);

        // Process summary table

        $md_summary = view('portfolio.profilesummarytable', ['result'=>$result, 'user'=>$this->user, 'summary'=>$summary, 'hcpcaudit'=>$this->audit])->render();
        # Save the md_summary file
        $summary_path = Storage::put($dir.'/markdown_summary.md', $md_summary);

        exec('cd '.$fulldir.';'.$this->pandoc_exec_command.' pandoc markdown_summary.md -o cpd_profile_summary.pdf --from markdown --template eisvogel 2>&1');


        // Process evidence

        $md_evidence = view('portfolio.profileevidence', ['portfolios'=>$portfolios,'result'=>$result, 'user'=>$this->user, 'summary'=>$summary, 'hcpcaudit'=>$this->audit])->render();
        # Save the md_summary file
        $evidence_path = Storage::put($dir.'/markdown_evidence.md', $md_evidence);

        exec('cd '.$fulldir.';'.$this->pandoc_exec_command.' pandoc markdown_evidence.md -o cpd_profile_evidence.pdf --from markdown --template eisvogel 2>&1');

        Log::debug("Despatching ready for download message to ".$this->user['id']);
        CpdProfileReadyForDownload::dispatch($this->user['id']);
    }

    public function processportfolio(Portfolio $portfolio) {

        // Get the IDs of any included competencies
        $clfs_id = $portfolio->clfs()->orderBy('name')->pluck('clfs.id');
        $ksfs_id = $portfolio->ksfs()->orderBy('name')->pluck('ksfs.id');
    
        // Custom SQL query to find the name and description of the compentencies
        // and insert them into an array
        //https://laravel.com/docs/6.x/queries#where-clauses
    
        if(count($clfs_id)) {
            $clfs = DB::table('clfs')->selectRaw('concat("CLF-",id) newid, concat(name,": ", domain, ": ", element) comp')->whereIn('id', $clfs_id)->pluck('comp','newid');
            //dd($clfs);
        }
        if(count($ksfs_id)) {
            $ksfs =  DB::table('ksfs')->selectRaw('concat("KSF-",id) newid, concat(name,": ", description) comp')->whereIn('id', $ksfs_id)->pluck('comp','newid');
        }
    
        // Combine all competencies
        $comps = [];
    
        if(count($clfs_id)) {
            if(count($ksfs_id)) {
                $comps = $clfs->merge($ksfs);
            }
            else {
                $comps = $clfs;
            }
        } else {
            if(count($ksfs_id)) {
                $comps = $ksfs;
            }
        }
    
        $activity_name = $portfolio->activity()->get()->pluck('name')[0];
    
        $portfolio->description = str_replace(array('#','##','###','*'), '', $portfolio->description);
        $portfolio->benefit = str_replace(array('#','##','###','*'), '', $portfolio->benefit);
    
        return(['portfolio' => $portfolio, 'comps' => $comps, 'activity' => $activity_name]);
    
    }

    public function prepDocsForDownload($dir, $convert_dir, $documents, $profile = 0) {
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
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
                    if($root == "http://localhost:8000" || $root == "http://127.0.0.1:8000") {
                        Log::debug("Test server");
                        $command = "/Applications/LibreOffice.app/Contents/MacOS/soffice --headless --convert-to pdf";
                    } else {
                        Log::debug("We are not on the local server, you need to configure LibreOffice");
                        $command = "/usr/bin/soffice --headless --convert-to pdf";
                    }

                    $full_command = "cd $fulltmp && $command $new_file_name --outdir $fulltmp";
                    Log::debug($full_command);

                    $process = Process::fromShellCommandline($full_command);
                    $process->run();
            
                    if ($process->isSuccessful()) {
                        Log::debug('Successful conversion');
                        // Log::debug($process->getOutput());
                        if($profile == 1) {
                            array_push($filesToReturn, ['filepath'=>$fulltmp.'/'.$new_file_name_no_ext.'.pdf', 'title'=>$doc['title'], 'format'=>$doc['format']]);
                        } else {
                            array_push($filesToReturn, $fulltmp.'/'.$new_file_name_no_ext.'.pdf');
                        }
                        
                    }
                    else {
                        throw new ProcessFailedException($process);
                    }
                } else {
                    Log::debug('No need for conversion');
                    $new_file_name = pathinfo($doc->filepath, PATHINFO_BASENAME);
                    $new_file_name_no_ext = pathinfo($doc->filepath, PATHINFO_FILENAME);

                    Storage::copy($doc->filepath, $dir.'/'.$new_file_name);
                    if($profile == 1) {
                        array_push($filesToReturn, ['filepath'=>$fulltmp.'/'.$new_file_name, 'title'=>$doc['title'], 'format'=>$doc['format']]);
                    } else {
                        array_push($filesToReturn, $fulltmp.'/'.$new_file_name);
                    }
                }
            } else {
                Log::debug('Not a supported mimtype: '. $doc->mimetype);
            }

        }

        return $filesToReturn;
    }
}


