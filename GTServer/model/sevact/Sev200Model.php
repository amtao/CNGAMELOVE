<?php
/*
 * 新年活动-boss血量
 */
require_once "SevListBaseModel.php";
class Sev200Model extends SevListBaseModel
{
	public $comment = "新年活动-boss血量";
	public $act = 200;//活动标签
	public $hd_cfg;
	public $hd_id = 'huodong_298';
	public $_init = array(//初始化数据
		/*
		 * array(
		 *  'num' => 1,
		 * 	'boss' => ''
		 * )
		 */
	);

	public function __construct($hid, $cid, $serverID)
	{
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		parent::__construct($hid, $cid, $serverID);

		if(empty($this->info)){//第一轮 初始话
			self::_init();
		}
	}

	private function _init(){
		$this->info = array(
			'num' => 1,//当前轮数
			'boss' => array(
				1 => array(//第一轮
					'hp' => 100,//血量
					'stime' => Game::get_now(),//开启时间
					'etime' => 0,//死亡时间
					'kill' => 0,//击杀者id
				),
			),
			'daylist' => array(
				Game::get_today_long_id() => 1,
			),
		);
		$this->save();
	}

	/*
	 * 添加伤害
	 * @params uid 造成的伤害uid
	 * @params hurt 造成的伤害
	 * @params kill 是否直接击杀 1直接击杀
	 */
	public function add($uid,$hurt,$kill = 0){
		//判断新的一轮是否开启
		$kill_state = 0;// 0 未击杀 1击杀
		if(Game::dis_over($this->info['boss'][$this->info['num']]['stime'])){//返回？ 倒计时
			Master::error_msg(NIANSHOU_FIGHTED_AWAY);
			Master::back_s(2);
			$this->back_data();
		}else{//可以开启

			if(!isset($this->info['daylist'][Game::get_today_long_id()])){
				$this->info['daylist'][Game::get_today_long_id()] = 1;
			}
			//正常打可不可以击杀
			$boss = $this->info['boss'][$this->info['num']];
			if($hurt >= $boss['hp']){
				$u_kill = 1;
				$hurt = $boss['hp'];
			}else{
				$u_kill = 0;
			}

			//添加福气值
			Master::add_item($uid,2,41,$hurt);

			//结算
			if($kill || $u_kill){//杀死
				$kill_state = 1;
				//上一轮结算
				$this->info['boss'][$this->info['num']]['hp'] -= $hurt;
				$this->info['boss'][$this->info['num']]['etime'] = Game::get_now();
				$this->info['boss'][$this->info['num']]['kill'] = $uid;

				//新一轮初始化
				$this->info['num'] +=1;
				$ftime = empty($this->hd_cfg['boss']['ftime']) ? 60 : $this->hd_cfg['boss']['ftime'];
				$this->info['boss'][$this->info['num']] = array(
					'hp' => 100,//血量
					'stime' => Game::get_now()+$ftime,//开启时间
					'etime' => 0,//死亡时间
					'kill' => 0,//击杀者id
				);
				$outf = array(
					'state' => $kill ? 2 : 1,//是否激活吓退 0:未杀死 1：正常杀死 2：直接吓死
					'hurt' => $hurt,//道具造成伤害
				);
			}else{
				$this->info['boss'][$this->info['num']]['hp'] -= $hurt;
				$outf = array(
					'state' => 0,//未打死
					'hurt' => $hurt,//道具造成伤害
				);
			}
			$this->save();
			Master::back_data($uid,'newyear','fight',$outf);
			$this->back_data();
		}
		return $kill_state;
	}

	public function getDayList(){
		$dayList = array();
		if(!empty($this->info['daylist'])){
			foreach ($this->info['daylist'] as $day => $val){
				if(Game::get_today_long_id() == $day){
					continue;
				}
				$dayList[] = $day;
			}
		}
		return $dayList;
	}

	public function back_data(){
		//返回boss信息
		$outf = array(
			'hp' => $this->info['boss'][$this->info['num']]['hp'],
			'cd' => array(
				'next' => Game::dis_over($this->info['boss'][$this->info['num']]['stime']),
				'label' => 'newyear',
			),
		);
		Master::back_data(0,'newyear','bossinfo',$outf);
	}

}
