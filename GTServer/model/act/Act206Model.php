<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动206
 */
class Act206Model extends ActHDBaseModel
{
	public $atype = 206;//活动编号
	public $comment = "限时奖励-势力涨幅";
	public $b_ctrl = "shili";//子类配置
	public $hd_id = 'huodong_206';//活动配置文件关键字
    protected $_rank_id = 206;
	
	/*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
	public $_init =  array(
		'cons' => 0,  //已消耗(完成)量
		'rwd' => 0,  //已领取的档次
		'num' => 0, //存放活动开启时候玩家的势力
		'id' => 0, //活动id
	);
	
	
	/*
	 * 初始化函数
	 */
	public function do_save($num)
	{
		//在活动中
		if( parent::get_state() == 1){
			//初始化
			if($this->info['id'] != $this->hd_cfg['info']['id']){
				$this->info['num'] = $num;
				$this->info['id'] = $this->hd_cfg['info']['id'];
			}
			//当前势力
			$Redis1Model = Master::getRedis1();
			$score = $Redis1Model->zScore($this->uid);
			$this->info['cons'] = $score - $this->info['num'];
			$this->save();

            //保存到排行榜中
            if (!empty($this->_rank_id)) {
                $RedisModel = Master::getRedis($this->_rank_id, $this->hd_cfg['info']['id']);
                $RedisModel->zAdd($this->uid, $this->info['cons']);
            }
		}
	}


    /*
     * 此函数 不删除了
     * 用于 bug处理
     * 正常逻辑 不使用该函数
     * ps:   用到该函数,准备等死
     */
    public function do_debug($num){

        //在活动中
        if( parent::get_state() == 1){
            //初始化
            $this->info['num'] = $num;
            $this->info['id'] = $this->hd_cfg['info']['id'];

            //当前势力
            $Redis1Model = Master::getRedis1();
            $score = $Redis1Model->zScore($this->uid);
            $this->info['cons'] = $score - $this->info['num'];
            $this->save();

        }

    }
	
}
