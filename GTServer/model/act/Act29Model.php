<?php
require_once "ActBaseModel.php";
/*
 * 寻访--NPC
 */
class Act29Model extends ActBaseModel
{
	public $atype = 29;//活动编号
	
	public $comment = "寻访-NPC";
	public $b_mol = "xunfang";//返回信息 所在模块
	public $b_ctrl = "xfNPC";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		//  红颜id =>  好感度
		'isFirst' => 0,  //  1:第一次寻访
		'lastId' => 0,//记录随机到门客id
		'lastNpcId'=> 0,//记录随机特殊事件id
	);

	/**
	 * 触发加道具所返回的NPCid
	 */
	public function xf_add(){
		//获取加道具的NPC列表
		// $npc_list = self::NPC_list(1);
		$npc_list = self::NPC_list(4);
		//随机获取一个NPCid
		return array_rand($npc_list,1);
	}
	
	/**
	 * 触发减道具所返回的NPCid
	 */
	public function xf_sub(){
		//获取减道具的NPC列表
		// $npc_list = self::NPC_list(2);
		$npc_list = self::NPC_list(4);
		//随机获取一个NPCid
		return array_rand($npc_list,1);
	}
	
	/**
	 * 触发红颜所返回的NPCid
	 */
	public function xf_wifi(){
		//获取红颜的NPC列表
		// $npc_list = self::NPC_list(3);
		$npc_list = self::NPC_list(4);
		//过滤掉没必要的红颜
		foreach ($npc_list as $k => $v){
		    $wifeCfg = Game::getcfg_info('wife', $v['wfid']);
		    if(empty($wifeCfg) || $wifeCfg['open'] == 0){
		        unset($npc_list[$k]);
		    }
		}
		return array_rand($npc_list,1);
	}

	/**
	 * 触发门客羁绊
	 */
	public function xf_servant(){
		//获取门客羁绊
		$npc_list = self::NPC_list(4);
		//随机获取一个NPCid
		$npc_list = Game::array_rand($npc_list,1);
		//获取npc信息
		return  $npc_list[0];
	}
	
	/**
	 * 获取当前号感度
	 * @param $wfid  红颜id
	 */
	public function xf_get_haogan($wfid){
		if(empty($this->info[$wfid])){
			$this->info[$wfid] = 1;
		}
		if($this->info[$wfid] > 5){
			$this->info[$wfid] = 5;
		}
		return $this->info[$wfid];
	}
	
	/**
	 * 加好感度
	 * @param $num   要增加的数值
	 */
	public function xf_add_haogan($npcID,$num){
		//加红颜好感度
		if(empty($this->info[$npcID])){
			$this->info[$npcID] = 1;
		}
		else{
			$this->info[$npcID] += $num;
		}		
		$this ->_save();
	}
	
	
	
	/**
	 * NPC 分类   
	 * @param unknown_type $type  0:全部   1:加道具的npc列表  2:减道具的npc列表  3:红颜的npc列表
	 */
	public function NPC_list($type = 0){
		//ncp配置
		$cfg_xf_NPC = Game::getcfg('xf_NPC');
		
		if(empty($type)){
			return $cfg_xf_NPC;
		}

		$userModel = Master::getUser($this->uid);
		$bmap = $userModel->info['bmap'];
		
		$get_type = array(2);  //配置中  2:有加有减的NPC
		switch($type){
			case 1:  //1:加道具的npc列表 
				$get_type[] = 0;//配置中  2:有加的NPC
				break;
				
			case 2:  // 2:减道具的npc列表
				$get_type[] = 3;//配置中  2:有减的NPC
				break;
				
			case 3:   //红颜的npc列表
				$get_type = array(1); //配置中  2:红颜的NPC
				break;
			case 4:
				$get_type = array(4); //门客
				break;
			default:
				Master::error("act29_NPC_list_err_".$type);
		}
		
		//NPC列表
		$npc_list = array();
		foreach($cfg_xf_NPC as $k => $v){
			if(!in_array($v['type'],$get_type)){
				continue;
			}
			if ($v['build'] != 0){
				$l = Game::getcfg_info('xf_build', $v['build']);
				if (!empty($l) && $l['lock'] >= $bmap)continue;
			}
			$npc_list[$k] = $v;
		}
		return $npc_list;
		
	}

	/**
	 * 是否触发特殊事件
	 */
	public function getSpecId(){
		$act39Model = Master::getAct39($this->uid);
		$id = $act39Model->info['id'];
		$lid = $this->info['isFirst'];
		$cfg_xf_sp = Game::getcfg('xf_clientevent');
		foreach($cfg_xf_sp as $k => $v){
			if ($v['type'] == 5 && $v['object'] <= $id && $lid < $v['object']){
				$this->info['isFirst'] = $v['object'];
		    	$this->_save();
				return $v['id'];
			}
		}

		$act6002Model = Master::getAct6002($this->uid);
		$spid = $act6002Model->getHeroXF();
		if ($spid != 0){
			return $spid;
		}

		$spid = $act6002Model->getWifeXF();
		if ($spid != 0){
			return $spid;
		}

		return 0;
	}

	/**
	 * 获取门客的特殊事件
	 */
	public function xf_sp_servant($build=0){
		//获取门客羁绊
		$npc_list = self::getSPType(1, $build);
		//随机获取一个NPCid
		return Game::get_rand_key1($npc_list,'prob');
	}

	/**
	 * 获取门客的特殊事件
	 */
	public function xf_sp_wife($build = 0){
		//获取门客羁绊
		$npc_list = self::getSPType(2, $build);
		//随机获取一个NPCid
		return Game::get_rand_key1($npc_list,'prob');
	}

	/**
	 * 获取门客类型的特殊事件
	 */
	public function xf_sp_servant_type($build = 0){
		//获取门客羁绊
		$npc_list = self::getSPType(4, $build);
		//随机获取一个NPCid
		return Game::get_rand_key1($npc_list,'prob');
	}

	/**
	 * 获取门客性格的特殊事件
	 */
	public function xf_sp_servant_char($build = 0){
		//获取门客羁绊
		$npc_list = self::getSPType(3, $build);
		//随机获取一个NPCid
		return Game::get_rand_key1($npc_list,'prob');
	}

	private function getSPType($type = 1, $build = 0){
		//ncp配置
		$cfg_xf_sp = Game::getcfg('xf_clientevent');
		$npc_list = array();
		//用户数据
		$teamModel = Master::getTeam($this->uid);
		$userModel = Master::getUser($this->uid);
		$bmap = $userModel->info['bmap'];
		$allep = $teamModel->info['allep'];
		$shili = $allep[1] + $allep[2] + $allep[3] + $allep[4];
		//羁绊数据
		$act6001Model = Master::getAct6001($this->uid);
		//
		foreach($cfg_xf_sp as $k => $v){
			if ($v['type'] != $type || $v['prob'] == 0){
				continue;
			}
			if ($v['locale'] != 0 && $build != 0 && $build != $v['locale']){
			    continue;
            }
			if ($v['locale'] != 0){
				$l = Game::getcfg_info('xf_build', $v['locale']);
				if (!empty($l) && $l['lock'] >= $bmap)continue;
			}
			if ($v['shili'] > $shili){
				continue;
			}
			if ($v['ep'] != 0){
				$ss = explode('|', $v['ep']);
				if (count($ss) < 2 || $allep[intval($ss[0])] < intval($ss[1]))continue;
			}
			if ($v['jibang'] != 0 && $v['object'] != 0){
				if ($v['type'] == 1 || $v['type'] == 6 || $v['type'] == 3 || $v['type'] == 4){
					if ($act6001Model->getHeroJB($v['object']) < $v['jibang'])continue;
				}else if ($v['type'] == 2){
					if ($act6001Model->getWifeJB($v['object']) < $v['jibang'])continue;
				}
			}
			if ($v['qiyun'] != 0){
				if ($act6001Model->getHeroSW($v['object']) < $v['qiyun'])continue;
			}
			$npc_list[$k] = $v;
		}
		return $npc_list;		
	}

	/*
	 * 可以选择选项么
	 */
	public function canSelectStory($groupId){
		$info = $this->info;
		if ($info['lastNpcId'] == 0)return false;
		$npcitem = Game::getcfg_info('xf_clientevent', $info['lastNpcId']);
		if (!empty($npcitem)){
			return $npcitem['group'] == $groupId;			
		}
		return false;
	}

 	public function saveLastNpc($npcid, $heroid){
 		$this->info['lastId'] = $heroid;
 		$this->info['lastNpcId'] = $npcid;
 		$this->_save();
 	}
	
}