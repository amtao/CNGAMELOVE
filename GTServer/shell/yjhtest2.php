<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$start = intval($_SERVER['argv'][1]);// 默认是全部区
$end = intval($_SERVER['argv'][2]);
if (empty($start) || empty($end)){
    echo "请传入起始跟结束参数!";
    exit();
}
Common::loadModel("Master");

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;



$hd_bug = 262; //对应活动序号
//$text_uid = 0;  //测试uid   0:所有   111000482

$startTime = strtotime('2018-04-24 00:00:00');
$endTime =   strtotime('2018-04-24 14:40:00');



$all = array();


$SevidCfg = Common::getSevidCfg($serverID);//子服ID
$db = Common::getDbBySevId($SevidCfg['sevid']);
$table_div = Common::get_table_div($SevidCfg['sevid']);


for ($i = 0 ; $i < $table_div ; $i++) {

    $table_user = 'user_'.Common::computeTableId($i);
    echo $table_user."\n";
    $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
    $res = $db->fetchArray($sql_user);

    if(!empty($res)){
        foreach ($res as $val){
            $uid = $val['uid'];

            if(!empty($text_uid) && $uid != $text_uid){
                continue;
            }

            $lastlogin = $val['lastlogin'];

            if($lastlogin < $startTime){
                continue;
            }


            //此处该为对应的限时活动
            $Act202Model = Master::getAct202($uid);
            //活动对应的数值
            $Act202Model->info['cons'];


            //活动redi对应的key
            $key = 'huodong_202_20180424_redis';
            //将数值Zadd到redis中

        }
    }
}