<?php
//数据统计
exit();
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
Common::loadModel('ServerModel');
$redis3 = Common::getRedisNoPrekeyBySevId(32);
$redis_shili_key = "huodong_280_my_20171001_redis";
$shili3 = $redis3->zRevRange($redis_shili_key, 0, -1,true);
foreach ($shili3 as $shili3Key => $shili3Value){
    if ($shili3Key > 32000000){
        echo '新官上任:'.$shili3Key.'->'.$shili3Value.'</br>';
        $shiliData3[$shili3Key] = $shili3Value;
    }
}
$redis11 = Common::getRedisBySevId(31);
$shili11 = $redis11->zRevRange($redis_shili_key, 0, -1,true);
$guanka11 = $redis11->zRevRange($redis_guanka_key, 0, -1,true);
foreach ($shiliData3 as $sk => $sv){
        //加入11服势力榜单
        $redis11->zIncrBy($redis_shili_key, $sv, $sk);
    /*//删除3服势力榜单
    $redis3->zDelete($redis_shili_key, $sk);*/
    echo $sk.'操作完成<br/>';
}