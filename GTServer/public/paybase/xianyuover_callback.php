<?php
/**
 * 咸鱼海外支付回调
 * @author wulong
 * @email wulongcomputer@gmail.com
 * @version 201810241714
 */
require_once dirname(dirname(__FILE__)) . '/common.inc.php';

// 记录request参数
$_params = file_get_contents('php://input');
Game::order_debug('!!!!回调接收到的数据' . file_get_contents('php://input'));
$params = json_decode($_params, true);
if (empty($params)) {
    Game::order_debug('失败:接收不到数据');
    $data = json_encode(array('code' => 1, 'msg' => '验证失败'));
    echo $data;
    exit();
}

// 获取平台
$platform = defined('SNS') ? SNS : '';
if (empty($platform)) {
    Game::order_debug('失败:平台为空');
    $data = json_encode(array('code' => 1, 'msg' => '验证失败'));
    echo $data;
    exit();
}

// 平台验证
Common::loadModel('OrderModel');
$Api = OrderModel::sdk_func($platform);
$rdata = $Api->verifyOrder($params);
if (!$rdata) {
    Game::order_debug('失败:验证失败');
    $data = json_encode(array('code' => 1, 'msg' => '验证失败'));
    echo $data;
    exit();
}

Game::order_debug('进入支付回调处理:');

$orderData = json_decode(base64_decode($params['data']), true);

$extrainfo = explode('|', $orderData['extInfo']);
$orderAmount = floatval($orderData['orderFee']);
if (0.01 > $orderAmount) {
    Game::order_debug('错误:订单金额错误');
    $data = json_encode(array('code' => -1, 'msg' => '错误:订单金额错误'));
    echo $data;
    exit();
}
//验证通过,处理逻辑
$data = array(
    'openid' => (defined('SNS_PF_PREFIX') && SNS_PF_PREFIX) ? SNS_PF_PREFIX . '_' . trim($orderData['uId']) : trim($orderData['uId']),
    'newOrder' => 1,
    'servid' => intval($orderData['serverId']),
    'orderid' => 0, //游戏唯一订单号
    'money' => $orderAmount, //RMB
    'tradeno' => $orderData['xyOrderId'],
    'paytype' => SNS_BASE,
    'roleid' => $extrainfo[0],
    'payid' => $extrainfo[1],
    'actcoin' => $extrainfo[2],
);

//更新sql数据并且添加玩家数据
$is_ok = OrderModel::order_success($data);

if ($is_ok) {
    Game::order_debug('订单成功处理完成');
    $data = json_encode(array('code' => 0, 'msg' => '下发成功'));
    echo $data;
    exit();
} else {
    Game::order_debug('失败:数据更新失败');
    $data = json_encode(array('code' => 1, 'msg' => '下发失败'));
    echo $data;
    exit('');
}

