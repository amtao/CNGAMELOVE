<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6212
 */
class Act6212Model extends ActHDBaseModel
{
	public $atype = 6212;//活动编号
	public $comment = "限时奖励-偷取晨露次数";
	public $b_ctrl = "stealdew";//子类配置
	public $hd_id = 'huodong_6212';//活动配置文件关键字
    protected $_rank_id = 6212;

}
