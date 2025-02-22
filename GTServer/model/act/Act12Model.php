<?php
require_once "ActBaseModel.php";
/*
 * 子嗣席位数量类
 */
class Act12Model extends ActBaseModel
{
	public $atype = 12;//活动编号
	
	public $comment = "子嗣席位";
	public $b_mol = "son";//返回信息 所在模块
	public $b_ctrl = "base";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//席位数量
		'seat' => 2,
	);
	
	/*
	 * 返回当前席位
	 */
	public function get_seat(){
		return $this->info['seat'];
	}
	/*
	 * 
	 */
	
	/*
	 * 加上席位数量
	 * 只能一个个加
	 */
	public function add_seat(){
		$this->info['seat'] += 1;
		//所需黄金
		$son_seat_info = Game::getcfg_info('son_seat',$this->info['seat']);
		
		//直接在这里扣钱
		Master::sub_item($this->uid,KIND_ITEM,1,$son_seat_info['cash']);
		$this->save();
	}
}
