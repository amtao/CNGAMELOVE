<?php
require_once "ActHDBaseModel.php";

/*
 * 活动272
 */
class Act6015Model extends ActHDBaseModel
{
	public $atype = 6015;//活动编号
	public $comment = "抢汤圆";
	public $b_mol = "tangyuan";//返回信息 所在模块
	public $b_ctrl = "info";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_6015';//活动配置文件关键字
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'rwd' => 0,
        'count' => 0,
        'lastTime' => 0,
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

	public function resetTime(){
        if ($this->info['lastTime'] < Game::day_0()){
            $this->info['lastTime'] = Game::get_now();
            $this->info['rwd'] = 0;
            $this->_save();
        }
    }

	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
	    $this->resetTime();
	    $news = 0;
        if( parent::get_state() == 0){
            return $news;
        }
        $hd_cfg = $this->hd_cfg;
        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = null;
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        $Sev6015Model = Master::getSev6015($this->hd_cfg['info']['id']);
        $need = $curData['need'];

        if (!empty($curData) && $this->info['rwd'] < $curData['need'] &&
            $Sev6015Model->info['hits'][$need] < $curData['all']){
            $news = 1;
        }
        return $news;
	}
	
	/**
	 * 获得奖励
	 * $id 兑换的门客id
	 */
	public function get_rwd($count){
		if( parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE.__LINE__);
		}
        if( parent::get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        $hd_cfg = $this->hd_cfg;
        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = null;
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        if (!empty($curData) && $this->info['rwd'] < $curData['need']){
            $this->info['rwd'] = $curData['need'];
            $item = $curData['items'][0];
            $min = $item['min'] + min(floor($item['max']/10), $count);
            $max = $item['max'] + min(floor($item['max']/10), $count);
            $r = rand($min, $max);

            $Sev6015Model = Master::getSev6015($this->hd_cfg['info']['id']);
            $r = $Sev6015Model->hit($r);

            if ($r > 0){
                Master::add_item($this->uid, KIND_ITEM, $item['id'], $r);
                $this->info['count'] = $this->info['count'] + $r;

                $Redis6015Model = Master::getRedis6015($this->hd_cfg['info']['id']);
                $Redis6015Model->zAdd($this->uid, $this->info['count']);
                if ($r >= $item['max'] && $this->hd_cfg['info']['hdtype']!=2){
                    $Sev6012Model = Master::getSev6012();
                    $msg = "#tangyuan#::".$r;
                    $Sev6012Model->add_msg($this->uid, $msg, 3);
                }
                $this->save();
                $this->back_data_hd();
            }
            else {
                Master::error(TANG_YUAN_QIANG_WAN);
            }
        }
        else {
            if (empty($curData))
                Master::error(TANG_YUAN_WEI_KAI_QI);
            else
                Master::error(TANG_YUAN_JIN_RI_YI_QIANG);
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

        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;

        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
	}

	private function getBase(){
	    $d = array('rwd'=>$this->info['rwd'], 'count'=>$this->info['count']);
	    $Sev6015Model = Master::getSev6015($this->hd_cfg['info']['id']);
	    $d['damage'] = array();
	    foreach ($Sev6015Model->info['hits'] as $k => $v){
            $d['damage'][] = array('id'=>$k, 'count'=>$v);
        }
	    return $d;
    }
	
}
