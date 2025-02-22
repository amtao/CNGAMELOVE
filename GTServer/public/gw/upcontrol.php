<?php
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求  
$pf = $_REQUEST['pf'];
$pfv = $_REQUEST['pfv'];
$version = $_REQUEST['version'];
$iosVersion = "1.1.6.31";
//$iosVersion = "1.1.5.105";
$targetVersion = "1.1.6.31";

require_once 'lib.php';
//验证IP地址
$ip = real_ip();
$iparr = array(
    "59.57.250.246",
    "27.154.231.94",
    "119.137.54.218",
    "202.104.136.46",
    "119.137.55.66",
    "218.17.161.138",
    "218.17.161.138",
    "218.17.161.138",
    );

$updateURLTrunk = 'http://game-ep.xianyuyouxi.com/update_trunk/';
$updateURLTest = 'http://game-ep.xianyuyouxi.com/update/';
//$updateURL = 'http://game-epzjfhover-cdn.id-g.com/update/';
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
//
    if($pf == "epandxianyu_qgym") {     // 咸鱼买量（国内） 清宫一梦
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "serverList":"http://game-epzjfh.xianyuyouxi.com/serverlist.php", "isShowShare":false, "lang":"zh-ch", "addShowCreateHeadId":[], "isRSN":true,
            "manifestUrl":"'.$updateURL_QGYM.$iosVersion.'/project.manifest", "questUrl":"", "isNewServerList":true, "download_url":"https://dwz.cn/5OGIxYXr",
            "enter_game":false, "target_version_code":10, "isShowMyServer":true, "pfApi": "http://game-epzjfh.xianyuyouxi.com/pfapi/", "accountprefix":"qgym_",
            "hotUpdateUrl":"'.$updateURL_QGYM.'"}';
        return ;
    }else if($pf == "epandtaptap_zjfh") {     // taptap 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$iosVersion.'", "serverList":"",
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandhykb_zjfh") {     // 好游快爆 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$iosVersion.'", "serverList":"",
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandxianyu_zjfh") {     // 咸鱼游戏 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$iosVersion.'", "serverList":"",
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
    else if($pf == "epiosxianyu_zjfh") {     // 咸鱼IOS游戏 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$iosVersion.'", "serverList":"",
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandquick_zjfh") {   // QUICKSDK 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$iosVersion.'", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
    else if($pf == "epandxianyuover_zjfh") {    // 咸鱼海外包 紫禁繁花
        echo '{"update":"true", "remoteVersion":"'.$iosVersion.'", "questUrl":"", "isOpenUserCenter":true, "freebackUrl":"https://www.facebook.com/%E7%B4%AB%E7%A6%81%E7%B9%81%E8%8A%B1-108527340064937/",
                "manifestUrl":"'.$updateURL.$iosVersion.'/project.manifest", "serverList":"http://game-epzjfhover.xianyuyouxi.com/serverlist.php","isHideChange":true,  "addShowCreateHeadId":[99],
                "download_url":"https://play.google.com/store/apps/details?id=com.xianyugame.zjfh",  "enter_game11":false, "isRSN":true, "enter_game":true, "target_version_code":8, 
                "isShowMyServer":false, "pfApi": "http://game-epzjfhover.xianyuyouxi.com/pfapi/",
                "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }


}
else {
    if($pf == "epandxianyu_qgym") {    // 咸鱼买量(国内) 清宫一梦
        //"serverList":"http://game-epzjfh.id-g.com/serverlist.php",
        echo '{"update":"true", "remoteVersion":"'.$targetVersion.'", "questUrl":"", "lang":"zh-ch",
            "share_meta_url":"http://gt-cdn.zanbugames.com/shareQR/gtmz.png", "addShowCreateHeadId":[],"enter_game":false, "target_version_code":10,
             "download_url":"https://dwz.cn/5OGIxYXr",  "isNewServerList":true, "111enter_game":false,
            "manifestUrl":"'.$updateURL_QGYM.$targetVersion.'/project.manifest", "serverList":"http://game-epzjfh.xianyuyouxi.com/serverlist.php",
            "isShowMyServer":true, "pfApi": "http://game-epzjfh.xianyuyouxi.com/pfapi/", "accountprefix":"qgym_",
            "hotUpdateUrl":"'.$updateURL_QGYM.'"}';
        return ;
    }else if($pf == "epandtaptap_zjfh") {    // taptap 紫禁繁花
        //"serverList":"http://game-epzjfh.id-g.com/serverlist.php",
        echo '{"update":"false", "remoteVersion":"'.$targetVersion.'", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandhykb_zjfh") {    // 好游快爆 紫禁繁花
        //"serverList":"http://game-epzjfh.id-g.com/serverlist.php",
        echo '{"update":"false", "remoteVersion":"'.$targetVersion.'", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandxianyu_zjfh") {    // 咸鱼游戏 紫禁繁花
        //"serverList":"http://game-epzjfh.id-g.com/serverlist.php",
        echo '{"update":"false", "remoteVersion":"'.$targetVersion.'", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }
    else if($pf == "epiosxianyu_zjfh") {    // 咸鱼IOS游戏 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$targetVersion.'", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandquick_zjfh") {   // QUICKSDK 紫禁繁花
        echo '{"update":"false", "remoteVersion":"'.$targetVersion.'", "questUrl":"https://www.wjx.cn/jq/28951989.aspx",
            "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"",
            "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }else if($pf == "epandxianyuover_zjfh") {    // 咸鱼海外包 紫禁繁花
        echo '{"update":"true", "remoteVersion":"'.$targetVersion.'", "questUrl":"", "isOpenUserCenter":true, "freebackUrl":"https://www.facebook.com/%E7%B4%AB%E7%A6%81%E7%B9%81%E8%8A%B1-108527340064937/",
                "manifestUrl":"'.$updateURL.$targetVersion.'/project.manifest", "serverList":"http://game-epzjfhover.xianyuyouxi.com/serverlist.php","isHideChange":true,  "addShowCreateHeadId":[99],
                "target_version_code":1,  "download_url":"https://play.google.com/store/apps/details?id=com.xianyugame.zjfh", "enter_game":true, "target_version_code":8, "isRSN":true, 
                "isShowMyServer":true, "pfApi": "http://game-epzjfhover.xianyuyouxi.com/pfapi/", "accountprefix":"zjfh_",
                "hotUpdateUrl":"'.$updateURL.'"}';
        return ;
    }

}

?>
