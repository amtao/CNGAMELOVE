<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6213
 */
class Act6213Model extends ActHDBaseModel
{
	public $atype = 6213;//活动编号
	public $comment = "限时奖励-御花园种植次数";
	public $b_ctrl = "plant";//子类配置
	public $hd_id = 'huodong_6213';//活动配置文件关键字
    protected $_rank_id = 6213;
	
}
