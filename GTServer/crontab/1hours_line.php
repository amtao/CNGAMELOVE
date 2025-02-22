<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每小时跑一次
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$serverList = include(ROOT_DIR.'/config/server.php');
Common::loadModel('ServerModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
if ($serverID == 999) {
    $serverID = 999;
    $crontabName = $serverID."_1hours_line";
    $btime = microtime(true);
    $nowTime = date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']);

    //服务器过滤
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    // Game::crontab_debug("服务器ID：:".$SevidCfg['sevid'], $crontabName);

    //活动发放奖励  --   每个区各自发放
    on_line_time($SevidCfg, $crontabName);
    on_line_count($SevidCfg, $crontabName);

    Game::crontab_debug("执行时间:".$nowTime."      耗时(s)=".(microtime(true)-$btime), $crontabName);
    Game::crontab_debug("-------------------------------------------------------------------", $crontabName);

    exit();
}

foreach ($serverList as $key => $value) {

    $serverID = intval($value["sevId"]);// 默认是全部区
    if ($value["isOpen"] == 0) {
        continue;   // 未开服
    }

    $crontabName = $serverID."_1hours_line";
    $btime = microtime(true);
    $nowTime = date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']);
    // Game::crontab_debug("当前时间:".date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), $crontabName);

    //服务器过滤
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    // Game::crontab_debug("服务器ID：:".$SevidCfg['sevid'], $crontabName);
    if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
        // Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }
    if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
        // Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }
    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
        && $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
        // Game::crontab_debug(">>>从服跳过", $crontabName);
        continue;
    }

    if($SevidCfg['sevid'] != $SevidCfg['he']){
        // Game::crontab_debug(">>>不是指定合服id跳过", $crontabName);
        continue;
    }

    $open_day = ServerModel::isOpen($serverID);
    //过滤未开服的
    if($open_day <= 0){
        // Game::crontab_debug(">>>open_day：".$open_day, $crontabName);
        continue;
    }

    //活动发放奖励  --   每个区各自发放
    on_line_time($SevidCfg, $crontabName);
    on_line_count($SevidCfg, $crontabName);

    Game::crontab_debug("执行时间:".$nowTime."      耗时(s)=".(microtime(true)-$btime), $crontabName);
    Game::crontab_debug("-------------------------------------------------------------------", $crontabName);
}
exit();

/**
 * 更新用户的在线时长
 */
function on_line_time($SevidCfg, $crontabName){

    $redisKey = "user_on_line";
    $redis = Common::getDftRedis();
    $lineList = $redis->zRevRange($redisKey, 0, -1, true);
    if(empty($lineList)){
        return false;
    }

    $pIndex = 1;
    $valus = "";
    $count = count($lineList);
    $nowDay = $_SERVER['REQUEST_TIME'];
    foreach ($lineList as $uid => $lineTime) {
        if ($pIndex == $count) {
            $valus .= "({$nowDay}, '{$uid}', {$lineTime})";
        } else {
            $valus .= "({$nowDay}, '{$uid}', {$lineTime}),";
        }
        $pIndex++;
    }

    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $sql = "INSERT INTO `user_on_line_time` (`date`, `uid`, `lineTime`) VALUES {$valus}";
    if ($db->query($sql)) {
        $redis->delete($redisKey);
    }
}

/**
 * 保存在线用户数
 */
function on_line_count($SevidCfg, $crontabName){

    $redisKey = "user_last_login";
    $redis = Common::getDftRedis();
    $loginList = $redis->zRevRange($redisKey, 0, -1, true);
    if(empty($loginList)){
        return false;
    }

    $onLine = 0;
    foreach ($loginList as $uid => $loginTime) {

        $onLineTime = $_SERVER['REQUEST_TIME'] - $loginTime;
        if ($onLineTime >= 3600) {

            $redis->zDelete($redisKey, $uid);
            continue;
        }

        $onLine++;
    }

    $nowDay = $_SERVER['REQUEST_TIME'];
    $db = Common::getDbBySevId($SevidCfg['sevid']);

    $sql = "INSERT INTO `user_on_line_count` (`date`, `count`) VALUES ('{$nowDay}', {$onLine})";
    if ($db->query($sql)) {
        $redis->delete($redisKey);
    }
}