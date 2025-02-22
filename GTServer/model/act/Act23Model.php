<?php
require_once "ActBaseModel.php";
/*
 * 世界BOSS积分兑换
 */
class Act23Model extends ActBaseModel
{
	public $atype = 23;
	
	public $comment = "世界BOSS积分兑换";
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "shop";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'score' => 0,  //当前积分
		'dayid' => 0,   //上次兑换日期ID
		'buys' => 0,   //已兑换列表
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//是不是今天
		if ($this->info['dayid'] != Game::get_today_id()){
			//ID置为今天
			$this->info['dayid'] = Game::get_today_id();
			//购买记录清空
			$this->info['buys'] = array();
		}
		
		//购买列表
		$buys = array();
		foreach ($this->info['buys'] as $id => $v){
			$buys[] = array(
				'id' => $id,
				'num' => $v
			);
		}
		
		$this->outf = array(
			'score' => $this->info['score'],
			'buys' => $buys,
		);
	}
	
	
	//加上积分
	public function add($score){
		$this->info['score'] += $score;
		$this->save();
		//世界BOSS积分流水
		Game::cmd_flow(38, 1, $score, $this->info['score']);
	}
	
	/**
	 * 购买
	 */
	public function goumai($id){
		//商店配置 // ID是否存在
		$shop_cfg_info = Game::getcfg_info("wordboss_shop",$id);
		if(empty($this->info['buys'][$id])){
			$this->info['buys'][$id] = 0;
		}
		
		//已经购买次数
		$times = $this->info['buys'][$id];
		//次数上限
		if($times >= $shop_cfg_info['buymax']){
			Master::error(ACT23_CREDITS_EXCHANGE_MAX);
		}
		//需要的分值
// 		$need = round(pow(1.2,$times) * $shop_cfg_info['score']);
		$need = $shop_cfg_info['score'.($shop_cfg_info['buymax']-$times)];
		if($this->info['score'] < $need){
			Master::error(ACT23_INTEGRAL_SHORT);
		}
		$this->info['buys'][$id] += 1;
		//减去积分
		$this->info['score'] -= $need;
		//加上道具
		Master::add_item($this->uid,KIND_ITEM,$shop_cfg_info['itemid'],1);
		$this->save();

		//世界BOSS积分流水
		Game::cmd_flow(38, 1, -$need, $this->info['score']);
	}
}
