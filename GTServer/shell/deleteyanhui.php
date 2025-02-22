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
    $db = Common::getMyDb();
    $uids = array();
    for ($i=0;$i<=99;$i++){
        $table = $i < 10 ? 'user_0'.$i : 'user_'.$i;
        $sql = "select `uid` from {$table} WHERE `name` LIKE '小米%' AND `ip`='222.73.183.88'";
        $res = $db->fetchArray($sql);
        if(!empty($res)){
            $uids = array_merge($uids,$res);
        }
    }
   if(!empty($uids)){
       $Redis9Model = Master::getRedis9();
       $key = 'huodong_256_20180226_redis';
       $db = Common::getDftRedis();
       foreach ($uids as $uid_arr){
           $Redis9Model->add_sb($uid_arr['uid']);
           $db->zDelete($key,$uid_arr['uid']);
       }
   }
    echo '操作完成';
}