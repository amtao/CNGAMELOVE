<?php
//
require_once "ActFlBaseModel.php";
/*
 * 世界BOSS - 蒙古军来袭 门客出战列表
 */
class Act4Model extends ActFlBaseModel
{
	public $atype = 4;//活动编号
	
	public $comment = "蒙古军来袭出战列表";
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "mgft";//返回信息 所在控制器
}
