<?php
require_once "ActBaseModel.php";
/*
 * 政务处理
 */
class Act2Model extends ActBaseModel
{
	public $atype = 2;//活动编号
	public $label = "zhengwu";//倒计时标记
	public $comment = "政务处理";
	public $b_mol = "jingYing";//返回信息 所在模块
	public $b_ctrl = "exp";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//政务处理
		'num' => 0,//	政务 累计次数
		'time' => 0,//	政务	上次时间
		'type' => 0,//	当前政务种类
		'heroId' => 0,//门客ID
		'lastHeroId' => 0,//上一次随机到的门客id
		'lastType' => 0,//上一次随机到的政务id 
		'lastTaskId' => 0,//记录最后一次特殊任务的id
	);
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		//阵法
		$team = Master::get_team($this->uid);
		//冷却时间  跟智力相关
        $cd = 1800;

		//获得官阶配置
		$UserModel = Master::getUser($this->uid);
		$guan_cfg_info = Game::getcfg_info('guan',$UserModel->info['level']);
		//次数上限
        $max = $guan_cfg_info['max_zw'];
		//计算恢复时间
		$hf_num = Game::hf_num($this->info['time'],$cd,$this->info['num'],$max);
		
		//政务处理配置			
		if ($this->info['type'] == 0){
            $this->info['type'] = $this -> getSpecId();
		}
		if ($this->info['type'] == 0){
			$this->info['type'] = 1;
		}
		$zw_cfg_info = Game::getcfg_info('zw',$this->info['type']);
		$item_id = $zw_cfg_info['item'];//道具ID
		
		//道具类型 数量
		$item_count = 1;
		switch ($item_id){
			case 2://银两 跟智力相关
				$item_count = $this->_get_zwcount($team['allep'][2]); 
				break;
			case 3://粮草 跟政治相关
				$item_count = $this->_get_zwcount($team['allep'][3]);
				break;
			case 4://士兵 跟魅力相关
				$item_count = $this->_get_zwcount($team['allep'][4]);
				break;
			default:
				//其他道具都是1个
				break;
		}
		
		//保存数据
		$this->info['time'] = $hf_num['stime'];
		$this->info['num'] = $hf_num['num'];
		
		//输出数据
		$this->outf = array(
			'type' => $this->info['type'],//事件类型ID
			'count' => $item_count,//道具数量
			'itemid' => $item_id,//道具ID
			'heroId' => $this->info['heroId'],//门客ID
			'cd' => array(
				'next' => $hf_num['next'],//下次绝对时间
				'num' => $hf_num['num'],//剩余次数
				'label' => $this->label,
			),
		);

		$this->_saveData();
	}

	private function _saveData(){
        $ActModel = Master::getAct($this->uid,$this->atype);
        $ActModel->setAct($this->atype,array(
            'id'=>$this->hid,
            'data' => $this->info,
        ));
        $this->_update = true;
    }

	/*
	 * 获取道具数量
	 * 属性值
	 */
	private function _get_zwcount($ep){
		//5000 +属性*系数

		return floor(1000 +$ep);
	}

	/*
	 * 减去政务次数
	 */
	private function getSpecId(){
	    $act39Model = Master::getAct39($this->uid);
		$zw_cfg = Game::getCfg('zw');
		$id = $act39Model->info['id'];
		$id = empty($id)?1:$id;
		$lastId = $this->info['lastTaskId'];
		foreach($zw_cfg as $k => $v){
			if ($v['cond'] == 10){
				$ss = explode('|', $v['task'] );
				$taskId = intval($ss[0]);
				$heroId = intval($ss[1]);
				if ($taskId <= $id && $lastId < $taskId){
					$this->info['lastTaskId'] = $taskId;
					$this->info['type'] = $k;
					$this->info['heroId'] = $heroId;
					return $k;
				}				
			}
		}
		return 0;
	}
	
	/*
	 * 减去政务次数
	 */
	public function sun_time($num = 1){
		if ($this->info['num'] < $num){
			Master::error(GOVERNMENT_NUM_ENPTY);
		}
		//减去次数
		$this->info['num'] -= 1;
		if($this->info['num'] < 0){
		    Master::error(ACT_2_BUZU);
		}

		//记录上一次政务
		$this->info['lastType'] = $this->info['type'];
		$this->info['lastHeroId'] = $this->info['heroId'];

		//随机出下一次政务事件
		$zw_cfg = Game::getCfg('zw');
		$id = $this->getSpecId();		
		$this->info['type'] = $id == 0?Game::get_rand_key1($zw_cfg,'prob_1000'):$id;

		//增加事件判定
		if ($id == 0){
			$zw_cfg1 = $this->info['type'] != 0?Game::getcfg_info('zw', $this->info['type']):null;
			if (!empty($zw_cfg1) && rand(1,10000) < $zw_cfg1['prob2_1000']){
//            if (!empty($zw_cfg1) && 0 < $zw_cfg1['prob2_1000']){
                $type = $zw_cfg1['cond'];
                if ($type != 0){
                    $HeroModel = Master::getHero($this->uid);
                    $this->info['heroId'] = $HeroModel->get_one_hero_type($type);
                }
                else{
                    $this->info['heroId'] = 0;
                }
            }
            else{
                $this->info['heroId'] = 0;
            }
		}

		//保存
		$this->save();
	}

	/*
	 * 可以选择选项么
	 */
	public function canSelectStory($groupId){
		$lastType = $this->info['lastType'];
        $heroId = $this->info['lastHeroId'];
		//判断门客是否存在
        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroId);
        //是否是调用组
        if ($lastType == 0)return false;
        $zw_cfg = Game::getcfg_info('zw', $lastType);
        $events = Game::getcfg('jyevent_dialogue');
		if (!empty($zw_cfg)){
			foreach($events as $item){
				if ($item['type'] == $lastType + 100 && $item['award'] == $groupId){
					return true;
				}
			}
		}
		else {
            Master::error(STORY_DATA_NOT_FIND.$lastType);
        }
		return false;
	}

	/*
	 * 清理最后数据
	 */
	public function clearLast(){
		$this->info['lastType'] = 0;
		$this->info['lastHeroId'] = 0;
		$this->save();
	}
	
	/*
	 * 加上政务次数
	 */
	public function add_time($addnum){
		//当前次数
//		$old_num = $this->info['num'];
		
		//获得官阶配置
//		$UserModel = Master::getUser($this->uid);
//		$guan_cfg_info = Game::getcfg_info('guan',$UserModel->info['level']);
//		//次数上限
//		$max = $guan_cfg_info['max_jy'];
		
		//加上次数
		$this->info['num'] += $addnum;
//		if ($this->info['num'] >= $max){
//			$this->info['num'] = $max;
//			$this->info['time'] = 0;
//		}
//
//		$d_num = $this->info['num'] - $old_num;
//		if ($d_num <= 0){
//			Master::error(GOVERNMENT_NUM_FULL);
//		}else{
//			$this->save();
//
//			//返回政务处理次数增加窗口
//			Master::back_basewin_item(14,$d_num,2);
//
//			return $d_num;
//		}

		$this->save();
		

		//返回政务处理次数增加窗口
		Master::back_basewin_item(14,$addnum,2);
	}
	
}
