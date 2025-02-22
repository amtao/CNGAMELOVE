<?php
require_once "ActBaseModel.php";
/*
 *  通用兑换码
 */
class Act96Model extends ActBaseModel
{
	public $atype = 96;//活动编号

	public $comment = "通用兑换码";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		//'key' => 0,  //  act_key => key
	);
	
	public function save_acode($act,$key){
		if(!empty($this->info[$act])){
		    Master::error(ACODE_HAS_USER_SAME_TYPE);
		}
		$this->info[$act] = $key;
		$this->save();
	}
	
	public function back_data() {
	    
	}
}
