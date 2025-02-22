<?php
//统计

require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//$uid = intval($_SERVER['argv'][1]);// $uid
$startTime = strtotime('2017-9-1 00:00:00');// $startTime
$endTime = Game::get_now();  //$endTime



Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$flowDb = Common::getDbBySevId($id, 'flow');

$sql = "SElECT * FROM `pandect` WHERE `time`>=".$startTime." AND `time`<=".$endTime."AND `platform` = 'anfenggjyp'  ORDER BY `time` ASC;";
$result = $flowDb->fetchArray($sql);

$data = array();
if (!empty($result)){
    foreach ($result as $key => $value){
        $time = date('Y-m', $value['time']);
        $data[$time]['register'] += $value['register'];
        $data[$time]['income'] += $value['income'];
        $data[$time]['pay_man'] += $value['pay_man'];
        $data[$time]['pay_count'] += $value['pay_count'];
        $data[$time]['new_pay'] += $value['new_pay'];
        $data[$time]['new_income'] += $value['new_income'];
    }
}


$sql = "SELECT * FROM `remain` WHERE UNIX_TIMESTAMP(`date`)>={$startTime} AND UNIX_TIMESTAMP(`date`)<={$endTime}";
$result = $flowDb->fetchArray($sql);
if (!empty($result)){
    foreach ($result as $k =>$v){
        $v['date'] = date('Y-m', strtotime($v['date']));
        if ($v['login']){
            $v['login'] = json_decode($v['login'], true);
            if (!empty($v['login'])) {
                foreach ($v['login'] as $lk => $lv) {
                    if ($lv['platform'] == 'anfenggjyp') {
                        $data[$v['date']]['login'] += $lv['login'];
                    }

                }
            }
        }
    }
}

foreach ($data as $k => $d) {
    if (!empty($d['pay_man'])){
        $data[$k]['ARPPU'] = number_format($d['income']/$d['pay_man'], 2);
    }else{
        $data[$k]['ARPPU'] = 0;
    }
    if (!empty($d['login'])){
        $data[$k]['fufeilv'] = number_format($d['pay_man']*100/$d['login'], 2);
    }else{
        $data[$k]['fufeilv'] = 0;
    }
    if (!empty($d['register'])){
        $data[$k]['xingfufeilv'] = number_format($d['new_pay']*100/$d['register'], 2);
    }else{
        $data[$k]['xingfufeilv'] = 0;
    }

}

foreach ($data as $k => $v) {
    echo $k.','.$v['register'].','.$v['login'].','.$v['income'].','.$v['pay_man'].','.$v['pay_count'].','.$v['fufeilv'].','.$v['new_pay'].','.$v['new_income'].','.$v['xingfufeilv'].','.$v['ARPPU'];
}



//if (!empty($data['login'])){
//    $data['ARPU'] = number_format($data['income']/$data['login'], 2);
//}else{
//    $data['ARPU'] = 0;
//}


//$data['consume']= 0;
//
//$where = " WHERE `type`=1 AND `time`>".$startTime.' AND `time`<'.$endTime;
//
//$sql = "SELECT `num` FROM `flow_consume` ".$where;
//$serverList = ServerModel::getServList();
//foreach ($serverList as $k => $v) {
//    if ( empty($v) ) {
//        continue;
//    }
//    $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
//    if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
//        continue;
//    }
//    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
//        continue;
//    }
//    $db = Common::getMyDb('flow');
//    $data1 = $db->fetchArray($sql);
//
//    foreach ($data1 as $k => $v) {
//
//        $data['consume'] += $v['num'];
//    }
//
//}

//var_export($data);


//$sevid = Game::get_sevid($uid);
//$SevidCfg = Common::getSevidCfg($sevid);
//
//$tableE = 'flow_event_'.Common::computeTableId($uid);
//$tableR = 'flow_records_'.Common::computeTableId($uid);
//
//
//
//$db = Common::getMyDb('flow');
//$sql = "SELECT cha FROM {$tableE} as f1 JOIN {$tableR} as f2 WHERE f1.uid = {$uid} AND f1.id = f2.flowid AND f2.type=1;";
//$data1 = $db->fetchArray($sql);
//
//$data = array();
//$data['uid'] = $uid;
//$data['add'] = 0;
//$data['sub'] = 0;
//foreach ($data1 as $k => $v) {
//    if($v['cha'] > 0) {
//        $data['add'] += $v['cha'];
//    }else{
//        $data['sub'] += $v['cha'];
//    }
//
//}
//
//$data['consume'] = number_format(abs($data['sub'])/$data['add'], 2);
//var_export($data);