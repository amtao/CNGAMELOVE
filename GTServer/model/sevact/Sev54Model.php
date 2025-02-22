<?php
/**
 * 帮会战-信息
 */
require_once "SevBaseModel.php";
class Sev54Model extends SevBaseModel
{
    public $comment = "帮会战-信息";
    public $act = 54;//活动标签
    
    
	/**
	 * 构造输出
	 */
	public function out_data($uid){
		$this->outof = array();
		//帮会战时间控制
		$s_time = self::show_time();
		$g_macth = self::get_macth();
		$g_bminfo = self::get_bminfo($uid);
		
		$Sev55Model = Master::getSev55($this->cid);
		$rwd_time = $Sev55Model->rwd_time();
		$this->outof = array(
			'tType' => $s_time['tType'],	//1:报名;2:匹配;3:等待开战;4:开战;5:距离下次报名;
			'ltime' => array('next' => $s_time['next'], 'label' => 'kuaclubpktime'),
			'mType' => $g_macth['mType'],	//0:暂未匹配到对手帮会,1:匹配到的对手帮会
			'msevid' => $g_macth['msevid'], //匹配到对手的帮会服务器id
			'mName' => $g_macth['mName'],	//匹配到对手的帮会名字
			'mytype' => $g_bminfo['mytype'],	//0:未获取参战资格,1:获取参战资格
			'allshili' =>  $g_bminfo['allshili'],  //参战总战力
			'heroid' => $g_bminfo['heroid'],	//我参战门客id
			'hname' => $g_bminfo['hname'],	//我参战门客名字
			'hpower' => $g_bminfo['hpower'],	//我参战门客战斗力
			'usejn' => $g_bminfo['usejn'],	//使用锦囊id
			'usejnhid' => $g_bminfo['usejnhid'],	//伏兵对应门客id
			'isWin' => 0,	//是否获胜0:失败 1:获胜 2:平局 tType = 5生效
			'gejifen' => 0,	//获得积分 tType = 5生效
			'rwdltype' => empty($rwd_time['ltype'])?0:1,  //0:不显示,1:显示阶段
			'rwdltime' => array('next' => $rwd_time['next'], 'label' => 'kuaclubrwdtime'),  //奖励倒计时
		);
		return $this->outof;
	}
    
	/**
	 * 时间控制
	 */
	public function show_time(){
		
		$next = 0; //倒计时
		$tType = 0;//1:报名;2:匹配;3:等待开战;4:开战;5:距离下次报名;
		$wday  =  date("w");//今天周几
		
		switch($wday){
			//报名
			case 2:
			case 4:
			case 0:
				$next = strtotime(date("Y-m-d",strtotime("+1 day")));
				$tType = 1;
				break;
			case 6:
				$next = strtotime(date("Y-m-d",strtotime("+2 day")));
				$tType = 1;
				break;
			case 1:
			case 3:
			case 5:
				$T_0 = strtotime(date("Y-m-d"));  //今天0点的时间错
				$T_1 = $T_0 + 60 * 60;  //今天1点的时间错
				$T_12 = $T_0 + 60 * 60 * 12;  //今天12点的时间错
				$T_12_5 = $T_0 + 60 * 60 * 12 + 60 * 5;  //今天12:05点的时间错
				$T_24 = $T_0 + 60 * 60 * 24;  //今天12:05点的时间错
				//1:匹配期间
				if( $_SERVER['REQUEST_TIME'] < $T_1){
					$next = $T_1;
					$tType = 2;
				}elseif($_SERVER['REQUEST_TIME'] < $T_12){
					$next = $T_12;
					$tType = 3;
				}elseif($_SERVER['REQUEST_TIME'] < $T_12_5){
					$next = $T_12_5;
					$tType = 4;
				}else{
					$next = $T_24;
					$tType = 5;
				}
				break;
				default:
					Master::error(SEV_54_XITONGMANG);
					break;
		}
		return array(
			'next' => $next,
			'tType' => $tType,
		);
	}
    
	
	/*
	 * 获取匹配信息
	 */
	public function get_macth(){
		
		$macth = array(
			'mType' => 0,	//0:暂未匹配到对手帮会,1:匹配到的对手帮会
			'msevid' => 0,  //帮会服务器id
			'mName' => '',	//匹配到对手的帮会名字
		);
		
		$cid = $this->cid;  //公会id
		
		//帮会战-匹配列表
		$Sev50Model = Master::getSev50();
		if(empty($Sev50Model->info[$cid])){
			//前100 报名前已匹配
			$redis11Model = Master::getRedis11();
	    	$rank_id = $redis11Model->get_rank_id($cid);
	    	if( empty($rank_id) || $rank_id > 100 ){
	    		return $macth;
	    	}
	    	//找出匹配排名id
	    	$left =  $rank_id % 2 ;
	    	$rid = $left == 1?$rank_id+1:$rank_id-1;
	    	//找出匹配公会id
	    	$fcid = $redis11Model->get_member($rid);
	    	$fcinfo = $Sev50Model->add($cid,$fcid);
	    	$macth = array(
				'mType' => 1,	//0:暂未匹配到对手帮会,1:匹配到的对手帮会
	    		'msevid' => $fcinfo['msevid'],	//帮会对应服务器id
				'mName' => $fcinfo['fname'],	//匹配到对手的帮会名字
			);
		}else{
			$macth = array(
				'mType' => 1,	//0:暂未匹配到对手帮会,1:匹配到的对手帮会
				'mName' => $Sev50Model->info[$cid]['fname'],	//匹配到对手的帮会名字
				'msevid' => $Sev50Model->info[$cid]['msevid'],	//匹配到对手的帮会名字
			);
		}
		return $macth;
	}
	
	
	/*
	 * 获取我方报名信息
	 */
	public function get_bminfo($uid){
		
		$bminfo = array(
			'mytype' => 0,	//0:未获取参战资格,1:获取参战资格
			'allshili' => 0,  //参战总战力
			'heroid' => 0,   //我参战门客id
			'hpower' => 0,	//我参战门客战斗力
			'hname' => '',	//我参战门客name
			'usejn' => 0,	//使用锦囊id
			'usejnhid' => 0,	//伏兵id
		);
		
		$cid = $this->cid;  //公会id
		$Sev51Model = Master::getSev51($cid);
		if(!empty($Sev51Model->info['list'])){
			$uidinfo = $Sev51Model->reset_hero($uid);
			$clubpk = Game::get_peizhi('clubpk');
			$upnum = empty($clubpk['upnum'])?10:$clubpk['upnum'];
			$mytype = count($Sev51Model->info['list'])<$upnum?0:1;
			$bminfo = array(
				'mytype' => $mytype,//0:未获取参战资格,1:获取参战资格
				'allshili' => $Sev51Model->info['allshili'],  //参战总战力
				'hpower' => empty($uidinfo['hpower'])?0:$uidinfo['hpower'],	//我参战门客战斗力
				'hname' => empty($uidinfo['hname'])?0:$uidinfo['hname'],	//我参战门客id
				'heroid' => empty($uidinfo['heroid'])?0:$uidinfo['heroid'],	//我参战门客id
				'usejn' => empty($uidinfo['jnuse'])?0:$uidinfo['jnuse'],	//使用锦囊id
				'usejnhid' => empty($uidinfo['jnfunc']['heroid'])?0:$uidinfo['jnfunc']['heroid'],	//伏兵id
			);
		}
		$redis11Model = Master::getRedis11();
		$rank_id = $redis11Model->get_rank_id($cid);
    	if( !empty($rank_id) && $rank_id <= 100 ){
    		$bminfo['mytype'] = 1;
    	}
    	
		return $bminfo;
	}
	
}






