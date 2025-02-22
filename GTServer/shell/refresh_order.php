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

Common::loadModel("Master");
Common::loadModel("ClubModel");
Common::loadModel("FlowModel");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$startTime = strtotime('2018-04-11 00:00:00');
$endTime = strtotime('2018-04-11 23:59:59');






$uids = array(61000189);

foreach ($uids as $uid){

    $serverID = Game::get_sevid($uid);

    $Sev_Cfg = Common::getSevidCfg($serverID);//子服ID
    $SevidCfg = Common::getSevidCfg($Sev_Cfg['sevid']);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);
    $cache = Common::getCacheBySevId($SevidCfg['sevid']);


    $Act152Model = Master::getAct152($uid);
    $Act152Model->add(0);
    $cache->delete($uid.'_mail');
    $Act152Model->save();
    $Act152Model->ht_destroy();
    echo 'uid: '.$uid."已补\n";
}





echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();
















