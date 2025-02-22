<?php 
require_once "ActBaseModel.php";
/*
 *七日庆典 
 */

class Act700Model extends ActBaseModel{
    
    public $atype = 700;//活动编号

	public $comment = "七日庆典";
	public $b_mol = "sevenCelebration";//返回信息 所在模块
    public $b_ctrl = "seveninfo";//返回信息 所在控制器
    
    public $_init = array(

    );

    public function do_init(){
        //获取七日任务配置
        $sevenTask_cfg = Game::getcfg("seven_task");
        
        $UserModel = Master::getUser($this->uid);
        $regTime = $UserModel->info['regtime'];

        $init = array(
            'regTime' => $regTime,	    //注册时间
            'score' => 0,	            //总积分
            'isPickFinalAward' => 0,    //是否领取连续七天登录奖励
            'scorePick' => array(),	    //积分领取
            'buyInfo' => array(),	    //超值购购买信息
            'sevenLogin' => array(),	//七日登录领取信息
            'seventTask' => array(),	//七日任务完成信息
            'isMail' => 0,              //是否发送过邮件
        );
        //任务档次 完成次数/领奖情况
		foreach ($sevenTask_cfg as $v){
			$init['seventTask'][$v['id']] = array('type' => $v['type'],'set' => 0,'isPick' => 0);
		}
        return $init;
    }

    public function checkNewSevenDaysTasks(){
        $sevenTask_cfg = Game::getcfg("seven_task");
                //任务档次 完成次数/领奖情况
		foreach ($sevenTask_cfg as $v){
            if(empty($this->info['seventTask'][$v['id']])){
                $this->info['seventTask'][$v['id']] = array('type' => $v['type'],'set' => 0,'isPick' => 0);
            }
		}
    }

    //获取当前开启天数
    public function getOpenDay() {
        //注册时间
        $regTime =  $this->info['regTime'];
        $regTime = strtotime(date('Ymd', $regTime));
        $days = ceil(($_SERVER['REQUEST_TIME'] - $regTime)/86400);
		return $days;
    }
    
    /*
    * 1.第八天任务次数不累加
    * 2.可以领取奖励
    * 3.不可以购买超值购礼包
    */
    public function checkIsEffect($isPick = false){
        $days = $this->getOpenDay();
        if($isPick){
            if($days >= 9){
                return 0;
            }
        }else {
            if($days >= 8){
                return 8;
            }
        }
        return $days;
    }

    //七日登录检测登陆时塞数据
    public function setSevenSign(){
        $day = $this->getOpenDay();
        if($day <= 0 || $day >= 8){
            return;
        }
        if (!isset($this->info['sevenLogin'][$day])){
            $this->info['sevenLogin'][$day] = 0;
        }
        $this->checkNewSevenDaysTasks();
        $this->save();
    }

    //七日登录签到奖励领取
    public function getSevenSignAward($signday){
        $day = $this->getOpenDay();
        if($day <= 0 || $day >= 9){
            return;
        }
        if (!isset($this->info['sevenLogin'][$signday])){
            Master::error(ACT700_NOT_SIGN);
        }
        if($this->info['sevenLogin'][$signday] == 1){
            Master::error(REWARD_IS_GET);
        }
        $this->_getSignAward($signday);
    }

    //七日签到补签
    public function supplySign($signday){

        $day = $this->getOpenDay();
        if($day <= 0 || $day >= 8){
            return;
        }
        if ($signday > $day){
            Master::error(ACT700_NOT_REACH); 
        }
        if (isset($this->info['sevenLogin'][$signday]) && $this->info['sevenLogin'][$signday] == 1){
            Master::error(ACT700_SIGN);
        }
        $this->_getSignAward($signday,true);
    }

    //获取签到获得的奖励
    private function _getSignAward($signday,$isSupply = false){
        $signCfg = Game::getcfg_info('seven_sign',$signday);
        //补签消耗
        if($isSupply){
            foreach($signCfg['repair'] as $v){
                Master::sub_item2($v);
            }
        }
        $this->info['sevenLogin'][$signday] = 1;
        Master::add_item3($signCfg['rwd']);
        $this->info['score'] += $signCfg['score'];
        $this->save();
    }

    //七日购买超值购礼包
    public function buyGift($b_day){
        $day = $this->getOpenDay();
        if($day <= 0 || $day >= 8){
            return;
        }
        if($b_day > $day){
            Master::error(ACT700_NOT_BUY_GIFT); 
        }
        if(empty($this->info['buyInfo'][$b_day])){
            $this->info['buyInfo'][$b_day] = 0;
        }
        $shopCfg = Game::getcfg_info('seven_shop',$b_day);
        if($this->info['buyInfo'][$b_day] >= $shopCfg['limit']){
            Master::error(ACT700_BUY_GIFT_MAX);
        }
        foreach($shopCfg['need'] as $v){
            Master::sub_item2($v);
        }

        Master::add_item3($shopCfg['rwd']);
        $this->info['score'] += $shopCfg['score'];
        $this->info['buyInfo'][$b_day] += 1;
        $this->save();
    }

    //检测七天是否连续登录，是否领取完全部奖励
    public function checkIsContinueLogin(){
        if(count($this->info['sevenLogin']) < 7){
            Master::error(ACT700_NOT_CONTINUE_LOGIN);
        }
        foreach($this->info['sevenLogin'] as $k => $v){
            if($v == 0){
                Master::error(ACT700_NOT_PICK_AWARD);
            }
        }
        return true;
    }

    //领取最终奖励
    public function pickFinalAward(){
        if($this->info['isPickFinalAward'] == 1){
            Master::error(REWARD_IS_GET);
        }
        //获取通用表里最终奖励 id|count|kind
        $ss = Game::getcfg_param('senven_sign');
        $fAward = explode('|', $ss);
        Master::add_item($this->uid,$fAward[2],$fAward[0],$fAward[1]);
        $this->info['isPickFinalAward'] = 1;
        $this->save();
    }

    //领取积分礼包奖励
    public function pickScoreGift($id){
        $scoreCfg = Game::getcfg_info('giftpack',$id);
        if($scoreCfg['type'] != 2){
            Master::error(ACT700_NOT_SCORE_AWARD);
        }
        if(!empty($this->info['scorePick']) && in_array($id,$this->info['scorePick'])){
            Master::error(REWARD_IS_GET);
        }
        if($this->info['score']<$scoreCfg['set']){
            Master::error(ACT700_NOT_SCORE_ENOUGH);
        }
        array_push($this->info['scorePick'],$id);
        Master::add_item3($scoreCfg['rwd']);
        $this->save();
    }

    //七日任务完成设置参数
    public function setSevenTask($type){
        foreach($this->info['seventTask'] as $taskid => $task){
            if($task['type'] == $type){
                if($type == 5){
                    $num = 0;
                    $taskCfg = Game::getcfg_info('seven_task',$taskid);
                    $level = $taskCfg['set'][1];
                    $HeroModel = Master::getHero($this->uid);
                    foreach($HeroModel->info as $k => $v){
                        if($v['level'] >= $level){
                            $num ++;
                        }
                    }
                    $this->info['seventTask'][$taskid]['set'] = $num;
                }else{
                    $Act39Model = Master::getAct39($this->uid);
                    $this->info['seventTask'][$taskid]['set'] = $Act39Model->task_num($type);
                }

            }
        }
        $this->save();
    }

    //领取任务奖励
    public function pickTaskAward($taskId){
        $day = $this->getOpenDay();
        $taskCfg = Game::getcfg_info('seven_task',$taskId);
        if($day <= 0 || $day < $taskCfg['day'] || $day >= 9){
            Master::error(ACT700_NOT_PICK_TASK);
        }
        if(!isset($this->info['seventTask'][$taskId])){
            Master::error(ACTHD_ACTIVITY_ERROR);
        }
        if($this->info['seventTask'][$taskId]['type'] == 116){
            if($this->info['seventTask'][$taskId]['set'] > $taskCfg['set'][0]){
                Master::error(DAILY_UN_COMPLETE);
            }
        }else{
            if($this->info['seventTask'][$taskId]['set'] < $taskCfg['set'][0]){
                Master::error(DAILY_UN_COMPLETE);
            }
        }
        foreach($this->info['seventTask'][$taskId] as $v){
            if($v['type'] == $taskCfg['type'] && $v['isPick'] == 1){
                Master::error(REWARD_IS_GET);
            }else {
                $this->info['seventTask'][$taskId]['isPick'] = 1;
            }
        }
        $pickCount = 1;
        $UserModel = Master::getUser($this->uid);
        $viplv = $UserModel->info['vip'];
        if($taskCfg['vip'] != 0 && $viplv >= $taskCfg['vip']){
            $pickCount = 2;
        }
        for( $i = 0; $i < $pickCount; $i++ ){
            Master::add_item3($taskCfg['rwd']);
        }
        $this->info['score'] += $taskCfg['score'];
        $this->save();
    }

    //活动结束之后发放邮件
    public function sendSevenMail(){
        $day = $this->getOpenDay();
        if ($day < 9 || $this->info['isMail'] == 1){
            return;
        }
        $UserModel = Master::getUser($this->uid);
        $viplv = $UserModel->info['vip'];
        $sendAward = array();
        foreach($this->info['seventTask'] as $taskId => $task){
            $taskCfg = Game::getcfg_info('seven_task',$taskId);
            if($task['isPick'] >= 1){
                continue;
            }
            if($task['type'] == 106 && $task['set'] > $taskCfg['set'][0]){
                continue;
            }elseif ($task['set'] < $taskCfg['set'][0]) {
                continue;
            }

            $sendCount = 1;
            if($viplv >= $taskCfg['vip']){
                $sendCount = 2;
            }
            foreach($taskCfg['rwd'] as $items){
                if(empty($sendAward[$items['itemid']])){
                    $sendAward[$items['itemid']] = array('kind'=>$items['kind'],'id'=>$items['itemid'],'count'=>$items['count']*$sendCount);
                }else {
                    $sendAward[$items['itemid']]['count'] += $items['count']*$sendCount;
                }
            }
            $this->info['seventTask'][$taskId]['isPick'] = 1;
        }
        $sendAwardItems = array();
        if (!empty($sendAward)) {
            foreach ($sendAward as $key => $value) {
                $sendAwardItems[] = $value;
            }

            $mailModel = Master::getMail($this->uid);
            $mailModel->sendMail($this->uid, MAIL_SEVEN_CELEBRATION_TITLE, MAIL_SEVEN_CELEBRATION_CONTENT, 1, $sendAwardItems);
            $mailModel->destroy();
        }
        $this->info['isMail'] = 1;
        $this->save();
        
    }

    public function make_out(){
        $day = $this->getOpenDay();

        $this->outf['openday'] = $day;
        $this->outf['endtime'] =  strtotime(date('Ymd', $this->info['regTime'])) +7*24*3600;
        $this->outf['showtime'] =  strtotime(date('Ymd', $this->info['regTime'])) +8*24*3600;
        
        $this->outf['score'] = isset($this->info['score']) ? $this->info['score'] : 0;
        $this->outf['isPickFinalAward'] = isset($this->info['isPickFinalAward']) ? $this->info['isPickFinalAward'] : 0;
        $this->outf['scorePick'] = isset($this->info['scorePick']) ? $this->info['scorePick'] : array();
        $this->outf['buyInfo'] = isset($this->info['buyInfo']) ? $this->info['buyInfo'] : array();
        $this->outf['sevenLogin'] = isset($this->info['sevenLogin']) ? $this->info['sevenLogin'] : array();
        $this->outf['seventTask'] = isset($this->info['seventTask']) ? $this->info['seventTask'] : array();
    }
}
