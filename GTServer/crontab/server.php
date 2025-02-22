<?php
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$config = require_once dirname( __FILE__ ) . '/../config/'.GAME_MARK.'/AllServerDbConfig.php';
$serverList = ServerModel::getServList();

$serverId = 1;
$time = time();
$serverName = '';
$url = '';
if ($serverList) {
    foreach ($serverList as $server) {
        if ($server['showtime'] > $time) {
            break;
        }
        $serverId = $server['id'];
        if (!$serverName) {
            $serverName = $server['name']['zh'];
            $url = $server['url'];
        }
    }
}

$nextServerId = $serverId + 1;
$serverName = str_replace(1,$nextServerId,$serverName);
if (isset($serverList[$nextServerId])) {
    $serverName = $serverList[$nextServerId]['name'];
}

// 获取当前用户人数
if (!isset($config[$nextServerId])) {
    echo '下一组服务器配置未配置';
    exit;
}

$config = $config[$serverId]['game'];

$sql= "select count(*) as c from `gm_sharding`";
$db = @mysql_connect( "{$config['host']}:{$config['port']}" , $config['user'] , $config['passwd']);
mysql_query( "SET NAMES 'utf8'" );
mysql_select_db( $config['name'], $db );
$result = mysql_query($sql,$db);
$result = mysql_fetch_assoc($result);

if ($result['c'] <= 10001) {
    echo '当前人数:'.$result['c'];
    exit;
}

// 配置新的服务器
$adddata = array(
    'id' => $nextServerId,
    'url' => $url,
    'name' => array(
        'zh' => $serverName,
    ),
    'status' => 5,
    'skin' => 1,
    'showtime' => $time,
);

ServerModel::addServList($adddata);

echo '新开服务器:'.$nextServerId;
mysqli_close($db);