<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动218
 */
class Act218Model extends ActHDBaseModel
{
	public $atype = 218;//活动编号
	public $comment = "限时奖励-赈灾次数";
	public $b_ctrl = "zhenzai";//子类配置
	public $hd_id = 'huodong_218';//活动配置文件关键字
    protected $_rank_id = 218;
}
