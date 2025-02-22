<?php
require_once "ActBaseModel.php";
/*
 * 通商-一键通商状态
 */
class Act113Model extends ActBaseModel
{
	public $atype = 113;//活动编号
	
	public $comment = "通商-一键通商";
	public $b_mol = "trade";//返回信息 所在模块
	public $b_ctrl = "root";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	     'status' => 0,
	);
	
	public function update(){
	    if(empty($this->info['status'])){
	        $this->info['status'] = 1;
	        $this->save();
	    }
	}
	/*
	 * 是否开启了一键通商
	 * */
	public function isOpen(){
	    $status = 0;
	    if($this->info['status'] == 1){
	        $status = 1;
	    }
	    return $status;
	}
	public function back_data() {
	    
	}
	
}
