<?php
class DanServer
{

	/**
	 * 全服数据基础界面
	 */
	public function index() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	/**
	 * 充值统计
	 */
	public function orderTJ() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	/**
	 * 充值查询
	 */
	public function orderCX() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	/**
	 * 直充
	 */
	public function zhichong() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	/**
	 * 直充查询
	 */
	public function zhichongCX() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
}
