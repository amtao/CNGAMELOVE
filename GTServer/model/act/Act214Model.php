<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动214
 */
class Act214Model extends ActHDBaseModel
{
	public $atype = 214;//活动编号
	public $comment = "限时奖励-招募士兵次数";
	public $b_ctrl = "zhaomu";//子类配置
	public $hd_id = 'huodong_214';//活动配置文件关键字
    protected $_rank_id = 214;
}
