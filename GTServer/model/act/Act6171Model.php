<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6171
 */
class Act6171Model extends ActHDBaseModel
{
	public $atype = 6171;//活动编号
	public $comment = "限时奖励-祈福次数";
	public $b_ctrl = "qifu";//子类配置
	public $hd_id = 'huodong_6171';//活动配置文件关键字
    protected $_rank_id = 6171;


    /**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
		$news = 0; //不可领取
		if( self::get_state() == 0){
			$news = 0;
		}else{
			//奖励信息
			$rinfo = $this->hd_cfg['rwd'][$this->info['rwd']+1];
			if(!empty($rinfo) && $this->info['cons'] >= $rinfo['need']){
				$news = 1; //可以领取
			}
		}
		return $news;
	}
}
