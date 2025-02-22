<?php

/**
 * 咸鱼SDK 只有登录 支付未接入
 * @author wulong
 * @version 1808181954
 */
require_once 'SDK.php';

class Api extends PlatformBase {

    private $_SDK;
    private static $apiInstance;
    private $_platformId;

    public static function getInstance() {
		if (!Api::$apiInstance) {
			Api::$apiInstance = new Api();
		}
		return Api::$apiInstance;
    }

    public function __construct() {
		$this->_SDK = new SDK();
    }

    //获取第三方平台的用户id标识
    public function getPlatformId(){
        return $this->_platformId;
    }
    
    // 验证用户登录
    public function verifyToken($params) {
		try {
			$retult = $this->_SDK->checkToken($params);
			if ($retult){
				$this->_platformId = trim($params['openid']);
				if ( !empty($this->_platformId) ) {
					if ( defined('SNS_PF_PREFIX') && SNS_PF_PREFIX ) {
						$this->_platformId = SNS_PF_PREFIX  . '_' . $this->_platformId;
					}
					return true;
				}
			}
		} catch (Exception $e) {

		}
		return false;
    }

    // 订单核实
    public function verifyOrder($params) {
        try {
            return $this->_SDK->checkOrder($params);
        } catch (Exception $e) {

        }
        return false;
    }

}
