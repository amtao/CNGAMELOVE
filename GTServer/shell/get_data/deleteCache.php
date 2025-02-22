<?php
/**
 * 删除不常用缓存
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);

//echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {
    $total = array();
    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        //echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
            // echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
            //echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            // echo PHP_EOL, '>>>从服跳过', PHP_EOL;
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
    $db = Common::getMyDb('flow');
    $cache = Common::getMyMem();
    $total = 0;
    $now = Game::get_now();
    $Time1 = $now - 3*24*60*60;
    $Time2 = $now - 7*24*60*60;
    $Time3 = $now - 14*24*60*60;
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table = '0'.$i;
        }else{
            $table = $i;
        }
        $sql = "select `uid` from `user_{$table}` where (`level`=0 and `lastlogin` < {$Time1}) or (`level` = 1 and `lastlogin`< {$Time2}) or (`level` = 2 and `lastlogin` <{$Time3})";
        $data = $db->fetchArray($sql);
        foreach ($data as $value){
            echo $value['uid'],PHP_EOL;
            deleteCache($value['uid'],$cache);
        }
    }
    return $total;
}

function deleteCache($uid,$cache){
    $cache->delete($uid.'_user');
    $cache->delete($uid.'_team');
    $cache->delete($uid.'_wife');
    $cache->delete($uid.'_son');
    $cache->delete($uid.'_hero');
    $cache->delete($uid.'_item');
    $cache->delete($uid.'_mail');

    //act数据
    Common::loadActModel('ActBaseModel');
    $info = array();
    foreach (ActBaseModel::$rightActTypes as $actType => $actComment) {
        $cache->delete($uid.'_act_'.$actType);
    }
    return $info;
}
