<?php
require_once "ActHDBaseModel.php";

/*
 * 活动290
 */
class Act290Model extends ActHDBaseModel
{
	public $atype = 290;//活动编号
	public $comment = "转盘双12";
	public $b_mol = "zphuodong";//返回信息 所在模块
	public $b_ctrl = "zhuanpan";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_290';//活动配置文件关键字
	protected $_rank_id = 290;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'cons' => 0,  //已消耗(完成)量
		'yao1' => 0,  //外圈摇奖次数
		'yao2' => 0,  //内圈摇奖次数
		'list' => array(), //兑换档次
		
		'time' => 0,	   //每天刷新免费次数
		'free' => array(	//免费次数
			1 => array('num' => 0,'next' => 0), //外圈
			2 => array('num' => 0,'next' => 0), //内圈
		), 
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
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//构造输出
		$this->outf = array();
		if( parent::get_state() == 0 ){
			Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
		}
		
		//更新免费次数
		if(!Game::is_today($this->info['time'])){
			$this->info['time'] = $_SERVER['REQUEST_TIME'];
			$this->info['free'][1]['num'] = 0;
			$this->info['free'][2]['num'] = 0;
		}
		
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['info']['no']);
		$this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝
		
		//外圈免费次数
		$waitime = $this->info['free'][1]['next'] + $this->hd_cfg['wairwd']['freeT'] * 60 ;
		$this->outf['cfg']['wairwd']['cd'] = array(
			'next' => self::set_time($waitime),
			'num' => $this->hd_cfg['wairwd']['free'] - $this->info['free'][1]['num'],
			'label' => 'huodong_290_refresh_1',
		);
		//内圈免费次数
		$neitime = $this->info['free'][2]['next']  + $this->hd_cfg['neirwd']['freeT'] * 60 ;
		$this->outf['cfg']['neirwd']['cd'] = array(
			'next' => self::set_time($neitime),
			'num' => $this->hd_cfg['neirwd']['free'] - $this->info['free'][2]['num'],
			'label' => 'huodong_290_refresh_2',
		);
		
		
		//兑换商品
		$this->outf['cfg']['shop']['list'] = array();
		
		foreach($hd_cfg['shop']['list'] as $v){
			$this->outf['cfg']['shop']['list'][] = array(
				'id' => $v['id'],
				'need' => $v['need'],
				'items' => $v['items'],
				'is_limit' => $v['is_limit'],
				'limit' => $v['limit'] -$this->info['list'][$v['id']],
			);
		}
		$this->outf['cons'] = $this->info['cons'];  //活动期间获得的积分
	}
	
	
	/*
	 * 刷新免费次数
	 * $type 1 : 外圈      2 : 内圈
	 */
	public function back_free($type){
		
		if($type == 1){
			if($this->outf['cfg']['wairwd']['cd']['num'] <= 0){
				Master::error(CLUB_NO_DATA);  //参数错误
			}
		}
		if($type == 2){
			if($this->outf['cfg']['neirwd']['cd']['num'] <= 0){
				Master::error(CLUB_NO_DATA);  //参数错误
			}
		}
		
		$this->info['free'][$type]['num'] += 1;
		$this->info['free'][$type]['next'] = $_SERVER['REQUEST_TIME'];
		$this->save();
		
	}
	
	
	/*
	 *检查免费次数
	 * $type 1 : 外圈      2 : 内圈
	 */
	public function check_free($type){
		
		if($type == 1){
			if($this->outf['cfg']['wairwd']['cd']['num'] > 0 && $this->outf['cfg']['wairwd']['cd']['next'] <= 0 ){
				return true;
			}
		}
		if($type == 2){
			if($this->outf['cfg']['neirwd']['cd']['num'] > 0 && $this->outf['cfg']['neirwd']['cd']['next'] <= 0 ){
				return true;
			}
		}
		return false;
		
	}
	
	
	
	/*
	 * 返回活动信息--保存时不返回信息 
	 */
	public function back_data(){
		
	}
	
	/*
	 * 返回活动详细信息
	 */
	public function back_data_cd_u(){
		
		$data = array();
		$data['cfg']['wairwd']['cd'] = $this->outf['cfg']['wairwd']['cd'];
		$data['cfg']['neirwd']['cd'] = $this->outf['cfg']['neirwd']['cd'];
		$data['cons'] = $this->info['cons'];  //活动期间获得的积分
		
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
	}
	
	/*
	 * 返回活动--兑换商店
	 */
	public function back_data_shop_u(){
		
		$data = array();
		$data['cfg']['shop'] = $this->outf['cfg']['shop'];
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
	}
	
	/**
	 * 单次摇奖
	 * $type 1 : 外圈      2 : 内圈
	 */
	public function yao($type){
		
		//获取配置
		$cfg  = $this->get_cfg($type);
		
		static $cfg_rwd = array();
		//获得转盘档次配置
		if(empty($cfg_rwd[$type])){
			$cfg_rwd[$type] = array();
			foreach($cfg['list'] as $v){
				$cfg_rwd[$type][$v['dc']] = $v;
			}
		}
		
		$rid =  Game::get_rand_key(10000,$cfg_rwd[$type],'prob_10000');
		
		$ykey = 'yao'.$type;
		$this->info[$ykey] += 1;
		//判断是不是必中
		if( $cfg['cishu'] > 0 && $this->info[$ykey] >= $cfg['cishu'] ){
			$rid = $cfg['dc'];
			$this->info[$ykey] = 0;
		}
		$this->save();
		
		//配置是否正确
		if(empty($cfg_rwd[$type][$rid])){
			Master::error(ACT_14_CONFIGWRONG);
		}
		
		return  $cfg_rwd[$type][$rid];
		
	}
	
	/**
	 * 返回要扣除的元宝/可以获得的积分
	 * $type 1 : 外圈      2 : 内圈
	 * $num  1次    10 次
	 */
	public function pay($type,$num){
		
		//获取配置
		$cfg  = $this->get_cfg($type);
		//获取花费对应元宝
		$payNum = empty($cfg['need'])?0:$cfg['need'];
		$jifen = empty($cfg['jifen'])?0:$cfg['jifen'];
		if( $num == 10){
			$payNum = empty($cfg['needTen'])?0:$cfg['needTen'];
			$jifen *= $num;
		}
		
		return array(
			'pay' => $payNum,    //总消耗
			'jifen' => $jifen,  //总获得的积分
		);
	}
	
	/**
	 * 获取配置
	 * $type 1 : 外圈      2 : 内圈
	 */
	public function get_cfg($type){
		
		static $cfg = array();
		//获得转盘档次配置
		if(empty($cfg[$type])){
			//外圈
			$cfg[$type] = $this->hd_cfg['wairwd'];
			//内圈
			if( $type == 2){
				$cfg[$type] = $this->hd_cfg['neirwd'];
			}
		}
		
		//配置是否正确
		if(empty($cfg[$type])){
			Master::error(ACT_14_CONFIGWRONG);
		}
		
		return $cfg[$type];
	}
	
	/**
	 * 商店兑换
	 */
	public function get_shop($id){
		
		$shop = array();
		foreach($this->hd_cfg['shop']['list'] as $v){
			$shop[$v['id']] = $v;
		}
		
		//验证档次
		if(empty($shop[$id])){
			Master::error(PARAMS_ERROR.$id);
		}
		
		//如果限购
		if($shop[$id]['is_limit']){
			if(empty($this->info['list'][$id])){
				$this->info['list'][$id] = 0;
			}
			$this->info['list'][$id] += 1;
			if($shop[$id]['limit'] < $this->info['list'][$id]){
				Master::error(HD_TYPE8_EXCEED_LIMIT);
			}
			$this -> save();
		}
		
		return $shop;
	}
	
	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
		$news = 0; //不可领取
		return $news;
	}
	
	
	/**
	 * 列入跑马灯
	 * $data  : 信息
	 */
	public function add_pmd($data){
		
		$Sev81Model = Master::getSev81($this->hd_cfg['info']['id']);
		$Sev81Model->add_msg($data);
	}
	
	/**
	 * 输出跑马灯
	 * $uid : 玩家id
	 */
	public function out_pmd($uid,$init = 0){
		$Sev81Model = Master::getSev81($this->hd_cfg['info']['id']);
		//初始化
		if($init){
			$Sev81Model->list_init($uid);
		}
		$Sev81Model->list_click($uid);
	}
	
	/**
	 * 列入获奖情况
	 * $data  : 信息
	 */
	public function add_log($data){
		$Sev80Model = Master::getSev80($this->hd_cfg['info']['id']);
		$Sev80Model->add_msg($data);
	}
	
	/**
	 * 输出获奖情况
	 * $uid : 玩家id
	 */
	public function out_log($uid,$init = 0){
		$Sev80Model = Master::getSev80($this->hd_cfg['info']['id']);
		//初始化
		if($init){
			$Sev80Model->list_init($uid);
		}
		$Sev80Model->list_click($uid);
	}
	
	/**
	 * 输出获奖情况-历史消息
	 * $uid : 玩家id
	 * $id : 第几个
	 */
	public function out_log_history($uid,$id){
		$Sev80Model = Master::getSev80($this->hd_cfg['info']['id']);
		$Sev80Model->list_history($uid,$id);
	}
	
	/**
	 * 没超过当前时间,时间设为0
	 * $time
	 */
	public function set_time($time){
		if(Game::is_over($time)){
			return 0;
		}
		return $time;
	}
	
}




