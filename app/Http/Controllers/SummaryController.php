<?php

namespace App\Http\Controllers;

use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;


class SummaryController extends Controller
{

    // Change for production
    private $pandoc_exec_command;
    
    function __construct($pandoc_exec_command = '') {
        $this->pandoc_exec_command = url('/') == "http://127.0.0.1:8000" ? "PATH=/opt/homebrew/bin:/Library/TeX/texbin" : 'PATH=/usr/local/texlive/2022/bin/x86_64-linux:/usr/bin';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $summary = auth()->user()->summary;

        return view('summary.index', compact(['summary']));
    }

    public function print(Request $request)
    {

        //dd($request->all());
        $summary = auth()->user()->summary;
        $summary->work_details = str_replace(array('#','##','###','*'), '',$summary->work_details);
        $summary->job_description = str_replace(array('#','##','###','*'), '',$summary->job_description);
        $summary->service_users = str_replace(array('#','##','###','*'), '',$summary->service_users);

        $portfolios = [];
        $duration = '';
        $total_cpd_time = 0;
        if($request->summary_type_select !== 'summary') {
            // Get portfolio entries
            $date = Carbon::now()->subYears($request->summary_type_select);
            $portfolios = auth()->user()->portfolios()->where("actdate", ">", $date)->orderBy('actdate', 'DESC')->get();
            $duration = $request->summary_type_select === "100" ? "all" : "the previous ".$request->summary_type_select." years'";
            foreach($portfolios as $p) {
                $total_cpd_time += Carbon::parse($p->end)->diffInMinutes(Carbon::parse($p->start))/60;
            }
        }

        $user = auth()->user();
        $date = Carbon::now()->format('d-m-Y');

        $md = view('summary.print', compact(['summary', 'user', 'date', 'portfolios', 'duration', 'total_cpd_time']))->render();

        # Delete tmp dir
        $dir = 'store/'.auth()->user()->id.'/tmp';
        # Delete the tmp directory first
        $fulldir = storage_path().'/app/'.$dir;
        $filesystem = new Filesystem;
        // if($filesystem->isDirectory(storage_path().'/app/'.$dir)) {
        //     $fulldir = storage_path().'/app/'.$dir;
        //     $prep_folder_cmd = 'cd '.$fulldir.'; chmod -R 777 ./* 2>&1';
        //     exec($prep_folder_cmd);
        //     Storage::deleteDirectory($dir);
        // } 

        if($filesystem->isDirectory($fulldir)) {
            $prep_folder_cmd = 'rm -rf '.$fulldir.' 2>&1';
            exec($prep_folder_cmd);
        } 

        # Save the markdown file
        $path = Storage::put($dir.'/markdown.md', $md);

        $fulldir = storage_path().'/app/'.$dir;

        # Run the exec command.
        $command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md -o sop.pdf --from markdown --template eisvogel; chmod -R 777 ./* 2>&1';

        // Log::debug($command);
        exec($command);
        //dd($md);

        return Storage::download($dir.'/sop.pdf', 'summary_of_practice.pdf');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($profile)
    {

        // This flag will be for CPD profile checking, when the
        // Standard 1 and 2 fields will have to be filled in
        //$profile = 0;

        return view('summary.create', compact(['profile']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        // Log::debug($request);
        if($request->profile == 0) {
            $request->validate([
                'work_details' => 'required',
                'service_users' => 'required',
                'job_description' => 'required',
            ]);
        } else {
            $request->validate([
                'work_details' => 'required',
                'service_users' => 'required',
                'job_description' => 'required',
                'standard1' => 'required',
                'standard2' => 'required'
            ]);
        }

        $profile = $request['profile'];
        unset($request['profile']);
        $request['user_id'] = auth()->user()->id;

        Summary::create($request->all());

        if($profile == 1) {
            // Redirect to portfolio entry selection page.
            //dd('Profile is 1');
            //dd($summary);
            return redirect('/portfolio/step3')->with('success', 'Your summary of practice has been updated');

        }
        else {
            return redirect('/home')->with('success', 'Your summary has been created');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function show(Summary $summary)
    {
        $this->authorize('view', $summary);

        // Log::debug($summary);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function edit(Summary $summary, $profile)
    {
        // Use policy to prevent user from editing someone else's
        // summary
        $this->authorize('update', $summary);

        //$profile = 0;
        //dd($summary);

        return view('summary.edit', compact(['summary','profile']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Summary $summary)
    {

        $this->authorize('update', $summary);

        if($request['profile'] == 0) {
            $request->validate([
                'work_details' => 'required',
                'service_users' => 'required',
                'job_description' => 'required',
            ]);
        } else {
            $request->validate([
                'work_details' => 'required',
                'service_users' => 'required',
                'job_description' => 'required',
                'standard1' => 'required',
                'standard2' => 'required'
            ]);
        }

        // Remove the profile tag from $request since this
        // is not a column in the summary table
        $profile = $request['profile'];
        unset($request['profile']);

        // Retrieve the user_id from current logged in user
        $request['user_id'] = auth()->user()->id;

        $summary->update($request->all());

        if($profile == 1) {
            // Redirect to portfolio entry selection page.
            //dd('Profile is 1');
            //dd($summary);
            return redirect('/portfolio/step3')->with('success', 'Your summary of practice has been updated');

        }
        else {
            return redirect('/home')->with('success', 'Your summary has been updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Summary  $summary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Summary $summary)
    {
        //
    }
}

