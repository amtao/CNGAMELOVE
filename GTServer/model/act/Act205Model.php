<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动205
 */
class Act205Model extends ActHDBaseModel
{
	public $atype = 205;//活动编号
	public $comment = "限时奖励-亲密度涨幅";
	public $b_ctrl = "qinmi";//子类配置
	public $hd_id = 'huodong_205';//活动配置文件关键字
    protected $_rank_id = 205;
	
	
	/*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
	public $_init =  array(
		'cons' => 0,  //已消耗(完成)量
		'rwd' => 0,  //已领取的档次
		'num' => 0, //存放活动开启时候玩家的亲密度
		'id' => 0, //活动id
	);
	
	
	/*
	 * 初始化函数
	 */
	public function do_save($num)
	{
		//在活动中
		if( parent::get_state() == 1){
			
			if($this->info['id'] != $this->hd_cfg['info']['id']){
				$this->info['num'] = $num;
				$this->info['id'] = $this->hd_cfg['info']['id'];
			}
			
			//当前势力
			$Redis3Model = Master::getRedis3();
			$score = $Redis3Model->zScore($this->uid);
			$this->info['cons'] = $score - $this->info['num'];
			$this->save();

            //保存到排行榜中
            if (!empty($this->_rank_id)) {
                $RedisModel = Master::getRedis($this->_rank_id, $this->hd_cfg['info']['id']);
                $RedisModel->zAdd($this->uid, $this->info['cons']);
            }
		}
	}
	
}
