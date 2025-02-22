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
    "119.137.52.198",
    "119.137.55.107",
    "202.104.136.46",
    "125.77.130.94",
    "119.137.54.139",
    "121.10.121.82",
    "59.57.250.246",
    "66.249.71.85",
    "119.137.52.214",
    "117.136.39.101",
    "66.249.79.85",
    "66.249.71.85",
    "202.104.136.46",
    "125.77.130.94",
    "148.70.179.125",
    "119.137.55.44",
    "183.56.168.60",
    "183.56.168.30",
    "202.104.136.46",
    "113.96.172.18",
    "119.137.54.218",
    "121.10.121.82",
    "202.104.136.46",
    "113.96.172.18",
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

//$updateURLTest = 'http://game-ep.id-g.com/update/';
$updateURL= 'http://game-epzjfhovergat-cdn.xianyuyouxi.com/update/';

// http://80d741.pathx.ucloudgda.com/serverlist.php

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

    if($pfv == "2.0") {    // 苹果审核包
        $iosVersion = $isUpdateMid?"1.1.3.5.11":$iosVersion;
        echo '{"update":"false", "remoteVersion":"'.$iosVersion.'", "questUrl":"", "isOpenUserCenter":true, "isHideChange":true, "isShowMonthCard":false, "isShowShare":false,
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"http://game-epzjfhovergatpgshf.xianyuyouxi.com/serverlist.php", "addShowCreateHeadId":[],
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }

    if($pf == "epiosxianyuovergat_zjfh") {    // 咸鱼海外包 紫禁繁花
       $iosVersion = $isUpdateMid?"1.1.3.5.11":$iosVersion;
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "questUrl":"", "isOpenUserCenter":true, "isHideChange":true, "isShowMonthCard11":false, "isShowShare111":false,
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"http://game-epzjfhovergat.xianyuyouxi.com/serverlist.php", "isRSN":true,  "addShowCreateHeadId":[],
            "enter_game":false, "target_version_code":9, "download_url":"https://itunes.apple.com/tw/app/id1442053164",
            "isShowMyServer":true, "pfApi": "http://game-epzjfhovergat.xianyuyouxi.com/pfapi/", "accountprefix":"zjfhiosgat_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}
else {
    if($pfv == "1.9") {    // 苹果审核包
        $trunkVersion = $isUpdateMid?"1.1.3.5.11":$trunkVersion;
        echo '{"update":"true", "remoteVersion":"'.$trunkVersion.'", "questUrl":"", "isOpenUserCenter":true, "isHideChange1":true, "isShowMonthCard111":false, "isShowShare111":false, "enter_game":false,"isRSN":true, "addShowCreateHeadId":[],
            "manifestUrl":"'.$updateURL.$trunkVersion.'/project.manifest", "serverList1111":"http://f90f87.pathx.ucloudgda.com/serverlist.php", "target_version_code11111111":3,  "download_url":"https://itunes.apple.com/tw/app/id1442053164",
            "serverList":"http://id-g.com:81/serverlist.php",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }

    if($pf == "epiosxianyuovergat_zjfh") {    // 咸鱼海外包 紫禁繁花
        $trunkVersion = $isUpdateMid?"1.1.3.5.11":$trunkVersion;
        echo '{"update":"true", "remoteVersion":"'.$trunkVersion.'", "questUrl":"", "isOpenUserCenter":true, "isHideChange1":true, "isShowMonthCard111":false, "isShowShare111":false, "enter_game":false, "target_version_code":9, "isRSN":true, "addShowCreateHeadId":[],
            "manifestUrl":"'.$updateURL.$trunkVersion.'/project.manifest", "serverList":"http://game-epzjfhovergat.xianyuyouxi.com/serverlist.php", "target_version_code11111111":3,  "download_url":"https://itunes.apple.com/tw/app/id1442053164",
            "isShowMyServer":true, "pfApi": "http://game-epzjfhovergat.xianyuyouxi.com/pfapi/", "accountprefix":"zjfhiosgat_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}

?>
