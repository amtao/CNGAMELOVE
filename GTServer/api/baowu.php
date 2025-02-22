<?php
//卡牌操作
class baowuMod extends Base
{
	private function drawOne($pool){
		$pitems = array();
		$totalP = 0;
		$nowP = 0;
		$pools_cfg = Game::getcfg('baowu_pool_items');
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
	public function drawbaowu($params){
		$backdata = array();
		$UserModel = Master::getUser($this->uid);
		$BaowuModel = Master::getBaowu($this->uid);
		$DrawType = Game::intval($params,'drawtype');
		$Poolid = Game::intval($params,'poolid');
		if($Poolid == 0)
		{
			$Poolid = 1;//默认卡池为1
		}
		
		$pool_cfg = Game::getcfg_info('baowu_pool',$Poolid);
		if(empty($pool_cfg)){
			Master::error('pool_cfg_err_'.$Poolid);
		}
		$drawcnt = 1;
		$Act319Model =  Master::getAct319($this->uid);
		//单抽
		if($DrawType ==0){
			$drawcnt = 1;
			if(!empty($pool_cfg["cost"]))
			{
				
				$is_free = $Act319Model->check_free($Poolid);
				
				if($is_free ==false){
					Master::sub_item($this->uid,KIND_ITEM,$pool_cfg['cost']['itemid'],$pool_cfg['cost']['num']);
				}else{
					$Act319Model->set_freeuse($Poolid,$pool_cfg["free_itv"]);
				}
				
			}
			$is_first = $Act319Model->getIsFirst();
			if ($is_first == false){
				$baowuItemn = $this->drawOne($Poolid);
			}else {
				$baowuItemn = $this->drawOne(4);
			}
			if(empty($baowuItemn))
			{
				Master::error('draw_baowu_item_err_');
			}else{
				$BaowuModel->drawAddBaowu($baowuItemn,$backdata );
			}
			
		}else if ($DrawType ==1){
			$drawcnt = 10;
			//尝试抽取
			$trydraw = array();
			//抽到的最佳品质
			$highqulity =0;
			//十连抽
			$mutCount = $pool_cfg['mut_times'];
			for($i = 1; $i <= $mutCount; ++$i) {
				$baowuItemn =$this->drawOne($Poolid);
				if($i == $mutCount){
                    //如果没有达到最低品质则进入保底奖池
                    if(!empty($pool_cfg['sure_pool'])  && $pool_cfg['sure_qulity'] >$highqulity){
                        $baowuItemn =$this->drawOne($pool_cfg['sure_pool']);
                    }
				}
				
				if(empty($baowuItemn))
				{
					Master::error('draw_baowu_item_err_');
				}
				//查看最大id
				if($baowuItemn["kind"] == 202)
				{
					$baowucfg = Game::getcfg_info('baowu',$baowuItemn["itemid"]);
					if($baowucfg['quality'] >$highqulity)
					{
						$highqulity = $baowucfg['quality'];
					}
				}
				$trydraw[] = $baowuItemn; 
				
			}

			if(!empty($pool_cfg["mut_cost"]))
			{
				//echo "id:".$pool_cfg['mut_cost']['itemid']."num".$pool_cfg['mut_cost']['num'];
				Master::sub_item($this->uid,KIND_ITEM,$pool_cfg['mut_cost']['itemid'],$pool_cfg['mut_cost']['num']);
			}
			foreach ($trydraw as $draw){
				$BaowuModel->drawAddBaowu($draw,$backdata );
			}
		}
		Master::back_data($this->uid,'baowu','drawBaowu',$backdata);
		$Act319Model->back_data();

		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(16,$drawcnt);

		//抽卡任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(111,$drawcnt);
		$Act39Model->task_add(110,count($BaowuModel->info));

		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(7);

		$Act750Mdoel = Master::getAct750($this->uid);
		$Act750Mdoel->setIsPop(8,$drawcnt);
		//$BaowuModel->getBaowuList();
		//return $backdata;
		//$Act319->draw_baowu($DrawType,$Poolid);
	}

	/*
	 *卡牌升级
	 */
	public function upgradeBaowu($params){
		/*
		$UserModel = Master::getUser($this->uid);
		$BaowuModel = Master::getBaowu($this->uid);
		
		//卡牌ID
		$BaowuId = Game::intval($params,'id');
		//卡牌ID合法
		$baowu_info = $BaowuModel->check_info($BaowuId);
		
		//数据提取
		$exp = $hero_info['exp'];//当前经验
		
		//门客升级所需金币
		$hero_level_cfg = Game::getcfg_info('hero_level',$hero_info['level']);
		//爵位等级配置
		$hero_senior_cfg = Game::getcfg_info('hero_senior',$hero_info['senior']);
		
		//是否达到爵位等级上限
		if ($hero_senior_cfg['max_level'] <= $hero_info['level']){
			Master::error(HERO_LEVEL_CAP);
		}
		
		//当前等级所需金币
		$need_cost = $hero_level_cfg['cost'] - $hero_info['exp'];
		$need_cost = max($need_cost,0);
		
		//金币够不够
		if ($UserModel->info['coin'] >= $need_cost){
			
			$hero_info['level'] += 1;
			//神迹
//			if ($hero_info['level'] < 160){
//				$Act65Model = Master::getAct65($this->uid);
//				if ($Act65Model->rand(3)){
//					//触发神迹:连升三级
//					$hero_info['level'] += 2;
//					//如果被神迹生了3级 判断等级上限
//					$hero_info['level'] = min($hero_senior_cfg['max_level'],$hero_info['level']);
//				}
//			}
			
			//够的话 直接升级 扣除金币
			$exp = 0;
		}else if ($UserModel->info['coin'] > 0){
			//不够的话 扣除剩余全部金币 加上对应经验值
			$need_cost = $UserModel->info['coin'];
			$exp += $need_cost;
		}else{
			Master::error(RES_SHORT.'|'."2");
		}
		
		//扣钱
		Master::sub_item($this->uid,KIND_ITEM,2,$need_cost);
		
		//更新门客
		$h_update = array(
			'heroid' => $HeroId,
			'level' => $hero_info['level'],
			'exp' => $exp,
		);
		$HeroModel->update($h_update);
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(4);
		$Act39Model->task_refresh(5);

        //舞狮大会 - 伙伴升级
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(5,1);
		*/
		return true;
	}

	public function quick_buy($params){	

		//$id = Game::intval($params,'id');
		$num = Game::intval($params,'num');
		$_price = Game::getcfg_param("baowu_item_buy_cost");
		//扣除元宝
		Master::sub_item($this->uid,KIND_ITEM,1,$num*$_price);

		Master::add_item($this->uid,KIND_ITEM,80000,$num);
	}

	public function upBaowuStar($params){
		$UserModel = Master::getUser($this->uid);
		$BaowuModel = Master::getBaowu($this->uid);
		$BaowuModel = Master::getBaowu($this->uid);
		//卡牌ID
		$BaowuId = Game::intval($params,'id');
		//卡牌ID合法
		$startUpCfg = $BaowuModel->findbaowustarupcfg($BaowuId);
		if($startUpCfg == NULL || $startUpCfg['star'] ==9 ){
			Master::error(BAOWU_LEVEL_CAP);
		}
		
		$baowuCfg = Game::getcfg_info('baowu',$BaowuId);
		$ItemModel = Master::getItem($this->uid);

		$orgcount = $ItemModel->get_item_count($baowuCfg['item']);

		$baowuitemcost = $startUpCfg['cost'];
		$anyId = 0;
		$anyCost = 0;
		/*
		if($orgcount< $baowuitemcost){
			$anyCost = $baowuitemcost - $orgcount;
			if($baowuCfg['quality'] == 1 || $baowuCfg['quality'] ==2){
				
				// $anyId = Game::getcfg_param("baowu_starup_currencyitem1");
				
			}else if($baowuCfg['quality'] == 3){
				// $anyId = Game::getcfg_param("baowu_starup_currencyitem2");
			}else if($baowuCfg['quality'] == 4){
				// $anyId = Game::getcfg_param("baowu_starup_currencyitem3");
			}
			if($anyId >0){
				$baowuitemcost = $orgcount;
			}
		}
		if($anyId>0){
			//echo "costa".$anyId."n".$anyCost."c".$baowuitemcost."org".$orgcount;
			//使用万能碎片
			Master::sub_item($this->uid,201,$anyId,$anyCost);
		}*/
		if($baowuitemcost>0){
			//echo "costb".$baowuCfg['item']."n".$baowuitemcost;
			Master::sub_item($this->uid,201,$baowuCfg['item'],$baowuitemcost);
		}

				
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(108, 1);
		
		$BaowuModel->upstartBaowu($BaowuId);
		

	}
	public function unlock_cloth($params){
		//卡牌ID
		$BaowuId = Game::intval($params,'baowuid');
		$BaowuModel = Master::getBaowu($this->uid);
		$Act319Model =  Master::getAct319($this->uid);
		if($Act319Model->has_clothe($BaowuId)){
			Master::error(BAOWU_HAVE_UNLOCK_CLOTHE);
		}
		$baowuData = $BaowuModel->getBaowuInfo($BaowuId);
		if(empty($baowuData)){
			Master::error(NO_BAOWU);
		}
		if( intval($baowuCfg['clotheunlockstar']) >$baowuData['star']){
			Master::error(BAOWU_STAR_ERR);
		}

		$baowuCfg = Game::getcfg_info('baowu',$BaowuId);
		if(!empty($baowuCfg['clothe'])){
			Master::add_item($this->uid, $baowuCfg['clothe']['kind'], $baowuCfg['clothe']['itemid'],$baowuCfg['clothe']['num']);
		}else{
			Master::error(BAOWU_NO_CLOTHE);
		}
		
		
		$Act319Model->unlock_clothe($BaowuId);
		$backdata = array();
		
		$backdata['unlock_cloth']= $BaowuId;
		//Master::back_data($this->uid,'baowu','baowusys',$backdata,true);
	}
	public function read_story($params){
		$storyid = Game::intval($params,'storyid');
		$Act320Model =  Master::getAct320($this->uid);
		$Act320Model->read_story($storyid);
	}
	
}
