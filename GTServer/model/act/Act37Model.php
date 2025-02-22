<?php
require_once "ActBaseModel.php";
/*
 * 签到
 */
class Act37Model extends ActBaseModel
{
	public $atype = 37;//活动编号
	
	public $comment = "签到";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "qiandao";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'days' => 0,//已领取的登录奖励天数
        'lastMonday' => 0,//最后一次操作周一礼包时间
	);

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
        $this->info['days']; // 'days' => 0,//已领取的登录奖励天数

        $UserModel = Master::getUser($this->uid);
        $regTime = $UserModel->info['regtime'];

        $regTime = strtotime(date('Ymd', $regTime));
		$days = ceil(($_SERVER['REQUEST_TIME'] - $regTime)/86400);

        if ($days > $this->info['days']) {
            $qiandao = 0;
            if(!empty($this->info['time']) && Game::is_today($this->info['time'])){
            	$qiandao = 1;
            }
        } else {
            $qiandao = 1;
        }

        $this->outf = array(
            'days' => $qiandao == 1 ?$this->info['days'] : $this->info['days']+1,//签到第几天
            'qiandao' => $qiandao,//签到信息-0-未签到,1-签到
        );
	}

	private function actQiandao(){
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6012');
        if (!empty($hd_cfg)){
            $Act6012Model = Master::getAct6012($this->uid);
            $Act6012Model -> get_rwd();
        }
    }

	/*
	 * 领取任务奖励
	 */
	public function rwd(){
        if ($this->outf['qiandao'] != 0){
            Master::error(SIGN_IN_COMPLETE);
        }

        $this->actQiandao();

        $this->info['days'] += 1;
        $this->info['time'] = $_SERVER['REQUEST_TIME'];
        $this->save();

        //发放签到奖励
        $qd_day = $this->info['days']%30;
        if($qd_day == 0){
        	$qd_day = 30; 
        }
        $qiandao_cfg_info = Game::getcfg_info("qiandao",intval($qd_day));
        $UserModel =Master::getUser($this->uid);
        $vip = $UserModel->info['vip'];
        $awardBonus = 1;
        if($qiandao_cfg_info['vip'] > 0 && $vip >= $qiandao_cfg_info['vip']){
            $awardBonus = 2;
        }
        for($i = 0;$i < $awardBonus;$i++){
            foreach($qiandao_cfg_info['qiandaoRwd'] as $v)
            {
                Master::add_item2($v);
            }
        }
	}

	private function getMondayState(){
	    $day_0 = Game::day_0();
	    $time = $this->info['lastMonday']?$this->info['lastMonday']:0;
	    if ($time > $day_0){
	        return 2;
        }

        $wday  =  date("w");//今天周几
        $wday = $wday == 0? 7:$wday;
        $mons = Game::getcfg("monday");
	    foreach ($mons as $m){
	        if ($wday == $m['dayid']  && $this->info['lastMonday'] <= $day_0){
	            return 1;
            }
        }

        return 0;
    }

    /*
	 * 返回活动信息
	 */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
        Master::back_data($this->uid,$this->b_mol,'mGift',array('isrwd'=>$this->getMondayState()));

        Common::loadModel('OrderModel');
        $money = OrderModel::getMyTotalPay($this->uid);
        Master::back_data($this->uid,$this->b_mol,'money',array('totalMoney'=> $money));
    }

	public function monday(){
        $state = $this->getMondayState();
        switch ($state){
            case 0:
                Master::error(SIGN_MONDAY_UNGIFT);
                break;
            case 2:
                Master::error(SIGN_MONDAY_COM);
                break;
        }

        $wday  =  date("w");//今天周几
        $wday = $wday == 0? 7:$wday;
        $mons = Game::getcfg("monday");
        $UserModel =Master::getUser($this->uid);
        $vip = $UserModel->info['vip'];
        $awardBonus = 1;
        $day_0 = Game::day_0();
        foreach ($mons as $m){
            if ($wday == $m['dayid']  && $this->info['lastMonday'] <= $day_0){
                if($m['vip'] > 0 && $vip >= $m['vip']){
                    $awardBonus = 2;
                }
                for($i = 0;$i < $awardBonus;$i++){
                    Master::add_item3($m['dayRwd']);
                }
                Game::cmd_other_flow($this->uid,"fuli","monday",array("id"=>$wday),10002,1,1,1);
                break;
            }
        }

        $this->info['lastMonday'] = Game::get_now();
        $this->save();
    }
}
















