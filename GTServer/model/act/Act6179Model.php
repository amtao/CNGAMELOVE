<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6179
 */
class Act6179Model extends ActHDBaseModel
{
	public $atype = 6179;//活动编号
	public $comment = "限时奖励-御膳房烹饪次数";
	public $b_ctrl = "pengren";//子类配置
	public $hd_id = 'huodong_6179';//活动配置文件关键字
    protected $_rank_id = 6179;
}
