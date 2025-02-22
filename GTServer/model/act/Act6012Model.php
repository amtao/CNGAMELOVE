<?php
require_once "ActHDBaseModel.php";

/*
 * 活动272
 */
class Act6012Model extends ActHDBaseModel
{
	public $atype = 6012;//活动编号
	public $comment = "福利签到";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "actqd";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_6012';//活动配置文件关键字
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'type' => 0,
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
	public function get_rwd(){
		if( parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE.__LINE__);
		}
        $hd_cfg = $this->hd_cfg;
        $dur_day = Game::day_dur($hd_cfg['info']['sTime']);
        foreach ($hd_cfg['rwd'] as $rwd){
            if ($rwd['need'] == $dur_day){
                $key = Game::get_rand_key1($rwd['items'], 'prop');
                if ($key == 0){
                    $Sev6012Model = Master::getSev6012();
                    $msg = "#actqiandao#::1";
                    $Sev6012Model->add_msg($this->uid, $msg, 3);
                }
                $this->info['type'] = $key + 1;
                Master::add_item2($rwd['items'][$key]);
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
		$this->outf = array();
		if( self::get_state() == 0 ){
			Master::error($this->hd_id.GAME_LEVER_UNOPENED);
		}
		$hd_cfg = $this->hd_cfg;
        $dur_day = Game::day_dur($hd_cfg['info']['sTime']);
        $items = null;
        $label = "";
        $nongli = "";
        foreach ($hd_cfg['rwd'] as $rwd){
            if ($rwd['need'] == $dur_day){
                $items = $rwd['items'][$this->info['type']-1];
                $label = empty($rwd['label'])?"":$rwd['label'];
                $nongli = empty($rwd['nongli'])?"":$rwd['nongli'];
                break;
            }
        }

		$this->outf = array('type'=> $this->info['type'], 'items'=>$items, 'label'=> $label, 'nongli'=>$nongli);
	}
	
}
