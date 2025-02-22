<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

if ( is_array($serverList) ) {

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
        do_account();
    }
}

function do_account(){

    $Sev26Model = Master::getSev26();
    if(!empty($Sev26Model->info)){
        $Redis12Model = Master::getRedis12();
        Game::logMsg('/tmp/trans_seal.log',json_encode($Sev26Model->info));
        foreach ($Sev26Model->info as $uid => $time){
            $Redis12Model->add_sb($uid,1);
            $Act59Model = Master::getAct59($uid);
            $Act59Model->info['account'] = array(
                'type' => 1,
                'start_time' =>$time,
                'end_time' => 0
            );
            $Act59Model->save();
            $Act59Model->ht_destroy();
        }
    }
}