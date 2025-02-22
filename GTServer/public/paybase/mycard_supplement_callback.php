<?php
/**
 * Mycard支付回调
 * @author wulong
 * @email wulongcomputer@gmail.com
 * @version 201810241714
 */
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
$ip = [
    '210.71.189.165',  // MyCard正式服务器IP
    '218.32.37.148'     // MyCard测试服务器IP
];

$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;

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
function _request($url, $data) {
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


//if (!in_array(Common::GetIP(), array(
//    '210.71.189.165',  // MyCard正式服务器IP
//    '218.32.37.148'     // MyCard测试服务器IP
//))){
//    exit('IPERR');
//}


_debug(__METHOD__, "mycard_supplement_callback.php{$url}:::" . var_export($params, true));

// 记录request参数
if (empty($params)) {
    Game::order_debug('失败:接收不到数据');
    $data = json_encode(array('code' => 1, 'msg' => '验证失败empty params'));
    echo $data;
    exit();
}
set_time_limit(0);
ini_set('memory_limit','4000M');

$data = json_decode($params['DATA'], true);
//echo var_export($data, true);
if(!empty($data) && $data["ReturnCode"] === "1"){
    $FacTradeSeqlist = '';
    foreach ($data['FacTradeSeq'] as $order) {
        $FacTradeSeqlist .= "'{$order}',";
    }
    $FacTradeSeqlist = rtrim($FacTradeSeqlist, ',');

    $sql = "SELECT * FROM `mycard_order` WHERE `FacTradeSeq` IN ({$FacTradeSeqlist}) AND `state` = '0'";
    $db = Common::getComDb();
    $dbdata = $db->fetchArray($sql);
    foreach($dbdata as $v){
        $v['platform'] = SNS;
        $v['Hash'] = hash('sha256', $v['AuthCode'].$v['FacTradeSeq'].$v['CustomerId'].$v['Amount'].$v['ExtraInfo'].$v['OrderHash'].PackageUUID);

        //echo  var_export($v, true);

        _debug(__METHOD__, "MyCardTrade发货请求{$url}:::" . var_export($v, true));
        $url = SNS_TRADE_URL."?".http_build_query($v);
        $TradeResult = _request($url, $v);
        _debug(__METHOD__, "MyCardTrade发货请求{$url}:::" . var_export($callBackResult, true));
    }

    Game::order_debug('成功:ReturnCodeSuccess');
    $data = json_encode(array('code' => 1, 'msg' => 'ReturnCodeSuccess'));
    echo $data;
    exit();

}
else {
    Game::order_debug('失败:ReturnCodeError');
    $data = json_encode(array('code' => 0, 'msg' => 'ReturnCodeError'));
    echo $data;
    exit();
}
