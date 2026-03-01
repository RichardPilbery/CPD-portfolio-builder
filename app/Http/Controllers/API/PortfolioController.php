<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        Log::debug('Request for API index');
        Log::debug(auth()->user());

        $portfolio = Portfolio::where('user_id',$user_id)->get();

        return $portfolio;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::debug("Inside API portfolios controller");
        Log::debug($request->all());
        Log::debug($request->user());

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        if (!$request->user()) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }


        $request['actdate'] = date('Y-m-d');
        $request['start'] = Carbon::now()->format('Y-m-d\TH:i');
        $request['end'] = Carbon::now()->addMinutes(30)->format('Y-m-d\TH:i');
        $request['user_id'] = auth()->user()->id;
        $request['profile'] = 0;
        $request['activity_id'] = 1;

        $attributes = request(['actdate','user_id','title','description','benefit','profile','start','end','activity_id']);
        $portfolio = $request->user()->portfolios()->create($attributes);

        if(!empty($request->file('docupload'))) {
            Log::debug('Inside DOCUUPLOAD');
            $file = $request->file('docupload');
            $path = Storage::putFile('store/'.$request->user()->id, $file);
            $doc = new Document();
            $savedoc = $doc->saveDocument(
                ($request->root() == "http://127.0.0.1:8000")? 1 : 0,
                'Standby CPD Certificate',
                'Evidence of CPD completion',
                '1 PDF certificate',
                $file->getMimeType(),
                $file->getSize(),
                $file->getClientOriginalName(),
                $path,
                "App\Models\Portfolio",
                $portfolio->id,
                $request->user()->id
            );
        }

        // return response(['message' => 'Portfolio Entry Saved']);
        return response()->json(['message' => 'Portfolio Entry Saved'], 200);
    }



    // public function store(Request $request, $email = false)
    // {
    //     Log::debug("Inside portfolios controller");

    //     //dd($request->all());

    //     if($email == false) {

            // $request->validate([
            //     'actdate' => 'required|date',
            //     'title' => 'required',
            //     'activity_id' => 'required|min:1|max:5|integer',
            //     'start' => 'nullable|date_format:"Y-m-d\TH:i"',
            //     'end' => 'nullable|date_format:"Y-m-d\TH:i"|after:start',
            //     'docupload.*' => 'mimes:pdf,jpeg,png,docx,pptx,bin|max:5000'
            // ]);

            //     $request['user_id'] = auth()->user()->id;
            //     $request['profile'] = 0;
            //     $request['activity_id'] = 1;
    //     }


    //     // Create portfolio entry first
    //     if($email == false) {
    //         $attributes = request(['actdate','user_id','title','description','benefit','profile','start','end','activity_id']);
    //         $portfolio = auth()->user()->portfolios()->create($attributes);
    //     }
    //     else {
    //         //Log::debug($request);
    //         $portfolio = Portfolio::create($request->all());
    //     }


    //     if($email == false) {
    //     // Check for competencies and save them
    //         if(isset($request->comp) && count($request->comp) > 1) {
    //             // We have competencies.
    //             // Split array into keys and values
    //             [$keys, $values] = Arr::divide($request->comp);

    //             // Remove first item in array which is empty
    //             // No need with React component
    //             //$keys = Arr::except($keys, 0);

    //             // Create 2 arrays which will contain the ids of comptencies

    //             $ksf = [];
    //             $clf = [];

    //             foreach($keys as $k) {

    //                 [$model, $id] = explode("-", $k);

    //                 if($model == 'KSF') {
    //                     $ksf[] = $id;
    //                 }
    //                 elseif ($model == 'CLF') {
    //                     $clf[] = $id;
    //                 }

    //             }

    //             // Now need to create the entries
    //             // Can use attach() for this https://laravel.com/docs/6.x/eloquent-relationships#the-create-method

    //             // NOTE: Pivot table needs to be singular not plural
    //             // i.e. ksf_portfolio or clf_portfolio
    //             Log::debug($ksf);
    //             Log::debug($clf);

    //             if(count($ksf) > 0) {
    //                 $portfolio->ksfs()->attach($ksf);
    //             }
    //             if(count($clf) > 0) {
    //                 $portfolio->clfs()->attach($clf);
    //             }

    //         }
    //     }

    //     // Need to check if doctitle has been completed as
    //     // still may have details even if file not uploaded.

    //    if(!empty($request->file('docupload'))) {
    //        //dd("We have files");
    //        $files = $request->file('docupload');
    //        //dd($files);
    //        foreach($request->file('docupload') as $file) {
    //             //dd($file->getMimeType());
    //             $path = Storage::putFile('store/'.auth()->user()->id, $file);
    //             // Need to get all info required for Document entry
    //             // set subject_type to App\Models\Portfolio
    //             // set subject_id to id of portfolio entry, so need to
    //             // create this first
    //             $doc = new Document();
    //             $savedoc = $doc->saveDocument(
    //                 ($request->root() == "http://127.0.0.1:8000")? 1 : 0,
    //                 $request->doctitle,
    //                 $request->docdescription,
    //                 $request->docformat,
    //                 $file->getMimeType(),
    //                 $file->getSize(),
    //                 $file->getClientOriginalName(),
    //                 $path,
    //                 "App\Models\Portfolio",
    //                 $portfolio->id,
    //                 auth()->user()->id
    //             );

    //             Log::debug("Save doc says:");
    //             Log::debug($savedoc);

    //        }
    //    } else {
    //        if(!empty($request->doctitle) || !empty($request->docdescription) || !empty($request->docformat)) {
    //             // need to create Document with these details in.
    //             $doc = new Document();
    //             $savedoc = $doc->saveDocument(
    //                 $request->root() == "http://127.0.0.1:8000"? 1 : 0,
    //                 $request->doctitle,
    //                 $request->docdescription,
    //                 $request->docformat,
    //                 NULL,
    //                 NULL,
    //                 NULL,
    //                 NULL,
    //                 "App\Models\Portfolio",
    //                 $portfolio->id,
    //                 auth()->user()->id
    //             );

    //             //Log::debug("Save doc says:");
    //             //Log::debug($savedoc);
    //        }
    //    }

    //    if($email == false) {
    //     return redirect('/portfolio')->with('success', 'Your portfolio entry has been created');
    //    } else {
    //        return $portfolio->id;
    //    }


    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portfolio $portfolio)
    {
        //
    }
}
