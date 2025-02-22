<?php
/**
 * 跨服帮会战脚本
 *
 */
set_time_limit(0);
ini_set('memory_limit','3000M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
Common::loadModel("ClubModel");
Common::loadModel("FlowModel");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$startTime = strtotime('2018-04-11 00:00:00');
$endTime = strtotime('2018-04-11 23:59:59');



/*







uid: 68001249  新数值: 50000 老数值: 67220


*/


$uids = array(82005779 => 42860,82001981 => 51840,82002062 => 70180,83002105=>20040);


foreach ($uids as $uid => $cons){

    $serverID = Game::get_sevid($uid);

    $Sev_Cfg = Common::getSevidCfg($serverID);//子服ID
    $SevidCfg = Common::getSevidCfg($Sev_Cfg['sevid']);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);
    $cache = Common::getCacheBySevId($SevidCfg['sevid']);


    $Act152Model = Master::getAct152($uid);


    if($cons >= 10000 ){
        if(!in_array($uid,array(82002062,82001981))){
            $Act152Model->info['get'][] = 10000;
        }

    }

    if($cons >= 20000 ){
        if(!in_array($uid,array(82002062,82001981))){
            $Act152Model->info['get'][] = 20000;
        }

    }

    if($cons >= 30000){
        $Act152Model->info['get'][] = 30000;
    }

    if($cons >= 50000){
        $Act152Model->info['get'][] = 50000;
    }

    if($cons >= 100000){
        $Act152Model->info['get'][] = 100000;
    }

    if($cons >= 200000){
        $Act152Model->info['get'][] = 200000;
    }



    $Act152Model->save();
    $Act152Model->ht_destroy();


    echo 'uid: '.$uid."已修复\n";
}



echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();
















