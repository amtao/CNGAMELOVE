<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6177
 */
class Act6177Model extends ActHDBaseModel
{
	public $atype = 6177;//活动编号
	public $comment = "限时奖励-出城寻访次数";
	public $b_ctrl = "xufang";//子类配置
	public $hd_id = 'huodong_6177';//活动配置文件关键字
    protected $_rank_id = 6177;
}
