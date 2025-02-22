<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动207
 */
class Act207Model extends ActHDBaseModel
{
	public $atype = 207;//活动编号
	public $comment = "限时奖励-处理政务次数";
	public $b_ctrl = "zhengwu";//子类配置
	public $hd_id = 'huodong_207';//活动配置文件关键字
    protected $_rank_id = 207;
}
