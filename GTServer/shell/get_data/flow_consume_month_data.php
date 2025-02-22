<?php
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
        if (empty($v)) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        //echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $Sev_Cfg['sevid']) {
            //echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if (0 < $serverID && $serverID != $Sev_Cfg['sevid']) {
            //echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID
        ) {
            //echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if ($open_day <= 0) {
            continue;
        }
        $ls = getdata($Sev_Cfg['sevid']);
        foreach ($ls as $ls_v) {
            if (!isset($total[$ls_v['month']])) {
                $total[$ls_v['month']] = 0;
            }
            $total[$ls_v['month']] += $ls_v['consume'];
        }
    }
    ksort($total);
    echo '年月',',','元宝消耗',PHP_EOL;
    foreach ($total as $month => $consume) {
        echo $month,',',$consume,PHP_EOL;
    }
}
function getdata($sid){
    //一月到6月
    $start = strtotime("2018-01-01 00:00:00");
    $end = strtotime("2018-07-01 00:00:00");
    $db = Common::getDbBySevId($sid, 'flow');

    $sql = "SELECT FROM_UNIXTIME(`time`,'%Y-%m') as month,SUM(`num`) as consume 
FROM `flow_consume` 
WHERE `from`='shop' AND `time` BETWEEN $start AND $end
GROUP BY FROM_UNIXTIME(`time`,'%Y-%m') 
ORDER BY FROM_UNIXTIME(`time`,'%Y-%m');";

    $res = $db->fetchArray($sql);
    return $res;
}