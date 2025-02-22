<?php
require_once "ActHDBaseModel.php";

/*
 * 活动272
 */
class Act272Model extends ActHDBaseModel
{
	public $atype = 272;//活动编号
	public $comment = "巾帼女将";
	public $b_mol = "njhuodong";//返回信息 所在模块
	public $b_ctrl = "nvjiang";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_272';//活动配置文件关键字
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		
	
	);
	
	
	/**
	 * @param unknown_type $uid   玩家id
	 * @param unknown_type $id    活动id
	 */
	public function __construct($uid)
	{
		$this->uid = intval($uid);
		//获取活动配置
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($this->hd_cfg['info']['id'])){
			parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
		}
	}
	
	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
		$news = 0; //不可领取
		$ItemModel = Master::getItem($this->uid);
		$HeroModel = Master::getHero($this->uid);
		foreach($this->hd_cfg['rwd'] as $rwd){
			//验证道具是否充足
			$flag = $ItemModel->sub_item(138,$rwd['need'],true);
			$hero_info = $HeroModel->check_info($rwd['id'],true);
			if($flag && !$hero_info){
				return 1; //可领取
			}
		}
		return $news;
	}
	
	/**
	 * 获得奖励
	 * $id 兑换的门客id
	 */
	public function get_rwd($id = 0){
		if( parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}
		//转化配置
		$brwd = Game::get_key2id($this->hd_cfg['rwd'],'id');
		if(empty($brwd[$id]['need'])){
			Master::error(PARAMS_ERROR);
		}
		//先判断道具是否充足
		$ItemModel = Master::getItem($this->uid);
		$ItemModel->sub_item(138,$brwd[$id]['need'],true);
		
		//加门客
		switch($id){
			case 50 :
				Master::add_item($this->uid,KIND_HERO,50);  //孙尚香
				Master::add_item($this->uid,KIND_WIFE,27,1,0,0);  //孙尚香
				break;
			default:
				Master::error(PARAMS_ERROR);
				break;
		}
		
		//扣除道具
		$ItemModel->sub_item(138,$brwd[$id]['need']);
	}
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//构造输出
		$this->outf = array();
		if( self::get_state() == 0 ){
			Master::error($this->hd_id.GAME_LEVER_UNOPENED);
		}
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['info']['no']);
		$this->outf['cfg'] = $hd_cfg;
	}
	
}
