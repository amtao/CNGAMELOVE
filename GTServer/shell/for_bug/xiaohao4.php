<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
Common::loadModel('ClubModel');
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);

if ( is_array($serverList) ) {

    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID



        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {

            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {

            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {

            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if($open_day <= 0){
            continue;
        }

        data();
    }
}

exit();



/*
 * 小号脚本
 */
function data(){
    $todayTime = strtotime('2018-06-21 00:00:00');
    $db = Common::getMyDb();
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table = 'user_0'.$i;
        }else{
            $table = 'user_'.$i;
        }
        $sql = "select `uid`,`name`,`level`,`vip`,`cash_buy`,`regtime`,`lastlogin`,`platform`,`ip` from {$table} where `lastlogin` > {$todayTime}";
        $data[$table] = $db->fetchArray($sql);
        echo mysql_error();
    }
    foreach ($data as $key => $value){
        foreach ($value as $v) {
            //echo $v['uid'].','.$v['name'].','.$v['level'].','.$v['vip'].','.$v['cash_buy'].','.date('Y-m-d H:i:s',$v['regtime']).','.date('Y-m-d H:i:s',$v['lastlogin']).','.$v['ip'].'PHP_EOL';
            echo implode(',', $v).PHP_EOL;
        }
    }
}