<?php
error_reporting(E_ALL);
ini_set('display_errors','on');
$pf = 'epmycxianyuovergat_zjfh';
//echo "!!!!!:". dirname(dirname(__FILE__)) . '/../pay_cfg/' . $pf . '.php';

if (file_exists(dirname(dirname(__FILE__)) . '/../pay_cfg/' . $pf . '.php')) {
    require_once dirname(dirname(__FILE__)) . '/../pay_cfg/' . $pf . '.php';
} else {
    exit('param invalid(_pf)');
}

require_once dirname(dirname(__FILE__)) . '/../common.inc.php';

$sevid = 1;
if (!defined('SERVER_ID')) {
    $sevid = ( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) ? 999 : 1;
} else {
    $sevid = intval(SERVER_ID);
}
$SevidCfg = Common::getSevidCfg($sevid);

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

$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;

_debug(__METHOD__, "params:{$url}:::" . var_export($params, true));

if((empty($params['StartDateTime']) &&
    empty($params['EndDateTime'])) && empty($params['MyCardTradeNo'])) {
    echo '参数错误:';
    exit();
}

if(!(empty($params['StartDateTime']) ||
    empty($params['EndDateTime']))) {
    $mStartDateTime = strtotime($params['StartDateTime']);
    $mEndDateTime = strtotime($params['EndDateTime']);
    $sql = "SELECT *, from_unixtime(time,'%Y-%m-%dT%H:%i:%S')as 'TradeDateTime' FROM `mycard_order` WHERE `state` = '1' and `time` BETWEEN '{$mStartDateTime}' AND '{$mEndDateTime}'";
}
else {
    $MyCardTradeNo = $params['MyCardTradeNo'];
    $sql = "SELECT *, from_unixtime(time,'%Y-%m-%dT%H:%i:%S')as 'TradeDateTime' FROM `mycard_order` WHERE `state` = '1' and `MyCardTrade` = '{$MyCardTradeNo}'";
}

//echo "\r\n".$sql;




$OrderData = "";
$db = Common::getComDb();
$dbdata = $db->fetchArray($sql);
foreach($dbdata as $v) {
    $OrderData .= ($v['PaymentType']) . "," .
        $v['TradeSeq'] . "," .
        $v['MyCardTrade'] . "," .
        $v['FacTradeSeq'] . "," .
        $v['CustomerId'] . "," .
        $v['Amount'] . "," .
        'TWD' . "," .
        $v['TradeDateTime'] . "<BR>";
        }


$OrderData = rtrim($OrderData, ',<RB>');

echo $OrderData;


