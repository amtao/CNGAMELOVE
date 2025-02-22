<?php
//
require_once "ActFlBaseModel.php";
/*
 * 门客出战列表 跨服大理寺战 正常出战+出使令
 */
class Act302Model extends ActFlBaseModel
{
	public $atype = 302;//活动编号
	
	public $comment = "跨服大理寺战出战";
	public $b_mol = "kuafuyamen";//返回信息 所在模块
	public $b_ctrl = "cslist";//返回信息 所在控制器
	public $hd_id = 'huodong_300';
	public $hd_cfg;
	public function __construct($uid,$hid){
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    if(!empty($this->hd_cfg)){
	        parent::__construct($uid,$this->hd_cfg['info']['id'].Game::get_today_id());
	    }
	}
}