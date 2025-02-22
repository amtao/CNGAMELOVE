<?php
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求  
$pf = $_REQUEST['pf'];
$pfv = $_REQUEST['pfv'];
$version = $_REQUEST['version'];
$iosVersion = "1.1.6.30";
$trunkVersion =  "1.1.6.30";




require_once 'lib.php';
//验证IP地址
$ip = real_ip();
$insideIp = "27.154.231.94";

$iparr = array(
    "27.154.231.94",
    "119.137.55.107",
    "202.104.136.46",
    "125.77.130.94",
    "119.137.54.139",
    "119.137.54.139",
    "121.10.121.82",
    "66.249.71.85",
    "119.137.52.214",
    "117.136.39.101",
    "66.249.79.85",
    "66.249.71.85",
    "202.104.136.46",
    "125.77.130.94",
    "148.70.179.125",
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

$updateURLTest = 'http://game-ep.id-g.com/update/';
$updateURL= 'http://game-epzjfhovergat-cdn.xianyuyouxi.com/update/';

// http://game-epzjfhovergat.id-g.com/serverlist.php


if (!in_array($ip, $iparr)) {
    if($pf == "epmycxianyuovergat_zjfh") {
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "questUrl":"", "isOpenUserCenter":true,"isRSN":true,  "addShowCreateHeadId":[],
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"http://game-epzjfhovergat.xianyuyouxi.com/serverlist.php",
             "target_version_code":8,  "enter_game":false, "download_url":"http://game-epzjfhovergat-cdn.xianyuyouxi.com/package/zjfh_zjfh_1.1.5.91_mycard_xianyu_default_1_1906102150_1.0.apk",
             "isShowMyServer":true, "pfApi": "http://game-epzjfhovergat.xianyuyouxi.com/pfapi/", "accountprefix":"zjfhandgat_",

            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}
else {
    if($pf == "epmycxianyuovergat_zjfh") {
        echo '{"update":"true", "remoteVersion":"'.$trunkVersion.'", "questUrl":"", "isOpenUserCenter":true,  "target_version_code":8,  "download_url":"http://game-epzjfhovergat-cdn.xianyuyouxi.com/package/zjfh_zjfh_1.1.5.91_mycard_xianyu_default_1_1906102150_1.0.apk",
            "manifestUrl":"'.$updateURL.$trunkVersion.'/project.manifest", "serverList":"http://game-epzjfhovergat.xianyuyouxi.com/serverlist.php", "enter_game":false, "showLang":true,"isRSN":true,  "addShowCreateHeadId":[],
            "isShowMyServer":true, "pfApi": "http://game-epzjfhovergat.xianyuyouxi.com/pfapi/", "accountprefix":"zjfhandgat_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}

?>
