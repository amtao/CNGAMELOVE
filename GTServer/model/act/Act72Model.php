<?php
require_once "ActBaseModel.php";
/*
 * 充值翻倍
 */
class Act72Model extends ActBaseModel
{
	public $atype = 72;//活动编号
	
	public $comment = "充值翻倍";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
	public $cfg ;//配置信息
	public $hd_id = 'huodong_72';//活动配置文件关键字
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		

	);
	
	public function __construct($uid){
		$this->uid = intval($uid);
		$this->cfg = self::rinfo();
		if(!empty($this->cfg)){
			parent::__construct($uid,$this->cfg['info']['id']);//执行基类的构造函数
		}
	}
	
	/**
	 * 保存已翻倍的
	 * $rmb:充值的rmb
	 */
	public function do_save($rmb,$diamond){
		if(empty($this->cfg)){
			return 0;
		}
		
		$UserModel = Master::getUser($this->uid);
		$channel = $UserModel->info['channel_id'];
		$platform = $UserModel->info['platform'];
		Common::loadModel('OrderModel');
		$list = OrderModel::recharge_list($platform,$channel);
		if(empty($list[$rmb]['type']) || $list[$rmb]['type'] != 1){
			return 0;
		}
		
		if( $this->info[$rmb] && $this->info[$rmb] == 1){
			return 0;
		}
		
		//发翻倍邮件奖励
		$beishu = $this->cfg['rwd']['beishu'] -1;
		
		if($beishu <= 0){
			return 0;
		}
		$this->info[$rmb] = 1;
		$this->save();
		
		return $beishu;
	}
	
	/**
	 * 返回信息
	 * $rmb:充值的rmb
	 */
	public function do_out(){
		if(empty($this->cfg)){
			return -1;
		}
		return $this->info;
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		//充值-充值档次
        $Act70Model = Master::getAct70($this->uid);
        $Act70Model->back_data();
	}
	
	/**
	 * 返回充值翻倍信息
	 */
	public function rinfo(){
		
		$chongzhi = self::newinfo();
		if(!empty($chongzhi)){
			return $chongzhi;
		}
		$chongzhi = array();
		
		$SevidCfg = Common::getSevidCfg();
		//获取配置
		$chongzhi = Game::get_peizhi('chongzhi');
		Common::loadModel('ServerModel');
		$openDay = ServerModel::isOpen($SevidCfg['sevid']);
		
		//开服时间
		if(  !empty($chongzhi['info']['startDay']) && !empty($chongzhi['info']['endDay'])){
			if($openDay >= $chongzhi['info']['startDay'] && $openDay <= $chongzhi['info']['endDay']){
				return $chongzhi;
			}
		}else{
			//固定日期开始时间内
			if(  !empty($chongzhi['info']['startTime']) && !empty($chongzhi['info']['endTime']) 
			  && $_SERVER['REQUEST_TIME'] >= strtotime($chongzhi['info']['startTime']) 
			  && $_SERVER['REQUEST_TIME'] <= strtotime($chongzhi['info']['endTime']) ){
			  	return $chongzhi;
			}
		}
		return array();
	}
	
	/**
	 * 返回充值翻倍信息 -- 新版
	 */
	public function newinfo(){
		Common::loadModel('HoutaiModel');
		$chongzhi = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($chongzhi)){
			return $chongzhi;
		}
		return array();
	}
	
	public function get_news(){
	    return 0;
	}
	
}