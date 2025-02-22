<?php
class PlatformBase {
	public function __construct() {
		
	}
	
	//验证用户登录
	public function verifyToken($params) {
		return false;
	}
	
	// 订单核实
	public function verifyOrder($params) {
        return false;
	}
}