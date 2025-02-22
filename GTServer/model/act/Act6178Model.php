<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6178
 */
class Act6178Model extends ActHDBaseModel
{
	public $atype = 6178;//活动编号
	public $comment = "限时奖励-徒弟历练次数";
	public $b_ctrl = "lilian";//子类配置
	public $hd_id = 'huodong_6178';//活动配置文件关键字
    protected $_rank_id = 6178;
}
