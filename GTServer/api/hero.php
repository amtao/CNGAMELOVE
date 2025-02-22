<?php
//伙伴操作
class heroMod extends Base
{
	/*
	 *门客升级
	 */
	public function upgrade($params){
		$UserModel = Master::getUser($this->uid);
		$HeroModel = Master::getHero($this->uid);
		
		//门客ID
		$HeroId = Game::intval($params,'id');
		//门客ID合法
		$hero_info = $HeroModel->check_info($HeroId);
		
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

		$Act700Model = Master::getAct700($this->uid);
		if($Act700Model->getOpenDay() < 8){
			$Act700Model->setSevenTask(5);
		}

        //舞狮大会 - 伙伴升级
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(5,1);
		
		return true;
	}

    /*
 *门客升级 连升10级
 */
    public function upgradeTen($params){
        $UserModel = Master::getUser($this->uid);
        $HeroModel = Master::getHero($this->uid);

        //门客ID
        $HeroId = Game::intval($params,'id');
        //门客ID合法
        $hero_info = $HeroModel->check_info($HeroId);

        if($UserModel->info['coin'] <= 0){
            Master::error(RES_SHORT.'|'."2");
        }

        //数据提取
        $exp = $hero_info['exp'];//当前经验

        //爵位等级配置
        $hero_senior_cfg = Game::getcfg_info('hero_senior',$hero_info['senior']);

        //可以升级的等级差
        $lv_cha = $hero_senior_cfg['max_level'] - $hero_info['level'];

        $lv_cha = min($lv_cha,10);  //最大10级

        //是否达到爵位等级上限
        if ($lv_cha <= 0){
            Master::error(HERO_LEVEL_CAP);
        }

        for($i = 1; $i <= $lv_cha; $i++){
            //门客升级所需金币
            $hero_level_cfg = Game::getcfg_info('hero_level',$hero_info['level']);
            //当前等级所需金币
            $need_cost = $hero_level_cfg['cost'] - $hero_info['exp'];
            $need_cost = max($need_cost,0);

            //金币够不够
            if ($UserModel->info['coin'] >= $need_cost){

                $hero_info['level'] += 1;

                //够的话 直接升级 扣除金币
                $exp = 0;
            }else if ($UserModel->info['coin'] > 0){
                //不够的话 扣除剩余全部金币 加上对应经验值
                $need_cost = $UserModel->info['coin'];
                $exp += $need_cost;
            }else{
                continue;
            }
            //扣钱
            if($need_cost > 0){
                Master::sub_item($this->uid,KIND_ITEM,2,$need_cost);
            }
        }
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
        $Act6224Model->task_add(5,$lv_cha);

        return true;
    }
	
	/*
	 *门客封爵 
	 */
	public function upsenior($params){
		$HeroModel = Master::getHero($this->uid);
		$UserModel = Master::getUser($this->uid);
		
		//门客ID
		$HeroId = Game::intval($params,'id');
		//门客存在
		$hero_info = $HeroModel->check_info($HeroId);
		
		//爵位配置文件
		$hero_senior_cfg = Game::getcfg_info('hero_senior',$hero_info['senior']);
		
		//是否达到爵位等级上限
		if ($hero_senior_cfg['max_level'] > $hero_info['level']){
			Master::error(HERO_LEVEL_SHORT);
		}
		$guan_cfg = Game::getcfg_info('guan', $UserModel->info['level']);
		//是否达到爵位等级上限
		if ($hero_senior_cfg['player_level'] > $UserModel->info['level']){
			Master::error(HERO_LEVEL_REACH.$guan_cfg['name']);
		}
		//爵位上限
		if ($hero_info['senior'] >= count($hero_senior_cfg)){
			Master::error(HERO_LEVEL_FULL);
		}
		
		//提拔所需道具
		// if (empty($hero_senior_cfg['need'])){
		// 	Master::error('sys_err_hero_senior_cfg_need_null');
		// }
		
		//减去所需道具
//		foreach ($hero_senior_cfg['need'] as $itemid){
//			Master::sub_item($this->uid,KIND_ITEM,$itemid,1);
//		}


        for($i=0; $i<count($hero_senior_cfg['need']); $i++){
			Master::sub_item($this->uid,KIND_ITEM,$hero_senior_cfg['need'][$i], $hero_senior_cfg['need_count'][$i]);
        }

		//给予书籍经验
		$zzexp = $hero_senior_cfg['zzexp'];
		
		//更新门客
		$h_update = array(
			'heroid' => $HeroId,
			'senior' => $hero_info['senior']+1,
			'zzexp' => $zzexp,
		);
		$HeroModel->update($h_update);
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(6);

		//御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(17, 1);
		
		return true;
	}

	/*
	 * 门客资质技能升级  使用书籍经验升级
	 * id
	 * sid 升级的技能ID
	 * type 升级道具类型 1:书籍经验升级  2:卷轴升级 3:星级卷轴升级
	 */
	public function upzzskill($params){
		$HeroModel = Master::getHero($this->uid);
		
		//门客ID
		$HeroId = Game::intval($params,'id');
		//升级的资质技能ID
		$skillid = Game::intval($params,'sid');
		//升级道具类型
		$uptype = Game::intval($params,'type');
		
		//门客存在
		$hero_info = $HeroModel->check_info($HeroId);
		
		//数据提取
		$epskill = $hero_info['epskill'];//当前资质技能
		
		//升级的资质技能合法
		if (!isset($epskill[$skillid])){
			Master::error('sid_err_'.$skillid);
		}
		//爵位配置文件
		$hero_senior_cfg = Game::getcfg_info('hero_senior',$hero_info['senior']);
		if ($epskill[$skillid] >= $hero_senior_cfg['maxeplv']){
			Master::error(HERO_SKILL_FULL);
		}

		//资质技能性配置
		$epskill_cfg_info = Game::getcfg_info('hero_epskill',$skillid);
		//资质技能升级配置
		$epskill_level_cfg_info = Game::getcfg_info('hero_epskill_level',$epskill_cfg_info['star']);
		
		//当前爵位下 是否已经升满技能等级
		//maxelv
		
		//门客更新数组
		$h_update = array(
			'heroid' => $HeroId,
		);
		//等级是否增加
		$skill_uplv = 0;
		switch ($uptype){
			case 1:
				//所需书籍经验
				$need_zzexp = $epskill_level_cfg_info['exp'];

				//书籍经验是不是够
				if ($hero_info['zzexp'] < $need_zzexp){
					Master::error(HERO_BOOK_LEVEL_SHORT);
				}
				//减去经验值 加上等级
				$h_update['zzexp'] = -$need_zzexp;
				//等级增加标记
				$skill_uplv = 1;
				break;
			case 2:
				//技能属性 => 所需卷轴 配置
				$book_cfg = array(
					1 => 61,2 => 62,3 => 63,4 => 64 ,
				);
				//所需技能卷轴道具ID
				$need_bookid = $book_cfg[$epskill_cfg_info['ep']];

				//扣除道具
				Master::sub_item($this->uid,KIND_ITEM,$need_bookid,$epskill_level_cfg_info['quantity']);

				//获取失败次数  =>  伪概率
				$Act92Model = Master::getAct92($this->uid);
				$weiprob = $Act92Model->get_prob($HeroId,$skillid);

				//升级概率
				$up_pron_100 = $epskill_level_cfg_info['prob_100'];
				if (rand(1,100) <= $up_pron_100 + $weiprob ){
					//升级成功
					$skill_uplv = 1;
					$Act92Model->clear_prob($HeroId,$skillid);
				}else{
					//升级失败
					$Act92Model->add_prob($HeroId,$skillid);
					Master::back_s(2);
				}
				//主线任务
				$Act39Model = Master::getAct39($this->uid);
				$Act39Model->task_add(21,1);
				break;
			case 3:
				switch ($epskill_cfg_info['star']){
					case 1:
						$itemid = 84;
						break;
					case 2:
						$itemid = 85;
						break;
					case 3:
						$itemid = 86;
						break;
					case 4:
						$itemid = 147;
						break;
					case 5:
						$itemid = 148;
						break;
					case 6:
						$itemid = 149;
						break;
					default:
						Master::error(ITEMS_ERROR);
						break;
				}
				Master::sub_item($this->uid,KIND_ITEM,$itemid,1);
				$skill_uplv = 1;
				break;
			default:
				Master::error('type_err_'.$uptype);
				break;
		}
		
		//是否升级成功
		if ($skill_uplv > 0){
			$epskill[$skillid] = $epskill[$skillid]+1;
			$h_update['epskill'] = $epskill;
			
			Master::win_other($this->uid,'zz'.$epskill_cfg_info['ep'],$epskill_cfg_info['star'] * 0.2);
            //舞狮大会 - 提升伙伴资质等级
            $Act6224Model = Master::getAct6224($this->uid);
            $Act6224Model->task_add(2,1);
		}
		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(2,1);

		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(104, 1);

		$HeroModel->update($h_update);
		
		return true;
	}
	
	/*
	 * 门客PK技能升级
	 * * id
	 * sid 升级的技能ID
	 */
	public function uppkskill($params){
		$UserModel = Master::getUser($this->uid);
		$HeroModel = Master::getHero($this->uid);
		
		//门客ID
		$HeroId = Game::intval($params,'id');
		//升级的PK技能ID
		$skillid = Game::intval($params,'sid');
		
		//门客存在
		$hero_info = $HeroModel->check_info($HeroId);
		
		//数据提取
		$pkskill = $hero_info['pkskill'];//当前资质技能
		
		//升级的PK技能合法
		if (!isset($pkskill[$skillid])){
			Master::error('sid_err_'.$skillid);
		}
		
		//PK技能性配置
		$pkskill_cfg_info = Game::getcfg_info('hero_pkskill',$skillid);
		//PK技能升级配置
		$PKskill_level_cfg_info = Game::getcfg_info('hero_pkskill_level',$pkskill[$skillid]);
		
		//所需技能经验
		$need_pkexp = $PKskill_level_cfg_info['exp'];
			
		//技能经验是不是够
		if ($hero_info['pkexp'] < $need_pkexp){
			Master::error(HERO_SKILL_LEVEL_SHORT);
		}

		//技能升级
		
		$pkskill[$skillid] += 1;
		
		if($pkskill[$skillid] > $pkskill_cfg_info['maxLevel']){
		    Master::error(HERO_SKILL_LEVEL_FULL);
		}
		//门客更新数组
		$h_update = array(
			'heroid' => $HeroId,
			'pkexp' => -$need_pkexp,
			'pkskill' => $pkskill,
		);
		
		$HeroModel->update($h_update);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(25,1);
		return true;
	}
	
	
	/*
	 * 光环技能升级
	 *  id 门客id  sid 技能
	 * */
	public function upghskill($param) {
	    $HeroModel = Master::getHero($this->uid);
	    
	    //门客ID
	    $HeroId = Game::intval($param,'id');
	    
	    //升级的光环技能ID
	    $ghskillid = Game::intval($param,'sid');
	    
	    //门客存在
	    $hero_info = $HeroModel->check_info($HeroId);
	    
	    //数据提取
	    $ghskill = $hero_info['ghskill'];//当前资质技能
	    
	    //升级的光环技能合法
	    $pkskill_cfg_info = Game::getcfg_info('hero_pkskill',$ghskillid);
	    if (!isset($ghskill[$ghskillid]) || $pkskill_cfg_info['type']!=3 || empty($pkskill_cfg_info['up']) || empty($pkskill_cfg_info['upitem'])){
	        Master::error('sid_err_'.$ghskillid);
	    }
	    
	    //扣除升级需要的道具
	    $need_item = $pkskill_cfg_info['upitem'];
	    foreach ($need_item as $item){
	        $kind = empty($item['kind']) ? 1: $item['kind'];
	        Master::sub_item($this->uid, $kind, $item['id'], $item['count']);
	    }
	    
	    
	    //技能升级
	    if(empty($ghskill[$ghskillid])){
	        $ghskill[$ghskillid] = 0;
	    }
	    $ghskill[$ghskillid] += 1;
	    
	    if($ghskill[$ghskillid] > $pkskill_cfg_info['maxLevel']){
	        Master::error(HERO_SKILL_LEVEL_FULL);
	    }
	    
	    //门客更新数组
	    $h_update = array(
	        'heroid' => $HeroId,
	        'ghskill' => $ghskill,
	    );
	    
	    $HeroModel->update($h_update);
	    
	    //主线任务
	    $Act39Model = Master::getAct39($this->uid);
	    $Act39Model->task_add(25,1);
	    
	    return true;
	}

    /*
     * 送门客礼物
     *  id 门客id  sid 技能
     * */
    public function giveGift($param) {

        $HeroModel = Master::getHero($this->uid);
        //门客ID
        $HeroId = Game::intval($param,'id');

        //门客存在
        $hero_info = Game::getcfg_info('hero',$HeroId);
        if (empty($hero_info)){
            Master::error(PARAMS_ERROR.$HeroId);
		}
		
		$count = 0;
		$sumValue = 0;
		$items = Game::strval($param,'items');
		$itemArr = is_string($items) ?explode(';', $items):$items;
		for($i = 0; $i <count($itemArr); $i++){
			$item = is_string($itemArr[$i])?explode(',', $itemArr[$i]):$itemArr[$i];
			//扣除羁绊需要的道具
			$need_item=Game::getcfg_info('item',$item[0]);
			if(!empty($need_item['belong_hero']) && $need_item['belong_hero'][0] != $HeroId){
				Master::error(ITEMS_NOT_BELONG_HERO);
			}
			if ($need_item['type'][0]!='hero' && $need_item['type'][1]!='jiban'){
				Master::error(ITEMS_ERROR);
			}
			$kind = empty($need_item['kind']) ? 1: $need_item['kind'];
			Master::sub_item($this->uid, $kind, $need_item['id'],$item[1]);
			$count += $item[1];
			$sumValue += $need_item['type'][2]*$item[1];
		}
		$Act6001Model = Master::getAct6001($this->uid);
		$Act6001Model -> addHeroJB($HeroId, $sumValue);

		
        //主线任务 - 刷新
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(103, $count);

        return true;
    }

    /*
     * 伙伴闲谈
     *  id 伙伴id
     * */
    public function hchat($param) {

        $HeroModel = Master::getHero($this->uid);
        //伙伴ID
        $HeroId = Game::intval($param,'id');
        //伙伴存在
        $hero_info = $HeroModel->check_info($HeroId);
        $Act6138Model = Master::getAct6138($this->uid);
        $Act6138Model -> chat($HeroId,'hero');

    }

    /*
     * 领袖气质升级
     *
     * */
    public function upcharisma($param) {

        //伙伴ID
        $HeroId = Game::intval($param,'id');
        //门客存在
        $hero_info = Game::getcfg_info('hero',$HeroId);
        if (empty($hero_info['leaderid'])){
            Master::error(ITEMS_ERROR);
        }
        //升级
        $Act6219Model = Master::getAct6219($this->uid);
        $Act6219Model->leader_lv_up($hero_info['leaderid']);
    }

    /**
     *	改变伙伴时装
     */
	public function setClothe($params){
		//伙伴ID
		$heroId = Game::intval($params,"id");
        $dressId = Game::intval($params,"dressId");

        if ($dressId > 0) {
        	$dressInfo = Game::getcfg_info("hero_dress", $dressId);
	        $heroId = $dressInfo["heroid"];
        }

        //门客存在
        $hero_info = Game::getcfg_info('hero',$heroId);
        if (empty($hero_info)){
            Master::error(PARAMS_ERROR.$HeroId);
        }

        $Act6143Model = Master::getAct6143($this->uid);
		$Act6143Model->changeClothe($heroId, $dressId);
		
		$Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(101, 1);
	}

	/**
     *	改变伙伴空间背景
     */
	public function setBlanks($params){
		//伙伴ID
		$heroId = Game::intval($params,"id");
        $blankId = Game::intval($params,"blankid");

		$HeroModel = Master::getHero($this->uid);
		$heroInfo = $HeroModel->check_info($heroId);

        $Act6144Model = Master::getAct6144($this->uid);
		$Act6144Model->changeBlanks($heroId, $blankId);
		
	}

	/**
     *	领取羁绊解锁之后类型为3的奖励
     */
	public function pickJibanAward($params){
		//awardid
		$id = Game::intval($params,"id");

		$Act2004Model = Master::getAct2004($this->uid);
		$Act2004Model->pickAward($id);
		
	}

	/**
     *	购买hero商店中的道具
     */
	public function buyShopItem($params){
		//awardid
		$id = Game::intval($params,"id");

		$Act2005Model = Master::getAct2005($this->uid);
		$Act2005Model->buyItem($id);
		
	}

	/**
	 *  伙伴升星
	*/
	public function upStar($params) {
		$heroId = Game::intval($params,"heroId");
		$HeroModel = Master::getHero($this->uid);
        //伙伴ID合法
		$hero_info = $HeroModel->check_info($heroId);
		$heroCfg = Game::getcfg_info('hero',$heroId);
		if (empty($heroCfg)){
			Master::error(PARAMS_ERROR.'hero'.$heroId);
		}
		$currentStar = $hero_info['star'];
		if ($hero_info['star'] <= 0){
			$hero_info['star'] = $heroCfg['initStar'];
			$currentStar = $hero_info['star'];
		}
		if ($currentStar >= $heroCfg['maxStar']){
			Master::error(HERO_STAR_MAX);
		}
		$heroStarCfg = Game::getcfg_info("hero_star",$currentStar+1);
		if (empty($heroStarCfg)){
			Master::error(PARAMS_ERROR.'upStar'.$currentStar);
		}
		foreach ($heroStarCfg['cost'] as $v){
            Master::sub_item2($v);
		}
		$h_update = array(
	        'heroid' => $heroId,
	        'star' => $currentStar+1,
	    );
	    //主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(106, 1);
		$taskType = 132 + $heroId;
		$Act39Model->task_add($taskType,1);
		
		$HeroModel->update($h_update);
		return true;
	}

	/**
	 *  伙伴信物激活
	*/
	public function tokenActivation($params) {
		$heroId = Game::intval($params,"heroId");
		$HeroModel = Master::getHero($this->uid);
		$hero_info = $HeroModel->check_info($heroId);
		$tokenId = Game::intval($params,"tokenId");

		$Act2001Model = Master::getAct2001($this->uid);
		$Act2001Model->tokenActivation($heroId,$tokenId);
		// $Act2001Model->back_data();

		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(1);

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(123,1);
		$tasktype = 139 + $heroId;
		$Act39Model->task_add($tasktype,1);
    
		return true;
	}


	/**
	 *  伙伴信物升级
	*/
	public function tokenUpLv($params) {
		$heroId = Game::intval($params,"heroId");
		$HeroModel = Master::getHero($this->uid);
		$hero_info = $HeroModel->check_info($heroId);
		$tokenId = Game::intval($params,"tokenId");

		$Act2001Model = Master::getAct2001($this->uid);
		$Act2001Model->mailUpLevel($heroId,$tokenId);
		// $Act2001Model->back_data();

		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(1);

		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(105, 1);
    
		return true;
	}

	/**
	 *  伙伴羁绊激活
	*/
	public function fetterActivation($params) {
		$heroId = Game::intval($params,"heroId");
		$HeroModel = Master::getHero($this->uid);
		$hero_info = $HeroModel->check_info($heroId);
		$fetterId = Game::intval($params,"fetterId");

		$Act2002Model = Master::getAct2002($this->uid);
		$Act2002Model->tokenFetter($heroId,$fetterId);
		// $Act2002Model->back_data();

		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(1);
    
		return true;
	}
	
	/**
	 *  伙伴随机拜访
	*/
	public function randVisit() {

		$HeroModel = Master::getHero($this->uid);
        //随机一位知己
		$heroId=array_rand($HeroModel->info);
		
		$type = rand(1,3);

		$Act725Model = Master::getAct725($this->uid);
		$Act725Model->startGame($type,$heroId,false);
		
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(17,1);
		
		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(3,1);
    
		return true;
	}

	/**
	 *  伙伴定向拜访
	*/
	public function visit($params) {
		$type = Game::intval($params,"type");
		$heroId = Game::intval($params,"heroId");
		$HeroModel = Master::getHero($this->uid);
		$hero_info = $HeroModel->check_info($heroId);

		$Act725Model = Master::getAct725($this->uid);
		$Act725Model->startGame($type,$heroId,true);

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(17,1);
		
		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(3,1);
	}

	/**
	 *  伙伴选择答案
	*/
	public function chooseAnswer($params) {
		$id = Game::intval($params,"id");
		$index = Game::intval($params,"index");

		$Act726Model = Master::getAct726($this->uid);
		$Act726Model->chooseAnswer($id,$index);
	}

	
	/**
	 *  游戏结束
	*/
	public function endGame() {

		$Act726Model = Master::getAct726($this->uid);
		$Act726Model->endGame();
	}

	/*
	 * 出游
	 * id:伙伴ID
	 */
	public function xxoo($params){
		//伙伴ID
		$heroId = Game::intval($params,'id');
        $herotravel=$this->_xxoogetbaby($heroId,true);
        //出游弹窗
        Master::back_custom_data('hero','travel',$herotravel);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(53,1);
        //限时-知己出游次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6173',1);
        //重新构造阵法 刷新伙伴数量标签
        $TeamModel = Master::getTeam($this->uid);
		// $TeamModel->reset(2);
		$TeamModel->reset(1);
        //重新构造阵法 刷新子嗣数量标签
        if ($herotravel['type'] == 1)$TeamModel->reset(3);
	}

	/*
     * 随机出游
     */
    public function sjcy(){
        //扣除假期
        $Act6131Model = Master::getAct6131($this->uid);
        $Act6131Model->apao();
        $HeroModel = Master::getHero($this->uid);
        //随机一位知己
        $heroId=array_rand($HeroModel->info);
        $herotravel = $this->_xxoogetbaby($heroId,false);
        //出游弹窗
        Master::back_custom_data('hero','travel',$herotravel);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(53,1);
        //限时-知己出游次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6173',1);

        //刷新知己缓存
        $TeamModel = Master::getTeam($this->uid);
		// $TeamModel->reset(2);
		$TeamModel->reset(1);
        //刷新徒弟缓存
        if ($herotravel['type'] == 1)$TeamModel->reset(3);
	}

	  /*
     * 一键出游
     */
    public function yjxxoogetbaby(){
    	//vip等级解锁
    	$UserModel = Master::getUser($this->uid);
    	// wenhou_vip_level 
    	// 策划:一键出游问候统一使用 wenhou_vip_level
    	$vipLimit = Game::getcfg_param('wenhou_vip_level');
    	if($UserModel->info['vip'] < $vipLimit){
    		Master::error(CHUYOU_VIP_NOT_REACH);
    	}
        //扣除全部假期 返回点数
        $Act6131Model = Master::getAct6131($this->uid);
        //目前总的出游次数
        $totalCount = $Act6131Model->qunp();

        $HeroModel = Master::getHero($this->uid);

        $yjwin = array();
        for($i = 0 ; $i < $totalCount ; $i++){
            //随机一个伙伴
            $heroId = array_rand($HeroModel->info);
            //宠幸
            $yjwin[] = $this->_xxoogetbaby($heroId);
        }
        //过滤不是徒弟的
        foreach ($yjwin as $k => $v){
            if ($v['type'] != 1){
                unset($yjwin[$k]);
            }
        }
        sort($yjwin);
        Master::$bak_data['a']["hero"]['win']['yjtravel'] = $yjwin;
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(53,$totalCount);
        //限时-知己出游次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6173',$totalCount);
        //刷新伙伴缓存
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(1);
        //刷新徒弟缓存
        if (!empty($yjwin))$TeamModel->reset(3);

        return true;
    }
	
	/*
	 * 问候
	 */
	public function xxoonobaby($params){
        //伙伴ID
        $heroId = Game::intval($params,'id');

        $Herohello=$this->_xxoonobaby($heroId,true);

        //问候弹窗
        Master::back_custom_data('hero','hello',$Herohello);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(17,1);
        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
		// $TeamModel->reset(2);
		$TeamModel->reset(3);
        return true;
    }

    /*
	 * 随机问候
	 */
    public function sjxo(){
        //扣除精力
        $Act11Model = Master::getAct11($this->uid);
        $Act11Model->apao();
        //伙伴ID
        $HeroModel = Master::getHero($this->uid);
        $heroId = array_rand($HeroModel->info);
        $herohello=$this->_xxoonobaby($heroId);

        $Act11Model = Master::getAct11($this->uid);
        $Act11Model->make_out();
        //问候弹窗
        Master::back_custom_data('hero','hello',$herohello);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(17,1);

        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
		// $TeamModel->reset(2);
		$TeamModel->reset(3);
        return true;
   }

   /*
     * 一键问候
     */
    public function yjxo(){

    	$UserModel = Master::getUser($this->uid);
    	// wenhou_vip_level 
    	// 策划:一键出游问候统一使用 wenhou_vip_level
    	$vipLimit = Game::getcfg_param('wenhou_vip_level');
    	if($UserModel->info['vip'] < $vipLimit){
    		Master::error(WENHOU_VIP_NOT_REACH);
    	}
        //扣除全部精力 返回点数
        $Act11Model = Master::getAct11($this->uid);
        //目前总的问候
        $totalCount = $Act11Model->qunp();

        $HeroModel = Master::getHero($this->uid);

        $yjwin = array();
        for($i = 0 ; $i < $totalCount ; $i++){
            //随机一个伙伴
            $heroId = array_rand($HeroModel->info);
            //问候之后获得的所有奖励
            $yjwin[] = $this->_xxoonobaby($heroId);
        }
        Master::$bak_data['a']["hero"]['win']['yjhello'] = $yjwin;
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(17,$totalCount);

        //重新构造阵法 刷新伙伴数量标签
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(1);

        return true;
    }

   	/*
	 * 恢复精力
	 */
	public function weige($params){
		
		//扣除全部精力 返回点数
		$Act11Model = Master::getAct11($this->uid);
		$Act11Model->huifu();
		//限时-精力丹消耗
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6172',1);
		
		return true;
	}

	/*
     * 恢复假期
     */
    public function hfjiaqi($params){
        //消耗数量
        $num = Game::intval($params,'num');
        if ($num < 1){
            Master::error(PARAMS_ERROR);
        }
        $Act6131Model = Master::getAct6131($this->uid);
        $Act6131Model->huifu($num);

        return true;
    }

   /*
	 * 内部函数
	 * 问候某个知己
	 * 返回 问候信息
	 * 参数 知己ID , 是否需要元宝 , 是否增加亲密度
	 */
    private function _xxoonobaby($heroId , $needCash = false , $addLove = 1){
        $HeroModel = Master::getHero($this->uid);
        //知己ID合法
        $hero_info = $HeroModel->check_info($heroId);
		//伙伴羁绊值
		$Act6001Model = Master::getAct6001($this->uid);	
		$fetterValue = $Act6001Model->getHeroJB($heroId);
        //如果要钱
        if($needCash){
            //所需元宝
            $_need_cash = Game::getCfg_formula()->hero_meet_cost($fetterValue);
            Master::sub_item($this->uid,KIND_ITEM,1,$_need_cash);
        }
        $shenji = 0;
    	//神迹

        // $w_update = array(
        //     'heroid' => $heroId,
        //     'love' => $addLove,
        // );
		// $HeroModel->update($w_update);
		$Act6001Model->addHeroJB($heroId,$addLove);

        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(3,1);

        //限时-问候知己次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6174',1);
        //舞狮大会 - 随机问候知己
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(3,1);
        return array(
            'heroid' => $heroId,
            // 'love' => $addLove,
            'isgad' => $shenji,
            'pro' => 0,
            'type' => 0,
        );

    }

    /*
	 * 内部函数
	 * 出游某个知己
	 * 返回 出游信息
	 * 参数 知己ID , 是否需要元宝 ,是否需要收徒
	 */
    private function _xxoogetbaby($heroId , $needCash = false){
        $HeroModel = Master::getHero($this->uid);
        //伙伴ID合法
		$hero_info = $HeroModel->check_info($heroId);
		//伙伴羁绊值
		$Act6001Model = Master::getAct6001($this->uid);	
		$fetterValue = $Act6001Model->getHeroJB($heroId);
        //如果要钱
        if($needCash){
            $need_cash = Game::getCfg_formula()->hero_chuyou_cost($fetterValue);
            Master::sub_item($this->uid,KIND_ITEM,1,$need_cash);
        }

        //获取配置数据
        $hero_chuyou_cfgs = Game::getcfg('wife_chuyou');
        //对应档位数据
        $hero_chuyou=array();
        for ($i = 1;$i<=count($hero_chuyou_cfgs);$i++){
			$currentCharm = $hero_chuyou_cfgs[$i]['charm'];
			$nextCharm = $hero_chuyou_cfgs[$i+1]['charm'];
			if ($fetterValue >= $currentCharm && $fetterValue <= $nextCharm){
                $hero_chuyou = $hero_chuyou_cfgs[$i];break;
            }
        }
        $pupil_pro = $hero_chuyou['pupil_pro'];//徒弟概率
        $item_pro = $hero_chuyou['item_pro'];//物品概率
        $item_id = $hero_chuyou['item_id'];//获得的物品
        $rands = rand(1,10000);
        //当前未科举徒弟数量
        $team = Master::get_team($this->uid);
        //徒弟席位
        $Act12Model = Master::getAct12($this->uid);
        $seat = $Act12Model->get_seat();
        //如果席位不足
        if ($seat <= $team['smson']){
            $rands = rand($pupil_pro,10000);
        }
        //记录第一次收徒
        $Act90Model = Master::getAct90($this->uid);
        $isborn = $Act90Model->do_save();
        //第一次出游必得徒弟
        if ($isborn){
            $rands = rand(1,$pupil_pro);
        }

        if ($rands <= $pupil_pro){
            $tmp = 1;
        }elseif ($rands <= $item_pro){
            $tmp = 0;
        }else{
            $tmp = 2;
        }
        // $h_update = array(
        //     'heroid' => $heroId,
        //     'love' => 5,
        // );
		// $HeroModel->update($h_update);
		$Act6001Model->addHeroJB($heroId,5);

        //根据heroid随机某个剧情
        $Act6131Model = Master::getAct6131($this->uid);
        $storyInfo = $Act6131Model->randStoryId($heroId);

        //舞狮大会 - 与知己出游次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(15,1);

        switch ($tmp) {//0:物品 1:获得徒弟 2:资源
            case 0:
                $item_id= explode('|',$item_id);

                $addItems = array();
                if(!empty($item_id)){
                    foreach ($item_id as $val){

                        $addItems[] = array('id'=>$val,'kind'=>1,'count'=>1);

                    }
                }
                $num = array_rand($addItems,1);
                if(!empty($addItems)){
                    Master::add_item2($addItems[$num]);//加奖励
                    return array(
                        "type" => $tmp,
                        "itemid" => $addItems[$num]['id'],
                        "itemcount" => $addItems[$num]['count'],
                        "heroid" => $heroId,
                        "storyId" => $storyInfo['storyId'],
                        "isRand" => $storyInfo['isRand'],
                    );
                }

            case 1:
                //收徒
                $SonModel = Master::getSon($this->uid);
                $sonuid = $SonModel->addSon($heroId);
                $son_info = $SonModel->check_info($sonuid);
                return array(
                    "type" => $tmp,
                    "babyid" => $sonuid,
                    "babysex" => $son_info['sex'],
                    "heroid" => $heroId,
                    "storyId" => $storyInfo['storyId'],
                    "isRand" => $storyInfo['isRand'],
                );
            case 2:
                //资源
                $res = json_decode($hero_chuyou['res'],1);
                $res = $res[array_rand($res,1)];
                if(!empty($res)){
                    Master::add_item2($res);//加奖励
                    return array(
                        "type" => $tmp,
                        "itemid" => $res['id'],
                        "itemcount" => $res['count'],
                        "heroid" => $heroId,
                        "storyId" => $storyInfo['storyId'],
                        "isRand" => $storyInfo['isRand'],
                    );
                }

        }

    }

}
