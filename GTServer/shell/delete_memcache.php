<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 * 
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$serverid = ServerModel::getDefaultServerId();
$mccache = Common::getCacheBySevId($serverid);
$date = strtotime('2017-08-01 00:00:00');
$now = time();
while ($date < $now){
    $mccache->delete('REGISTER_CX_'.$date);
    $mccache->delete('ORDER_CX_'.$date);
    $date = $date+24*3600; 
}

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();