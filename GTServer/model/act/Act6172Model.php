<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6172
 */
class Act6172Model extends ActHDBaseModel
{
	public $atype = 6172;//活动编号
	public $comment = "限时奖励-精力丹消耗";
	public $b_ctrl = "jinglidan";//子类配置
	public $hd_id = 'huodong_6172';//活动配置文件关键字
    protected $_rank_id = 6172;
}
