<?php
//卡牌
require_once "AModel.php";
class CardModel extends AModel
{
    protected $_syn_w = true;
	public $_key = "_card";
	/*
	protected  $updateSetKey = array(
		'exp','senior','epskill','pkskill','ghskill','level',
	);
	protected $updateAddKey =  array(
		'zzexp','pkexp',
		'e1','e2','e3','e4',
	);*/
	
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());

		if($this->info == false){
			$table = 'card_'.Common::computeTableId($this->uid);
			$sql = "select * from `{$table}` where `uid`='{$this->uid}'";
    		$db = $this->_getDb();
			if (empty($db))
			{
				Master::error('dberruid_'.$this->uid);
				return false;
			}
			$data = $db->fetchArray($sql);
			if($data == false) $data = array();
			
			$info = array();
			foreach ($data as $v){
				//$v['epskill'] = json_decode($v['epskill'],1); 
				//$v['pkskill'] = json_decode($v['pkskill'],1);
				//$v['ghskill'] = json_decode($v['ghskill'],1);
				$info[$v['cardid']] = $v;
			}
			$this->info = $info;
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	/*
	 * 检查是否合法 并返回数据
	 */
	public function check_info($id,$is_click = false){
		if (empty($this->info[$id])){
			if ($is_click){
				return false;
			}
			Master::error('card_id_err_'.$id);
		}else{
			/*if($is_click === false){
				$Act129Model = Master::getAct129($this->uid);
				$isBanish = $Act129Model->isBanish($id);
				if($isBanish){
					Master::error(BANISH_009);
				}
			}*/
			return $this->info[$id];
		}
	}

	public function findcardstarupcfg($cardid){
		$info =  $this->check_info($cardid);
		$cardCfg = Game::getcfg_info('card',$cardid);
		$starupcfg = Game::getcfg_info('card_starup',$cardCfg['quality']);
		// foreach ($starupcfg as $id => $starupcfgData){
		// 	if($starupcfgData['quality']== $cardCfg['quality'] 
		// 	&& $starupcfgData['star'] == $info['star'])
		// 	{
		// 		return $starupcfgData;
		// 	}
		// }
		return $starupcfg[$info['star']];
	}
	public function upstartCard($cardid){
		$cardData =   $this->check_info($cardid);
	
		$cardData['star'] = $cardData['star']+1;
		if($cardData['star'] == 9){
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(146,1);
		}
		$this->update_card($cardData );
		return $cardData;
	}

	public function uplvlCard($cardid,$level){
		$cardData =   $this->check_info($cardid);
	
		$cardData['level'] = $level;
		$this->update_card($cardData );
		return $cardData;
	}

	public function upImprintlvCard($cardid){
		$cardData =   $this->check_info($cardid);
	
		$cardData['imprintLv'] = $cardData['imprintLv']+1;
		$this->update_card($cardData );
		return $cardData;
	}

	public function upFlowerPointCard($cardid,$flowerPoint){
		$cardData =   $this->check_info($cardid);
	
		$cardData['flowerPoint'] = $flowerPoint;
		$this->update_card($cardData );
		return $cardData;
	}
	
	/*
	 * 获取输出值单个
	 */
	public function getCardInfo($cardid,$detail= false)
	{
		$info = $this->info[$cardid];
		if(empty($info))
		{
			return null;
		}
		$data = array(
			'id' => $info['cardid'],
			'level' => $info['level'],
			'star' => $info['star'],
			'isEquip' => intval($info['isEquip']),
			'imprintLv' => intval($info['imprintLv']),//印痕等级
			'flowerPoint' => empty($info['flowerPoint'])?'[]':$info['flowerPoint'],//卡牌升华
			'isClotheEquip' => intval($info['isClotheEquip']),
		);
		$cardCfg = Game::getcfg_info('card',$cardid);
		if($detail){
			if(empty($cardCfg))
			{
				Game::defult_error("card cfg not found".$cardid);
				return null;
			}
			//修改后属性	
			$data['e1'] = Game::getCfg_formula()->card_ep($cardCfg['ep1'],$info['level'],$info['star']);
			$data['e2'] = Game::getCfg_formula()->card_ep($cardCfg['ep2'],$info['level'],$info['star']);
			$data['e3'] = Game::getCfg_formula()->card_ep($cardCfg['ep3'],$info['level'],$info['star']);
			$data['e4'] = Game::getCfg_formula()->card_ep($cardCfg['ep4'],$info['level'],$info['star']);
			if($data['imprintLv'] > 0){
				$yinhenCfg = Game::getcfg_info('card_yinhen',$cardCfg['quality']);
				$yhInfo = $yinhenCfg[$data['imprintLv']];
				$data['e1'] += $yhInfo['ep1'];
				$data['e2'] += $yhInfo['ep2'];
				$data['e3'] += $yhInfo['ep3'];
				$data['e4'] += $yhInfo['ep4'];
			}
			// $cfg = $this->findcardstarupcfg($cardid);
			// $data['e1']= intval(floatval($cardData['ep1']) * floatval($cfg['ep1']));
			// $data['e2']= intval(floatval($cardData['ep2']) * floatval($cfg['ep2']));
			// $data['e3']= intval(floatval($cardData['ep3']) * floatval($cfg['ep3']));
			// $data['e4']= intval(floatval($cardData['ep4']) * floatval($cfg['ep4']));
			$cardFlowerCfg = Game::getcfg_info('card_flower',$cardCfg['quality']);
			$flowers = json_decode($data['flowerPoint'],true);
			foreach($flowers as $point){
				$flowerInfo = $cardFlowerCfg[$point];
				$data['e1'] += $flowerInfo['ep1'];
				$data['e2'] += $flowerInfo['ep2'];
				$data['e3'] += $flowerInfo['ep3'];
				$data['e4'] += $flowerInfo['ep4'];
			}

			//卡牌羁绊获得的属性
			$Act762Model = Master::getAct762($this->uid);
			$type1Prop = $Act762Model->info['cardForeverProp'][1][$cardid];
			$type2Prop = $Act762Model->info['cardForeverProp'][2][$cardCfg['shuxing']];

			//服装激活卡牌的额外属性
			$Act757Model = Master::getAct757($this->uid);
			$activate = $Act757Model->info['activateSmallProp'][2][$cardid];
			if(!empty($activate)){
				$data['e1'] += $activate['1'];
				$data['e2'] += $activate['2'];
				$data['e3'] += $activate['3'];
				$data['e4'] += $activate['4'];
			}
			//槽位激活解锁的技能属性
			$skillProp = $Act757Model->getSkillProp(1,$cardid);
			$data['e1'] = ceil($data['e1']*(1+$skillProp['1']/100+$type1Prop['1']+$type2Prop['1']));
			$data['e2'] = ceil($data['e2']*(1+$skillProp['2']/100+$type1Prop['2']+$type2Prop['2']));
			$data['e3'] = ceil($data['e3']*(1+$skillProp['3']/100+$type1Prop['3']+$type2Prop['3']));
			$data['e4'] = ceil($data['e4']*(1+$skillProp['4']/100+$type1Prop['4']+$type2Prop['4']));
		}
		
		return $data;
	}
	
	/*
	 * 获取简略输出值单个
	 */
	public function getEasyBase_buyid($cardid){
		$info = $this->info[$cardid];
		$data = array(
			'id' => $info['cardid'],
			'level' => $info['level'],
			'star' => $info['star'],
			'isEquip' => intval($info['isEquip']),
			'imprintLv' => intval($info['imprintLv']),
			'flowerPoint' => $info['flowerPoint'],
			'isClotheEquip' => intval($info['isClotheEquip']),
		);
		return $data;
	}
	/*
	 * 是否有某个卡牌
	 */
	public function hasCard($cardid){
		if(!empty($this->info[$cardid]))
			return true;
		return false;
	}

	public function backCardList(){
		Master::back_data($this->uid,"card","cardList",$this->getCardList());
	}
	

	public function getCardList($detail =false)
	{
		$data = array();
		if (is_array($this->info))
		{
			foreach ($this->info as $k=>$v)
			{
				$_card = $this->getCardInfo($k,$detail);
				
				if(!empty($_card)){
					$data[] = $_card;
				}				
			}
		}
		return $data;
	}
	
	public function drawAddCard($cardItemn,&$backdata){
		if($cardItemn["kind"] == 99)
		{	
			$cardid = $cardItemn["itemid"];
			

			if($this->add_card($cardid ))
			{
				$cardData = array(
					$cardid =>1
				);
				$backdata["drawids"][count($backdata["drawids"])+1] = $cardData ;
				//$backdata["addcards"][$cardid ] = $this->getCardInfo($cardid );
			}else{
				$cardData = array(
					$cardid =>0
				);
				$backdata["drawids"][count($backdata["drawids"])+1 ]=$cardData ;
			}
		}else{

			Master::add_item($this->uid, $cardItemn["kind"], $cardItemn["itemid"], $cardItemn["num"],"");
			$backdata["drawItems"][] = array("kind"=>$cardItemn["kind"],"id"=>$cardItemn["itemid"],"count"=>$cardItemn["num"]);
		}
	}
	
	/*
	 * 添加一个卡牌
	 */
	public function add_card($id){

		//判断这个卡牌有没有
		//获取卡牌配置
		//获取卡牌升星强化数据
		$cardcfg = Game::getcfg_info('card',$id);
		/*if (empty($cardcfg[$id])){
			Master::error('card_cfg_id_err_'.$id);
		}
		$card_cfg_info = $cardcfg[$id];
		//格式化
		$skills= array();
		foreach($card_cfg_info['skills'] as $v){
			$skills[$v['id']] = $v['lv'];
		}
		$pkskill= array();
		foreach($card_cfg_info['pks'] as $v){
			$pkskill[$v['id']] = $v['lv'];
		}
		
		$ghskill= array();
		if(!empty($card_cfg_info['ghs'])){
		    foreach($card_cfg_info['ghs'] as $v){
		        $ghskill[$v['id']] = $v['lv'];
		    }
		}*/
		if(!empty($cardcfg['storynameid'])){
			$Act6006Model = Master::getAct6006($this->uid);
			$Act6006Model->addCardFetter($cardcfg['storynameid']);
		}
		//没有卡则转换为碎片
		if (isset($this->info[$id])){
//			return;
			//Master::error(CARD_HAVEED);
			
			Master::add_item($this->uid, KIND_CARD_STONE, $cardcfg["item"],1,"");
			return false;
		}
		$Act750Mdoel = Master::getAct750($this->uid);
		$Act750Mdoel->setIsPop(2,$cardcfg['quality']);

		$Act762Model = Master::getAct762($this->uid);
		$Act762Model->checkFetter();

		$Act763Model = Master::getAct763($this->uid);
		$isChange = false;
		if(count($Act763Model->info['fTroops']) < 2){
			if(empty($Act763Model->info['fTroops'])){
				$Act763Model->info['fTroops'] = array();
			}
			array_push($Act763Model->info['fTroops'],$id);
			$isChange = true;
		}
		for($i = 1; $i <= 6;$i++){
			if(count($Act763Model->info['jTroops'][$i]) < 2){
				if(empty($Act763Model->info['jTroops'][$i])){
					$Act763Model->info['jTroops'][$i] = array();
				}
				if($cardcfg['hero'] == 0 || $cardcfg['hero'] == $i){
					array_push($Act763Model->info['jTroops'][$i],$id);
					$isChange = true;
				}
			}
		}
		if($isChange){
			$Act763Model->save();
		}
		
		//添加卡牌
		$_update = array(
			'cardid' => $id,
			'level' => 1,
			'star' => 0,
			'isEquip' => 0,
			'imprintLv' => 0,
			'flowerPoint' => '[]',
			'isClotheEquip' => 0,
		);
		$this->update_card($_update);
		$_addback = array();
		$_addback[$id] = $this->getCardInfo($id);
		Master::back_data($this->uid,"card","addcard",$_addback);
		
		return true;
	}	
	
	/*
	 * 更新
	 */
	public function update_card($data)
	{
		if (!isset($data['cardid'])){
			exit ('update_card_itemid_null');
		}
		
		$is_new = 0;//是否新建卡牌
		$card_old_level = 1;//卡牌旧等级
		
		if (isset($this->info[$data['cardid']])){//存在 则更新
			$info = $this->info[$data['cardid']];
			$card_old_level = $info['level'];
			//更新
			foreach ($data as $k => $v){
				//if (in_array($k,$this->updateSetKey)){
					//如果是技能列表 检查格式?
					$info[$k] = $v;
                    //'level','exp','senior','poexp'
                   /* if ($k == 'level'){
                        //记录流水 ($type,$itemid,$cha,$next)
                        Game::cmd_flow(9, $data['cardid'], $this->info[$data['cardid']][$k], $info[$k]);
                    }elseif ($k == 'senior'){
                        Game::cmd_flow(11, $data['cardid'], $this->info[$data['cardid']][$k], $info[$k]);
                    }elseif ($k == 'polevel'){
                        Game::cmd_flow(12, $data['cardid'], $this->info[$data['cardid']][$k], $info[$k]);
                    }*/
			}
			$info['_update'] = true;
			Master::back_data($this->uid,"card","updatecard",$data,true);
		}else{
			//新建
			$info = array();
			$info['uid'] = $this->uid;
			$info['cardid'] = $data['cardid'];
			$info['level'] = isset($data['level'])?$data['level']:1;
			$info['star'] = isset($data['star'])?$data['star']:0;
			$info['isEquip'] = isset($data['isEquip'])?$data['isEquip']:0;
			$info['imprintLv'] = isset($data['imprintLv'])?$data['imprintLv']:0;
			$info['flowerPoint'] = isset($data['flowerPoint'])?$data['flowerPoint']:'[]';
			$info['isClotheEquip'] = isset($data['isClotheEquip'])?$data['isClotheEquip']:0;
			//插入数据库

			$table = 'card_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set 
				`uid`='{$this->uid}',
				`cardid`='{$info['cardid']}',
				`level`='{$info['level']}',
				`star`='{$info['star']}',
				`isEquip`='{$info['isEquip']}',
				`imprintLv`='{$info['imprintLv']}',
				`flowerPoint`='{$info['flowerPoint']}',
				`isClotheEquip`='{$info['isClotheEquip']}'";
			$db = $this->_getDb();
			$db->query($sql);
			$is_new = 1;
		}
		$this->info[$data['cardid']] = $info;
		$this->_update = true;
		
		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(6);
		//如果新增卡牌
		/*if($is_new){
			//成就更新 卡牌数量
			$Act36Model = Master::getAct36($this->uid);
			$Act36Model->set(4,$TeamModel->info['cardcount']);
		}
		//如果卡牌升级
		$level_up = $info['level'] - $card_old_level;
		if ($level_up > 0){
			//日常任务 升级卡牌
			$Act35Model = Master::getAct35($this->uid);
			$Act35Model->do_act(9,$level_up);
		}
		//如果新增或者升级
        if($is_new) {
            //记录流水 ($type,$itemid,$cha,$next)
            Game::cmd_flow(8, $data['cardid'], 1, 1);
        }
		if($is_new || $level_up){
			//判断是否加入衙门战
			if ($TeamModel->info['cardcount'] >= Game::getcfg_param("gongdou_unlock_servant")
			&& $TeamModel->info['maxlv'] >= Game::getcfg_param("gongdou_unlock_level")){
				//加入衙门积分排行
				$Redis6Model = Master::getRedis6();
				$Redis6Model->join($this->uid);
				//刷新
		        $Redis6Model->back_data();
			}
		}*/
		//
		/*
        $card_cfg = Game::getcfg_info('card',$data['cardid']);
		if ($card_cfg['leaderid'] > 0){
            $TeamModel->back_card();//返回卡牌信息.
            $TeamModel->back_all_ep();//输出总属性
        }else{
            //返回更新英雄信息
            Master::add_card_rst($data['cardid']);
        }*/

	}

	/*
	 * 活动获得英雄技能升级
	 * $type  271:五虎  270:谋士
	 * */
	/*
	public function add_ghlevel($type){
	    if(empty($this->info)){
	        return 0;
	    }
	    
	    switch ($type){
	        case 270:
	            $cardlist = array(38,39,40,41);
	            $skillId = 5;
	            break;
	        case 271:
	            $cardlist = array(33,34,35,36,37);
	            $skillId = 3;
	            break;
            default:
                return 0;
                break;
	    }
	    $card = array();
	    foreach ($this->info as $cardid => $val){
	        if(in_array($cardid, $cardlist)){
	             $card[] = $cardid;
	        }
	    }
	    if(!empty($card)){
	        $level = count($card);
	        foreach ($card as $id){
	            $this->info[$id]['ghskill'][$skillId] = $level;
	            $_update = array(
	                'cardid' => $id,
	                'ghskill' => $this->info[$id]['ghskill'],
	            );
	            $this->update($_update);
	        }
	    }
	}
	*/
	/*
	 */
	public function sync()
	{
		if (!is_array($this->info)) return;
		$table = 'card_'.Common::computeTableId($this->uid);
		$db = $this->_getDb();
		foreach ($this->info as $k=>$v){
			if ($v['_update']){
				$this->info[$k]['_update'] = false;
				
				$sql=<<<SQL
update
	       `{$table}`
set
	`level`	='{$v['level']}',
	`star`	='{$v['star']}',
	`isEquip` = '{$v['isEquip']}',
	`imprintLv` = '{$v['imprintLv']}',
	`flowerPoint` = '{$v['flowerPoint']}',
	`isClotheEquip` = '{$v['isClotheEquip']}'
where
	`uid` ='{$this->uid}' 
	and
	`cardid` ='{$k}' 
limit   1;
SQL;
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error CardModel_'.$sql);
				}
				
			}
		}
		return true;
	}
}
