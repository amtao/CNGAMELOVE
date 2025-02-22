<?php
require_once dirname( __FILE__ ) . '/common.inc.php';

$banKey = 'AWY_BAN_USER_LIST';
$platformPrefix = 'shenhaih5_';
Game::order_debug('!!!!回调接收到的数据' . var_export($params, 1));
if (!isset($_REQUEST['uid']) || !isset($_REQUEST['gameid']) || !isset($_REQUEST['type']) || !isset($_REQUEST['sign'])) {
    $result = array(
        'error' => 1,
        'msg' => '缺少参数'
    );
    echo json_encode($result,true);
    exit;
}

$privateKey = '123123123123';
$params = $_REQUEST;
unset($params['sign']);
ksort($params);
$queryString = '';
if ($params && is_array($params)) {
    foreach ($params as $key => $val) {
        $params[] = $key . '=' . $val;
        unset($params[$key]);
    }
    $queryString = implode('&', $params);
}
$queryString .= '&key='.$privateKey;
$mySign =  strtoupper(md5($queryString));
$uid = $_REQUEST['uid'];
$type = $_REQUEST['type'];
$sign = $_REQUEST['sign'];

if ($mySign != $sign) {
    $result = array(
        'error' => 2,
        'msg' => '签名验证不一致'
    );
    echo json_encode($result,true);
    exit;
}

$redis = Common::getComRedis();

// type 1:禁言，2:解封
if ($type == 1) {
    $redis->hDel($banKey,$platformPrefix.$uid);
} else {
    $time = isset($_REQUEST['time']) ? $_REQUEST['time'] : 0;
    $redis->hSet($banKey,$platformPrefix.$uid,$time);
}

$redis->close();

$result = array(
    'error' => 0,
    'msg' => ''
);
echo json_encode($result,true);
exit;
