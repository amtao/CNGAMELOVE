<?php
//正式服咸鱼日志脚本  * * * * *  /usr/local/services/php/bin/php /data/www/kingh5hf/s1_kingh5hf/crontab/xianyu_log.php > /data/logs/kingh5hf_log/xianyu_log 2>&1
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
echo PHP_EOL, 'serverID=', $serverID, PHP_EOL;
$defaultSid = ServerModel::getDefaultServerId();
echo PHP_EOL, '----------------默认服务器'.$defaultSid.'----------------------', PHP_EOL;
$SevidCfg = Common::getSevidCfg($defaultSid);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;

$btime = microtime(true);
$current_time = $_SERVER['REQUEST_TIME'];
echo PHP_EOL, '当前时间 ', date('Y-m-d H:i:s', $current_time), PHP_EOL;
//咸鱼日志
$xianyu_log_path_old = array(
    'role' => '',  //创建角色信息
    'loginserver' => '',  //登录游戏服务器信息
    'loginsdk' => '',  //登录sdk信息
    'charge' => '',  //充值信息
    'online' => '',  //用户在线信息
    'consume' => '',  //一级代币消费信息
    'output' => '',  //一级代币产出信息
    'loginrole' => '',  //登录角色信息
    'rolelevel' => '',  //角色等级信息
    'vipgrade' => '',  //VIP等级信息
    'tutorial' => '',  //通过新手指引信息
    'roleinfo' => '',  //角色详细信息
    'item' => '',  //道具产出消耗信息
    'money' => '',  //二级代币变化日志
    'copy' => '',  //副本使用信息
);

Common::loadModel('XianYuLogModel');
$xianyu_path = XianYuLogModel::getXianyuPath();
foreach ($xianyu_log_path_old as $key=>$value){
    $xianyu_log_file_old = $xianyu_path . $key . '/' . date('Ymd') . '/' . $key . '_' . date('Ymd_Hi', $current_time - 60) . '.log.temp';
    $xianyu_log_file_new = str_replace(".temp", "", $xianyu_log_file_old);
    if(file_exists($xianyu_log_file_old)){
        rename($xianyu_log_file_old, $xianyu_log_file_new);
    }
}

echo PHP_EOL, '耗时(s)=', (microtime(true) - $btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();








