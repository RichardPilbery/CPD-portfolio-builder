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
use App\Events\AirwayLogReadyForDownload;
use App\Models\Audit;
use App\Models\Audititem;
use App\Models\AirwayActivityType;
use App\Models\Capnography;
use App\Models\User;

class ProcessAirwayLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dir;
    protected $user_id;
    protected $start;
    protected $end;
    protected $pandoc_exec_command;
    protected $owners;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dir, $user_id, $start, $end)
    {
        $this->dir = $dir;
        $this->user_id = $user_id;
        $this->start = $start;
        $this->end = $end;
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
        Log::debug('We are preparing a Airway Log');
        Log::debug('Start is '.$this->start.' and End is '.$this->end);

        $audits = Audit::has('airways')
            ->with('airways')
            ->where('user_id',$this->user_id)
            ->where('incdatetime', '>=', $this->start)
            ->where('incdatetime', '<=', $this->end.' 23:59:59')
            ->orderBy('incdatetime', 'desc')
            ->get();

        // dd($audits);

        $airways = [];
        foreach($audits as $audit) {
            foreach($audit->airways as $a) {
                $airways[] = [
                    'id'                => $audit->id,
                    'incdatetime'       => $audit->incdatetime, 
                    'incnumber'         => $audit->incnumber, 
                    'simulation'        => $audit->simulation, 
                    'age'               => $audit->age.' '.$audit->ageunit,
                    'sex'               => $audit->sex,
                    'airwaytype_id'     => $a->airwaytype_id,
                    'success'           => $a->success,
                    'grade'             => $a->grade,
                    'size'              => $a->size,
                    'bougie'            => $a->bougie,
                    'capnography_id'    => $a->capnography_id,
                    'notes'             => $a->notes
                ];
            }
        }

        $user = User::where('id', $this->user_id)->first(); // Can't use auth()->user() here
        $date = Carbon::now()->format('d-m-Y');
        $airway_types = AirwayActivityType::get()->pluck('name', 'id')->toArray();
        $cap_types = Capnography::get()->pluck('name', 'id')->toArray();
        $start = $this->start;
        $end = $this->end;

        $md = view('audit.printairway', compact(['user', 'date', 'airways', 'start', 'end', 'airway_types','cap_types']))->render();

        $filesystem = new Filesystem; 

        $fulldir = storage_path().'/app/'.$this->dir;

        if($filesystem->isDirectory($fulldir)) {
            $prep_folder_cmd = 'rm -rf '.$fulldir.' 2>&1';
            exec($prep_folder_cmd);
        } 

        $prep_folder_cmd = 'mkdir '.$fulldir.'; chown -R '.$this->owners.' '.$fulldir.' 2>&1';
        exec($prep_folder_cmd);

        // # Save the markdown file
        $path = Storage::put($this->dir.'/markdown.md', $md);

        $fulldir = storage_path().'/app/'.$this->dir;

        // # Run the exec command.
        $command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md -o airway_log.pdf --from markdown --template eisvogel -V geometry:landscape -V geometry:left=1cm -V geometry:right=1cm 2>&1';

        // Log::debug($command);
        exec($command);
        // //dd($md);

        Log::debug("Despatching Airway Log ready for download message to ".$this->user_id);

        AirwayLogReadyForDownload::dispatch($this->user_id);
    }


  
}


