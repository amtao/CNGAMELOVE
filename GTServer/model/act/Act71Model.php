<?php
require_once "ActBaseModel.php";
/*
 * vip经验
 */
class Act71Model extends ActBaseModel
{
	public $atype = 71;//活动编号
	
	public $comment = "vip经验";
	public $b_mol = "order";//返回信息 所在模块
	public $b_ctrl = "vipexp";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		

	);
	

	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		
		$UserModel = Master::getUser($this->uid);
		$channel = $UserModel->info['channel_id'];
		$platform = $UserModel->info['platform'];
		Common::loadModel('OrderModel');
		$outf = OrderModel::vipexp_list($platform,$channel);
		$this->outf = $outf;
	}
	
	
	
	
	
}