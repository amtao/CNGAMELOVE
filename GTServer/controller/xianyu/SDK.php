<?php

/**
 * 咸鱼SDK 只有登录 支付未接入
 * @author wulong
 * @version 1808181954
 */
class SDK {
	// const LOGIN_VERIFY = 'https://sdk.xianyuyouxi.com/ucenter/login/verify';
    private $client_id;
    private $server_secret;

    public function __construct() {
	
    }

    /**
     * 调试日志
     * @param type $location
     * @param type $msg
     * @return type
     */
    private function _debug($location, $msg) {
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
    private function _request($url, $data) {
		$this->_debug(__METHOD__, "请求{$url}?" . http_build_query($data));
		$jsonStr = Common::request($url, $data, 'POST');
		$this->_debug(__METHOD__, "请求{$url} 返回了" . $jsonStr);
		if (empty($jsonStr)) {
			throw new Exception('返回空值', __LINE__);
		}
		$response = json_decode($jsonStr, true);
		$this->_debug(__METHOD__, "请求{$url} json解压后结果为" . var_export($response, 1));
		if (empty($response)) {
			throw new Exception('解析失败', __LINE__);
		}
		return $response;
    }

    /**
     * @param $data
     * @return bool
     */
	public function checkToken($data) {
		$this->_debug(__METHOD__, var_export($data, 1));
		$datas = array(
			'xyid' => $data['openid'],
			'token' => $data['openkey'],
        );
        if(defined('OVERSEAS')  && OVERSEAS){
            if(defined('OVERSEAS_DEF')&& OVERSEAS_DEF== 'xm')
            {
               $LOGIN_VERIFY = 'https://sdk-abroad.tomatogames.com/ucenter/login/verify';
            }
        }else{
            $LOGIN_VERIFY = 'https://sdk.xianyuyouxi.com/ucenter/login/verify';
        }

		$result = $this->_request($LOGIN_VERIFY, $datas);
        $this->_debug(__METHOD__, 'datas：' . var_export($datas,true));
		if ($result['code'] === '1'){
			return true;
		}else{
			return false;
		}
	}

    public function checkOrder($params) {
        //print_r($params);
        $pay_key = SERVER_SECRET;
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $queryString = '';
        foreach ($params as $key => $val) {
            $params[] = $key . '=' . $val;
            unset($params[$key]);
            $queryString = implode('&', $params);
        }

        $md5_str = $queryString . $pay_key;
        $mineSig = md5($md5_str);
        if ($sign == $mineSig){
            return true;
        }

		$this->_debug(__METHOD__, '验签:'.$params['sign']);
		$this->_debug(__METHOD__, '验签失败');
		return false;
    }
}
