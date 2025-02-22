<?php

//同一时间内有做贡献的人啊
set_time_limit(0);
ini_set('memory_limit','1500M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$serverMinID = intval($_SERVER['argv'][1]);// 区服最小值
$serverMaxID = intval($_SERVER['argv'][2]);// 区服最大值
//$mUse = memory_get_usage();

//每个区
for($sid = $serverMinID;$sid<=$serverMaxID;$sid++){
    $Sev_Cfg = Common::getSevidCfg($sid);//子服ID

//    echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
//    echo $Sev_Cfg['sevid'],PHP_EOL;
    $db = Common::getDbBySevId($Sev_Cfg['sevid'],'flow');
    $sign = array('服务器ID'.$Sev_Cfg['sevid']);
    $total = array();
    //每个表
    for ($i = 0; $i < 99; $i++) {
        if($i < 10){
            $table1 = 'flow_event_0'.$i;
//            $table2 = 'flow_records_0'.$i;
        }else{
            $table1 = 'flow_event_'.$i;
//            $table2 = 'flow_records_'.$i;
        }
//        $sql = "SELECT a.uid,b.* FROM {$table1} a JOIN {$table2} b ON a.id = b.flowid WHERE a.ftime > 1520870400 AND a.ftime < 1520906400 AND b.type = 1 AND b.itemid = 2 AND b.cha<0";
        $sql = "select uid,model FROM {$table1} where ftime = 1505446860 and ctrl = 'setUinfo'";
        $data = $db->fetchArray($sql);
        //匹配到数据就填充到$total中
        if(!empty($data)){
            $total = array_merge($total,$sign);
            $total = array_merge($total,$data);
        }

    }

}
//没有找到数据就输出无
if (!empty($total)){
    var_dump($total);
} else {
    echo '没有匹配的数据啊';
}
