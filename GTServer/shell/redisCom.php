<?php
/**
 * 合服，排行榜合并
 * 临时使用
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$serverList = array(1,2,3,4);//合服id集合
hefu($serverList);


exit();

function hefu($serverList){
    $key = 'huodong_336_20180520_redis';
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
                $he_redis->zAdd($key,$score,$uid);
                unset($uid,$score);
                $he_cache->delete($key.'_msg');
            }
            echo $key.'转移完成',PHP_EOL;
            unset($rdata,$v);
        }

    }
}