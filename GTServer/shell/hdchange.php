<?php
/**
 * 281活动修改
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$Sev_Cfg = Common::getSevidCfg($serverID);//子服ID

echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

change();

exit();


function change(){
    $key = 'huodong_281_my_20171028_redis';
    $club_key = 'huodong_281_club_20171028_redis';
    $reidsdb = Common::getDftRedis();
    $rdata = $reidsdb->zRevRange($key, 0, -1,true);
    if(!empty($rdata)){
        $reidsdb->delete($club_key);
        foreach ($rdata as $uid => $score){
            $Act40Model = Master::getAct40($uid);
            if (!empty($Act40Model->info['cid'])) {
                $reidsdb->zIncrBy($club_key,intval($score),$Act40Model->info['cid']);
            }
        }
        echo '操作完成';
    }else{
        echo '没有数据';
    }
}