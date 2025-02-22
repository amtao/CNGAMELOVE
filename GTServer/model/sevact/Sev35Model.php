<?php
/*
 * 聊天玩家GM系统
 */
require_once "SevBaseModel.php";
class Sev35Model extends SevBaseModel
{
	public $comment = "聊天玩家GM系统";
	public $act = 35;//活动标签
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	'uid' => '玩家uid'，
		 * )
		 */
	);
}
