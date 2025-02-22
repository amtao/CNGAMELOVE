<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动215
 */
class Act215Model extends ActHDBaseModel
{
	public $atype = 215;//活动编号
	public $comment = "限时奖励-击杀葛尔丹次数";
	public $b_ctrl = "jishag2d";//子类配置
	public $hd_id = 'huodong_215';//活动配置文件关键字
    protected $_rank_id = 215;
}
