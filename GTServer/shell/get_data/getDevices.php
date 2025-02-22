<?php
/**
 * 玩家的设备号和充值
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';

Common::loadModel('ServerModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {
    $total_num = 0;
    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if($open_day <= 0){
            continue;
        }
        getData();
    }
}

function getData(){
    $db = Common::getMyDb();
    $sql = "select `uid`,`device` from `devices`;";
    $data = $db->fetchArray($sql);
    if(!empty($data)){
        foreach ($data as $k => $v){
            $UserModel = Master::getUser($v['uid']);
            $cz = $UserModel->info['cash_buy'] > 0 ? 1 : 0;
            Game::logMsg('/tmp/devices'.GAME_MARK,$v['uid'].','.$v['device'].','.$cz);
        }
    }
}