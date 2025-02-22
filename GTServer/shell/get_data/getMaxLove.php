<?php
/**
 * 获取最大红颜最大亲密度
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {
    $total = array();
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
        $db = Common::getMyDb();
        $uid = 0;
        $love = 0;
        for($i=0;$i<100;$i++){
            if($i < 10){
                $table = "wife_0{$i}";
            }else{
                $table = "wife_{$i}";
            }
            $sql = "select `uid`,`love` from {$table} ORDER BY `love` DESC limit 1";
            $ls_data = $db->fetchArray($sql);
            if(!empty($ls_data) && $ls_data[0]['love']>$love){
                $uid = $ls_data[0]['uid'];
                $love = $ls_data[0]['love'];
            }
        }
        $total[$uid] = $love;
    }
    Game::logMsg('/tmp/maxLove'.Game::get_today_id(),json_encode($total));
    echo '最大的亲密度:'.max($total),PHP_EOL;
    echo 'uid:';
    print_r(array_keys($total,max($total)));
}