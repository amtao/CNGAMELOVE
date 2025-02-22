<?php
/*
 * 礼包配置
 */
require_once "SevBaseModel.php";
class Sev33Model extends SevBaseModel
{
	public $comment = "礼包配置信息";
	public $act = 33;//活动标签
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	
		 * )
		 */
	);
}
