<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动213
 */
class Act213Model extends ActHDBaseModel
{
	public $atype = 213;//活动编号
	public $comment = "限时奖励-经营农产次数";
	public $b_ctrl = "nongchan";//子类配置
	public $hd_id = 'huodong_213';//活动配置文件关键字
    protected $_rank_id = 213;
}
