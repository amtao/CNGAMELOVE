<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动223
 */
class Act223Model extends ActHDBaseModel
{
	public $atype = 223;//活动编号
	public $comment = "限时奖励-联盟副本伤害";
	public $b_ctrl = "clubbosshit";//子类配置
	public $hd_id = 'huodong_223';//活动配置文件关键字
    protected $_rank_id = 223;
}
