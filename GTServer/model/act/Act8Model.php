<?php
//
require_once "ActFlBaseModel.php";
/*
 * 门客出战列表 衙门战 挑战令 挑战+复仇
 */
class Act8Model extends ActFlBaseModel
{
	public $atype = 8;//活动编号
	
	public $comment = "衙门战复仇";
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "fclist";//返回信息 所在控制器
}
