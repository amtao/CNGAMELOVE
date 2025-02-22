<?php
set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
$sid = intval($_SERVER['argv'][1]);// 默认是全部区
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$SevCfg = Common::getSevidCfg($sid);
Common::loadModel('Master');

$SonModel = Master::getSon(133009936);
$Act134Model = Master::getAct134(133009936);

$info1 = $SonModel->info['502'];
//$son1 = $SonModel->getBase_buyid(502);
$myqjlove = $Act134Model->get_love($info1['spuid'],0);
$fqjlove = $Act134Model->get_love($info1['spuid'],1);
echo '1-----'.$myqjlove,'-------'.$fqjlove,PHP_EOL;
echo $info1['sonuid'].'------'.$info1['spuid'],PHP_EOL;
echo $SonModel->check_qjadd(133009936,$info1['sonuid'],$info1['spuid']),PHP_EOL;
$qjadd = $SonModel->get_qjadd();
if(empty($qjadd[133009936])){
    echo '死了1';exit;
}
$is_add = 0;
$fuid = 133009936;
$qjuid = $info1['spuid'];
$fsonid = $info1['sonuid'];
arsort($qjadd[$fuid]);
$qjnewadd = $qjadd[$fuid];
if(empty($qjadd[$fuid][$qjuid])){
    echo '死了2';exit;
}
arsort($qjadd[$fuid][$qjuid]);
$qjnewadd = $qjadd[$fuid][$qjuid];

$fUserModel = Master::getUser($fuid);
$vip_cfg = Game::getcfg_info('vip',$fUserModel->info['vip']);
//vip加成   默认10人(亲家1对1)
$add_qjvip = empty($vip_cfg['qingjia'])?10:10+$vip_cfg['qingjia'];
print_r($qjnewadd);

echo $add_qjvip,PHP_EOL;

$new_qjadd = array_slice($qjnewadd,0,$add_qjvip,true);

print_r($new_qjadd);
if(!empty($new_qjadd[$fsonid])){
    $is_add = 1;
}

echo $is_add;


//print_r($son1);
//
//
//print_r($son2);

