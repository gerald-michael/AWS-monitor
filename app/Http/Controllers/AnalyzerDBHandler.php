<?php

namespace station\Http\Controllers;

use Illuminate\Http\Request;

class AnalyzerDBHandler extends Controller
{
    //
    private $problem_classifn_tb;
    private $stns_tb;
    private $statn_prob_sttngs_tb;
    private $prob_tb;
    private $gnd_nd_tb;
    private $twoM_nd_tb;
    private $tenM_nd_tb;
    private $sink_nd_tb;
    private $timezone;

    public function __construct() {
        $this->problem_classifn_tb = 'problem_classification';
        $this->stns_tb = 'stations';
        $this->statn_prob_sttngs_tb = 'station_problem_settings';
        $this->prob_tb = 'problems';
        $this->gnd_nd_tb = 'groundNode';
        $this->twoM_nd_tb = 'twoMeterNode';
        $this->tenM_nd_tb = 'tenMeterNode';
        $this->sink_nd_tb = 'sinkNode';
        $this->timezone = 'Africa/Kampala';
    }

    /**
     * getProblemClassifications()
     * gets all the problem classifications in the table
     * @returns $problemClassfications
     */
    private function getProblemClassifications(){
        $problemClassfications = DB::table($this->problem_classifn_tb)->select('id','problem_description','source')->get();
        return $problemClassfications;
    }

    /**
     * getStations();
     * @returns $stations
     */
    private function getStations(){
        $stations = DB::table($this->stns_tb)->select('station_id', 'StationName', 'StationNumber')->get();
        return $stations;
    }
    
    /**
     *  getStationName($stn_id)
     *  @param stn_id
     * @returns $station_name;
     */
    private function getStationName($stn_id){
        $station_name = '';
        $stations = $this->getStations(); 

        foreach ($stations as $station) {
            if ($station->station_id === $stn_id) {
                $station_name = $station->StationName;
                break;
            }
        }
        return $station_name;
    }

    /**
     * pick problem station configurations
     * getNodeConfigurations():
     * @returns $stn_prb_conf;
     */
    private function getStationProbConfigs($stn_id){
        
        // problem_id, station_id, max_track_counter, criticality
        $stn_prb_conf = DB::table($this->statn_prob_sttngs_tb)->where('station_id',$stn_id)->select('problem_id','max_track_counter','criticality')->get();

        return $stn_prb_conf;
    }

    /**
     * get all the node status data
     */
    private function getNodeData($nd_tb, $txt)
    {
        $node_tb = DB::table($nd_tb)->select('node_id','station_id',$txt,'v_in_max_value','v_in_min_value','v_mcu_max_value','v_mcu_min_value')->get();
        
        return $node_tb;
    }

    /**
     * getNodeAndStationIds()
     * it also gets the node configurations
     * @returns array('stn_id' => $stn_id, 'nd_id' => $nd_id, 'nd_name' => $nd_name, 'vinMaxVal' => $vinMaxVal, 'vinMinVal' => $vinMinVal, 'vmcuMaxVal' => $vmcuMaxVal, 'vmcuMinVal' => $vmcuMinVal,);
     * Get the respective node ids
     * node name:- '2m_node','10m_node','sink_node','ground_node'
     * this should be separated into its own function
     * the txt identifiers should be stated as variables to manage them as they change
     * Since only configured nodes are saved to the database, there shouldn't be an unassigned variable.
     */
    private function getNodeAndStationInfo($txt){

        $nd_id = '';
        $nd_name = '';
        $stn_id = '';
        $vinMaxVal = '';
        $vinMinVal = '';
        $vmcuMaxVal = '';
        $vmcuMinVal = '';
        /* 'sensor','station','2m_node','10m_node','sink_node','ground_node' */
        if (stripos($txt, 'gnd') !== false) {
            // node_id, station_id, txt_gnd_value
            $node_tb = $this->getNodeData($this->gnd_nd_tb,'txt_gnd_value');
            foreach ($node_tb as $nd_tb) {
                if ($nd_tb->txt_gnd_value === $txt) {
                    $stn_id = $nd_tb->station_id;
                    $nd_id = $nd_tb->node_id;
                    $nd_name = 'ground_node';
                    $vinMaxVal = $nd_tb->v_in_max_value;
                    $vinMinVal = $nd_tb->v_in_min_value;
                    $vmcuMaxVal = $nd_tb->v_mcu_max_value;
                    $vmcuMinVal = $nd_tb->v_mcu_min_value;
                    break;
                }
            }
        }
        else if (stripos($txt, '2m') !== false) {
            // node_id, station_id, txt_gnd_value
            $node_tb = $this->getNodeData($this->twoM_nd_tb,'txt_2m_value');
            foreach ($node_tb as $nd_tb) {
                if ($nd_tb->txt_2m_value === $txt) {
                    $stn_id = $nd_tb->station_id;
                    $nd_id = $nd_tb->node_id;
                    $nd_name = '2m_node';
                    $vinMaxVal = $nd_tb->v_in_max_value;
                    $vinMinVal = $nd_tb->v_in_min_value;
                    $vmcuMaxVal = $nd_tb->v_mcu_max_value;
                    $vmcuMinVal = $nd_tb->v_mcu_min_value;
                    break;
                }
            }
        }
        else if (stripos($txt, '10m') !== false) {
            // node_id, station_id, txt_gnd_value
            $node_tb = $this->getNodeData($this->tenM_nd_tb,'txt_10m_value');
            foreach ($node_tb as $nd_tb) {
                if ($nd_tb->txt_10m_value === $txt) {
                    $stn_id = $nd_tb->station_id;
                    $nd_id = $nd_tb->node_id;
                    $nd_name = '10m_node';
                    $vinMaxVal = $nd_tb->v_in_max_value;
                    $vinMinVal = $nd_tb->v_in_min_value;
                    $vmcuMaxVal = $nd_tb->v_mcu_max_value;
                    $vmcuMinVal = $nd_tb->v_mcu_min_value;
                    break;
                }
            }
        }
        else if (stripos($txt, 'sink') !== false) {
            // node_id, station_id, txt_gnd_value
            $node_tb = $this->getNodeData($this->sink_nd_tb,'txt_sink_value');
            foreach ($node_tb as $nd_tb) {
                if ($nd_tb->txt_sink_value === $txt) {
                    $stn_id = $nd_tb->station_id;
                    $nd_id = $nd_tb->node_id;
                    $nd_name = 'sink_node';
                    $vinMaxVal = $nd_tb->v_in_max_value;
                    $vinMinVal = $nd_tb->v_in_min_value;
                    $vmcuMaxVal = $nd_tb->v_mcu_max_value;
                    $vmcuMinVal = $nd_tb->v_mcu_min_value;
                    break;
                }
            }
        }
        
        return array(
            'stn_id' => $stn_id, 
            'nd_id' => $nd_id, 
            'nd_name' => $nd_name, 
            'vinMaxVal' => $vinMaxVal, 
            'vinMinVal' => $vinMinVal, 
            'vmcuMaxVal' => $vmcuMaxVal, 
            'vmcuMinVal' => $vmcuMinVal, 
        );
    }

    /**
     * Resetting a table
     * @param $tb_name
     */
    private function cleanDBTable($tb_name)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table($tb_name)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Insert data into problem table     
     * @param $nd_id, 
     * @param $nd_name, 
     * @param $prob_id, 
     * @param $criticality
     * @return null
     */
    private function insertIntoProbTb($nd_id, $nd_name, $prob_id, $criticality)
    {
        /*
         * Field        possible values
         * id
         * source       ('sensor','station','2m_node','10m_node','sink_node','ground_node')
         * source_id
         * criticality  ('critical', 'non-critical')
         * classification_id
         * track_counter
         * status       ('reported', 'investigation', 'solved')
         * created_at
         * updated_at
         */
        DB::table($this->prob_tb)->insert(
            ['source'=>$nd_name,'source_id'=>$nd_id,'criticality'=>$criticality,'classification_id'=>$prob_id,'track_counter'=>1,'status'=>'investigation', 'created_at'=>$this->getCurrentDateTime()]
        );
    }

    /**
     * check data in the problems table to see if problem has been reported yet.
     * @return problem
     * it excludes the solved problems and so should return only one record
     * this is because we don't record a problem again before it has been solved.
     */
    private function getProblem($nd_id,$source,$classification_id)
    {
        // id, source_id, track_counter
        $prob = DB::table($this->prob_tb)->select('id','source_id','track_counter','status')->where([
            ['source_id','=',$nd_id],
            ['source','=',$source],
            ['status','<>','solved'],
            ['classification_id','=',$classification_id],
        ])->get();
        
        return $prob;
    }

    /**
     * call reporter to report problem
     */
    private function getReporter($prob_id)
    {
        //............ call reporter here.............................

        // update status if reporting was done successfully
        DB::table($this->prob_tb)->where('id',$prob_id)->update(['status'=>'reported']);
    }

    /**
     * update problem 
     */
    private function updateProblem($prob_track_counter,$nd_id,$max_track_counter,$prob_status,$prob_id)
    {
        if ($prob_status == 'reported') {
            // update the updated_at column to affirm that the problem was encountered again
            DB::table($this->prob_tb)->where('id',$prob_id)->update(['updated_at'=>$this->getCurrentDateTime()]);
        }
        else {
            // check if max_counter had already been reached to avoid incrementing it again.
            if (($prob_track_counter)>= $max_track_counter) {
                $this->getReporter($prob_id);
                return;// exit method
            }
            DB::table($this->prob_tb)->where('id',$prob_id)->increment('track_counter');
            // check if max_counter has been reached and if so change status and call the reporter.
            if (($prob_track_counter + 1)>= $max_track_counter) {
                $this->getReporter($prob_id);
            }
        }        
    }

    /**
     * get current date
     */
    private function getCurrentDateTime()
    {
        $zone = new DateTimeZone($this->timezone);// set timezone
        $date = new DateTime("now", $zone);// get current time
        
        return $date;
    }

    /**
     * Get time difference to use when quering the node_status table
     */
    private function getTimeDiff()
    {
        // "date_time_recorded": "2018-02-28 15:40:19"
        $date = $this->getCurrentDateTime();// get current time
        // $date->sub(new DateInterval('PT1H'));// subtract one hour from current time
        $date = $date->format('Y-m-d H:m:s');// change date time object to string format used in the database.
        return $date;
    }

    /**
     * 
     */
    private function registerProblem($nd_id, $nd_name, $problem_id, $criticality, $max_track_counter)
    {
        // check data in the problems table to see if problem has been reported yet.
        $prob = $this->getProblem($nd_id,$nd_name,$problem_id); 
        
        if ($prob->isEmpty()) {
            /**
             * record doesn't exit in the database and so..registerProblem($nd_id, $nd_name, $problem->id, $criticality, $max_track_counter)
             * insert into the the problem into the database
             * at this point, we get the criticality of this problem
             */
            $this->insertIntoProbTb($nd_id, $nd_name, $problem_id, $criticality);
        }
        else {
            /**
             * $nd_id, $nd_name, $problem->id, $criticality, $max_track_counter
             * problem exists
             * if problem is not yet reported, then increment the counter
             * after increment, check if the counter has reached max, if so, call the reporter and then set the status to reported.
             * if status is reported, then just update the 'updated_at' column to show that the problem was realised
             */

            $prob = $prob->toArray();
            
            $this->updateProblem($prob[0]->track_counter,$nd_id,$max_track_counter,$prob[0]->status,$prob[0]->id);
        }
    }
}