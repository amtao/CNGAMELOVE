<?php
/**
 * 咸鱼支付回调
 * @author wulong
 * @email wulongcomputer@gmail.com
 * @version 201810241714
 */
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
// 记录request参数
$params = $_REQUEST;

Game::order_debug('!!!!回调接收到的数据' . var_export($params, 1));
if (empty($params)) {
    Game::order_debug('失败:接收不到数据');
    $data = json_encode(array('code' => '3','msg'=>'参数不存在'));
    echo $data;
    exit();
}

// 获取平台
$platform = defined('SNS') ? SNS : '';
if (empty($platform)) {
    Game::order_debug('失败:平台为空');
    $data = json_encode(array('code' => '3','msg'=>'服务器平台为空'));
    echo $data;
    exit();
}

// 平台验证
Common::loadModel('OrderModel');
$Api = OrderModel::sdk_func($platform);
$rdata = $Api->verifyOrder($params);
if (!$rdata) {
    Game::order_debug('失败:验证失败');
    $data = json_encode(array('code' => '1','msg'=>'签名验证失败'));
    echo $data;
    exit();
}

Game::order_debug('进入支付回调处理:');

$extrainfo = explode('|', $params['cpOrderExtenson']);
$orderAmount = floatval($params['money']);
if (0.01 > $orderAmount) {
    Game::order_debug('错误:订单金额错误');
    $data = json_encode(array('code' => '2','msg'=>'moneyError'));
    echo $data;
    exit();
}

/*if(intval($extrainfo[2]) > 6480) {
    // 直购礼包订单校验
    if (($extrainfo[2] % 10000) / 10 != $orderAmount) {
        Game::order_debug('错误:直购礼包订单金额错误');
        $data = json_encode(array('code' => '3','msg'=>'直购礼包订单金额错误'));
        echo $data;
        exit();
    }
}*/


//验证通过,处理逻辑
$data = array(
    'openid' => (defined('SNS_PF_PREFIX') && SNS_PF_PREFIX) ? SNS_PF_PREFIX . '_' . trim($params['uid']) : trim($params['uid']),
    'newOrder' => 1,
    'servid' => intval($params['serverId']),
    'orderid' => 0, //游戏唯一订单号
    'money' => $orderAmount, //RMB
    'tradeno' => $params['xyOrderNo'],
    'paytype' => SNS_BASE,
    'roleid' => $extrainfo[0],
    'payid' => $extrainfo[1],
    'actcoin' => $extrainfo[2],
);


//更新sql数据并且添加玩家数据
$is_ok = OrderModel::order_success($data);

if ($is_ok) {
    Game::order_debug('订单成功处理完成');
    $databack="";
    $databack = json_encode(array('code' => '0','msg'=>'success'));
    echo $databack;
    exit();
} else {
    Game::order_debug('失败:数据更新失败');
    $databack="";
    $databack = json_encode(array('code' => '3','msg'=>'充值失败'));
    echo $databack;
    exit('');
}

