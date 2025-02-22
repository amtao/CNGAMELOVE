<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每小时跑一次
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$serverList = include(ROOT_DIR.'/config/server.php');
Common::loadModel('ServerModel');

$serverID = 1;// 默认是全部区

//服务器过滤
$SevidCfg = Common::getSevidCfg($serverID);//子服ID

$allSqls = array();
$table_div = Common::get_table_div();
for ($i = 0 ; $i < $table_div ; $i++)
{
    //用户表
    $table = 'user_'.Common::computeTableId($i);
    $allSqls[] = "SELECT (SUM(`cash_sys`) + SUM(`cash_buy`) - SUM(`cash_use`)) AS allDiamond FROM ".$table;
}

$diamondList = array();
foreach ($serverList as $key => $value) {

    $serverID = intval($value["sevId"]);// 默认是全部区
    if ($value["isOpen"] == 0) {
        continue;   // 未开服
    }

    $crontabName = $serverID."_diamond_log";
    $btime = microtime(true);
    $nowTime = date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']);
    // Game::crontab_debug("当前时间:".date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), $crontabName);

    //服务器过滤
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $sevId = $SevidCfg['sevid'];
    // Game::crontab_debug("服务器ID：:".$SevidCfg['sevid'], $crontabName);
    if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $sevId ) {
        // Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }
    if ( 0 < $serverID && $serverID != $sevId ) {
        // Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }
    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
        && $sevId > PASS_SEV_CRONTAB_MAXID) {
        // Game::crontab_debug(">>>从服跳过", $crontabName);
        continue;
    }

    if($sevId != $SevidCfg['he']){
        // Game::crontab_debug(">>>不是指定合服id跳过", $crontabName);
        continue;
    }

    $open_day = ServerModel::isOpen($serverID);
    //过滤未开服的
    if($open_day <= 0){
        // Game::crontab_debug(">>>open_day：".$open_day, $crontabName);
        continue;
    }

    $db = Common::getDftDb();
    $sevidDiamond = 0;
    foreach ($allSqls as $allSql){
        $rt = $db->query($allSql);
        while($row = mysql_fetch_assoc($rt)){
            $sevidDiamond += $row["allDiamond"];
        }
    }

    $diamondList[$sevId] = $sevidDiamond;
}

$dayTime = $_SERVER['REQUEST_TIME'];
$pIndex = 1;
$valus = "";
$count = count($diamondList);
foreach ($diamondList as $sevId => $diamond) {
    if ($pIndex == $count) {
        $valus .= "({$sevId}, '{$diamond}', {$dayTime})";
    } else {
        $valus .= "({$sevId}, '{$diamond}', {$dayTime}),";
    }
    $pIndex++;
}

$db = Common::getDbBySevId(1);
$sql = "INSERT INTO `user_diamond_day_log` (`sevId`, `diamond`, `dayTime`) VALUES {$valus}";
$db->query($sql);