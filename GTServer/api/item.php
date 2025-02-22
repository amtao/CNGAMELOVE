<?php
//使用道具
class itemMod extends Base
{
	/*
	 * 使用道具
	 * 资源类型道具
	 * id:道具id
	 * count:使用数量
	 */
	public function useitem($params){
		//道具ID
		$item_id = Game::intval($params,'id');
		//数量
		$count = Game::intval($params,'count');

		//道具配置
		$itemcfg_info = Game::getcfg_info('item',$item_id);
		if ($itemcfg_info['type'][0] != 'item'){
			Master::error(ITEMS_ERROR);
		}

		//限制 每次最多使用100个道具
		$count = min($count,100);

		//特别道具类型只能使用1个
		if($itemcfg_info['type'][1] == 'base' && in_array($itemcfg_info['type'][4],array(7,8))){//暂时只有7 以后有的话再加
			$count = 1;
		}
		if($itemcfg_info['type'][1] == 'wifeadd'){
			$count = 1;
		}
		
		//数量合法
		if ($count <= 0){
			Master::error('num_err_'.$count);
		}
		
		//减去使用的道具
		Master::sub_item($this->uid,KIND_ITEM,$item_id,$count);
		
		switch($itemcfg_info['type'][1]){
			case 'base'://单种道具
				$kind = isset($itemcfg_info['type'][4]) ? $itemcfg_info['type'][4] : KIND_ITEM;

				switch ($kind){//对红颜门客等进行单独处理 有点坑
					case 7:
						$HeroModel = Master::getHero($this->uid);
						if(isset($HeroModel->info[$itemcfg_info['type'][2]])){
							Master::error(HERO_HAVEED);
						}
						break;
					case 8:
						$WifeModel = Master::getWife($this->uid);
						if(isset($WifeModel->info[$itemcfg_info['type'][2]])){
							Master::error(WIFE_ALREADY_OWNED);
						}
						break;
					default:
						break;
				}

				Master::add_item($this->uid,$kind,$itemcfg_info['type'][2],$itemcfg_info['type'][3]*$count);
				break;
			case 'list'://道具列表
				foreach ($itemcfg_info['type'][2] as $itdata){
					Master::add_item($this->uid,KIND_ITEM,$itdata['id'],$itdata['num']*$count);
				}
				break;
			case 'sjitem'://随机一个道具
				$allitems = array();
				for($i=0 ; $i < $count ; $i++){
					$add_itemid = $itemcfg_info['type'][2][array_rand($itemcfg_info['type'][2],1)];
					if(empty($allitems[$add_itemid])){
						$allitems[$add_itemid] = 0;
					}
					$allitems[$add_itemid] ++;
				}
				foreach($allitems as $ak => $av){
					Master::add_item($this->uid,KIND_ITEM,$ak,$av);
				}
				break;
			case 'baibao': //百宝箱随机道具
				$allitems = array();
				for($i=0 ; $i < $count ; $i++){
					$rk = Game::get_rand_key(100,$itemcfg_info['type'][2],'prob');
					$team = Master::get_team($this->uid);
					switch($itemcfg_info['type'][2][$rk]['id']){
						case 2:
						case 5:
						case 10:
							$epid = rand(2,4);
			                $bcount = $team['allep'][$epid]*$itemcfg_info['type'][2][$rk]['id'];
							if(empty($allitems[$epid])){
								$allitems[$epid] = 0;
							}
							$allitems[$epid] += $bcount;
							break;
						case 999:
							if(empty($allitems[77])){
								$allitems[77] = 0;
							}
							$allitems[77] += 1;
							break;
					}
				}
				foreach($allitems as $ak => $av){
					Master::add_item($this->uid,KIND_ITEM,$ak,$av);
				}
				break;
			case 'drop': //礼包掉落
				$allitems = array();
				for($i=0 ; $i < $count ; $i++){
					$rk = Game::get_rand_key(100,$itemcfg_info['type'][2],'prob');
					if(!empty($itemcfg_info['type'][2][$rk])){
						
						$aitemid = $itemcfg_info['type'][2][$rk]['id'];
						$acount = $itemcfg_info['type'][2][$rk]['count'];
						if(empty($allitems[$aitemid])){
							$allitems[$aitemid] = 0;
						}
						$allitems[$aitemid] += $acount;
					}
				}
				foreach($allitems as $ai => $ac){
					Master::add_item($this->uid,KIND_ITEM,$ai,$ac);
				}
				break;
			case 'probvalue'://黑市随机数量
				$allitems = array();
				for($i=0 ; $i < $count ; $i++){
					$rk = Game::get_rand_key(100,$itemcfg_info['type'][2],'prob');
					if(!empty($itemcfg_info['type'][2][$rk])){
						$aitemid = $itemcfg_info['type'][2][$rk]['id'];
						$acount = $itemcfg_info['type'][2][$rk]['count'];
						Master::add_item($this->uid,KIND_ITEM,$aitemid,$acount);
					}
				}
				break;
			case 'sjhero'://随机门客加技能经验或书籍经验
				$HeroModel = Master::getHero($this->uid);
				//存放所有门客数据
				$allheros = array();
				$hkind = intval($itemcfg_info['type'][2]);
				$itemArr = array();
				for($i=0 ; $i < $count ; $i++){
					//获取随机一只门客id
					$hid = $HeroModel->get_one_hero();
					if (is_array($itemcfg_info['type'][3])){
                        $rk = Game::get_rand_key(100,$itemcfg_info['type'][3],'prob');
                        $hcount = $itemcfg_info['type'][3][$rk]['count'];  //随机值
                    }else{
                        $hcount = $itemcfg_info['type'][3];
                    }
					if(empty($itemArr[$hid])){
						$itemArr[$hid] = 0;
					}
					$itemArr[$hid] += $hcount;
				}
				foreach($itemArr as $heroid => $sumCount){
					Master::add_item($this->uid,$hkind,$heroid,$sumCount);
				}
				break;
			case 'sjwifeexp'://随机红颜获得指定经验值  弃用 使用sjwife
				$WifeModel = Master::getWife($this->uid);
				for($i=0 ; $i < $count ; $i++){
					//随机一个红颜  / 无红颜报错
					$wife_id = $WifeModel->get_one_wife();
					//增加指定经验值
					Master::add_item($this->uid,12,$wife_id,$itemcfg_info['type'][2]);
				}
				break;
			case 'wifeadd'://新增指定红颜

				$WifeModel = Master::getWife($this->uid);
				$wifeid = $itemcfg_info['type'][2];
				if($WifeModel->info[$wifeid]){
					$num = empty($itemcfg_info['type'][3]) ? 1 : $itemcfg_info['type'][3];
					Master::add_item($this->uid,4,$wifeid,$num);
					return true;
				}

				Master::add_item($this->uid,8,$itemcfg_info['type'][2],1);
				break;
            case 'heroadd'://新增指定门客
                Master::add_item($this->uid,7,$itemcfg_info['type'][2],1);
                break;
            case 'changjing'://新增场景
                $Act74Model = Master::getAct74($this->uid);
                $Act74Model->add($itemcfg_info['type'][2],$itemcfg_info['type'][3] * $count);
                $Act74Model->back_data_a();
                break;
			case 'sjwife'://随机红颜 属性加成 $itemcfg_info['type'][2]  3红颜亲密 2红颜魅力 12红颜经验
				$WifeModel = Master::getWife($this->uid);
				for($i=0 ; $i < $count ; $i++){
					//随机一个红颜  / 无红颜报错
					$wife_id = $WifeModel->get_one_wife();
					$kind = $itemcfg_info['type'][2];
					$rk = Game::get_rand_key(100,$itemcfg_info['type'][3],'prob');
					$wcount = $itemcfg_info['type'][3][$rk]['count'];  //随机值
					Master::add_item($this->uid,$kind,$wife_id,$wcount);
				}
				break;
            case 'sjdebris'://随机拼图碎片
                //获取用户uid最后一个数字
                $num = str_split($this->uid);
                $number = $num[count($num) - 1];
                //获取小概率
                $prob = $itemcfg_info['type'][2];
                $few = $itemcfg_info['type'][4][$number];//小概率获取物品
                $lot = array_diff($itemcfg_info['type'][3],$few);//用总物品和小概率物品差异获得大概率获取物品
                for($i=0 ; $i < $count ; $i++){
                    $rand = rand(1,10000);
                    if ($rand < $prob){//小概率
                        $k = array_rand($few,1);
                        $item = $few[$k];
                    }else{//大概率
                        $k = array_rand($lot,1);
                        $item = $lot[$k];
                    }
                    Master::add_item($this->uid,KIND_HUODONG,$item,1);
                }
                break;
			case 'user':
				/**
				 * $itemcfg_info['type'][2]  2:粮食 3:士兵 4:银两
				 * $itemcfg_info['type'][3] 倍数
				 */
				if(!in_array($itemcfg_info['type'][2],array(2,3,4))){
					Master::error(ITEMS_USE_ERROR);
				}
				$typeId = $itemcfg_info['type'][2];
				$Act1Model = Master::getAct1($this->uid);
				$res = $Act1Model->get_onetime_Num($typeId) + 1000;
				$res = $itemcfg_info['type'][3] * $count * $res;
                Master::add_item($this->uid,1, $typeId, $res);
				break;
			case 'week':
				$day = Game::getcfg_param('week_card_time');
				$Act68Model = Master::getAct68($this->uid);
				$weekid = 4;
				if(empty($Act68Model->info[$weekid])){
					$Act68Model->info[$weekid] = array(
						'daytime' => 0,
						'rwdtime' => 0,
					);
				}
				if (Game::is_over($Act68Model->info[$weekid]['daytime'])){
					//如果过期 从今天开始加上
					$Act68Model->info[$weekid]['daytime'] = Game::day_0() + $count* $day * 86400;
				}else{
					//未过期 时间加上
					$Act68Model->info[$weekid]['daytime'] += $count* $day * 86400;
				}
				$Act68Model->info[$weekid]['retime'] = Game::day_0();
				$Act68Model->save();
				break;
			case 'sjshuxing':
				$HeroModel = Master::getHero($this->uid);
				$heroid = $HeroModel->get_one_hero();
				if($itemcfg_info['type'][2] == 5){
					$yao = array();
					for($i=0 ; $i < $count ; $i++){
						$r_id = rand(1,4);//随机属性
						$yao[$r_id] = empty($yao[$r_id])?1:$yao[$r_id]+1;
					}
					//分类嗑药
					foreach($yao as $yk => $yv){
						$epstr = 'e'.$yk;
						$h_update = array(
							'heroid' => $heroid,
							$epstr => $itemcfg_info['type'][3] * $yv,
						);
						$HeroModel->update($h_update);
						Master::win_other($this->uid,$epstr,$itemcfg_info['type'][3] * $yv);
					}
				}
				break;
			default:
				Master::error(ITEMS_USE_ERROR);
				break;
		}
		
		return true;
	}
	/*
	 * 使用道具
	 * 指定门客类型道具
	 * id:道具ID
	 * count:使用数量
	 * hero:门客ID
	 */
	public function useforhero($params){
		//道具ID
		$item_id = Game::intval($params,'id');
		//数量
		$count = Game::intval($params,'count');
		if ($count <= 0){
			Master::error('num_err_'.$count);
		}
		//门客ID
		$heroid = Game::intval($params,'heroid');
		
		$HeroModel = Master::getHero($this->uid);
		//门客存在
		$hero_info = $HeroModel->check_info($heroid);

		//根据羁绊值 判断数量是否达到改星级最大
		//之前根据星级判断 目前取消掉
		$Act2000Model = Master::getAct2000($this->uid);
		$Act2000Model->checkIsEnough($heroid,$item_id,$count);
		
		//减去使用的道具
		Master::sub_item($this->uid,KIND_ITEM,$item_id,$count);
		
		$Act2000Model->addUseInfo($heroid,$item_id,$count);
		$Act2000Model->back_data();
		
		//道具配置
		$itemcfg_info = Game::getcfg_info('item',$item_id);
		
		if ($itemcfg_info['type'][0] != 'hero'){
			Master::error(ITEMS_TYPE_ERROR,$itemcfg_info['type'][0]);
		}

		//$count
		switch($itemcfg_info['type'][1]){
			case 'ep'://嗑药
				if($itemcfg_info['type'][2] != 5){
					$epstr = 'e'.$itemcfg_info['type'][2];
					$h_update = array(
						'heroid' => $heroid,
						$epstr => $itemcfg_info['type'][3] * $count,
					);
					$HeroModel->update($h_update);
					Master::win_other($this->uid,$epstr,$itemcfg_info['type'][3] * $count);
				}else{
					//分类嗑药
					$yao = array();
					for($i=0 ; $i < $count ; $i++){
						$r_id = rand(1,4);//随机属性
						$yao[$r_id] = empty($yao[$r_id])?1:$yao[$r_id]+1;
					}
					//分类嗑药
					foreach($yao as $yk => $yv){
						$epstr = 'e'.$yk;
						$h_update = array(
							'heroid' => $heroid,
							$epstr => $itemcfg_info['type'][3] * $yv,
						);
						$HeroModel->update($h_update);
						Master::win_other($this->uid,$epstr,$itemcfg_info['type'][3] * $yv);
					}
				}
				break;
			case 'pkexp'://技能经验书
				$h_update = array(
					'heroid' => $heroid,
					'pkexp' => $itemcfg_info['type'][2] * $count,
				);
				$HeroModel->update($h_update);
				Master::win_other($this->uid,'pkexp',$itemcfg_info['type'][2] * $count);
				break;
			case 'zzexp'://书籍经验书
				$h_update = array(
					'heroid' => $heroid,
					'zzexp' => $itemcfg_info['type'][2] * $count,
				);
				$HeroModel->update($h_update);
				Master::win_other($this->uid,'zzexp',$itemcfg_info['type'][2] * $count);
				break;
			//case 'zzlv'://书籍提升 PASS 作为功能道具
				//break;
			default:
				Master::error(ITEMS_USE_HERO_ERROR);
				break;
		}
		$Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(102, 1);
			
		return true;
	}
	
	
	/*
	 * 道具合成
	 * id:道具id
	 */
	public function hecheng($params){
		$id = Game::intval($params,'id');
		$count = Game::intval($params, 'count');
		$Act14Model = Master::getAct14($this->uid);
		$Act14Model->hecheng($id, $count);
	}
	/*
	 * 道具列表
	 * */
	public function itemlist() {
	    //道具列表
	    $ItemModel = Master::getItem($this->uid);
	    $ItemModel->getBase();
	}
	
}
