<?php
require_once "ActBaseModel.php";
/*
 * 活动199
 */
class Act199Model extends ActBaseModel
{
	public $atype = 199;//活动编号
	public $comment = "活动生效列表版本";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'ver' => 0,
	);
	
	/**
	 * 保存活动生效列表版本id
	 * @param $ver  
	 */
	public function add_ver($ver){
		if($this->info['ver'] != $ver){
			$this->info['ver'] = $ver;
			$this ->save();
		}
	}
	
	public function back_data(){
		
	}
	
}
