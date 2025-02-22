<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动222
 */
class Act222Model extends ActHDBaseModel
{
	public $atype = 222;//活动编号
	public $comment = "限时奖励-赴宴次数";
	public $b_ctrl = "fuyanhui";//子类配置
	public $hd_id = 'huodong_222';//活动配置文件关键字
    protected $_rank_id = 222;
}
