<?php
/**
 * 帮会战-奖励
 */
require_once "SevBaseModel.php";
class Sev55Model extends SevBaseModel
{
    public $comment = "帮会战-奖励";
    public $act = 55;//活动标签
    
    
    /*
	 * 初始化结构体
	 */
	public $_init = array(
		'is_win' => 1, //默认胜利
		'exp' => 100 , //公会基础经验奖励
		'extra' => 0,  //帮战榜加成
		'getCuid' => 0,  //领取的uid
		'getCname' => '',  //领取的名字
		'setMems' => array(),  //存放可领取的uid
		'getMems' => array(),  //领取的名字
		'name'	=> '',//我公会name
		'fcid'	=> 0,//敌方公会id
		'flevel' => 1, //敌方公会等级
		'fname'	=> '',//敌方公会name
		'is_get' => 0, //是否可以领取 0: 不能领取
	);
    
	/**
	 * 领取公会奖励
	 */
	public function get_crwd($uid){
		$this->info['getCuid'] = $uid;
		$UserModel = Master::getUser($uid);
		$this->info['getCname'] = $UserModel->info['name'];
		$this->save();
	}
	
	/**
	 * 领取个人奖励
	 */
	public function get_mrwd($uid){
		$this->info['getMems'][] = $uid;
		$this->save();
	}
	
	
	/**
	 * 刷新帮战奖励
	 * @param $is_win   是否获胜
	 * @param $fclevel   对手帮会等级
	 */
	public function reset($is_win,$fcid,$flv){
		
		//平局
		$this->info['exp'] = 100;
		$this->info['is_win'] = $is_win;
		$cid = $this->cid;
		$ClubModel = Master::getClub($cid);

        $flevel = $flv;
		$fname = '';
		if(!empty($fcid)){
			$fClubModel = Master::getClub($fcid);
			$fname = $fClubModel->info['name'];
		}
		
		//如果获胜
		if($is_win){
			$this->info['exp'] = $this->info['exp'] * $flevel;
			$redis11Model = Master::getRedis11();
			$rid = $redis11Model->get_rank_id($cid);
			$rid = empty($rid)?0:$rid;
			//1~10名的帮派，奖励+500
			if($rid >= 1 && $rid <= 10){
				$this->info['extra'] = 500;
			}
			//11~20名的帮派，奖励+300
			if($rid >= 11 && $rid <= 20){
				$this->info['extra'] = 300;
			}
			//21~50名的帮派，奖励+200
			if($rid >= 21 && $rid <= 50){
				$this->info['extra'] = 200;
			}
			//51~100名的帮派，奖励+100
			if($rid >= 51 && $rid <= 100){
				$this->info['extra'] = 100;
			}
		}
		
		$this->info['name'] = $ClubModel->info['name'];  //我公会name
		$this->info['fcid'] = $fcid;				     //敌方公会id
		$this->info['fname'] = $fname; //敌方公会name
		$this->info['flevel'] = $flevel; //敌方公会等级
		
		//我方pk阵容
		$Sev51Model = Master::getSev51($cid);
		$this->info['setMems'] = array_keys($Sev51Model->info['list']);
		$this->info['is_get'] = 1;
		$this->save();

        unset($ClubModel,$fClubModel);
		
	}
	
	/**
	 * 构造输出
	 */
	public function mk_outf(){
		$this->outof = array();
		$info = self::rwd_time();
		if(empty($info['ltype'])){
			return $this->outof;
		}
		if( $this->info['extra'] > 0){
			$this->outof = array(
				'isWin' => $this->info['is_win'],
				'club' => array(
					array( 'id' => 30, 'kind' => 2, 'count' =>  $this->info['exp'] ),
					array( 'id' => 30, 'kind' => 2, 'count' =>  $this->info['extra'] ),
				),
				'member' => array(
					array( 'id' => 32, 'kind' => 2, 'count' =>  $this->info['exp'] ),
					array( 'id' => 32, 'kind' => 2, 'count' =>  $this->info['extra'] ),
				),
			);
		}else{
			$this->outof = array(
				'isWin' => $this->info['is_win'],
				'club' => array(
					array( 'id' => 30, 'kind' => 2, 'count' =>  $this->info['exp'] ),
				),
				'member' => array(
					array( 'id' => 32, 'kind' => 2, 'count' =>  $this->info['exp'] ),
				),
			);
		}
		
		$this->outof['getCuid'] = $this->info['getCuid'];  //领取的uid
		$this->outof['getCname'] = $this->info['getCname'];  //领取的名字
		$this->outof['setMems'] = $this->info['setMems'];  //领取的名字数组
		$this->outof['getMems'] = $this->info['getMems'];  //领取的名字数组
		$this->outof['flevel'] = $this->info['flevel'];  //敌方公会等级
		$this->outof['fname'] = $this->info['fname'];  //敌方公会name
		$this->outof['fservid'] = Game::get_sevid_club($this->info['fcid']);  //敌方公会id
		$this->outof['is_get'] = $this->info['is_get'];  
		$this->outof['fcid'] = $this->info['fcid'];  
		return $this->outof;
	}
	
	
	
	
	/**
	 * 奖励时间控制
	 */
	public function rwd_time(){
		
		/*
		$Sev50Model = Master::getSev50();
		if(empty($Sev50Model->info[$this->cid])){
			return array(
				'next' => 0,  
				'ltype' => 0,  //0:不显示,1:显示阶段,2:可领取阶段
			);
		}
		*/
		$next = 0; //倒计时
		$ltype = 1;//0:不显示,1:显示阶段,2:可领取阶段
		$wday  =  date("w");//今天周几
		$T_0 = strtotime(date("Y-m-d"));  //今天0点的时间错
		switch($wday){
			case 1:
				$T_12_5 = $T_0 + 60 * 60 * 12 + 60 * 5;  //今天12:05点的时间错
				if($_SERVER['REQUEST_TIME'] < $T_12_5){
					$next = $T_12_5;
				}else{
					$next = $T_0 + 2*24*60*60;
					$ltype = 2;
				}
				break;
			case 3:
				$T_12_5 = $T_0 + 60 * 60 * 12 + 60 * 5;  //今天12:05点的时间错
				if($_SERVER['REQUEST_TIME'] < $T_12_5){
					$next = $T_12_5;
				}else{
					$next = $T_0 + 2*24*60*60;
					$ltype = 2;
				}
				break;
			case 5:
				$T_12_5 = $T_0 + 60 * 60 * 12 + 60 * 5;  //今天12:05点的时间错
				if($_SERVER['REQUEST_TIME'] < $T_12_5){
					$next = $T_12_5;
				}else{
					$next = $T_0 + 3*24*60*60;
					$ltype = 2;
				}
				break;
			case 2:
			case 4:	
				$next = $T_0 + 24*60*60;
				$ltype = 2;
				break;
			case 6:	
				$next = $T_0 + 2*24*60*60;
				$ltype = 2;
				break;
			case 0:	
				$next = $T_0 + 24*60*60;
				$ltype = 2;
				break;
			default:
					Master::error(SEV_54_XITONGMANG);
					break;
		}
		return array(
			'next' => $next,
			'ltype' => $ltype,
		);
	}
	
}



