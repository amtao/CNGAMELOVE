<?php
//徒弟操作
class sonMod extends Base
{
	/*
	 * 购买席位
	 */
	public function buyseat($params){
		//子嗣席位类
		$Act12Model = Master::getAct12($this->uid);
		//加席位
		$Act12Model->add_seat();
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(11);
	}

	/*
	 * 徒弟名字
	 */
	public function randSonName($params)
	{
		//载入文件
		$rand_name = Common::getLang('rand_name');
		$name = '';
		
		$sid = Game::intval($params,'sonid');
		
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);

		if ($son_info['state'] != 0){
			Master::error(SON_HAVE_NAME);
		}
		$hero_info = Game::getcfg_info('hero',$son_info['mom']);
		if($son_info['mom'] == 2){
			$surName = mb_substr($hero_info['name'],0,2,'utf-8');
		}else{
			$surName = mb_substr($hero_info['name'],0,1,'utf-8');
		}
		$name = $surName.$rand_name['names'][1][array_rand($rand_name['names'][1])];
		// for ($i=0 ; $i < $rand_name['len'] ; $i++){
		// 	$name .= $rand_name['names'][$i][array_rand($rand_name['names'][$i])];
		// }
		Master::back_data($this->uid,"system","randname",array("name"=>$name));
	}
	
	/*
	 * 子嗣取名字
	 */
	public function sonname($params){
		$sid = Game::intval($params,'id');
		$sname = Game::intval($params,'name');
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		
		if ($son_info['state'] != 0){
			Master::error(SON_HAVE_NAME);
		}
		$sname = Game::filter_char($sname,0);
		//敏感字符判定
		$sname = Game::str_mingan($sname);
		//非法字符判定
		$sname = Game::str_feifa($sname);
		//名字长度判定
		$len = Common::utf8_strlen($sname);
		if ($len  < 2 ||$len > 8){
		    Master::error(USER_COUNT_SHORT_NAME);
		}
		
		
		$s_update = array(
			'sonuid' => $sid,
			'name' => $sname,
			'state' => 1,
		);
		$SonModel->update($s_update);
	}
	
	/*
	 * 子嗣改名字
	 */
	public function rname($params){
		$sid = Game::intval($params,'id');
		$sname = Game::intval($params,'name');
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		//非法字符判定
		$sname = Game::filter_char($sname,0);
		//敏感字符判定
		$sname = Game::str_mingan($sname);
		//非法字符判定
		$sname = Game::str_feifa($sname);
		//名字长度判定
		$len = Common::utf8_strlen($sname);
		if ($len  < 2 ||$len > 8){
		    Master::error(USER_COUNT_SHORT_NAME);
		}
		
		Master::sub_item($this->uid,KIND_ITEM,1,20);
		
		
		$s_update = array(
			'sonuid' => $sid,
			'name' => $sname,
		);
		$SonModel->update($s_update);
	}
	
	/*
	 * 子嗣培养
	 */
	public function play($params){
		$sid = Game::intval($params,'id');
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		
		//是否处于幼儿或者儿童阶段
		if (!in_array($son_info['state'],array(1,2))){
			Master::error(SON_NOT_CULTIVATE);
		}
		
		$son_out_base = $SonModel->getBase_buyid($sid);
		if ($son_out_base['power'] <= 0){
			Master::error(SON_POWER_SHORT);
		}
		
		$exp_add = 10;
		//神迹
//		$Act65Model = Master::getAct65($this->uid);
//		if ($Act65Model->rand(4)){
//			//触发神迹: 4.天资聪慧
//			$exp_add = 30;
//		}
		
		//更改信息数组
		$s_update = array(
			'sonuid' => $sid,
			'exp' => $exp_add,
			'power' => $son_out_base['power'] - 1,
		);
		$SonModel->update($s_update);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(9,1);
		
		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(10,1);

        //舞狮大会 - 培养徒弟
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(10,1);
		
		
	}
	
	/*
	 * 恢复活力
	 */
	public function onfood($params){
		$sid = Game::intval($params,'id');
		
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		
		$son_out_base = $SonModel->getBase_buyid($sid);
		if($son_out_base['power'] > 0){
			$SonModel->getBase();
			return true;
		}
		
		//扣除道具
		Master::sub_item($this->uid,KIND_ITEM,73,1);

		
		$s_update = array(
			'sonuid' => $sid,
			'ptime' => 0,
		);
		$SonModel->update($s_update);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(28,1);

		
		Master::error_msg(SON_NOTIN_SUCCESS_UP);
		
		return true;
	}
	
	/*
	 * 一键培养
	 */
	public function allplay($params){
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		
		//神迹
		$Act65Model = Master::getAct65($this->uid);
		
		$add_num = 0;
		foreach ($SonModel->info as $k => $son_info)
		{
			if (in_array($son_info['state'],array(1,2))){
				$son_out_base = $SonModel->getBase_buyid($k);
				//活力值
				$pow = $son_out_base['power'];
				if ($pow > 0){
					//加上经验值
					$exp = $pow * 10;
					
					if ($Act65Model->rand(4)){
						//触发神迹: 4.天资聪慧
						$exp += 20;
					}
		
					$s_update = array(
						'sonuid' => $k,
						'exp' => $exp,
						'power' => 0,
					);
					$SonModel->update($s_update);
					$add_num ++;
					
					//主线任务
					$Act39Model = Master::getAct39($this->uid);
					$Act39Model->task_add(9,$pow);
					
					//日常任务
					// $Act35Model = Master::getAct35($this->uid);
					// $Act35Model->do_act(10,$pow);
				}
			}
		}
		if (empty($add_num)){
			Master::error(SON_CULTIVATE_IS_EMPTY);
		}
        //舞狮大会 - 培养徒弟
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(10,$add_num);
	}
	
	/*
	 * 一键恢复
	 */
	public function allfood($params){
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		
		$add_num = 0;
		
		foreach ($SonModel->info as $k => $son_info)
		{
			if (in_array($son_info['state'],array(1,2))){
				$son_out_base = $SonModel->getBase_buyid($k);
				//活力值
				if ($son_out_base['power'] == 0){
					//扣除活力丹
					Master::sub_item($this->uid,KIND_ITEM,73,1);
					$s_update = array(
						'sonuid' => $k,
						'ptime' => 0,
					);
					$SonModel->update($s_update);
					$add_num++;
				}
			}
		}
		if (empty($add_num)){
			Master::error(SON_TIME_IS_UP);
		}
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(28,$add_num);
	}
	
	/*
	 * 科举
	 */
	public function keju($params){
		$sid = Game::intval($params,'id');
		
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		
		//是否满级
		if ($son_info['state'] != 3){
			Master::error(SON_LEVER_SHORT);
		}
		
		//子嗣势力
		$shili = $son_info['e1'] + $son_info['e2'] + $son_info['e3'] + $son_info['e4'];
		
		//根据属性配置 给予名次
		$honor = 1;//默认童生
		//科举配置
		$son_cn_cfg = Game::getcfg('son_cn');
		foreach ($son_cn_cfg as $k => $v){
			if ($shili >= $v['shili']){
				$honor = $k;
			}else{
				break;
			}
		}
		$s_update = array(
			'sonuid' => $sid,
			'honor' => $honor,
			'state' => 4,
		);
		$SonModel->update($s_update);
		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(3);
	}
	
	
	
	//-----------------以下联姻方法--------------
	/*
	 * 打开媒婆界面 
	 * 检查有没 结婚成功事件
	 */
	public function meipo($params){
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		
		$m_wins = array();//婚姻弹窗列表
		//遍历子嗣列表 如果有结婚的
		foreach ($SonModel->info as $sid => $v){
			if ($v['state'] == 8){
				//结婚弹窗
				$m_win = array(
					'sid' => $sid,
					'items' => array()
				);
				
				//加上银两
//				$coin = 50000 * $v['honor'];
//				Master::add_item($this->uid,KIND_ITEM,2,$coin);
//				Master::add_item($this->uid,KIND_ITEM,73,1);//活力丹
//				$m_win['items'][] = array('id' => 2,'count' => $coin);
//				$m_win['items'][] = array('id' => 73,'count' => 1);
				
				$s_update = array(
					'sonuid' => $sid,
					'state' => 9,
				);
				$SonModel->update($s_update);
				$m_wins[] = $m_win;
				
				if( !empty($v['tqitem']) && $v['tqitem'] == 1 ){
					//活动消耗钻石/元宝
					$son_cn_cfg_info = Game::getcfg_info('son_cn',$v['honor']);
					$xh_cash = intval($son_cn_cfg_info['zhaoqin']);
					$HuodongModel = Master::getHuodong($this->uid);
					$HuodongModel->xianshi_huodong('cash',$xh_cash);

                    //活动296 - 挖宝锄头-每日任务
                    $Act296Model = Master::getAct296($this->uid);
                    $Act296Model->get_chutou_task(9,$xh_cash);
				}

                $Act90Model = Master::getAct90($this->uid);
                $Act90Model->qjTip($v['spuid']);

			}
		}
		
		if (!empty($m_wins)){
			
			
			//弹出结婚成功页面
			Master::$bak_data['a']['son']['win']['jiehun'] = $m_wins;
			$Act10Model = Master::getAct10($this->uid);
			$Act10Model->back_data();
			
			//更新阵法
			$TeamModel = Master::getTeam($this->uid);
			$TeamModel->reset(3);
			
			$Act133Model = Master::getAct133($this->uid);
			$Act133Model->del_key();

            //国力庆典
            $Act6208Model = Master::getAct6208($this->uid);
            $Act6208Model->add(count($m_wins));
		}else{
			//否则不弹出 S=0
			Master::back_s(0);
		}
	}
	
	/*
	 * 扣除联姻道具
	 * $type:1 元宝 2,道具
	 * honor 身份
	 * $is_off ：是否扣元宝
	 */
	private function _sub_mar_item($type,$honor,$is_off = 0){
		//需求配置
		$son_cn_cfg_info = Game::getcfg_info('son_cn',$honor);
		
		//所需道具
		if ($type == 1){
			//需求元宝
			Master::sub_item($this->uid,KIND_ITEM,1,$son_cn_cfg_info['zhaoqin'],false,false,$is_off);
		}elseif($type == 2){
			//需求道具
			Master::sub_item($this->uid,KIND_ITEM,$son_cn_cfg_info['itemid'],1);
		}else{
			Master::error("tiqin_type_err_".$type);
		}
	}
	//提亲 / 全服提亲
	//提亲 / 指定提亲
	/*
	 * 提亲
	 * $uid / 0的话 就是全服提亲
	 * $type 道具ID 或者元宝 1
	 * $sid / 0的话 就是全服提亲
	 */
	public function tiqin($params){
		//指定ID 或者 全服0
		$fuid = Game::intval($params,'uid');
		//元宝1 , 道具2
		$type = Game::intval($params,'type');
		//子嗣ID
		$sid = Game::intval($params,'sid');
        //子嗣ID
        $ishonor = Game::intval($params,'ishonor');
		
		//UID合法
		if(!empty($fuid)){
			//是否合服范围内
			Game::isHeServerUid($fuid);
		
			Master::click_uid($fuid);
		}
		
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		
		if ($son_info['state'] != 4){
			Master::error(SON_REFRESH_ERROR);
		}

        if ($son_info['liLianStatus']){
            Master::error(STATUS_ERROR);
        }
		
		if ($fuid == $this->uid){
			Master::error(SON_MARRIAGE_SISTER_ERROR);
		}
		
		//扣除所需道具
		$this->_sub_mar_item($type,$son_info['honor'],1);
        $state = 5;
		//进行提亲
		if ($fuid > 0){
			
			//判断对方是否有合适的子嗣
			$fSonModel = Master::getSon($fuid);
			$falg = $fSonModel->check_mson($son_info['sex']);
			if(!$falg){
				Master::error(SON_NOTIN_NO_MARRY);
			}
			
			//对个人进行提亲
			//获取对方用户锁
			Master::get_lock(1,"user_".$fuid,true,'对方忙碌中');
			
			$fAct10Model = Master::getAct10($fuid);
			$fAct10Model->add($this->uid,$sid);
		}else{
            $state = empty($ishonor)? 5:10;
			//全服提亲
			$Sev1Model = Master::getSev1();
			$Sev1Model->request($this->uid,$sid,$son_info['sex'],$son_info['honor'],$ishonor);

            //家宴公开或者官宴发聊天广播
            $Sev6012Model = Master::getSev6012();
            $ep = $son_info['e1'] + $son_info['e2'] + $son_info['e3'] + $son_info['e4'];
            $msg = "#childMarry#::".$son_info['name'].":".$son_info['sex'].":".$ep.":".$son_info['honor'];
            $Sev6012Model->add_msg($this->uid, $msg, 3);
		}
		
		//修改子嗣信息
		$s_update = array(
			'sonuid' => $sid,
			'state' => $state,	//提亲中
			
			'tquid' => $fuid,	//提亲UID 0 则全服
			'tqitem' => $type,	//1元宝 2道具
			'tqtime' => $_SERVER['REQUEST_TIME']+259200,//提亲超时时间 / 超时吧state置为7  timeout
		);
		$SonModel->update($s_update);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(16, 1);
	}
	
	/*
	 * 招亲 / 从全服招亲列表中刷3个人
	 */
	public function zhaoqin($params){
		//子嗣ID
		$sid = Game::intval($params,'id');
		
		//招亲类
		$Act9Model = Master::getAct9($this->uid);
		$Act9Model->get_zhaoqin($sid);
	}
	
	/*
	 * 立即刷新 全服招亲列表
	 */
	public function rstzhaoqin($params){
		//子嗣ID
		$sid = Game::intval($params,'id');
		
		//扣钱
		Master::sub_item($this->uid,KIND_ITEM,1,100);
		
		//招亲类
		$Act9Model = Master::getAct9($this->uid);
		$Act9Model->get_zhaoqin($sid,1);
	}
	
	
	/*
	 * 结婚 从全服招亲列表中 选中一个人结婚
	 * 
	 * $uid / 对方UID
	 * $sid / 对方子嗣ID
	 * $type 道具ID 或者元宝 1
	 * $mysid 我的子嗣ID
	 */
	public function jiehun($params){

		//提亲UID
		$fuid = Game::intval($params,'uid');
		//元宝1 , 道具2
		$type = Game::intval($params,'type');
		//对方子嗣ID
		$sid = Game::intval($params,'sid');
		//我的子嗣ID
		$mysid = Game::intval($params,'mysid');
		
		//验证 这个人是不是在我的全服提亲表里面
		//省略~
		
		//对方子嗣信息
		$fSonModel = Master::getSon($fuid);
		$s_info = $fSonModel->check_info($sid);
		if ($s_info['state'] != 5 && $s_info['state'] != 10){
			Master::error(SON_STATE_ERROR_MARRY);
		}

		//我的子嗣ID
		$mysid = Game::intval($params,'mysid');
		$SonModel = Master::getSon($this->uid);
		$my_son_info = $SonModel->check_info($mysid);

		//我的子嗣状态
		if ($my_son_info['state'] != 4){
			Master::error(SON_REFRESH_ERROR);
		}

		//性别验证
		if ($my_son_info['sex'] == $s_info['sex']){
			Master::error(SON_MARRIAGE_SEX_ERROR);
		}

		//学历验证
//		if ($my_son_info['honor'] != $s_info['honor']){
//			Master::error(SON_MARRIAGE_HONOR_ERROR);
//		}
		
		//扣除结婚道具
		$this->_sub_mar_item($type,$my_son_info['honor']);

		//对方状态设置为 结婚待确认
		$fSonModel->update(array(
			'sonuid' => $sid,
			'state' => 8,	//结婚待确认
			
			'spuid' => $this->uid,	//对方的亲家UID = 我
			'spsonuid' => $mysid,	//对方的配偶子嗣ID = 我的子嗣ID
			'sptime' => $_SERVER['REQUEST_TIME'],
		));

		//我的状态设置为 已结婚 
		$SonModel->update(array(
			'sonuid' => $mysid,
			'state' => 9,	//已婚
			
			'spuid' => $fuid,	//亲家UID
			'spsonuid' => $sid,	//配偶子嗣ID
			'sptime' => $_SERVER['REQUEST_TIME'],
		));

		//全服招亲列表删除
		$Sev1Model = Master::getSev1();
		$Sev1Model->delete($fuid,$sid);
		
		// 弹窗 / 和奖励
		//结婚弹窗 
		$m_win = array(
			'sid' => $mysid,
			'items' => array()
		);
		//加上银两
//		$coin = 50000 * $my_son_info['honor'];
//		Master::add_item($this->uid,KIND_ITEM,2,$coin);
//		Master::add_item($this->uid,KIND_ITEM,73,1);//活力丹
		/*
		$m_win['items'][] = array('id' => 2,'count' => $coin);
		$m_win['items'][] = array('id' => 73,'count' => 1);
		*/
		$m_wins[] = $m_win;
		Master::$bak_data['a']['son']['win']['jiehun'] = $m_wins;
		$Act10Model = Master::getAct10($this->uid);
		$Act10Model->back_data();
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);//自己
		$Act39Model->task_add(22,1);
		$fAct39Model = Master::getAct39($fuid);//提亲
		$fAct39Model->task_add(22,1);
		
		//活动消耗 - 限时联姻次数
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong210',1);
		$fHuodongModel = Master::getHuodong($fuid);//提亲
		$fHuodongModel->xianshi_huodong('huodong210',1);
		
		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(3);
		
		$Act133Model = Master::getAct133($this->uid);
		$Act133Model->del_key();

        //国力庆典
        $Act6208Model = Master::getAct6208($this->uid);
        $Act6208Model->add(count($m_wins));

        //舞狮大会 - 联姻次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(25,count($m_wins));

        //舞狮大会 - 联姻次数 - 对方
        $Act6224fModel = Master::getAct6224($fuid);
		$Act6224fModel->task_add(28,count($m_wins));
		
		// 好友亲密度-联姻
		$Act8023Model = Master::getAct8023($this->uid);
		$Act8023Model->lianyin($fuid);
	}
	
	/*
	 * 提亲请求 / 刷新对我的提请请求
	 */
	public function getTiqin($params){
		$Act10Model = Master::getAct10($this->uid);
		$Act10Model->back_data();
	}
	
	/* 
	 * 同意提亲请求 / 选中来提亲的 进行联姻
	 * $uid / 提亲UID
	 * $sid / 0的话 提亲子嗣ID
	 * $type 道具ID 或者元宝 1
	 * $mysid 我的子嗣ID
	 */
	public function agree($params){
		//提亲UID
		$fuid = Game::intval($params,'uid');
		//元宝1 , 道具2
		$type = Game::intval($params,'type');
		//子嗣ID
		$sid = Game::intval($params,'sid');
		
		//判断这个人 是不是有正在对我提亲
		$Act10Model = Master::getAct10($this->uid);
		
		if (empty($Act10Model->info[$fuid][$sid])){
			Master::error(SON_STATE_ERROR_MARRY);
		}
		
		//对方子嗣信息
		$fSonModel = Master::getSon($fuid);
		$s_info = $fSonModel->check_info($sid);
		if ($s_info['state'] != 5){
			Master::error(SON_STATE_ERROR_MARRY);
		}
		
		//我的子嗣ID
		$mysid = Game::intval($params,'mysid');
		$SonModel = Master::getSon($this->uid);
		$my_son_info = $SonModel->check_info($mysid);
		
		//我的子嗣状态
		if ($my_son_info['state'] != 4){
			Master::error(SON_REFRESH_ERROR);
		}
		
		//性别验证
		if ($my_son_info['sex'] == $s_info['sex']){
			Master::error(SON_MARRIAGE_SEX_ERROR);
		}
		//学历验证
//		if ($my_son_info['honor'] != $s_info['honor']){
//			Master::error(SON_MARRIAGE_HONOR_ERROR);
//		}
		
		//扣除结婚道具
		$this->_sub_mar_item($type,$s_info['honor']);
		
		//对方状态设置为 结婚待确认
		$fSonModel->update(array(
			'sonuid' => $sid,
			'state' => 8,	//结婚待确认
			
			'spuid' => $this->uid,	//对方的亲家UID = 我
			'spsonuid' => $mysid,	//对方的配偶子嗣ID = 我的子嗣ID
			'sptime' => $_SERVER['REQUEST_TIME'],
		));
		
		//我的状态设置为 已结婚 
		$SonModel->update(array(
			'sonuid' => $mysid,
			'state' => 9,	//已婚
			
			'spuid' => $fuid,	//亲家UID
			'spsonuid' => $sid,	//配偶子嗣ID
			'sptime' => $_SERVER['REQUEST_TIME'],
		));
		// 弹窗 ??
		
		//我的提亲请求 列表更新
		$Act10Model->remove($fuid,$sid);
		
		//结婚弹窗 
		$m_win = array(
			'sid' => $mysid,
			'items' => array()
		);
		//加上银两
//		$coin = 50000 * $my_son_info['honor'];
//		Master::add_item($this->uid,KIND_ITEM,2,$coin);
//		Master::add_item($this->uid,KIND_ITEM,73,1);//活力丹
		
		$m_wins[] = $m_win;
		Master::$bak_data['a']['son']['win']['jiehun'] = $m_wins;
		$Act10Model->back_data();
		//阵法更新?
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);//自己
		$Act39Model->task_add(22,1);
		$fAct39Model = Master::getAct39($fuid);//自己
		$fAct39Model->task_add(22,1);
		
		//活动消耗 - 限时联姻次数
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong210',1);
		$fHuodongModel = Master::getHuodong($fuid);//提亲
		$fHuodongModel->xianshi_huodong('huodong210',1);
		
		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(3);
		
		$Act133Model = Master::getAct133($this->uid);
		$Act133Model->del_key();

        $Act90Model = Master::getAct90($this->uid);
        $Act90Model->qjTip($fuid);

        //国力庆典
        $Act6208Model = Master::getAct6208($this->uid);
        $Act6208Model->add(count($m_wins));

        //舞狮大会 - 联姻次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(25,count($m_wins));

        //舞狮大会 - 联姻次数 - 对方
        $Act6224fModel = Master::getAct6224($fuid);
		$Act6224fModel->task_add(28,count($m_wins));
		
		// 好友亲密度-联姻
		$Act8023Model = Master::getAct8023($this->uid);
		$Act8023Model->lianyin($fuid);
	}
	
	/*
	 * 拒绝提亲请求
	 * $uid / 提亲UID
	 * $sid / 0的话 提亲子嗣ID
	 */
	public function pass($params){
		//对方UID
		$fuid = Game::intval($params,'uid');
		//子嗣ID
		$sid = Game::intval($params,'sid');
		
		//对方UID验证
		Master::click_uid($fuid);
		
		//对方子嗣信息
		$fSonModel = Master::getSon($fuid);
		$s_info = $fSonModel->check_info($sid);
		if ($s_info['state'] != 5){
			Master::error(SON_STATE_ERROR_MARRY);
		}
		
		//判断这个人 是不是有正在对我提亲
		$Act10Model = Master::getAct10($this->uid);
		
		if (empty($Act10Model->info[$fuid][$sid])){
			Game::error(SON_STATE_ERROR_MARRY);
		}
		//对方状态设置为 被拒绝PASS
		$fSonModel->update(array(
			'sonuid' => $sid,
			'state' => 6,	//被拒绝PASS
		));
		
		//我的提亲请求 列表更新
		$Act10Model->remove($fuid,$sid);
	}
	
	
	/*
	 * 拒绝所有提亲请求
	 */
	public function allpass($params){
		//我的提亲信息
		$Act10Model = Master::getAct10($this->uid);
		//$Act10Model->
		//遍历所有提亲请求
		foreach ($Act10Model->outf as $v){
			$fuid = $v['fuid'];
			$sid = $v['sonuid'];
			
			//对方状态设置为 被拒绝PASS
			$fSonModel = Master::getSon($fuid);
			$fSonModel->update(array(
				'sonuid' => $sid,
				'state' => 6,	//被拒绝PASS
			));
		}
		
		//我的提亲请求 列表更新
		$Act10Model->remove_all();
	}
	
	/*
	 * 终止联姻 / 全服提亲撤回 / 个人提亲撤回 / 被拒绝撤回  / 超时撤回
	 * 提亲
	 * $sid 撤回的子嗣ID
	 */
	public function cancel($params){
		//子嗣ID
		$sid = Game::intval($params,'id');
		
		//子嗣类
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sid);
		//$son_base= $SonModel->getBase_buyid($sid);
		
		$dct = 1;//1:打折返还聘礼 2:前额返还聘礼
		
		//撤回前的状态
		if ($son_info['state'] == 5 || $son_info['state'] == 10){//提亲中
			//获取子嗣输出信息(超时判断)
			$son_base= $SonModel->getBase_buyid($sid);
			if ($son_base['state'] == 7){
				//提亲超时了 全额返还
				$dct = 2;
			}
			/*
			 * 'tquid' => $info['tquid'],//提亲UID (等于0 表示全服提亲)
			'tqitem' => $info['tqitem'],//提亲道具(可能退还)
			 */
			//撤回提亲信息 个人/全服
			if ($son_info['tquid'] == 0){
				//全服提亲撤回
				$Sev1Model = Master::getSev1();
				$Sev1Model->delete($this->uid,$sid);
			}else{
				//个人提亲撤回
				$fAct10Model = Master::getAct10($son_info['tquid']);
				$fAct10Model->remove($this->uid,$sid);
			}
			
		}elseif($son_info['state'] == 6){//被拒绝
			//加上返还聘礼 全额
			$dct = 2;
		}else{
			Master::error(SON_CANCEL_ERROR_MARRY);
		}
		
		//加上返还聘礼
		//需求配置
		$son_cn_cfg_info = Game::getcfg_info('son_cn',$son_info['honor']);
		//所需道具
		
		//返回弹窗信息
		$b_item = array();
		if ($son_info['tqitem'] != 2){
			//返还元宝
			$cash_count = $son_cn_cfg_info['zhaoqin'];
            //限时元宝消耗活动
            $xh_cash = floor($cash_count*0.2);
			if ($dct = 1){
				//打折
				$cash_count = floor($cash_count*0.8);
			}
			Master::add_item($this->uid,KIND_ITEM,1,$cash_count);
			$b_item = array(
				'id' => 1,
				'count' => $cash_count,
			);
            //限时元宝消耗活动
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->xianshi_huodong('cash',$xh_cash);

            //活动296 - 挖宝锄头-每日任务
            $Act296Model = Master::getAct296($this->uid);
            $Act296Model->get_chutou_task(9,$xh_cash);

		}else{
			//返还道具
			Master::add_item($this->uid,KIND_ITEM,$son_cn_cfg_info['itemid'],1);
			$b_item = array(
				'id' => $son_cn_cfg_info['itemid'],
				'count' => 1,
			);
		}
		
		//更新我的状态
		$s_update = array(
			'sonuid' => $sid,
			'state' => 4,	//单身屌丝中
			
			'tquid' => 0,	//提亲UID 0 则全服
			'tqitem' => 0,	//1元宝 2道具
			'tqnext' => 0,//提亲时间 
		);
		$SonModel->update($s_update);
		
		//返回获得道具弹窗?
		Master::$bak_data['a']['son']['win']['backitem'] = $b_item;
	}

    /*
     * 进入徒弟历练
     */
    public function intoLilian(){
        //历练席位信息
        $Act6132Model = Master::getAct6132($this->uid);
        $Act6132Model->back_data();
        //历练信息
        $Act6133Model = Master::getAct6133($this->uid);
		$Act6133Model->back_data();
		
		$Act766Model = Master::getAct766($this->uid);
        $Act766Model->getTodayDirection();
    }

    /*
     * 选择徒弟历练
     */
    public function liLianSon($params){
        //子嗣ID
        $sid = Game::intval($params,'sid');
        //位置
        $did = Game::intval($params,'did');
        //出行方式
        $travel = Game::intval($params,'travel');
        //行李
        $luggage = Game::intval($params,'luggage');
        //当前智力总属性
        $localep2 = Game::intval($params,'localep2');

        $Act6133Model = Master::getAct6133($this->uid);
        $Act6133Model->play($sid,$did,$travel,$luggage,$localep2);

        //限时-徒弟历练次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6178',1);

        //舞狮大会 - 徒弟历练次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(16,1);
    }

    /*
     * 一键选择徒弟历练
     */
    public function yjLiLianSon($params){
        Master::vip_limit($this->uid,5,'LOOK_FOR_VIP_LEVEL_SHORT');
        $arr = Game::arrayval($params,'arr');
        if (empty($arr)){
            Master::error(PARAMS_ERROR);
        }
        $Act6133Model = Master::getAct6133($this->uid);
        $num = $Act6133Model->oneKeyPlay($arr);

        //限时-徒弟历练次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6178',$num);

        //舞狮大会 - 徒弟历练次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(16,$num);
    }

    /*
     * 历练结束领取奖励
     */
    public function liLianReward($params){
        //子嗣ID
        $sid = Game::intval($params,'sid');
        //位置
        $did = Game::intval($params,'did');
        //领取奖励
        $Act6133Model = Master::getAct6133($this->uid);
        $Act6133Model->recall($sid,$did);
    }

    /*
     * 一键历练结束领取奖励
     */
    public function yjLiLianReward(){
        Master::vip_limit($this->uid,4,'LOOK_FOR_VIP_LEVEL_SHORT');
        //领取奖励
        $Act6133Model = Master::getAct6133($this->uid);
        $Act6133Model->oneKeyRecall();
    }

    /*
     * 购买历练席位
     */
    public function buyLilianSeat(){
        //历练席位类
        $Act6132Model = Master::getAct6132($this->uid);
        //加席位
        $Act6132Model->add_seat();

    }

    /*
     * 删除已读历练书信
     */
    public function delReadMail(){

        $Act6134Model = Master::getAct6134($this->uid);
        $Act6134Model->clearReadMail();
    }

    /*
     * 历练书信返回
     */
    public function liLianMail(){
        //历练席位信息
        $Act6134Model = Master::getAct6134($this->uid);
        $Act6134Model->back_data();
    }

    /*
    *	历练加速完成
    */
   public function speedFinish($params){
   		//sid 徒弟id
   		//did 位置id
   		$sid = Game::intval($params,'sid');
   		$did = Game::intval($params,'did');

   		$Act6133Model = Master::getAct6133($this->uid);
   		$Act6133Model->speedFinish($sid,$did);
   }
}
