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
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$data = array(
    2000598 => 1040,
    2000754 => 680,
    2000876 => 680,
    2002977 => 680,
    2004417 => 540,
    2005505 => 480,
    2008657 => 400,
);

foreach ( $data as $uid => $money) {


    $serv = Game::get_sevid($uid);
    $SevidCfg = Common::getSevidCfg($serv);//子服ID
    //用户类
    $UserModel = Master::getUser($uid);

    echo $uid.'_'.$UserModel->info['cash_buy']."\n";

    $qian = intval($money/10);
    $UserModel->add_cash_buy($money,$qian,1);
    $UserModel->destroy();


    echo $uid.'_____'.$money.'_____'.$UserModel->info['cash_buy']."\n";

}

Master::click_destroy();


echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();
















