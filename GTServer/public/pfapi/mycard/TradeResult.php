<?php
error_reporting(E_ALL);
ini_set('display_errors','on');


require_once dirname(dirname(__FILE__)) . '/../common.inc.php';
if (file_exists(dirname(dirname(__FILE__)) . '/../pay_cfg/' . $_GET['platform'] . '.php')) {
    require_once dirname(dirname(__FILE__)) . '/../pay_cfg/' . $_GET['platform'] . '.php';
} else {
    exit('param invalid(_pf)');
}


/**
 * MyCard查询订单的地址
 * 正式地址
 * 沙箱地址
 */
const ENDPOINT_QUERY = 'https://b2b.mycard520.com.tw/MyBillingPay/api/TradeQuery';
const SANDBOX_ENDPOINT_QUERY = 'https://test.b2b.mycard520.com.tw/MyBillingPay/api/TradeQuery';


/**
 * MyCard确认请款地址
 * DOC 3.4 確認 MyCard 交易，並進行請款
 */
const SANDBOX_ENDPOINT_PAYMENT_CONFIRM = 'https://test.b2b.mycard520.com.tw/MyBillingPay/api/PaymentConfirm';
const ENDPOINT_PAYMENT_CONFIRM = 'https://b2b.mycard520.com.tw/MyBillingPay/api/PaymentConfirm';

/**
 * 调试日志
 * @param type $location
 * @param type $msg
 * @return type
 */
function _debug($location, $msg) {
    if (defined('MSDK_DEBUG') && MSDK_DEBUG) {
        $logpath = ( defined('LOG_PATH') ) ? LOG_PATH : '/tmp/';
        $logpath .= 'msdk_' . SNS . '_' . date('Ymd') . '.log';
        Common::logMsg($logpath, sprintf("%s %s %s", date('Y-m-d H:i:s'), $location, $msg));
    }
    return;
}

/**
    * 请求平台接口
    * @param type $url
    * @param type $data
    * @return type
        * @throws Exception
    */
function _requestHTTPS($url, $data) {
    _debug(__METHOD__, "请求{$url}?" . http_build_query($data));
    $jsonStr = Common::requestHTTPS($url, $data, 'GET',false, 60);
    _debug(__METHOD__, "请求{$url} 返回了" . $jsonStr);
    if (empty($jsonStr)) {
        throw new Exception('返回空值', __LINE__);
    }
    $response = json_decode($jsonStr, true);
    _debug(__METHOD__, "请求{$url} json解压后结果为" . var_export($response, 1));
    if (empty($response)) {
        throw new Exception('解析失败', __LINE__);
    }
    return $response;
}

function _requestHTTP($url, $data) {
    _debug(__METHOD__, "请求{$url}?" . http_build_query($data));
    $jsonStr = Common::request($url, $data, 'GET',false, 60);
    _debug(__METHOD__, "请求{$url} 返回了" . $jsonStr);
    if (empty($jsonStr)) {
        throw new Exception('返回空值', __LINE__);
    }
    $response = json_decode($jsonStr, true);
    _debug(__METHOD__, "请求{$url} json解压后结果为" . var_export($response, 1));
    if (empty($response)) {
        throw new Exception('解析失败', __LINE__);
    }
    return $response;
}

set_time_limit(0);
ini_set('memory_limit','4000M');


$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
if(empty($params['AuthCode']) ||
    empty($params['FacTradeSeq']) ||
    empty($params['CustomerId']) ||
    empty($params['Amount']) ||
    empty($params['ExtraInfo'])||
    empty($params['OrderHash'])) {
    echo '参数错误:';
    exit();
}

_debug(__METHOD__, "public/pfapi/mycard/TradeResult.php:80:::" . var_export($params, true));
$hashrow = $params['AuthCode'].$params['FacTradeSeq'].$params['CustomerId'].$params['Amount'].$params['ExtraInfo'].$params['OrderHash'].PackageUUID;
if ($params['Hash'] != hash('sha256', $hashrow)){
    _debug(__METHOD__, "public/pfapi/mycard/TradeResult.php:83:::" . var_export($hashrow, true));
    _debug(__METHOD__, "public/pfapi/mycard/TradeResult.php:80:::" . var_export(hash('sha256', $hashrow), true));
    _debug(__METHOD__, "public/pfapi/mycard/TradeResult.php:85:::{$url}:::" . var_export($params, true));
    echo '签名错误:';
    exit();
}

$orderhashrow = $params['AuthCode'].$params['FacTradeSeq'].$params['CustomerId'].$params['Amount'].$params['ExtraInfo'].FactoryKey.PackageUUID;
if ($params['OrderHash'] != hash('sha256', $orderhashrow)){
    echo '订单签名错误:';
    exit();
}

$platform = $_GET['platform'];


// 校验订单
$parameter = [
    'AuthCode' => $params['AuthCode'],
];
if (false === SandBoxMode) {
    $url = ENDPOINT_QUERY . '?' . http_build_query($parameter);
} else {
    $url = SANDBOX_ENDPOINT_QUERY . '?' . http_build_query($parameter);
}
$result = _requestHTTPS($url, $parameter);
_debug(__METHOD__, "订单校验请求{$url}:::" . var_export($params, true));
_debug(__METHOD__, "订单校验请求{$url}:::" . var_export($parameter, true));
_debug(__METHOD__, "订单校验请求{$url}:::" . var_export($result, true));

$resultCode = array();
$resultCode['ReturnCode'] = '0';

if ($result['ReturnCode'] === "1" && $result['PayResult'] === "3") {    // 订单确认
    $parameter = [
        'AuthCode' => $params['AuthCode'],
    ];

    if (false === SandBoxMode) {
        $url = ENDPOINT_PAYMENT_CONFIRM . '?' . http_build_query($parameter);
    } else {
        $url = SANDBOX_ENDPOINT_PAYMENT_CONFIRM . '?' . http_build_query($parameter);
    }
    $resultConfirm = _requestHTTPS($url, $parameter);
    _debug(__METHOD__, "确认MyCard交易并进行请款{$url}:::" . var_export($params, true));
    _debug(__METHOD__, "确认MyCard交易并进行请款{$url}:::" . var_export($parameter, true));
    _debug(__METHOD__, "确认MyCard交易并进行请款{$url}:::" . var_export($result, true));
    if ($resultConfirm['ReturnCode'] === "1") {    // 请款发货
        $params['MyCardTrade'] = $result['MyCardTradeNo'];
        $params['PromoCode'] = $result['PromoCode'];
        $url = SNS_BACK_URL . "?" . http_build_query($params);
        $callBackResult = _requestHTTP($url, $parameter);
        $resultCode['ReturnCode'] = '1';
        $time = time();
        _debug(__METHOD__, "MyCard发货请求{$url}:::" . var_export($callBackResult, true));
        $db = Common::getComDb();
        $sql = "UPDATE `mycard_order` SET `state`='1', `MyCardTrade`='{$result['MyCardTradeNo']}',
                                                         `PaymentType`='{$result['PaymentType']}',
                                                         `TradeSeq`='{$resultConfirm['TradeSeq']}',
                                                         `time`='{$time}' WHERE (`FacTradeSeq`='{$params['FacTradeSeq']}')";
        _debug(__METHOD__, "MyCard发货请求SQL{$url}:::" . var_export($sql, true));
        $db->query($sql);
    }
}

echo json_encode($resultCode);

return;
