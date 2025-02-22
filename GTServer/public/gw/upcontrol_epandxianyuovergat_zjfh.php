<?php
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求  
$pf = $_REQUEST['pf'];
$pfv = $_REQUEST['pfv'];
$version = $_REQUEST['version'];
$iosVersion = "1.1.6.30";
$trunkVersion = "1.1.6.30";



require_once 'lib.php';
//验证IP地址
$ip = real_ip();
$insideIp = "27.154.231.94";

$iparr = array(
    "27.154.231.94",
    "119.137.55.107",
    "202.104.136.46",
    "119.137.52.208",
    "121.10.121.83",
    "119.137.55.51",
    "119.137.55.52",
    "121.10.121.82",
    "218.17.161.138",
    "58.63.233.30",
    "119.137.52.69",
    "218.17.161.138",
    );

$updateURLTest = 'http://game-ep.id-g.com/update/';
$updateURL= 'http://game-epzjfhovergat-cdn.xianyuyouxi.com/update/';

// http://game-epzjfhovergat.id-g.com/serverlist.php


function isNeedUpdateMid($version){
    //if version less than 1.0.90 need update 1.0.90
    if (empty($version))return true;
    $versionData = explode(".", $version);
    if ($versionData[0] && $versionData[0] != 1)return $versionData[0] < 1;
    if ($versionData[1] && $versionData[1] != 1)return $versionData[1] < 1;
    if ($versionData[2] && $versionData[2] != 3)return $versionData[2] < 3;
    if ($versionData[3] && $versionData[3] != 5)return $versionData[3] < 5;
    if ($versionData[4] && $versionData[4] != 11)return $versionData[4] < 11;
    return false;
}

$isUpdateMid = isNeedUpdateMid($version);

if (!in_array($ip, $iparr)) {
    $iosVersion = $isUpdateMid?"1.1.3.5.11":$iosVersion;
    if($pf == "epandxianyuovergat_zjfh") {    // 咸鱼海外包 紫禁繁花
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "questUrl":"", "isOpenUserCenter":true,"isRSN":true, "addShowCreateHeadId":[], "enter_game":false, "target_version_code":10,
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"http://game-epzjfhovergat.xianyuyouxi.com/serverlist.php",
            "isShowMyServer":true, "pfApi": "http://game-epzjfhovergat.xianyuyouxi.com/pfapi/", "accountprefix":"zjfhandgat_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}
else {
    if($pf == "epandxianyuovergat_zjfh") {    // 咸鱼海外包 紫禁繁花
       // $trunkVersion = $isUpdateMid?"1.1.3.5.11":$trunkVersion;
        echo '{"update":"true", "remoteVersion":"'.$trunkVersion.'", "questUrl":"", "isOpenUserCenter":true,  "target_version_code111111":3,  "download_url":"https://play.google.com/store/apps/details?id=com.xianyugame.zijfh",
            "manifestUrl":"'.$updateURL.$trunkVersion.'/project.manifest", "serverList":"http://game-epzjfhovergat.xianyuyouxi.com/serverlist.php", "enter_game":false, "target_version_code":10,"showLang":true,"isRSN":true, "addShowCreateHeadId":[],
            "isShowMyServer":true, "pfApi": "http://game-epzjfhovergat.xianyuyouxi.com/pfapi/", "accountprefix":"zjfhandgat_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}

?>
