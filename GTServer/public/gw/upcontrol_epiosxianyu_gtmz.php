<?php
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求  
$pf = $_REQUEST['pf'];
$pfv = $_REQUEST['pfv'];
$version = $_REQUEST['version'];
$iosVersion =  "1.1.6.31";
//$iosVersion = "1.1.5.84";
$targetVersion = "1.1.6.31";
$WhiteListTargetVersion = "1.1.5.50";

require_once 'lib.php';
//验证IP地址
$ip = real_ip();
$insideIp = "27.154.231.94";

$iparr = array(
//    "59.57.250.246",
//    "119.137.54.22",
//    "119.137.53.19",
//    "202.104.136.46",
//    "119.137.52.153",
//    "202.104.136.46",
//    "119.137.52.210",
//    "119.137.55.227",
//    "119.137.53.54",
//      "207.148.26.68",
//      "119.137.55.18",
//      "119.137.53.92",
//      "59.57.250.246",
//      "119.137.52.198",
    "119.137.52.198",
    "59.57.250.246",
    "119.137.54.206",
    "119.137.53.130",
    "119.137.53.207",
    "202.104.136.46",
    "202.104.136.46",
    "202.104.136.46",
    "119.137.52.238",
    "119.137.54.218",
    "202.104.136.46",
    "218.17.161.138",
    "218.17.161.138",
    "119.137.52.69",
    "119.137.55.201",
    );

$updateURLTest = 'http://game-epgtmz-cdn.xianyuyouxi.com/update/';
$updateURL= 'http://game-epgtmz-cdn.xianyuyouxi.com/update/';
$updateURLWhiteList= 'http://game-epgtmz-cdn.xianyuyouxi.com/update_ios/';


if (!in_array($ip, $iparr)) {

    if($pfv == "2.2") {    // 苹果审核包
        echo '{"update":"false", "remoteVersion":"'.$targetVersion.'", "questUrl":"", "isOpenUserCenter":false, "isShowMonthCard":false, "isShowShare":false, "addShowCreateHeadId":[], 
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"http://14817b.pathx.ucloudgda.com/serverlist.php", "isNewServerList":true, "enter_game":false,
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png", "111target_version_code":3,  "download_url":"https://itunes.apple.com/cn/app/id1443993842","isAutoLogin":false,
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }

    if($pf == "epiosxianyu_gtmz") {    // 咸鱼海外包 紫禁繁花
        //
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "questUrl":"", "isOpenUserCenter":false, "isShowMonthCard1111":false, "isShowShare11111":false, "isRSN":true,
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"http://game-epgtmz.xianyuyouxi.com/serverlist.php", "isNewServerList":true,  "enter_game":false, "addShowCreateHeadId":[],
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png", "target_version_code":10,  "download_url":"https://itunes.apple.com/cn/app/id1443993842", "showLang":true,
            "isShowMyServer":true, "pfApi": "http://game-epgtmz.xianyuyouxi.com/pfapi/", "accountprefix":"GTMZ_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}
else {

    if($pfv == "2.2") {    // 苹果审核包
        echo '{"update":"true", "remoteVersion":"'.$targetVersion.'", "questUrl":"", "isOpenUserCenter":false, "isShowMonthCard111":false, "isShowShare111":false, "addShowCreateHeadId":[],
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList11":"http://game-epgtmz.id-g.com/serverlist.php", "isNewServerList":true, "enter_game":false,
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png", "111target_version_code":3,  "download_url":"https://itunes.apple.com/cn/app/id1443993842",
            "serverList":"http://id-g.com:81/serverlist.php",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }

    if($pf == "epiosxianyu_gtmz") {    // 咸鱼海外包 紫禁繁花
        //
        echo '{"update":"true", "remoteVersion":"'.$targetVersion.'", "questUrl":"", "isOpenUserCenter":false, "isShowMonthCard111":false, "isShowShare111":false, 
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"http://game-epgtmz.xianyuyouxi.com/serverlist.php", "isNewServerList":true, "enter_game":false, "addShowCreateHeadId":[],
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png", "target_version_code":10,  "download_url":"https://itunes.apple.com/cn/app/id1443993842",
            "isShowMyServer":true, "pfApi": "http://game-epgtmz.xianyuyouxi.com/pfapi/", "accountprefix":"GTMZ_",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
}

?>
