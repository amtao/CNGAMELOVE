<?php
require_once "ActBaseModel.php";
/*
 * 酒楼-兑换商店
 */
class Act51Model extends ActBaseModel
{
	public $atype = 51;//活动编号
	
	public $comment = "酒楼-兑换商店";
	public $b_mol = "jiulou";//返回信息 所在模块
	public $b_ctrl = "jlShop";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'score' => 0,  //玩家拥有的积分
		'ftime' => 0, //刷新时间  2小时刷新一次
		'list' => array(),//兑换列表
	);
	
	/**
	 * 玩家拥有的积分
	 * @param unknown_type $num  要增加的宴会分数
	 */
	public function add_score($num){
		$this->info['score'] += $num;
		if($this->info['score'] < 0){
			$this->info['score'] = 0;
		}
		//记录流水 ($type,$itemid,$cha,$next)
        Game::cmd_flow(19,1,$num,$this->info['score']);
		$this->save();
	}
	
	/**
	 * 玩家拥有的积分
	 * @param unknown_type $num  使用的宴会分数
	 */
	public function sub_score($num){
		$this->info['score'] -= $num;
		if($this->info['score'] < 0){
			Master::error(BOITE_EXCHANGE_SCORE_SHORT);
		}
		//记录流水 ($type,$itemid,$cha,$next)
        Game::cmd_flow(19,1,-$num,$this->info['score']);
		$this->save();
	}
	
	/**
	 * 兑换 联盟物品
	 * @param unknown_type $id  档次id
	 */
	public function shop_buy($id){
		
		if(empty($this->info['list'][$id])){
			Master::error(PARAMS_ERROR);
		}
		if($this->info['list'][$id]['buy'] == 1){
			Master::error(BOITE_EXCHANGE_HAVE_GOODS);
		}
		//扣积分
		$this->sub_score($this->info['list'][$id]['cost']);
		//加道具
		$itemid = $this->info['list'][$id]['item']['id'];
		$count = $this->info['list'][$id]['item']['count'];
		Master::add_item($this->uid,KIND_ITEM,$itemid,$count);
		
		$this->info['list'][$id]['buy'] = 1;  //标记已兑换
		$this->save();
	}
	
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		
		//输出结构体
		$this->outf = array();
		$ftime = 2 * 60 * 60;  //刷新时间2小时 刷新一次
		$this->outf['score'] = $this->info['score'];
		
		$shop_cfg = Game::getcfg('jl_shop');
		
		//初始化列表     列表为空或者超过刷新时间
		if(empty($this->info['list']) 
		|| $this->info['ftime'] + $ftime < $_SERVER['REQUEST_TIME']){
			$this->info['ftime'] = $_SERVER['REQUEST_TIME'];
			//获取9个商品
			$this->refresh_list();
		}
		
		$this->outf['list'] = array_values($this->info['list']);
		$this->outf['ltime'] = array(
				'next' => $this->info['ftime'] + $ftime,//下次绝对时间
				'label' => 'jlShopltime',
		);
		return $this->outf;
	}
	
	public function refresh_list(){
		//获取9个商品
		$this->info['list'] = array();
		$shop_cfg = Game::getcfg('jl_shop');
		for($i = 1; $i <= 9; $i++){
			//获得商品标识id
			$shopid = $this->get_id($shop_cfg);
			if(empty($shop_cfg[$shopid])){
				continue;
			}
			
			$disCount = 10;  //打折  0:不打折
			if(!empty($shop_cfg[$shopid]['dis_prob_10000'])){
				$disr = rand(1,10000);  //随机出一个 1-10000的数
				foreach($shop_cfg[$shopid]['dis_prob_10000'] as $k => $v){
					if($disr > $v){
						$disr -= $v;
						continue;
					}
					$disCount = $k;
					break;
				}
			}
			
			
			//单个商品数据
			$this->info['list'][$shopid] = array(
				'id' => $shopid,
				'item' => $shop_cfg[$shopid]['good'],
				'cost' => floor($shop_cfg[$shopid]['cost']*$disCount/10),
				'buy' => 0,  //是否
				'discount' => $disCount, //打折
			);
			unset($shop_cfg[$shopid]);
		}
		$this->save();
	}
	
	
	
	/**
	 * 通过商店商品id
	 * @param unknown_type $data
	 */
	public function get_id($shop){
		$prob = 0;
		foreach($shop as $k => $v){
			$prob += $v['prob_10000'];
		}
		
		$roll = rand(1,$prob);
		$pid = 0;
		foreach($shop as $k => $v){
			if ($roll <= $v['prob_10000']){
				$pid = $k;
				break;
			}
			$roll -= $v['prob_10000'];
		}
		return $pid;
	}
	
	
}
















