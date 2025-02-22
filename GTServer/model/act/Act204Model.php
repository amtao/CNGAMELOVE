<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动204
 */
class Act204Model extends ActHDBaseModel
{
	public $atype = 204;//活动编号
	public $comment = "限时奖励-强化卷轴消耗";
	public $b_ctrl = "juanzhou";//子类配置
	public $hd_id = 'huodong_204';//活动配置文件关键字
    protected $_rank_id = 204;
	
}
