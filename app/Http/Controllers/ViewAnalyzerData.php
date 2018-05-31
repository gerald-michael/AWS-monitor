<?php

namespace station\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use station\Http\Controllers\Controller;
use station\Http\Controllers\AnalyzerHandler;

class ViewAnalyzerData extends Controller
{
    private $Handler;
    public function __construct()
    {
        $this->Handler = new AnalyzerHandler();
    }
    public function showProbTable()
    {
        //get data in problems table   problems
        //source, source_id, criticality, classification_id, track_counter, status
        $data = DB::table('problems')->get();
        $problems = DB::table('problem_classification')->get();

        $data->transform(function($problem){
            $names =  $this->Handler->problemStationNames($problem->source, $problem->source_id);
            // dd($names);
            $problem->stn_name = $names['stn_name'];
            $problem->parameter_read = $names['parameter_read'];
            return $problem;
        });
        // dd($data);
        // return $data;
        return view('layouts.analyzer', compact('data','problems'));
    }
}