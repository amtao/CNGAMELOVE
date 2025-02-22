<?php
/*
 * 邮件审核
 */
require_once "SevBaseModel.php";
class Sev34Model extends SevBaseModel
{
	public $comment = "邮件审核信息";
	public $act = 34;//活动标签
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	'title' => '邮件标题'，
		 *  'items' => 0,//道具
		 *  'time' => 0,//过期时间
		 * )
		 */
	);
}
