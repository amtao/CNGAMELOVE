<?php
/**
 * 酒楼限时冲榜
 * 还原冲榜积分
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$data = array();
$uid = array(35003926,23010844,54003622,56008527,13001941,22003671,47007273,13010902,36006292,35005002,19000870,22006613,22009607,29006619,59001741,22009887,23010233,10002889,36015982,19010396,1027417,16008839,19000463,48004656,22009595,22000200,60000386,48010799,56001805,56006947,58011871,49005854,58008153,56001999,26015884,58019196,56011157,19008425,22007367,25000806,58013896,58005616,36013155,25007448,41005393,58013762,58015080,58013134,10003460,58018866,22013166,48006269);
$score = array(370000,211100,207500,202800,202000,200200,184100,169000,163100,156800,151000,144900,144600,135100,134000,130100,128500,122500,98500,89600,86200,84000,84000,81500,75300,73000,69700,68500,67700,57600,55600,52600,49500,45600,44600,40000,36400,34500,28000,26000,25800,25600,22600,22000,22000,20600,15800,12600,11000,10400,10200,3000);
foreach ($uid as $key => $value){
    $data[$value] = $score[$key];
}
foreach ($data as $key => $value){
    $serverID = Game::get_sevid($key);// 默认是全部区
    $redis = Common::getRedisBySevId($serverID);
    $redis_key = 'huodong_256_20171128_redis';
    //$redis->zDelete($key,$value);
    $score = $redis->zScore($redis_key,$key);
    if($score > 0){
        $score -= $value;
    }
    if(empty($score)){
       $score = 0;
    }
    $redis->zAdd($redis_key,$score,$key);
    $serCfg = Common::getSevidCfg($serverID);
    $act256Model = Master::getAct256($key);
    $act256Model->info['num'] = $score;
    $act256Model->save();
    $act256Model->ht_destroy();
}

