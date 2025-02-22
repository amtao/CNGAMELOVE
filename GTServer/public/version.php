<?php
require_once dirname( __FILE__ ) . '/common.inc.php';

$SevidCfg = Common::getSevidCfg(1);
//$cdn = 'https://zjfhkorea-test-1251697691.cos.ap-shanghai.myqcloud.com';//发布服
//$cdn = 'https://gtmzkorea-cdn.xianyuyouxi.com';//正式服
//切换cdn
//if(Common::istestuser(false))
//{
    $cdn = 'https://d4bv3hvy923rl.cloudfront.net';//aws cdn
//}

$is_constraint = 0;
$constraint_path = "";
//$serverlist_url = "https://kortest-gtmz.meogames.com/serverlist.php";//发布服
//$serverlist_url = "https://gs-gtmz.meogames.com/serverlist.php";//正式服
$serverlist_url ="";


$cacheKey = "memcache_key_version";
$cache = Common::getDftMem ();
$versionInfo = $cache->get($cacheKey);
if (empty($versionInfo)){
    $db = Common::getDftDb();
    $sql = "select `value` from `vo_common` where `key`='version'";
    $versionRes = $db->fetchRow($sql);
    $versionInfo = json_decode($versionRes["value"], true);

    if ($versionInfo) {
        $cache->set($cacheKey, $versionInfo, 300);
    }
}

$version = $versionInfo["all"]; //'1.0.0.6';
//测试服
if(Common::istestuser(false))
{
    $version = $versionInfo["white"];
}

$cacheVersionKey = "memcache_key_version_list";
$versionList = $cache->get($cacheVersionKey);

if (empty($versionList)){
    $versionList = array();
    $db = Common::getDftDb();
    $sql = "SELECT * FROM `version_management` WHERE `id` > 0";
    $rt = $db->query($sql);
    while($row = mysql_fetch_assoc($rt)){
        $versionList[]=$row;
    }

    if (!empty($versionList)){
        $cache->set($cacheVersionKey, $versionList, 3600);
    }
}

if (!empty($versionList)){

    foreach ($versionList as $key => $value) {
        if ($value["channel_id"] == $_GET["channel_id"] && $value["base_ver"] == $_GET["base_ver"]) {
        
            //审核加密
            if(!empty($value["is_ts"]) && $value["is_ts"] =="1" ){
                echo '{}';
                exit();   
            }
            if(!empty($value["all_version"]))
            {
                $version = $value["all_version"];
            }
            if(!empty($value["cdn_path"]))
            {
                $cdn = $value["cdn_path"];
            }
            if(!empty($value["is_constraint"]))
            {
                $is_constraint = $value["is_constraint"];
            }
            if(!empty($value["constraint_path"]))
            {
                $constraint_path = $value["constraint_path"];
            }
            if(Common::istestuser(false))
            {
                if(!empty($value["white_version"]))
                {
                    $version = $value["white_version"];
                }
            }
            if(!empty($value["server_list_url"]))
            {
                $serverlist_url = $value["server_list_url"];
            }
            
            break;
        }
    }
}
//临时提审服切换
/*if($_GET["channel_id"] ==3 &&$_GET["base_ver"]==1)
{
    $serverlist_url = "https://gtk-gs.meogames.com/serverlist.php";//审核服
}*/

$data = array(
    'gt_kt' => array(
        'update' => true,
        'remoteVersion' => $version,
        'target_version_code' => $version,
        //'download_url' => $cdn,
        'hotUpdateUrl' => $cdn.'/'. $version .'/',
        'manifestUrl' =>  $cdn.'/'. $version .'/project.manifest',
        'is_constraint' =>  $is_constraint,
        //'constraint_path' =>  $constraint_path
        //'serverList' =>  $serverlist_url
    ),
);
if(!empty($serverlist_url))
{
    $data['gt_kt']['serverList'] = $serverlist_url;
}
if(!empty($constraint_path))
{
    $data['gt_kt']['download_url'] = $constraint_path;
}

if(defined('SP_DECODE') && SP_DECODE){
    $data['gt_kt']['CRYPTOJSKEY'] = SP_DECODE;
}
$data['gt_kt']['servername'] = "1";
//审核特殊返回空json
echo json_encode($data,true);
//$data_rv = array();
//echo json_encode($data_rv,true);
//echo '{}';
