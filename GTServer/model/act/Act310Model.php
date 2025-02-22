<?php
require_once "ActHDBaseModel.php";

/*
 * 活动310联盟势力涨幅冲榜排行
 */
class Act310Model extends ActHDBaseModel
{
	public $atype = 310;//活动编号
	public $comment = "联盟势力涨幅冲榜排行";
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "clubshili";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_310';//活动配置文件关键字
	
	
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
	 * 联盟势力涨幅排行保存
	 * @param $num  联盟增加的分数
	 */
	public function do_save($num){

        $num = intval($num);
		if( parent::get_state() == 1){
		    //保存个人数据
            $this->info['cons'] += $num;
            $this->save();

            //如果有公会,加入排行
            $Act40Model = Master::getAct40($this->uid);
            $cid = $Act40Model->info['cid'];
            if(!empty($cid)){
                $Redis310Model = Master::getRedis310($this->hd_cfg['info']['id']);
                $Redis310Model->zIncrBy($cid,$num);
                Game::cmd_other_flow($cid, 'club', 'huodong_310_'.$this->hd_cfg['info']['id'], array($this->uid), 42, 1, $num, $Redis310Model->zScore($cid));
            }

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
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		$Redis310Model = Master::getRedis310($this->hd_cfg['info']['id']);
        $Redis310Model->back_data();
        $Redis310Model->back_data_my($cid);
		
	}



    /**
     * 资源消耗 -- 解散帮会
     * @param $cid
     */
    public function del_club_rank($cid){
        if( self::get_state() == 1 ){
            $Redis310Model = Master::getRedis310($this->hd_cfg['info']['id']);
            $Redis310Model->del_member($cid);
        }
    }

    /**
     * 资源消耗 -- 退出帮会
     * @param $cid
     * @param $num
     */
    public function out_club_rank($cid,$num){
        if( self::get_state() == 1 ){
            $Redis310Model = Master::getRedis310($this->hd_cfg['info']['id']);
            $Redis310Model->zIncrBy($cid,-$num);
            Game::cmd_other_flow($cid, 'club', 'huodong_310_'.$this->hd_cfg['info']['id'], array($this->uid), 42, 1, $num, $Redis310Model->zScore($cid));
        }
    }

    /**
     * 资源消耗 -- 加入帮会
     * @param $cid
     * @param $num
     */
    public function in_club_rank($cid,$num){
        if( self::get_state() == 1 ){
            $Redis310Model = Master::getRedis310($this->hd_cfg['info']['id']);
            $Redis310Model->zIncrBy($cid,$num);
            Game::cmd_other_flow($cid, 'club', 'huodong_310_'.$this->hd_cfg['info']['id'], array($this->uid), 42, 1, $num, $Redis310Model->zScore($cid));
        }
    }

	
}
