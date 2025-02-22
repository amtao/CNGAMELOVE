<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动220
 */
class Act220Model extends ActHDBaseModel
{
	public $atype = 220;//活动编号
	public $comment = "限时奖励-活力丹消耗";
	public $b_ctrl = "huolidan";//子类配置
	public $hd_id = 'huodong_220';//活动配置文件关键字
    protected $_rank_id = 220;
}
