<?php
/**
 * 合服，导出redis
 * 临时使用
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$serverList = array(52,57,54,55);//合服id集合
export($serverList);


exit();

function export($serverList){
    $key = 'popular_redis';
    foreach ($serverList as $sid){

        Game::logMsg('/data/logs/popular_redis'.Game::get_today_id(),$sid.'区');

        $redis = Common::getRedisBySevId($sid);

        Game::logMsg('/data/logs/popular_redis'.Game::get_today_id(),$key);
        $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
        Game::logMsg('/data/logs/popular_redis'.Game::get_today_id(),json_encode($rdata));
    }
}