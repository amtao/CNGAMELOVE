<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动216
 */
class Act216Model extends ActHDBaseModel
{
	public $atype = 216;//活动编号
	public $comment = "限时奖励-挑战书消耗";
	public $b_ctrl = "tiaozhanshu";//子类配置
	public $hd_id = 'huodong_216';//活动配置文件关键字
    protected $_rank_id = 216;
}
