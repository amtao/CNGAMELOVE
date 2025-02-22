<?php
require_once "ActHDBaseModel.php";

/*
 * 活动272
 */
class Act6152Model extends ActHDBaseModel
{
	public $atype = 6152;//活动编号
	public $comment = "兑换";
	public $b_mol = "duihuodong";//返回信息 所在模块
	public $b_ctrl = "duihuan";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_6152';//活动配置文件关键字
	

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
        $WifeModel = Master::getWife($this->uid);
        foreach($this->hd_cfg['rwd'] as $rwd){
            //验证道具是否充足
            if ($rwd['heroid'] != 0){
                $flag = $ItemModel->sub_item($rwd['itemid'],$rwd['need'],true);
                $hero_info = $HeroModel->check_info($rwd['heroid'],true);
                if($flag && !$hero_info){
                    return 1; //可领取
                }
            }
            else if ($rwd['wifeid'] != 0){
                $flag = $ItemModel->sub_item($rwd['itemid'],$rwd['need'],true);
                $wife_info = $WifeModel->check_info($rwd['wifeid'],true);
                if($flag && !$wife_info){
                    return 1; //可领取
                }
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
		$heroid = $id > 200?0:$id;
		$wifeid = $id > 200?$id % 200:0;

		//转化配置
        $findRwd = null;
        foreach($this->hd_cfg['rwd'] as $rwd){
            if ($heroid != 0 && $rwd['heroid'] == $heroid){
                $findRwd = $rwd;
                break;
            }
            if ($wifeid != 0 && $rwd['wifeid'] == $wifeid){
                $findRwd = $rwd;
                break;
            }
        }

		if(empty($findRwd) || empty($findRwd['need'])){
			Master::error(PARAMS_ERROR);
		}
		//先判断道具是否充足
		$ItemModel = Master::getItem($this->uid);
		if (!$ItemModel->sub_item($findRwd['itemid'], $findRwd['need'],true)){
		    return;
        }
		
		//加门客
        if ($heroid != 0){
            Master::add_item($this->uid,KIND_HERO,$heroid);  //孙尚香
        }
        else if ($wifeid != 0){
            Master::add_item($this->uid,KIND_WIFE,$wifeid,1,0,0);  //孙尚香
        }
		
		//扣除道具
		$ItemModel->sub_item($findRwd['itemid'], $findRwd['need']);
        $this->make_out();
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
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['info']['no']);
        $hd_cfg['info']['news'] = $this->get_news();
		$this->outf = $hd_cfg;
	}
	
}
