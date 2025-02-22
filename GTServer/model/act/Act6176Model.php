<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6176
 */
class Act6176Model extends ActHDBaseModel
{
	public $atype = 6176;//活动编号
	public $comment = "限时奖励-皇子应援次数";
	public $b_ctrl = "yingyuan";//子类配置
	public $hd_id = 'huodong_6176';//活动配置文件关键字
    protected $_rank_id = 6176;
}
