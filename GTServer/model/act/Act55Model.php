<?php
require_once "ActBaseModel.php";
/*
 * 宴会--赴会次数
 */
class Act55Model extends ActBaseModel
{
	public $atype = 55;//活动编号
	
	public $comment = "宴会--赴会次数";
	public $b_mol = "jiulou";//返回信息 所在模块
	public $b_ctrl = "jlfy";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'fynum'  => 3, //当前剩余赴宴次数
		'fymax'  => 3, //赴宴次数最大值
	);
	
	/**
	 * 扣除刷新次数
	 */
	public function sub_fynum(){
		$this->info['fynum'] --;
		if($this->info['fynum'] < 0){
			Master::error(BOITE_ATTEND_NUM_SHORT);
		}
		$this->save();
	}
	
	
}
















