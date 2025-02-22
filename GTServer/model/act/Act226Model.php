<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动226
 */
class Act226Model extends ActHDBaseModel
{
	public $atype = 226;//活动编号
	public $comment = "限时奖励-粮食消耗";
	public $b_ctrl = "food";//子类配置
	public $hd_id = 'huodong_226';//活动配置文件关键字
    protected $_rank_id = 226;
}
