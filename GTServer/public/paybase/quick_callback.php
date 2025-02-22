<?php

/**
 * quick
 * @author wulong <wulongcomputer@gmail.com>
 * @version 20190306
 */
require_once dirname(dirname(__FILE__)) . '/common.inc.php';

function returnRes($data, $msg = '') {
    if (!empty($msg)) {
	Game::order_debug($msg);
    }
    exit($data);
}

// 记录request参数
$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
Game::order_debug('回调接收到的数据=' . var_export($params, 1));
if (empty($params)) {
    returnRes('FAILED', '数据为空');
}

//获取平台
$platform = defined('SNS') ? SNS : '';
if (empty($platform)) {
    returnRes('FAILED', '平台为空');
}

//平台验证
Common::loadModel('OrderModel');
$Api = OrderModel::sdk_func($platform);
if (!$Api->verifyOrder($params)) {
    returnRes('SignError', '签名错误');
}

$decodeData = $Api->decodeParams($params);
Game::order_debug('解析后数据=' . var_export($decodeData, 1));

$extrainfo = explode('|', $decodeData['extras_params']);
$orderAmount = floatval($decodeData['amount']);
if (0.01 > $orderAmount) {
    Game::order_debug('错误:订单金额错误');
    $data = json_encode(array('ResultCode' => 5, 'ResultMsg' => '验证失败', 'gameid'=> CLIENT_ID));
    echo $data;
    exit();
}

if(intval($extrainfo[2]) > 6480) {
    // 直购礼包订单校验
    if (($extrainfo[2] % 10000) / 10 != $orderAmount) {
        Game::order_debug('错误:直购礼包订单金额错误');
        $data = json_encode(array('ResultCode' => 5, 'ResultMsg' => '验证失败', 'gameid'=> CLIENT_ID));
        echo $data;
        exit();
    }
}

if ( '0' != $decodeData['status'] ) {
    returnRes('FAILED', '充值失败');
}

//验证通过,处理逻辑
$data = array(
    'openid' => (defined('SNS_PF_PREFIX') && SNS_PF_PREFIX) ? SNS_PF_PREFIX . '_' . trim($decodeData['channel_uid']) : trim($decodeData['channel_uid']),
    'newOrder' => 1,
    'servid' => intval($extrainfo[1]),
    'orderid' => 0, //游戏唯一订单号
    'money' => $orderAmount, //RMB
    'tradeno' => $decodeData['order_no'],
    'paytype' => SNS_BASE,
    'roleid' => $extrainfo[0],
    'actcoin' => $extrainfo[2],
);


//更新sql数据，并且添加玩家数据
$is_ok = OrderModel::order_success($data);

if ($is_ok) {
    returnRes('SUCCESS', '充值成功');
}

returnRes('FAILED', '数据更新失败');

