<?php
require_once "ActHDBaseModel.php";

/*
 * 活动272
 */
class Act6014Model extends ActHDBaseModel
{
	public $atype = 6014;//活动编号
	public $comment = "吉祥话";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "jxh";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_6014';//活动配置文件关键字
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'day' => 0,
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
        return 0;
	}
	
	/**
	 * 获得奖励
	 * $id 兑换的门客id
	 */
	public function get_rwd($msg){
		if( parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE.__LINE__);
		}
        $hd_cfg = $this->hd_cfg;
        $dur_day = Game::day_dur($hd_cfg['info']['sTime']);
        if ($dur_day <= $this->info['day'])return;
        foreach ($hd_cfg['rwd'] as $rwd){
            if ($rwd['need'] == $dur_day){
                foreach($rwd['label'] as $str){
                    $find = stripos($msg, $str);
                    if (!empty($find) || $find === 0){
                        Master::add_item3($rwd['items']);
                        $this->info['day'] = $dur_day;
                        break;
                    }
                }
                break;
            }
        }
        $this->save();
        $this->back_data_hd();
	}
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//构造输出
		if( self::get_state() == 0 ){
			Master::error($this->hd_id.GAME_LEVER_UNOPENED);
		}

		$this->outf = $this->info;
	}
	
}
