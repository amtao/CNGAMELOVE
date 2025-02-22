<?php
require_once "ActBaseModel.php";
/*
 * 新版酒楼-兑换商店
 */
class Act171Model extends ActBaseModel
{
	public $atype = 171;//活动编号
	
	public $comment = "新版酒楼-兑换商店";
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "jlShop";//返回信息 所在控制器
	protected $shop_cfg;
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'score' => 0,  //玩家拥有的积分
		'reset' => 0, //是否同步过积分
		'time' => 0, //刷新时间  每日重置
		'list' => array(),//兑换列表
	);

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//输出结构体
		$outf = array(
			'score' => intval($this->info['score']),
		    'list' => array(),
		);
		if(!empty($this->info['list'])){
			foreach ($this->info['list'] as $id => $num){
				$outf['list'][] = array(
					'id' => $id,
					'num' => $num,
				);
			}
		}
		$this->outf = $outf;
	}
	
	/**
	 * 玩家拥有的积分
	 * @param int $num  要增加的宴会分数
	 */
	public function add_score($num){
		$this->info['score'] += $num;
        Game::cmd_flow(19,1,$num,$this->info['score']);
		$this->save();
	}
	
	/**
	 * 玩家拥有的积分
	 * @param int $num  使用的宴会分数
	 * @param $is_save
	 */
	public function sub_score($num,$is_save = true){
		$this->info['score'] -= $num;
		if($this->info['score'] < 0){
			Master::error(BOITE_EXCHANGE_SCORE_SHORT);
		}
        Game::cmd_flow(19,1,-$num,$this->info['score']);
		if($is_save){
			$this->save();
		}
	}
	
	/**
	 * 兑换 宴会物品
	 * @param int $id  档次id
	 */
	public function shop_buy($id){
		//商城配置
		$cfg = Game::getcfg_info('boite_shop',$id,PARAMS_ERROR);
		$num = empty($this->info['list'][$id]) ? 0 : $this->info['list'][$id];
		$cost = intval($cfg['cost'] * pow(1.2,$num));
		$this->sub_score($cost,false);//扣积分 判断分数够不够
		//获得道具
		$itemid = $cfg['good']['id'];
		$count = $cfg['good']['count'];
		$kind = empty($cfg['good']['kind']) ? KIND_ITEM : $cfg['good']['kind'];
		Master::add_item($this->uid,$kind,$itemid,$count);
		//保存
		$this->info['list'][$id] += 1;
		$this->save();
	}

	/**
	 * 初始化
	 */
	public function init_data($back = true){
		$up = false;//是否更新
		//是否重置过积分
		if(empty($this->info['reset'])){//未重置
			$this->info['reset'] = 1;
			$Act51Model = Master::getAct51($this->uid);
			$this->info['score'] = $Act51Model->info['score'];
			$up = true;
		}
		//判断当前商店的购买次数是否是最新的
		if($this->info['time'] != Game::get_today_id()){
			$this->info['time'] = Game::get_today_id();
			$this->info['list'] = array();//已购买列表
			$up = true;
		}
		if($up){
			$this->save();
		}elseif($back){
			$this->back_data();
		}
	}
}
















