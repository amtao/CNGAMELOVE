<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$begindt = strtotime(date('Y-m-d'));
$enddt = strtotime(date('Y-m-d 23:59:59'));
if (!empty($_REQUEST['begindt']) &&  !empty($_REQUEST['enddt'])){
    $begindt = $_REQUEST['begindt'];
    $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['enddt']));
}
if ($_REQUEST['begindt'] == $_REQUEST['enddt'] ){
    $begindt = date('Y-m-d 00:00:00', strtotime($_REQUEST['begindt']));
    $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['begindt']));
}
$server = include (ROOT_DIR.'/administrator/extend/server.php');
foreach ($server as $value){
    $url = $value.'/api/allserverList.php?begindt='.$begindt.'&enddt='.$enddt;
    $data = curl_https($url);
    if (!empty($data)&&is_numeric($data)){
        $totalMoney = $totalMoney+$data;
    }
    $totalMoney = $totalMoney + 0;
}
echo $totalMoney;
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