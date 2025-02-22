<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动221
 */
class Act221Model extends ActHDBaseModel
{
	public $atype = 221;//活动编号
	public $comment = "限时奖励-魅力值涨幅";
	public $b_ctrl = "meilizhi";//子类配置
	public $hd_id = 'huodong_221';//活动配置文件关键字
    protected $_rank_id = 221;
}
