<?php
/**
 * Mycard支付回调
 * @author wulong
 * @email wulongcomputer@gmail.com
 * @version 201810241714
 */
require_once dirname(dirname(__FILE__)) . '/common.inc.php';

// 记录request参数
$params = $_REQUEST;
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


$extrainfo = explode('|', $params['ExtraInfo']);
$orderAmount = floatval($extrainfo[3]);
if (0.01 > $orderAmount) {
    Game::order_debug('错误:订单金额错误');
    $data = json_encode(array('code' => -1, 'msg' => '错误:订单金额错误'));
    echo $data;
    exit();
}

//验证通过,处理逻辑
$data = array(
    'openid' => (defined('SNS_PF_PREFIX') && SNS_PF_PREFIX) ? SNS_PF_PREFIX . '_' . trim($extrainfo[5]) : trim($extrainfo[5]),
    'newOrder' => 1,
    'servid' => $extrainfo[1],
    'orderid' => 0, //游戏唯一订单号
    'money' => $orderAmount, //RMB
    'tradeno' =>$params['MyCardTrade'],
    'paytype' => SNS_BASE,
    'roleid' => $extrainfo[0],
    'actcoin' => $extrainfo[2],
);

//更新sql数据并且添加玩家数据
$is_ok = OrderModel::order_success($data);

if ($is_ok) {

    if($params['PromoCode'] != "A0000"){
        $rate = 0;
        switch ($params['PromoCode'])
        {
//            case "E1316": $rate = 0.2;break;
//            case "E1315": $rate = 0.2;break;
//            case "E1314": $rate = 0.1;break;
//
//            case "E1310": $rate = 0.15;break;
//            case "E1309": $rate = 0.2;break;
//            case "E1308": $rate = 0.2;break;
//            case "E1307": $rate = 0.1;break;
//
//            case "E1287": $rate = 0.1;break;
//            case "E1291": $rate = 0.1;break;
//            case "E1292": $rate = 0.2;break;
//            case "E1294": $rate = 0.2;break;
//            case "E1295": $rate = 0.1;break;
//            case "E1296": $rate = 0.1;break;
//            case "E1297": $rate = 0.2;break;
//            case "E1298": $rate = 0.2;break;
//
//            case "E1299": $rate = 0.1;break;
//            case "E1300": $rate = 0.2;break;
//            case "E1302": $rate = 0.2;break;

            case "E2045": $rate = 0.1;break;
            case "E2044": $rate = 0.1;break;
            case "E2043": $rate = 0.2;break;
            case "E2042": $rate = 0.2;break;
            case "E1940": $rate = 0.1;break;
            case "E1941": $rate = 0.1;break;
            case "E1942": $rate = 0.2;break;
            case "E1943": $rate = 0.2;break;
            case "E2049": $rate = 0.1;break;
            case "E2048": $rate = 0.1;break;
            case "E1980": $rate = 0.2;break;
            case "E1979": $rate = 0.2;break;
            case "E1977": $rate = 0.2;break;
            case "E1978": $rate = 0.2;break;
            case "E2046": $rate = 0.1;break;
            case "E2047": $rate = 0.1;break;
            case "E2036": $rate = 0.1;break;
            case "E2037": $rate = 0.1;break;
            case "E2038": $rate = 0.2;break;
            case "E2041": $rate = 0.2;break;

            case "E2120": $rate = 0.1;break;
            case "E2121": $rate = 0.1;break;
            case "E2122": $rate = 0.2;break;
            case "E2123": $rate = 0.2;break;


            case "E2124": $rate = 0.1;break;
            case "E2125": $rate = 0.1;break;
            case "E2126": $rate = 0.2;break;
            case "E2127": $rate = 0.2;break;
            case "E2128": $rate = 0.1;break;
            case "E2129": $rate = 0.1;break;
            case "E2130": $rate = 0.2;break;
            case "E2131": $rate = 0.2;break;

            case "E2135": $rate = 0.2;break;
            case "E2134": $rate = 0.2;break;
            case "E2133": $rate = 0.1;break;
            case "E2132": $rate = 0.1;break;
        }

        $title = 'Mycard儲值返利到賬通知';
        $content = '您的Mycard儲值返利'.$orderAmount * 10 * $rate.'元寶已到賬';

        $mailModel = Master::getMail($extrainfo[0]);
        $mailModel->sendMail($extrainfo[0], $title, $content, 1, array(
            array('id'=>1, 'count'=>$orderAmount * 10 * $rate),
        ));
        Master::click_destroy();
    }

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

