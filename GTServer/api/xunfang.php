<?php
//排行榜
class XunfangMod extends Base
{
    
    /*
     * 寻访
     * */
    public function xunfan($params){
        $type = Game::intval($params,'type');
        //用户信息
        $UserModel = Master::getUser($this->uid);
        if( $type && $UserModel->info['vip'] < 2 && $type < 100){
            Master::error(LOOK_FOR_VIP_LEVEL_SHORT);
        }
        
        //寻访
        $Act26Model = Master::getAct26($this->uid);
        //寻访-赈灾-转运
        $Act27Model = Master::getAct27($this->uid);
        
        $Act28Model = Master::getAct28($this->uid);
        //自动赈灾
        $Act28Model->auto_zhenzai($Act27Model->info['auto2'],$Act27Model->info['auto3'],$Act27Model->info['ysSet']);
        //当前要扣除的体力
        if(empty($Act26Model->info['num'])){
            Master::error(LOOK_FOR_POWER_SHORT);
        }
        $num = $type == 1 ? $Act26Model->info['num']:1;
        $build = $type > 100?$type%100:0;
        
        if($Act27Model->info['num'] <1){
            Master::error(LOOK_FOR_FATE_SHORT);
        }
        $add_wife =array();
        for($i = 1; $i <= $num; $i ++){
            //扣运势
            $Act27Model->sub_ys(1);
            //扣体力
            $Act26Model->sub_num(1, $build);
            //自动赈灾
            $Act28Model->auto_zhenzai($Act27Model->info['auto2'],$Act27Model->info['auto3'],$Act27Model->info['ysSet']);
            //触发寻访事件
            //当前运势
            $Act27Model->info['num'];
            //(1)  幸运值触发概率事件
            $cfg_xf_lucky = Game::getcfg('xf_lucky');
            $lucky_info = array();
            foreach($cfg_xf_lucky as $k => $v){
                if($Act27Model->info['num'] <= $k){
                    $lucky_info = $v;
                    break;
                }
            }
            if(empty($lucky_info)){
                Master::error(LOOK_FOR_FATE_SHORT);
            }
            //获取事件id
            $rid = Game::get_rand_key1($lucky_info['event_prob_100']);
            if ($build != 0){
                $rid = 9;
            }

//            $rid = 10;

            //寻访-触发事件信息
            $cfg_xf_event = Game::getcfg('xf_event');
            $Act29Model = Master::getAct29($this->uid);
            $npcid = 0;
            $id = 0;
            $win_type = 0;//0:没人 1加道具 2减道具 3加好感 4获得红颜 5加亲密 6增加门客羁绊 7特殊事件
            $items = array();//道具列表
            $haogan = 0;//红颜好感度
            //事件触发   (0:+,1：红颜，2+-，3-)
            $specId = $Act29Model->getSpecId();
            if($specId != 0){  //第一次寻访
	    		$npcid = $specId;
                $win_type = 7;
                $Act39Model = Master::getAct39($this->uid);
                $Act39Model->task_refresh(49);
            }
	    	else {
                switch($rid){
                case 1 :  //0.1*属性+1000
                case 2 :  //0.2*属性+3000
                case 3 :  //1*属性+5000
                case 4 :  //1*属性+10000
                case 7 :  // 失去资源
                    //随机 银两  粮草  士兵
                    $epid = rand(2,4);
                    //获取对应属性值
                    $TeamModel  = Master::getTeam($this->uid);
                    $e = $TeamModel->info['allep'][$epid]; //
                    $type = eval("return ".$cfg_xf_event[$rid]['event'].";");
                        
                    //获取 变化的数值
                    $value =  floor($type);
                    //数值对应的npcid
                    if($rid == 7){
                        $win_type = 2;
                        $npcid = $Act29Model->xf_sub();
        
                        switch($epid){
                            case 2:
                                $max_value = $UserModel->info['coin'];
                                break;
                            case 3:
                                $max_value = $UserModel->info['food'];
                                break;
                            case 4:
                                $max_value = $UserModel->info['army'];
                                break;
                        }
                        $value = min($value,$max_value);
                        Master::sub_item($this->uid,KIND_ITEM,$epid,$value);
                    }else{
                        $win_type = 1;
                        $npcid = $Act29Model->xf_add();
                        //添加道具
                        Master::add_item($this->uid,KIND_ITEM,$epid,$value);
                    }

                    //返回结构体
                    $items[] = array(
                        'id' => $epid,
                        'count' => $value,
                    );
                        
                    break;
                        
                case 5 :  //(红颜)好感+1
                    $Act29Model = Master::getAct29($this->uid);
                    //获得NPCid
                    $npcid = $Act29Model->xf_wifi();
                    $npc_info = Game::getcfg_info("xf_NPC", $npcid);

                    //获得红颜id
                    $wifeId = $npc_info['wfid'];
                    //判断该红颜是不是已获得  如果是,获得亲密度 +1
                    //红颜ID合法
                    $WifeModel = Master::getWife($this->uid);
                    $wife_info = $WifeModel->check_info($wifeId,1);
                    if(!empty($wife_info)){ //已获得
                        $win_type = 5;
                        //获得亲密度 +1
                        $w_update = array(
                            'wifeid' => $wifeId,
                            'love' => 1,
                        );
                        $WifeModel->update($w_update);
                    }else{ //如果未获得
                        //当前好感度
                        $haogan = $Act29Model->xf_get_haogan($wifeId);
                        // 概率获得
                        if(rand(1,100) <= $haogan*5 && !in_array($wifeId, $add_wife)){
                            //加红颜
                            Master::add_item($this->uid,KIND_WIFE,$wifeId);
                            $add_wife[] = $wifeId;
                            $win_type = 4;
                        }else{//未获得 ,  加好感度
                            $win_type = 3;
                        }
                    }
                    $Act29Model->xf_add_haogan($wifeId,1);
                    $haogan = $Act29Model->xf_get_haogan($wifeId);  
                    break;
                        
                case 6 :  //一无所获
                    //$win_type = 0;
                    $npcid = $Act29Model->xf_add();
                    break;
                case 8:  //触发门客加羁绊
                    $db = $Act29Model->xf_servant();
                    $npcid = $db['id'];
                    $Act6001Model = Master::getAct6001($this->uid);
                    $Act6001Model -> addHeroJB($db['id'], 1);
                    $win_type = 6;
                    break;
                case 9:
                    $npcid = $Act29Model->xf_sp_servant($build);
//                    $npcid = 220;
                    $id = $this->getSpEventId($npcid, $build);
                    $win_type = 7;
                    break;
                case 10:
                    $npcid = $Act29Model->xf_sp_wife($build);
//                    $npcid = 1000;
                    $id = $this->getSpEventId($npcid, $build);
                    $win_type = 7;
                    break;
                case 11:
                    $npcid = $Act29Model->xf_sp_servant_char($build);
                    $id = $this->getSpEventId($npcid, $build);
                    $win_type = 7;
                    break;
                case 12:
                    $npcid = $Act29Model->xf_sp_servant_type($build);
//                    $npcid = 1710;
                    $id = $this->getSpEventId($npcid, $build);
                    $win_type = 7;
                    break;
                }
            }
            $Act29Model->saveLastNpc($npcid, $id);
            //构造特殊返回弹窗
            $back_win = array(
                'npcid' => $npcid,
                'type' => $win_type,
                'haogan' => $haogan,
                'items' => $items,
                'build'=>$build,
                'id' => $id
            );
            $xf_win[] = $back_win;
        }
        
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(23,$num);
        
        
        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(4,$num);

        //活动293 - 获得骰子-寻访次数
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(3,$num);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(3,$num);
		//限时-出城寻访次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6177',1);

        //国力庆典
        $Act6207Model = Master::getAct6207($this->uid);
        $Act6207Model->add($num);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(6, $num);

        //舞狮大会 - 出城寻访
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(4,$num);
        
        Master::$bak_data['a']['xunfang']['win']['xfAll'] = $xf_win;
    }

    /*
     * 获取特殊id
     * */
    private function getSpEventId($npcid, $build = 0){
        if ($npcid == 0)return 0;
    	$event = Game::getcfg_info('xf_clientevent', $npcid);
    	if (empty($event))return 0;
    	if (($event['type'] == 1 || $event['type'] == 2) && $event['object'] != 0)return $event['object'];
    	if ($event['locale'] != 0 || $build != 0){
    		$build = Game::getcfg_info('xf_build', $build != 0?$build:$event['locale']);
    	}
    	else {
    		$userModel = Master::getUser($this->uid);
			$bmap = $userModel->info['bmap'];
			$xf_builds = Game::getcfg('xf_build');
			foreach($xf_builds as $k => $v){
				if ($v['lock'] >= $bmap){
					$build = $v;
					continue;
				}
				$build = $v;
				break;
			}
    	}
    	if (empty($build))return 0;
    	$type = $event['type'];
    	$param = $event['object'];
    	switch ($type) {
    		case 1:
    			$ss = explode('|', $build['chenzi']);
    			return count($ss) < 2?intval($ss[0]):intval($ss[rand(0, count($ss)-1)]);
    		case 2:
    			$ss = explode('|', $build['wife']);
    			return count($ss) < 2?intval($ss[0]):intval($ss[rand(0, count($ss)-1)]);
    		case 4:
    			$ss = explode('|', $build['chenzi']);
    			$heorIds = array();		      
                $HeroModel = Master::getHero($this->uid);
                $Act6001Model = Master::getAct6001($this->uid);
		        foreach ($ss as $id){
		        	if (intval($id) == 0){
		        		continue;
		        	}
		            $heroData = Game::getcfg_info("hero", intval($id));
		            $spec = $heroData["spec"];
		            if ($spec[0] == $param || $spec[0] == 5 || $spec[0] == 6 || (count($spec) > 1 && $spec[1] == $param)){
                        if ($Act6001Model->getHeroJB($id) != 0 || 
                            $HeroModel->check_info($id, true)){
                            $heorIds[intval($id)] = intval($id);
                        }		                
		            }
		        }            
		        return array_rand($heorIds);
		    case 3:
		    	$ss = explode('|', $build['chenzi']);
    			$heorIds = array();
                $HeroModel = Master::getHero($this->uid);
                $Act6001Model = Master::getAct6001($this->uid);
            	foreach ($ss as $id){
            		if (intval($id) == 0){
            			continue;
            		}
            		$heroData = Game::getcfg_info("hero", intval($id));
                	$dis = $heroData["disposition"];
                	if ($dis == $param){
                        if ($Act6001Model->getHeroJB($id) != 0 || 
                            $HeroModel->check_info($id, true)){
                            $heorIds[intval($id)] = intval($id);
                        }
                	}
           		}            
            	return array_rand($heorIds);        
       	}
       	return 0;
    }

    /*
     * 运势
     * */
    public function yunshi($params){
        $auto2 = Game::intval($params,'auto2');
        $auto3 = Game::intval($params,'auto3');
        $ysSet = Game::intval($params,'ysSet');
        if($ysSet > 90){
            Master::error("xunfang_ysSet_err_".$ysSet);
        }
        
        //寻访-赈灾-转运
        $Act27Model = Master::getAct27($this->uid);
        
        //设置
        $Act27Model->set_auto2($auto2);
        $Act27Model->set_ys($ysSet);
        $Act27Model->set_auto3($auto3);
    }
	/**
	 * 寻访
	 * @param unknown_type $params 
	 * {"type":0:不是一键寻访  1:一键寻访}
	 * {"auto2":0:自动银两赈灾未设置  1:自动银两赈灾已设置}
	 * {"auto3":0:自动粮草赈灾未设置  1:自动粮草赈灾已设置}
	 * {"ysSet":运势设置}
	 */
	public function xunfang($params){
		$type = Game::intval($params,'type');
		$auto2 = Game::intval($params,'auto2');
		$auto3 = Game::intval($params,'auto3');
		$ysSet = Game::intval($params,'ysSet');
		
		if($ysSet > 90){
			Master::error("xunfang_ysSet_err_".$ysSet);
		}
		//用户信息
		$UserModel = Master::getUser($this->uid);
		if( $type && $UserModel->info['vip'] < 2 ){
			Master::error(LOOK_FOR_VIP_LEVEL_SHORT);
		}
		
		//寻访
		$Act26Model = Master::getAct26($this->uid);
		//寻访-赈灾-转运
		$Act27Model = Master::getAct27($this->uid);
		//寻访-赈灾-转运
		$Act28Model = Master::getAct28($this->uid);
		//当前要扣除的体力
		if(empty($Act26Model->info['num'])){
			Master::error(LOOK_FOR_POWER_SHORT);
		}
		$num = $type == 1 ? $Act26Model->info['num']:1;
		
		//设置
		$Act27Model->set_auto2($auto2);
		$Act27Model->set_ys($ysSet);
		$Act27Model->set_auto3($auto3);
		if($Act27Model->info['num'] <1){
		    Master::error(LOOK_FOR_FATE_SHORT);
		}
		$add_wife =array();
		for($i = 1; $i <= $num; $i ++){
			
			//一次自动赈灾
			$Act28Model->auto_zhenzai($auto2,$auto3,$ysSet);
			
			//扣运势
			$Act27Model->sub_ys(2);
			//扣体力
			$Act26Model->sub_num(1);
			
			//触发寻访事件
			//当前运势
			$Act27Model->info['num'];
			//(1)  幸运值触发概率事件
			$cfg_xf_lucky = Game::getcfg('xf_lucky');
			$lucky_info = array();
			foreach($cfg_xf_lucky as $k => $v){
				if($Act27Model->info['num'] <= $k){
					$lucky_info = $v;
					break;
				}
			}
			if(empty($lucky_info)){
				Master::error(LOOK_FOR_FATE_SHORT);
			}
			//获取事件id
			$rid = Game::get_rand_key(100,$lucky_info['event_prob_100']);
			//寻访-触发事件信息
			$cfg_xf_event = Game::getcfg('xf_event');
			$Act29Model = Master::getAct29($this->uid);
			$npcid = 0;
			$win_type = 0;//0:没人 1加道具 2减道具 3加好感 4获得红颜 5加亲密
			$items = array();//道具列表
			$haogan = 0;//红颜好感度
			//事件触发   (0:+,1：红颜，2+-，3-)
			
			switch($rid){
				case 1 :  //0.1*属性+1000
				case 2 :  //0.2*属性+3000
				case 3 :  //1*属性+5000
				case 4 :  //1*属性+10000
				case 7 :  // 失去资源
					//随机 银两  粮草  士兵
					$epid = rand(2,4);
					//获取对应属性值
					$TeamModel  = Master::getTeam($this->uid);
					$e = $TeamModel->info['allep'][$epid]; //
					$type = eval("return ".$cfg_xf_event[$rid]['event'].";");
					
					//获取 变化的数值
			        $value =  floor($type); 
					//数值对应的npcid
					if($rid == 7){
						$win_type = 2;
						$npcid = $Act29Model->xf_sub();
						
						switch($epid){
							case 2:
								$max_value = $UserModel->info['coin'];
								break;
							case 3:
								$max_value = $UserModel->info['food'];
								break;
							case 4:
								$max_value = $UserModel->info['army'];
								break;
						}
						$value = min($value,$max_value);
						Master::sub_item($this->uid,KIND_ITEM,$epid,$value);
					}else{
						$win_type = 1;
						$npcid = $Act29Model->xf_add();
						//添加道具
			       		Master::add_item($this->uid,KIND_ITEM,$epid,$value);
					}
			       
			        //返回结构体
					$items[] = array(
						'id' => $epid,
						'count' => $value,
					);
					
					break;
					
				case 5 :  //(红颜)好感+1
					$Act29Model = Master::getAct29($this->uid);
					$npc_info = $Act29Model->xf_wifi();
					//获得NPCid
					$npcid = $npc_info['id'];
					//获得红颜id
					$wifeId = $npc_info['wfid'];
					//判断该红颜是不是已获得  如果是,获得亲密度 +1
					//红颜ID合法
					$WifeModel = Master::getWife($this->uid);
					$wife_info = $WifeModel->check_info($wifeId,1);
					if(!empty($wife_info)){ //已获得
						$win_type = 5;
						//获得亲密度 +1
						$w_update = array(
							'wifeid' => $wifeId,
							'love' => 1,
						);
						$WifeModel->update($w_update);
					}else{ //如果未获得
				        //当前好感度
				        $haogan = $Act29Model->xf_get_haogan($wifeId);
				        // 概率获得
				        if(rand(1,100) <= $haogan*5 && !in_array($wifeId, $add_wife)){
				            //加红颜
				            Master::add_item($this->uid,KIND_WIFE,$wifeId);
				            $add_wife[] = $wifeId;
				            $win_type = 4;
				        }else{//未获得 ,  加好感度
				            $Act29Model->xf_add_haogan($wifeId,1);
				            $win_type = 3;
				        }
					}
					
					break;
					
				case 6 :  //一无所获
					//$win_type = 0;
					$npcid = $Act29Model->xf_add();
					break;
					
			}
			//构造特殊返回弹窗
			$back_win = array(
				'npcid' => $npcid,
				'type' => $win_type,
				'haogan' => $haogan,
				'items' => $items,
			);
			$xf_win[] = $back_win;
		}
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(23,$num);
		
		
	   //日常任务
	//    $Act35Model = Master::getAct35($this->uid);
	//    $Act35Model->do_act(4,$num);

        //活动293 - 获得骰子-寻访次数
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(3,$num);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(3,$num);

        //国力庆典
        $Act6207Model = Master::getAct6207($this->uid);
        $Act6207Model->add($num);
	   
		Master::$bak_data['a']['xunfang']['win']['xfAll'] = $xf_win;
	}
	
	/**
	 * 恢复体力
	 * @param unknown_type $params 
	 */
	public function recover($params){
		$count = Game::intval($params,'type');
        $count = empty($count)?1:$count;
		//寻访
		$Act26Model = Master::getAct26($this->uid);
		if($Act26Model->info['num'] > 0){
			$Act26Model->back_data();
			return;
		}
		//用户信息 -- 消耗体力丹
		Master::sub_item($this->uid,KIND_ITEM,72,$count);
		//加体力
		$Act26Model->add_num($count);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(24,1);
	}
	
	/**
	 * 手动赈灾
	 * @param unknown_type $params  {"type":1:元宝 2:银两 3:粮草}
	 */
	public function zzHand($params){
		//"type":1:元宝 2:银两 3:粮草
		$type = Game::intval($params,'type');
		
		//寻访-赈灾-转运
		$Act28Model = Master::getAct28($this->uid);
		$Act27Model = Master::getAct27($this->uid);
		$yunsi = 0;
		switch($type){
			case 3:
				if($Act27Model->info['num'] >= 100 ){
					Master::error(LOOK_FOR_FATE_FULL);
				}
				$Act28Model->zhuanyun();
				$yunsi = 10;
				break;
			case 2:
			case 1:
				if($Act27Model->info['num'] >= 90 ){
					Master::error(LOOK_FOR_FATE_GT_MAX);
				}
				$Act28Model->zhenzai($type+1);
				$yunsi = 2;
				break;
			default:
			   Master::error("xunfang_zzHand_err_".$type);
		}
		//添加返回信息
		Master::back_win('xunfang','yunsi','ys',$yunsi);
	}
	
	
}









