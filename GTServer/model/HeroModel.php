<?php
//门客
require_once "AModel.php";
class HeroModel extends AModel
{
    protected $_syn_w = true;
	public $_key = "_hero";
	protected  $updateSetKey = array(
		'exp','senior','epskill','pkskill','ghskill','level','star',
	);
	protected $updateAddKey =  array(
		'zzexp','pkexp','love',
		'e1','e2','e3','e4','num',
	);
	
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());

		if($this->info == false){
			$table = 'hero_'.Common::computeTableId($this->uid);
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
				$v['epskill'] = json_decode($v['epskill'],1); 
				$v['pkskill'] = json_decode($v['pkskill'],1); 
				$v['ghskill'] = json_decode($v['ghskill'],1);
				$info[$v['heroid']] = $v;
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
			Master::error('hero_id_err_'.$id);
		}else{
			if($is_click === false){
				$Act129Model = Master::getAct129($this->uid);
				$isBanish = $Act129Model->isBanish($id);
				if($isBanish){
					Master::error(BANISH_009);
				}
			}
			return $this->info[$id];
		}
	}
	
	/*
	 * 获取玩家所有门客id  存储格式:  array( id , id , ...)
	 */
	public function get_all_heros(){
		//获取所有门客id
		$all_heros = array_keys($this->info); 
		if(empty($all_heros)){
			Master::error('get_all_heros_err_');
		}
		return $all_heros;
	}
	
	/*
	 * 随机获取一只玩家拥有的门客id
	 */
	public function get_one_hero(){
		//数组随机id
		$all_heros = self::get_all_heros();
		//扣除发配的
		$Act129Model = Master::getAct129($this->uid);
		if(!empty($Act129Model->info['list'])){
			$all_heros = array_diff($all_heros,array_keys($Act129Model->info['list']));
		}
		$key_id = array_rand($all_heros,1);
		if(empty($all_heros[$key_id])){
			Master::error('get_one_hero_err');
		}
		return $all_heros[$key_id];
	}

	/*
	 * 随机获取一只玩家拥有某个特性的门客id
	 */
	public function get_one_hero_type($type){
		//数组随机id
		$all_heros = self::get_all_heros();
		//扣除发配的
		$Act129Model = Master::getAct129($this->uid);
		if(!empty($Act129Model->info['list'])){
			$all_heros = array_diff($all_heros,array_keys($Act129Model->info['list']));
		}
		$heorIds = array();
        if(!empty($all_heros)){
            foreach ($all_heros as $id){
                $heroData = Game::getcfg_info("hero", $id);
                $spec = $heroData["spec"];
                if ($spec[0] == $type || $spec[0] == 5 || $spec[0] == 6 || (count($spec) > 1 && $spec[1] == $type)){
                	$heorIds[$id] = $id;
                }
            }            
            return array_rand($heorIds);
        }
        return 0;
	}

	/*
	 * 随机获取一只某个特性的门客id
	 */
	public function get_hero_type_id($type){
		$heroData = Game::getCfg('hero');
		$heorIds = array();
        if(!empty($heroData)){
            foreach ($heroData as $id => $heroInfo){
                $spec = $heroInfo["spec"];
                if ($spec[0] == $type || $spec[0] == 5 || $spec[0] == 6 || (count($spec) > 1 && $spec[1] == $type)){
                	$heorIds[$id] = $id;
                }
            }            
            return array_rand($heorIds);
        }
        return 0;
	}

	/*
	 * 随机获取一只某个性格的门客id
	 */
	public function get_hero_char_id($type){
		$heroData = Game::getCfg('hero');
		$heorIds = array();
        if(!empty($heroData)){
            foreach ($heroData as $id => $heroInfo){
                $dis = $heroInfo["disposition"];
                if ($dis == $type){
                	$heorIds[$id] = $id;
                }
            }            
            return array_rand($heorIds);
        }
        return 0;
	}

	/*
	 * 获取输出值单个
	 */
	public function getBase_buyid($heroid)
	{
		$info = $this->info[$heroid];
		$epskill = array();
		foreach ($info['epskill'] as $k => $v){
			$epskill[] = array(
				'id' => $k,
				'level' => $v
			);
		}
		$pkskill = array();
		foreach ($info['pkskill'] as $k => $v){
			$pkskill[] = array(
				'id' => $k,
				'level' => $v
			);
		}
		$ghskill = array();
		if(!empty($info['ghskill'])){
    		foreach ($info['ghskill'] as $k => $v){
    		    $ghskill[] = array(
    		        'id' => $k,
    		        'level' => $v
    		    );
    		}
		}
		if ($info['star'] <= 0){
			$hero_info = Game::getcfg_info('hero',$heroid);
			if($hero_info){
				$info['star'] = $hero_info['initStar'];
			}
		}
		$data = array(
			'id' => $info['heroid'],
			'level' => $info['level'],
			'senior' => $info['senior'],
			'exp' => $info['exp'],
			'zzexp' => $info['zzexp'],
			'pkexp' => $info['pkexp'],
			'epskill' => $epskill,
			'pkskill' => $pkskill,
		    'ghskill' => $ghskill,
			'hep' => array(
				'e1' => $info['e1'],
				'e2' => $info['e2'],
				'e3' => $info['e3'],
				'e4' => $info['e4'],
			),
			'love' => $info['love'],
			'star' => $info['star'],
			'num' => $info['num'],
		);
		return $data;
	}
	
	/*
	 * 获取简略输出值单个
	 */
	public function getEasyBase_buyid($heroid){
		$info = $this->info[$heroid];
		$data = array(
			'id' => $info['heroid'],
			//'level' => $info['level'],
			'senior' => $info['senior'],
		);
		return $data;
	}
	
	/*
	 * 添加一个门客
	 */
	public function add_hero($id){

		//判断这个英雄有没有
		//获取英雄配置
		//获取资质技能配置
		//获取PK技能配置
		$hero_cfg = Game::getcfg('hero');
		if (empty($hero_cfg[$id])){
			Master::error('hero_cfg_id_err_'.$id);
		}
		$hero_cfg_info = $hero_cfg[$id];
		//格式化
		$skills= array();
		foreach($hero_cfg_info['skills'] as $v){
			$skills[$v['id']] = $v['lv'];
		}
		$pkskill= array();
		foreach($hero_cfg_info['pks'] as $v){
			$pkskill[$v['id']] = $v['lv'];
		}
		
		$ghskill= array();
		if(!empty($hero_cfg_info['ghs'])){
		    foreach($hero_cfg_info['ghs'] as $v){
		        $ghskill[$v['id']] = $v['lv'];
		    }
		}
		
		
		if (isset($this->info[$id])){
//			return;
			Master::error(HERO_HAVEED);
		}
		//添加英雄
		$_update = array(
			'heroid' => $id,
			'level' => 1,
			'exp' => 0,
			'senior' => 1,
			'epskill' => $skills,
			'pkskill' => $pkskill,
		    'ghskill' => $ghskill,
			'zzexp' => 0,
			'pkexp' => 0,
			'e1' => 0,//丹药属性加成
			'e2' => 0,
			'e3' => 0,
			'e4' => 0,
			'love' => 1,
			'num' => 0,
			'star' => $hero_cfg_info['initStar'],
		);

		$this->update($_update);
		if($id != 1){
			$Act750Mdoel = Master::getAct750($this->uid);
			$Act750Mdoel->setIsPop(3,1);
		}
	}
	
	
	/*
	 * 更新
	 */
	public function update($data)
	{
		if (!isset($data['heroid'])){
			exit ('update_hero_itemid_null');
		}
		
		$is_new = 0;//是否新建门客
		$hero_old_level = 1;//门客旧等级

		$hero_cfg = Game::getcfg_info('hero',$data['heroid']);
		
		if (isset($this->info[$data['heroid']])){//存在 则更新
			$info = $this->info[$data['heroid']];
			$hero_old_level = $info['level'];
			//更新
			foreach ($data as $k => $v){
				if (in_array($k,$this->updateSetKey)){
					//如果是技能列表 检查格式?
					$info[$k] = $v;
                    //'level','exp','senior','poexp'
                    if ($k == 'level'){
                        //记录流水 ($type,$itemid,$cha,$next)
                        Game::cmd_flow(9, $data['heroid'], $this->info[$data['heroid']][$k], $info[$k]);
                    }elseif ($k == 'senior'){
                        Game::cmd_flow(11, $data['heroid'], $this->info[$data['heroid']][$k], $info[$k]);
                    }elseif ($k == 'polevel'){
                        Game::cmd_flow(12, $data['heroid'], $this->info[$data['heroid']][$k], $info[$k]);
                    }
				} elseif (in_array($k,$this->updateAddKey)){
					$info[$k] += $v;
                    if ($k == 'zzexp'){
                        Game::cmd_flow(25, $data['heroid'], $v, $info[$k]);
                        //御花园
                        // $Act6190Model = Master::getAct6190($this->uid);
                        // $Act6190Model->addType(14, $v);
                    }elseif ($k == 'pkexp'){
                        Game::cmd_flow(26, $data['heroid'], $v, $info[$k]);
                    }
				}else{
					//exit('update_hero_type_err:'.$k);
				}
			}
			$info['_update'] = true;
		}else{
			//新建
			$info = array();
			$info['uid'] = $this->uid;
			$info['heroid'] = $data['heroid'];
			$info['level'] = isset($data['level'])?$data['level']:1;
			$info['exp'] = 0;
			$info['senior'] = isset($data['senior'])?$data['senior']:1;
			$info['zzexp'] = isset($data['zzexp'])?$data['zzexp']:0;
			$info['pkexp'] = isset($data['pkexp'])?$data['pkexp']:0;
			$info['epskill'] = isset($data['epskill'])?$data['epskill']:array();
			$info['pkskill'] = isset($data['pkskill'])?$data['pkskill']:array();
			$info['ghskill'] = isset($data['ghskill'])?$data['ghskill']:array();
			$info['e1'] = isset($data['e1'])?$data['e1']:0;
			$info['e2'] = isset($data['e2'])?$data['e2']:0;
			$info['e3'] = isset($data['e3'])?$data['e3']:0;
			$info['e4'] = isset($data['e4'])?$data['e4']:0;
			$info['love'] = isset($data['love'])?$data['love']:0;
			$info['num'] = isset($data['num'])?$data['num']:0;
			$initStar = 0;
			if (!empty($hero_cfg)){
				$initStar = $heroCfg['initStar'];
			}
			$info['star'] = isset($data['star'])?$data['star']:$initStar;
			//插入数据库
			$epskill_json = json_encode($info['epskill']);
			$pkskill_json = json_encode($info['pkskill']);
			$ghskill_json = json_encode($info['ghskill']);
			$table = 'hero_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set 
				`uid`='{$this->uid}',
				`heroid`='{$info['heroid']}',
				`level`='{$info['level']}',
				`exp`='{$info['exp']}',
				`senior`='{$info['senior']}',
				`zzexp`='{$info['zzexp']}',
				`pkexp`='{$info['pkexp']}',
				`epskill`='{$epskill_json}',
				`pkskill`='{$pkskill_json}',
				`ghskill`='{$ghskill_json}',
				`e1`='{$info['e1']}',
				`e2`='{$info['e2']}',
				`e3`='{$info['e3']}',
				`e4`='{$info['e4']}',
				`love`='{$info['love']}',
				`star`='{$info['star']}',
				`num`='{$info['num']}'";
			$db = $this->_getDb();
			$db->query($sql);
			$is_new = 1;
		}
		$this->info[$data['heroid']] = $info;
		$this->_update = true;

		//更新阵法
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(1);
		//如果新增门客
		if($is_new){
			$Act2003Model = Master::getAct2003($this->uid);
			$Act2003Model->checkHeroJibanUnlock($data['heroid']);
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(125,$TeamModel->info['herocount']);
		}
		//如果门客升级
		$level_up = $info['level'] - $hero_old_level;
		if ($level_up > 0){
			//日常任务 升级门客
			// $Act35Model = Master::getAct35($this->uid);
			// $Act35Model->do_act(5,$level_up);
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(155,$level_up);
		}
		//如果新增或者升级
        if($is_new) {
            //记录流水 ($type,$itemid,$cha,$next)
            Game::cmd_flow(8, $data['heroid'], 1, 1);
        }
		if($is_new || $level_up){
			//判断是否加入衙门战
			if ($TeamModel->info['herocount'] >= Game::getcfg_param("gongdou_unlock_servant")
			&& $TeamModel->info['maxlv'] >= Game::getcfg_param("gongdou_unlock_level")){
				//加入衙门积分排行
				$Redis6Model = Master::getRedis6();
				$Redis6Model->join($this->uid);
				//刷新
		        $Redis6Model->back_data();
			}
		}
		if ($hero_cfg['leaderid'] > 0){
            $TeamModel->back_hero();//返回门客信息.
            $TeamModel->back_all_ep();//输出总属性
        }else{
            //返回更新英雄信息
            Master::add_hero_rst($data['heroid']);
        }

	}
	/*
	 * 活动获得英雄技能升级
	 * $type  271:五虎  270:谋士
	 * */
	public function add_ghlevel($type){
	    if(empty($this->info)){
	        return 0;
	    }
	    
	    switch ($type){
	        case 270:
	            $herolist = array(38,39,40,41);
	            $skillId = 5;
	            break;
	        case 271:
	            $herolist = array(33,34,35,36,37);
	            $skillId = 3;
	            break;
            default:
                return 0;
                break;
	    }
	    $hero = array();
	    foreach ($this->info as $heroid => $val){
	        if(in_array($heroid, $herolist)){
	             $hero[] = $heroid;
	        }
	    }
	    if(!empty($hero)){
	        $level = count($hero);
	        foreach ($hero as $id){
	            $this->info[$id]['ghskill'][$skillId] = $level;
	            $_update = array(
	                'heroid' => $id,
	                'ghskill' => $this->info[$id]['ghskill'],
	            );
	            $this->update($_update);
	        }
	    }
	}
	
	/*
	 */
	public function sync()
	{
		if (!is_array($this->info)) return;
		$table = 'hero_'.Common::computeTableId($this->uid);
		$db = $this->_getDb();
		foreach ($this->info as $k=>$v){
			if ($v['_update']){
				$this->info[$k]['_update'] = false;
				
				$epskill_json = json_encode($v['epskill']);
				$pkskill_json = json_encode($v['pkskill']);
				$ghskill_json = json_encode($v['ghskill']);
				$sql=<<<SQL
update
	       `{$table}`
set
	`level`	='{$v['level']}',
	`exp`	='{$v['exp']}',
	`senior`	='{$v['senior']}',
	`epskill`	='{$epskill_json}',
	`pkskill`	='{$pkskill_json}',
	`ghskill`	='{$ghskill_json}',
	`e1`	='{$v['e1']}',
	`e2`	='{$v['e2']}',
	`e3`	='{$v['e3']}',
	`e4`	='{$v['e4']}',
	`zzexp`	='{$v['zzexp']}',
	`pkexp`	='{$v['pkexp']}',
	`love`	='{$v['love']}',
	`num`	='{$v['num']}',
	`star`	='{$v['star']}'
where
	`uid` ='{$this->uid}' 
	and
	`heroid` ='{$k}' 
limit   1;
SQL;
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error HeroModel_'.$sql);
				}
				
			}
		}
		return true;
	}
}
