<?php
//子嗣
require_once "AModel.php";
class SonModel extends AModel
{
	public $_key = "_son";
	protected  $updateSetKey = array(
		'name','sex','state','power','ptime','level',
		'e1','e2','e3','e4','honor',
		'tquid','tqitem','tqtime','spuid','spsonuid','sptime',
	);
	protected $updateAddKey =  array(
		'exp',
	);
	
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());

		if($this->info == false){
			$table = 'son_'.Common::computeTableId($this->uid);
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
				$info[$v['sonuid']] = $v;
			}
			$this->info = $info;
			$cache->set($this->getKey(),$this->info);
		}
		
		//获取子嗣活力上限
		//setUid
		$UserModel = Master::getUser($this->uid);
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		$this->pow_max = $vip_cfg_info['sonpow'];
	}
	
	/*
	 * 检查子嗣是否合法 并返回
	 */
	public function check_info($id,$is_click = false){
		if (empty($this->info[$id])){
			if ($is_click){
				return false;
			}
			Master::error('son_id_err_'.$id);
		}else{
			return $this->info[$id];
		}
	}
	
	/*
	 * 检查提亲是否有合适的子嗣
	 * 性别 , 科举名次
	 */
	public function check_mson($sex){
		
		if(empty($this->info)){
			return false;
		}
		foreach ($this->info as $k => $v)
		{
			if($v['sex'] != $sex && $v['state'] == 4){
				return true;
			}
		}
		return false;
	}
	
	
	/*
	 * 获取输出值xx
	 */
	public function getBase()
	{
		$data = array();
		if (is_array($this->info)){
		foreach ($this->info as $k=>$v)
		{
			$data[] = $this->getBase_buyid($k);
		}}
		Master::back_data($this->uid,"son","sonList",$data);
		
		//子嗣席位
		$Act12Model = Master::getAct12($this->uid);
		$Act12Model->back_data();
	}
	
	/*
	 * 获取子嗣概况
	 */
	public function getSonMsg_buyid($sonuid){
		$info = $this->info[$sonuid];
		
		$data = array(
			'id' => $info['sonuid'],//子嗣流水ID
			'name' => $info['name'],//名字
			'sex' => $info['sex'],//性别
			'mom' => $info['mom'],//老妈ID
			'state' => $info['state'],//状态ID:0未取名,1婴儿,2儿童,3等待科举,4等待结婚,5提亲中,6被拒绝,7提亲超时,8等待确认婚礼,9结婚后
			'ep' => array(
				'e1' => $info['e1'],
				'e2' => $info['e2'],
				'e3' => $info['e3'],
				'e4' => $info['e4'],
			),
			'talent' => $info['talent'],//天赋品级
			'cpoto' => $info['cpoto'],//儿童形象
			'level' => $info['level'],//等级
			'honor' => $info['honor'],//科举名次
			'spuid' => $info['spuid'],//配偶UID
			'spsonuid' => $info['spsonuid'],//配偶流水ID
		);
		
		return $data;
	}
	
	
	/*
	 * 亲家加成子嗣数量 => 获取子嗣势力排行
	 */
	public function get_qjadd()
	{
		static $add_qj = array(); //静态生效活动列表
		
		if(empty($add_qj[$this->uid])){
			if(!empty($this->info)){
				foreach ($this->info as $k => $v)
				{
					//过滤没有结婚的
					if($v['state'] != 9){
						continue;
					}
					//我方
					$mybase = $this->getSonMsg_buyid($k);
					$add_qj[$this->uid][$this->uid][$v['spuid']][$k] = array_sum($mybase['ep']);
					//对方
					$fSonModel = Master::getSon($v['spuid']);
					$fbase = $fSonModel->getSonMsg_buyid($v['spsonuid']);
					$add_qj[$this->uid][$v['spuid']][$v['spsonuid']] = array_sum($fbase['ep']);
				}
			}
		}
		return $add_qj[$this->uid];
	}
	
	/*
	 * 判断是否有拜访加成
	 */
	public function check_qjadd($fuid,$fsonid,$qjuid = 0)
	{
		$is_add = 0;
		
		$qjadd = self::get_qjadd();
		if(empty($qjadd[$fuid])){
			return $is_add;
		}
		arsort($qjadd[$fuid]);
		$qjnewadd = $qjadd[$fuid];
		if($this->uid == $fuid){
			if(empty($qjadd[$fuid][$qjuid])){
				return $is_add;
			}
			arsort($qjadd[$fuid][$qjuid]);
			$qjnewadd = $qjadd[$fuid][$qjuid];
		}
		
		$fUserModel = Master::getUser($fuid);
		$vip_cfg = Game::getcfg_info('vip',$fUserModel->info['vip']);
		//vip加成   默认10人(亲家1对1)
		$add_qjvip = empty($vip_cfg['qingjia'])?10:10+$vip_cfg['qingjia'];
		$new_qjadd = array_slice($qjnewadd,0,$add_qjvip,true);
		if(!empty($new_qjadd[$fsonid])){
			$is_add = 1;
		}
		
		return $is_add;
	}

	/**
	 * 获取已结婚列表信息
	 * @return array
	 */
	public function getJiehunList(){
		if(empty($this->info)){
			return array();
		}
		foreach ($this->info as $sid => $val){
			if($val['state'] == 9){
				$list[] = $sid;
			}
		}
		return empty($list) ? array() : $list;
	}
	
	
	
	
	/*
	 * 获取输出值单个
	 */
	public function getBase_buyid($sonuid)
	{
		$info = $this->info[$sonuid];
		
		//活力恢复时间计算
		$cd = 10800;
		$p_hf = Game::hf_num($info['ptime'],$cd,$info['power'],$this->pow_max);
		//历练状态
        $Act6133Model = Master::getAct6133($this->uid);
        $isPlay=$Act6133Model->isPlay($sonuid);
		//保存数据更新
		$this->info[$sonuid]['ptime'] = $p_hf['stime'];
		$this->info[$sonuid]['power'] = $p_hf['num'];
		
		//如果提亲中 
		if(($info['state'] == 5 || $info['state'] == 10) && $info['tqtime'] > 0 )
		{
			if (Game::is_over($info['tqtime'])){
				$info['state'] = 7;//超时
				$info['tqtime'] = 0;
			}
		}else{
			$info['tqtime'] = 0;
		}
		$info['name'] = Game::filter_char($info['name']);
		$data = array(
			'id' => $info['sonuid'],//子嗣流水ID
			'name' => $info['name'],//名字
			'sex' => $info['sex'],//性别
			'mom' => $info['mom'],//老妈ID
			'state' => $info['state'],//状态ID:未取名,幼儿,儿童,等待科举,等待结婚,结婚等待通知,婚后
			'ep' => array(
				'e1' => $info['e1'],
				'e2' => $info['e2'],
				'e3' => $info['e3'],
				'e4' => $info['e4'],
			),
			'talent' => $info['talent'],//天赋品级
			'cpoto' => $info['cpoto'],//儿童形象
			'level' => $info['level'],//等级
			'exp' => $info['exp'],//等级经验
			'power' => $p_hf['num'],//活力值
			'cd' => array(
				'next' => $p_hf['next'],//下次恢复时间
				'label' => "sonpow",//倒计时标记
			),
			'honor' => $info['honor'],//科举名次
			'tquid' => $info['tquid'],//提亲UID (等于0 表示全服提亲)
			'tqitem' => $info['tqitem'],//提亲道具(可能退还)
			'tqcd' => array(
				'next' => $info['tqtime'],//提亲超时
				'label' => 'tiqintime',//提亲超时
			),
			//'tqnext' => 
			//'spuid' => $info['spuid'],//配偶UID
			//'spsonuid' => $info['spsonuid'],//配偶流水ID
			'sptime' => $info['sptime'],//结婚时间
			'spouse' => array(),//结婚对象信息
			'myqjadd' => 0,  //我方亲家加成
			'fqjadd'  => 0,  //对方亲家加成
            'liLianStatus'  => $isPlay== true ? 1 : 0, //历练状态 0:历练结束 1:历练中或历练结束未领取物品

		);
		
		if (in_array($info['state'],array(5,6,7)) && $info['tquid'] > 0){
			//如果提亲中 附加提亲对象数据
			$data['spouse'] = Master::getMarryDate_onlyuser($info['tquid']);
		}elseif(in_array($info['state'],array(8,9))){
			//如果这个子嗣已婚 则附加结婚对象结构体
			$data['spouse'] = Master::getMarryDate($info['spuid'],$info['spsonuid']);
			
			$Act134Model = Master::getAct134($this->uid);
			$myqjlove = $Act134Model->get_love($info['spuid'],0);
			$fqjlove = $Act134Model->get_love($info['spuid'],1);
			if( self::check_qjadd($this->uid,$info['sonuid'],$info['spuid'])){
				$data['myqjadd'] = array_sum(Game::qjepadd($info['honor'],$myqjlove));
			}
			if( self::check_qjadd($info['spuid'],$info['spsonuid'])){
				$data['fqjadd'] = array_sum(Game::qjepadd($info['honor'],$fqjlove));
			}
			
		}
		
		return $data;
	}
	
	/*
	 * 添加孩子 母亲ID
	 */
	public function addSon($heroId){
		//母亲亲密度
		$HeroModel = Master::getHero($this->uid);
		$hero_info = $HeroModel->check_info($heroId);
		$Act6001Model = Master::getAct6001($this->uid);
		$jbNum = $Act6001Model->getHeroJB($heroId);
		$love = $jbNum;
		
		//亲密度随机偏移
		//按照亲密度 生成孩子品质 随机算法待优化
		$cfg_son_type = Game::getcfg('son_type');
		$talent = 1;//笨拙
		if(!empty($cfg_son_type)){
			krsort($cfg_son_type);
			foreach($cfg_son_type as $sonk => $sonv){
				if($love >= $sonv['love']){
					$rid = rand(1,100);
					foreach($sonv['prob_100'] as $k => $v){
						if($rid > $v){
							$rid -= $v;
							continue;
						}
						$talent = $k;
						break;
					}
					break;
				}
			}
		}
		//构造添加子嗣数据
		$s_update = array(
			'mom' => $heroId,
			'talent' => $talent,
			'honor' => 1,//默认童生
			'e1' => rand(5,30),
			'e2' => rand(5,30),
			'e3' => rand(5,30),
			'e4' => rand(5,30),
		);
		$sonuid = $this->update($s_update);

		// //成就更新
		// $Act36Model = Master::getAct36($this->uid);
		// $Act36Model->set(17,count($this->info));

		//更新当前母亲的子嗣数量
		$w_update = array(
		    'heroid' => $heroId,
		    'num' => 1
		);
		$HeroModel->update($w_update);
		$Act750Mdoel = Master::getAct750($this->uid);
		$Act750Mdoel->setIsPop(4,1);
		return $sonuid;
	}


	/*
	 * 更新
	 */
	public function update($data)
	{	
		$is_new_son = 0;
		//更新子嗣
		if (isset($data['sonuid'])){
			$info = $this->info[$data['sonuid']];
			if (empty($info)){
				exit('update_son_err');
			}

			//更新字段
			foreach ($data as $k => $v){
				if (in_array($k,$this->updateSetKey)){
					$info[$k] = $v;
				} elseif (in_array($k,$this->updateAddKey)){
					$info[$k] += $v;
				}else{
					//exit('update_son_type_err:'.$k);
				}
				//如果更新了经验值  判断升级
				if ($k == 'exp'){
					//子嗣等级配置
					$son_exp_cfg = Game::getcfg('son_exp');
					//子嗣等级上限配置
					$son_yn_cfg_info = Game::getcfg_info('son_yn',$info['talent']);
					$lv = $info['level'];
					while(1){
						if (in_array($info['state'],array(1,2))//婴儿,儿童(还没到等待科举)
						&& $info['exp'] >= $son_exp_cfg[$info['level']]['exp']){//经验值满
							//扣除经验
							$info['exp'] -= $son_exp_cfg[$info['level']]['exp'];
							//等级增加
							$info['level'] ++;
							//加上4项成长属性
							//红颜类 取的老妈亲密度
							$HeroModel = Master::getHero($this->uid);
							$Act6001Model = Master::getAct6001($this->uid);
							$jbNum = $Act6001Model->getHeroJB($HeroModel->info[$info['mom']]);
							// $love = $HeroModel->info[$info['mom']]['love'];
							$love = $jbNum;

							$up_e1 = Game::sonepup($info['level']-1,$love);
							$up_e2 = Game::sonepup($info['level']-1,$love);
							$up_e3 = Game::sonepup($info['level']-1,$love);
							$up_e4 = Game::sonepup($info['level']-1,$love);

							$info['e1'] += $up_e1;
							$info['e2'] += $up_e2;
							$info['e3'] += $up_e3;
							$info['e4'] += $up_e4;


							//************************ 升级属性增加弹窗
							$upeps = array();
							$upeps['sid'] = $data['sonuid'];
							$upeps['ep'] = array(
								'e1' => $up_e1,
								'e2' => $up_e2,
								'e3' => $up_e3,
								'e4' => $up_e4,
							);
							Master::$bak_data['a']['son']['win']['upsonep'][] = $upeps;

							$HuodongModel = Master::getHuodong($this->uid);
							$HuodongModel->chongbang_huodong('huodong311',$data['sonuid'],array_sum($upeps['ep']));

							if ($info['level'] >= 5 && $info['state'] <= 1){
								//婴儿 -> 幼儿
								$info['state'] = 2;
							}else{
								//幼儿 -> 考生
								if ($info['level'] >= $son_yn_cfg_info['level_max']){
									$info['state'] = 3;
								}
							}
						}else{
							break;
						}
					}
                    if ($info['level'] - $lv > 0){
                        //御花园
                        // $Act6190Model = Master::getAct6190($this->uid);
                        // $Act6190Model->addType(7, $info['level'] - $lv);
                    }
				}elseif ($k == 'power'){
					//如果更新了点数 则写入恢复时间
					//略 获取输出时候 已经设置
				}
			}
			$sonuid = $data['sonuid'];
			$info['_update'] = true;
		} else {//插入新子嗣
			if (empty($data['mom'])){//母亲
				exit('new_son_mom_null');
			}
			$is_new_son = 1;
			$info = array();
			$info['sex'] = isset($data['sex'])?$data['sex']:rand(1,2);
			$info['name'] = isset($data['name'])?$data['name']:'';
			$info['mom'] = $data['mom'];
			$info['talent'] = isset($data['talent'])?$data['talent']:1;
			$info['cpoto'] = isset($data['cpoto'])?$data['cpoto']:rand(1,5);
			$info['state'] = 0;//插入 未取名
			$info['power'] = 2;//
			$info['ptime'] = 0;
			$info['level'] = 1;
			$info['exp'] = 0;
			$info['e1'] = isset($data['e1'])?$data['e1']:0;
			$info['e2'] = isset($data['e2'])?$data['e2']:0;
			$info['e3'] = isset($data['e3'])?$data['e3']:0;
			$info['e4'] = isset($data['e4'])?$data['e4']:0;
			$info['honor'] = $data['honor'];
			
			$info['tquid'] = 0;
			$info['tqitem'] = 0;
			$info['spuid'] = 0;
			$info['spsonuid'] = 0;
			
			$info['sptime'] = 0;
			//插入数据库
			$table = 'son_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set 
				`uid`='{$this->uid}',
				`sex`='{$info['sex']}',
				`name`='{$info['name']}',
				`mom`='{$info['mom']}',
				`talent`='{$info['talent']}',
				`cpoto`='{$info['cpoto']}',
				`state`='{$info['state']}',
				`power`='{$info['power']}',
				`ptime`='{$info['ptime']}',
				`level`='{$info['level']}',
				`exp`='{$info['exp']}',
				`e1`='{$info['e1']}',
				`e2`='{$info['e2']}',
				`e3`='{$info['e3']}',
				`e4`='{$info['e4']}',
				`tquid`='{$info['tquid']}',
				`tqitem`='{$info['tqitem']}',
				`spuid`='{$info['spuid']}',
				`spsonuid`='{$info['spsonuid']}',
				`sptime`='{$info['sptime']}'";
			$db = $this->_getDb();
			
			//获取插入ID
			$db->query($sql);
			$sonuid = $db->insertId();
			$info['sonuid'] = $sonuid;

			$HuodongModel = Master::getHuodong($this->uid);
			$son_ep = array($info['e1'],$info['e2'],$info['e3'],$info['e4']);
			$HuodongModel->chongbang_huodong('huodong311',$sonuid,array_sum($son_ep));
			//新增子嗣流水
			Game::cmd_flow(51, $sonuid, $info['mom'], $info['mom']);
		}
		$this->info[$sonuid] = $info;
		$this->_update = true;
		
		//获得子嗣弹窗
		if ($is_new_son > 0){
			if (!isset(Master::$bak_data['a']['msgwin']['son'])){
				Master::$bak_data['a']['msgwin']['son'] = array();
			}
			Master::$bak_data['a']['msgwin']['son'][] = $this->getBase_buyid($sonuid);
		}
		
			
		//返回更新信息
		$s_outback = $this->getBase_buyid($sonuid);
		Master::back_data($this->uid,"son","sonList",array($s_outback),true);
		
		//重新构造阵法
		$TeamModel  = Master::getTeam($this->uid);
		$TeamModel->reset(3);
		
		return $sonuid;
	}
	
	
	/*
	 */
	public function sync()
	{
		if (!is_array($this->info)) return;
		$table = 'son_'.Common::computeTableId($this->uid);
		$db = $this->_getDb();
		foreach ($this->info as $k=>$v){
		    $v['name'] = mysql_real_escape_string($v['name']);
			if ($v['_update']){
				$this->info[$k]['_update'] = false;
				$sql=<<<SQL
update
	       `{$table}`
set
	`name`	='{$v['name']}',
	`state`	='{$v['state']}',
	`power`	='{$v['power']}',
	`ptime`	='{$v['ptime']}',
	`level`	='{$v['level']}',
	`exp`	='{$v['exp']}',
	`e1`	='{$v['e1']}',
	`e2`	='{$v['e2']}',
	`e3`	='{$v['e3']}',
	`e4`	='{$v['e4']}',
	`honor`	='{$v['honor']}',
	`tquid`	='{$v['tquid']}',
	`tqitem`	='{$v['tqitem']}',
	`spuid`	='{$v['spuid']}',
	`spsonuid`	='{$v['spsonuid']}',
	`sptime`	='{$v['sptime']}'
where
	`uid` ='{$this->uid}' 
	and
	`sonuid` ='{$k}' 
limit   1;
SQL;
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error SonModel_'.$sql);
				}
			}
		}
		return true;
	}
}
