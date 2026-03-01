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
use App\Events\AuditLogReadyForDownload;
use App\Models\Audit;
use App\Models\Audititem;
use App\Models\AirwayActivityType;
use App\Models\Capnography;
use App\Models\Ivsite;
use App\Models\Ivtype;
use App\Models\User;

class ProcessAuditLog implements ShouldQueue
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
        Log::debug('We are preparing a Audit Log');
        Log::debug('Start is '.$this->start.' and End is '.$this->end);

        $audits = Audit::with(['airways', 'vasculars', 'audititems'])
            ->where('user_id',$this->user_id)
            ->where('incdatetime', '>=', $this->start)
            ->where('incdatetime', '<=', $this->end)
            ->orderBy('incdatetime', 'desc')
            ->get();

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

        $print_audit = [];
        $airways = [];
        $vasculars = [];

        foreach($audits as $audit) {
            $ampds = $audit->audititems->where('name', 'AMPDS code')->pluck('name2')->toArray();
            $ampds = $ampds == null? "Undefined" : $ampds[0];          
            $call_type = $audit->audititems->where('name', 'Call type')->pluck('name2')->toArray();
            // In case array is null as will get undefined offset error
            $call_type = $call_type == null? "Undefined" : $call_type[0];

            $outcome = $audit->audititems->where('name', 'Outcome')->pluck('name2')->toArray();
            $outcome = $outcome == null? "Undefined" : $outcome[0];

            $ai = $audit->audititems->whereIn('name', ["Cardiac", "Cardiac - Advanced", "Patient Assessment", "Patient Assessment - Advanced", "Trauma", "Other", "Drugs", "Drugs - Advanced", "HART", "ECP", "Wound closure"])->sortBy(function($x) {
                return [$x->name, $x->name2];
            });

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

            foreach($audit->vasculars as $v) {
                $vasculars[] = [
                    'id'                => $audit->id,
                    'incdatetime'       => $audit->incdatetime, 
                    'incnumber'         => $audit->incnumber, 
                    'simulation'        => $audit->simulation, 
                    'age'               => $audit->age.' '.$audit->ageunit,
                    'sex'               => $audit->sex,
                    'ivtype'            => $iv_types[$v->ivtype_id],
                    'success'           => $v->success,
                    'size'              => $v->size,
                    'ivsite'            => $iv_sites[$v->ivsite_id]
                ];
            }

            $print_audit[] = [
                'id'                => $audit->id,
                'incdatetime'       => $audit->incdatetime, 
                'incnumber'         => $audit->incnumber, 
                'simulation'        => $audit->simulation, 
                'age'               => $audit->age.' '.$audit->ageunit,
                'sex'               => $audit->sex,
                'ampds'             => $ampds,
                'call_type'         => $call_type,
                'outcome'           => $outcome,
                'audititems'        => $this->convertAuditItems($ai),
                'notes'             => $audit->notes
            ];
            
        }

        //dd($print_audit);

        $user = User::where('id', $this->user_id)->first();
        // Log::debug('ProcessAuditLog');
        // Log::debug('User id is '.$this->user_id);
        // Log::debug($user);

        $date = Carbon::now()->format('d-m-Y');
        $airway_types = AirwayActivityType::get()->pluck('name', 'id')->toArray();
        $cap_types = Capnography::get()->pluck('name', 'id')->toArray();
        $start = $this->start;
        $end = $this->end;

        $md = view('audit.printlog', compact(['print_audit', 'user', 'date', 'start', 'end', 'airways', 'airway_types', 'cap_types', 'vasculars']))->render();

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
        $command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md -o audit_log.pdf --from markdown --template eisvogel -V geometry:landscape -V geometry:left=1cm -V geometry:right=1cm 2>&1';

        exec($command);

        Log::debug("Despatching Audit Log ready for download message to ".$this->user_id);
        AuditLogReadyForDownload::dispatch($this->user_id);
    }

    public function convertAuditItems($ai) {
        $oldheading = "";
        $newheading = "";

        $final_string = "";
        $x = 1;

        foreach($ai as $a) {
            if($a->name != $oldheading) {
                // This will work except for the first loop
                $newheading = $a->name;
                $oldheading = $newheading;
                if($x == 1) {
                    $oldheading = $a->name;
                }
                $final_string .= "**$newheading:** ";
                }

            $final_string .= "$a->name2; ";
            $x ++;
        }

        return $final_string;
    }
  
}


