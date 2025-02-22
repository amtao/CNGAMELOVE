<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6174
 */
class Act6174Model extends ActHDBaseModel
{
	public $atype = 6174;//活动编号
	public $comment = "限时奖励-问候知己次数";
	public $b_ctrl = "wenhou";//子类配置
	public $hd_id = 'huodong_6174';//活动配置文件关键字
    protected $_rank_id = 6174;
}
