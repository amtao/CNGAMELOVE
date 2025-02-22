<?php
header("Content-type: text/html; charset=utf-8");
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$params =
array (
  'appId' => '2882303761517595362',
  'cpOrderId' => '238_999',
  'cpUserInfo' => 'cpUserInfo',
  'orderId' => '20170720141838798587migc',
  'orderStatus' => 'TRADE_SUCCESS',
  'payFee' => '100',
  'payTime' => '2017-07-20 14:18:47',
  'productCode' => '01',
  'productCount' => '1',
  'productName' => '黄金',
  'uid' => '143390505',
  'signature' => 'd4cde8c38064332cdad75ce54dd1269e55be346c',
)
;

$url = 'http://king.coolnull.com/pay/miuigjjp_callback.php';
 
echo 'request:', EOL;
echo '<pre>', $url, '</pre><hr/>', EOL;
echo '<pre>', var_export($params, 1), '</pre><hr/>', EOL;
 
$ret = request($url, $params, 'POST');
echo 'response:', EOL;
echo '<pre>', var_export($ret, 1), '</pre><hr/>', EOL;
 
 
$ret = json_decode($ret, true);
echo 'json_decode:', EOL;
echo '<pre>', var_export($ret, 1), '</pre><hr/>', EOL;
 
function request($url, $params = '', $mode='POST', $needHeader = false, $timeout = 8){
	$curlHandle = curl_init();
	curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($curlHandle, CURLOPT_USERAGENT, 'MSDK_PHP_v0.0.3(20131010)');
 
	if ($needHeader) {
		curl_setopt($curlHandle, CURLOPT_HEADER, true);
	}
 
	if (strtolower($mode) == 'post') {
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($curlHandle, CURLOPT_POST, true);
		if (is_array($params)) {
			curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($params));
		} else {
			curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $params);
		}
	} else {
		if (is_array($params)) {
			$url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
		} else {
			$url .= (strpos($url, '?') === false ? '?' : '&') . $params;
		}
	}
	curl_setopt($curlHandle, CURLOPT_URL, $url);
 
	$result = curl_exec($curlHandle);
 
	if ($needHeader) {
		$tmp = $result;
		$result = array();
		$info = curl_getinfo($curlHandle);
		$result['header'] = substr($tmp, 0, $info['header_size']);
		$result['body'] = trim(substr($tmp, $info['header_size']));  //直接从header之后开始截取，因为 1.body可能为空   2.下载可能不全
		//$info['download_content_length'] > 0 ? substr($tmp, -$info['download_content_length']) : '';
	}
	$errno = curl_errno($curlHandle);
	if ($errno) {
		$result = $errno;
	}
	curl_close($curlHandle);
	return $result;
}