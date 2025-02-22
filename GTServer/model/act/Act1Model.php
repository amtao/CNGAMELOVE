<?php
require_once "ActBaseModel.php";
/*
 * 3种类资源经营信息
 */
class Act1Model extends ActBaseModel
{
	public $atype = 1;//活动编号
	public $label = "jingying";//倒计时标记

	public $comment = "3种资源经商";
	public $b_mol = "jingYing";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器

	//public

	/*
	 * 初始化结构体
	 */
	public $_init = array(
		'coin' => array(
			'num' => 0,		//剩余次数
			'time' => 0,	//上次时间
			'sum' => 0,		//累计金币
		),
		'food' => array(
			'num' => 0,		//剩余次数
			'time' => 0,	//上次时间
			'sum' => 0,		//累计粮食
		),
		'army' => array(
			'num' => 0,		//剩余次数
			'time' => 0,	//上次时间
			'sum' => 0,		//累计进队
		),
		'win' => array(
			'id' => 0,		//事件ID
			'heroid' => 0,	//门客ID
			'get' => 0,		//收益
		),
	);

	/*
	 * 构造输出结构体
	 */
	public function make_out()
	{
		$UserModel = Master::getUser($this->uid);
		$level = $UserModel->info['level'];
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		$vipAdd = (isset($vip_cfg_info['jingying'])?$vip_cfg_info['jingying']:0);

		//构造输出结构体 $outf
		foreach ($this->info as $k => $v){
			$this->outf[$k] = $this->_mk_out($k,$level,$vipAdd);
		}
	}

	private function _mk_out($type,$level,$vipAdd)
	{
		if ($type == 'win'){
			return $this->info['win'];
		}
		$info = $this->info[$type];

		//次数上限
		$guan_cfg = Game::getCfg('guan');//官阶配置
		$max = $guan_cfg[$level]['max_jy'] + $vipAdd;
		$userinfo = Master::fuidData($this->uid);

		$id = 2;
		$id = $type == 'food'?3:$id;
        $id = $type == 'army'?4:$id;

		//冷却时间算法
//		$cd = min(floor($userinfo['ep']['e'.$id]/800+1)*60,3600);
		$cd = Game::getCfg_formula()->jingying_time($userinfo['ep']['e'.$id]); //积分算法?
        $cd = $cd < 60?60:$cd;
		$hf_data = Game::hf_num($info['time'],$cd,$info['num'],$max);

		$this->info[$type]['time'] = $hf_data['stime'];
		$this->info[$type]['num'] = $hf_data['num'];

		return array(
			'next' => $hf_data['next'],
			'num' => $hf_data['num'],
			'label' => $this->label,
			'max' => $max,
		);
	}

	/*
	 * private
	 */
	private function id2type($id){
		$cfg = array(
			2 => 'coin',
			3 => 'food',
			4 => 'army',
		);
		if (empty($cfg[$id])){
			Master::error('jyid_err_'.$id);
		}
		return $cfg[$id];
	}

	/*
	 * 计算一次征收的资源数量
	 */
	public function get_onetime_Num($id){
		//获取阵法信息
		$team = Master::get_team($this->uid);
		$act6003Model = Master::getAct6003($this->uid);
		return $team['allep'][$id] + $act6003Model->getAddEp($id);
	}

    /*
     * 计算一次征收的资源数量
     */
    private function get_onetime_cost($id){
        //获取阵法信息
        $team = Master::get_team($this->uid);
        return $team['allep'][$id];
    }

	/*
	 * 执行经营
	 * 经营种类 , 是否一键全拉
	 */
	public function jingying($id,$is_all = 0){		
		//ID转换
		$sun_type = $this->id2type($id);
		if ($this->outf[$sun_type]['num'] < 1){
			if ($is_all > 0){
				return 0;
			}else{
				Master::error(OPERATE_NUM_SHORT);
			}
		}
		//次数
		$times = $is_all?$this->info[$sun_type]['num']:1;

		//计算资源
		$zy_one = $this->get_onetime_Num($id);
		$zy_count = ($zy_one + 1000) * $times;

		//如果是征兵
		if ($id == 4){
			$UserModel = Master::getUser($this->uid);

			//判断还有没有粮草
			if ($UserModel->info['food'] <= 0) {
				if ($is_all > 0){
					//如果是一键征收 / 不报错 不征兵即可
					return 0;
				}else{
					Master::error(OPERATE_PROVISION_SHORT);
				}
			}

			//如果粮草不够
            $cost_one = $this->get_onetime_cost($id);
            $cost = $cost_one * $times;
			if ($UserModel->info['food'] < $cost){
                //判断这些粮草 够几次征兵
                $times = floor($cost/$cost_one);
				//使用剩余的所有粮草
                $cost = $times * $cost_one;
			}
			//减去粮草
			Master::sub_item($this->uid,KIND_ITEM,3, $cost);
		}

		//$times 次数  计算暴击 每次暴击 增加一次分量的资源
		/*
		$haoshi_prob_1000 = 500;//好事成双概率
		for ($i = 0 ; $i < $times ; $i ++){
			if (rand(1,1000) < $haoshi_prob_1000){
				//暴击窗口
				$win = array(
					'zyid' => $id,
					'bas' => 2,//倍数 写死2
				);
				//暴击窗口增加
				Master::back_win_array('jingYing','haoshi',$win);

				//加上资源
				Master::add_item($this->uid,1,$id,$zy_one);
			}
		}
		*/
		//神迹
//		if($id == 2){
//			$Act65Model = Master::getAct65($this->uid);
//			if ($Act65Model->rand(1)){
//				//触发神迹:十倍商产
//
//				//加上资源 (分2次加 用来跳窗口)
//				Master::add_item($this->uid,1, $id,($zy_one + 1000) * 10);
//			}
//		}

		//触发经营事件
		$this->check_event($id);

		//减去经营次数
		$this->info[$sun_type]['num'] -= $times;
		$this->info[$sun_type]['sum'] += $zy_one;
		$this->save();

		//加上资源
        //奖励倍数
        $beishu = Game::pv_beishu('jingying');

		Master::add_item($this->uid,1,$id,ceil($zy_count * $beishu));

		//勤政爱民次数增加
		$Act31Model = Master::getAct31($this->uid);
		$Act31Model->add($times);

		//活动消耗 - 经营消耗
		$HuodongModel = Master::getHuodong($this->uid);
        //舞狮大会
        $Act6224Model = Master::getAct6224($this->uid);
		switch($id){
			case 2:
				$HuodongModel->xianshi_huodong('huodong212',$times);
                //办差赚取银两
                $Act6224Model->task_add(6,$times);
				break;
			case 3:
				$HuodongModel->xianshi_huodong('huodong213',$times);
                //办差赚取银两
                $Act6224Model->task_add(7,$times);
				break;
			case 4:
				$HuodongModel->xianshi_huodong('huodong214',$times);
                //办差获取名声
                $Act6224Model->task_add(8,$times);
				break;
		}

		return $times;
	}

	private function check_event($type){
		//用户数据		
		$userinfo = Master::fuidData($this->uid);

		//经营事件表
		$jyevent = Game::getcfg('jyevent');
		$arr = array();

		$lv = $userinfo['level'] == 0?1:$userinfo['level'];
		foreach($jyevent as $item){
			if ($item['type'] == $type && $lv >= $item['lv']){
				$arr[] = $item;
			}
		}

		$c = count($arr);
		if ($c == 0){
			return;
		}
		$rand = $arr[0];
		if ($c > 1){
			$rand = $arr[rand(0, count($arr)-1)];
		}
		
		if ($rand && $rand['prop'] > rand(0, 10000)){
			$HeroModel = Master::getHero($this->uid);
			$data = $this->info['win'];
			$data['id'] = $rand['id'];
			$data['heroid'] = $HeroModel->get_one_hero_type($rand['herotype']);
			$data['get'] = 0;
			$this->info['win'] = $data;
			Master::back_data($this->uid,"jingYing","win", array('id'=>$data['id'], 'heroid'=>$data['heroid']));
		}
	}

	public function deal_event(){
		//经营数据
		$win = $this->info['win'];
		if ($win['id'] == 0 || $win['get'] != 0){
			$this->info['win'] = array('id'=>0, 'heroid'=>0, 'get'=>0);
			$this->save();
			Master::error(OPERATE_EVENT_ERROR);
		}

		$item = Game::getcfg_info('jyevent', $win['id']);
		if (empty($item))return;
		$prop = rand($item['percentmin'], $item['percentmax']);
        
		$id = $item['type'];
		//计算资源
		$zy_one = $this->get_onetime_Num($id) * $prop / 10000;
		Master::add_item($this->uid,1,$id,ceil($zy_one));
		
		$edata = $this->info['win'];
		$edata['get'] = ceil($zy_one);
		$this->info['win'] = $edata;
		$this->info['win']['heroid'] = 0;
		$this->save();
	}

	/*
	 * 可以选择选项么
	 */
	public function canSelectStory($groupId){
		$win = $this->info['win'];
		$jyItem = Game::getcfg_info('jyevent', $win['id']);
		$events = Game::getcfg('jyevent_dialogue');
		if (!empty($events) && !empty($jyItem)){
			foreach($events as $item){
				if ($item['type'] == $jyItem['type'] && $item['nextid'] == $groupId){
					return true;
				}
			}
		}
		return false;
	}

	/*
	 * 加上经营次数
	 */
	public function add_time($id,$num = 1){
		//ID转换
		$sun_type = $this->id2type($id);

		/*
		if ($this->outf[$sun_type]['num'] > 0){
			Master::error("还有次数");
		}*/


		$UserModel = Master::getUser($this->uid);
		$level = $UserModel->info['level'];
		$guan_cfg = Game::getCfg('guan');//官阶配置
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		//次数上限
		$max = $guan_cfg[$level]['max_jy'] + (isset($vip_cfg_info['jingying'])?$vip_cfg_info['jingying']:0);
//		if ($this->outf[$sun_type]['num'] + $num > $max){
//			Master::error(OPERATE_POWER_GT_MAX);
//		}

		//加上次数
		$this->info[$sun_type]['num'] += $num;
		//保存
		$this->save();

		//减去道具
		Master::sub_item($this->uid,KIND_ITEM,122,$num);
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		foreach ($this->outf as $k => $v){
			Master::back_data($this->uid,$this->b_mol,$k,$v);
		}
	}
}
