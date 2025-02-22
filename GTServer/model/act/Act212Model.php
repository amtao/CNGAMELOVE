<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动212
 */
class Act212Model extends ActHDBaseModel
{
	public $atype = 212;//活动编号
	public $comment = "限时奖励-经营商产次数";
	public $b_ctrl = "jingshang";//子类配置
	public $hd_id = 'huodong_212';//活动配置文件关键字
    protected $_rank_id = 212;
}
