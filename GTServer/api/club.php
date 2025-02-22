<?php
//联盟操作
class ClubMod extends Base
{

    public function __construct($uid)
    {
        parent::__construct($uid);

        $UserModel = Master::getUser($this->uid);
        $flag = Game::is_limit_level('club', $this->uid, $UserModel->info['level']);
        if ($flag == 2 && $UserModel->info['level'] < 6) {
            //默认限制正7开启
            // Master::error(FRIEND_LIMIT_QI);
        }
    }


    public function check($k_ctrl)
    {
        $lock_cfg = Game::getBaseCfg('lock');
        $k_mod = 'club';
        if (!empty($lock_cfg[$k_mod][$k_ctrl]['key_arg'])) {
            $Act40Model = Master::getAct40($this->uid);
            $cid = $Act40Model->info['cid'];
            if (!empty($cid)) {
                Master::get_lock($lock_cfg[$k_mod][$k_ctrl]['type'], $lock_cfg[$k_mod][$k_ctrl]['key_arg'].'_'.$cid);
            }
        }
        return parent::check($k_ctrl);
    }

    /**
	----------------------------------------------------------公会操作-----------------------------------------------------------------
	 */
	/**
	 * 创建联盟
	 * @param $params
	 * $params['name'] :联盟名称(必填)
	 * $params['weixin'] :微信
	 * $params['qq'] :QQ
	 * $params['password'] :密码
	 * $params['outmsg'] :对外公告
	 * $params['isJoin'] :是否允许其他玩家随机加入1:是 0:否
	 */
	public function clubCreate($params)
	{
		$params['weixin'] = empty($params['weixin'])?'':$params['weixin'];
		$params['weixin'] = empty($params['laoma'])?$params['weixin']:$params['laoma'];
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(!empty($cid)){
			Master::error(CLUB_HAVE_JOIN);
		}
		if(empty($params['name'])){
			Master::error(CLUB_NAME_NOT_NULL);
		}
		//扣除消费 2000元宝
		Master::sub_item($this->uid,KIND_ITEM,1,Game::getcfg_param("union_build_cost"));
		//获取联盟id
		$data = array(
			'name' => $params['name'],
			'weixin' => $params['weixin'],
			'qq' => empty($params['qq'])?'':$params['qq'],
			'password' => $params['password'],
			'outmsg' => $params['outmsg'],
			'isJoin' => empty($params['isJoin'])?'':$params['isJoin'],//1:是 0:否
		);
		//创建并且返回联盟id
		Common::loadModel("ClubModel");
		$cid = ClubModel::create_club($data);

		//更新联盟成员信息
		$ClubModel = Master::getClub($cid);
		$ClubModel->check_member();
		//加入联盟
 		$ClubModel->join_club($this->uid,1);

		//更新公会个人信息
 		$Act40Model->inClub($cid,0);

		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);

		// 更新公会排行榜
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(4);//返回门客信息.

        //创建帮会-跑马灯
        $UserInfo = Master::fuidInfo($this->uid);
        $Sev91Model = Master::getSev91();
        $Sev91Model->add_msg(array(107,Game::filter_char($UserInfo['name']),Game::filter_char($params['name'])));
	}

	/**
	 * 查看公会信息
	 * @param unknown_type $params
	 */
	public function clubInfo($params)
	{
		//我的公会
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Act40Model->back_data();
		//公会信息
		$ClubModel = Master::getClub($cid);
		if (empty($ClubModel->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$h_info = $ClubModel->getBase();
		$isOpen = empty($Act40Model->info['password'])?0:1;
		$mypost = $ClubModel->info['members'][$this->uid]['post']; //我的职位
		if(empty($mypost)){
			Master::error(CLUB_NO_DATA);
		}

		$h_info['pwdTip'] = $mypost == 1?$isOpen:0; //只有盟主才能提示
		Master::back_data($this->uid,'club','clubInfo',$h_info);

		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->bake_data();

		$Sev12Model = Master::getSev12($cid);
		$Sev12Model->bake_data();
	}

	public function getMyCid(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		Master::back_data($this->uid,'club','myCid',array('cid' => $cid));
	}

	/**
	 * 获取自己的公会信息
	 * @param unknown_type $params
	 */
	public function getUserClubInfo($params)
	{
		//我的公会
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		//联盟boss
        $Sev12Model = Master::getSev12($cid);
		$Sev12Model->bake_data();
		
		$Sev17Model = Master::getSev17($cid);
		$Sev17Model->bake_data();

		$Sev11Model = Master::getSev11();
		$list = $Sev11Model->apply_list($cid);
		Master::back_data($this->uid,'club','applyList',$list);

		$ClubModel = Master::getClub($cid);
		$mypost = $ClubModel->info['members'][$this->uid]['post']; //我的职位
		if(empty($mypost)){
			$Act40Model->qz_out($cid,$mypost);
		}else{
			$h_info = $ClubModel->getBase();
			Master::back_data($this->uid,'club','clubInfo',$h_info);

			$Sev15Model = Master::getSev15($cid);
			$Sev15Model->bake_data();
		}

		// 收纳府-任务
		$Act761Model = Master::getAct761($this->uid);
		$Act761Model->back_data();
		
		$Act760Model = Master::getAct760($this->uid);
		$Act760Model->back_data();

        $cidKey = $cid."_".Game::get_today_long_id();
        // 公会进贡排行榜
        $Redis19Model = Master::getRedis19($cidKey);
        $Redis19Model->back_data();

        // 公会贡献排行榜
        $Redis18Model = Master::getRedis18($cidKey);
        $Redis18Model->back_data();
	}

	/**
	 * 联盟名字修改
	 * @param unknown_type $params
	 * $params['name'] : 联盟名字
	 * $params['type'] : 类型 0:元宝改名 1:道具改名
	 */
	public function clubName($params)
	{
		$name = $params['name'];
		$type = Game::intval($params,'type');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);

		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1))){
			Master::error(CLUB_MODIFY_ONLY_LEADER);
		}
		//扣除消费 500元宝
		if(empty($type)){//元宝改名
			Master::sub_item($this->uid,KIND_ITEM,1,100);
		}else{//道具改名
			Master::sub_item($this->uid,KIND_ITEM,140,1);
		}

		$data = array(
			'name' => $name,
		);
		$ClubModel->update($data);
		Game::addClubName($cid,$name);
		//记录公会日志
		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(6,$this->uid);
	}

	/**
	 * 联盟密码修改
	 * @param unknown_type $params
	 * $params['password'] : 联盟密码
	 */
	public function clubPwd($params)
	{
		$password = $params['password'];
		if(empty($password)){
			Master::error(CLUB_PWD_NOT_NULL);
		}

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);

		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1))){
			Master::error(CLUB_MODIFY_ONLY_LEADER);
		}
		//设置公会密码弹窗
		$Act40Model->pwd_tip(0);
		$data = array(
			'password' => $password,
		);
		$ClubModel->update($data);

		Master::error_msg('修改成功');
	}

	/**
	 * 联盟信息保存
	 * @param unknown_type $params
	 * $params['weixin'] : 微信群
	 * $params['qq'] : QQ群
	 * $params['outmsg'] : 对外宣言
	 * $params['notice'] : 公告
	 */
	public function clubInfoSave($params)
	{
		$params['weixin'] = empty($params['weixin'])?'':$params['weixin'];
		$params['weixin'] = empty($params['laoma'])?$params['weixin']:$params['laoma'];
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);

		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_MODIFY);
		}

		//记录公会日志
		if($params['notice'] != $ClubModel->info['notice']){
			$Sev15Model = Master::getSev15($cid);
			$Sev15Model->add_log(2,$this->uid);
		}

		$data = array(
			'weixin' => $params['weixin'],
			'qq' => empty($params['qq'])?0:$params['qq'],
			'outmsg' => empty($params['outmsg'])?'':$params['outmsg'],
			'notice' => empty($params['notice'])?'':$params['notice'],
		);
		$ClubModel->update($data);
	}

	/**
	 * 职位变更/逐出联盟
	 * @param unknown_type $params
	 * $params['fuid'] 玩家id
	 * $params['postid'] 1:盟主 2:副盟主 3: 精英   4:成员    5:逐出联盟
	 */
	public function memberPost($params)
	{
		$fuid = Game::intval($params,'fuid');
		$postid = Game::intval($params,'postid');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		$myPost = $ClubModel->info['members'][$this->uid]['post'];
		$fpost = $ClubModel->info['members'][$fuid]['post'];
		if($myPost == 2 && $myPost >= $fpost){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}
		if(!in_array($myPost,array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}
		if($myPost >= $postid){
			Master::error(CLUB_PERMISSION_DENIED_MODIFY);
		}
		if($fuid == $this->uid){
			Master::error(CLUB_PERMISSION_DENIED_USER);
		}

		$Sev15Model = Master::getSev15($cid);
		if($postid == 5){// 5:逐出联盟
			$Act40Model = Master::getAct40($fuid);
			$Act40Model->outClub($cid);
			$ClubModel->goout_club($fuid);

			//记录公会日志
			$Sev15Model->add_log(7,$fuid,$this->uid,$myPost,$fpost);
			$date = date('Y', $_SERVER['REQUEST_TIME']).'年'.date('m', $_SERVER['REQUEST_TIME']).'月'.date('d', $_SERVER['REQUEST_TIME']).'日'.date('H', $_SERVER['REQUEST_TIME']).'时';
			$content = $date.'|'.MAIL_CLUB_CONTENT_HEAD.'|'.$ClubModel->info['name'].'|'.MAIL_CLUB_CONTENT_FOOT;
			//被T邮件下发
			$mailModel = Master::getMail($fuid);
            $mailModel->sendMail($fuid, CLUB_T_TITLE, $content, 0, '');

            // 退出公会排行榜
			$this->outClubRank($fuid);

		}else{ //1:盟主 2:副盟主 3: 精英   4:成员

			$Act40Model = Master::getAct40($fuid);
			if($Act40Model->info['cid'] != $cid){
				Master::error(CLUB_YITUIBANG);
			}
			$ClubModel->postlimit($postid);
			$ClubModel = Master::getClub($cid);
			$ClubModel->info['members'][$fuid]['post'] = $postid;
			$data = array(
				'members' => $ClubModel->info['members'],
			);
			$ClubModel->update($data);

			//记录公会日志
			$Sev15Model->add_log(5,$fuid,$this->uid,$myPost,$postid);

			//改变帮会战职位
			$Sev51Model = Master::getSev51($cid);
			$Sev51Model->reset_post($fuid);

		}
	}
	/*
	 * 帮主转让列表
	 * */
	public function transList(){
	    //我的公会
	    $Act40Model = Master::getAct40($this->uid);
	    $cid = $Act40Model->info['cid'];
	    if(empty($cid)){
	        Master::error(CLUB_IS_NULL,1);
	    }
	    //公会信息
	    $ClubModel = Master::getClub($cid);
	    $h_info = $ClubModel->getBase();
	    //获取信息
	    $transList = array();
	    foreach($h_info['members'] as $val){
	        if($val['post'] == 2 && (time() - $val['loginTime'] < 3*24*60*60)){
	            $transList[] = $val;
	        }
	    }
	    Master::back_data($this->uid,'club','transInfo',$transList);
	}
	/*
	 * 转让帮主
	 * */
	public function transWang($params)
	{
	    $fuid = Game::intval($params,'fuid');//转让的UId
	    $password = Game::intval($params,'password');
	    $Act40Model = Master::getAct40($this->uid);
	    $cid = $Act40Model->info['cid'];
	    if(empty($cid)){
	        Master::error(CLUB_IS_NULL,1);
	    }
	    $ClubModel = Master::getClub($cid);
	    //判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他
	    $myPost = $ClubModel->info['members'][$this->uid]['post'];
	    $fpost = $ClubModel->info['members'][$fuid]['post'];
	    if($myPost != 1){
	        Master::error(CLUB_NO_BANGZHU);
	    }
	    if($fpost != 2){
	        Master::error(CLUB_ZHUAN_FUBANG);
		}
		
		$club_param = Game::getcfg_info('club_param', 2);
		$cost = $club_param['param'];

	    if($ClubModel->info['fund'] < $cost){
	        Master::error(CLUB_CAIFU_BUZU);
	    }

	    //判断密码是否正确
	    if($ClubModel->info['password'] != $password){
	        Master::error(CLUB_PWD_ERROR);
	    }

	    $h_info = $ClubModel->getBase();
	    //获取信息
	    $transList = array();
	    foreach($h_info['members'] as $val){
	        if($val['post'] == 2 && (time() - $val['loginTime'] < 3*24*60*60)){
	            $transList[] = $val['id'];
	        }
	    }
	    if(empty($transList)){
	        Master::error(CLUB_NO_FUBANG);
	    }
	    if(!in_array($fuid, $transList)){
	        Master::error(CLUB_NO_ZHUANGLANG);
	    }

	    $Sev15Model = Master::getSev15($cid);

        $ClubModel->info['members'][$fuid]['post'] = 1;
        $ClubModel->info['members'][$this->uid]['post'] = 4;
        $data = array(
            'fund' => $ClubModel->info['fund'] - $cost,
            'members' => $ClubModel->info['members'],
        );
        $ClubModel->update($data);

        //记录公会日志
        $Sev15Model->add_log(5,$fuid,$this->uid,$fpost,1);

        //工会信息
        $h_info = $ClubModel->getBase();
        Master::back_data($this->uid,'club','clubInfo',$h_info,true);
        $Act40Model->n_update(array('n_post' => 4));

        //改变帮会战职位
		$Sev51Model = Master::getSev51($cid);
		$Sev51Model->reset_post($this->uid);
		$Sev51Model->reset_post($fuid);

		//设置公会密码弹窗
		$fAct40Model = Master::getAct40($fuid);
		$fAct40Model->pwd_tip(1);
	}

	/**
	 * 公会建筑升级
	 */
	public function cluBuildingUp($params)
	{

		$bId = Game::intval($params,'bId');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$ClubModel = Master::getClub($cid);
		$myPost = $ClubModel->info['members'][$this->uid]['post'];
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}

		$buildInfo = $ClubModel->getBuildInfo($bId);
		if ($ClubModel->info["level"] < $buildInfo["need"]) {
			Master::error(CLUB_BUILD_LEVEL_UP_ERR);
		}

		if (empty($buildInfo["cost"])) {
			Master::error(CLUB_BUILD_LEVEL_UP_MAX);
		}

		foreach ($buildInfo['cost'] as $key => $cost) {
			Master::sub_item2($cost);
		}

		$newLv = $ClubModel->buildLevelUp($bId);
		$myPost = $ClubModel->info['members'][$this->uid]['post'];

		//记录公会日志
		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(13,$this->uid,$this->uid,$myPost,$bId,$newLv);

		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);
	}

	/**
	----------------------------------------------------------公会操作END-----------------------------------------------------------------
	 */

	/**
	----------------------------------------------------------加入公会-----------------------------------------------------------------
	 */

	/**
	 * 随机入盟
	 * @param unknown_type $params
	 */
	public function clubRand($params)
	{
        //判断是否已经有联盟
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        if(!empty($cid)){
            Master::error(CLUB_HAVE_JOIN);
        }
        //退宫24小时不能再进
        // if($_SERVER['REQUEST_TIME'] -  $Act40Model->info['outTime'] < 3600 ){
        //     Master::error(CLUB_QUIT_TIME_TIPS);
        // }
		$Redis10Model = Master::getRedis10();
		$clubs = $Redis10Model->out_redis();
		$join = array();
		foreach($clubs as $info){
            $cfg_club = Game::getcfg_info('club',$info['level']);
			if( !empty($info['isJoin']) && $info['num']<$cfg_club['maxMember']){
				$join[] = $info['id'];
			}
		}
		if(empty($join)){
			Master::error(CLUB_IS_EMPTY);
		}
		$randid = array_rand($join,1);
		if(empty($join[$randid])){
			Master::error(CLUB_IS_EMPTY);
		}
		
		//随机到工会ID是否合工会ID
		Game::isHeServerClubid($join[$randid]);
		
		//验证联盟人数
		$ClubModel = Master::getClub($join[$randid]);
		//检验工会是否真的能随机进入
		if(!$ClubModel->isRandomJoin()){
			Master::error(CLUB_IS_EMPTY);
		}
		if (empty($ClubModel->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$ClubModel->check_member();
		
		$Act40Model = Master::getAct40($this->uid);
		$Act40Model->check_my(1);
		//加入联盟
		$ClubModel->join_club($this->uid,4);
		//更新
		$Act40Model->inClub($join[$randid],1);
		
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);
		
		//记录公会日志
		$Sev15Model = Master::getSev15($join[$randid]);
		$Sev15Model->add_log(11,$this->uid,$this->uid);
	}

	/**
	 * 查询联盟
	 * @param unknown_type $params
	 * $params['cid'] : 联盟id
	 */
	public function clubFind($params)
	{
		$cid = Game::intval($params,'cid');
		if(empty($cid)){
			Master::error(CLUB_IS_NULL);
		}
		//工会ID是否合工会ID
		Game::isHeServerClubid($cid);

		$ClubModel = Master::getClub($cid);
		if (empty($ClubModel->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info,true);
	}

	/**
	 * 申请入盟
	 * @param unknown_type $params
	 * $params['cid'] : 联盟id
	 */
	public function clubApply($params)
	{
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(!empty($Act40Model->info['cid'])){
			Master::error(CLUB_HAVE_JOIN);
		}

		// if($_SERVER['REQUEST_TIME'] -  $Act40Model->info['outTime'] < 3600 ){
		// 	Master::error(CLUB_QUIT_TIME_TIPS);
		// }

		$cid = Game::intval($params,'cid');

		//工会ID是否合工会ID
		Game::isHeServerClubid($cid);

		$ClubModel = Master::getClub($cid);
		if (empty($ClubModel->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		//判断该联盟的入盟状态 不需审核 和需要审核
		if($ClubModel->info['isJoin'] == 1){
			$ClubModel->check_member();
			$Act40Model = Master::getAct40($this->uid);
			$Act40Model->check_my(1);
			//更新联盟成员信息
		    $ClubModel->join_club($this->uid,4);
		    //更新
		    $Act40Model->inClub($cid,1);

		    $h_info = $ClubModel->getBase();
			Master::back_data($this->uid,'club','clubInfo',$h_info);

		}else{
    		$Sev11Model = Master::getSev11();
    		$Sev11Model->add_apply($this->uid,$cid);
    		Master::error(CLUB_SUCCESS_APPLICATION);
		}

		$Act8020Model = Master::getAct8020($this->uid);
		$Act8020Model->del_invitation_user($cid);
	}

	/**
	 * 联盟榜单
	 * @param unknown_type $params
	 */
	public function clubList($params)
	{

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];

		$Redis10Model = Master::getRedis10();
		$Redis10Model->back_data();
		$Redis10Model->back_data_my($cid);
	}

	/**
	 * 是否允许其他玩家随机加入
	 * @param unknown_type $params
	 * $params['join'] : 是否允许其他玩家随机加入-加入
	 */
	public function isJoin($params)
	{
		$join = Game::intval($params,'join');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$ClubModel = Master::getClub($cid);
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_MODIFY);
		}

		$data = array(
			'isJoin' => $join,
		);
		$ClubModel->update($data);
	}

	/**
	 * 申请列表
	 * @param unknown_type $params
	 */
	public function applyList($params){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Sev11Model = Master::getSev11();
		$list = $Sev11Model->apply_list($cid);
		Master::back_data($this->uid,'club','applyList',$list);
	}

	/**
	 * 拒绝加入联盟
	 * @param unknown_type $params
	 * $params['fuid'] : >0:对应uid/单个拒绝     0:一键拒绝
	 */
	public function noJoin($params)
	{
		$fuid = Game::intval($params,'fuid');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$ClubModel = Master::getClub($cid);
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}

		//指定某个玩家拒绝
		$Sev11Model = Master::getSev11();
		if(!empty($fuid)){
			$Sev11Model->del_apply_user($fuid,$cid);
		}else{  //一键拒绝
			$Sev11Model->del_apply_club($cid);
		}

		$list = $Sev11Model->apply_list($cid);
		Master::back_data($this->uid,'club','applyList',$list);
	}

	/**
	 * 同意加入
	 * @param unknown_type $params
	 * $params['fuid'] : uid   0:一键同意
	 */
	public function yesJoin($params)
	{
		$fuid = Game::intval($params,'fuid');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);
		$Sev11Model = Master::getSev11();
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}
		//获取当前公会列表
		$applyList = $Sev11Model->info;
		$tempList = array();
		if(!empty($fuid)){
			array_push($tempList,$fuid);
		}else{
			foreach($applyList as $Kfuid => $kInfo){
				array_push($tempList,$Kfuid);
			}
		}

		$addCount = count($tempList);
		
		//判断联盟人员是否已满 
		$cfg_club_id = Game::getcfg_info('club',$ClubModel->info['level']);
		$maxMember = empty($cfg_club_id['maxMember'])?0:$cfg_club_id['maxMember'];
		if($maxMember <= count($ClubModel->info['members'])){
			//人员满了  拒绝掉所以申请的人员
			$Sev11Model->del_apply_club($cid);
			Master::error(CLUB_PERSON_IS_FULL);
		}
		//一键同意时判断人数是否会超出
		if($maxMember < count($ClubModel->info['members']) + $addCount){
			//人员满了  拒绝掉所以申请的人员
			Master::error(CLUB_PERSON_IS_FULL);
		}
		
		foreach($tempList as $index => $fuid){
			//UID是否和服范围内
			Game::isHeServerUid($fuid);
			$Act40Model = Master::getAct40($fuid);
			$fcid = $Act40Model->info['cid'];
			if(!empty($fcid)){
				Master::error(CLUB_HAVE_JOIN);
			}
			//删除该玩家的所以联盟申请
			$Sev11Model->del_apply_user($fuid,0);
			//该玩家加入公会
			$Act40Model = Master::getAct40($fuid);
			$Act40Model->check_my(1);
			$ClubModel->check_member();
			//更新联盟成员信息
			$ClubModel->join_club($fuid,4);
			$Act40Model->inClub($cid,1);

		}
		
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		$Sev11Model = Master::getSev11();
		$list = $Sev11Model->apply_list($cid);
		Master::back_data($this->uid,'club','applyList',$list);

		//记录公会日志
		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(9,$fuid);
	}

	/**
	 * 邀请入盟
	 * @param unknown_type $params
	 * $params['cid'] : 联盟id
	 */
	public function clubInvitation($params)
	{

		$fUid = Game::intval($params,'fUid');
		//判断是否已经有联盟
		$fAct40Model = Master::getAct40($fUid);
		if(!empty($fAct40Model->info['cid'])){
			Master::error(CLUB_HAVE_JOIN);
		}

		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}

		$cid = $Act40Model->info['cid'];
		$ClubModel = Master::getClub($cid);
		if (empty($ClubModel->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}

		//玩家信息
		$fuidInfo = Master::fuidInfo($this->uid);
		//邀请列表
        $Act8020Model = Master::getAct8020($fUid);
        $Act8020Model->add_invitation($this->uid, $fuidInfo['name'], $cid, $ClubModel->info['name']);

		Master::error(CLUB_SUCCESS_INVITATION);
	}

	/**
	 * 忽略邀请
	 * @param unknown_type $params
	 */
	public function invitationRefuse($params){

		$cid = Game::intval($params,'cid');

		$Act8020Model = Master::getAct8020($this->uid);
		$Act8020Model->del_invitation_user($cid);
	}

	/**
	 * 退出联盟
	 * @param unknown_type $params
	 */
	public function outClub($params)
	{
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		$ClubModel = Master::getClub($cid);
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(in_array($ClubModel->info['members'][$this->uid]['post'],array(1))){
			Master::error(CLUB_LEADER_NOT_QUIT);
		}
		$Act40Model = Master::getAct40($this->uid);
		$Act40Model->outClub($cid);
		$ClubModel->goout_club($this->uid);

		//记录公会日志
		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(10,$this->uid,$this->uid);

		// 退出公会排行榜
		$this->outClubRank($this->uid);
	}

	//提前解散工会 有24小时冷却时间
	public function pre_delClub($params){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);

		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}

		if($ClubModel->info['dissolutionTime'] > 0){
			Master::error(CLUB_IS_DEL);
		}
		$clubParamCfg = Game::getcfg_info('club_param',3);
		$clostTime = $clubParamCfg['param'];
		$now = Game::get_now();
		$data = array(
			'dissolutionTime' => $now + $clostTime,
		);
		$ClubModel->update($data);
	}

	//提前解散工会 有24小时冷却时间
	public function cancel_delClub($params){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);

		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}

		if($ClubModel->info['dissolutionTime'] <= 0){
			Master::error(CLUB_IS_NOT_DEL);
		}
		$data = array(
			'dissolutionTime' => 0,
		);
		$ClubModel->update($data);
	}

	//确定解散 时间到了直接解散
	public function delClub($params){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);
		$ClubModel->delClub();
	}

	/**
	----------------------------------------------------------加入公会END-----------------------------------------------------------------
	 */

	/**
	----------------------------------------------------------收纳府-----------------------------------------------------------------
	 */

	/**
	 * 每日贡献
	 * @param unknown_type $params
	 * $params['dcid'] :档次id
	 */
	public function getShouNaInfo($params)
	{

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		//每日贡献排行
		$Redis18Model = Master::getRedis18($cid."_".Game::get_today_long_id());
		$Redis18Model->back_data();

		//每日进贡排行
	    $Redis19Model = Master::getRedis19($cid."_".Game::get_today_long_id());
	    $Redis19Model->back_data();

	    $ClubModel = Master::getClub($cid);
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);
	}

	/**
	 * 每日贡献
	 * @param unknown_type $params
	 * $params['dcid'] :档次id
	 */
	public function dayGongXian($params)
	{
		$dcid = Game::intval($params,'dcid');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$ClubModel = Master::getClub($cid);
		$club_donate = Game::getcfg_info('club_donate',$dcid);
		$gxMax = $club_donate['time'];

		//贡献档次
		$Sev10Model = Master::getSev10($cid);
		if(!empty($Sev10Model->info[$this->uid][$dcid]) && $Sev10Model->info[$this->uid][$dcid] >= $gxMax){
			Master::error(CLUB_TODAY_BUILDED);
		}

		//扣除道具
		$costId = 0;
		$costNum = 0;
		foreach($club_donate['pay'] as $pay){
			$costId = $pay['id'];
			$costNum = $pay['count'];
			Master::sub_item($this->uid,KIND_ITEM,$pay['id'],$pay['count']);
		}

		//加入建设信息
		$Sev10Model->add_gx_list($this->uid,$dcid, $gxMax);

		$buildInfo = $ClubModel->getBuildInfo(1);
		//加获得奖励
		$allGx = 0;
		$allWw = 0;
		// $items = array();
		// foreach($club_donate['get'] as $k => $v){
		// 	switch($v["id"]){
		// 		case 119:
		// 			$v["count"] = $v["count"] * $buildInfo["exp_buff"];
        // 			$items[] = $v;
		// 			break;
		// 		case 117:
		// 			$v["count"] = $v["count"] * $buildInfo["fund_buff"];
        // 			$items[] = $v;
		// 			$allGx += $v["count"];
		// 			break;
		// 		case 118:
		// 			//加个人剩余贡献  和  总贡献
		// 			$v["count"] = $v["count"] * $buildInfo["ctbt_buff"];
        // 			$items[] = $v;
		// 			break;
		// 	}
		// }
		Master::add_item3($club_donate['get']);

		//记录公会日志
		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(14,$this->uid,0,$costNum,$costId,$allGx);

		//每日贡献排行
		$Redis18Model = Master::getRedis18($cid."_".Game::get_today_long_id());
		if ($allGx > 0) {

	        $Redis18Model->zIncrBy($this->uid,$allGx);
		}

		//每日进贡排行
	    $Redis19Model = Master::getRedis19($cid."_".Game::get_today_long_id());
		if ($allWw > 0) {

	        $Redis19Model->zIncrBy($this->uid,$allWw);
		}


		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(159,1);

        $Redis18Model->back_data();
        $Redis19Model->back_data();
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);
	}

	/**
	 * 每日进贡
	 * @param unknown_type $params
	 * $params['dcid'] :档次id
	 */
	public function dayJinGong($params)
	{
		$dcid = Game::intval($params,'dcid');
		$num = Game::intval($params,'num');

		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$ClubModel = Master::getClub($cid);
		$club_contribution = Game::getcfg_info('club_contribution',$dcid);

		//扣除道具
		$costId = 0;
		$costNum = 0;
		foreach($club_contribution['pay'] as $pay){
			$costId = $pay['id'];
			$costNum = $pay['count'];
			Master::sub_item($this->uid,KIND_ITEM,$pay['id'],$pay['count'] * $num);
		}

		$buildInfo = $ClubModel->getBuildInfo(1);
		//加获得奖励
		$allGx = 0;
		$allWw = 0;
		$allFund = 0;
		$items = array();
		foreach($club_contribution['get'] as $k => $v){
			switch($v["id"]){
				case 119:
					$v["count"] = ceil($v["count"] * $buildInfo["exp_buff"] * $num);
        			$items[] = $v;
					break;
				case 117:
					$v["count"] = ceil($v["count"] * $buildInfo["fund_buff"] * $num);
        			$items[] = $v;
        			$allFund += $v["count"];
					break;
				case 118:
					//加个人剩余贡献  和  总贡献
					$v["count"] = ceil($v["count"] * $buildInfo["ctbt_buff"] * $num);
        			$items[] = $v;
					$allGx += $v["count"];
					break;
			}
		}
		Master::add_item3($items);

		//记录公会日志
		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(12,$this->uid,0,$costNum * $num,$costId,$allWw);

		//每日贡献排行
	    $Redis18Model = Master::getRedis18($cid."_".Game::get_today_long_id());
		if ($allFund > 0) {

	        $Redis18Model->zIncrBy($this->uid,$allFund);
		}

		//每日进贡排行
        $Redis19Model = Master::getRedis19($cid."_".Game::get_today_long_id());
		if ($allWw > 0) {

	        $Redis19Model->zIncrBy($this->uid,$allWw);
		}

		$Redis18Model->back_data();
        $Redis19Model->back_data();
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);
	}

	/**
	 * 获取任务奖励
	 * @param unknown_type $params
	 * $params['dcid'] :档次id
	 */
	public function getTaskRwd($params)
	{
		$dcid = Game::intval($params,'dcid');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$club_task = Game::getcfg_info('club_task', $dcid);
		$Act761Model = Master::getAct761($this->uid);
		$taskInfo = $Act761Model->info;
		if (empty($taskInfo["list"][$dcid])) {
			Master::error(DAILY_UN_COMPLETE);
		}

		if (!empty($taskInfo["get"][$dcid])) {
			Master::error(CLUB_TASK_IS_COMPLETE);
		}

		$ClubModel = Master::getClub($cid);
		$buildInfo = $ClubModel->getBuildInfo(1);

		//加获得奖励
		$allFund = 0;
		$items = array();
		foreach($club_task['get'] as $k => $v){
			switch($v["id"]){
				case 118:
					$v["count"] = ceil($v["count"] * $buildInfo["exp_buff"]);
        			$items[] = $v;
					break;
				case 117:
					$v["count"] = ceil($v["count"] * $buildInfo["fund_buff"]);
        			$items[] = $v;
        			$allFund += $v["count"];
					break;
				case 119:
					//加个人剩余贡献  和  总贡献
					$v["count"] = ceil($v["count"] * $buildInfo["ctbt_buff"]);
        			$items[] = $v;
					break;
				default:
        			$items[] = $v;
					break;
			}
		}
		Master::add_item3($items);
		$Act761Model->get($dcid);

		$Act760Model = Master::getAct760($this->uid);
		$Act760Model->add_score($club_task['activenum']);

		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info);
	}

	/**
	----------------------------------------------------------贡献商店-----------------------------------------------------------------
	 */

	/**
	 * 商店列表
	 * @param unknown_type $params
	 */
	public function shopList($params)
	{
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);
		$Act41Model = Master::getAct41($this->uid);
		$buildInfo = $ClubModel->getBuildInfo(2);

		$cfg_club_shop = Game::getcfg('club_shop');
		$list = array();//商店列表
		foreach($cfg_club_shop as $k => $v){

			//商品
			$items = $v['get'];
			//是否上锁,0::已开锁 大于0:未解锁,解锁等级
			$lock = 0;
			if($v['need_lv'] - $buildInfo["lv"] > 0){
				$lock = $v['need_lv'];
			}

			//可兑换次数
			$num = 0;
			if($lock == 0){
				$num = $v['limit_get'];
				$num -= $Act41Model->info[$k];
			}
			//构造输出列表
			$list[] = array(
				'payGX' => $v['cost'],
				'id' => $k,
				'item' => $items,
				'lock' => $lock,
				'num' => $num,
				'page' => $v['page'],
			);
		}
		Master::back_data($this->uid,'club','shopList',$list);

	}

	/**
	 * 贡献兑换商店=>兑换
	 * @param unknown_type $params
	 * $params['id']  :  物品标识id
	 */
	public function shopBuy($params)
	{
		$id = Game::intval($params,'id');
		$Act41Model = Master::getAct41($this->uid);
		$Act41Model->shop_buy($id);

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		$ClubModel = Master::getClub($cid);

		//工会信息
		$h_info = $ClubModel->getBase();
		Master::back_data($this->uid,'club','clubInfo',$h_info,true);

		$cfg_club_shop = Game::getcfg('club_shop');
		$list = array();//商店列表
		$buildInfo = $ClubModel->getBuildInfo(2);
		foreach($cfg_club_shop as $k => $v){

			//商品
			$items = $v['get'];
			//是否上锁,0::已开锁 大于0:未解锁,解锁等级
			$lock = 0;
			if($v['need_lv'] - $buildInfo["lv"] > 0){
				$lock = $v['need_lv'];
			}

			//可兑换次数
			$num = 0;
			if($lock == 0){
				$num = $v['limit_get'];
				$num -= $Act41Model->info[$k];
			}
			//构造输出列表
			$list[] = array(
				'payGX' => $v['cost'],
				'id' => $k,
				'item' => $items,
				'lock' => $lock,
				'num' => $num,
				'page' => $v['page'],
			);
		}
		Master::back_data($this->uid,'club','shopList',$list);

		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(160,1);
	}

	//领取活跃度奖励
	//$id = 0 一键领取
	public function pickActiveAward($params){
		$id = Game::intval($params,'id');
		$Act760Model = Master::getAct760($this->uid);
		$Act760Model->get_rwd($id);
	}

	/**
	----------------------------------------------------------公会BOSS-----------------------------------------------------------------
	 */

	/**
	 * 公会boss信息
	 * @param unknown_type $params
	 */
	public function clubBossInfo($params)
	{
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Sev12Model = Master::getSev12($cid);
		$Sev12Model->checkNewBoss($this->uid);
		$Sev12Model->bake_data();

		$Act6Model = Master::getAct6($this->uid);//出战列表
		$Act6Model->back_data();
	}

	/**
	 * 开启公会副本
	 * @param unknown_type $params
	 * $params['cbid'] : 公会bossid
	 * $params['type'] : 1:钻石消耗  2:财富值消耗
	 */
	public function clubBossOpen($params)
	{
		$cbid = Game::intval($params,'cbid');
		$type = Game::intval($params,'type');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		if(!$Act40Model->checkIsCanJoinBoss()){
			Master::error(CLUB_COPY_JOIN_24_HOUR);
		}
		$ClubModel = Master::getClub($cid);
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_PERMISSION_DENIED_OPERATE);
		}

		$Sev12Model = Master::getSev12($cid);
		$Sev12Model->open_club_boss($this->uid,1,true,$type);
		$Sev12Model->bake_data();
	}

	/**
	 * 公会boss pk  => 打boss
	 * @param unknown_type $params
	 * $params['cbid']: BOSS-id
	 * $params['id']: 门客id
	 */
	public function clubBossPK($params)
	{
		$cbid = Game::intval($params,'cbid');
		$hid = Game::intval($params,'id');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		if(!$Act40Model->checkIsCanJoinBoss()){
			Master::error(CLUB_COPY_JOIN_24_HOUR);
		}
		$ClubModel = Master::getClub($cid);
		$HeroModel = Master::getHero($this->uid);
		//门客存在
		$hero_info = $HeroModel->check_info($hid);

		//门客出战列表
		$Act6Model = Master::getAct6($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act6Model->go_fight($hid);

		//获取阵法信息
		$TeamModel  = Master::getTeam($this->uid);
		//英雄伤害值
		$BossCfg = Game::getcfg_info('club_boss',$cbid);
		$dType = $BossCfg['ep'];
		$hero_hit = $TeamModel->getHeroDamage($hid, $dType);

		//判断副本是否已开启   判断boss是否被击杀
		$Sev12Model = Master::getSev12($cid);
		$dahit = $Sev12Model->sub_club_boss_hp($cbid,$hero_hit);

		$windata = array();
		//战报列表(boss被击杀)
		$award = Game::getcfg_param("club_bossRwd");
		$awardArr = explode('|',$award);
		$gx = ceil($dahit/$awardArr[1]*$awardArr[0]);  //获得的贡献 => 以boss扣的血量/2000000
		//获得的贡献
		$windata[] = array(
			'id' => 118,
			'kind' => 114,
			'count' => $gx
		);

		$post = $ClubModel->info['members'][$this->uid]['post'];
		//战报信息(boss未被击杀)
		$Sev13Model = Master::getSev13($cid);
		$Sev13Model->add_hero_log($this->uid,$dahit,$post);
		$Sev13Model->boss_log_outf();

		Master::back_win("club","cbosspkwin","id",$cbid);
		Master::back_win("club","cbosspkwin","hit",$dahit);
		Master::back_win("club","cbosspkwin","gx",$gx);

		//被击杀
		if( $Sev12Model->info['currentCbid'] > $cbid || $Sev12Model->info['bosshp'] <= 0){
			$title = MAIL_CLUB_BOSS_TITLE.'|'.$cbid.'|'.MAIL_CLUB_BOSS_TITLE1;
			$tips = MAIL_CLUB_BOSS_CONTENT_1.'|'.$BossCfg['name'].'|'.MAIL_CLUB_BOSS_CONTENT_2;
			foreach($ClubModel->info['members'] as $uid => $v){
				$mailModel = Master::getMail($uid);
				$mailModel->sendMail($uid, $title, $tips, 1, $BossCfg['rwd_personal']);
				$mailModel->destroy();
			}
			$Sev14Model = Master::getSev14($cid);
			$Sev14Model->kill_log($cbid,$this->uid);

			$cfg_club_boss_cfg = Game::getcfg_info('club_boss', $cbid);
			foreach ($cfg_club_boss_cfg['rwd'] as $key => $value) {
				$windata[] = array(
					'id' => $value["id"],
					'kind' => $value["kind"],
					'count' => $value["count"],
				);
			}

            //双旦活动道具产出
            // $Act292Model = Master::getAct292($this->uid);
            // $hditem = $Act292Model->chanChu(8,0,1);
            // if(!empty($hditem)){
            //     Master::add_item2($hditem);
            //     $windata[] = $hditem;
            // }
			Master::back_s(2);//击杀

			//记录公会日志
			$Sev15Model = Master::getSev15($cid);
			$Sev15Model->add_log(3,$this->uid,0,$cbid,0,0,$windata);
			//活动消耗 - 限时联盟副本击杀（累计击杀僵尸）
			$HuodongModel = Master::getHuodong($this->uid);
			$HuodongModel->xianshi_huodong('huodong224',1);
			//击杀流水
			Game::cmd_other_flow($cid , 'ClubModel', 'kill', array($cbid => $this->uid), 39, 1, 1, $Sev12Model->info['list'][$cbid]);
		}
		Master::add_item3($windata);
		Master::back_win("club","cbosspkwin","items",$windata);


		//活动消耗 - 限时联盟副本伤害（累计副本伤害）
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong223',$dahit);

        $Sev12Model = Master::getSev12($cid);
        $Sev12Model->bake_data();
    }

	/**
	 * 公会门客复活
	 * @param unknown_type $id  门客id
	 * $params['id']: 门客id
	 */
	public function clubHeroCone($params)
	{
		//需要复活的门客ID
		$hero_id = Game::intval($params,'id');
		//判断是否被踢出宫殿
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        if(empty($cid)){
            Master::error(CLUB_IS_NULL,1);
		}
		if(!$Act40Model->checkIsCanJoinBoss()){
			Master::error(CLUB_COPY_JOIN_24_HOUR);
		}
		//门客出战列表
		$Act6Model = Master::getAct6($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act6Model->cone_back($hero_id);
		$Act6Model->back_data();
	}

	/**
	 * boss未被打死, 每个门客的日志
	 * @param unknown_type $params
	 * $params['id']   bossid
	 */
	public function clubBosslog($params)
	{
		$cbid = Game::intval($params,'id');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Sev13Model = Master::getSev13($cid);
		$Sev13Model->boss_log_outf();
	}

	/**
	 * 公会boss战报列表 =>  伤害排行
	 * @param unknown_type $params
	 * $params['id']   bossid
	 */
	public function clubBossHitList($params)
	{
		$cbid = Game::intval($params,'id');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Sev14Model = Master::getSev14($cid);
		$Sev14Model->log_outf($cid);
	}

	/**
	----------------------------------------------------------公会BOSSEND-----------------------------------------------------------------
	 */

	// **********************帮会战**********************************

	/**
	 * 帮会战--开关
	 * @param unknown_type $params
	 */
	public function kua_open()
	{
		$is_open = Game::get_gq_status('mengZhan');
		if(!$is_open){
			Master::error(GONGNENG_NO_OPEN);
		}
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$ClubModel = Master::getClub($cid);
		if ($ClubModel->info['level'] < 3 ){
			Master::error(CLUB_NO_OPEN);
		}
	}

	/**
	 * 帮会战--信息
	 * @param unknown_type $params
	 */
	public function kuaPKinfo($params)
	{
		self::kua_open();
		//判断是否在跨服范围内
		$sevid_cfg = Common::getSevidCfg();
		if(!Game::club_pk_serv($sevid_cfg['sevid'])){
			Master::error(GONGNENG_NO_OPEN);
		}
		
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL,1);
		}
		$Sev54Model = Master::getSev54($Act40Model->info['cid']);
		$outf = $Sev54Model->out_data($this->uid);
		
		Master::back_data($this->uid,'club','clubKuaInfo',$outf);
		
		$Sev56Model = Master::getSev56();
		$Sev56Model->bake_data();
		
		//说明
		$clubpk = Game::get_peizhi('clubpk');
		$msg = empty($clubpk['msg'])?'':$clubpk['msg'];
		Master::back_data($this->uid,'club','clubKuaMsg',array('msg' => $msg));

	}

	/**
	 * 帮会战-- 参赛阵容
	 * @param unknown_type $params
	 */
	public function kuaPKCszr($params)
	{
		self::kua_open();
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL,1);
		}

		$Sev51Model = Master::getSev51($Act40Model->info['cid']);
		$Sev51Model->bake_data();

		//联盟----跨服帮会战门客列表
		$Act42Model = Master::getAct42($this->uid);//出战列表
		$Act42Model->back_data();
	}

	/**
	 * 帮会战-- 帮会战派遣/更换
	 * @param unknown_type $params
	 * $params['hid'] : 门客id
	 */
	public function kuaPKAdd($params)
	{
		self::kua_open();
		$heroid = Game::intval($params,'hid');
		//门客ID合法
		$HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroid);

		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$Sev51Model = Master::getSev51($Act40Model->info['cid']);
		$Sev51Model->baoming($this->uid,$heroid);

		//帮会战-信息
		$Sev54Model = Master::getSev54($Act40Model->info['cid']);
		$outf = $Sev54Model->out_data($this->uid);
		Master::back_data($this->uid,'club','clubKuaInfo',$outf);
		
		//帮会战-参赛阵容
		$Sev51Model->bake_data();
	}

	/**
	 * 帮会战-- 帮会战撤回
	 * @param unknown_type $params
	 */
	public function kuaPKBack($params)
	{
		self::kua_open();
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$Sev51Model = Master::getSev51($Act40Model->info['cid']);
		$Sev51Model->cancel($this->uid);
		
		//帮会战-信息
		$Sev54Model = Master::getSev54($Act40Model->info['cid']);
		$outf = $Sev54Model->out_data($this->uid);
		Master::back_data($this->uid,'club','clubKuaInfo',$outf);
		
		//帮会战-参赛阵容
		$Sev51Model = Master::getSev51($Act40Model->info['cid']);
		$Sev51Model->bake_data();
	}

	/**
	 * 帮会战-- 帮会战pk阵容
	 * @param unknown_type $params
	 */
	public function kuaPKzr($params)
	{
	    self::kua_open();
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		//我方pk阵容
		$Sev51Model = Master::getSev51($Act40Model->info['cid']);
		$outf['myclub'] = $Sev51Model->get_outf();

		//敌方pk阵容
		$Sev50Model = Master::getSev50();
		$outf['diclub'] = array();
		if(!empty($Sev50Model->info[$Act40Model->info['cid']])){
			//匹配到的敌方公会id
			$fcid = $Sev50Model->info[$Act40Model->info['cid']]['fcid'];
			$Sev51Model = Master::getSev51($fcid,Game::get_sevid_club($fcid));
			$outf['diclub'] = $Sev51Model->get_outf();
		}
		Master::back_data($this->uid,'club','clubKuapkzr',$outf);
	}

	/**
	 * 帮会战-- 使用锦囊
	 * @param unknown_type $params
	 */
	public function kuaPKusejn($params)
	{
		self::kua_open();
		
		$id = Game::intval($params,'id');
		$heroid = Game::intval($params,'heroid');
		
		$ItemModel = Master::getItem($this->uid);
		if(!$ItemModel->sub_item($id,1,true)){
			Master::error(ITEMS_NUMBER_SHORT);
		}
					
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$Sev51Model = Master::getSev51($Act40Model->info['cid']);
		$Sev51Model->usejinnang($this->uid,$id,$heroid);
		$Sev51Model->bake_data();
		Master::sub_item($this->uid,KIND_ITEM,$id,1);
		
		$Sev54Model = Master::getSev54($Act40Model->info['cid']);
		$outf = $Sev54Model->out_data($this->uid);
		Master::back_data($this->uid,'club','clubKuaInfo',$outf);

        //主线任务 ---  拔旗易帜 使用令旗X次（任意令旗）
        // $Act39Model = Master::getAct39($this->uid);
        // $Act39Model->task_add(38, 1);
	}

	/**
	 * 帮会战-- 帮会战pk 日志
	 * @param unknown_type $params
	 */
	public function kuaPKbflog($params)
	{
		self::kua_open();
		
		$cid = Game::intval($params,'cid');
		
		$Sev53Model = Master::getSev53($cid,Game::get_sevid_club($cid));
		$outf = $Sev53Model->out_zr();
		$log = $Sev53Model->get_outf();
		
		if(empty($Sev53Model->info['diclub'])){
			Master::error(CLUB_NO_HIT_LOG);
		}
		if( !empty($outf['myclub']['list']) && empty($outf['diclub']['list'])){
			Master::error(CLUB_NO_PX);
		}
		if( empty($outf['myclub']['list'])&& empty($outf['diclub']['list']) ){
			Master::error(CLUB_NO_LOG);
		}
		Master::back_data($this->uid,'club','clubKuapkzr',$outf);
		Master::back_data($this->uid,'club','clubKuapklog',$log['pklog']);
	}

	/**
	 * 帮会战-- 帮会战pk 奖励信息
	 * @param unknown_type $params
	 */
	public function kuaPKrwdinfo($params)
	{
		self::kua_open();
		
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		$Sev55Model = Master::getSev55($Act40Model->info['cid'],Game::get_sevid_club($Act40Model->info['cid']));
		$rwd = $Sev55Model->get_outf();
		$rwd['isGet'] = in_array($this->uid,$rwd['getMems'])?1:0;
		unset($rwd['getMems']);
		Master::back_data($this->uid,'club','clubKuapkrwd',$rwd);
	}

	/**
	 * 帮会战-- 帮会战pk 奖励领取
	 * @param unknown_type $params
	 * @param $id  1: 个人奖励   2:联盟奖励
	 */
	public function kuaPKrwdget($params)
	{
		self::kua_open();
		
		$id = Game::intval($params,'id');
		if(!in_array($id,array(1,2))){
			Master::error(PARAMS_ERROR);
		}
		//判断是否已经有联盟
		$Act40Model = Master::getAct40($this->uid);
		if(empty($Act40Model->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		
		$Sev55Model = Master::getSev55($Act40Model->info['cid']);
		$rwd_time = $Sev55Model->rwd_time();
		if( $rwd_time['ltype'] != 2){
			Master::error(ACTHD_NO_RECEIVE);
		}
		$rwd = $Sev55Model->get_outf();
		$rwd['isGet'] = in_array($this->uid,$rwd['getMems'])?1:0;
		
		//个人
		if($id == 1){
			if(empty($rwd['setMems'])  || !in_array($this->uid,$rwd['setMems'])){
				Master::error(CLUB_NO_JOIN);
			}
			if($rwd['isGet'] == 1){
				Master::error(DAILY_IS_RECEIVE);
			}
			$Sev55Model->get_mrwd($this->uid);
			foreach($rwd['member'] as $info){
				$Act40Model->add_gx($info['count']);
			}
			$rwd['isGet'] = 1;
		}elseif($id == 2){//联盟
			$redis11Model = Master::getRedis11();
	    	$rank_id = $redis11Model->get_rank_id($Act40Model->info['cid']);
			if(empty($rwd['is_get'])){
				Master::error(CLUB_NO_JOIN);
			}
			if( !empty($rwd['getCuid']) ){
				Master::error(DAILY_IS_RECEIVE);
			}
			
			$ClubModel = Master::getClub($Act40Model->info['cid']);
			//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
			if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
				Master::error(CLUB_PERMISSION_DENIED_OPERATE);
			}
			
			$UserModel = Master::getUser($this->uid);
			$Sev55Model->get_crwd($this->uid,$UserModel->info['name']);
			foreach($rwd['club'] as $info){
				$ClubModel->add_exp($this->uid,$info['count']);
			}
			$rwd['getCuid'] = $this->uid;
			$rwd['getCname'] = $UserModel->info['name'];
		}
		unset($rwd['getMems']);
		unset($rwd['setMems']);
		unset($rwd['is_get']);
		Master::back_data($this->uid,'club','clubKuapkrwd',$rwd);
	}

	/**
	 * 帮会战-- 获胜/失败弹窗
	 */
	public function kuaLookWin($params)
	{
		self::kua_open();
		
		$cid = Game::intval($params,'cid');
		$Sev55Model = Master::getSev55($cid,Game::get_sevid_club($cid));
		$isWin = $Sev55Model->info['is_win'];
		$gejifen = 0;
		if($isWin == 1){
			$gejifen = $Sev55Model->info['flevel'];
		}elseif($isWin == 0){
			$gejifen = (-1)*$Sev55Model->info['flevel'];
		}
		/*
		if(empty($Sev55Model->info['fcid'])){
			$gejifen = 0;
		}
		*/
		$data = array(
			'cid' => $cid,
			'name' => $Sev55Model->info['name'],
			'isWin' => $isWin,
			'gejifen' => $gejifen,
			'fcid' => $Sev55Model->info['fcid'],
			'fname' => $Sev55Model->info['fname'],
		);
		
		
		Master::back_data(0,'club','clubKuaWin',$data);
	}

	/**
	 * 帮会战-- 查看更多日志
	 */
	public function kuaLookLog($params)
	{
		self::kua_open();
		
		$Sev56Model = Master::getSev56();
		$Sev56Model->bake_data();
	}

	/**
	 * 帮会战-- 伤害排行
	 * cid : 公会id
	 */
	public function kuaLookHit($params)
	{
		self::kua_open();
		
		$data = array();
		
		//我方
		$cid = Game::intval($params,'cid');
		$Sev57Model = Master::getSev57($cid,Game::get_sevid_club($cid));
		$outf = $Sev57Model->get_outf();
		$fcid = $outf['fcid'];
		unset($outf['fcid']);
		$data['my'] = $outf;
		
		//敌方
		$foutf = array();
		if(empty($fcid)){
			Master::error(CLUB_NO_HIT_LOG);
		}
		
		$fSev57Model = Master::getSev57($fcid,Game::get_sevid_club($fcid));
		$foutf = $fSev57Model->get_outf();
		$data['f'] = $foutf;
		
		if( !empty($outf['list']) && empty($foutf['list']) ){
			Master::error(CLUB_NO_PX);
		}
		if( empty($outf['list']) && empty($foutf['list']) ){
			Master::error(CLUB_NO_LOG);
		}
		
		Master::back_data(0,'club','clubKuahit',$data);
	}

    /**
     * 宫殿个人信息
     */
    public function clubMemberInfo($params)
    {
        //联盟ID
        $cid = Game::intval($params,'cid');
        //判断是否被踢
        $Act40Model = Master::getAct40($this->uid);
        if(empty($Act40Model->info['cid'])){
            Master::error(CLUB_IS_NULL,1);
        }
        //联盟boss
        $Sev12Model = Master::getSev12($Act40Model->info['cid']);
        $Sev12Model->bake_data();

        //宫殿个人信息
        $Act40Model->back_data();
        //宫殿成员信息
        $ClubModel = Master::getClub($cid);
        $h_info = $ClubModel->getBase();
        Master::back_data($this->uid,'club','clubInfo',$h_info);
    }

    /**
     * 公会bossinfo
     * @param unknown_type $params
     * $params['cbid']: BOSS-id
     */
    public function clubBossPKLog($params)
    {
        $cbid = Game::intval($params,'cbid');

        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        if(empty($cid)){
            Master::error(CLUB_IS_NULL);
        }
        //战报信息(boss未被击杀)
        $Sev13Model = Master::getSev13($cid);
        $Sev13Model->boss_log_outf();
    }

    // 退出公会排行榜
    public function outClubRank($fuid) {
	}
	
	    /**
	----------------------------------------------------------公会宴会-----------------------------------------------------------------
	 */

	 //获取资源的基础信息
	public function getResourceBaseInfo(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Act767Model = Master::getAct767($this->uid);
		$Act767Model->randResource();
		$Act767Model->back_data();

		$Sev17Model = Master::getSev17($cid);
		$Sev17Model->bake_data();
	}

	//获取宴会基础信息
	public function getPartyBaseInfo(){

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Act768Model = Master::getAct768($this->uid);
		$Act768Model->back_data();

		$Act769Model = Master::getAct769($this->uid);
		$Act769Model->back_data();

		$Act770Model = Master::getAct770($this->uid);
		$Act770Model->back_data();

		$Act771Model = Master::getAct771($this->uid);
		$Act771Model->back_data();

		$Sev17Model = Master::getSev17($cid);
		$Sev17Model->bake_data();

		$Sev18Model = Master::getSev18($cid);
		$Sev18Model->bake_data();

		$Sev100Model = Master::getSev100($cid);
		$Sev100Model->bake_data();
	}

	//提交资源
	public function submitResource(){
		
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Act767Model = Master::getAct767($this->uid);
		if(empty($Act767Model->info['resourceList'])){
			$Act767Model->randResource();
		}
		$Act767Model->submit();

		$Sev17Model = Master::getSev17($cid);
		$Sev17Model->bake_data();
	}

	//购买提交资源次数
	public function buyCount(){
	
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Act767Model = Master::getAct767($this->uid);
		$Act767Model->buyCount();
	}

	//刷新提交资源列表
	public function refreshList(){

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		$Act767Model = Master::getAct767($this->uid);
		$Act767Model->randResource(true);
	}

	/**
	 * 开启宴会
	 */
	public function openParty($params){
		$id = Game::intval($params,'id');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		if(!$Act40Model->checkIsCanJoinBoss()){
			Master::error(CLUB_COPY_JOIN_24_HOUR);
		}
		//公会信息
		$ClubModel = Master::getClub($cid);
		if (empty($ClubModel->info['cid'])){
			Master::error(CLUB_IS_NULL);
		}
		//判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他 
		if(!in_array($ClubModel->info['members'][$this->uid]['post'],array(1,2))){
			Master::error(CLUB_MODIFY_ONLY_LEADER);
		}

		$Sev17Model = Master::getSev17($cid);
		$Sev17Model->setPartyStart($id);
		$Sev17Model->bake_data();

		$Sev100Model = Master::getSev100($cid);
		$Sev100Model->removeData();
		$Sev100Model->bake_data();

		$title = MAIL_CLUB_PARTY_OPEN_TITLE;
		$tips = MAIL_CLUB_PARTY_OPEN_CONTENT_1.'|'.$ClubModel->info['name'].'|'.MAIL_CLUB_PARTY_OPEN_CONTENT_2;
		foreach($ClubModel->info['members'] as $uid => $v){
			$mailModel = Master::getMail($uid);
			$mailModel->sendMail($uid, $title, $tips, 0, '');
			$mailModel->destroy();
		}
	}


	//参加宴会
	public function joinParty(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		if(!$Act40Model->checkIsCanJoinBoss()){
			Master::error(CLUB_COPY_JOIN_24_HOUR);
		}
		
		$Sev17Model = Master::getSev17($cid);
		
		if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}

		if(!in_array($this->uid,$Sev17Model->info['joinPartyPeople'])){
			$Act771Model = Master::getAct771($this->uid);
			$Act771Model->removeData();
	
			$Act768Model = Master::getAct768($this->uid);
			$Act768Model->randMusician();
		}

		$Sev17Model->setPartyUser($this->uid);
		$Sev17Model->bake_data();
		if(count($Sev17Model->info['joinPartyPeople']) == 1){
			$partyCfg = Game::getcfg_info('party',$Sev17Model->info['partyLv']);
			Master::add_item3($partyCfg['club_rwd']);
		}
	}

	//更换乐师
	public function changeMusician($params){
		$id = game::intval($params,'id');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}
		
		$Sev17Model = Master::getSev17($cid);
        if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}

		$Act768Model = Master::getAct768($this->uid);
		$Act768Model->changeMusician($id);
	}

	//购买特效buff
	public function buyBuff($params){
		$id = Game::intval($params,'id');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Sev17Model = Master::getSev17($cid);
		
		if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}

		$Act768Model = Master::getAct768($this->uid);
		$Act768Model->buyBuff($id);

		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->add_log(21,$this->uid,0,$id);

		//购买特效之后发放红包
		$Sev18Model = Master::getSev18($cid);
		$Sev18Model->add($id,$this->uid);
		$Sev18Model->bake_data();
	}

	//抢红包
	public function robRedBag($params){
		$robUid = Game::intval($params,'robUid');

		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Sev17Model = Master::getSev17($cid);
		
		if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}

		//检测是否是可抢红包次数
		$Act769Model = Master::getAct769($this->uid);
		$Act769Model->checkIsRob();
		
		//抢红包
		$Sev18Model = Master::getSev18($cid);
		$robList = $Sev18Model->robRedBag($this->uid);
		$Sev18Model->bake_data();

		Master::back_data(0,'club','redBag',array('robList' => $robList));

	}

	//开始挂机
	public function startHook(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Sev17Model = Master::getSev17($cid);
        if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}

		$Act770Model = Master::getAct770($this->uid);
		$Act770Model->startHook();
	}

	//领取挂机奖励
	public function pickHookAward(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Sev17Model = Master::getSev17($cid);
		
		if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}

		$Act770Model = Master::getAct770($this->uid);
		$Act770Model->pickAward($cid);
	}

	//更新公会内的消息
	public function updateClubInfo(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Sev15Model = Master::getSev15($cid);
		$Sev15Model->bake_data();

		$Sev17Model = Master::getSev17($cid);
		if($Sev17Model->isEnd()){
            Master::error(CLUB_PARTY_PARTY_IS_END,3);
		}
		$Sev17Model->bake_data();

		$Sev18Model = Master::getSev18($cid);
		$Sev18Model->removeRobedBag();
		$Sev18Model->bake_data();
	}

	//获取投壶信息
	public function getThrowInfo(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Act771Model = Master::getAct771($this->uid);
		$Act771Model->back_data();

		$Sev100Model = Master::getSev100($cid);
		$Sev100Model->checkAwardInfo($cid);
		$Sev100Model->bake_data();

	}

	//投壶
	public function throwPot($params){
		$id = Game::intval($params,'id');
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		if($id > 3){
			return;
		}
		$Act771Model = Master::getAct771($this->uid);
		$Act771Model->setFpoint();

		$Sev100Model = Master::getSev100($cid);
		$Sev100Model->setThrowPot($id,$this->uid);
		$Sev100Model->bake_data();
	}

	//领取投壶奖励
	public function pickAward(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

	
		$Sev100Model = Master::getSev100($cid);
		$Sev100Model->checkAwardInfo($cid);
		$Sev100Model->bake_data();

		$Act771Model = Master::getAct771($this->uid);
		$Act771Model->pickAward($cid,$id);
	}

	//随机游戏玩家
	public function randGameUser(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$randId = rand(1,100);
		$randStandard = Game::getcfg_param("club_gameProb");
		if($randId >= $randStandard){
			return;
		}

		$Sev17Model = Master::getSev17($cid);
		$rUid = Game::array_rand($Sev17Model->info['joinPartyPeople'],1);
		if(empty($rUid[0])){
			return;
		}
		if($this->uid == $rUid[0]){
			return;
		}
		$fUser = Master::fuidInfo($rUid[0]);
		Master::back_data($this->uid,'club','randUser',array('info' => $fUser));
	}

	//领取游戏奖励
	public function pickGamesAward(){
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_IS_NULL,1);
		}

		$Act772Model = Master::getAct772($this->uid);
		$Act772Model->pickGameAward();
	}
}