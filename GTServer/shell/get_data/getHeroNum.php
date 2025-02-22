<?php
/**
 * 获取小号信息
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
        $num = getData();
        Game::logMsg('/tmp/heroNum',$Sev_Cfg['sevid'].'服'.$num);
        $total_num +=$num;
    }
    Game::logMsg('/tmp/heroNum','总个数:'.$total_num);
    echo '总个数:'.$total_num;
}

function getData(){
    $db_flow = Common::getMyDb('flow');
    $all = 0;
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table2 = 'flow_records_0'.$i;
            $table1 = 'flow_event_0'.$i;
        }else{
            $table2 = 'flow_records_'.$i;
            $table1 = 'flow_event_'.$i;
        }
        $sql = "select SUM(b.cha) as num from {$table1} as a JOIN {$table2} as b ON a.id=b.flowid where a.model='item' AND a.ctrl='hecheng' AND a.ftime>1527667200 AND b.type=6 AND b.itemid=153;";
        $data = $db_flow->fetchRow($sql);
        if(!empty($data['num'])){
            $all += $data['num'];
        }
    }
    return $all;
}