<?php

namespace App\Http\Controllers;

use App\Models\Swot;
use App\Models\Portfolio;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SwotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $swots = Portfolio::has('swot')->with('swot')->where('user_id', auth()->user()->id)->orderBy('actdate', 'DESC')->get();

        $act_abbr = Activity::get()->pluck('abbr','id');
        $act_name = Activity::get()->pluck('name','id');

        return view('swot.index', compact(['swots', 'act_abbr', 'act_name']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('swot.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'strength' => 'required',
            'weakness' => 'required',
            'opportunity' => 'required',
            'threat' => 'required'
        ]);

        // Create new portfolio Entry
        $portfolio = Portfolio::create([
            'user_id' => auth()->user()->id,
            'actdate' => date('Y-m-d'),
            'title' => 'SWOT analysis',
            'description' => 'An analysis of my current strenghts, weaknesses, opportunities and threats',
            'activity_id' => 1,
            'start' => date('Y-m-d\TH:i'),
            'end' => Carbon::now()->addMinutes(30)->format('Y-m-d\TH:i')
        ]);

        $request['portfolio_id'] = $portfolio->id;

        Swot::create($request->all());


        return redirect('/swot')->with('success', 'Your SWOT analysis has been created');
    }

}
