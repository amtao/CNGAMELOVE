<?php
require_once "ActBaseModel.php";
/*
 * 我的红包券
 * 根据活动id和联盟重置
 */
class Act43Model extends ActBaseModel
{
	public $atype = 43;//活动编号
	
	public $comment = "我的红包券";
	public $b_mol = "hbhuodong";//返回信息 所在模块
	public $b_ctrl = "myRedTicket";//返回信息 所在控制器
	public $hd_id = "huodong_295";
	public $hd_cfg;

	//活动id和联盟id 重置
	//字段  money  stime etime
	public function __construct($uid)
	{
		$this->uid = intval($uid);
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($this->hd_cfg)){
			$Act40Model = Master::getAct40($this->uid);
			parent::__construct($uid, $this->hd_cfg['info']['id'].$Act40Model->info['cid']);
		}
	}

	/**
	 * 构造输出函数
	 */
	public function make_out()
	{
		$outf = array(//初始话
			'money' => 0,
			'num' => 0,
			'list' => array()
		);
		if(!empty($this->info)){
			foreach ($this->info as $k => $val){
				if($val['isuse']){//已用
					$state = 1;
					$outf['money'] += $val['money'];
					$outf['num'] += 1;
				}elseif(Game::dis_over($val['etime'])){//可使用
					$state = 0;
				}else{
					$state = 2;
				}

				$outf['list'][] = array(
					'id' => $k,
					'money' => $val['money'],
					'stime' => $val['stime'],
					'etime' => $val['etime'],
					'state' => $state, // 0: 可用  1：已用 2：过期
				);
			}
		}
		$this->outf = $outf;
	}

	/**
	 * 添加红包券
	 * @param $money
	 */
	public function add($money){
		$state = self::get_state();
		if($state == 0 || $state == 2){
			return;
		}
		$this->info[] = array(
			'money' => $money,
			'stime' => Game::get_now(),
			'etime' => Game::get_over(86400),
			'isuse' => 0,//是否已使用
		);
		$this->save();
		Common::loadModel('HoutaiModel');
		$outf = HoutaiModel::get_huodong_list($this->uid,$this->hd_id);
		Master::back_data($this->uid,'huodonglist','all',$outf,true);
		$max_key = count($this->info)-1;
		Game::cmd_flow(47,"{$this->uid}_{$max_key}($money)",1,1);
	}

	/**
	 * 活动活动状态
	 * 返回:
	 * 0: 活动未开启
	 * 1: 活动中
	 * 2: 活动结束,展示中
	 */
	public function get_state(){
		$state = 0;  //活动未开启
		if(!empty($this->hd_cfg) ){
			if(Game::dis_over($this->hd_cfg['info']['showTime'])){
				$state = 2;  //活动结束,展示中
			}
			if(Game::dis_over($this->hd_cfg['info']['eTime'])){
				$state = 1;  //活动中
			}
		}
		return $state;
	}

	/**
	 * 能否能使用
	 * @param $id
	 * @return $money
	 */
	public function isuse($id){
		if(empty($this->info[$id])){
			Master::error(HBHD_NO_FIND_VOUCHER);
		}elseif($this->info[$id]['isuse']){
			Master::error(HBHD_USED_VOUCHER);
		}elseif(Game::is_over($this->info[$id]['etime'])){
			Master::error(HBHD_OVERDUE_VOUCHER);
		}
		$this->info[$id]['isuse'] = 1;
		$this->save();
		Game::cmd_flow(47,"{$this->uid}_$id({$this->info[$id]['money']})",-1,0);
		return $this->info[$id]['money'];
	}

	public function get_news(){
		$news = 0;
		$state = self::get_state();
		if($state != 0 && !empty($this->info)){
			foreach ($this->info as $val){
				if($val['isuse']){//已用
					continue;
				}elseif(Game::dis_over($val['etime'])){//可使用
					$news = 1;
				}else{
					continue;
				}
			}
		}
		return $news;
	}
}














