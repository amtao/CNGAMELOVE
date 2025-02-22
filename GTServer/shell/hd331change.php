<?php

//跨服衙门出现bug备用
set_time_limit(0);
ini_set('memory_limit','1500M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$min = $serverID;
$max = $serverID;
$mUse = memory_get_usage();

for($sid = $min;$sid<=$max;$sid++){
    $Sev_Cfg = Common::getSevidCfg($sid);//子服ID

    echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
    echo $Sev_Cfg['sevid'],PHP_EOL;
    $db = Common::getDbBySevId($Sev_Cfg['sevid'],'flow');
    $total = array();
    for ($i = 0; $i < 99; $i++) {
        if($i < 10){
            $table1 = 'flow_event_0'.$i;
            $table2 = 'flow_records_0'.$i;
        }else{
            $table1 = 'flow_event_'.$i;
            $table2 = 'flow_records_'.$i;
        }
        $sql = "SELECT a.uid,b.* FROM {$table1} a JOIN {$table2} b ON a.id = b.flowid WHERE a.ftime > 1520870400 AND a.ftime < 1520906400 AND b.type = 1 AND b.itemid = 2 AND b.cha<0";
        $records = $db->fetchArray($sql);
        if(!empty($records)){
            $total = array_merge($total,$records);
        }
    }

    if(!empty($total)){
        $data = array();
        foreach ($total as $val){
            $data[$val['uid']] = empty($data[$val['uid']]) ? intval(-$val['cha']) : $data[$val['uid']]+intval(-$val['cha']);
        }
        Game::logMsg('/tmp/hd331'.$sid,json_encode($data));
        unset($total);
        print_r($data);
        foreach ($data as $uid => $score){
            $Act331Model = Master::getAct331($uid);
            $Act331Model->add($score);
            $Act331Model->ht_destroy();
        }
    }
    echo "当前内存使用:".(memory_get_usage() - $mUse),PHP_EOL;
}