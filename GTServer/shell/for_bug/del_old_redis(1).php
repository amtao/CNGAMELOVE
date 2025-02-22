<?php 
/**
 * 各种活动补偿
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$start = intval($_SERVER['argv'][1]);
$end = intval($_SERVER['argv'][2]);
if (empty($start) || empty($end)){
    echo "请传入起始跟结束参数!";
    exit();
}
Common::loadModel("Master");

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//活动时间范围
$startTime = strtotime('2018-04-27 00:00:00');
$endTime = strtotime('2018-04-27 23:59:59');


echo "进入脚本\n";
$serverIDs = array(
    2,3,8,11,12,13,14,15,16,17,18,19,20,24,25,29,30,34,35,39,40,44,45,49,50,54,55,59,60,64,65,69,70,74,75,79,80,84,85,89,90,94,95,99,100,104,105,109,110,114,115,120,125,130,135,140,145,150,155,160,165,170,174,175,180,184,185,190,194,195,200,204,205,210,214,215,220,224,225,230
);
foreach ($serverIDs as $sID) {
    echo 'server:'.$sID.PHP_EOL;
    $redis = Common::getRedisBySevId($sID);
    $config = Common::getConfig(GAME_MARK."/AllServerRedisConfig");

    //清理酒楼-消息-来宾统计
/*
    for ($i=8; $i<=12; $i++) {
        delRedis($redis, $sID, $config, "yhLaiBin*_170{$i}_*");
    }
    for ($i=1; $i<=3; $i++) {
        delRedis($redis, $sID, $config, "yhLaiBin*_180{$i}_*");
    }
    //葛二蛋伤害排行
    $startTime = strtotime('2017-12-16 00:00:00');
    $endTime = strtotime('2018-04-20 23:59:59');
    while ($startTime < $endTime) {
        $dayID = date('ymd', $startTime);
        delRedis($redis, $sID, $config, "*wordboss_{$dayID}_*");
        $startTime += 86400;
    }
    //清理关卡冲榜排行
    delRedis($redis, $sID, $config, "*huodong_251*");


    //开服1-8天
    delRedis($redis, $sID, $config, "*huodong_202_20171001*");
    delRedis($redis, $sID, $config, "*huodong_203_20171001*");
    delRedis($redis, $sID, $config, "*huodong_204_20170901*");
    delRedis($redis, $sID, $config, "*huodong_205_20170901*");
    delRedis($redis, $sID, $config, "*huodong_206_20170901*");
    delRedis($redis, $sID, $config, "*huodong_207_20170901*");
    delRedis($redis, $sID, $config, "*huodong_208_20170901*");
    delRedis($redis, $sID, $config, "*huodong_209_20170901*");
    delRedis($redis, $sID, $config, "*huodong_210_20170901*");
    delRedis($redis, $sID, $config, "*huodong_211_20170901*");
    //开服9-15天
    delRedis($redis, $sID, $config, "*huodong_204_20170902*");
    delRedis($redis, $sID, $config, "*huodong_206_20170902*");
    delRedis($redis, $sID, $config, "*huodong_208_20170902*");
    delRedis($redis, $sID, $config, "*huodong_209_20170902*");
    delRedis($redis, $sID, $config, "*huodong_212_20170902*");
    delRedis($redis, $sID, $config, "*huodong_213_20170902*");
    delRedis($redis, $sID, $config, "*huodong_214_20170902*");
    //开服1-8天
    delRedis($redis, $sID, $config, "*huodong_252_20171001*");*/

/*
     //官方混服清理
     $huodong = array(
        202=>array(20180523,20180522,20180515,20180427,20180426,20180425,20180106,20180122,20180101,20171005,20171001),
        203=>array(20180605,20180601,20180523,20180522,20180515,20180427,20180426,20180425,20180122,20180101,20171005,20171001),
        204=>array(20180608,20180607,20180606,20180606,20180605,20180605,20180601,20180531,20180531,20180530,20180529,20180528,20180523,20180522,20180515,20180427,20180426,20180425,20180105,20180122,20180103,20180101,20170902,20170901,20170904),
        205=>array(20180607,20180604,20180603,20180602,20180601,20180527,20180526,20180518,20180102,20170903,20170901),
        206=>array(20180606,20180605,20180605,20180604,20180603,20180602,20180601,20180601,20180531,20180531,20180530,20180529,20180528,20180527,20180526,20180523,20180522,20180518,20180515,20180427,20180426,20180425,20180106,20180105,20180122,20180103,20180102,20180101,20170903,20170902,20170901,20170904),
        207=>array(20170901,20180101),
        208=>array(20180608,20180607,20180607,20180606,20180606,20180605,20180605,20180604,20180603,20180602,20180601,20180601,20180531,20180531,20180530,20180529,20180528,20180527,20180526,20180523,20180522,20180518,20180515,20180427,20180426,20180425,20180106,20180105,20180122,20180103,20180102,20180101,20171005,20170903,20170902,20170901,20170904),
        209=>array(20180608,20180607,20180606,20180605,20180531,20180531,20180530,20180529,20180528,20180105,20180101,20170902,20170901,),
        210=>array(20180605,20180601,20180523,20180522,20180515,20180427,20180426,20180425,20180106,20180122,20180101,20170901,),
        211=>array(20170901,20180101),
        212=>array(20170902),
        213=>array(20170602,20180102),
        214=>array(20170902),
        252=>array(20180101),
    );
    foreach ($huodong as $hid => $v) {
        foreach ($v as $hh_id) {
            delRedis($redis, $sID, $config, "*huodong_{$hid}_{$hh_id}*");
        }
    }*/
    //限时排行榜
    $huodong = array(
        201,202,203,204,205,206,207,208,209,210,211,212,213,214,
        215,216,217,218,219,220,221,222,223,224,225,226,227
    );
    foreach ($huodong as $hid) {
        delRedisByTime($redis, $sID, $config, "*huodong_{$hid}*");
    }
}

/**
 * @param Redis $redis
 * @param int $sID
 * @param string $keyName
 */
function delRedis($redis, $sID, $config, $keyName) {
    $keys = $redis->keys($keyName);
    echo 'start:'.count($keys).PHP_EOL;
    if (!empty($config[$sID]['preKey'])) {
        foreach ($keys as &$kkk) {
            $kkk = ltrim($kkk, $config[$sID]['preKey'].'_');
        }
    }
    if (!empty($keys)) {
        $redis->delete($keys);
    }
    $keys = $redis->keys($keyName);
    echo 'end:'.count($keys).PHP_EOL;
}

/**
 * @param Redis $redis
 * @param int $sID
 * @param string $keyName
 */
function delRedisByTime($redis, $sID, $config, $keyName) {
    $keys = $redis->keys($keyName);
    $delKeys = array();
    foreach ($keys as $kkk) {
        if (!empty($config[$sID]['preKey'])) {
            $kkk = ltrim($kkk, $config[$sID]['preKey'] . '_');
        }
        $arr = explode('_', $kkk);
        if ($arr[2] > 20180701) {
            continue;
        }
        $delKeys = $kkk;
    }
    if (!empty($delKeys)) {
        $redis->delete($delKeys);
    }
}