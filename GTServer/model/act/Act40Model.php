<?php
require_once "ActBaseModel.php";
/*
 * 联盟个人信息
 */
class Act40Model extends ActBaseModel
{
	public $atype = 40;//活动编号
	
	public $comment = "联盟个人信息";
	public $b_mol = "club";//返回信息 所在模块
	public $b_ctrl = "memberInfo";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'cid' => 0, //联盟id
		'n_cid' => 0, //通知的联盟id
		'n_clv' => 0, //通知的联盟等级
		'n_post' => 0, //通知的联盟职位
		'allgx' => 0, //总贡献
		'leftgx' => 0, //剩余贡献
		'inTime' => 0, //入盟时间
		'outTime' => 0, //退盟时间
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->info['cid'] = intval($this->info['cid']);
		//贡献档次
		$Sev10Model = Master::getSev10($this->info['cid']);
		$dcid = empty($Sev10Model->info[$this->uid])?0:$Sev10Model->info[$this->uid];
		
		$post = 0; //玩家联盟职位
		if(!empty($this->info['cid'])){
			$ClubModel = Master::getClub($this->info['cid']);
			if(!empty($ClubModel->info['members'][$this->uid]['post'])){
				$post = $ClubModel->info['members'][$this->uid]['post'];
			}
		}
		
		//默认输出直接等于内部存储数据
		$this->outf = array(
//			'n_cid' => $this->info['n_cid'], //通知的联盟id
//			'n_clv' => $this->info['n_clv'], //通知的联盟等级
//			'n_post' => $this->info['n_post'], //通知的联盟职位
			'cid' => $this->info['cid'], //联盟id
			'allgx' => $this->info['allgx'], //总贡献
			'leftgx' => $this->info['leftgx'], //剩余贡献
			'donate'  => $dcid,  //今日贡献的档次id -0:未建设 1:初建 2:中建 3:高建 4:道具建设 5:高级道具建设
			'post' => $post,//1:盟主  2:副盟主 3:精英 4:成员 5:其他
			'ltime' => array(   //退盟时间,入盟倒计时
				'next' => $this->info['outTime']+86400,
				'label' => 'clubInTime',
			),
			'inTime' => $this->info['inTime'],
		);
	}
	
	/**
	 * 更新通知的字段信息
	 * @param unknown_type $data
	 */
	public function n_update($data){
		$flag = 0;
		if(empty($data)){
			return 0;
		}
		foreach($data as $k => $v){
			//与原始一样不更新
			if( !isset($this->info[$k]) || $this->info[$k] == $v){
				continue;
			}
			$this->info[$k] = $v;
			$flag = 1;
		}
		if($flag == 1){
			$this->save();
		}
	}
	
	
	/**
	 * 减剩余贡献
	 * @param unknown_type $num
	 */
	public function sub_leftgx($num){
		if($this->info['leftgx'] < $num){
			Master::error(CLUB_CONTRIBUTION_SHORT);
		}
		$this->info['leftgx'] -= $num;
		$this->save();

		$ClubModel = Master::getClub($this->info['cid']);
		$ClubModel->delete_cache();
        //帮会贡献流水
        Game::cmd_flow(24, 1, -$num, $this->info['leftgx']);
	}
	
	
	/**
	 * 加总贡献和剩余贡献
	 * @param unknown_type $num
	 */
	public function add_gx($num){
		$this->info['leftgx'] += $num;
		$this->info['allgx'] += $num;
		$this->save();

		$ClubModel = Master::getClub($this->info['cid']);
		$ClubModel->delete_cache();
        //个人贡献流水
        Game::cmd_flow(24, 1, $num, $this->info['leftgx']);
        //个人总贡献流水
        Game::cmd_flow(23, 1, $num, $this->info['allgx']);
	}
	
	/**
	 * 检查是否有可以加入联盟
	 * @param $type 0:创建联盟   1:加入联盟
	 */
	public function check_my($type){
		
		if(!empty($this->info['cid'])){
			Master::error(CLUB_HAVE_JOIN);
		}
		// if($type && $_SERVER['REQUEST_TIME'] -  $this->info['outTime'] < 86400 ){
		// 	Master::error(CLUB_QUIT_TIME_TIPS);
		// }
	}
	
	/**
	 * 加入联盟
	 * @param $cid  联盟id
	 * @param $type 0:创建联盟   1:加入联盟
	 */
	public function inClub($cid,$type = 1){
		
		if(!empty($this->info['cid'])){
			Master::error(CLUB_HAVE_JOIN);
		}
		// if($type && $_SERVER['REQUEST_TIME'] -  $this->info['outTime'] < 86400 ){
		// 	Master::error(CLUB_QUIT_TIME_TIPS);
		// }
		
		$this->info['cid'] = intval($cid);
		$this->info['inTime'] = $_SERVER['REQUEST_TIME'];
		
		$this->save();
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(33);
	}
	
	/**
	 * 退出联盟
	 */
	public function outClub($cid){
		if($cid != $this->info['cid']){
			return 0;
		}
		$this->info['cid'] = 0;
		$this->info['allgx'] = 0;
		$this->info['leftgx'] = floor($this->info['leftgx']/2);
		$this->info['inTime'] = 0;
		$this->info['outTime'] = $_SERVER['REQUEST_TIME'];
		$this->info['outcid'] = $cid;
		$this->save();

		$Act771Model = Master::getAct771($this->uid);
		$Act771Model->removeData();
		
		$Sev51Model = Master::getSev51($cid);
		$Sev51Model->cancel($this->uid,true);
	}
	
	/**
	 * 异常 退出联盟
	 */
	public function qz_out($cid,$mypost){
		if($cid != $this->info['cid']){
			return 0;
		}
		$this->info['cid'] = 0;
		$this->info['allgx'] = 0;
		$this->info['inTime'] = 0;
		$this->info['outTime'] = 0;
		$this->info['outcid'] = $cid.'_'.$mypost;
		$this->save();
		
		$Sev51Model = Master::getSev51($cid);
		$Sev51Model->cancel($this->uid,true);
	}

	//根据入盟时间判断是否可以参加工会战，宴会等项目
	public function checkIsCanJoinBoss(){
		//暂时屏蔽掉24小时限制
		// return true;
		if($this->info['inTime'] + 86400 < Game::get_now()){
			return true;
		}
		return false;
	}
	
	
	/**
	 * 盟主修改密码弹窗  只有被动转让和主动转让才有
	 * $is_open=> 1:打开弹窗 0:关闭弹窗
	 */
	public function pwd_tip($is_open){
		$this->info['password'] = $is_open==1?1:0;  //默认0:不弹窗 1:弹窗
		$this->save();
	}
	
	
}
















