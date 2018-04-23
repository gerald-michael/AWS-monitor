<?php

namespace station\Http\Controllers;

use Illuminate\Http\Request;
use station\Station;
use station\Sensor;

use station\SinkNode;
use station\NodeStatus;
use station\ObservationSlip;
use DB;
class SinkNodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stations = Station::all()->toArray();
        $sinkNodes = SinkNode::all()->toArray();
        $pressuresensors = Sensor::where('node_type','sinkNode')
                                    ->where('parameter_read', 'pressure')
                                    ->get();
        return view('layouts.configureSinkNode',compact('sinkNodes','stations','pressuresensors'));
    
        
    }

    public function report1(){
        $data["action"]="/reportsSink";
        $data["stations"]=Station::all();
        $data["heading"]="Sink Node Reports";
        
        $data["vin_vmcu_sink"]=array(0,0);
        $data["pressure"]=array(0,0);
        return view("reports.nodesink",$data);
    }


    public function getSinkStationReports(Request $request){
        $station_id=request("id");
        $data=array();
       
       //get the txt value used for the particular station 10m node

       
       $stationSinkNodeCofigs = SinkNode::where('station_id', '=', $station_id)
            
            ->select('txt_sink_value')
            ->first();
       
        
        //get node status where the configulations are the ones specifie above
        $nodeStatus=NodeStatus::where('TXT','=',$stationSinkNodeCofigs->txt_sink_value)
                        
                        ->select(DB::raw("CONCAT(date,' ',time)  AS y"),
                                    'V_MCU','V_IN')
                        ->oldest('date_time_recorded')
                        ->limit(1000)
                        ->get();
        
        $dyGraph_data=array();
        $i=1;
        foreach($nodeStatus as $status){

            if($status->V_MCU=="" || $status->V_MCU==null){
              $status->V_MCU=0;  
            }
            if($status->V_IN=="" || $status->V_IN==null){
              $status->V_IN=0;  
            }

            $temp_array=array($i,(float)$status->V_MCU,(float)$status->V_IN);
            $dyGraph_data[]=$temp_array;
            $i++;
        }

        $data["vin_vmcu_sink"]=$dyGraph_data;
        //get values for other graphs as well
        
        //get precipitation for ground node

        //nop
         $pressure=ObservationSlip::where('station','=',$station_id)
                        
                        ->select(DB::raw("CONCAT(date,' ',time)  AS y"),
                                    'VapourPressure')
                        ->oldest('creationDate')
                        ->limit(1000)
                        ->get();

        // foreach($precipitations as $precipitation){
        //     $precipitation->DurationOfPeriodOfPrecipitation=$precipitation->DurationOfPeriodOfPrecipitation*0.2;
        // }
        $pressure_data=array();
        $i=1;
        foreach($pressure as $pres){
            $temp_array=array($i,(float)$pres->VapourPressure);
            $pressure_data[]=$temp_array;
            $i++;
        }

        $data["pressure"]=$pressure_data;


        $data["action"]="/reportsSink";
        $data["stations"]=Station::all();
        $data["heading"]="Sink Node Reports";
        return view("reports.nodesink",$data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->get('sinknode_id')!= null){
            $id=$request->get('sinknode_id');
            $sinkNode = SinkNode::where('node_id',$id)->first();
            $sinkNode->txt_sink = $request->get('sinktxt_key');
            $sinkNode->e64_sink = $request->get('sinkmac_add');
            $sinkNode->v_in_sink = $request->get('sinkvin_label');
            $sinkNode->time_sink = $request->get('sinktime');
            $sinkNode->ut_sink = $request->get('sinkut');
            $sinkNode->date_sink = $request->get('sinkdate');
            $sinkNode->gw_lat_sink = $request->get('sinkgwlat');
            $sinkNode->gw_long_sink = $request->get('sinkgwlong');
            $sinkNode->v_in_min_value = $request->get('sinkv_in_max_value');
            $sinkNode->v_in_max_value = $request->get('sinkv_in_min_value');
            $sinkNode->ttl_sink = $request->get('sinkttl');
            $sinkNode->rssi_sink = $request->get('sinkrssi');
            $sinkNode->drp_sink = $request->get('sinkdrp');
            $sinkNode->lqi_sink = $request->get('sinklqi');
            $sinkNode->v_mcu_max_value = $request->get('sinkv_mcu_max_value');
            $sinkNode->v_mcu_min_value = $request->get('sinkv_mcu_min_value');
            $sinkNode->v_mcu_sink = $request->get('sinkv_mcu_label');
            $sinkNode->p_ms5611_sink= $request->get('psidentifier_used');
            $sinkNode->t_sink= 'T';
            $sinkNode->ps_sink = $request->get('sinkps');
            $sinkNode->node_status= $this->getStatus($request,'sinknode_status');
            $sinkNode->txt_sink_value= $request->get('sinktxt_value');
            $sinkNode->up_sink =$request->get('sinkup');
            $sinkNode->save();
        $pressure = Sensor::where('node_id',$id)
                                    ->where('parameter_read', 'pressure')
                                    ->first();
            
            $pressure->parameter_read = $request->get('psparameter_read');
            $pressure->identifier_used= $request->get('psidentifier_used');
            $pressure->min_value = $request->get('psmin_value');
            $pressure->max_value= $request->get('psmax_value');
            $pressure->sensor_status= $this->getStatus($request,'pssensor_status');
            $pressure->report_time_interval=$request->get('psrptTime');
            $pressure->save();
        
        
        
        
        }
        return redirect('/configuresinknode');
    }
    public function getStatus(Request $request, $status){
        $value = 'on';
          if($request->has($status)) {
            $value = 'on';
            }
            else{
                $value = 'off';
            }
        return $value;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
