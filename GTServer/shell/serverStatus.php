<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29
 * Time: 10:42
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$btime = microtime(true);
echo "mem:",checkMem($serverID),PHP_EOL;
echo "redis:",checkRedis($serverID),PHP_EOL;
function checkMem($sevid){
    $mes = "错误";
    $mem = Common::getCacheBySevId($sevid);
    $mem->set('serverStatus','正常');
    $data = $mem->get('serverStatus');
    if($data !== false){
        $mes = $data;
    }
    return $mes;
}

function checkRedis($sevid){
    $mes = "错误";
    $redis = Common::getRedisBySevId($sevid);
    $redis->set('serverStatus','正常');
    $data = $redis->get('serverStatus');
    if($data !== false){
        $mes = $data;
    }
    return $mes;
}