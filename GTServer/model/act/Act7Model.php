<?php
//
require_once "ActFlBaseModel.php";
/*
 * 门客出战列表 衙门战 正常出战+出使令
 */
class Act7Model extends ActFlBaseModel
{
	public $atype = 7;//活动编号
	
	public $comment = "衙门战出战";
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "cslist";//返回信息 所在控制器
}
