<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6170
 */
class Act6170Model extends ActHDBaseModel
{
	public $atype = 6170;//活动编号
	public $comment = "限时珍宝阁累计整理关卡次数";
	public $b_ctrl = "treasure";//子类配置
	public $hd_id = 'huodong_6170';//活动配置文件关键字
    protected $_rank_id = 6170;
}
