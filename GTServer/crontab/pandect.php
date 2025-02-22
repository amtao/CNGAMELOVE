<?php
//数据统计
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
set_time_limit(0);
Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$serverList = ServerModel::getServList();
//注册
$startTime = strtotime(date('Y-m-d', strtotime("-1 day")));
$endTime = strtotime(date('Y-m-d'));
$data = array();
$list = array();
//注册人数统计
$sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`data` AS d from `register` where `reg_time`<{$endTime} and `reg_time`>={$startTime}";
$db = Common::getDbBySevId($id);
$register = $db->fetchArray($sql);
if (!empty($register)) {
    foreach ($register as $val) {
        $regtime = date('Ymd', $val['r']);
        $platform = $val['p'];
        $data[$regtime][$platform]['register'] += 1;
        $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
    }
    //用完释放
    unset($register);
}

foreach ($serverList as $k => $v) {
    if (empty($v)) {
        continue;
    }
    $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
    if (999 == $SevidCfg1['sevid']) {
        continue;
    }
    if (0 < $serverID && $serverID != $SevidCfg1['sevid']) {
        continue;
    }
    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
        continue;
    }
    $db = Common::getDbBySevId($SevidCfg1['sevid']);
    $sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`platform` AS p from `t_order` where `status`>0 and `ptime`<{$endTime} and `ptime`>={$startTime};";
    $order_list = $db->fetchArray($sql);
    if (!empty($order_list)) {
        $list = array_merge($list, $order_list);
    }
    unset($order_list);
}

if (!empty($list)) {
    $ls_total_arr = array();//临时总存放
    foreach ($list as $value) {
        $ptime = date('Ymd', $value['pt']);
        $platform = $value['p'];
        if (empty($ls_total_arr[$ptime])) {
            $ls_total_arr[$ptime] = array();
        }
        if (!isset($ls_total_arr[$ptime][$value['o']])) {
            $data[$ptime][$platform]['pay_man'] += 1;//总充值人数
            $ls_total_arr[$ptime][$value['o']] = $value['o'];
        }
        $data[$ptime][$platform]['pay_count'] += 1;//付费笔数
        $data[$ptime][$platform]['income'] += $value['m'];//总金额

        //新增统计
        if (isset($resultLog[$ptime][$platform]['openid'][$value['o']])) {
            if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1) {
                $data[$ptime][$platform]['new_pay'] += 1;
                $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
            }
            $data[$ptime][$platform]['new_income'] += $value['m'];
        }
    }
    if (!empty($data)){
        ksort($data);
    }
}
$flowDb = Common::getDbBySevId($id, 'flow');
if($data){
    foreach ($data as $dk => $dv){
        foreach ($dv as $k => $v){
            $time = strtotime($dk);
            $register = $v['register']?$v['register']:0;
            $income   = $v['income']?$v['income']:0;
            $pay_man  = $v['pay_man']?$v['pay_man']:0;
            $pay_count = $v['pay_count']?$v['pay_count']:0;
            $new_pay  = $v['new_pay']?$v['new_pay']:0;
            $new_income  = $v['new_income']?$v['new_income']:0;

            $sql = 'SELECT * FROM `pandect` WHERE `time`='.$time.' AND `platform`="'.$k.'"';
            $result = $flowDb->fetchArray($sql);
            if (empty($result)){
                $sql = "INSERT INTO `pandect` (`platform`, `register`, `income`, `pay_man`, `pay_count`, `new_pay`, `new_income`, `time`) VALUES ('{$k}', {$register}, {$income}, {$pay_man}, {$pay_count}, {$new_pay}, {$new_income}, {$time});";
                $results = $flowDb->query($sql);
                if (!$results){
                    echo $sql.'<br/>';
                }else{
                    echo '插入成功！';
                }
            }else{
                $sql = "UPDATE  `pandect` SET  `register`={$register}, `income`={$income}, `pay_man`={$pay_man}, `pay_count`={$pay_count}, `new_pay`={$new_pay}, `new_income`={$new_income} WHERE `time`={$time} AND `platform`='{$k}';";
                $results = $flowDb->query($sql);
                if (!$results){
                    echo $sql.'<br/>';
                }else{
                    echo '更新成功！';
                }
            }
            unset($result, $results);
        }
    }
}
?>