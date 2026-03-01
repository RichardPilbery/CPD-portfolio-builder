<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Document;
use App\Models\Swot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Jobs\ProcessDownload;
use App\Jobs\ProcessCpdProfile;
use Illuminate\Filesystem\Filesystem;

class PortfolioController extends Controller
{
    // Change for production
    // private $pandoc_exec_command = 'PATH=/opt/homebrew/bin:/Library/TeX/texbin';
    private $pandoc_exec_command;
    
    function __construct($pandoc_exec_command = '') {
        $this->pandoc_exec_command = url('/') == "http://127.0.0.1:8000" ? "PATH=/opt/homebrew/bin:/Library/TeX/texbin" : 'PATH=/usr/local/texlive/2022/bin/x86_64-linux:/usr/bin';
    }
    
    public function search($query) {

        //Log::debug("Inside the portfolio search function");
        $result = Portfolio::where('user_id', auth()->user()->id)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%');
            })
            ->orderBy('actdate', 'desc')
            ->select(['id','actdate', 'title'])
            ->limit(10)
            ->get();

        //Log::debug($result);

        return response()->json($result);

    }

    public function index()
    {
        $portfolios = DB::table('portfolios')->where('user_id', auth()->user()->id)->orderBy('actdate', 'DESC')->paginate(25);
        // dd($portfolios);

       $act_abbr = Activity::get()->pluck('abbr','id');
       $act_name = Activity::get()->pluck('name','id');
        //Log::debug($portfolios);
        //dd($act_abbr);

        $service_id = auth()->user()->service_id;
        $user_email = base64_encode(auth()->user()->email).'@cpd.com';

        return view('portfolio.index', compact(['portfolios','act_abbr','act_name', 'service_id','user_email']));
    }


    public function step3() {

        Log::debug('Step 3');
        // Grab the activity info into an array for use in the view
        //$act_abbr = Activity::get()->pluck('abbr','id');
        $act_name = Activity::get()->pluck('name','id')->all();
        //dd($act_name);
        $summary = auth()->user()->summary;

        // Grab portfolio entries from the last 2 years
        $portfolios = auth()->user()->portfolios->where('actdate','>=', Carbon::now()->subYears(2));
        $portfolios = $portfolios->sortBy('activity_id');

        // Return array that summarises the number of entries in each activity type
        $port_activity = $portfolios->pluck('activity_id')->all();
        $act_array = array_count_values($port_activity);
        $new = array();
        foreach($act_array as $key => $value) {
            $new[$act_name[$key]] = $value;
        }
        //dd($new);

        return view('portfolio.step3', compact(['portfolios','act_name', 'new', 'summary']));

    }

    public function savestep3(Request $request) {

        // Must have at least one entry selected
        $request->validate([
            'choices' => 'required'
        ]);
        //dd($request->all());

        // Update portfolio entries with cpd profile selection
        // ids contains all the portfolio entries
        // so can see if they have been chosen or not
        // and then update each portfolio entry
        foreach($request->ids as $key => $value) {
            $portfolio = Portfolio::where('id', $key)->first();
            if(isset($request->choices[$key])) {
                $portfolio->update(['profile' => 1]);
            } else {
                $portfolio->update(['profile' => 0]);
            }

        }

        $audit = 1;
        if($request->audit == "not") {
            $audit = 0;
        }

        $user = auth()->user();

        ProcessCpdProfile::dispatch($audit, $user);

        return redirect("/portfolio/step4/$audit")->with('success', 'Your portfolio entry choices have been recorded.');
        //$this->step4($request->audit);

    }


    public function show(Portfolio $portfolio)
    {

        $this->authorize('view', $portfolio);

        $documents = $portfolio->documents;

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

        //$comps = [];

        $activity_name = $portfolio->activity['name'];
        $swot = $portfolio->swot;

        return view('portfolio.show', compact(['portfolio', 'activity_name', 'documents', 'comps', 'swot']));
    }

    public function create()
    {
        $act = Activity::get();
        //dd($act);
        // Custom SQL query to find the name and description of the compentencies
        // and insert them into an array
        //https://laravel.com/docs/6.x/queries#where-clauses

        $clfs = DB::table('clfs')->selectRaw('concat("CLF-",id) id, concat(name,": ", domain, ": ", element) comp')->pluck('comp','id');
        $ksfs =  DB::table('ksfs')->selectRaw('concat("KSF-",id) id, concat(name,": ", description) comp')->pluck('comp','id');

        $comps = $clfs->merge($ksfs);
        //dd($comps);

        return view('portfolio.create', compact(['act','comps']));
    }


    public function edit(Portfolio $portfolio)
    {
        // Apply policy
        $this->authorize('update', $portfolio);

        $act = Activity::get();

        // Custom SQL query to find the name and description of the compentencies
        // and insert them into an array
        //https://laravel.com/docs/6.x/queries#where-clauses

        $comps = [];

        $clfs = DB::table('clfs')->selectRaw('concat("CLF-",id) id, concat(name,": ", domain, ": ", element) comp')->pluck('comp','id');
        $ksfs =  DB::table('ksfs')->selectRaw('concat("KSF-",id) id, concat(name,": ", description) comp')->pluck('comp','id');

        $comps = $clfs->merge($ksfs);

        $documents = $portfolio->documents->where('user_id', auth()->user()->id);

        //dd($documents);

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
        $selected_comps = [];

        if(count($clfs_id)) {
            if(count($ksfs_id)) {
                $selected_comps = $clfs->merge($ksfs);
            }
            else {
                $selected_comps = $clfs;
            }
        } else {
            if(count($ksfs_id)) {
                $selected_comps = $ksfs;
            }
        }

        //dd($selected_comps);

        $activity_name = $portfolio->activity['name'];

        //dd($documents);

        return view('portfolio.edit', compact(['portfolio', 'activity_name', 'documents', 'comps', 'selected_comps', 'act']));
    }


    public function store(Request $request, $email = false)
    {
        // Log::debug("Inside portfolios controller");
        // Log::debug($request->all());

        // dd($request->all());

        if($email == false) {

            $request->validate([
                'actdate' => 'required|date',
                'title' => 'required',
                'activity_id' => 'required|min:1|max:5|integer',
                'start' => 'nullable|date_format:"Y-m-d\TH:i"',
                'end' => 'nullable|date_format:"Y-m-d\TH:i"|after:start',
                'docupload.*' => 'mimes:pdf,jpeg,png,docx,pptx,bin,txt,doc,ppt|max:5000'
            ]);

                $request['user_id'] = auth()->user()->id;
                $request['profile'] = (isset($request->profile))? 1 : 0;
        }


        // Create portfolio entry first
        if($email == false) {
            $attributes = request(['actdate','user_id','title','description','benefit','profile','start','end','activity_id']);
            $portfolio = auth()->user()->portfolios()->create($attributes);
        }
        else {
            //Log::debug($request);
            $portfolio = Portfolio::create($request->all());
        }

        // Log::debug("Portfolio entry created has ID: ".$portfolio->id);


        if($email == false) {
        // Check for competencies and save them
            if(isset($request->comp) && count($request->comp) > 1) {
                // Log::debug('We have comptencies');
                // We have competencies.
                // Split array into keys and values
                [$keys, $values] = Arr::divide($request->comp);

                // Remove first item in array which is empty
                // No need with React component
                //$keys = Arr::except($keys, 0);

                // Create 2 arrays which will contain the ids of comptencies

                $ksf = [];
                $clf = [];

                foreach($keys as $k) {

                    [$model, $id] = explode("-", $k);

                    if($model == 'KSF') {
                        $ksf[] = $id;
                    }
                    elseif ($model == 'CLF') {
                        $clf[] = $id;
                    }

                }

                // Now need to create the entries
                // Can use attach() for this https://laravel.com/docs/6.x/eloquent-relationships#the-create-method

                // NOTE: Pivot table needs to be singular not plural
                // i.e. ksf_portfolio or clf_portfolio
                //Log::debug($ksf);
                //Log::debug($clf);

                if(count($ksf) > 0) {
                    $portfolio->ksfs()->attach($ksf);
                }
                if(count($clf) > 0) {
                    $portfolio->clfs()->attach($clf);
                }

            }
        }

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
                    "App\Models\Portfolio",
                    $portfolio->id,
                    auth()->user()->id
                );

                // Log::debug("Save doc says:");
                // Log::debug($savedoc);

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
                    "App\Models\Portfolio",
                    $portfolio->id,
                    auth()->user()->id
                );

                //Log::debug("Save doc says:");
                //Log::debug($savedoc);
           }
       }

       if($email == false) {
        return redirect('/portfolio')->with('success', 'Your portfolio entry has been created');
       } else {
           return $portfolio->id;
       }


    }

    public function update(Request $request, Portfolio $portfolio)
    {

        // Log::debug($request->all());

        Validator::make($request->all(), [
            'actdate' => 'required|date',
            'title' => 'required',
            'activity_id' => 'required|min:1|max:5|integer',
            'start' => 'nullable|date_format:"Y-m-d\TH:i"',
            'end' => 'nullable|date_format:"Y-m-d\TH:i"|after:start',
            'docupload.*' => 'mimes:pdf,jpeg,png,docx,pptx,bin|max:5000',
            'strength' => Rule::requiredIf(function () use ($request) {
                // https://laravel.com/docs/8.x/validation#rule-required-if
                return array_key_exists('strength',$request->all());
            }),
            'weakness' => Rule::requiredIf(function () use ($request) {
                return array_key_exists('weakness',$request->all());
            }),
            'opportunity' => Rule::requiredIf(function () use ($request) {
                return array_key_exists('opportunity',$request->all());
            }),
            'threat' => Rule::requiredIf(function () use ($request) {
                return array_key_exists('threat',$request->all());
            }),
        ])->validate();

        $request['user_id'] = auth()->user()->id;
        $request['profile'] = (isset($request->profile))? 1 : 0;

        // ($request->all());

        // Update the portfolio entry
        $portfolio->update(request(['actdate','user_id','title','description','benefit','profile','activity_id','start','end']));

        // NEED TO SAVE SWOT IF EXISTS

        if(isset($request->strength)) {
            // We have a SWOT
            $swot = Swot::where('portfolio_id', $portfolio->id);

            $swot->update(request(['strength', 'weakness', 'opportunity', 'threat']));
        }

        // Check for competencies and save them
        if(!empty($request->comp)) {
            // We have competencies.
            // Split array into keys and values
            [$keys, $values] = Arr::divide($request->comp);

            // Remove first item in array which is empty
            // No need with React component
            //$keys = Arr::except($keys, 0);

            // Create 2 arrays which will contain the ids of comptencies

            $ksf = [];
            $clf = [];

            foreach($keys as $k) {

                [$model, $id] = explode("-", $k);

                if($model == 'KSF') {
                    $ksf[] = $id;
                }
                elseif ($model == 'CLF') {
                    $clf[] = $id;
                }

            }

            // Sync the entries

            $portfolio->ksfs()->sync($ksf);
            $portfolio->clfs()->sync($clf);

        } else {
            // Need to remove any competencies
            // if user has deleted all of them.
            $portfolio->ksfs()->sync([]);
            $portfolio->clfs()->sync([]);
        }

        // Check for currently uploaded files and manage them

        if(isset($request->documents) || isset($portfolio->documents)) {
            // When deleting last portfolio document, $request->docs will not trigger
            // So need extra case
            // Log::debug('Documents already uploded');

            if(!isset($request->documents)) {
                // Delete all docs
                foreach($portfolio->documents as $d) {
                    // Log::debug("Delete id: ".$d->id);
                    Storage::delete($d->filepath);
                    $portfolio->documents()->where('id', $d->id)->delete();
                }
            } else {
                [$keys, $values] = Arr::divide($request->documents);

                foreach($portfolio->documents as $d) {
                    // Log::debug("There are portfolio documents");
                    if(isset($keys) && in_array($d->id, $keys)) {
                        // Log::debug("Keep id: ".$d->id);
                    }
                    else {
                        // Log::debug("Delete id: ".$d->id);
                        Storage::delete($d->filepath);
                        $portfolio->documents()->where('id', $d->id)->delete();
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
                     "App\Models\Portfolio",
                     $portfolio->id,
                     auth()->user()->id
                 );

                //  Log::debug("Save doc says:");
                //  Log::debug($savedoc);

            }
        } else if(!empty($request->doctitle) || !empty($request->docdescription) || !empty($request->docformat)) {
                // Need to check if document
                // need to create Document with these details in.
                // Log::debug('Doc title not empty');
                // Log::debug($portfolio->documents);

            if(count($portfolio->documents) == 0) {
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
                    "App\Models\Portfolio",
                    $portfolio->id,
                    auth()->user()->id
                );
            }

        }

        return redirect('/portfolio')->with('success', 'Your portfolio entry has been updated');

    }

    public function destroy(Portfolio $portfolio){

        // Log::debug("Going to delete portfolio $portfolio->id");
        // Log::debug($portfolio->documents()->pluck('filepath'));

        $this->authorize('delete', $portfolio);

        $id = $portfolio->id;

        $files = $portfolio->documents()->pluck('filepath')->toArray();

        
        // Log::debug("Files are here:");
        // Log::debug($files);

        if(count($files)) {
            // Log::debug('Deleting files');

            Storage::delete($files);
        }
        // Delete polymorphic first
        $portfolio->documents()->delete();

        // Then delete everything else
        $portfolio->delete();

        return redirect('/portfolio')->with('success', 'Your portfolio entry has been deleted');

    }


    public function print(Portfolio $portfolio)
    {
        // Log::debug("Downloading portfolio entry");

        $this->authorize('view', $portfolio);

        $documents = $portfolio->documents->where('user_id', auth()->user()->id);

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

        //$comps = [];

        $activity_name = $portfolio->activity['name'];
        $user = auth()->user();

        $portfolio->description = str_replace(array('#','##','###','*'), '',$portfolio->description);
        $portfolio->benefit = str_replace(array('#','##','###','*'), '',$portfolio->benefit);

        $swot = $portfolio->swot;

        $dir = 'store/'.auth()->user()->id.'/tmp';

        // Log::debug('Deleted directory and now dispatching processdownload');

        // Need to save copy of attached docs to tmp file too.
        ProcessDownload::dispatch($dir, $documents, $portfolio, $activity_name, $comps, $user, $swot);
        // Need a confirmation that the job has been done prior to running next step

        // return redirect('/portfolio/'.$portfolio->id)->with('success', 'Your portfolio entry will be ready for download in just a sec...');

    }

    public function step4($hcpcaudit)
    {

        $dir = 'store/'.auth()->user()->id.'/tmp';

        // Get user details and summary
        $user = auth()->user();
        $summary = auth()->user()->summary;

        // Fix in case job description has newlines
        //$summary->job_description = str_replace('\r\n', 'HHH', $summary->job_description);
        //dd($summary->job_description);

        //dd($user);
        // HCPC audit will either be not or audit depending on what is require.
        // hcpc audit now needs to be a page with copy and paste items as well as
        // links to download summary table and evidence.

        // CPD profile for not can be PDF

        // Fix summary multiple newlines
        $summary->job_description = str_replace("\r\n","\n", $summary->job_description);
        $summary->job_description = str_replace("\n\n","\n", $summary->job_description);
        if($hcpcaudit == 1) {
            $summary->job_description = str_replace("\n","\n    +  ", $summary->job_description);
        } else {
            $summary->job_description = str_replace("\n","\n+  ", $summary->job_description);
        }

        $fulldir = storage_path().'/app/'.$dir;

        // You are going to need to pass some text like this:

//         A summary of the previous 2 years' CPD activity is listed in the summary sheet (Evidence 1).
// @php
//     $counter = 0;
//     $evidence = 2;
// @endphp
// @foreach($result as $entry)
// @php
//     $counter++;
// @endphp

// Example {{ $counter }} @if($entry['docs'] != null && count($entry['docs']->all()) > 0) (Evidence {{ $evidence }})
// @php
//     $evidence++;
// @endphp
// @else

// @endif

//     Activity type: {{ $entry['activity'] }}

//     Title: {{ $entry['portfolio']->title }}

//     Description: {{ $entry['portfolio']->description }}

//     Benefit: {{ $entry['portfolio']->benefit }}
// @endforeach

        return view('portfolio.step4', compact(['user', 'summary', 'hcpcaudit']));

    }

    public function downloadprofile($whichbit, $audit) {

        // whichbit
        // Default (0) is full profile
        // 1 is summary table
        // 2 is evidence

        $file = "cpd_profile.pdf";
        $type = "cpd-profile";

        switch($whichbit) {
            case(1):
                $file = "cpd_profile_summary.pdf";
                $type = "summary";
                break;
            case(2):
                $file = "cpd_profile_evidence.pdf";
                $type = "evidence";
                break;
        }

        $user = auth()->user();
        $dir = 'store/'.$user->id.'/tmp';
        $fulldir = storage_path().'/app/'.$dir;

        $filename = Str::slug($user->name, '-').'-'.$type.'.pdf';

        if($audit == 1) {
            // Anonymise downloads
            $filename = "$type.pdf";
        }


        return Storage::download($dir.'/'.$file.'/', $filename);

    }

    public function ksf($print = FALSE) {

        $user_ksfs = auth()->user()->ksfs()->get()->toArray();

        $portfolios = Portfolio::with('ksfs')
            ->withCount('ksfs')
            ->where('user_id', auth()->user()->id)
            ->orderBy('actdate', 'DESC')
            ->get()
            ->where('ksfs_count', '>', 0)
            ->toArray();

        //dd($user_ksfs);
        $portfolio_ksf_array = [];

        foreach($user_ksfs as $u) {
            $portfolio_ksf_array[$u['name']]['name'] = $u['name'];
            $portfolio_ksf_array[$u['name']]['description'] = $u['description'];
            $portfolio_ksf_array[$u['name']]['count'] = 0;
            $i = 0;
            foreach($portfolios as $p) {
                $i++;
                if(array_search($u['name'], array_column($p['ksfs'], 'name')) !== FALSE) {
                    $portfolio_ksf_array[$u['name']]['count'] += 1;
                    if($portfolio_ksf_array[$u['name']]['count'] < 11) {
                        $portfolio_ksf_array[$u['name']]['portfolio'][] = [
                            'id' => $p['id'],
                            'title' => str_replace(array("\r", "\n"), "", $p['title']),
                            'actdate' => $p['actdate']
                        ];
                    }
                }
            }
            if($portfolio_ksf_array[$u['name']]['count'] > 10) {
                $portfolio_ksf_array[$u['name']]['portfolio'][] = [
                    'id' => 999999999999,
                    'title' => 'and '.($portfolio_ksf_array[$u['name']]['count'] - 10).' more entries',
                    'actdate' => '00-00-00'
                ];
            }
        }

        if($print) {
            return $portfolio_ksf_array;
        } else {
            return view('portfolio.ksf', compact('user_ksfs', 'portfolio_ksf_array'));
        }

    }

    // Provide link to download portfolio_entry.pdf file once created.
    public function downloadentry(Portfolio $portfolio) {

        $this->authorize('view', $portfolio);

        $dir = 'store/'.auth()->user()->id.'/tmp/';

        return Storage::download($dir.'portfolio_entry.pdf');
    }

    public function printksf() {

        $user = auth()->user();
        $portfolio_ksfs = $this->ksf(1);

        $md = view('portfolio.printksf', compact('user','portfolio_ksfs'))->render();

        # Delete tmp dir
        $dir = 'store/'.auth()->user()->id.'/tmp';
        # Delete the tmp directory first
        Storage::deleteDirectory($dir);

        # Save the markdown file
        $path = Storage::put($dir.'/markdown.md', $md);

        $fulldir = storage_path().'/app/'.$dir;
        //dd($fulldir);
        // Log::debug('cd '.$fulldir.'; pandoc markdown.md -o pe.pdf --from markdown --template eisvogel');

        # Run the exec command.
        $command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md --template eisvogel -o ksf.pdf --from markdown 2>&1';

        //$command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md -H /Users/tricky999/Desktop/test.sty -o ksf.pdf --from markdown --template eisvogel 2>&1';

        // Log::debug($command);
        Log::debug(exec($command));
        //dd($md);
        $filename = 'KSF-summary.pdf';

        return Storage::download($dir.'/ksf.pdf', $filename);

    }

    public function clf($print = FALSE) {

        $user_clfs = auth()->user()->clfs()->get()->toArray();

        $portfolios = Portfolio::with('clfs')
            ->withCount('clfs')
            ->where('user_id', auth()->user()->id)
            ->orderBy('actdate', 'DESC')
            ->get()
            // Only return portfolio entries with an associated CLF activity
            ->where('clfs_count', '>' , '0')->toArray();

        //dd($portfolios[3]);
        $portfolio_clf_array = [];

        foreach($user_clfs as $u) {
            // Retrieve basic details from user_clfs
            ///dd($u);
            $portfolio_clf_array[$u['name']]['name'] = $u['name'];
            $portfolio_clf_array[$u['name']]['domain'] = $u['domain'];
            $portfolio_clf_array[$u['name']]['element'] = $u['element'];
            // Need to get count for each CLF from portfolio values
            $portfolio_clf_array[$u['name']]['count'] = 0;
            foreach($portfolios as $p) {
                //dd($p);
                if(array_search($u['name'], array_column($p['clfs'], 'name')) !== FALSE) {
                    $portfolio_clf_array[$u['name']]['count'] += 1;
                    $portfolio_clf_array[$u['name']]['portfolio'][] = [
                        'id' => $p['id'],
                        'title' => $p['title'],
                        'actdate' => $p['actdate']
                    ];
                }
            }
        }

       if($print) {
        return $portfolio_clf_array;
       } else {
        return view('portfolio.clf', compact('user_clfs','portfolio_clf_array'));
       }


    }

    public function printclf() {

        $user = auth()->user();

        $portfolio_clfs = $this->clf(1);

        $md = view('portfolio.printclf', compact('user','portfolio_clfs'))->render();

        # Delete tmp dir
        $dir = 'store/'.auth()->user()->id.'/tmp';
        # Delete the tmp directory first
        $filesystem = new Filesystem;
        if($filesystem->isDirectory(storage_path().'/app/'.$dir)) {
            $fulldir = storage_path().'/app/'.$dir;
            $prep_folder_cmd = 'cd '.$fulldir.'; chmod -R 777 ./* 2>&1';
            exec($prep_folder_cmd);
            Storage::deleteDirectory($dir);
        } 
        Storage::makeDirectory($dir, 0777, true);

        # Save the markdown file
        $path = Storage::put($dir.'/markdown.md', $md);

        $fulldir = storage_path().'/app/'.$dir;
        //dd($fulldir);
        #Log::debug('cd '.$fulldir.'; pandoc markdown.md -o pe.pdf --from markdown --template eisvogel');

        # Run the exec command.
        $command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md --template eisvogel -o clf.pdf --from markdown 2>&1';

        // Log::debug($command);
        Log::debug(exec($command));
        //dd($md);
        $filename = 'CLF-summary.pdf';

        return Storage::download($dir.'/clf.pdf', $filename);

    }

}
