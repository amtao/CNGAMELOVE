<?php
//数据统计
exit();
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$redis3 = Common::getRedisBySevId(3);
$redis_key = "huodong_280_my_20171001_redis";
$redis11 = Common::getRedisBySevId(11);
$shili3 = $redis3->zRevRange($redis_key, 0, -1,true);
foreach ($shili3 as $shili3Key => $shili3Value){
    if ($shili3Key > 4000000){
        $redis3->zDelete($redis_key, $shili3Key);
        $redis11->zIncrBy($redis_key, $shili3Value, $shili3Key);
        echo $shili3Key.'处理成功<br/>';
    }

}
echo '完成';

