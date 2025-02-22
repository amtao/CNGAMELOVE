<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6135
 */
class Act6135Model extends ActHDBaseModel
{
	public $atype = 6135;//活动编号
	public $comment = "珍宝阁积分冲榜";
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "treasure";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_6135';//活动配置文件关键字
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'num' => 0, //存放活动开启时候玩家的积分
		'id' => 0, //活动id
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
	 * 积分分数排行保存
	 * @param $num  通过的积分数
	 */
	public function do_save($num){
		//在活动中
		if( parent::get_state() == 1){
			//初始化
            if($this->info['id'] != $this->hd_cfg['info']['id']){
                $this->info['num'] = $num;
                $this->info['id'] = $this->hd_cfg['info']['id'];
                $this->save();
            }
			//当前积分
			$Redis6110Model = Master::getRedis6110();
			$score = $Redis6110Model->zScore($this->uid);
			//保存到排行榜中
			$Redis6135Model = Master::getRedis6135($this->hd_cfg['info']['id']);
            $Redis6135Model->zAdd($this->uid,$score - $this->info['num']);

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
		$Redis6135Model = Master::getRedis6135($this->hd_cfg['info']['id']);
        $Redis6135Model->back_data();
        $Redis6135Model->back_data_my($this->uid);

	}


    /**
     * 积分分数排行保存
     * @param $num  通过的积分数
     */
    public function do_debug($num){
        //在活动中
        if( parent::get_state() == 1){
            //初始化
            $this->info['num'] = $num;
            $this->info['id'] = $this->hd_cfg['info']['id'];
            $this->save();
            //当前积分
            $Redis6110Model = Master::getRedis6110();
            $score = $Redis6110Model->zScore($this->uid);
            //保存到排行榜中
            $Redis6135Model = Master::getRedis6135($this->hd_cfg['info']['id']);
            $Redis6135Model->zAdd($this->uid,$score - $this->info['num']);

        }
    }

	
}
