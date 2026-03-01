<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Audititem;
use App\Models\AirwayActivityType;
use App\Models\Capnography;
use App\Models\Ivsite;
use App\Models\Ivtype;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\Filesystem;
use App\Jobs\ProcessAuditDownload;
use App\Jobs\ProcessAuditLog;
use App\Jobs\ProcessAirwayLog;

class AuditController extends Controller
{
    // Change for production
    private $pandoc_exec_command;

    function __construct($pandoc_exec_command = '') {
        $this->pandoc_exec_command = url('/') == "http://127.0.0.1:8000" || url('/') == "http://localhost:8000" ? "PATH=/opt/homebrew/bin:/Library/TeX/texbin" : 'PATH=/usr/local/texlive/2022/bin/x86_64-linux:/usr/bin';
    }

    public function search($query) {

        //Log::debug("Inside the audit search function");
        $result = Audit::where('user_id', auth()->user()->id)
            ->where(function ($q) use ($query) {
                $q->where('incnumber', 'like', '%'.$query.'%')
                  ->orWhere('incdatetime', 'like', '%'.$query.'%')
                  ->orWhere('provdiag', 'like', '%'.$query.'%')
                  ->orWhere('note', 'like', '%'.$query.'%');
            })
            ->orderBy('incdatetime', 'desc')
            ->select(['id','incdatetime', 'provdiag'])
            ->limit(10)
            ->get();

        //Log::debug($result);

        return response()->json($result);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $userid = $user->id;
        $audits = Audit::with('audititems')->where('user_id', auth()->user()->id)->orderBy('incdatetime', 'DESC')->paginate(25);
        $first_entry_query = Audit::with(['airways', 'vasculars'])->where('user_id',$user->id);
        $start = $first_entry_query->count() > 0 ? $first_entry_query->first()->incdatetime : now();
        $end = now();
        $show_download = $first_entry_query->count() > 0 ? true : false;
        $audittype = 'audit';

        $ampds = Audititem::where('name', 'AMPDS code')->pluck('name2', 'id')->toArray();
        asort($ampds);

        return view('audit.index', compact(['audits', 'ampds', 'show_download', 'userid', 'start', 'end', 'audittype']));
    }

    public function summary()
    {
        $user = auth()->user();
        $userid = $user->id;
        $audits = Audit::with('audititems')->where('user_id', auth()->user()->id)->orderBy('incdatetime', 'DESC')->paginate(25);
        $first_entry_query = Audit::with(['airways', 'vasculars'])->where('user_id',$user->id);
        $start = $first_entry_query->count() > 0 ? $first_entry_query->first()->incdatetime : now();
        $end = now();
        $show_download = $first_entry_query->count() > 0 ? true : false;
        $audittype = 'audit';

        $ampds = Audititem::where('name', 'AMPDS code')->pluck('name2', 'id')->toArray();
        asort($ampds);

        return view('audit.summary', compact(['audits', 'ampds', 'show_download', 'userid', 'start', 'end', 'audittype']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Log::debug("Create an audit entry");

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

        $ampds = Audititem::where('name', 'AMPDS code')->pluck('name2', 'id')->toArray();
        asort($ampds);

        $call_type = Audititem::where('name', 'Call type')->pluck('name2', 'id')->toArray();

        $outcome = Audititem::where('name', 'Outcome')->pluck('name2', 'id')->toArray();
        asort($outcome);
        //dd($outcome);

        // // Organise all the rest of the audititems (exclude airway/vascular for legacy entries)
        $ai = Audititem::whereIn('name', ["Breathing", "Breathing - Advanced", "Cardiac", "Cardiac - Advanced", "Patient Assessment", "Patient Assessment - Advanced", "Trauma", "Other", "Drugs", "Drugs - Advanced", "HART", "ECP", "Wound closure"])->orderByRaw('name, name2')->cursor(function($x) {
             return [$x->name2];
         });

         // dd($ai->toArray());

         $list_of_skills = '';
         $i = 0;
         $current_heading = '';
         foreach($ai as $value) {
            if($value['name'] != $current_heading) {
                $current_heading = $value['name'];
                $list_of_skills .= '<br><br><b>'.$value['name'].': </b>'.$value['name2'].'; ';
            } else {
                $list_of_skills .= $value['name2'].'; ';
            }
         }

         //dd($list_of_skills);

         //$ai_arrange = $ai->orderBy('name');

         $ai2 = $ai->pluck('name2', 'id');
         // dd($ai->toArray());

        return view('audit.create', compact(['ampds','call_type','outcome','airway_types','cap_types', 'int_codes', 'cap_codes','dev_codes','iv_types', 'iv_sites', 'ai2', 'list_of_skills']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log::debug("At store");
        // dd($request->all());

        $request->validate([
            'incdatetime' => 'required|date_format:"Y-m-d\TH:i"',
            'age' => 'nullable|numeric',
            'docupload.*' => 'mimes:pdf,jpeg,png,docx,pptx,bin|max:5000'
        ]);


        $request['user_id'] = auth()->user()->id;
        $request['simulation'] = $request['simulation'] == 'on' ? 1 : 0;

        $attributes = request(['age', 'ageunit', 'sex', 'incdatetime', 'incnumber', 'simulation', 'provdiag', 'note']);
        $audit = auth()->user()->audits()->create($attributes);

        if(isset($request->skill) && count($request->skill) > 0) {
            // Add skills
            // Can use attach() for this https://laravel.com/docs/6.x/eloquent-relationships#the-create-method
            $skill_ids = array_keys($request->skill);
            $audit->audititems()->attach($skill_ids);
        }

        // Add outcome, AMPDS code, call type
        if(isset($request->outcome)) {
            $audit->audititems()->attach($request->outcome);
        }

        if(isset($request->ampds)) {
            $audit->audititems()->attach($request->ampds);
        }

        if(isset($request->call_type)) {
            $audit->audititems()->attach($request->call_type);
        }

        // Check for Airway items
        if(isset($request->airway) && count($request->airway) > 0) {
            foreach($request->airway as $a) {
                Log::debug('Key is  and value is ' . $a['airwaytype_id']);
                $audit->airways()->create([
                    'airwaytype_id' => $a['airwaytype_id'],
                    'success' => $a['success'],
                    'grade' => $a['grade'],
                    'size' => $a['size'],
                    'bougie' => $a['bougie'],
                    'capnography_id' => $a['capnography_id'] == 0 ? null : $a['capnography_id'],
                    'notes' => $a['notes']
                ]);
            };
        };

        // Check for Vacular items
        if(isset($request->vascular) && count($request->vascular) > 0) {
            foreach($request->vascular as $v) {
                $audit->vasculars()->create([
                    'ivtype_id' => $v['ivtype_id'],
                    'success' => $v['success'],
                    'size' => $v['size'],
                    'ivsite_id' => $v['ivsite_id'],
                    'location' => $v['location']
                ]);
            };
        };

        // Need to check if doctitle has been completed as
        // still may have details even if file not uploaded.

       if(!empty($request->file('docupload'))) {
            //dd("We have files");
            $files = $request->file('docupload');
            //dd($files);
            foreach($request->file('docupload') as $file) {
                //dd($file->getMimeType());
                $path = Storage::putFile('store/'.auth()->user()->id, $file);
                // Need to get all info required for Document entry
                // set subject_type to App\Models\Audit
                $doc = new Document();
                $savedoc = $doc->saveDocument(
                    ($request->root() == "http://127.0.0.1:8000")? 1 : 0,
                    $request->doctitle,
                    $request->docdescription,
                    $request->docformat,
                    $file->getMimeType(),
                    $file->getSize(),
                    $file->getClientOriginalName(),
                    $path,
                    "App\Models\Audit",
                    $audit->id,
                    auth()->user()->id
                );
            }
        } else {
            if(!empty($request->doctitle) || !empty($request->docdescription) || !empty($request->docformat)) {
                // need to create Document with these details in.
                $doc = new Document();
                $savedoc = $doc->saveDocument(
                    $request->root() == "http://127.0.0.1:8000"? 1 : 0,
                    $request->doctitle,
                    $request->docdescription,
                    $request->docformat,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    "App\Models\Audit",
                    $audit->id,
                    auth()->user()->id
                );

            }
        }

        return redirect('/audit')->with('success', 'Your audit entry has been created');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Audit  $audit
     * @return \Illuminate\Http\Response
     */
    public function show(Audit $audit)
    {
        // Log::debug("Inside audit show");
        $this->authorize('view', $audit);

        //dd($audit->audititems);
        //dd($audit->vasculars);
        $airway_types = AirwayActivityType::get()->pluck('name', 'id')->toArray();
        $cap_types = Capnography::get()->pluck('name', 'id')->toArray();
        // Log::debug($cap_types);
        // Log::debug($audit);

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
        //dd($ai);

        // Always null when seeding using factory
        $documents = $audit->documents->where('user_id', auth()->user()->id);

        return view('audit.show', compact(['audit', 'documents','ampds','call_type','outcome','airway_types','cap_types', 'int_codes', 'cap_codes','dev_codes','iv_types', 'iv_sites', 'ai']));
    }


    // Provide link to download portfolio_entry.pdf file once created.
    public function downloadentry(Audit $audit) {
        // Log::debug('Download audit entry functino');

        $this->authorize('view', $audit);

        $dir = 'store/'.auth()->user()->id.'/tmp/';

        return Storage::download($dir.'audit_entry.pdf');
    }

    public function downloadlog($logtype) {

        // Log::debug('Download audit entry function');

        // $this->authorize('view', $audit);

        $dir = 'store/'.auth()->user()->id.'/tmp/';

        if($logtype == "audit") {
            return Storage::download($dir.'audit_log.pdf');
        } else {
            return Storage::download($dir.'airway_log.pdf');
        }
        
    }

    public function airway() {
        Log::debug("Airway log");

        $userid = auth()->user()->id;

        // Get audit entries that have associated airway entries
        $audits = Audit::has('airways')->with('airways')->where('user_id',$userid)->orderBy('incdatetime', 'desc')->paginate(25);
        // $first_entry = Audit::has('airways')->with('airways')->where('user_id',$user_id)->first()->incdatetime;
        $first_entry_query = Audit::has('airways')->where('user_id',$userid);
        $first_entry = $first_entry_query->count() > 0 ? $first_entry_query->first()->incdatetime : now();
        $end = now();
        $show_airwaylog = $first_entry_query->count() > 0 ? true : false;
        //dd($audits);
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

        // dd($airways);
        $airway_types = AirwayActivityType::get()->pluck('name', 'id')->toArray();
        $cap_types = Capnography::get()->pluck('name', 'id')->toArray();

        // Airway types that involve intubation
        $int_codes = [7, 12];
        // Airway types that require capnography
        $cap_codes = [5, 6, 7, 10, 12, 13, 15];
        //dd($airway_types);
        $dev_codes = [1, 2, 5, 6, 7, 10, 11, 13, 15];
        $audittype = 'airway';

         //dd($ai2->toArray());

        return view('audit.airway', compact(['airways', 'audits', 'airway_types','cap_types', 'int_codes', 'cap_codes', 'first_entry', 'show_airwaylog', 'audittype', 'userid', 'end']));
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

    public function downloadairway(Request $request) {
        //dd($request->all());

        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $user_id = auth()->user()->id;
        // Get audit entries that have associated airway entries
        $dir = 'store/'.$user_id.'/tmp';

        ProcessAirwayLog::dispatch($dir, $user_id, $request->start, $request->end);

    }

    public function download(Request $request) {
        //dd($request->all());

        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $user_id = auth()->user()->id;
        // Get audit entries that have associated airway entries
        $dir = 'store/'.$user_id.'/tmp';

        ProcessAuditLog::dispatch($dir, $user_id, $request->start, $request->end);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Audit  $audit
     * @return \Illuminate\Http\Response
     */
    public function edit(Audit $audit)
    {

        $this->authorize('view', $audit);

        $selaudititems = $audit->audititems()->get()->toArray();

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

        $ampds = Audititem::where('name', 'AMPDS code')->pluck('name2','id')->toArray();
        asort($ampds);

        $call_type = Audititem::where('name', 'Call type')->pluck('name2', 'id')->toArray();

        $outcome = Audititem::where('name', 'Outcome')->pluck('name2', 'id')->toArray();
        asort($outcome);

        # Get previously selected values
        $sel_ampds = $audit->audititems->where('name', 'AMPDS code')->pluck('name2')->toArray();
        //dd($sel_ampds[0]);
        $sel_call_type = $audit->audititems->where('name', 'Call type')->pluck('name2')->toArray();
        //dd($sel_call_type);
        $sel_outcome = $audit->audititems->where('name', 'Outcome')->pluck('name2')->toArray();
        //dd($sel_outcome);

        // // Organise all the rest of the audititems (exclude airway/vascular for legacy entries)
        $ai = Audititem::whereIn('name', ["Cardiac", "Cardiac - Advanced", "Patient Assessment", "Patient Assessment - Advanced", "Trauma", "Other", "Drugs", "Drugs - Advanced", "HART", "ECP", "Wound closure"])->cursor(function($x) {
            return [$x->name2];
        });
        $ai2 = $ai->pluck('name2', 'id');

        $sel_ai = $audit->audititems->whereIn('name', ["Cardiac", "Cardiac - Advanced", "Patient Assessment", "Patient Assessment - Advanced", "Trauma", "Other", "Drugs", "Drugs - Advanced", "HART", "ECP", "Wound closure"])->sortBy(function($x) {
            return [$x->name, $x->name2];
        });

        $sel_ai2 = $sel_ai->pluck('name2', 'id');

        // Check for Airway items

        $airways = $audit->airways()->get();
        //dd($airways);
        $vasculars = $audit->vasculars()->get();
        //dd($vasculars);


        $documents = $audit->documents->where('user_id', auth()->user()->id);

        return view('audit.edit', compact(['audit', 'documents','ampds','call_type','outcome','airway_types','cap_types', 'int_codes', 'cap_codes','dev_codes','iv_types', 'iv_sites', 'ai2', 'sel_ampds', 'sel_call_type', 'sel_outcome', 'sel_ai2', 'airways', 'vasculars']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Audit  $audit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Audit $audit)
    {
        $this->authorize('update', $audit);

        // dd($request->all());

       $request->validate([
           'incdatetime' => 'required|date_format:"Y-m-d\TH:i"',
           'docupload.*' => 'mimes:pdf,jpeg,png,docx,pptx,bin|max:5000'
       ]);


       $request['user_id'] = auth()->user()->id;
       $request['simulation'] = $request['simulation'] == 'on' ? 1 : 0;

       $attributes = request(['age', 'ageunit', 'sex', 'incdatetime', 'incnumber', 'simulation', 'provdiag', 'note']);

       $audit->update($attributes);

       // Remove existing audititems
       $audit->audititems()->sync([]);

       if(isset($request->skill) && count($request->skill) > 0) {
           // Add skills
           // Can use attach() for this https://laravel.com/docs/6.x/eloquent-relationships#the-create-method
           $skill_ids = array_keys($request->skill);
           $audit->audititems()->attach($skill_ids);
       }

       // Add outcome, AMPDS code, call type
       if(isset($request->outcome)) {
           $audit->audititems()->attach($request->outcome);
       }

       if(isset($request->ampds)) {
           $audit->audititems()->attach($request->ampds);
       }

       if(isset($request->call_type)) {
           $audit->audititems()->attach($request->call_type);
       }

       // Check for Airway items
       // Remove existing airways first
       // Note that if airways are deleted, they will not trigger the if statement
       // below so run delete anyway
       $audit->airways()->delete();

       if(isset($request->airway) && count($request->airway) > 0) {
           
           foreach($request->airway as $a) {
               Log::debug($a);
               $audit->airways()->create([
                   'airwaytype_id' => $a['airwaytype_id'],
                   'success' => $a['success'],
                   'grade' => $a['grade'],
                   'size' => $a['size'],
                   'bougie' => $a['bougie'],
                   'capnography_id' => $a['capnography_id'] == 0 ? null : $a['capnography_id'],
                   'notes' => $a['notes']
               ]);
           };
       };

       // Check for Vacular items
       // Remove existing airways first
       $audit->vasculars()->delete();
       if(isset($request->vascular) && count($request->vascular) > 0) {
            
           foreach($request->vascular as $v) {
                Log::debug($v);
               $audit->vasculars()->create([
                   'ivtype_id' => $v['ivtype_id'],
                   'success' => $v['success'],
                   'size' => $v['size'],
                   'ivsite_id' => $v['ivsite_id'],
                   'location' => $v['location']
               ]);
           };
       };


       if(isset($request->documents) || isset($audit->documents)) {
            // When deleting last portfolio document, $request->docs will not trigger
            // So need extra case
            // Log::debug('Documents already uploded');

            if(!isset($request->documents)) {
                // Delete all docs
                foreach($audit->documents as $d) {
                    // Log::debug("Delete id: ".$d->id);
                    Storage::delete($d->filepath);
                    $audit->documents()->where('id', $d->id)->delete();
                }
            } else {
                [$keys, $values] = Arr::divide($request->documents);

                foreach($audit->documents as $d) {
                    // Log::debug("There are portfolio documents");
                    if(isset($keys) && in_array($d->id, $keys)) {
                        Log::debug("Keep id: ".$d->id);
                    }
                    else {
                        // Log::debug("Delete id: ".$d->id);
                        Storage::delete($d->filepath);
                        $audit->documents()->where('id', $d->id)->delete();
                    }
                }
            }
        }

        // Process newly uploaded docs.

        if(!empty($request->file('docupload'))) {
            // Log::debug("Docs have been newly uploaded");
            //dd("We have files");
            $files = $request->file('docupload');
            //dd($files);
            foreach($request->file('docupload') as $file) {
                //dd($file->getMimeType());
                $path = Storage::putFile('store/'.auth()->user()->id, $file);
                // Need to get all info required for Document entry
                // set subject_type to App\Models\Portfolio
                // set subject_id to id of portfolio entry, so need to
                // create this first
                $doc = new Document();
                $savedoc = $doc->saveDocument(
                    ($request->root() == "http://127.0.0.1:8000")? 1 : 0,
                    $request->doctitle,
                    $request->docdescription,
                    $request->docformat,
                    $file->getMimeType(),
                    $file->getSize(),
                    $file->getClientOriginalName(),
                    $path,
                    "App\Models\Audit",
                    $audit->id,
                    auth()->user()->id
                );

                // Log::debug("Save doc says:");
                // Log::debug($savedoc);

            }
        } else if(!empty($request->doctitle) || !empty($request->docdescription) || !empty($request->docformat)) {
                // Need to check if document
                // need to create Document with these details in.
                // Log::debug('Doc title not empty');
                // Log::debug($audit->documents);

            if(count($audit->documents) == 0) {
                // Log::debug("Create new empty docs");
                $doc = new Document();
                $savedoc = $doc->saveDocument(
                    $request->root() == "http://127.0.0.1:8000"? 1 : 0,
                    $request->doctitle,
                    $request->docdescription,
                    $request->docformat,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    "App\Models\Audit",
                    $audit->id,
                    auth()->user()->id
                );
            }

        }

       return redirect('/audit')->with('success', 'Your audit entry has been updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Audit  $audit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audit $audit)
    {
        $this->authorize('delete', $audit);

        $id = $audit->id;

        $files = $audit->documents()->pluck('filepath')->toArray();

        // Log::debug($files);

        if(count($files)) {
            // Log::debug('Deleting files');

            Storage::delete($files);
        }
        // Delete polymorphic first
        $audit->documents()->delete();

        // Then delete everything else
        $audit->delete();

        return redirect('/audit')->with('success', 'Your audit entry has been deleted');
}

    public function print(Audit $audit)
    {
        // Log::debug("Downloading audit entry");

        $this->authorize('view', $audit);

        //dd($ai);

        $user = auth()->user();
        # Delete tmp dir
        $dir = 'store/'.auth()->user()->id.'/tmp';

        // Log::debug("Sending audit id: ".$audit->id);

        ProcessAuditDownload::dispatch($dir, $user, $audit->id);

    }

}
