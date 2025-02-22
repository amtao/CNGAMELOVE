<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动217
 */
class Act217Model extends ActHDBaseModel
{
	public $atype = 217;//活动编号
	public $comment = "限时奖励-惩戒犯人次数";
	public $b_ctrl = "cjfanren";//子类配置
	public $hd_id = 'huodong_217';//活动配置文件关键字
    protected $_rank_id = 217;
}
