<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5
 * Time: 16:20
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$serverList = array(1,3,5);//合服id集合
export($serverList);


exit();

function export($serverList){
    $key = array(//需要遍历的redis_key
        'shili_redis',//势力排行
        'guanka_redis',//关卡排行
        'love_redis',//亲密排行
        'fbscore_redis',//副本积分排行
        'yamen_redis',//衙门积分排行
        'club_redis',//公会排行
        'taofa_redis',//乱党
        'trade_redis',//丝绸之路
        'jiulou_redis',//酒楼-宴会排行榜
        'yhLaiBin_redis',//酒楼-消息-来宾统计
    );
    foreach ($serverList as $sid){
        echo $sid.'区',PHP_EOL;
        $redis = Common::getRedisBySevId($sid);
        foreach ($key as $v){//数据合并
            echo $v,PHP_EOL;
            $rdata  = $redis->zRevRange($v, 0, -1,true);  //获取排行数据
            if(empty($rdata)){
                continue;
            }
            echo json_encode($rdata),PHP_EOL;
        }
    }
}