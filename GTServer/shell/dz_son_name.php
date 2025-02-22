<?php
/**
 * 订正子嗣名字反斜杠
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ClubModel');
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
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
        echo '生效时间'.$open_day."\n";
        son_name();
    }
}

exit();


function son_name(){
    $db = Common::getMyDb();
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table = 'son_0'.$i;
        }else{
            $table = 'son_'.$i;
        }
        $sql = "select `uid`,`sonuid`,`name` from {$table} where `name` like '%\\\%';";
        $data = $db->fetchArray($sql);
        echo mysql_error();
        foreach ($data as $v){
            echo $v['uid'],">>>>",$v['sonuid'],">>>>",$v['name'];
            $name = str_replace("\\",'',$v['name']);
            $sql = "update {$table} set `name` = {$name} where `uid` = {$v['uid']} and `sonuid` = {$v['sonuid']};";
            $db->query($sql);
        }
    }

}