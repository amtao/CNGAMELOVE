<?php
/**
 * @wenyj
 */
class Api extends PlatformBase {
	private $_instance;
	private static $apiInstance;

	public static function getInstance() {
		if(!Api::$apiInstance)
		{
			Api::$apiInstance = new Api();
		}
		return Api::$apiInstance;
	}

	public function __construct() {
		
	}
	
	//验证用户登录
	public function verifyToken($params) {
		// 停止联运
		return true;
	}
	
	// 订单核实
	public function verifyOrder($params) {
        return true;
	}

}
