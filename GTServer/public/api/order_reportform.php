<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$begindt = strtotime(date('Y-m-d'));
$enddt = strtotime(date('Y-m-d'))+86400;
if (!empty($_REQUEST['begindt']) &&  !empty($_REQUEST['enddt'])){
    $begindt = $_REQUEST['begindt'];
    $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['enddt']));
}
if ($_REQUEST['begindt'] == $_REQUEST['enddt'] ){
    $begindt = date('Y-m-d 00:00:00', strtotime($_REQUEST['begindt']));
    $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['begindt']));
}
$classifyServer = include (ROOT_DIR.'/administrator/extend/classifyServer.php');
$server = $classifyServer[AGENT_CHANNEL_ALIAS];
Common::loadModel('OrderModel');
$platformList = OrderModel::get_all_platform();
$data = array();
foreach ($server as $value){
    $url = $value.'/api/order_reportform_info.php?begindt='.$begindt.'&enddt='.$enddt;
    $result = curl_https($url);
    if (!empty($result)){
        $dataInfo = json_decode($result, true);
        foreach ($dataInfo as $dk => $dv){
            foreach($dv as $k => $v){
                $data[$dk][$k]['total'] += $v['total'];
                $data[$dk][$k]['zc'] += $v['zc'];
            }
        }
    }
}
echo json_encode($data);
function curl_https($url, $data=array(), $header=array(), $timeout=30){
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