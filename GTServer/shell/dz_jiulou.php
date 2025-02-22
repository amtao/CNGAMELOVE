<?php
/**
 * 酒楼限时冲榜
 * 清理小号外挂冲榜
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {

    $data = array();
    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服I

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {

            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {

            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {

            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if($open_day <= 0){
            continue;
        }
        jiulou_rank();
    }
}

exit();


/*
 * 根据桌位购买记录更新act数据
 */
function jiulou_rank(){
    $db2 = Common::getMyDb('flow');
    $arr = array(41016785,41016750,41016950,41017250,41016790,41016784,41017308,41016738,41017238,41016780,41017000,41016777,41017307,41016781,41017304,41016755,41016955,41017255,41017305,41016783,41016993,41016742,41016942,41017242,41017542,41017310,41016741,41016941,41017241,41017541,41016776,41016778,41016782,41016789,41016779,41016727,41016728,41016729,41016730,41016739,41016939,41017239,41016740,41016940,41017240,41017540,41016743,41016943,41017243,41017543,41016744,41016944,41017244,41017544,41016745,41016945,41017245,41017545,41016746,41016946,41017246,41017546,41016747,41016947,41017247,41017547,41016748,41016948,41017248,41017548,41016749,41016949,41017249,41017549,41016751,41016951,41017251,41016752,41016952,41017252,41016753,41016953,41017253,41016754,41016954,41017254,41016756,41016956,41016757,41016957,41016758,41016958,41016759,41016769,41016770,41016771,41016772,41016773,41016774,41016775,41016786,41016787,41016788,41016992,41016994,41016995,41016996,41016997,41016999,41017001,41017002,41017003,41017303,41017237,41017309,41017311,41017312,41017313);
    foreach ($arr as $v) {
        $table_id = Common::computeTableId($v);
        $sql2 = "select `uid`,`ftime`,`ip`,`cha`,`next` from `flow_event_{$table_id}` JOIN `flow_record_{$table_id}`  where `uid` = {$v} and `id` = `flowid` and `ftime` > 1511798400 and `model` = 'jiulou' and `ctrl` = 'yhChi';";
        $row = $db2->fetchRow($sql2);
        if(!empty($row)){
            $time = date("Y-m-d H:i",$row['ftime']) ;
            echo '酒楼    吃宴会 宴会积分    ',$row['uid'],' ',$row['cha'],' ',$row['next'],'    ',$time,'   ',$row['ip'],PHP_EOL;
        }
    }
    unset($arr);
}

