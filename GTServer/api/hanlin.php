<?php
//翰林院
class HanlinMod extends Base
{
	/*
	 * 进入翰林院 刷新大厅桌子列表
	 */
	public function listinfo($params){
		//等级限制
		$UserModel = Master::getUser($this->uid);
		if ($UserModel->info['level'] < 7){
			Master::error(LEVEL_LIMIT_SIX);
		}
		
		//翰林信息类
		$Act58Model = Master::getAct58($this->uid);
		//检查自己是否处于学习中 是否触发放学/被踢
		$Act58Model->click_over();
		
		//刷大厅
		$Sev8Model = Master::getSev8();
		$Sev8Model->back_data();
	}
	
	/*
	 * 进入桌子
	 * fuid 桌子编号
	 */
	public function comein($params){
		//等级限制
		$UserModel = Master::getUser($this->uid);
		if ($UserModel->info['level'] < 7){
			Master::error(LEVEL_LIMIT_SIX);
		}
		
		//翰林信息类
		$Act58Model = Master::getAct58($this->uid);
		//检查自己是否处于学习中 是否触发放学/被踢
		if ($Act58Model->click_over()){
			//如果被T了 刷新大厅
			$Sev8Model = Master::getSev8();
			$Sev8Model->back_data();
			return;
		}
		
		//房间号
		$fuid = Game::intval($params,'fuid');
		
		//合服范围限制
		Game::isHeServerUid($fuid);
		
		//桌子信息
		$Sev9Model = Master::getSev9($fuid);
		//检查桌子是否依旧存在
		if ($Sev9Model->click_state(0,true)){
			//房间已到期 刷新大厅
			//刷大厅
			$Sev8Model = Master::getSev8();
			$Sev8Model->back_data();
			Master::error(YAMUN_UNFUND_ENEMY);
			Master::back_s(2);
		}else{
			//刷房间
			//刷房间特殊操作 / 角色冷却信息
			$Sev9Model->back_data($Act58Model->info['tcode']);//弹窗
		}
	}
	
	/*
	 * 开桌子
	 */
	public function opendesk($params){
		//等级限制
		$UserModel = Master::getUser($this->uid);
		if ($UserModel->info['level'] < 7){
			Master::error(LEVEL_LIMIT_SIX);
		}
		
		//翰林信息类
		$Act58Model = Master::getAct58($this->uid);
		$Act58Model->click_state(0);//是否空闲中
		
		//扣除道具
		Master::sub_item($this->uid,1,133,1);
		
		//开桌子
		$fUser = Master::fuidInfo($this->uid);
		$fUser['num2'] = Game::get_now();//开学时间
		$fUser['num'] = Game::get_over(10800);//放学时间
        $fUser['suoding'] = 1;//可以锁定次数
		
		//大厅加座
		$Sev8Model = Master::getSev8();
		$Sev8Model->add($fUser);
		//刷大厅?
		
		//座位初始化
		$Sev9Model = Master::getSev9($this->uid);
		$Sev9Model->open($fUser);
		//log
		$Sev9Model->addlog(
			array(
				'name1' => $fUser['name'],
				'type' => 1,
			)
		);
		$Sev9Model->back_data();//弹窗
		//个人信息更新
		$Act58Model->sitdown($fUser);

        //活动293 - 获得骰子-演武场
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(4,1);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(4,1);

        //主线任务 ---   开启校场	开启X次校场
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(45, 1);

	}
	
	/*
	 * 加入空位置
	 * fuid = 桌编号
	 * rid = 坑编号 1~3
	 */
	public function sitdown($params){
		//等级限制
		$UserModel = Master::getUser($this->uid);
		if ($UserModel->info['level'] < 7){
			Master::error(LEVEL_LIMIT_SIX);
		}
		
		//翰林信息类
		$Act58Model = Master::getAct58($this->uid);
		$Act58Model->click_state(0);//是否空闲中
		
		
		//自己是否冷却中
		if (!Game::is_over($Act58Model->info['ctime'])){
			Master::error(HANLIN_XIUXI);
		}
		
		
		//参数
		$fuid = Game::intval($params,'fuid');//桌子编号
        if ($fuid == $this->uid) {
            Master::error(ACT_58_XIWU.__LINE__);
        }
		$rid = Game::intval($params,'rid');//坑位编号
		
		//座位号合法
		if(!in_array($rid,array(1,2,3))){
			Master::error(DESK_WRONG);
		}
		//合服范围限制
		Game::isHeServerUid($fuid);
		
		//坐下信息
		$user_my = Master::fuidInfo($this->uid);
		$user_my['rid'] = $rid;//座位
		
		//坐到房间座位上
		$Sev9Model = Master::getSev9($fuid);
		$Sev9Model->click_state(1);//当前是否非空房
		if (isset($Sev9Model->info['desk'][$rid])){//座位是否为空
			Master::error(DESK_HAVE_PEOPEL);
		}
		$Sev9Model->sitdown($user_my);
		//log
		$Sev9Model->addlog(
			array(
				'name1' => $user_my['name'],
				'type' => 2,
			)
		);
		$Sev9Model->back_data();//弹窗 刷新房间
		//更新个人信息
		$Act58Model->sitdown($Sev9Model->info['master']);//坐下
		//更新大厅信息
		$Sev8Model = Master::getSev8();
		$Sev8Model->update($fuid,count($Sev9Model->info['desk']),$this->uid);
		//刷新大厅信息?
		
	}
	
	/*
	 * T 人
	 * fuid = 桌编号
	 * rid = 坑编号 1~3
	 * uid = 对方UID
	 */
	public function ti($params){
		//等级限制
		$UserModel = Master::getUser($this->uid);
		if ($UserModel->info['level'] < 7){
			Master::error(LEVEL_LIMIT_SIX);
		}
		
		//翰林信息类
		$Act58Model = Master::getAct58($this->uid);
		$Act58Model->click_state(0);//是否空闲中
		
		//参数
		$fuid = Game::intval($params,'fuid');//桌子编号
		$rid = Game::intval($params,'rid');//坑位编号
		$uid = Game::intval($params,'uid');//人编号
		
		if ($uid == $this->uid){
			Master::error(HANLIN_SELF);
		}
		
		//自己是否冷却中
		if (!Game::is_over($Act58Model->info['ctime'])){
			Master::error(HANLIN_XIUXI);
		}
		//单独保护中?
		if (!Game::is_over($Act58Model->info['tcode'][$uid])){
			Master::error(HANLIN_JIAOLIANG);
		}
		
		//座位号合法
		if(!in_array($rid,array(1,2,3))){
			Master::error(DESK_WRONG);
		}
		//合服范围限制
		Game::isHeServerUid($fuid);
		
		//验证房间号合法
		$Sev9Model = Master::getSev9($fuid);
		$Sev9Model->click_state(1);
		
		//验证座位上有人
		if(!isset($Sev9Model->info['desk'][$rid])){
			Master::error_msg(HANLIN_NO_PEOPEL);
			$Sev9Model->back_data();//弹窗 刷新房间
			return;
		}
		//延迟 人变了
		if ($Sev9Model->info['desk'][$rid]['uid'] != $uid){
			Master::error(HANLIN_DESK_CHANGE);
			return;
		}

		
		//打架
		$UserModel = Master::getUser($this->uid);
		$fuser = $Sev9Model->info['desk'][$rid];
		if ($fuser['level'] > $UserModel->info['level']){
			Master::error(HERO_LEVEL_SHORT);
		}
		
		
		//这个人被保护中
		if (!Game::is_over($fuser['num'])){
			Master::error(HANLIN_PROTECT);
		}
		
		//名字安全化 用来获取名字
		$User_my = Master::fuidInfo($this->uid);
			
		//政绩对比
		if ( $UserModel->info['level'] > $fuser['level']
		 || $UserModel->info['exp'] > $fuser['exp']){
			//赢了
			$win = 1;
			
			//坐下信息
			$user_my = Master::fuidInfo($this->uid);
			$user_my['rid'] = $rid;//座位
			//坐下
			$Sev9Model->sitdown($user_my);
			$Sev9Model->back_data();//弹窗 刷新房间
			//log
			$Sev9Model->addlog(
				array(
					'name1' => $User_my['name'],
					'name2' => $fuser['name'],
					'type' => 3,
				)
			);
			//更新个人信息
			$Act58Model->sitdown($Sev9Model->info['master']);//坐下
			
			//被T者 信息改变
			$fAct58Model = Master::getAct58($fuser['uid']);
			$fAct58Model->ti($this->uid);//被T
			
		}else{
			$win = 0;
			//输了 冷却时间
			$Act58Model->ti_code($uid);//这个人10分钟内不能再T
			
			//输的日志
			$Sev9Model->addlog(
				array(
					'name1' => $User_my['name'],
					'name2' => $fuser['name'],
					'type' => 4,
				)
			);
		}
		//刷新房间信息
		$Sev9Model->back_data();
		
		//插入大厅信息 用来找BUG
		//更新大厅信息
		$Sev8Model = Master::getSev8();
		$Sev8Model->update($fuid,count($Sev9Model->info['desk']),$this->uid);
			
		//战斗弹窗
		$tif_win = array(
			'win' => $win,
			'fuser1' => Master::fuidInfo($this->uid),
			'fuser2' => $fuser,
		);
		Master::$bak_data['a']['hanlin']['win']['tif'] = $tif_win;
	}
	
	/*
	 * 升级翰林技能
	 */
	public function upskill($params){
		//获取配置
		
		//翰林信息类
		$Act58Model = Master::getAct58($this->uid);
		$Act58Model->upskill();
		
	}
	
	/*
	 * 搜索房间
	 */
	public function find($params){
		//等级限制
		$UserModel = Master::getUser($this->uid);
		if ($UserModel->info['level'] < 7){
			Master::error(LEVEL_LIMIT_SIX);
		}
		
		//房间号
		$fuid = Game::intval($params,'fuid');
		
		//合服范围限制
		Game::isHeServerUid($fuid);
		
		//大厅
		$Sev8Model = Master::getSev8();
		//$Sev8Model->back_data();
		
		if (isset($Sev8Model->info[$fuid])	//如果房间存在 
		&& !Game::is_over($Sev8Model->info[$fuid]['num'])){//并且还未超时
			//刷房间
			$fuser = $Sev8Model->info[$fuid];
			//显示人数+1
			$fuser['num2'] += 1;
			Master::$bak_data['a']['hanlin']['win']['find'] = $fuser;
		}else{
			Master::error(HANLIN_BUCUNZAI);
		}
	}
	
	public function suoding($params){
        //参数
        $fuid = Game::intval($params,'fuid');//桌子编号
        $rid = Game::intval($params,'rid');//坑位编号
        $uid = Game::intval($params,'uid');//人编号


        //验证房间号合法
        $Sev9Model = Master::getSev9($fuid);
        $Sev9Model->click_state(1);

        //验证自己是否为房间主人
        if($this->uid != $Sev9Model->info['master']['uid']){
            Master::error(CLUB_NO_BANGZHU);
        }
        //验证座位上有人
        if(!isset($Sev9Model->info['desk'][$rid])){
            Master::error_msg(HANLIN_NO_PEOPEL);
            $Sev9Model->back_data();//弹窗 刷新房间
            return;
        }
        //延迟 人变了
        if ($Sev9Model->info['desk'][$rid]['uid'] != $uid){
            Master::error(HANLIN_DESK_CHANGE);
            return;
        }
        //更新保护时间
        $Sev9Model->baohu($rid);
    }
}
