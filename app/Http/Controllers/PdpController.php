<?php

namespace App\Http\Controllers;

use App\Models\Pdp;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Change for production
    private $pandoc_exec_command;
    
    function __construct($pandoc_exec_command = '') {
        $this->pandoc_exec_command = url('/') == "http://127.0.0.1:8000" ? "PATH=/opt/homebrew/bin:/Library/TeX/texbin" : 'PATH=/usr/local/texlive/2022/bin/x86_64-linux:/usr/bin';
    }

    public function index()
    {

        $pdps = Pdp::where('user_id', auth()->user()->id)->orderBy('finishdate', 'ASC')->get();

        //dd($pdps);

        return view('pdp.index', compact(['pdps']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pdp.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'objective' => 'required',
            'activity' => 'required',
            'measure' => 'required',
            'support' => 'required',
            'barriers' => 'required',
            'finishdate' => 'date_format:"Y-m-d"|required'
        ]);

        $request['user_id'] = auth()->user()->id;
        $request['portfolio_id'] = 99999999; // Kept for historic purposes

        Pdp::create($request->all());

        return redirect('/pdp')->with('success', 'Your PDP objective has been created');
    }

    private function format_pdp($p) {
        $p1 = str_replace(array('#','##','###','*','
        '), '',$p);
        $p2 = preg_replace("/\r\n|\n/", '; ', $p1);

        return $p2;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pdp  $pdp
     * @return \Illuminate\Http\Response
     */
    public function print(Pdp $pdp)
    {
        $pdps = Pdp::where('user_id', auth()->user()->id)->orderBy('finishdate', 'ASC')->get()->toArray();


        $format_pdp = [];
        foreach($pdps as $p) {
            $temp_array = [];
            foreach($p as $key => $value) {
                $temp_array[$key] = $this->format_pdp($value);
            }
            $format_pdp[] = $temp_array;
        }

        $user = auth()->user();


        $md = view('pdp.print', compact('user','format_pdp'))->render();

        # Delete tmp dir
        $dir = 'store/'.auth()->user()->id.'/tmp';
        # Delete the tmp directory first
        Storage::deleteDirectory($dir);

        # Save the markdown file
        $path = Storage::put($dir.'/markdown.md', $md);

        $fulldir = storage_path().'/app/'.$dir;

        # Run the exec command.
        $command = 'cd '.$fulldir.'; '.$this->pandoc_exec_command.' pandoc markdown.md -o pdp.pdf --from markdown --template eisvogel -V geometry:landscape -V geometry:left=1cm -V geometry:right=1cm 2>&1';

        exec($command);
        //dd($md);
        $filename = 'Personal-Development-Plan.pdf';

        return Storage::download($dir.'/pdp.pdf', $filename);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pdp  $pdp
     * @return \Illuminate\Http\Response
     */
    public function edit(Pdp $pdp)
    {
        $this->authorize('update', $pdp);

        return view('pdp.edit', compact(['pdp']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pdp  $pdp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pdp $pdp, $toggle = false)
    {
        $this->authorize('update', $pdp);

        if($toggle) {
            $toggle = !$request->completed;
            $pdp->update(['completed'=> $toggle]);
        }

        $request->validate([
            'objective' => 'required',
            'activity' => 'required',
            'measure' => 'required',
            'support' => 'required',
            'barriers' => 'required',
            'finishdate' => 'date_format:"Y-m-d"|required'
        ]);

        if(!$toggle) {
            $pdp->update($request->all());
        }

        return redirect('/pdp')->with('success', 'Objective status updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pdp  $pdp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pdp $pdp)
    {
        $this->authorize('delete', $pdp);

        $pdp->delete();

        return redirect('/pdp')->with('success', 'Your objective has been deleted');
    }
}
