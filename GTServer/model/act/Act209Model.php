<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动209
 */
class Act209Model extends ActHDBaseModel
{
	public $atype = 209;//活动编号
	public $comment = "限时奖励-衙门分数涨幅";
	public $b_ctrl = "yamen";//子类配置
	public $hd_id = 'huodong_209';//活动配置文件关键字
    protected $_rank_id = 209;
}
