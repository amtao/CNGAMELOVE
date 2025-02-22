<?php
header("Content-type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求  
$pf = $_REQUEST['pf'];
$pfv = $_REQUEST['pfv'];
$version = $_REQUEST['version'];
$iosVersion = "1.1.6.31";
//$iosVersion = "1.1.3.34";
$targetVersion = "1.1.6.31";
$baseVersionConfig = array();

require_once 'lib.php';
//验证IP地址
$ip = real_ip();
$iparr = array(
    "27.154.231.94",
    "119.137.53.130",
    "202.104.136.46",
    "58.63.233.34",
    "183.2.215.23",
    "113.96.172.19",
    "113.96.172.18",
    "202.104.136.46",
    "202.104.136.46",
    "119.137.53.127",
    "119.137.54.218",
    "202.104.136.46",
    "218.17.161.138",
    "218.17.161.138",
);



$updateURL = 'http://game-epgtmz-cdn.xianyuyouxi.com/update_gtmzcha/';

$baseVersionConfig['update'] = 'true';
$baseVersionConfig['remoteVersion'] = "{$iosVersion}";
$baseVersionConfig['questurl'] = "";
$baseVersionConfig['isAutoLogin'] = false;
$baseVersionConfig['isShowShare'] = false;
$baseVersionConfig['lang'] = 'zh-ch';
$baseVersionConfig['isNewServerList'] = true;
$baseVersionConfig['target_version_code'] = 1;
$baseVersionConfig['download_url'] = 'http://gtmz.xinghegame.com/m/';
$baseVersionConfig['share_meta_url'] = 'http://gt-cdn.zanbugames.com/shareQR/gtmz.png';
$baseVersionConfig['serverList'] = "http://game-epgtmz-ch.xianyuyouxi.com/serverlist.php";
$baseVersionConfig['hotUpdateUrl'] = "{$updateURL}";

$baseVersionConfig['isShowMyServer'] = true;
$baseVersionConfig['pfApi'] = 'http://game-epgtmz-ch.xianyuyouxi.com/pfapi/';
$baseVersionConfig['accountprefix'] = 'epgtmzch_';



if (!in_array($ip, $iparr)) {
    $exiparr = array(
        //"223.104.6.3",
    );

    //if (!in_array($ip, $exiparr)) {  echo 'error';exit(); }


    $baseVersionConfig['manifestUrl'] = "{$updateURL}{$iosVersion}/project.manifest";
   // $baseVersionConfig['serverList'] = 'http://id-g.com:81/serverlist.php';

    switch ($pf)
    {
        case "epandqktest_gtmz":break;         // 母包测试
        case "epandqkaligames_gtmz":break;     // 九游
        case "epandqkxiaomi_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://game.xiaomi.com/wap/index.php?c=app&v=download&package=com.xianyugame.zjfh.mi";
            break;       // 小米
        case "epandqkyyb_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://imtt.dd.qq.com/16891/apk/1252E3670D84FB84327660E64158C759.apk?fsname=com%2Etencent%2Etmgp%2Ezjfh%5F22%2E02%5F2202.apk&csr=97c2";
            break;          // 应用宝
        case "epandqkbaidu_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://p.gdown.baidu.com/f05398cd0f640e5b53b3afe29efd59d82093102d7e6c0d3eb5e70383cdd8148cd64412fea1e2750440b58da9ff99d8303e5d77b361222568abe8227fd25238f03107043e883c8002ddc858799d7586dbffb415acc41f25d38835d0fe09a60b56d9596a9ec38ebdcfb8420f96f1a4d67c";
            break;        // 百度
        case "epandqkqihoo_gtmz":break;        // 奇虎
        case "epandqkhuawei_gtmz":break;       // 华为
        case "epandqkoppo_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://gt-cdn.zanbugames.com/channel/oppo/gtmz-oppo-20190712.apk";
            break;         // oppo
        case "epandqkvivo_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://appstore.vivo.com.cn/appinfo/downloadApkFile?id=2436595";
            break;         // vivo
        case "epandqklenovo_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://www.lenovomm.com/appstore/psl/com.xingyugame.gt.lenovo";
            break;       // lenovo
        case "epandqkmeizu_gtmz":break;        // 魅族
        case "epandqkjl_gtmz":break;           // 金立
        case "epandqkcoolpad_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://appstorecos.coolyun.com/group5/dev_upload/3e/3eeacbfa3716b761f093b967bf249b08.apk";
            break;      // 酷派
        case "epandqksamsung_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://apps.samsung.com/appquery/appDetail.as?appId=com.xingyugame.gt.samsung";
            break;      // 三星
        case "epandqkbilibili_gtmz":break;    // 哔哩哔哩动画
        default:unset($baseVersionConfig);return; break;
    }

    echo str_replace("\\/", "/", json_encode($baseVersionConfig));
}
else {

    $baseVersionConfig['manifestUrl'] = "{$updateURL}{$targetVersion}/project.manifest";
    //$baseVersionConfig['serverList'] = 'http://id-g.com:81/serverlist.php';
    $baseVersionConfig['remoteVersion'] = "{$targetVersion}";



    switch ($pf)
    {
        case "epandqktest_gtmz":break;         // 母包测试
        case "epandqkaligames_gtmz":break;     // 九游
        case "epandqkxiaomi_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://game.xiaomi.com/wap/index.php?c=app&v=download&package=com.xianyugame.zjfh.mi";
            break;       // 小米
        case "epandqkyyb_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://imtt.dd.qq.com/16891/apk/1252E3670D84FB84327660E64158C759.apk?fsname=com%2Etencent%2Etmgp%2Ezjfh%5F22%2E02%5F2202.apk&csr=97c2";
            break;          // 应用宝
        case "epandqkbaidu_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://p.gdown.baidu.com/f05398cd0f640e5b53b3afe29efd59d82093102d7e6c0d3eb5e70383cdd8148cd64412fea1e2750440b58da9ff99d8303e5d77b361222568abe8227fd25238f03107043e883c8002ddc858799d7586dbffb415acc41f25d38835d0fe09a60b56d9596a9ec38ebdcfb8420f96f1a4d67c";
            break;        // 百度
        case "epandqkqihoo_gtmz":break;        // 奇虎
        case "epandqkhuawei_gtmz":break;       // 华为
        case "epandqkoppo_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://gt-cdn.zanbugames.com/channel/oppo/gtmz-oppo-20190712.apk";
            break;         // oppo
        case "epandqkvivo_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://appstore.vivo.com.cn/appinfo/downloadApkFile?id=2436595";
            break;         // vivo
        case "epandqklenovo_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://www.lenovomm.com/appstore/psl/com.xingyugame.gt.lenovo";
            break;       // lenovo
        case "epandqkmeizu_gtmz":break;        // 魅族
        case "epandqkjl_gtmz":break;           // 金立
        case "epandqkcoolpad_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://appstorecos.coolyun.com/group5/dev_upload/3e/3eeacbfa3716b761f093b967bf249b08.apk";
            break;      // 酷派
        case "epandqksamsung_gtmz":
            $baseVersionConfig['target_version_code'] = 10;
            $baseVersionConfig['enter_game'] = false;
            $baseVersionConfig['download_url'] = "http://apps.samsung.com/appquery/appDetail.as?appId=com.xingyugame.gt.samsung";
            break;      // 三星
        case "epandqkbilibili_gtmz":break;    // 哔哩哔哩动画
        default:unset($baseVersionConfig);return; break;
    }

    //echo json_encode($baseVersionConfig);
    echo str_replace("\\/", "/", json_encode($baseVersionConfig));
}


?>