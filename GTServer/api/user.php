<?php

class userMod extends Base
{		
	/*
	 * 改名字
	 */
	public function resetName($params)
	{
	    $sname = Game::intval($params,'name');
        $type = Game::intval($params,'type');
	
	    $UserModel = Master::getUser($this->uid);
	    //非法字符判定
		$sname = Game::filter_char($sname,0);
	    //敏感字符判定
	    $sname = Game::str_mingan($sname);
	    //非法字符判定
	    $sname = Game::str_feifa($sname);
	    //名字长度判定
	    $len = Common::utf8_strlen($sname);
	    if ($len  < 2 ||$len > 5){
	        Master::error(USER_COUNT_SHORT_NAME);
	    }
	    
	    //检查重名
	    Game::chick_name($this->uid,$sname);
        if ($type==1){
            Master::sub_item($this->uid,KIND_ITEM,1,100);
        }else{
            Master::sub_item($this->uid,KIND_ITEM,115,1);
        }

	
	    $s_update = array(
	        'name' => $sname,
	    );
	    $UserModel->update($s_update);
	}
	
	/*
	 * 改头像
	 */
	public function resetImage($params)
	{
	    //性别
	    $sex = Game::intval($params,"sex");
	    $sex = empty($sex)?2:$sex;
	    //头像
	    $job = Game::intval($params,'job');

        $UserModel = Master::getUser($this->uid);
        if ($job == $UserModel->info['job'])return;

	    //性别合法
	    if (!in_array($sex,array(1,2))){
	        Master::error("sex_err_".$sex);
	    }
	    //头像合法
        $sys_job = Game::getcfg("clothe_job");
	    if (empty($sys_job[$job])){
	        Master::error(COMMON_DATA_ERROR);
		}
		$IsOk = false;
		$allJob = json_decode($UserModel->info['allJob'],true);
		foreach($allJob as $value){
			if($value == $job){
				$IsOk=true;
				break;
			}
		}
		if(!$IsOk){
			return;
		}
		
	    // $d = $sys_job[$job];
	    // $cost = $d['cost'];
		// Master::sub_item($this->uid,KIND_ITEM, $cost["itemid"], $cost["count"]);
		$TeamModel = Master::getTeam($UserModel->uid);
		$TeamModel->reset(4);

	    $s_update = array(
	        'sex' => $sex,
			'job' =>  $job,
		);
		$TeamModel->back_all_ep();//输出总属性
		$UserModel->update($s_update);
	}
	
	/*
	 * 买头像
	 */
	public function buyImage($params)
	{
	    //性别
	    $sex = Game::intval($params,"sex");
	    $sex = empty($sex)?2:$sex;
	    //头像
	    $job = Game::intval($params,'job');

        $UserModel = Master::getUser($this->uid);
        if ($job == $UserModel->info['job'])return;

	    //性别合法
	    if (!in_array($sex,array(1,2))){
	        Master::error("sex_err_".$sex);
	    }
	    //头像合法
        $sys_job = Game::getcfg("clothe_job");
	    if (empty($sys_job[$job])){
	        Master::error(COMMON_DATA_ERROR);
		}
		$IsOk = false;
		$allJob = json_decode($UserModel->info['allJob'],true);
		foreach($allJob as $value){
			if($value == $job){
				$IsOk=true;
				break;
			}
		}
		if($IsOk){
			return;
		}else{
			$d = $sys_job[$job];
			$cost = $d['cost'];
			Master::sub_item($this->uid,KIND_ITEM, $cost["itemid"], $cost["count"]);

			array_push($allJob,$job);
		}
		$allJob = json_encode($allJob,JSON_UNESCAPED_UNICODE);

	
	    $s_update = array(
	        'sex' => $sex,
			'job' =>  $UserModel->info['job'],
			'allJob' => $allJob,
		);
		$UserModel->update($s_update);
		$TeamModel = Master::getTeam($UserModel->uid);
		$TeamModel->reset(4);
		$TeamModel->back_all_ep();//输出总属性
	}

	
	
	/*
	 * 经营 商业 农业 招兵
	 */
	public function jingYing($params){
		//经营类型
		$id = Game::intval($params,'jyid');
		//活动1 资源经营
		$Act1Model = Master::getAct1($this->uid);
		//执行经营
		$Act1Model->jingying($id);
				
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add($id-1,1);

		$Act39Model->task_add(117, 1);
		
		//日常任务
		$Act35Model = Master::getAct35($this->uid);
		$Act35Model->jytype($id,1);

		return true;
	}
	
	/*
	 * 勤政爱民
	 */
	public function qzam($params){
		//接口皮限制
		//
		
		//勤政爱民
		$Act31Model = Master::getAct31($this->uid);
		$Act31Model->rwd();
		
		//加上奖励
		//获取阵法信息
		$team = Master::get_team($this->uid);
		//加上对应道具
		Master::add_item($this->uid,KIND_ITEM,2,$team['allep'][2] * 8);//几倍金币
		Master::add_item($this->uid,KIND_ITEM,3,$team['allep'][3] * 8);//几倍粮草
		Master::add_item($this->uid,KIND_ITEM,4,$team['allep'][4] * 8);//几倍士兵
		return;
	}

	public function weipai($params){
        //
        $type =  Game::intval($params,'type');
        $heroId1 =  Game::intval($params,'heroId1');
        $heroId2 =  Game::intval($params,'heroId2');
        $heroId3 =  Game::intval($params,'heroId3');

        $act6003Model = Master::getAct6003($this->uid);
        $act6003Model->replaceHero($type, $heroId1, $heroId2, $heroId3);
    }
	
	/*
	 * 使用征收令
	 */
	public function jingYingLing($params){
		//经营类型
		$id = Game::intval($params,'jyid');
		//使用道具数量
		$num = Game::intval($params,'num');
		
		//活动1 资源经营
		$Act1Model = Master::getAct1($this->uid);
		$Act1Model->add_time($id,$num);
	}
	
	/*
	 * 一键征收
	 */
	public function jingYingAll($params){
		//活动1 资源经营
		$Act1Model = Master::getAct1($this->uid);
		
		//3种资源经营
		$ids = array(2,3,4);
		
		$times = 0;
		foreach ($ids as $id){
		    $time = 0;
			//执行全部征收
			$time = $Act1Model->jingying($id,1);
			$times +=$time;
			
			//主线任务
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add($id-1,$time);
			
			//日常任务
			$Act35Model = Master::getAct35($this->uid);
			$Act35Model->jytype($id,$time);

		}

		if ($times <= 0){
			Master::error(OPERATE_NUM_SHORT);
		}
	}
	
	/*
	 * 处理政务
	 * (修改日常之后该接口废弃)
	 * 参数 act 1贪污 2清白
	 */
	public function zhengWu($params){
		//处理类型
		$type = Game::intval($params,'act');
		
		//活动2 政务处理
		$Act2Model = Master::getAct2($this->uid);
		//减去政务次数
		$outf = $Act2Model->outf;//暂存奖励数据
		$Act2Model->sun_time(1);
		
		//政务选择
		if ($type == 1 && $outf['itemid'] != 0){
			//加上道具
			Master::add_item($this->uid,KIND_ITEM,$outf['itemid'],$outf['count']);
		}else if ($type == 2){
			//获得官阶配置
			$UserModel = Master::getUser($this->uid);
			$guan_cfg_info = Game::getCfg_info('guan',$UserModel->info['level']);
			$exp = $guan_cfg_info['zw_exp'];
			//加上政绩
			Master::add_item($this->uid,KIND_ITEM,5,$exp);
		}else if ($type == 3){
			
		}


        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(12,1);

        //限时活动-处理政务
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong207',1);

        //日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(9,1);

        //活动293 - 获得骰子-处理政务
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(2,1);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(2,1);

        //国力庆典
        $Act6206Model = Master::getAct6206($this->uid);
        $Act6206Model->add(1);

        //舞狮大会 - 办差处理日常
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(9,1);

	}
	
	/*
	 * 使用政务令
	 * (修改日常之后该接口废弃)
	 */
	public function zhengWuLing($params){
		$num = Game::intval($params,'num');
		
		if ($num <= 0){
			Master::error("num_err".$num);
		}
		//活动2 政务处理
		$Act2Model = Master::getAct2($this->uid);
        Master::sub_item($this->uid,KIND_ITEM,121,$num);
		$Act2Model->add_time($num);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(13,$num);
	}
	
	/*
	 * 添加门客
	 */
	public function add_hero($params){
		return;
		$UserModel = Master::getUser($this->uid);
		$HeroModel = Master::getHero($this->uid);
		$HeroModel->add_hero($params[0]);
		
	}
	
	/*
	 * 添加红颜
	 */
	public function add_wife($params){
		return;
		$UserModel = Master::getUser($this->uid);
		$WifeModel = Master::getWife($this->uid);
		$WifeModel->add_wife($params[0]);
		return 1;
	}
	
	//pve前置 扣除名声
	public function pve(){
		/*$UserModel = Master::getUser($this->uid);
		$hit_smap = $UserModel->info['smap']+1;
		$smap_cfg = Game::getcfg_info('pve_smap',$hit_smap,"已经通关");
		if (Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_GT_BMAP);
		}
		//还有没有小兵
		if ($UserModel->info['army'] <= 0){
			Master::error(GAME_LEVER_NO_SOLDIER,4);
		}
		$map_army = $smap_cfg['army'] - $UserModel->info['mkill'];
		//将要打的关卡信息
		// $smap_cfg['ep1'];//武力
		// $smap_cfg['army'];//兵力
		//获取我的阵法属性
		$team = Master::get_team($this->uid);
		//我的武力值
		$wuli = $team['allep'][1];
		$wuli = $wuli == 0?1:$wuli;
		$need_army = round($map_army * $smap_cfg['ep1'] / $wuli);
		if($smap_cfg['bmap'] <= 10){
			$need_army = round($map_army/2 +  $map_army/2* $smap_cfg['ep1'] / $wuli);
		}
		$need_army = $need_army > 0 ? $need_army : 1;
		if($UserModel->info['army'] < $need_army){
			Master::error(GAME_LEVER_NO_SOLDIER);
		}
		//扣除兵力
		Master::sub_item($this->uid,KIND_ITEM,4,$need_army);

		Master::back_win('user','pvewin','deil',$need_army);
		Master::back_win('user','pvewin','pvewin',$need_army);*/
		$Act765Model = Master::getAct765($this->uid);
		$Act765Model->pve();
	}
	/*
	 * 打地图
	 */
	public function pveRestraint($params){
		$cardId = Game::intval($params,'cardId');
		$Act765Model = Master::getAct765($this->uid);
		$Act765Model->fight($cardId);
		/*$UserModel = Master::getUser($this->uid);
		//id 2 3 4克制关系
		$epId = Game::intval($params,"id");
		if($epId == 0){
			$epId = rand(2,4);
		}
		
		/*
		$UserModel->info['bmap']; //大关ID 已经到达的
		//$UserModel->info['mmap']; //中关ID 已经到达的 //中关ID 暂时无用
		$UserModel->info['smap']; //小关ID 已经打过的
		$UserModel->info['mkill']; //剩余兵李/BOSS血量
		
		
		//如果将要打的小关的大关ID 大于本大关的ID 则认为打到了BOSS
		$hit_smap = $UserModel->info['smap']+1;

		$smap_cfg = Game::getcfg_info('pve_smap',$hit_smap,"已经通关");
	
		//判定大小关逻辑修改
		if (Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_GT_BMAP);
		}
	
		//获取我的阵法属性
		$team = Master::get_team($this->uid);
		/*
		* 根据配置表判断是否克制
		* true克制  系数param  pve_restraint
		* false被克制 pve_be_restrained
		$epIsBig = false;
		//获取我的对应属性
		$mineEp = $team['cardep'][$epId];
		if($epId == $smap_cfg['jisuan_number'][0]){
			if($mineEp > $smap_cfg['jisuan_number'][1]){
				$epIsBig = true;
			}
		}else{
			if(Master::checkRestraint($epId,$smap_cfg['jisuan_number'][0])){
				//克制
				$rate = Game::getcfg_param("pve_restraint");
			}else{
				//被克制
				$rate = Game::getcfg_param("pve_be_restrained");
			}
			$totalEp = floor($mineEp*($rate/10));
			if($totalEp > $smap_cfg['jisuan_number'][1]){
				$epIsBig = true;	
			}
		}

		//将要打的关卡信息
		// $smap_cfg['ep1'];//武力
		// $smap_cfg['army'];//兵力
		$cfg_rwd = array ( //过关奖励  配置数据太大,这边写死
		  array ( 'itemid' => 2,  'type' => '$e2*0.12497+1000', ),
		  array ( 'itemid' => 5, 'count' => 5, ),
		);
		
		//当前关卡剩下多少兵力 (减去已经干掉的)
		$map_army = $smap_cfg['army'] - $UserModel->info['mkill'];
		
	
		//我的武力值
		$wuli = $team['allep'][1];
		$wuli = $wuli == 0?1:$wuli;
		//$wuli = 100;
		
		//杀光这些小兵需要多少兵力
		// $need_army = round($map_army * $smap_cfg['ep1'] / $wuli);
		// if($smap_cfg['bmap'] <= 10){
		// 	$need_army = round($map_army/2 +  $map_army/2* $smap_cfg['ep1'] / $wuli);
		// }
        // $need_army = $need_army > 0 ? $need_army : 1;
		
		$u_update = array();

		//弹窗
		$pvewin = array();
		
		//兵力是否够
		$win = 1;//胜利
		if ($epIsBig){
			//足够 胜利 标示这一关已经打过
			$u_update['smap'] = $hit_smap;//关卡ID更新
			$u_update['mkill'] = 0;//已击溃清0
			
			//胜利弹窗
			Master::back_win('user','pvewin','kill',$map_army);

			//奖励倍数
            $beishu = Game::pv_beishu('pve');

			//加上过关奖励
			foreach ($cfg_rwd as $rv){
				//构造数量
				$item = Game::auto_count($rv,$team['allep']);
                $item['count'] = $item['count'] == 5?$item['count']:ceil($item['count'] * $beishu);
				Master::add_item2($item,'user','pvewin');
			}
			//通过一个中关卡获得的额外奖励
			if( !empty($smap_cfg['rwd_prob_100']) ){
				$extra = $smap_cfg['rwd_prob_100'];
				$rk = Game::get_rand_key(10000,$extra,'prob_10000');
				if(!empty($extra[$rk])){
				    $count = $extra[$rk]['itemid'] == 5?$extra[$rk]['count']:ceil($extra[$rk]['count']* $beishu);
					$item = array(
						'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
						'itemid' => $extra[$rk]['itemid'],
						'count' => $count,
					);
					Master::add_item2($item,'user','pvewin');
				}
			}
			
			//日常任务
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(154,1);
			
			//更新关卡排行
			$Redis2Model = Master::getRedis2();
			$Redis2Model->zAdd($this->uid,$UserModel->info['bmap'] + $u_update['smap'] - 1);

			//关卡冲榜
			$HuodongModel = Master::getHuodong($this->uid);
	   		$HuodongModel->chongbang_huodong('huodong251',$this->uid,1);

	   		//小关卡流水
            Game::cmd_flow(17, 1, 1, $hit_smap);

            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::copy($UserModel->info['platform'], $this->uid, $hit_smap, '地图小关');
		}else{
			$win = 0;//失败
			
			//杀光这些小兵需要多少兵力
			$mkill = round($UserModel->info['army']/ ($smap_cfg['ep1'] / $wuli));
			if($smap_cfg['bmap'] <= 10){
				$mkill = round($UserModel->info['army']/2 +  $UserModel->info['army']/2* $smap_cfg['ep1'] / $wuli);
			}
			$all_mkill = $UserModel->info['mkill'] + $mkill;//已击溃增加
			if($smap_cfg['army'] <= $all_mkill){
				$all_mkill = floor( $smap_cfg['army'] -  $map_army * 0.9 );
			}
			
			$u_update['mkill'] = $all_mkill;//已击溃增加
			// $need_army = $UserModel->info['army'];
			
			//失败弹窗
			Master::back_s(2);
			Master::back_win('user','pvewin','kill',$mkill);
			$Act750Mdoel = Master::getAct750($this->uid);
			$Act750Mdoel->setIsPop(1,1);
			$Act750Mdoel->setIsPop(5,1);
		}
		$UserModel->update($u_update);
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(7);
		
		// Master::back_win('user','pvewin','deil',$need_army);
		// Master::back_win('user','pvewin','pvewin',$need_army);
		*/
	}

	public function pvenew($params){
		$UserModel = Master::getUser($this->uid);
		
		/*
		$UserModel->info['bmap']; //大关ID 已经到达的
		//$UserModel->info['mmap']; //中关ID 已经到达的 //中关ID 暂时无用
		$UserModel->info['smap']; //小关ID 已经打过的
		$UserModel->info['mkill']; //剩余兵李/BOSS血量
		*/
		
		
		//如果将要打的小关的大关ID 大于本大关的ID 则认为打到了BOSS
		$hit_smap = $UserModel->info['smap']+1;

		$smap_cfg = Game::getcfg_info('pve_smap',$hit_smap,"已经通关");
		/*
		if ($smap_cfg['bmap'] > $UserModel->info['bmap']){
			Master::error(GAME_LEVER_GT_BMAP);
		}*/
		//判定大小关逻辑修改
		if (Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_GT_BMAP);
		}
		
		//还有没有小兵
		if ($UserModel->info['army'] <= 0){
			Master::error(GAME_LEVER_NO_SOLDIER,4);
		}
		
		//将要打的关卡信息
		$smap_cfg['ep1'];//武力
		$smap_cfg['army'];//兵力
		$cfg_rwd = array ( //过关奖励  配置数据太大,这边写死
		  array ( 'itemid' => 2,  'type' => '$e2*0.12497+1000', ),
		  array ( 'itemid' => 5, 'count' => 5, ),
		);
		
		//当前关卡剩下多少兵力 (减去已经干掉的)
		$map_army = $smap_cfg['army'] - $UserModel->info['mkill'];
		
		//获取我的阵法属性
		$team = Master::get_team($this->uid);
		//我的武力值
		$wuli = $team['allep'][1];
		$wuli = $wuli == 0?1:$wuli;
		//$wuli = 100;
		
		//杀光这些小兵需要多少兵力
		$need_army = round($map_army * $smap_cfg['ep1'] / $wuli);
		if($smap_cfg['bmap'] <= 10){
			$need_army = round($map_army/2 +  $map_army/2* $smap_cfg['ep1'] / $wuli);
		}
        $need_army = $need_army > 0 ? $need_army : 1;
		
		$u_update = array();

		//弹窗
		$pvewin = array();
		
		//兵力是否够
		$win = 1;//胜利
		if ($UserModel->info['army'] >= $need_army){
			//足够 胜利 标示这一关已经打过
			$u_update['smap'] = $hit_smap;//关卡ID更新
			$u_update['mkill'] = 0;//已击溃清0
			
			//胜利弹窗
			Master::back_win('user','pvewin','kill',$map_army);

			//奖励倍数
            $beishu = Game::pv_beishu('pve');

			//加上过关奖励
			foreach ($cfg_rwd as $rv){
				//构造数量
				$item = Game::auto_count($rv,$team['allep']);
                $item['count'] = $item['count'] == 5?$item['count']:ceil($item['count'] * $beishu);
				Master::add_item2($item,'user','pvewin');
			}
			//通过一个中关卡获得的额外奖励
			if( !empty($smap_cfg['rwd_prob_100']) ){
				$extra = $smap_cfg['rwd_prob_100'];
				$rk = Game::get_rand_key(10000,$extra,'prob_10000');
				if(!empty($extra[$rk])){
				    $count = $extra[$rk]['itemid'] == 5?$extra[$rk]['count']:ceil($extra[$rk]['count']* $beishu);
					$item = array(
						'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
						'itemid' => $extra[$rk]['itemid'],
						'count' => $count,
					);
					Master::add_item2($item,'user','pvewin');
				}
			}

			$pveNum = $UserModel->info['bmap'] + $u_update['smap'] - 1;
			//成就更新
			// $Act36Model = Master::getAct36($this->uid);
			// $Act36Model->set(5,$pveNum);

			//日常任务
			// $Act35Model = Master::getAct35($this->uid);
			// $Act35Model->do_act(1,1);
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(154,1);

			//更新关卡排行
			$Redis2Model = Master::getRedis2();
			$Redis2Model->zAdd($this->uid,$pveNum);

			//关卡冲榜
			$HuodongModel = Master::getHuodong($this->uid);
	   		$HuodongModel->chongbang_huodong('huodong251',$this->uid,1);

	   		//小关卡流水
            Game::cmd_flow(17, 1, 1, $hit_smap);

            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::copy($UserModel->info['platform'], $this->uid, $hit_smap, '地图小关');
		}else{
			$win = 0;//失败
			
			//杀光这些小兵需要多少兵力
			$mkill = round($UserModel->info['army']/ ($smap_cfg['ep1'] / $wuli));
			if($smap_cfg['bmap'] <= 10){
				$mkill = round($UserModel->info['army']/2 +  $UserModel->info['army']/2* $smap_cfg['ep1'] / $wuli);
			}
			$all_mkill = $UserModel->info['mkill'] + $mkill;//已击溃增加
			if($smap_cfg['army'] <= $all_mkill){
				$all_mkill = floor( $smap_cfg['army'] -  $map_army * 0.9 );
			}
			
			$u_update['mkill'] = $all_mkill;//已击溃增加
			// $need_army = $UserModel->info['army'];//小兵耗光
			
			//失败弹窗
			Master::back_s(2);
			Master::back_win('user','pvewin','kill',$mkill);
		}
		$UserModel->update($u_update);
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(7);
		//扣除兵力
		Master::sub_item($this->uid,KIND_ITEM,4,$need_army);
		
		Master::back_win('user','pvewin','deil',$need_army);
	}
	
	/*
	 * 打BOSS
	 */
	public function pvb($params){
		$UserModel = Master::getUser($this->uid);
		/*
		$UserModel->info['bmap']; //大关ID 已经到达的
		//$UserModel->info['mmap']; //中关ID 已经到达的 //中关ID 暂时无用
		$UserModel->info['smap']; //小关ID 已经打过的
		$UserModel->info['mkill']; //剩余兵量/BOSS血量
		*/
		
		//如果将要打的小关的大关ID 大于本大关的ID 则认为打到了BOSS
		/*
		$hit_smap = $UserModel->info['smap']+1;
		$smap_cfg = Game::getcfg_info('pve_smap',$hit_smap);
		if ($smap_cfg['bmap'] <= $UserModel->info['bmap']){
			Master::error(GAME_LEVER_LT_BMAP);
		}
		*/
		//判定大小关逻辑修改
		if (!Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_LT_BMAP);
		}

        //门客出战列表
        $Act3Model = Master::getAct3($this->uid);

		//出战门客ID
		$hero_id = Game::intval($params,'id');
		if ($hero_id == 0){
		    if ($Act3Model->reset(true)){
                $u_update = array(
                    'mkill' => 0,
                );

                $UserModel->update($u_update);
            }
            return;
        }
		
		$HeroModel = Master::getHero($this->uid);
		//门客存在
		$hero_info = $HeroModel->check_info($hero_id);
		
		//BOSS配置
		Master::back_win('user','pvbwin','bmid',$UserModel->info['bmap']);
		$pve_bmap_cfg_info = Game::getcfg_info('pve_bmap',$UserModel->info['bmap']);

		//这个门客 是不是可以出战(活的)
		$Act3Model->go_fight($hero_id);
		
		//获取阵法信息
		$TeamModel  = Master::getTeam($this->uid);
		$hero_damage = $TeamModel->getHerodamage($hero_id);
		$hero_damage = intval($hero_damage);
		
		//$team = Master::get_team($this->uid);
		//门客伤害值
		//门客打关卡BOSS 伤害值=5000*武力资质*等级+武力属性
		//$hero_damage = 5000 * $team['zz'][$hero_id]['zz']['e1'] * $hero_info['level'] + $team['heros'][$hero_id]['aep']['e1'];
		//当前BOSS血量
		$boss_hp = $pve_bmap_cfg_info['hp'] - $UserModel->info['mkill'];
		if ($hero_damage >= $boss_hp){
			//BOSS配置
			$pve_boss_cfg = Game::getcfg_info('pve_boss',$UserModel->info['bmap']);
			//打死BOSS 过关
			$u_update = array(
				'bmap' => $UserModel->info['bmap'] + 1,
				'mkill' => 0,
			);

			$UserModel->update($u_update);
			//门客出战信息清空
			$Act3Model->reset();
			
			//胜利弹窗
			Master::back_win('user','pvbwin','damage',$boss_hp);

            //奖励倍数
            $beishu = Game::pv_beishu('pvb');

			//打死boss就奖励
            $team = Master::get_team($this->uid);
			foreach ($pve_boss_cfg['rwd'] as $rv){
				//构造数量
				$item = Game::auto_count($rv,$team['allep']);
                $item['count'] = $item['itemid'] == 5?$item['count']:ceil($item['count'] * $beishu);
				Master::add_item2($item,'user','pvbwin');
			}
			//通过一个中关卡获得的额外奖励
			if( !empty($pve_boss_cfg['rwd_prob_100']) ){
				$extra = $pve_boss_cfg['rwd_prob_100'];
				$rk = Game::get_rand_key(10000,$extra,'prob_10000');
				$count = $extra[$rk]['itemid'] == 5?$extra[$rk]['count']:ceil($extra[$rk]['count'] * $beishu);
				if(!empty($extra[$rk])){
					$item = array(
						'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
						'itemid' => $extra[$rk]['itemid'],
						'count' => $count,
					);
					Master::add_item2($item,'user','pvbwin');
				}
			}
			
			//更新关卡排行
			$Redis2Model = Master::getRedis2();
			$Redis2Model->zAdd($this->uid,$UserModel->info['bmap'] + $UserModel->info['smap'] - 1);
			
			//犯人
			// $cfg_fanren = Game::getcfg('pve_fanren');
			// foreach($cfg_fanren as $info){
			// 	if( $UserModel->info['bmap']-1 != $info['bmap']){
			// 		continue;
			// 	}
			// 	$Act19Model = Master::getAct19($this->uid);
			// 	$Act19Model->shouya($info['id']);
			// 	break;
			// }
			
			//名望
			$Act20Model = Master::getAct20($this->uid);
			$Act20Model->update_eday($UserModel->info['bmap']-1);//名望上限
			$Act20Model->add_mw(25);//名望值
			
			//关卡冲榜
			$HuodongModel = Master::getHuodong($this->uid);
	   		$HuodongModel->chongbang_huodong('huodong251',$this->uid,1);
	   		
	   		//主线任务 - 刷新
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_refresh(7);

            //大关卡流水
            Game::cmd_flow(18, 1, 1, $UserModel->info['bmap']);

            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::copy($UserModel->info['platform'], $this->uid, $UserModel->info['bmap'] + 1, '地图大关');

		}else{
			//减去BOSS血量
			$UserModel->add_sth('mkill',$hero_damage);
			
			//失败弹窗
			Master::back_s(2);
			Master::back_win('user','pvbwin','damage',$hero_damage);
		}
	}
	
	/*
	 * 使用出战令 复活门客
	 */
	public function comeback($params){
		$UserModel = Master::getUser($this->uid);
		
		//判定大小关逻辑修改
		if (!Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_LT_BMAP);
		}
		
		//需要复活的门客ID
		$hero_id = Game::intval($params,'id');
		
		//门客出战列表
		$Act3Model = Master::getAct3($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act3Model->cone_back($hero_id);
	}

	/*
	 * 第二次进来 血量加满 全部伙伴复活
	 * 
	 */
	public function comebackall($params){
		$UserModel = Master::getUser($this->uid);
		
		//判定大小关逻辑修改
		if (!Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_LT_BMAP);
		}
		
		//需要复活的门客ID

		
		//门客出战列表
		$Act3Model = Master::getAct3($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act3Model->cone_back_all();

		$u_update = array(
			'mkill' => 0,
		);

		$UserModel->update($u_update);
	}
	
	/*
	 * 获取其他玩家信息
	 */
	public function getFuserMember($params){
		$fuid = Game::intval($params,"id");
		$fuid_Data = Master::fuidData($fuid);
		$spid = Game::intval($params,"spid");
		if (!empty($spid) && $spid == 6142){
            $Act6142Model = Master::getAct6142($fuid);
            $v_info = $Act6142Model->info;
            $fuid_Data['clothe']['head'] = $v_info['head'];
            $fuid_Data['clothe']['body'] = $v_info['body'];
            $fuid_Data['clothe']['ear'] = $v_info['ear'];
            $fuid_Data['clothe']['background'] = $v_info['background'];
            $fuid_Data['clothe']['effect'] = $v_info['effect'];
            $fuid_Data['clothe']['animal'] = $v_info['animal'];
        }
		Master::back_data($this->uid,'user','fuser',$fuid_Data);

        //清除  新加亲家闪光
//        $Act90Model = Master::getAct90($this->uid);
//        $Act90Model->clearQjTip($fuid);
	}
	
	/*
	 * 心跳请求
	 */
	public function adok($params){
        //发放邮件
        $Act93Model = Master::getAct93($this->uid);
        $Act93Model->sendMail();
		//活动ID
		$label = Game::strval($params,'label');
		
		//事件路由
		switch ($label){
			case "":
				//无事件 心跳请求
				break;
            case 'clothepvp'://3种资源经营 1
                $Act6142Model = Master::getAct6142($this->uid);
                $Act6142Model->updateCount(true);
                break;
            case 'flower':
                $Act6193Model = Master::getAct6193($this->uid);
                $Act6193Model->updateNum();
                break;
			case 'jingying'://3种资源经营 1
				$Act1Model = Master::getAct1($this->uid);
				$Act1Model->back_data();
				break;
			case 'zhengwu'://政务处理 2 
				$Act2Model = Master::getAct2($this->uid);
				$Act2Model->back_data();
				break;
			case 'jingli'://红颜精力 11
				$Act11Model = Master::getAct11($this->uid);
				$Act11Model->back_data();
				break;
			case 'jiaQi'://知己假日 11
				$Act6131Model = Master::getAct6131($this->uid);
				$Act6131Model->back_data();
				break;
            case 'lilian'://历练刷新
                $Act6133Model = Master::getAct6133($this->uid);
                $Act6133Model->back_data();
                break;
            case 'school'://书院学习 6
				$Act16Model = Master::getAct16($this->uid);
				$Act16Model->back_data();
				break;
			case 'sonpow'://子嗣活力
                $this->refson();
				break;
			//case 'sonpow'://子嗣活力
			case 'wordboss1'://副本 蒙古军来袭
				//副本 蒙古军来袭
				$Act21Model = Master::getAct21($this->uid);
				$Act21Model->back_data();
				//$Act4Model = Master::getAct4($this->uid);
				//$Act4Model->back_data();
				break;
			case 'wordboss2'://副本 葛二蛋来袭
				//副本 葛二蛋来袭
				$Act22Model = Master::getAct22($this->uid);
				$Act22Model->back_data();
				//$Act5Model = Master::getAct5($this->uid);
				//$Act5Model->back_data();
				break;
			case 'xunfangtili'://寻访体力
				//寻访
				$Act26Model = Master::getAct26($this->uid);
				$Act26Model->back_data();
				break;
			case 'tiqintime'://子嗣提亲
				//子嗣信息
				$SonModel = Master::getSon($this->uid);
				$SonModel->getBase();
				break;
			case 'zhaoqin'://招亲列表
				//不刷
				break;
			case 'pvb2cd'://全军出击 冷却
				$Act13Model = Master::getAct13($this->uid);
				$Act13Model->back_data();
				break;
			case 'clubbossltime'://联盟-boss血量
				$Act40Model = Master::getAct40($this->uid);
				$cid = $Act40Model->info['cid'];
				if(!empty($cid)){
					$Sev12Model = Master::getSev12($cid);
					$Sev12Model->bake_data();
				}
				break;
			case 'yamen'://衙门信息
				
				//衙门信息
				$Act60Model = Master::getAct60($this->uid);
				//如果CD到了
				if ($Act60Model->outf['state'] == 0){
					//尝试开战
					$Act60Model->rand_qhid();
				}
				
				//发送衙门初始信息
				$Act60Model->back_data();
				
				//发送战斗信息
				$Act61Model = Master::getAct61($this->uid);
				$Act61Model->back_data();
				
				//刷新 20名日志表
				$Sev6Model = Master::getSev6();
				$Sev6Model->list_click($this->uid);
				break;
			case 'jlShopltime'://酒楼商店刷新
				//获取商店列表信息
				$Act51Model = Master::getAct51($this->uid);
				$Act51Model->back_data();
				break;
			case 'huntOnecd'://狩猎
				//狩猎
				$Act110Model = Master::getAct110($this->uid);
				$Act110Model->back_data();
				break;
				
			case 'kuaclubpktime':  //帮会跨服战
			case 'kuaclubrwdtime':  //帮会跨服战
				//判断是否已经有联盟
				$Act40Model = Master::getAct40($this->uid);
				if(!empty($Act40Model->info['cid'])){
					$Sev54Model = Master::getSev54($Act40Model->info['cid']);
					$outf = $Sev54Model->out_data($this->uid);
					Master::back_data($this->uid,'club','clubKuaInfo',$outf);
				}
				break;
			case 'kuayamen':
				$Act306Model = Master::getAct306($this->uid);
				$Act300Model = Master::getAct300($this->uid);
				if($Act306Model->info['state'] == 1 && $Act300Model->hd_state == 3){//有门票的才发信息 且 正式赛
					//衙门信息
					if ($Act300Model->outf['state'] == 0){
						//尝试开战
						$Act300Model->rand_qhid();
					}
					//发送衙门初始信息
					$Act300Model->back_data();
					//发送战斗信息
					$Act61Model = Master::getAct61($this->uid);
					$Act61Model->back_data();

					$Act301Model = Master::getAct301($this->uid);
					$Act301Model->back_data();
				}
				//刷新 20名日志表
				$Sev60Model = Master::getSev60($Act300Model->hd_cfg['info']['id']);
				$Sev60Model->list_click($this->uid);

				//结算阶段进入衙门返回当前第一名信息
				if($Act300Model->hd_state == 4){
					$Redis305Model = Master::getRedis305($Act300Model->hd_cfg['info']['id']);
					$Redis305Model->back_data_first();
				}
				break;
			case 'kuaymYuend'://跨服衙门战预选赛结束
			case 'kuaymYushow'://跨服衙门在正式赛开始
			    //重新请求
			    $Act300Model = Master::getAct300($this->uid);
			    $Act300Model->comehd();
			    break;
			case 'kuaymeTime'://跨服衙门战活动结束
			    $Act300Model = Master::getAct300($this->uid);
				$Act300Model->comehd();
			    if($Act300Model->hd_state ==4){
			        //返回是否领取过奖励
			        $Act307Model = Master::getAct307($this->uid);
			        $Act307Model->back_data();
			        //返回第一名信息
			        $Redis305Model = Master::getRedis305($Act300Model->hd_cfg['info']['id']);
			        $Redis305Model->back_data_first();
			    }
			    break;
			case 'huodong_290_refresh_1'://双十二免费次数刷新
			case 'huodong_290_refresh_2'://双十二免费次数刷新
			    //重新请求
			    $Act290Model = Master::getAct290($this->uid);
				$Act290Model->back_data_u();
			    break;
			case 'gzj'://国子监
			    //重新请求
			    $Act76Model = Master::getAct76($this->uid);
			    $Act76Model->back_data();
			    break;
			case 'gzj_primary'://国子监初级送礼
			case 'gzj_middle'://国子监中级送礼
				$Act79Model = Master::getAct79($this->uid);
				$Act79Model->back_data();
				break;
            case 'userChangJingTime'://用户场景
                //用户场景
                $Act74Model = Master::getAct74($this->uid);
                $Act74Model->back_data_a();
                break;
			case 'banish'://发配
				$Act129Model = Master::getAct129($this->uid);
				$Act129Model->back_data();
				break;
			case 'newyear'://新年活动
				$Act298Model = Master::getAct298($this->uid);
				$Act298Model->getBossInfo();
				break;
            case 'huodong_313_time'://跨服势力冲榜活动
                $Act313Model = Master::getAct313($this->uid);
                $Act313Model->back_data_hd();
                break;
            case 'huodong_314_time'://跨服好感冲榜活动
                $Act314Model = Master::getAct314($this->uid);
                $Act314Model->back_data_hd();
				break;
			case 'invite_count':
				$Act731Model = Master::getAct731($this->uid);
				$Act731Model->refreshCount();
				break;
			case 'invite_event':
				$Act730Model = Master::getAct730($this->uid);
				$Act730Model->refreshEventTime();
				break;
			case 'pop_gift':
				$Act750Model = Master::getAct750($this->uid);
				$Act750Model->refreshGift();
				break;
			default:
				//未知心跳协议
				Master::error("adok_act_err_".$label);
				break;
		}
	}
	
	/*
	 * 升官
	 */
	public function shengguan($params){
		
		$UserModel = Master::getUser($this->uid);
		
		//过新手引导就已经1级   目前出现0级 做兼容直接升1级   重新执行新手引导升级
		if( $UserModel->info['level'] <= 0 ){
			//新手引导步骤信息模块
			$Act32Model = Master::getAct32($this->uid);
			$Act32Model->up_guan();
			return true;
		}

		//官品配置
		$guan_cfg_info = Game::getcfg_info('guan',$UserModel->info['level']);

		//判断是都可以升级
		if (! empty($guan_cfg_info['condition'])){			
			$arr = explode('|', $guan_cfg_info['condition']);
			for ($i = 0; $i < count($arr); $i++){
				$guan_need = Game::getcfg_info('guanNeed', $arr[$i]);
				if (!empty($guan_need) && !$this->isCanUp($guan_need['condition'], $guan_need['para'])){
					return;
				}
			}
		}
		
		$UserModel->shengguan();
		
		if ($guan_cfg_info['heroid']){
			//添加门客
			Master::add_item($this->uid,KIND_HERO,$guan_cfg_info['heroid']);
		}

		//增加等级服装
		$Act6140Model = Master::getAct6140($this->uid);
        $Act6140Model -> addUseLvClothe($UserModel->info['level']);
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(14);

		$Act1Model = Master::getAct1($this->uid);
		$Act1Model->back_data();
	}

	/*
	*判断是否能升官
	*/
	public function isCanUp($type, $param){		
		switch ($type) {
			case 1:
				$Act39Model = Master::getAct39($this->uid);
				return $Act39Model->info['id'] > $param;
			case 2:
				$HeroModel = Master::getHero($this->uid);
				$hero_info = $HeroModel->check_info($param);
				return !empty($hero_info);
			case 3:
				$WifeModel = Master::getWife($this->uid);
				//红颜ID合法
				$wife_info = $WifeModel->check_info($param);
				return !empty($wife_info);
			case 4:
				$UserModel = Master::getUser($this->uid);
				$smap_cfg = Game::getcfg_info('pve_smap',intval($UserModel->info['smap']) + 1);
				return intval($smap_cfg['mmap']) > $param;
			case 5:
				$UserModel = Master::getUser($this->uid);
				return $UserModel->info['bmap'] > $param;
			case 6:
				$act6000Model = Master::getAct6000($this->uid);
				return $act6000Model->isOver($param);
			case 7:
				$arr = explode('|', $param);
				$act6001Model = Master::getAct6001($this->uid);
				return $act6001Model->getHeroJB(intval($arr[0])) >= intval($arr[1]);
			case 8:
				$arr = explode('|', $param);
				$act6001Model = Master::getAct6001($this->uid);
				return $act6001Model->getWifeJB(intval($arr[0])) >= intval($arr[1]);
		}
		return true;
	}
	
	/**
	 * 获取用户场景
	 */
	public function getuback($params){
		 //用户场景
        $Act74Model = Master::getAct74($this->uid);
        $Act74Model->back_data_a();
	}
	/**
	 * 设置用户场景
	 */
	public function setuback($params){
		$id = Game::intval($params,"id");
		 //用户场景
        $Act74Model = Master::getAct74($this->uid);
        $Act74Model->set($id);
        $Act74Model->back_data_a();
	}
	
	
	/**
	 * 经营刷新
	 */
	public function refjingying($params){
		$Act1Model = Master::getAct1($this->uid);
		$Act1Model->back_data();
	}
	/**
	 * 寻访刷新
	 */
	public function refxunfang($params){
		$Act26Model = Master::getAct26($this->uid);
		$Act26Model->back_data();
	}
	/**
	 * 红颜刷新
	 */
	public function refwife($params){
		$Act11Model = Master::getAct11($this->uid);
		$Act11Model->back_data();
	}
	/**
	 * 子嗣刷新
	 */
	public function refson(){
		$SonModel = Master::getSon($this->uid);
		$SonModel->getBase();
	}

	/**
	 * 卡牌刷新
	 */
	public function refcard(){
		$CardModel = Master::getCard($this->uid);
		$CardModel->backCardList();
	}
	/**
	 * 设置显示门客
	 */
	public function serHeroShow($params){
		$id = Game::intval($params,"id");

		$Act6120Model = Master::getAct6120($this->uid);
		$Act6120Model->changeHero($id);
	}

    /**
     *
     */
	public function setClothe($params){
        $head = Game::intval($params,"head");
        $body = Game::intval($params,"body");
        $ear = Game::intval($params,"ear");
        $bg = Game::intval($params,"background");
        $eff = Game::intval($params,"effect");
        $ani = Game::intval($params,"animal");
        $Act6141Model = Master::getAct6141($this->uid);
		$Act6141Model->changeClothe($head, $body, $ear, $bg, $eff, $ani);
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(100, 1);
		Game::cmd_other_flow($this->uid,"user","setClothe",$params,10000,1,1,1);
    }

    public function setAvatar($params){
        $head = Game::intval($params,"head");
        $blank = Game::intval($params,"blank");
        $Act6151Model = Master::getAct6151($this->uid);
        $Act6151Model->changeHead($head, $blank);
    }

    /**
     *
     */
    public function lockClothe($params){
        $id = Game::intval($params,"id");
        $param = Game::intval($params,"id1");
        if (empty($param)){
            Master::error(RESTART_GAME);
        }
        $Act6140Model = Master::getAct6140($this->uid);
        $Act6140Model->addClothe($id);
    }

    public function clotheRank(){
        $Redis6140Model = Master::getRedis6140();
        $Redis6140Model->back_data();
        $Redis6140Model->back_data_my($this->uid);//我的排名
    }

    public function lvupSuit($params){
        $id = Game::intval($params,"id");
        $Act6140Model = Master::getAct6140($this->uid);
        $Act6140Model->lvupSuit($id);
    }

    public function qifuCost($params){

        $Act6154Model = Master::getAct6154($this->uid);
        $cost = $Act6154Model->qifuCost();

        Master::back_data($this->uid,'user','qifu',array("qifuCost" => $cost));
    }

    public function qifu($params){
        $id = Game::intval($params,"jyid");
        $Act6154Model = Master::getAct6154($this->uid);
        $Act6154Model->qifu($id);

        //舞狮大会 - 祈福次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(20,1);
    }

    public function qifuTen($params){

        $id = Game::intval($params,"jyid");
        $Act6154Model = Master::getAct6154($this->uid);
        $Act6154Model->qifuTen($id);

        //舞狮大会 - 祈福次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(20,10);
    }

    /**
     * 属性详情
     */
    public function addition(){

        $TeamModel = Master::getTeam($this->uid);
        $HeroModel = Master::getHero($this->uid);
        $percentage = $TeamModel->info['percentage'];
        $hero_num = count($HeroModel->info);
        $son = $TeamModel->info['sonep'];
        $clothe_suit = Game::epmultiply($TeamModel->info['clothe_suit'],$hero_num);
        $clothe = Game::epaddr1($TeamModel->info['clothe'],$clothe_suit);
		$hero = $TeamModel->info['hero_ep'];
		$card = $TeamModel->info['cardep'];
		$baowu = $TeamModel->info['baowuep'];

        for ($i=1;$i<=count($clothe_suit);$i++){
            $hero[$i] -= $clothe_suit[$i];
        }
        if (!empty($percentage)){
            foreach ($percentage as $k=>$v){
                switch ($k){
                    case 'son':
                        $son_add = Game::epmultiply_arr($son,$v);
                        $son = Game::epaddr1($son,$son_add);
                        $pct['son'] = Game::fmt_ep($v);
                        break;
                    case 'hero':
                        $hero_add = Game::epmultiply_arr($hero,$v);
                        $hero = Game::epaddr1($hero,$hero_add);
                        $pct['hero'] = Game::fmt_ep($v);
                        break;
                    case 'clothe':
                        $clothe_add = Game::epmultiply_arr($clothe,$v);
                        $clothe = Game::epaddr1($clothe,$clothe_add);
                        $pct['clothe'] = Game::fmt_ep($v);
						break;
                }
            }
            Master::back_data($this->uid,'user','percentage',$pct);
        }

        $addition['son']    = Game::fmt_ep($son);
        $addition['hero']   = Game::fmt_ep($hero);
		$addition['clothe'] = Game::fmt_ep($clothe);
		$addition['card'] = Game::fmt_ep($card);
		$addition['baowu'] = Game::fmt_ep($baowu);
        Master::back_data($this->uid,'user','addition',$addition);
    }

    /**
     * 许愿树-信息
     */
    public function wishInfo(){
        $Act6210Model = Master::getAct6210($this->uid);
        $Act6210Model->back_data();
    }


    /**
     * 许愿树-抽奖
     * "id":许愿树id
     * "num":次数 1 次 或者 10次"
     */
    public function wishPlay($params){
        $id = Game::intval($params,"id");
        $num = Game::intval($params,"num");
        $Act6210Model = Master::getAct6210($this->uid);
        $Act6210Model->play($id,$num);

        //舞狮大会 - 许愿树许愿次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(22,$num);
    }

    /*
	 * 记录步骤
	 */
	public function recordSteps($params)
	{
	    //步骤ID
	    $stepId = Game::intval($params,"stepId");

	    $db = Common::getMyDb();
	    $sql = "select `step_id` from `user_step` where `uid`=".$this->uid;
        $stepRes = $db->fetchRow($sql);

        //$oldStepId = 0;
        if ($stepRes) {
			$oldStepId = $stepRes["step_id"];
			if ($stepId > $oldStepId) {
			//if ($oldStepId > 0) {

				// 修改
				$sql = "update `user_step` set `step_id` = " . $stepId . " where `uid` = ".$this->uid;
				$res = $db->query($sql);
			}
        }else{
			// 新增
			$sql = "insert into `user_step` (`uid`, `step_id`)
			values ('".$this->uid."', " . $stepId . ")";
			$res = $db->query($sql);
		}

        Master::back_data($this->uid,'user','recordSteps',array("stepId"=>$stepId));
	}

	public function getUserBaseInfo($params){
		$uid = Game::intval($params,'uid');

		$fUserInfo = Master::fuidInfo($uid);
		Master::back_data($this->uid,'user','baseInfo',array('info' => $fUserInfo));
	}

}
