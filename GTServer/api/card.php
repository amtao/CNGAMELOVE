<?php
//卡牌操作
class cardMod extends Base
{
	private function drawOne($pool){
		$pitems = array();
		$totalP = 0;
		$nowP = 0;
		$pools_cfg = Game::getcfg('pool_items');
		if(!empty($pools_cfg)){
            foreach ($pools_cfg as $itm){
				if($itm["pool_id"] == $pool)
				{
					$pitems[] = $itm;
					$totalP += $itm["rate"];
				}
			}
		}
		$rnd =  rand(1,$totalP);
		if(!empty($pitems)){
            foreach ($pitems as $itm){
				$nowP += $itm["rate"];
				if($nowP>=$rnd)
				{
					return $itm;
				}
			}
		}
		return NULL;
	}
	public function drawCard($params){
		$backdata = array();
		$UserModel = Master::getUser($this->uid);
		$CardModel = Master::getCard($this->uid);
		$DrawType = Game::intval($params,'drawtype');
		$Poolid = Game::intval($params,'poolid');
		if($Poolid == 0)
		{
			$Poolid = 1;//默认卡池为1
		}
		//活动抽卡
		if($Poolid !=1){
			$Act6242Model =  Master::getAct6242($this->uid);
			$Act6242Model->draw_check($Poolid);
		}
		
		$pool_cfg = Game::getcfg_info('card_pool',$Poolid);
		if(empty($pool_cfg)){
			Master::error('pool_cfg_err_'.$Poolid);
		}
		$drawcnt = 1;
		$Act317Model =  Master::getAct317($this->uid);
		//单抽
		if($DrawType ==0){
			$drawcnt = 1;
			if(!empty($pool_cfg["cost"]))
			{
				
				$is_free = $Act317Model->check_free($Poolid);
				
				if($is_free ==false){
					Master::sub_item($this->uid,KIND_ITEM,$pool_cfg['cost']['itemid'],$pool_cfg['cost']['num']);
				}else{
					$Act317Model->set_freeuse($Poolid,$pool_cfg["free_itv"]);
				}
				
			}
			$is_first = $Act317Model->getIsFirst();
			if ($is_first == false){
				$cardItemn = $this->drawOne($Poolid);
			}else {
				$cardItemn = $this->drawOne(4);
			}
			if(empty($cardItemn))
			{
				Master::error('draw_card_item_err_');
			}else{
				$CardModel->drawAddCard($cardItemn,$backdata );
			}
			
		}else if ($DrawType ==1){
			$drawcnt = 10;
			//尝试抽取
			$trydraw = array();
			//抽到的最佳品质
			$highqulity =0;
			//十连抽
			$mutCount = $pool_cfg['mut_times'];
			//天赐卡活动保底
			$Act6242Model =  Master::getAct6242($this->uid);
			$actSure = $Act6242Model->mult_rwd($Poolid);
			for($i = 1; $i <= $mutCount; ++$i) {
				$cardItemn =$this->drawOne($Poolid);
				if($i == $mutCount){
					//如果存在活动保底则按活动保底
					if($actSure>0){
						//echo "sureitem".$actSure;
						$cardItemn["kind"] =99;
						$cardItemn["itemid"] = $actSure;
						$cardItemn["num"] = 1;
						//$cardItemn = $actSure;
					}else{
						//如果没有达到最低品质则进入保底奖池
						if(!empty($pool_cfg['sure_pool'])  && $pool_cfg['sure_qulity'] >$highqulity){
							$cardItemn =$this->drawOne($pool_cfg['sure_pool']);
						}
					}
				}
				
				if(empty($cardItemn))
				{
					Master::error('draw_card_item_err_');
				}
				//查看最大id
				if($cardItemn["kind"] == 99)
				{
					$cardcfg = Game::getcfg_info('card',$cardItemn["itemid"]);
					if($cardcfg['quality'] >$highqulity)
					{
						$highqulity = $cardcfg['quality'];
					}
				}
				$trydraw[] = $cardItemn; 
			}

			if(!empty($pool_cfg["mut_cost"]))
			{
				//echo "id:".$pool_cfg['mut_cost']['itemid']."num".$pool_cfg['mut_cost']['num'];
				Master::sub_item($this->uid,KIND_ITEM,$pool_cfg['mut_cost']['itemid'],$pool_cfg['mut_cost']['num']);
			}
			foreach ($trydraw as $draw){
				$CardModel->drawAddCard($draw,$backdata );
			}
		}
		Master::back_data($this->uid,'card','drawCard',$backdata);
		$Act317Model->back_data();

		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(15,$drawcnt);

		//抽卡任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(60,$drawcnt);
		$Act39Model->task_add(112,$drawcnt);
		$Act39Model->task_add(109,count($CardModel->info));

		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(6);

		$Act750Mdoel = Master::getAct750($this->uid);
		$Act750Mdoel->setIsPop(7,$drawcnt);
		//$CardModel->getCardList();
		//return $backdata;
		//$Act317->draw_card($DrawType,$Poolid);
	}

	//卡牌升级
	public function upgradeCard($params){
		$UserModel = Master::getUser($this->uid);
		$CardModel = Master::getCard($this->uid);
		$cardId = Game::intval($params,'cardid');
		
		//获取卡牌数据
		$cardCfg = Game::getcfg_info('card',$cardId);
		//判断当前星级对应的等级上限是不是大于我当前的卡牌等级
		$starCfg = Game::getcfg_info('card_starup',$cardCfg['quality']);
		$cardData = $CardModel->getCardInfo($cardId);
		$star = $cardData['star'];
		$level = $cardData['level'];
		if($level >= 60){
			Master::error(CARD_LEVEL_MAX);
		}
		if($level >= $starCfg[$star]['lvmax']){
			Master::error(CARD_LEVEL_UP1.$starCfg[$star+1]['lvmax'].CARD_LEVEL_UP2);
		}

		//消耗 - 升级 - 更新
		$cardLvUpCfg = Game::getcfg_info('card_lvlup',$cardCfg['quality']);
		foreach($cardLvUpCfg[$level]['cost'] as $_items){
			Master::sub_item2($_items);
		}
		
		$CardModel->uplvlCard($cardId,$level+1);
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(20,1);

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(149,1);
	}

	//卡牌升级五次
	public function upgradeCardFive($params){
		$UserModel = Master::getUser($this->uid);
		$CardModel = Master::getCard($this->uid);
		$ItemModel = Master::getItem($this->uid);
		$cardId = Game::intval($params,'cardid');
		
		//获取卡牌数据
		$cardCfg = Game::getcfg_info('card',$cardId);
		//判断当前星级对应的等级上限是不是大于我当前的卡牌等级
		$starCfg = Game::getcfg_info('card_starup',$cardCfg['quality']);
		$cardData = $CardModel->getCardInfo($cardId);
		$star = $cardData['star'];
		$level = $cardData['level'];
		if($level >= 60){
			Master::error(CARD_LEVEL_MAX);
		}
		$chaLv = 60 - $level;
		$chaLv = min($chaLv,5);
		$count = 0;
		if($level >= $starCfg[$star]['lvmax']){
			Master::error(CARD_LEVEL_UP1.$starCfg[$star+1]['lvmax'].CARD_LEVEL_UP2);
		}
		for($i = 0;$i < $chaLv; $i++){

			if($cardData['level'] >= $starCfg[$star]['lvmax']){
				continue;
			}
			//消耗 - 升级 - 更新
			$cardLvUpCfg = Game::getcfg_info('card_lvlup',$cardCfg['quality']);
			$isEnough = true;
			foreach($cardLvUpCfg[$cardData['level']]['cost'] as $_items){
				if($_items['itemid'] <= 5){
					if(!$UserModel->check_sth($_items['itemid'],$_items['count'])){
						$isEnough = false;
						break;
					}
				}else{
					$orgcount = $ItemModel->get_item_count($_items['itemid']);
					if($orgcount < $_items['count']){
						$isEnough = false;
						break;
					}
				}
			}
			if(!$isEnough){
				continue;
			}
			foreach($cardLvUpCfg[$cardData['level']]['cost'] as $_items){
				Master::sub_item2($_items);
			}
			$cardData['level'] += 1;
			$count++;
		}
		//更新卡牌
		// $h_update = array(
		// 	'cardid' => $cardId,
		// 	'level' => $cardData['level'],
		// 	'star' => $cardData['star'],
		// 	'isEquip' => $cardData['isEquip'],
		// 	'imprintLv' => $cardData['imprintLv'],
		// 	'flowerPoint' => $cardData['flowerPoint'],
		// );
		if($count < 5){
			Master::error_msg('大人的心羽不太够呢，可以通过合成、许愿获取');
		}
		$CardModel->uplvlCard($cardId,$cardData['level']);
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(20,$count);

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(149,$count);
		
	}

	public function quick_buy($params){	

		//$id = Game::intval($params,'id');
		$num = Game::intval($params,'num');
		$_price = Game::getcfg_param("card_item_buy_cost");
		//扣除元宝
		Master::sub_item($this->uid,KIND_ITEM,1,$num*$_price);

		Master::add_item($this->uid,KIND_ITEM,6000,$num);
	}

	public function upCardStar($params){
		$UserModel = Master::getUser($this->uid);
		$CardModel = Master::getCard($this->uid);
		//卡牌ID
		$CardId = Game::intval($params,'id');
		//卡牌ID合法
		$startUpCfg = $CardModel->findcardstarupcfg($CardId);
		if($startUpCfg == NULL || $startUpCfg['star'] ==9 ){
			Master::error(CARD_LEVEL_CAP);
		}
		
		$cardCfg = Game::getcfg_info('card',$CardId);

		// $cardLvlUpCfg = Game::getcfg_info('card_lvlup',$cardCfg['quality']);
		$CardData = $CardModel->getCardInfo($CardId);
		$level = $CardData['level'];
		// if($startUpCfg['star'] >= $cardLvlUpCfg[$level]['star'] ){
		// 	Master::error(CARD_LEVEL_STAR1.$level.CARD_LEVEL_STAR2);
		// }
		if($level < $startUpCfg['lvmax'] ){
			Master::error(CARD_LEVEL_STAR1.$startUpCfg['lvmax'].CARD_LEVEL_STAR2);
		}

		$ItemModel = Master::getItem($this->uid);

		$orgcount = $ItemModel->get_item_count($cardCfg['item']);

		$carditemcost = $startUpCfg['cost'];
		$anyId = 0;
		$anyCost = 0;
		if($orgcount< $carditemcost){
			$anyCost = $carditemcost - $orgcount;
			if($cardCfg['quality'] == 1){
				$anyId = Game::getcfg_param("card_starup_currencyitem0");
			}else if($cardCfg['quality'] ==2){
				$anyId = Game::getcfg_param("card_starup_currencyitem1");
			}else if($cardCfg['quality'] == 3){
				$anyId = Game::getcfg_param("card_starup_currencyitem2");
			}else if($cardCfg['quality'] == 4){
				$anyId = Game::getcfg_param("card_starup_currencyitem3");
			}
			if($anyId >0){
				$carditemcost = $orgcount;
			}
		}
		if($UserModel->info['food'] < $startUpCfg['yinliang']){
			Master::error(CARD_LEVEL_UP_COIN);
		}
		if($anyId>0){
			//echo "costa".$anyId."n".$anyCost."c".$carditemcost."org".$orgcount;
			//使用万能碎片
			Master::sub_item($this->uid,KIND_CARD_STONE,$anyId,$anyCost);
		}
		if($carditemcost>0){
			//echo "costb".$cardCfg['item']."n".$carditemcost;
			Master::sub_item($this->uid,KIND_CARD_STONE,$cardCfg['item'],$carditemcost);
		}
		Master::sub_item($this->uid,KIND_ITEM,3,$startUpCfg['yinliang']);
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(107, 1);
		
		$CardModel->upstartCard($CardId);
		

	}
	public function unlock_cloth($params){
		//卡牌ID
		$CardId = Game::intval($params,'cardid');
		$CardModel = Master::getCard($this->uid);
		$Act317Model =  Master::getAct317($this->uid);
		if($Act317Model->has_clothe($CardId)){
			Master::error(CARD_HAVE_UNLOCK_CLOTHE);
		}
		$cardData = $CardModel->getCardInfo($CardId);
		if(empty($cardData)){
			Master::error(NO_CARD);
		}
		if( intval($cardCfg['clotheunlockstar']) >$cardData['star']){
			Master::error(CARD_STAR_ERR);
		}

		$cardCfg = Game::getcfg_info('card',$CardId);
		if(!empty($cardCfg['clothe'])){
			Master::add_item($this->uid, $cardCfg['clothe']['kind'], $cardCfg['clothe']['itemid'],$cardCfg['clothe']['num']);
		}else{
			Master::error(CARD_NO_CLOTHE);
		}
		
		
		$Act317Model->unlock_clothe($CardId);
		$backdata = array();
		
		$backdata['unlock_cloth']= $CardId;
		//Master::back_data($this->uid,'card','cardsys',$backdata,true);
	}

	/**
	 * 卡牌碎片分解
	 * framents id,count|id,count
	 */
	public function cardDecompose($params){
		//$aFraments所有的碎片信息
		//$aFragmentArr 所有的碎片array
		$aFragments = Game::strval($params,'fragments');
		$aFragmentArr = explode("|",$aFragments);
		$ItemModel = Master::getItem($this->uid);
		$getItemArr = array();
		foreach($aFragmentArr as $oFragment){
			$oArr = explode(",",$oFragment);
			//$oArr[0]--itemid
			//$oArr[1]--count
			$deItemid = $oArr[0];
			$deCount = $oArr[1];
			$cardCfg = Game::getcfg_info('card',$deItemid);
			$hasCount = $ItemModel->get_item_count($deItemid);
			if($hasCount < $deCount){
				Master::error_msg(CARD_DECOMPOSE_COUNT_1.$cardCfg['name'].CARD_DECOMPOSE_COUNT_2);
				continue;
			}
			$cardComposeCfg = Game::getcfg_info('card_decompose',$cardCfg['quality']);
			foreach($cardComposeCfg as $items){
				foreach($items as $_item){
					$getItemArr[$_item['itemid']] += $_item['count']*$deCount;
				}
			}
			Master::sub_item($this->uid,KIND_ITEM,$deItemid,$deCount);
		}
		foreach ($getItemArr as $itemid => $count) {
			Master::add_item($this->uid,KIND_ITEM,$itemid,$count);
		}
	}

	//卡牌印痕升级
	public function cardImprintUpLv($params){
		$cardId = Game::intval($params,'cardId');
		$CardModel = Master::getCard($this->uid);
		$cardInfo = $CardModel->check_info($cardId);

		$cardCfg = Game::getcfg_info('card',$cardId);
		if($cardCfg['hero'] == 0){
			Master::error(CARD_IMPRINTLV_NOT_UP_LEVEL);
		}
		$cardYhCfg = Game::getcfg_info('card_yinhen',$cardCfg['quality']);
		if($cardInfo['imprintLv'] >= end($cardYhCfg)['yinheng']){
			Master::error(CARD_IMPRINT_MAX_LEVLE);
		}
		$heroId = $cardCfg['hero'];
		$nextLv = $cardInfo['imprintLv']+1;
		$upInfo = $cardYhCfg[$nextLv];
		foreach($upInfo['item'.$heroId] as $items){
			Master::sub_item2($items);
		}
		$CardModel->upImprintlvCard($cardId);
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(157,1);
	}

	//卡牌升华
	public function cardFlowerPoint($params){
		$cardId = Game::intval($params,'cardId');
		$point = Game::intval($params,'point');
		$cardCfg = Game::getcfg_info('card',$cardId);
		$CardModel = Master::getCard($this->uid);
		$cardInfo = $CardModel->check_info($cardId);

		$flowers = json_decode($cardInfo['flowerPoint'],true);
		if(empty($flowers)){
			$flowers = array();
		}
		if(in_array($point,$flowers)){
			Master::error(CARD_FLOWER_HAS_POINT);
		}
		$cardFlowerCfg = Game::getcfg_info('card_flower',$cardCfg['quality']);
		$flowerInfo = $cardFlowerCfg[$point];
		if($cardInfo['imprintLv'] < $flowerInfo['yinhen']){
			Master::error(CARD_IMPRINTLV_NOT_ENOUGH);
		}
		if($flowerInfo['pre_point'] > 0){
			if(!in_array($flowerInfo['pre_point'],$flowers)){
				Master::error(CARD_FLOWER_NO_PRE_POINT);
			}
		}
		foreach ($flowerInfo['cost'] as $items) {
			Master::sub_item2($items);	
		}
		array_push($flowers,$point);
		$flowers = json_encode($flowers,JSON_UNESCAPED_UNICODE);
		$CardModel->upFlowerPointCard($cardId,$flowers);

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(158,1);
	}

	public function read_story($params){
		$storyid = Game::intval($params,'storyid');
		$Act318Model =  Master::getAct318($this->uid);
		$Act318Model->read_story($storyid);
	}

	/**
	 * 修改卡牌特效状态
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function updCardSpecialEffects($params){

		$cardId = Game::intval($params,'cardId');
		$status = Game::strval($params,'status');

		$Act8500Model = Master::getAct8500($this->uid);
		$Act8500Model->updCardSpecialEffects($cardId, $status);
	}
}
