<?php
//数据统计

require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
Common::loadModel('OrderModel');
Common::loadModel('ServerModel');
$begindt = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-86400);
$enddt = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-1);
$startTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-86400);
$endTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-1);
if (!empty($_REQUEST['startTime']) &&  !empty($_REQUEST['endTime'])){
    $begindt = $_REQUEST['startTime'];
    $startTime = $_REQUEST['startTime'];
    $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['endTime']));
    $endTime =  date('Y-m-d 23:59:59', strtotime($_REQUEST['endTime']));
    if ($_REQUEST['startTime'] == $_REQUEST['endTime'] ){
        $begindt = date('Y-m-d 00:00:00', strtotime($_REQUEST['startTime']));
        $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['startTime']));
        $startTime = date('Y-m-d 00:00:00', strtotime($_REQUEST['startTime']));
        $endTime = date('Y-m-d 23:59:59', strtotime($_REQUEST['startTime']));
    }
    if (strtotime($enddt)-strtotime($begindt)>86400*2){
        $display = 1;
    }
}

$server = include (ROOT_DIR.'/administrator/extend/server.php');
$type = 1;
if ($_REQUEST['sort'] == "按注册人数倒序" || $_REQUEST['sort'] == 1){
    $type = 1;
}elseif ($_REQUEST['sort'] == "按充值倒序" || $_REQUEST['sort'] == 2){
    $type = 2;
}elseif ($_REQUEST['sort'] == "按登录倒序" || $_REQUEST['sort'] == 3){
    $type = 3;
}
Common::loadModel('OrderModel');
$platformList = OrderModel::get_all_platform();
$platformClassify = OrderModel::get_platform_classify();
$data = array();
$totalRegister = 0;
$totalMoney = 0;
$totalLogin = 0;
$defServerId = ServerModel::getDefaultServerId();
$SevidCfg = Common::getSevidCfg($defServerId);
$memcache = Common::getMyMem();
$key = 'dataStatistics_admin';
$info = $memcache->get($key);
if ($info['startTime'] == $startTime && $info['endTime'] == $endTime){
    $datas = $info['data'];
    foreach ($datas as $dk =>$dv){
        if (!empty($_REQUEST['platform']) && $_REQUEST['platform'] != 'all'){
            if ($platformClassify[$dk] != $_REQUEST['platform']){
                continue;
            }
        }
        if ($type == 1) {
            $data[$dk]['register'] += $dv['register'];
            $data[$dk]['total'] += $dv['total'];
            $data[$dk]['totalLogin'] += $dv['totalLogin'];
        }elseif ($type == 2) {
            $data[$dk]['total'] += $dv['total'];
            $data[$dk]['register'] += $dv['register'];
            $data[$dk]['totalLogin'] += $dv['totalLogin'];
        }elseif($type == 3) {
            $data[$dk]['totalLogin'] += $dv['totalLogin'];
            $data[$dk]['total'] += $dv['total'];
            $data[$dk]['register'] += $dv['register'];
        }
        $totalRegister += $dv['register'];
        $totalMoney += $dv['total'];
        $totalLogin += $dv['totalLogin'];
    }
}else{
    foreach ($server as $value){
        $url = $value.'/api/dataStatistics.php?begindt='.$begindt.'&enddt='.$enddt;
        $result = curl_https($url);
        if (!empty($result)){
            $dataInfo = json_decode($result, true);
            if (is_array($dataInfo)){
                foreach ($dataInfo as $dk => $dv){
                    $datas[$dk]['register'] += $dv['register'];
                    $datas[$dk]['total'] += $dv['total'];
                    $datas[$dk]['totalLogin'] += $dv['totalLogin'];
                    if (!empty($_REQUEST['platform']) && $_REQUEST['platform'] != 'all'){
                        if ($platformClassify[$dk] != $_REQUEST['platform']){
                            continue;
                        }
                    }
                    if ($type == 1) {
                        $data[$dk]['register'] += $dv['register'];
                        $data[$dk]['total'] += $dv['total'];
                        $data[$dk]['totalLogin'] += $dv['totalLogin'];
                    }elseif ($type == 2) {
                        $data[$dk]['total'] += $dv['total'];
                        $data[$dk]['register'] += $dv['register'];
                        $data[$dk]['totalLogin'] += $dv['totalLogin'];
                    }elseif($type == 3) {
                        $data[$dk]['totalLogin'] += $dv['totalLogin'];
                        $data[$dk]['total'] += $dv['total'];
                        $data[$dk]['register'] += $dv['register'];
                    }
                    $totalRegister += $dv['register'];
                    $totalMoney += $dv['total'];
                    $totalLogin += $dv['totalLogin'];
                }
            }
        }
    }
    if (date('Y-m-d', strtotime($endTime)) == date('Y-m-d')){
        $memData['endTime'] = date('Y-m-d H:i:s');
    }else{
        $memData['endTime'] = $endTime;
    }
    $memData['startTime'] = $startTime;
    $memData['data'] = $datas;
    $memcache->set($key, $memData);
}
arsort($data);
?>
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
<BR>
    <form name="chat" method="POST" action="">
        <table>
            <tr><th colspan="6">时间选择</th></tr>
            <tr>
                <th style="text-align:right;">选择时间：</th>
                <td style="text-align:left;">
                    <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                           value='<?php echo $startTime;?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                    &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                    <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                           value='<?php echo $endTime;?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
                </td>
            </tr>
            <tr>
                <th style="text-align:right;">平台分类：</th>
                <td style="text-align:left;">
                    <select name="platform">
                        <option value="all" <?php echo $_REQUEST['platform'] == 'all'?'selected="selected"':'';?>>所有</option>
                        <option value="android" <?php echo $_REQUEST['platform'] == 'android'?'selected="selected"':'';?>>安卓</option>
                        <option value="ios" <?php echo $_REQUEST['platform'] == 'ios'?'selected="selected"':'';?>>苹果</option>
                    </select>
                </td>
            </tr>
            <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
        </table>
    </form>
    <BR>
<?php
if($data){
    ?>
    <table>
        <tr>
            <th>平台</th>
            <th>注册人数</th>
            <th>充值</th>
            <?php if ($display != 1):?>
                <th>登录人数</th>
            <?php endif;?>
        </tr>
        <tr>
            <th>排序</th>
            <form name="chat" method="POST" action="">
                <th>
                    <input name="startTime" style="display: none;" value="<?php echo $startTime;?>">
                    <input name="endTime"  style="display: none;" value="<?php echo $endTime;?>">
                    <input name="platform" style="display: none;" value="<?php echo $_REQUEST['platform'];?>">
                    <input name="sort" type="submit" value="按注册人数倒序">
                </th>
            </form>
            <form name="chat" method="POST" action="">
                <th>
                    <input name="startTime" style="display: none;" value="<?php echo $startTime;?>">
                    <input name="endTime"  style="display: none;" value="<?php echo $endTime;?>">
                    <input name="platform" style="display: none;" value="<?php echo $_REQUEST['platform'];?>">
                    <input name="sort" type="submit" value="按充值倒序">
                </th>
            </form>
            <?php if ($display != 1):?>
                <form name="chat" method="POST" action="">
                    <th>
                        <input name="startTime" style="display: none;" value="<?php echo $startTime;?>">
                        <input name="endTime"  style="display: none;" value="<?php echo $endTime;?>">
                        <input name="platform" style="display: none;" value="<?php echo $_REQUEST['platform'];?>">
                        <input name="sort" type="submit" value="按登录倒序">
                    </th>
                </form>
            <?php endif;?>
        </tr>
        <tr style="background-color:#f6f9f3">
            <th>总计</th>
            <th style="text-align: center;"><?php echo $totalRegister; ?></th>
            <th style="text-align: center;"><?php echo $totalMoney; ?></th>
            <?php if ($display != 1):?>
                <th style="text-align: center;"><?php echo $totalLogin; ?></th>
            <?php endif;?>
        </tr>
        <?php foreach($data as $k => $val){?>
            <?php if (!empty($k)):?>
                <tr style="background-color:#f6f9f3">
                    <td style="text-align:center;"><?php echo $platformList[$k]?$platformList[$k]:$k; ?></td>
                    <td style="text-align:center;"><?php echo $val['register']; ?></td>
                    <td style="text-align:center;"><?php echo $val['total']; ?></td>
                    <?php if ($display != 1):?>
                        <td style="text-align:center;"><?php echo $val['totalLogin']; ?></td>
                    <?php endif;?>
                </tr>
            <?php endif;?>
        <?php } ?>
    </table>
<?php  }?>
<?php

function curl_https($url, $data=array(), $header=array(), $timeout=180){
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
$response = curl_exec($ch);
if($error=curl_error($ch)){
die($error);
}
curl_close($ch);
return $response;
}
?>