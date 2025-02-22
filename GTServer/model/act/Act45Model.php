<?php
require_once "ActBaseModel.php";
/*
 * 已领取红包列表
 * 根据活动id和联盟重置
 */
class Act45Model extends ActBaseModel
{
	public $atype = 45;//活动编号
	
	public $comment = "已领取红包列表";
	public $b_mol = "hbhuodong";//返回信息 所在模块
	public $b_ctrl = "receiveRedList";//返回信息 所在控制器
	public $hd_id = "huodong_295";
	public $hd_cfg;
	public $cid;

	//活动id和联盟id 重置
	//字段  money  stime etime
	public function __construct($uid, $cid)
	{
		$this->uid = intval($uid);
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($this->hd_cfg)){
			$Act40Model = Master::getAct40($this->uid);
			$this->cid = $Act40Model->info['cid'];
			parent::__construct($uid, $this->hd_cfg['info']['id'].$this->cid.Game::get_today_id());
		}
	}


	public function make_out()
	{
		$outf = array();
		if(!empty($this->info)){
			$outf = $this->info;
		}
		$this->outf = $outf;
	}

	/**
	 * 添加红包记录
	 * @param $id
	 * @param $num
	 */
	public function add($fuid,$id){
		$state = self::get_state();
		if($state == 0){
			Master::error(ACTHD_OVERDUE);
		}
		$this->info[] = array('uid'=> $fuid,'id'=>$id);
		$this->save();
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

}














