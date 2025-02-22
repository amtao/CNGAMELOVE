<?php
require_once "ActBaseModel.php";
/*
 * 勤政爱民
 */
class Act31Model extends ActBaseModel
{
	public $atype = 31;//活动编号
	
	public $comment = "勤政爱民";
	public $b_mol = "jingYing";//返回信息 所在模块
	public $b_ctrl = "qzam";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'now' => 0,  //当前点数
		'max' => 60,  //点数上限 
	);
	
	/**
	 * 增加勤政次数
	 */
	public function add($num = 1){
		if ($this->info['now'] < $this->info['max']){
			$this->info['now'] += $num;
			$this->info['now'] = min($this->info['now'],$this->info['max']);
			$this->save();
		}
	}
	
	/*
	 * 领取勤政奖励
	 */
	public function rwd(){
		if ($this->info['now'] < $this->info['max']){
			Master::error(OPERATE_TIME_IS_NOT);
		}
		$this->info['now'] = 0;
		$this->save();
	}
}
















