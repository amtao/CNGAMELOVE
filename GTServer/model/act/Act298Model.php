<?php
require_once "ActHDBaseModel.php";

/*
 * 活动298 新年活动
 */
class Act298Model extends ActHDBaseModel
{
	public $atype = 298;//活动编号
	public $comment = "新年活动";
	public $b_mol = "newyear";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_298';//活动配置文件关键字
	public $date;
	public $hdid = 'hd298';
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);
	
	/*
	 * 吓年兽
	 * id 档次id
	 * */
	public function play($id){
	    //判断活动是否结束
    	if( parent::get_state() == 0 || parent::get_state() == 2){
	        Master::error(ACTHD_OVERDUE);
	    }
		$itemid = $this->buyone($id);

        //判断道具情况 道具表中记录
		$item_cfg = Game::getcfg_info('item',$itemid,'不存在该道具');
		$daoju = $this->hd_cfg['daoju'];
		if(empty($daoju[$itemid])){
			Master::error(ITEMS_NOT_USE);
		}
		$item = $daoju[$itemid];

		$hurt = rand($item['hurt']['min'],$item['hurt']['max']);//计算伤害
		$kill = rand(1,100) <= $item['kill'] ? 1 : 0;//判断能不能直接吓退boss
		$Sev200Model = Master::getSev200($this->hd_cfg['info']['id']);
		$kill_state = $Sev200Model->add($this->uid,$hurt,$kill);

		if($kill_state){//击杀了，要给奖励
			//个人奖励固定奖励
			if($this->hd_cfg['boss']['kill']['my']['rwd']){//个人固定奖励
				Master::add_item3($this->hd_cfg['boss']['kill']['my']['rwd']);
			}
			if($this->hd_cfg['boss']['kill']['my']['rwd_rand']){//个人随机奖励
				$rwd_rand = $this->hd_cfg['boss']['kill']['my']['rwd_rand'];
				$rk = Game::get_rand_key(100,$rwd_rand,'prob');
				if($rwd_rand[$rk]){
					$rand_item = $rwd_rand[$rk];
					Master::add_item($this->uid,$rand_item['kind'],$rand_item['id'],$rand_item['count']);
					//记录日志
					$Sev201Model = Master::getSev201($this->_get_hd_id());
					$Sev201Model->add($this->uid,$rand_item['id'],$rand_item['count'],$kill);
				}
			}
			//排行前几的奖励
			if($this->hd_cfg['boss']['kill']['other']['rwd']){
				$other_rwd = $this->hd_cfg['boss']['kill']['other']['rwd'];
				$key = self::_get_day_redis_key();//今日排行榜key
				$redis = Common::getDftRedis();
				$max = $this->_get_kill_rwd_num();
				$rdata  = $redis->zRevRange($key, 0, $max,true);  //获取排行数据
				if(!empty($rdata) && !empty($other_rwd)){
					foreach ($rdata as $fuid => $score){
						foreach ($other_rwd as $itm){
							Master::add_item($fuid,$itm['kind'],$itm['id'],$itm['count'],'msgwin','other');
						}
					}
				}
			}
		}
	}

	/**
	 * 获取击杀boss需要奖励的排行榜人数个数
	 * @return int
	 */
	private function _get_kill_rwd_num(){
      	return empty($this->hd_cfg['boss']['kill']['other']['max']) ? $this->hd_cfg['boss']['kill']['other']['max']-1 : 9;
    }

	/*
	 * 商品购买
	 * id 商品列表档次 id
	 * */
	public function buyone($id){
	    //判断id是否可以购买
		$shop = $this->hd_cfg['shop'][$id];
	    if(empty($shop) || empty($shop['need'])){
	        Master::error(HD_TYPE8_DONT_SHOPING);
	    }
		//商城购买限制
	    $Act143Model = Master::getAct143($this->uid);
	    if($shop['is_limit'] == 1 && $shop['limit'] <= $Act143Model->info[$id]){//限购判断
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    //扣除购买道具需要的元宝
	    Master::sub_item($this->uid,empty($shop['need']['kind']) ? KIND_ITEM : $shop['need']['kind'],$shop['need']['id'],$shop['need']['count']);

	    //购买 如果该商品有限购
	    if($shop['is_limit'] == 1){
	        $Act143Model->add($id);
	    }
		return $shop['items']['id'];
	}
	/*
	 * 商品兑换
	 * 兑换列表档次id
	 * */
	public function exchange($id){
	    
	    if( parent::get_state() == 0){
	        Master::error(ACTHD_OVERDUE);
	    }
	     
	    //判断id是否可以兑换
	    if(empty($this->hd_cfg['exchange'][$id]) ){
	        Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
	    }
		$exchange = $this->hd_cfg['exchange'][$id];

	    $Act142Model = Master::getAct142($this->uid);
	    if($exchange['is_limit'] == 1 && $exchange['limit'] <= $Act142Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
		//积分计算
	    $Act144Model = Master::getAct144($this->uid);
	    $Act144Model->add_score($exchange['need'],'hfscore');

	    $items = $exchange['item'];//要兑换的信息
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }

	    $Act142Model->add_items($id);

	    Master::add_item($this->uid,$items['kind'],$items['id'],$items['count']);
	}
	
	/*
	 * 构造输出
	 */
	public function data_out(){
	    $this->date = date('Ymd',time());
	    //活动状态
	    if( parent::get_state() == 0){
	        Master::error(ACTHD_OVERDUE);
	    }

		$Act142Model = Master::getAct142($this->uid);
		$Act142Model->back_data();

		$Act144Model = Master::getAct144($this->uid);
		$Act144Model->back_data();

		$Sev200Model = Master::getSev200($this->_get_hd_id());
		$Sev200Model->back_data();

		$Sev201Model = Master::getSev201($this->_get_hd_id());
		$Sev201Model->bake_data();

		//活动信息
		$hd_cfg['info'] = $this->hd_cfg['info'];
	    $hd_cfg['id'] = $hd_cfg['no'];
	    $hd_cfg['rwd'] = $this->hd_cfg['rwd'];
		if(!empty($this->hd_cfg['shop'])){
			foreach ($this->hd_cfg['shop'] as $shop){
				$hd_cfg['shop'][] = array('id' => $shop['id'],'cash'=>$shop['need']['count']);
			}
		}
		$hd_cfg['boss'] = 100;
	    Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
	}
	
	
	public function get_news(){
	     return 0;
	}
	
	
	/*
	 * 排行榜
	 * */
	public function paihang($type = 1){
		//活动状态
		if( parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}
		switch ($type){
			case 1:
				//今日榜单
				$Redis127Model = Master::getRedis127($this->_get_day_redis_id());
				$Redis127Model->back_data();
				$Redis127Model->back_data_my($this->uid);
				break;
			case 2:
				//总排行
				$Redis128Model = Master::getRedis128($this->_get_hd_id());
				$Redis128Model->back_data();
				$Redis128Model->back_data_my($this->uid);
				break;
			case 3:
                //历史榜单排行榜
				$cache_key = $this->hd_id.'_'.$this->hd_cfg['info']['id'].'_'.Game::get_today_id();
				$cache = Common::getDftMem();
				//1、获取活动开始到昨日的key
				$history = $cache->get($cache_key);
				if(empty($history)){

					$Sev200Model = Master::getSev200($this->_get_hd_id());
					$history_day = $Sev200Model->getDayList();

					if(!empty($history_day)){//存在历史记录
						$history = array();
						foreach ($history_day as $day){
							$Redis127Model = Master::getRedis127($this->_get_hd_id().'_'.$day);
							$Redis127Model->out_num = 10;
							$outf = $Redis127Model->out_redis();
							if(!empty($outf)){
								$history[] = array(
									'id' => strtotime($day),
									'list' => $outf,
								);
							}
							unset($day,$outf,$Redis127Model);
						}

						if(!empty($history)){
							$cache->set($cache_key,$history);
						}
					}
				}
				Master::back_data($this->uid,$this->b_mol,'historyRankList',empty($history) ? array() : $history);
				break;
			default:
				Master::error(TYPE_ERROR);
				break;
		}
	}
	
	public function back_data_hd() {
	    self::data_out();
	}


	private function _get_hd_id(){
		return $this->hd_cfg['info']['id'];
	}

	private function _get_day_redis_key(){
		return $this->hd_id.'_day_'.$this->hd_cfg['info']['id'].'_'.Game::get_today_long_id().'_redis';
	}

	public function getBossInfo(){
		if(parent::get_state() == 0){
			return;
		}
		$Sev200Model = Master::getSev200($this->_get_hd_id());
		$Sev200Model->back_data();
	}
}
