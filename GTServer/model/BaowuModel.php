<?php
//卡牌
require_once "AModel.php";
class BaowuModel extends AModel
{
    protected $_syn_w = true;
	public $_key = "_baowu";
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
			$table = 'baowu_'.Common::computeTableId($this->uid);
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
				$info[$v['baowuid']] = $v;
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
			Master::error('baowu_id_err_'.$id);
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

	public function findbaowustarupcfg($baowuid){
		$info =  $this->check_info($baowuid);
		$baowuCfg = Game::getcfg_info('baowu',$baowuid);
		$starupcfg = Game::getcfg('baowu_starup');
		foreach ($starupcfg as $id => $starupcfgData){
			if($starupcfgData['quality']== $baowuCfg['quality'] 
			&& $starupcfgData['star'] == $info['star'])
			{
				return $starupcfgData;
			}
		}
		return NULL;
	}
	public function upstartBaowu($baowuid){
		$baowuData =   $this->check_info($baowuid);
	
		$baowuData['star'] = $baowuData['star']+1;
		if($baowuData['star'] == 9){
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(147,1);
		}
		$this->update_baowu($baowuData );
		return $baowuData;
	}
	
	/*
	 * 获取输出值单个
	 */
	public function getBaowuInfo($baowuid,$detail= false)
	{
		$info = $this->info[$baowuid];
		if(empty($info))
		{
			return null;
		}
		$data = array(
			'id' => $info['baowuid'],
			'level' => $info['level'],
			'star' => $info['star'],
		);
		if($detail){
			$baowuData = Game::getcfg_info('baowu',$baowuid);
			if(empty($baowuData))
			{
				Game::defult_error("baowu cfg not found".$baowuid);
				return null;
			}
			$cfg = $this->findbaowustarupcfg($baowuid);
			$data['e1']= intval(floatval($baowuData['ep1']) * floatval($cfg['ep1']));
			$data['e2']= intval(floatval($baowuData['ep2']) * floatval($cfg['ep2']));
			$data['e3']= intval(floatval($baowuData['ep3']) * floatval($cfg['ep3']));
			$data['e4']= intval(floatval($baowuData['ep4']) * floatval($cfg['ep4']));
		}
		
		return $data;
	}
	
	/*
	 * 获取简略输出值单个
	 */
	public function getEasyBase_buyid($baowuid){
		$info = $this->info[$baowuid];
		$data = array(
			'id' => $info['baowuid'],
			'level' => $info['level'],
			'star' => $info['star'],
		);
		return $data;
	}
	/*
	 * 是否有某个卡牌
	 */
	public function hasBaowu($baowuid){
		if(!empty($this->info[$baowuid]))
			return true;
		return false;
	}

	public function backBaowuList(){
		Master::back_data($this->uid,"baowu","baowuList",$this->getBaowuList());
	}
	

	public function getBaowuList($detail =false)
	{
		$data = array();
		if (is_array($this->info))
		{
			foreach ($this->info as $k=>$v)
			{
				$_baowu = $this->getBaowuInfo($k,$detail);
				
				if(!empty($_baowu)){
					$data[] = $_baowu;
				}				
			}
		}
		return $data;
	}
	
	public function drawAddBaowu($baowuItemn,&$backdata){
		if($baowuItemn["kind"] == 202)
		{	
			$baowuid = $baowuItemn["itemid"];
			

			if($this->add_baowu($baowuid ))
			{
				$baowuData = array(
					$baowuid =>1
				);
				$backdata["drawids"][count($backdata["drawids"])+1] = $baowuData ;
				//$backdata["addbaowus"][$baowuid ] = $this->getBaowuInfo($baowuid );
			}else{
				$baowuData = array(
					$baowuid =>0
				);
				$backdata["drawids"][count($backdata["drawids"])+1 ]=$baowuData ;
			}
		}else{

			Master::add_item($this->uid, $baowuItemn["kind"], $baowuItemn["itemid"], $baowuItemn["num"],"");
			$backdata["drawItems"][] = array("kind"=>$baowuItemn["kind"],"id"=>$baowuItemn["itemid"],"count"=>$baowuItemn["num"]);
		}
	}
	
	/*
	 * 添加一个卡牌
	 */
	public function add_baowu($id){

		//判断这个卡牌有没有
		//获取卡牌配置
		//获取卡牌升星强化数据
		$baowucfg = Game::getcfg_info('baowu',$id);
		/*if (empty($baowucfg[$id])){
			Master::error('baowu_cfg_id_err_'.$id);
		}
		$baowu_cfg_info = $baowucfg[$id];
		//格式化
		$skills= array();
		foreach($baowu_cfg_info['skills'] as $v){
			$skills[$v['id']] = $v['lv'];
		}
		$pkskill= array();
		foreach($baowu_cfg_info['pks'] as $v){
			$pkskill[$v['id']] = $v['lv'];
		}
		
		$ghskill= array();
		if(!empty($baowu_cfg_info['ghs'])){
		    foreach($baowu_cfg_info['ghs'] as $v){
		        $ghskill[$v['id']] = $v['lv'];
		    }
		}*/
		
		//没有卡则转换为碎片
		if (isset($this->info[$id])){
//			return;
			//Master::error(BAOWU_HAVEED);
			
			Master::add_item($this->uid, 201, $baowucfg["item"],1,"");
			return false;
		}
		
		//添加卡牌
		$_update = array(
			'baowuid' => $id,
			'level' => 1,
			'star' => 0,
		);
		$this->update_baowu($_update);
		$_addback = array();
		$_addback[$id] = $this->getBaowuInfo($id);
		Master::back_data($this->uid,"baowu","addbaowu",$_addback);
		
		return true;
	}
	
	
	/*
	 * 更新
	 */
	public function update_baowu($data)
	{
		if (!isset($data['baowuid'])){
			exit ('update_baowu_itemid_null');
		}
		
		$is_new = 0;//是否新建卡牌
		$baowu_old_level = 1;//卡牌旧等级
		
		if (isset($this->info[$data['baowuid']])){//存在 则更新
			$info = $this->info[$data['baowuid']];
			$baowu_old_level = $info['level'];
			//更新
			foreach ($data as $k => $v){
				//if (in_array($k,$this->updateSetKey)){
					//如果是技能列表 检查格式?
					$info[$k] = $v;
                    //'level','exp','senior','poexp'
                   /* if ($k == 'level'){
                        //记录流水 ($type,$itemid,$cha,$next)
                        Game::cmd_flow(9, $data['baowuid'], $this->info[$data['baowuid']][$k], $info[$k]);
                    }elseif ($k == 'senior'){
                        Game::cmd_flow(11, $data['baowuid'], $this->info[$data['baowuid']][$k], $info[$k]);
                    }elseif ($k == 'polevel'){
                        Game::cmd_flow(12, $data['baowuid'], $this->info[$data['baowuid']][$k], $info[$k]);
                    }*/
			}
			$info['_update'] = true;
			Master::back_data($this->uid,"baowu","updatebaowu",$data,true);
		}else{
			//新建
			$info = array();
			$info['uid'] = $this->uid;
			$info['baowuid'] = $data['baowuid'];
			$info['level'] = isset($data['level'])?$data['level']:1;
			$info['star'] = isset($data['star'])?$data['star']:0;
			//插入数据库

			$table = 'baowu_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set 
				`uid`='{$this->uid}',
				`baowuid`='{$info['baowuid']}',
				`level`='{$info['level']}',
				`star`='{$info['star']}'";
			$db = $this->_getDb();
			$db->query($sql);
			$is_new = 1;
		}
		$this->info[$data['baowuid']] = $info;
		$this->_update = true;
		
		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(7);
		//如果新增卡牌
		/*if($is_new){
			//成就更新 卡牌数量
			$Act36Model = Master::getAct36($this->uid);
			$Act36Model->set(4,$TeamModel->info['baowucount']);
		}
		//如果卡牌升级
		$level_up = $info['level'] - $baowu_old_level;
		if ($level_up > 0){
			//日常任务 升级卡牌
			$Act35Model = Master::getAct35($this->uid);
			$Act35Model->do_act(9,$level_up);
		}
		//如果新增或者升级
        if($is_new) {
            //记录流水 ($type,$itemid,$cha,$next)
            Game::cmd_flow(8, $data['baowuid'], 1, 1);
        }
		if($is_new || $level_up){
			//判断是否加入衙门战
			if ($TeamModel->info['baowucount'] >= Game::getcfg_param("gongdou_unlock_servant")
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
        $baowu_cfg = Game::getcfg_info('baowu',$data['baowuid']);
		if ($baowu_cfg['leaderid'] > 0){
            $TeamModel->back_baowu();//返回卡牌信息.
            $TeamModel->back_all_ep();//输出总属性
        }else{
            //返回更新英雄信息
            Master::add_baowu_rst($data['baowuid']);
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
	            $baowulist = array(38,39,40,41);
	            $skillId = 5;
	            break;
	        case 271:
	            $baowulist = array(33,34,35,36,37);
	            $skillId = 3;
	            break;
            default:
                return 0;
                break;
	    }
	    $baowu = array();
	    foreach ($this->info as $baowuid => $val){
	        if(in_array($baowuid, $baowulist)){
	             $baowu[] = $baowuid;
	        }
	    }
	    if(!empty($baowu)){
	        $level = count($baowu);
	        foreach ($baowu as $id){
	            $this->info[$id]['ghskill'][$skillId] = $level;
	            $_update = array(
	                'baowuid' => $id,
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
		$table = 'baowu_'.Common::computeTableId($this->uid);
		$db = $this->_getDb();
		foreach ($this->info as $k=>$v){
			if ($v['_update']){
				$this->info[$k]['_update'] = false;
				
				$sql=<<<SQL
update
	       `{$table}`
set
	`level`	='{$v['level']}',
	`star`	='{$v['star']}'
where
	`uid` ='{$this->uid}' 
	and
	`baowuid` ='{$k}' 
limit   1;
SQL;
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error BaowuModel_'.$sql);
				}
				
			}
		}
		return true;
	}
}
