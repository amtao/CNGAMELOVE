<?php
//衙门打人记录

require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

Common::loadModel('ServerModel');

$uid = '287000113';// $uid
$startTime = strtotime('2018-06-04 14:00:00');// $startTime
$endTime = strtotime('2018-06-04 15:30:00');// $endTime

$sevid = Game::get_sevid($uid);
$SevidCfg = Common::getSevidCfg($sevid);
Common::loadModel('UserModel');
$Act62Model = Master::getAct62($uid);
foreach ($Act62Model->info as $k => $v) {
    if($v['dtime'] >= $startTime && $v['dtime'] <= $endTime && $v['kill']==0){
        $UserModel = Master::getUser($v['uid']);
        echo $v['uid'].','.$UserModel->info['name'].','.$UserModel->info['level'].','.$UserModel->info['vip'].','.$UserModel->info['cash_buy'],PHP_EOL;
    }
}