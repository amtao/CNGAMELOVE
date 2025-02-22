<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6175
 */
class Act6175Model extends ActHDBaseModel
{
	public $atype = 6175;//活动编号
	public $comment = "限时奖励-郊祀献礼次数";
	public $b_ctrl = "jiaoji";//子类配置
	public $hd_id = 'huodong_6175';//活动配置文件关键字
    protected $_rank_id = 6175;
}
