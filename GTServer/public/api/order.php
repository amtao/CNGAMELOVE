
<style type="text/css">
    *{ font-size:12px;}
    .header a{ margin-right:13px; border:#bda2a2 1px solid;  line-height:25px; padding:1px 6px 1px 6px;}
    a{ margin-right:13px; border:#bda2a2 1px solid; border-radius:0.2em; line-height:25px; padding:1px 6px 1px 6px;}
    a{text-decoration:none;}
    a:link {color: black;}
    a:visited {color: black;}
    a:hover {color:#F00;}
    a:active {color: #F00}
    .hr {color:#f9f0f0;margin: 10px 0px;}
    table{width:70%;border-collapse:collapse; line-height:20px;}
    table caption {background-color:#c6e4fe; border:#A49898 solid 0.5px;border-bottom: none; font-size: 16px;}
    table th {background-color:#c6e4fe; border:#DAB6A3 solid 1px;}
    table td {border:#DAB6A3 solid 1px; padding-left:5px;}

    .input {margin:1px 3px;}
    .backGroundColor{background-color: #f5edea;}
    .page a{width:20px; line-height:20px; display:block; float:left; background:#B5B5FF; margin:2px; padding-left:10px;}
    .page b{width:20px; line-height:20px; display:block; float:left;  margin:2px;padding-left:10px;background:#CCC}
    .mytable{margin-top:5px;margin-bottom:5px;}
    .mytable a{border:#CCC 1px dotted; padding:2px 4px; margin-right:5px;}
</style>
<table>
    <tr>
        <th>订单号</th>
        <th>平台订单号</th>
        <th>角色ID</th>
        <th>充值RMB(元)	</th>
        <th>充值平台</th>
        <th>充值来源</th>
        <th>日期</th>
    </tr>

<?php
//数据统计
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
set_time_limit(0);
Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$serverList = ServerModel::getServList();
if (!empty($_REQUEST['startTime']) &&  !empty($_REQUEST['endTime'])){
    $startTime = strtotime($_REQUEST['startTime']);
    $endTime = strtotime($_REQUEST['endTime']);
}else{
    //注册
    $startTime = strtotime(date("Y-m"));
    $endTime = strtotime(date("Y-m-d"));
}

$data = array();
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
    $sql = "select `roleid` AS rd,`money` AS m,`ptime` AS pt,`platform` AS p,`orderid` AS od,`tradeno` AS t,`paytype` AS ppt from `t_order` where `status`>0 and `ptime`<{$endTime} and `ptime`>={$startTime} and `platform` IN ('qitiangjypios','qitiangtxjios','qitianjpdgrios','qitianlwdgios','qitiantctyios','qitianypdgr','qitianypdgrios','qitianypdgrios1','qitianjsmr');";
    $order_list = $db->fetchArray($sql);
    if (!empty($order_list)){
        foreach($order_list as $key => $value){
            echo '<tr style="background-color:#f6f9f3">';
            echo '<td style="text-align:center;">'.$value['od'].'</td>';
            echo '<td style="text-align:center;">'.$value['t'].'</td>';
            echo '<td style="text-align:center;">'.$value['rd'].'</td>';
            echo '<td style="text-align:center;">'.$value['m'].'</td>';
            echo '<td style="text-align:center;">'.$value['p'].'</td>';
            echo '<td style="text-align:center;">'.$value['ppt'].'</td>';
            echo '<td style="text-align:center;">'.date('Y-m-d H:i:s', $value['pt']).'</td>';
            echo '</tr>';
        }
    }
    unset($order_list);
}
?>
</table>