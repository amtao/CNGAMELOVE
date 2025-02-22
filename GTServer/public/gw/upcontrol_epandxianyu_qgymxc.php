<?php
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求  
$pf = $_REQUEST['pf'];
$pfv = $_REQUEST['pfv'];
$version = $_REQUEST['version'];
$iosVersion = "1.1.6.31";
//$iosVersion = "1.1.3.34";
$targetVersion = "1.1.6.31";

require_once 'lib.php';
//验证IP地址
$ip = real_ip();
$iparr = array(
    "27.154.231.94",
    "202.104.136.208",


    "117.136.79.167",
    "47.52.33.204",
    "183.14.132.107",
    "207.146.26.68",
    "183.14.29.90",
    "207.148.26.68",
    "119.137.53.131",
    "119.137.53.219",
    "202.104.136.46",
    "218.17.161.138",
    );

$updateURLTrunk = 'http://game-ep.xianyuyouxi.com/update_trunk/';
$updateURLTest = 'http://game-ep.xianyuyouxi.com/update/';
$updateURL = 'http://game-epzjfhover-cdn.xianyuyouxi.com/update/';
$updateURL_QGYM = 'http://game-epgtmz-cdn.xianyuyouxi.com/update_andgtmz/';

//"serverList":"http://game-epzjfh.id-g.com/serverlist.php",

if (!in_array($ip, $iparr)) {
    //echo '{"update":"false", "recharge_98":false, "enableMonth": true, "enableSuper" : true}';
    //return ;
    $exiparr = array(
        //"223.104.6.3",
    );


    //if (!in_array($ip, $exiparr)) {  echo 'error';exit(); }

    if($pf == "epandxianyu_qgymxc") {     // 咸鱼买量（国内） 清宫一梦
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "serverList":"http://game-epzjfh.xianyuyouxi.com/serverlist.php", "isShowShare":true, "lang":"zh-ch", "isRSN":true,
            "manifestUrl":"'.$updateURL_QGYM.$iosVersion.'/project.manifest", "questUrl":"", "isNewServerList":true, "target_version_code":1, "download_url":"http://gtmz.xinghegame.com/m/",  
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png", "addShowCreateHeadId":[],
            "hotUpdateUrl":"'.$updateURL_QGYM.'"}';
        return ;
    }
}
else {
    if($pf == "epandxianyu_qgymxc") {    // 咸鱼买量(国内) 清宫一梦
        //"serverList":"http://game-epzjfh.id-g.com/serverlist.php",
        echo '{"update":"true", "remoteVersion":"'.$targetVersion.'", "questUrl":"", "isShowShare":true, "lang":"zh-ch",
            "target_version_code":1, "download_url":"http://gtmz.xinghegame.com/m/",  "isNewServerList":true, "111enter_game":false, "addShowCreateHeadId":[],
            "manifestUrl":"'.$updateURL_QGYM.$targetVersion.'/project.manifest", "serverList":"http://game-epzjfh.xianyuyouxi.com/serverlist.php",
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png",
            "hotUpdateUrl":"'.$updateURL_QGYM.'"}';
        return ;
    }

}

?>
