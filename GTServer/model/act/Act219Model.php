<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动219
 */
class Act219Model extends ActHDBaseModel
{
	public $atype = 219;//活动编号
	public $comment = "限时奖励-体力丹消耗";
	public $b_ctrl = "tilidan";//子类配置
	public $hd_id = 'huodong_219';//活动配置文件关键字
    protected $_rank_id = 219;
}
