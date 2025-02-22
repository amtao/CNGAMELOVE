<?php
require_once "ActHDBaseModel.php";

/*
 * 活动258
 */
class Act258Model extends ActHDBaseModel
{
	public $atype = 258;//活动编号
	public $comment = "魅力冲榜";
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "meili";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_258';//活动配置文件关键字
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'num' => 0,
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
		
		
		return $news;
	}
	
	/**
	 * 魅力值排行保存
	 * @param $num  通过的魅力值
	 */
	public function do_save($num){
		//在活动中
		if( parent::get_state() == 1){
            $this->info['num'] = $num;
            $this->save();

			//保存到排行榜中
			$Redis258Model = Master::getRedis258($this->hd_cfg['info']['id']);
            $Redis258Model->zIncrBy($this->uid,$this->info['num']);
		}
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
	
	/*
	 * 返回活动信息
	 */
	public function back_data_hd(){
		//配置信息
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
		//排行信息
		$Redis258Model = Master::getRedis258($this->hd_cfg['info']['id']);
        $Redis258Model->back_data();
        $Redis258Model->back_data_my($this->uid);
	}
}
