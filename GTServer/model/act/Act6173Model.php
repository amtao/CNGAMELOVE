<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6173
 */
class Act6173Model extends ActHDBaseModel
{
	public $atype = 6173;//活动编号
	public $comment = "限时奖励-知己出游次数";
	public $b_ctrl = "chuyou";//子类配置
	public $hd_id = 'huodong_6173';//活动配置文件关键字
    protected $_rank_id = 6173;
}
