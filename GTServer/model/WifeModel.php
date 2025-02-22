<?php
//老婆
require_once "AModel.php";
class WifeModel extends AModel
{
	public $_key = "_wife";
	protected  $updateSetKey = array(
		'skill','state',
	);
	protected $updateAddKey =  array(
		'love','flower','exp','num'
	);
	
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());
		
		if($this->info == false){
			$table = 'wife_'.Common::computeTableId($this->uid);
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
			$table_son = 'son_'.Common::computeTableId($this->uid);
			foreach ($data as $v){
				$v['skill'] = json_decode($v['skill'],true);
				$sql_son = "select count(`uid`) as 'snum' from `{$table_son}` where `uid`={$this->uid} and `mom`='{$v['wifeid']}'";
				$sdata = $db->fetchRow($sql_son);
				$v['num'] = $sdata['snum'];
				$info[$v['wifeid']] = $v;
			}
			$this->info = $info;
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	/*
	 * 检查红颜是否合法 并返回红颜数据引用
	 */
	public function check_info($id,$is_click = false){
		if (empty($this->info[$id])){
			if ($is_click){
				return false;
			}
			Master::error('wife_id_err_'.$id);
		}else{
			return $this->info[$id];
		}
	}
	
	
	/*
	 * 获取玩家所有wifeid  存储格式:  array( id , id , ...)
	 */
	public function get_all_wifes(){
	    //获取所有门客id
	    $all_wifes = array_keys($this->info);
	    if(empty($all_wifes)){
	        Master::error(WIFE_NOT_FUND);
	    }
	    return $all_wifes;
	}
	
	/*
	 * 随机获取一只玩家拥有的红颜id
	 */
	public function get_one_wife(){
	    //数组随机id
	    $all_wifes = self::get_all_wifes();
	    $key_id = array_rand($all_wifes,1);
	    if(empty($all_wifes[$key_id])){
	        Master::error('get_one_wife_err');
	    }
	    return $all_wifes[$key_id];
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
		Master::back_data($this->uid,'wife','wifeList',$data);
		$Act11Model = Master::getAct11($this->uid);
		$Act11Model->back_data();
		$Act6131Model = Master::getAct6131($this->uid);
		$Act6131Model->back_data();
	}
	
	/*
	 * 获取输出值单个
	 */
	public function getBase_buyid($wifeid)
	{
		$info = $this->info[$wifeid];
		
		$wskill = array();
		$wife_skill_cfg = Game::getcfg('wife_skill');
		foreach ($info['skill'] as $k => $v){
			$exptp = $wife_skill_cfg[$k]['add'];
			$exp = 0;
			switch($exptp){
				case 1:
					$exp = intval(0.0048*pow($v,3)+0.01298*pow($v,2)+1.0483*$v+5.851);
				break;
				case 2:
					$exp = intval(0.0099*pow($v,3)+0.0345*pow($v,2)+0.9494*$v+11.276);
					break;
				case 3:
					$exp = intval(3.6667*pow($v,3)+16*pow($v,2)+1.3333*$v+111);
					break;
			}
			$wskill[] = array(
				'id' => $k,
				'level' => $v,
				'exp' => $exp,
			);
		}
		
		$data = array(
			'id' => $info['wifeid'],
			'love' => $info['love'],
			'flower' => $info['flower'],
			'exp' => $info['exp'],
			'skill' => $wskill,
			'state' => $info['state'],
		    'num' => $info['num']
		);
		return $data;
	}
	
	/*
	 * add wife
	 * 增加一个老婆
	 */
	public function add_wife($wifeid){
		//判断这个老婆是不是已经存在
		if (!empty($this->info[$wifeid])){
			return false;
			//Master::error(WIFE_ALREADY_OWNED);
		}
		
		//获取红颜配置
		$wife_cfg = Game::getcfg('wife');
		
		if (empty($wife_cfg[$wifeid])){
			Master::error('wife_cfg_id_err_'.$wifeid);
		}
		if (empty($wife_cfg[$wifeid]['open'])){
			Master::error(WIFE_WEIKAIFANG.$wifeid);
		}
		
		$wife_cfg_info = $wife_cfg[$wifeid];
		//红颜技能
		$wife_skill_cfg = Game::getcfg_info('wife_skill_id',$wifeid);
		//红颜拥有技能ID配置
		$w_skill = array();
		foreach ($wife_skill_cfg as $wsid){
//			$wife_skill_id_cfg[$wsid];
			$w_skill[$wsid] = 0;
		}
		
		//添加红颜
		$_update = array(
			'wifeid' => $wifeid,
			'love' => 0,
			'flower' => $wife_cfg_info['wflower'],
			'exp' => 0,
			'skill' => $w_skill,
			'state' => 1,
		);
		$this->update($_update);
		
		
		/*
		if (!isset(Master::$bak_data['a']['msgwin']['wife'])){
			Master::$bak_data['a']['msgwin']['wife'] = array();
		}
		Master::$bak_data['a']['msgwin']['wife'][] = array('id'=>$wifeid);
		*/

        //主线任务 - 刷新
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_refresh(43);

        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
		
		return true;
	}
	
	/*
	 * 更新
	 */
	public function update($data)
	{
		if (!isset($data['wifeid'])){
			exit ('update_wife_id_null');
		}

		$update_skill = false;
		if (isset($this->info[$data['wifeid']])){//存在 则更新
			$info = $this->info[$data['wifeid']];
			//更新
			foreach ($data as $k => $v){
				if (in_array($k,$this->updateSetKey)){
					$info[$k] = $v;
					
					//每次更新技能等级 更新红颜对应英雄
					if($k == 'skill'){
						Master::add_wife_rst($data['wifeid']);
                        $update_skill = true;
					}
				} elseif (in_array($k,$this->updateAddKey)){
					$info[$k] += $v;
					//每次更新亲密值 / 检查技能解锁
                    switch ($k){
                        case 'love':
                            $wife_skill_cfg = Game::getcfg('wife_skill');

                            //遍历已经拥有的技能列表
                            foreach ($info['skill'] as $skid => $sklv){
                                //如果亲密度达到 并且技能还没解锁
                                if ($wife_skill_cfg[$skid]['love'] <= $info['love'] && $sklv < 1){
                                    //解锁该技能
                                    $info['skill'][$skid] = 1;
                                    //记录红颜更新对应英雄
                                    Master::add_wife_rst($data['wifeid']);
                                    $update_skill = true;
                                }
                            }
                            Master::add_u_type("alllove");

                            //御花园
                            $Act6190Model = Master::getAct6190($this->uid);
                            $Act6190Model->addType(13, $v);

                            //舞狮大会 - 知己好感涨幅
                            $Act6224Model = Master::getAct6224($this->uid);
                            $Act6224Model->task_add(24,$v);
                            break;
                        case 'exp':
                            //御花园
                            $Act6190Model = Master::getAct6190($this->uid);
                            $Act6190Model->addType(8, $v);
                            //活动消耗 - 知己技能经验涨幅冲榜
                            $HuodongModel = Master::getHuodong($this->uid);
                            $HuodongModel->chongbang_huodong('huodong6217',$this->uid,$v);

                            break;
                    }
				}else{
					//exit('update_wife_type_err');
				}
			}
			$info['_update'] = true;
		}else{
			//新建
			$info = array();
			$info['wifeid'] = $data['wifeid'];
			$info['love'] = isset($data['love'])?$data['love']:1;
			$info['flower'] = isset($data['flower'])?$data['flower']:10;
			$info['exp'] = isset($data['exp'])?$data['exp']:0;
			$info['skill'] = isset($data['skill'])?$data['skill']:array();
			$info['state'] = isset($data['state'])?$data['state']:1;
			$info['num'] = 0;//初始孩子个数
			//插入数据库
			$table = 'wife_'.Common::computeTableId($this->uid);
			$skill_json = json_encode($info['skill']);
			$sql = "insert into `{$table}` set 
				`uid`='{$this->uid}',
				`wifeid`='{$info['wifeid']}',
				`love`='{$info['love']}',
				`flower`='{$info['flower']}',
				`exp`='{$info['exp']}',
				`skill`='{$skill_json}',
				`state`='{$info['state']}'";
			$db = $this->_getDb();
			$db->query($sql);
			
			//新增红颜流水
			Game::cmd_flow(14,$data['wifeid'],1,1);
		}
		$this->info[$data['wifeid']] = $info;
		$this->_update = true;
		
		//返回更新信息
		$h_info = $this->getBase_buyid($data['wifeid']);
		//返回更新信息
		Master::back_data($this->uid,'wife','wifeList',array($h_info),true);

		if ($update_skill){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(2);
        }
		//亲密
		if (isset($data['love'])){
			Game::cmd_flow(15,$data['wifeid'],$data['love'],$info['love']);
		}
		//魅力
		if (isset($data['flower'])){
			Game::cmd_flow(16,$data['wifeid'],$data['flower'],$info['flower']);
		}
		//经验
		if (isset($data['exp'])){
			Game::cmd_flow(21,$data['wifeid'],$data['exp'],$info['exp']);
		}
	}
	
	
	/*
	 */
	public function sync()
	{
		if (!is_array($this->info)) return;
		$table = 'wife_'.Common::computeTableId($this->uid);
		$db = $this->_getDb();
		foreach ($this->info as $k=>$v){
			if ($v['_update']){
				$this->info[$k]['_update'] = false;
				$skill_json = json_encode($v['skill']);
				$sql=<<<SQL
update
	       `{$table}`
set
	`love`	='{$v['love']}',
	`flower`	='{$v['flower']}',
	`exp`	='{$v['exp']}',
	`skill`	='{$skill_json}',
	`state`	='{$v['state']}'
where
	`uid` ='{$this->uid}' 
	and
	`wifeid` ='{$k}' 
limit   1;
SQL;
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error wifeModel_'.$sql);
				}
			}
		}
		return true;
	}
}
