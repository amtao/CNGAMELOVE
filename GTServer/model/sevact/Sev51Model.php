<?php
/**
 * 帮会战-参赛阵容
 */
require_once "SevBaseModel.php";
class Sev51Model extends SevBaseModel
{
    public $comment = "帮会战-参赛阵容";
    public $act = 51;//活动标签
    
    /*
	 * 初始化结构体
	 */
	public $_init = array(
		  'servid' => 0,  //服务器id
		  'cname' => '',  //公会名字
		  'allshili' => 0 ,   // 所有战斗力
		  'list' => array(
				/*
			  uid => array(
			  	  'name' => '',   //玩家信息
			  	  'post' => 0,    //职位
			      'heroid' => 0   //门客id
			      'hname' => '' , //门客name
			      'hpower' => 0,  // 战斗力
			      
			      'jnuse' => 0,  //使用的锦囊
			      'jnfunc' => array(),  //使用的锦囊
			      
			      
			  )
			  */
		  ),
		  'post' => array(   //职位buff
			'mz'  => 5,
			'fmz' => 3,
			'jy'  => 1,
			'cy'  => 0,
		  ),
		  'mzpic' => array(   //盟主头像
		  	/*
			'sex'  => 0,
			'job' => 0,
			'level'  => 0,
			'chenghao'  => 0,
			*/
		  ),
		  'clevel' => 0,   //公会等级
	);
	
	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$this->outof = array();
		$this->outof['servid'] = $this->info['servid'];
		$this->outof['cname'] = $this->info['cname'];
		$this->outof['allshili'] = $this->info['allshili'];
		$this->outof['list'] = array();
		if(!empty($this->info['list'])){
			foreach($this->info['list'] as $uid => $info){
				$this->outof['list'][$info['post'].$info['pxtime'].$uid] = array(
					'uid' => $uid,
				    'name' => $info['name'],
					'post' => empty($info['post'])?4:$info['post'],
					'heroid' => $info['heroid'],
					'hname' => $info['hname'],
					'hpower' => $info['hpower'],
					'jnid' => $info['jnuse'],
				);
				unset($info);
			}
			ksort($this->outof['list']);
			$this->outof['list'] = array_values($this->outof['list']);
		}
		$this->outof['allnum'] = count($this->info['list']);
		$this->outof['post']   = $this->info['post'];
		$this->outof['clevel'] = intval($this->info['clevel']);
		if(empty($this->outof['clevel'])){
			$ClubModel = Master::getClub($this->cid);
			$this->outof['clevel'] = intval($ClubModel->info['level']);
		}
		
		if(empty($this->info['mzpic'])){
			$cid = $this->cid;
			$ClubModel = Master::getClub($cid);
			$getBase = $ClubModel->getBase();
			if(!empty($getBase['mzUID'])){
				$mzuid = $getBase['mzUID'];
				$fUserdate = Master::fuidData($mzuid);
				$this->info['mzpic']['sex'] = $fUserdate['sex'];
				$this->info['mzpic']['job'] = $fUserdate['job'];
				$this->info['mzpic']['level'] = $fUserdate['level'];
				$this->info['mzpic']['chenghao'] = $fUserdate['chenghao'];
				$this->save();

                unset($fUserdate);
			}
		}
		
		$this->outof['mzpic'] = $this->info['mzpic'];

		unset($ClubModel);

		return $this->outof;
	}
	
	/*
	 * 构造业务输出数据
	 */
	public function outf_pk(){
		
		$this->outof = array();
		$this->outof['servid'] = $this->info['servid'];
		$this->outof['cname'] = $this->info['cname'];
		$this->outof['allshili'] = $this->info['allshili'];
		$this->outof['allnum'] = count($this->info['list']);
		$this->outof['post']   = $this->info['post'];
		$this->outof['clevel'] = intval($this->info['clevel']);
		//兼容没有参加并且被匹配到的
		if(empty($this->outof['clevel'])){
			$ClubModel = Master::getClub($this->cid);
			$this->outof['clevel'] = intval($ClubModel->info['level']);
		}
		
		$this->outof['list'] = array();
		foreach($this->info['list'] as $uid => $info){
			$info['post'] = empty($info['post'])?4:$info['post'];
			$this->outof['list'][$info['post'].$info['pxtime'].$uid] = $info;
			$this->outof['list'][$info['post'].$info['pxtime'].$uid]['uid'] = $uid;
			$this->outof['list'][$info['post'].$info['pxtime'].$uid]['hh'] = 0;  //回合数
			$this->outof['list'][$info['post'].$info['pxtime'].$uid]['ispk'] = 0;

            unset($info);
		}
		ksort($this->outof['list']);

        unset($ClubModel);

		return $this->outof;
		
	}
	
	
	
	/**
	 * 添加一条报名信息
	 */
    public function baoming($uid,$heroid){
    	
    	if(!self::is_open()){
    		Master::error(CLUB_OPEN_BAOMING);
    	}
    	
    	$cid = $this->cid;
    	//门客出战列表
        $Act42Model = Master::getAct42($uid);
    	//撤销
        if(!empty($this->info['list'][$uid]['heroid'])){
        	$Act42Model->reset_fight($this->info['list'][$uid]['heroid']);
            Game::cmd_other_flow($this->cid, 'club', $this->hid, array($uid), 45, $this->info['list'][$uid]['heroid'], -1,0 );
        }
        //派遣
        $Act42Model->go_fight($heroid);
        
    	//玩家个人信息
    	$UserModel = Master::getUser($uid);
    	//玩家公会信息
		$ClubModel = Master::getClub($cid);
    	//门客配置信息
    	$cfg_hero = Game::getcfg('hero');
    	
    	//战斗力
    	$TeamModel  = Master::getTeam($uid);
        $hpower = $TeamModel->getHerodamage($heroid);
		//个人玩家信息
		$oldinfo = empty($this->info['list'][$uid])?array():$this->info['list'][$uid];
    	$this->info['list'][$uid] = array(
    		'name' => Game::filter_char($UserModel->info['name']),   //玩家信息
    		'post' => $ClubModel->info['members'][$uid]['post'],    //职位
      		'heroid' => $heroid,   //门客id
      		'hname' => $cfg_hero[$heroid]['name'], //门客name
      		'hpower' => $hpower,  // 战斗力
    		'jnuse' => empty($oldinfo['jnuse'])?0:$oldinfo['jnuse'],  //使用锦囊
    		'jnfunc' => array(
    			'heroid' => empty($oldinfo['jnfunc']['heroid'])?0:$oldinfo['jnfunc']['heroid'],
				'hpower' => empty($oldinfo['jnfunc']['hpower'])?0:$oldinfo['jnfunc']['hpower'],
	    		'name'  => empty($oldinfo['jnfunc']['name'])?'':$oldinfo['jnfunc']['name'],
	    		'to' => empty($oldinfo['jnfunc']['to'])?0:$oldinfo['jnfunc']['to'],  //0:我方  1:敌方
	    		'huihe' => empty($oldinfo['jnfunc']['huihe'])?5:$oldinfo['jnfunc']['huihe'],  //最大回合数
	    		'add'	=> empty($oldinfo['jnfunc']['add'])?0:$oldinfo['jnfunc']['add'],  //基数100   提升多少    敌方:减去多少
    		),  //使用锦囊
    		'pxtime' => empty($oldinfo['pxtime'])?$_SERVER['REQUEST_TIME']:$oldinfo['pxtime'],  //派遣时间的相对值
		);
		$this->info['servid'] = Game::get_sevid_club($cid);
		$this->info['cname'] = Game::filter_char($ClubModel->info['name']);
		$this->info['clevel'] = $ClubModel->info['level'];
		//重新计算总战力
		$this->info['allshili'] = 0;
		foreach($this->info['list'] as $k => $v){
			$this->info['allshili'] += $v['hpower'];
		}
		
		//参赛资格
    	$redis11Model = Master::getRedis11();
    	$rank_id = $redis11Model->get_rank_id($cid);
    	if( empty($rank_id) || $rank_id > 100 ){
	    	$clubpk = Game::get_peizhi('clubpk');
			$upnum = empty($clubpk['upnum'])?10:$clubpk['upnum'];
			if(count($this->info['list']) >= $upnum){
				$Sev52Model = Master::getSev52();
				$Sev52Model->add($cid);
			}
    	}
		
		$this->save();
        Game::cmd_other_flow($this->cid, 'club', $this->hid, array($uid), 45, $heroid, 1,1 );
    }
    
	/**
	 * 取消一条报名信息
	 */
    public function cancel($uid,$tip = false){
   	 	if(!self::is_open()){
   	 		if($tip){
   	 			self::out_club($uid);
   	 			return 0;
   	 		}
    		Master::error(CLUB_OPEN_BAOMING);
    	}
    	//门客出战列表
        $Act42Model = Master::getAct42($uid);
        //撤销
        if(!empty($this->info['list'][$uid]['heroid'])){
        	$Act42Model->reset_fight($this->info['list'][$uid]['heroid']);

            Game::cmd_other_flow($this->cid, 'club', $this->hid, array($uid), 45, $this->info['list'][$uid]['heroid'], -1,0 );
        }
        //取消报名
        if(!empty($this->info['list'][$uid]['hpower'])){
        	$this->info['allshili'] -= $this->info['list'][$uid]['hpower'];
        }
        unset($this->info['list'][$uid]);
		$this->save();
    }
    
	/**
	 * 取消一条报名信息
	 */
    public function out_club($uid){
   	 	
    	$t_12= Game::day_0(12); //今天12点
    	if( Game::is_over($t_12) ){  //大于12点不处理
    		return 0;
    	}
    	//门客出战列表
        $Act42Model = Master::getAct42($uid);
        //撤销
        if(!empty($this->info['list'][$uid]['heroid'])){
        	$Act42Model->reset_fight($this->info['list'][$uid]['heroid']);
        }
        //取消报名
        if(!empty($this->info['list'][$uid]['hpower'])){
        	$this->info['allshili'] -= $this->info['list'][$uid]['hpower'];
        }
        unset($this->info['list'][$uid]);
		$this->save();
    }
    
	/**
	 * 重置职位
	 */
    public function reset_post($uid){
    	if(empty($this->info['list'][$uid])){
			return 0;
		}
   	 	if(!self::is_open()){
    		return 0;
    	}
		$cid = $this->cid;
    	$ClubModel = Master::getClub($cid);
    	$this->info['list'][$uid]['post'] = $ClubModel->info['members'][$uid]['post'];
    	$this->save();
    }
    
	/**
	 * 重置公会等级
	 */
    public function reset_clevel($clevel){
    	if(!self::is_open()){
    		return 0;
    	}
    	$this->info['clevel'] = $clevel;
    	$this->save();
    }
    
	/**
	 * 重置门客
	 */
    public function reset_hero($uid){
    	
    	
		if(empty($this->info['list'][$uid])){
			return array();
		}
		
    	if(!self::is_open()){
    		return $this->info['list'][$uid];
    	}
    	
		$heroid = $this->info['list'][$uid]['heroid'];
		//战斗力
    	$TeamModel  = Master::getTeam($uid);
        $hpower = $TeamModel->getHerodamage($heroid);
		$this->info['list'][$uid]['hpower'] = $hpower;
    	$this->save();
    	return $this->info['list'][$uid];
    }
    
	/**
	 * 使用锦囊
	 * @param $uid  玩家id
	 * @param $id    道具id
	 * @param $hid   门客id (伏兵锦囊)
	 */
    public function usejinnang($uid,$id,$heroid = 0){
    	if(!self::is_open()){
    		Master::error(CLUB_OPEN_BAOMING);
    	}
    	//未派遣门客
    	if(empty($this->info['list'][$uid])){
    		Master::error(SEV_51_MENKECHUZHAN);
    	}
    	if(!empty($this->info['list'][$uid]['jnuse'])){
    		Master::error(SEV_51_JINNANG);
    	}
    	
    	$this->info['list'][$uid]['jnuse'] = $id;
    	$this->info['list'][$uid]['jnfunc'] = array(
    		'heroid' => $heroid,
			'hpower' => 0,
    		'name'  => '',
    		'to' => 0,  //0:我方  1:敌方
    		'huihe' => 5,  //最大回合数
    		'add'	=> 0,  //基数100   提升多少    敌方:减去多少
    	);
    	//如果是伏兵锦囊
    	switch($id){
    		case 280:
    			$HeroModel = Master::getHero($uid);
	        	$HeroModel->check_info($heroid);
		    	$TeamModel  = Master::getTeam($uid);
		        $hpower = $TeamModel->getHerodamage($heroid);
	    		$this->info['list'][$uid]['jnfunc']['hpower'] = $hpower;
	    		$this->info['list'][$uid]['jnfunc']['name'] = '伏兵';
	    		//门客出战列表
		        $Act42Model = Master::getAct42($uid);
		        $Act42Model->go_fight($heroid);
        
    			break;
    		case 281:
    			$this->info['list'][$uid]['jnfunc']['add'] = 20;
    			break;
    		case 282:
    			$this->info['list'][$uid]['jnfunc']['add'] = 50;
    			break;
    		case 283:
    			$this->info['list'][$uid]['jnfunc']['to'] = 30;
    			$this->info['list'][$uid]['jnfunc']['add'] = 30;
    			break;
    		case 284:
    			$this->info['list'][$uid]['jnfunc']['huihe'] = 1;
    			$this->info['list'][$uid]['jnfunc']['add'] = 300;
    			break;
    		case 285:
    			$this->info['list'][$uid]['jnfunc']['huihe'] = 10;
    			break;
    		default:
    			Master::error(SEV_51_DAOJUCUO);
    			break;
    	}
    	$this->save();
        Game::cmd_other_flow($this->cid, 'club', $this->hid, array($uid), 46, $id, 1,$heroid );
    }
    
	/*
	 * 是否可以操作
	 */
	public function is_open(){
		$wday  =  date("w");//今天周几
		if(in_array($wday,array(0,2,4,6))){
			return true;
		}
		return false;
	}
    
	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		$data = $this->get_outf();
		Master::back_data(0,'club','clubKuaCszr',$data);
	}
	
	
}

