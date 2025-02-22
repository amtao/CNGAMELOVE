<?php
/**
 * 定时计划异步脚本
 * 调用方式：* * * * * /usr/local/php/bin/php /data/www/guaji/crontab/Sync.php 1 > /data/logs/guaji_log/guaji_s999_1_sync_all 2>&1
 */
set_time_limit(0);
$start = microtime(true);

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$serverList = include(ROOT_DIR.'/config/server.php');

Common::loadModel('ServerModel');
Common::loadModel('FlowModel');
Common::loadActModel('ActBaseModel');

set_time_limit(0);

foreach ($serverList as $key => $value) {

    $serverID = intval($value["sevId"]);// 默认是全部区
    if ($value["isOpen"] == 0) {
        continue;   // 未开服
    }

    $open_day = ServerModel::isOpen($serverID);
    //过滤未开服的
    if($open_day <= 0){
        continue;
    }

    $updateNum = 0;
    $crontabName = $serverID."_Sync";
    Game::crontab_debug('----------------begin sync----------------------', $crontabName);

    Common::loadLib('sync');
    $keys = array('user', 'act', 'item' ,'hero','wife','son','mail','acode','huodong','card','baowu');
    $time1 = microtime(true);
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    Game::crontab_debug("服务器ID：:".$SevidCfg['sevid'], $crontabName);

    if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
        Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }

    foreach ($keys as $key) {

        Game::crontab_debug('流水纪录：'.$key, $crontabName);
        if ($key == 'act') {
            foreach (ActBaseModel::$rightActTypes as $actType => $actComments) {
                $updateNum += Sync::doSync('1_'.$key.'_'.$actType, $crontabName);
            }
        } else {
            $updateNum += Sync::doSync('1_' . $key, $crontabName);
        }
    }

    Game::crontab_debug('异步写入：', $crontabName);
    //异步写入
    //$flowRecordNum += FlowModel::sync();

    $time2 = microtime(true);
    Game::crontab_debug('>>>执行完毕。time='.($time2-$time1), $crontabName);
    Game::crontab_debug('全部执行完毕。time='.(microtime(true)-$start), $crontabName);
    Game::crontab_debug('更新条数：'.$updateNum, $crontabName);
    //echo '流水纪录：',$flowRecordNum ,PHP_EOL;

    Game::crontab_debug('----------------end sync----------------------', $crontabName);

    $time = time();
    Game::crontab_debug("耗时(s)=".(microtime(true)-$time1), $crontabName);
    Game::crontab_debug("-------------------------------------------------------------------", $crontabName);
}
exit();
