<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class AnalyserController extends Controller
{
  public function __construct()
  {
    $this->middleware(['permission:problem-archive']);
  }
  public function index()
  {

    $data = DB::select(DB::raw("select * from DetectedAnalyzerProblems inner join stations on DetectedAnalyzerProblems.stationID = stations.station_id where status != 'fixed'"));
    //print_r($data);

    return view('reports/analyser', compact('data'));
  }
}
