<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$serid = 999;
$redis = Common::getRedisBySevId($serid);
$newRedisKey = "huodong_256_20170923_redis";
$oldRedisKey = "huodong_256_20170904_redis";
$newData  = $redis->zRevRange($newRedisKey, 0, -1,true);
$oldData  = $redis->zRevRange($oldRedisKey, 0, -1,true);
foreach ($oldData as $ok => $ov){
    foreach ($newData as $nk => $nv){
        if (isset($oldData[$nk])){
            if ($ok == $nk){
                echo $ok.'</br>';
                $oldData[$ok] = $ov + $nv;
            }
        }elseif(!isset($oldData[$nk])) {
            $oldData[$nk] = $nv;
        }
    }
}
foreach ($oldData as $k => $v){
    $redis->zAdd($oldRedisKey, $v, $k);
}
echo '成功';