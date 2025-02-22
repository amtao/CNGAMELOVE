<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动224
 */
class Act224Model extends ActHDBaseModel
{
	public $atype = 224;//活动编号
	public $comment = "限时奖励-联盟副本击杀（累计击杀僵尸）";
	public $b_ctrl = "clubbossjs";//子类配置
	public $hd_id = 'huodong_224';//活动配置文件关键字
    protected $_rank_id = 224;
}
