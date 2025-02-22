<?php
require_once "ActBaseModel.php";
/*
 * 御膳房
 */
class Act6100Model extends ActBaseModel
{
	public $atype = 6100;//活动编号
	
	public $comment = "买火炉";
	public $b_mol = "kitchen";//返回信息 所在模块
	public $b_ctrl = "base";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//火炉数量
		'stove' => 3, //当前数量
        'overCount' => 0,//完成次数
	);
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	//略 因为输出信息 跟 保存信息一致 
	
	/*
	 * 检查火炉ID 范围合法
	 */
	public function click_id($id = 1){
		if ($id <= 0 || $id > $this->info['stove']){
			Master::error("STOVE_ID_ERR_".$id);
		}
	}

	public function addOver($num = 1){
        //完成次数
        $this->info['overCount'] = $this->info['overCount'] + $num;
        $this->save();
    }

	/*
	 * 加上书桌数量
	 * 只能一个个加
	 */
	public function add_stove(){
		//所需黄金
		$cost = Game::getcfg_info('kitchen_cost',$this->info['stove']);
		$this->info['stove'] += 1;
		//直接在这里扣钱
		if ($cost['cash'] > 0){
			Master::sub_item($this->uid,KIND_ITEM,1,$cost['cash']);
		}		
		$this->save();
	}
}
