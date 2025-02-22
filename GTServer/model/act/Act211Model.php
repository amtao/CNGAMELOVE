<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动211
 */
class Act211Model extends ActHDBaseModel
{
	public $atype = 211;//活动编号
	public $comment = "限时奖励-书院学习";
	public $b_ctrl = "school";//子类配置
	public $hd_id = 'huodong_211';//活动配置文件关键字
    protected $_rank_id = 211;
}
