<?php
//
require_once "ActFlBaseModel.php";
/*
 * 联盟 - 联盟boss 门客出战列表
 */
class Act6Model extends ActFlBaseModel
{
	public $atype = 6;//活动编号
	
	public $comment = "联盟boss出战列表";
	public $b_mol = "club";//返回信息 所在模块
	public $b_ctrl = "bossft";//返回信息 所在控制器
}
