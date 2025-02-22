<?php
require_once "ActBaseModel.php";
/*
 *  公用兑换码
 */
class Act38Model extends ActBaseModel
{
	public $atype = 38;//活动编号

	public $comment = "公用兑换码";
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
		    Master::error(ACT_96_DUIHUAN);
		}
		$this->info[$act] = $key;
		$this->save();
	}
	
	public function back_data() {
	    
	}
}
