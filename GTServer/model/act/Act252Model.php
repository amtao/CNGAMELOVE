<?php
require_once "ActHDBaseModel.php";

/*
 * 活动252
 */
class Act252Model extends ActHDBaseModel
{
	public $atype = 252;//活动编号
	public $comment = "势力冲榜";
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "shili";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_252';//活动配置文件关键字
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'num' => 0, //存放活动开启时候玩家的势力
		'id' => 0, //活动id
		'exchange'  => array(),  //兑换信息
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
	 * 势力分数排行保存
	 * @param int $num  通过的势力数
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
			//当前势力
			$Redis1Model = Master::getRedis1();
			$score = $Redis1Model->zScore($this->uid);
			//保存到排行榜中 势力变小的不管
			if($score - $this->info['num'] > 0){
				$Redis103Model = Master::getRedis103($this->hd_cfg['info']['id']);
				$Redis103Model->zAdd($this->uid,$score - $this->info['num']);
			}
		}
	}

	/**
     * 兑换
     * $id
     */
    public function exchange($id = 0){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        // $buy_count = floor($id / 10000);
        // if ($buy_count <= 0)return;
        // $id = $id % 10000;
        // if ($buy_count == 0)Master::error();
        $buy_count = 1;
        $exchangeList = $this->hd_cfg['exchange'];

        foreach($exchangeList as $rwd){
            if ($rwd['id'] == $id){
                $c = empty($this->info['exchange'][$id])?0:$this->info['exchange'][$id];
                if ($c + $buy_count > $rwd['count'] && $rwd['count'] != 0){
                    Master::error();
                }
                $item = $rwd['items'][0];
                // $ItemModel = Master::getItem($this->uid);
                // $ItemModel->sub_item($item['id'], $item['count'] * $buy_count);
                Master::sub_item($this->uid,KIND_ITEM,$item['id'],$item['count']);
                $this->info['exchange'][$id] = $c + $buy_count;
                $item = $rwd['items'][1];
                $item['count'] = $item['count'] * $buy_count;
                Master::add_item2($item);
                $this->save();
                break;
            }
        }
        $exchange = $this->back_data_exchange();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl.'exchange',$exchange);
    }

	    /*
     * 兑换列表
     * */
    public function back_data_exchange() {
        //构造输出
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $info = $this->info['exchange'];
        $exchange = $this->hd_cfg["exchange"];
        $exchangeTime = $this->hd_cfg["exchangeTime"];
        $idList = $exchangeTime[0]["idList"];
        foreach ($exchangeTime as $key => $value) {

            if ($_SERVER['REQUEST_TIME'] >= strtotime($value['startTime']) && $_SERVER['REQUEST_TIME'] <= strtotime($value['endTime'])) {
                $idList = $value["idList"];
                break;
            }
        }

        $list = array();
        foreach ($exchange as $key => $value) {

            if (in_array($value["id"], $idList)) {
                $value["isPay"] = 1;

                $list[$value["id"]] = $value;
            }
        }

        $giftBag = Game::getGiftBagCfg();
        foreach ($giftBag as $key => $value) {
            if ($value["actid"] == $this->atype && in_array($value["id"], $idList) ) {

                $value["isPay"] = 2;
                $list[$value["id"]] = $value;
            }
        }

        $newExchange = array();
        foreach ($idList as $k => $v) {

            if (isset($list[$v])) {
                array_push($newExchange, $list[$v]);
            }
        }

        $rwds = array();
        foreach($newExchange as $rwd){
            $rwd['buy'] = empty($info[$rwd['id']])?0:$info[$rwd['id']];
            $rwds[] = $rwd;
        }
        return $rwds;
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
		unset($hd_cfg['exchangeTime']);
		unset($hd_cfg['exchange']);
		$this->outf['cfg'] = $hd_cfg;
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data_hd(){
		//排行信息
		$Redis103Model = Master::getRedis103($this->hd_cfg['info']['id']);
		$Redis103Model->back_data();
		$Redis103Model->back_data_my($this->uid);

		//配置信息
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
		
	}

    /*
     * 返回活动信息
     */
    public function back_data_hd313(){

        if( parent::get_state() == 0){
            Master::error(KUAYAMEN_HD_YX_ERROE);
        }
        //排行信息
        $Redis103Model = Master::getRedis103($this->hd_cfg['info']['id']);
        $Redis103Model->back_data();
        $Redis103Model->back_data_my($this->uid);

    }


    /**
     * 势力分数排行保存
     * @param $num  通过的势力数
     */
    public function do_debug($num){
        //在活动中
        if( parent::get_state() == 1){
            //初始化
            $this->info['num'] = $num;
            $this->info['id'] = $this->hd_cfg['info']['id'];
            $this->save();
            //当前势力
            $Redis1Model = Master::getRedis1();
            $score = $Redis1Model->zScore($this->uid);
            //保存到排行榜中
            $Redis103Model = Master::getRedis103($this->hd_cfg['info']['id']);
            $Redis103Model->zAdd($this->uid,$score - $this->info['num']);

        }
    }

	
}
