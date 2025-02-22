<?php
/**
 * @author wenyj
 * @version	20150723
 */
class SDK {
	private $_serverHost = '';

	// Set up the API root URL.
	const LOGIN_URL = '/api/local_login.php';// 登录
	const BIND_ACCOUNT_URL = '/api/local_bind_account.php';// 绑定账户
	const FORGOT_PASSWORD_URL = '/api/local_forgot_password.php';// 忘记密码

	public function __construct() {
		$this->_serverHost = (defined('SNS_ACCOUNT_HOST') && '' != SNS_ACCOUNT_HOST) ? SNS_ACCOUNT_HOST : DOMAIN_HOST;
	}

	/**
	 * 请求平台接口
	 * @param type $url
	 * @param type $data
	 * @param type $decode
	 * @return type
	 * @throws Exception
	 */
	private function _request($url, $data) {
		$http_build_query = (is_array($data)) ? http_build_query($data) : $data;
		$this->_debug(__METHOD__, "请求{$url}?" . $http_build_query);
		$jsonStr = Common::request($url, $data);
		$this->_debug(__METHOD__, "请求{$url} 返回了" . $jsonStr);
		if ( empty($jsonStr) ) {
			throw new Exception(ERROR_EMPTY_RETURN, __LINE__);
		}
		$response = json_decode($jsonStr, true);
		$this->_debug(__METHOD__, "请求{$url} json解压后结果为" . var_export($response, 1));
		if ( empty($response) ) {
			throw new Exception(ERROR_JSONDECODE_FAIL, __LINE__);
		}
		if ( '1' != $response['result'] ) {
			throw new Exception($response['msg'], $response['result']);
		}
		return $response;
	}

	/**
	 * 调试日志
	 * @param type $location
	 * @param type $msg
	 * @return type
	 */
	private function _debug($location, $msg)
	{
		if ( defined('MSDK_DEBUG') && MSDK_DEBUG ) {
			$logpath = ( defined('LOG_PATH') ) ? LOG_PATH : '/tmp/';
			$logpath .= 'msdk_' . SNS . '_' . date('Ymd') . '.log';
			Common::logMsg($logpath, sprintf("%s %s %s", date('Y-m-d H:i:s'), $location, $msg));
		}
		return ;
	}
	
	/**
	 * 登录
	 * @param unknown_type $params
	 */
	public function _login($params) {
		return $this->_request($this->_serverHost . self::LOGIN_URL, $params);
	}

	/**
	 * 绑定账户
	 * @param unknown_type $params
	 */
	public function _bindAccount($params) {
		return $this->_request($this->_serverHost . self::BIND_ACCOUNT_URL, $params);
	}

	/**
	 * 忘记密码
	 * @param unknown_type $params
	 */
	public function _forgotPassword($params) {
		return $this->_request($this->_serverHost . self::FORGOT_PASSWORD_URL, $params);
	}

}

