<?php
/**
 * 合服，人气排行榜合并
 * 临时使用
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$serverList = array(52,53,54,55,56,57,58,59,60,61,62,63,64,65);//合服id集合
hefu($serverList);


exit();

function hefu($serverList){
    $key = 'popular_redis';
    foreach ($serverList as $sid){

        $SevCfg = Common::getSevidCfg($sid);
        if($SevCfg['sevid'] != $SevCfg['he']){//遍历的服务器不是合服id
            if(empty($key)) return;
            $my_redis = Common::getRedisBySevId($SevCfg['sevid']);
            $he_redis = Common::getDftRedis();
            $he_cache = Common::getDftMem();
            //数据合并
            $rdata  = $my_redis->zRevRange($key, 0, -1,true);  //获取排行数据

            if(empty($rdata)){
                continue;
            }
            foreach($rdata as $uid => $score){
                $he_redis->ZINCRBY($key,$score,$uid);
                unset($uid,$score);
                $he_cache->delete($key.'_msg');
            }
            echo $sid.'区转移完成',PHP_EOL;
            unset($rdata,$v);
        }

    }
}