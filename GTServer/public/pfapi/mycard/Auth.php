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
 * 获取授权码的
 * 正式地址 ENDPOINT_AUTH
 * 沙箱地址 SANDBOX_ENDPOINT_AUTH
 */
const ENDPOINT_AUTH = 'https://b2b.mycard520.com.tw/MyBillingPay/api/AuthGlobal';
const SANDBOX_ENDPOINT_AUTH = 'https://test.b2b.mycard520.com.tw/MyBillingPay/api/AuthGlobal';

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

set_time_limit(0);
ini_set('memory_limit','4000M');

$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
_debug(__METHOD__, "请求{$url}:::" . var_export($params, true));
$hashrow = $params['FacTradeSeq']."1".$params['CustomerId'].$params['ProductName'].$params['Amount'].$params['Currency'].$params['ExtraInfo'].PackageUUID;

if(empty($params['FacTradeSeq']) ||
    empty($params['CustomerId']) ||
    empty($params['ProductName']) ||
    empty($params['Amount']) ||
    $params['Currency'] != CURRENCY ||
    empty($params['ExtraInfo'])||
    empty($params['Hash'])) {
    echo '参数错误:';
    exit();
}

if($params['Amount'] != "30.00"   &&
    $params['Amount'] != "150.00"  &&
    $params['Amount'] != "300.00"  &&
    $params['Amount'] != "890.00"  &&
    $params['Amount'] != "1490.00" &&
    $params['Amount'] != "3000.00" &&
    $params['Amount'] != "150.00"  &&
    $params['Amount'] != "1390.00" &&

    $params['Amount'] != "60.00"  &&
    $params['Amount'] != "90.00"  &&
    $params['Amount'] != "120.00" &&
    $params['Amount'] != "180.00" &&
    $params['Amount'] != "210.00" &&
    $params['Amount'] != "240.00"
) {
    echo '充值档次异常:';
    exit();
}

$moneyCNY = 0;
switch ($params['Amount']) {
    case "30.00":   $moneyCNY = 6;break;
    case "150.00":  $moneyCNY = 30;break;
    case "300.00":  $moneyCNY = 68;break;
    case "890.00":  $moneyCNY = 198;break;
    case "1490.00": $moneyCNY = 328;break;
    case "3000.00": $moneyCNY = 648;break;

    case "150.00":  $moneyCNY = 28;break;
    case "1390.00": $moneyCNY = 288;break;

    case "60.00":   $moneyCNY = 12;break;
    case "90.00":   $moneyCNY = 18;break;
    case "120.00":  $moneyCNY = 25;break;
    case "180.00":  $moneyCNY = 40;break;
    case "210.00":  $moneyCNY = 45;break;
    case "240.00":  $moneyCNY = 50;break;
}


_debug(__METHOD__, "请求{$url}:::" . var_export($params, true));
$extrainfo = explode('|', $params['ExtraInfo']);

if($params['Amount'] == '150') {
    if($extrainfo[3] != "30.0" &&
        $extrainfo[3] != "28.0") {
        echo '150充值档次异常:';
    }
    $moneyCNY = $extrainfo[3];
}


$orderAmount = floatval($moneyCNY * 10);
if (0.01 > $orderAmount) {
    echo '订单金额错误:';
    exit();
}

if(intval($extrainfo[2]) > 6480) {
    // 直购礼包订单校验
    if (($extrainfo[2] % 10000) / 10 != ($moneyCNY)) {
        echo '直购礼包订单金额错误:';
        exit();
    }
}
else if($moneyCNY * 10 != $extrainfo[2]){
    echo '充值档次异常:';
    exit();
}


if ($params['Hash'] != hash('sha256', $hashrow)){
    echo '签名错误:';
    exit();
}
$platform = $_GET['platform'];

$TradeType = 1;
if($params['TradeType'] == '2'){
    $TradeType = 2;
}

$parameter = [
    'FacServiceId' => FacServiceId, //廠商服務代碼 由 MyCard 編列 测试环境参数Ginhi
    'FacTradeSeq' => $params['FacTradeSeq'], //廠商交易序號 廠商自訂，每筆訂單編號不得重 覆，為訂單資料 key 值
    'TradeType' => $TradeType, //※交易模式 1:Android SDK (手遊適用) 2:WEB
    // 'ServerId' => '', //伺服器代號 用戶在廠商端的伺服器編號 不可輸入中文 僅允許 0-9a-zA-Z._- 非必填
    'CustomerId' => $params['CustomerId'], //會員代號 用戶在廠商端的會員唯一識別 編號僅允許 0-9a-zA-Z._-
    // 'PaymentType' => '', // 此參數非必填，參數為空時將依 交易金額(Amount)和幣別 (Currency)判斷可用的付費方式 呈現給用戶選擇 INGAME/Billing/COSTPOINT
    // 'ItemCode' => '', // 此參數非必填，參數為空時將依 交易金額(Amount)和幣別 (Currency)判斷可用的付費方式 呈現給用戶選擇
    'ProductName' => $params['ProductName'], //產品名稱 用戶購買的產品名稱 中文字及全型符號一個字算兩 個字元
    'Amount' => $params['Amount'], //交易金額 可以為整數，若有小數點最多 2 位
    'Currency' => CURRENCY, //TWD/HKD/USD
    'SandBoxMode' => (SandBoxMode === true) ? "true" : "false", //※是否為測試環境 true/false string
];

$sort_list = ['FacServiceId', 'FacTradeSeq', 'TradeType', 'ServerId', 'CustomerId', 'PaymentType', 'ItemCode', 'ProductName', 'Amount', 'Currency', 'SandBoxMode'];

$pre_hash_value = '';
foreach ($sort_list as $key => $key_name) {
    if (isset($parameter[$key_name])) {
        if ($parameter[$key_name] !== urlencode($parameter[$key_name])) {
            $pre_hash_value = $pre_hash_value . strtolower(urlencode($parameter[$key_name]));
        } else {
            $pre_hash_value = $pre_hash_value . $parameter[$key_name];
        }
    }
}
$pre_hash_value = $pre_hash_value . FactoryKey;
$hash = hash('sha256', $pre_hash_value);
$parameter['Hash'] = $hash;

if (false === SandBoxMode) {
    $url = ENDPOINT_AUTH;
} else {
    $url = SANDBOX_ENDPOINT_AUTH;
}

$result = _requestHTTPS($url, $parameter);

_debug(__METHOD__, "请求{$url}:::" . var_export($params, true));
_debug(__METHOD__, "请求{$url}:::" . var_export($parameter, true));
_debug(__METHOD__, "请求{$url}:::" . var_export($result, true));

if ($result['ReturnCode'] == 1){
    _debug(__METHOD__, "Orderdata resign:::" . var_export($result, true));
    $result['FacTradeSeq'] = $params['FacTradeSeq'];
    $result['CustomerId'] = $params['CustomerId'];
    $result['Amount'] = $params['Amount'];
    $result['ExtraInfo'] = $params['ExtraInfo'];
    $result['OrderHash'] = hash('sha256', $result['AuthCode'].$result['FacTradeSeq'].$result['CustomerId'].$result['Amount'].$result['ExtraInfo'].FactoryKey.PackageUUID);
    $time = time();

    $db = Common::getComDb();
    $sql = "INSERT INTO `mycard_order` (`FacTradeSeq`, `CustomerId`, `Amount`, `ExtraInfo`, `OrderHash`, `AuthCode`, `time`, `Hash`) 
            VALUES ('{$result['FacTradeSeq'] }', '{$result['CustomerId'] }', '{$result['Amount']}', '{$result['ExtraInfo']}', '{$result['OrderHash']}', '{$result['AuthCode']}', '{$time}', '{$params['Hash']}')";
    $db->query($sql);
}

echo json_encode($result);
return;




