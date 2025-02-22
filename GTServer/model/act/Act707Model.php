<?php

require_once "ActBaseModel.php";
/**
 * 行商--新版日常
 */
class Act707Model extends ActBaseModel{
    public $atype = 707;

    public $comment = "行商-信息";
    public $b_mol = "business";//返回信息 所在模块
    public $b_ctrl = "info";//返回信息 所在控制器

    public $_init = array(
        /**
         * 今日行商次数
         * 金叶子个数
         * 商票个数
         * 当前行商的城市
         */
        'goldLeaf' => 0,            //金叶子
        'AgTicket' => 0,            //商票
        'currentCity' => 1,         //当前所在城市
        'isStart' => 0,
        'unlockCity' => array(),
        'businessManId' => 0,
        'initAgTicket' => 0, //初始值
        'initgoldLeaf' => 0, //初始商票
    );
    //开始-消耗次数
    //初始默认在长安城 不需要随机
    public function start($chooseArr){
        if(empty($chooseArr)){
            Master::error(BUSINESS_BAOWU_MUST_CHOOSE_ONE);
        }
        if($this->info['isStart'] == 1){
            Master::error(BUSINESS_IS_START);
        }
        //行商免费次数
        $freeTime = Game::getcfg_param('xingshang_freetime');
        $Act710Model = Master::getAct710($this->uid);
        $consumeCount = $Act710Model->info['consumeBusinessCount'];
        $buyCount = $Act710Model->info['buyBusinessCount'];
        $remainCount = $freeTime + $buyCount-$consumeCount;
        if($remainCount <= 0){
            Master::error(BUSINESS_TIMES_MAX);
        }
        $Act710Model->info['consumeBusinessCount'] += 1;
        
        $this->caculateGetResource($chooseArr);
        $this->info['initAgTicket'] = $this->info['AgTicket'];
        $this->info['initgoldLeaf'] = $this->info['goldLeaf'];
        $Act711Model = Master::getAct711($this->uid);
        $Act711Model->info['startCount'] += 1;
        $Act711Model->save();
        $Act710Model->save();
        $this->info['isStart'] = 1;
        $this->save();
    }

    //去下一个城市 消耗商票
    public function nextTravel($cityId){
        if($this->info['AgTicket'] <= 0){
            Master::error(BUSINESS_TICKEY_NOT_ENOUGH);
        }
        if(!in_array($cityId,$this->info['unlockCity'])){
            Master::error(BUSINESS_CITY_UNLOCK);
        }
        $this->info['AgTicket'] -= 1;
        $this->info['currentCity'] = $cityId;
        $this->save();
        $Act709Model = Master::getAct709($this->uid);
        $Act709Model->info['buyTotal'] = 0;
        $Act709Model->info['saleTotal'] = 0;
        $Act709Model->save();

        Game::cmd_other_flow($this->uid,"business","nextTravel",array("city"=>$cityId),10001,995,1,$this->info['AgTicket']);
    }

    /**
     * 随机城市（获取信息的时候随机，随机商人id）
     * 随机城市取消长安 默认给1
     */
    public function randCity(){
        if(!empty($this->info['unlockCity']) && $this->info['businessManId'] != 0){
        
        }else{
            $chengshiCfg = Game::getcfg('xs_chengshi');
            $UserModel = Master::getUser($this->uid);
            $level = $UserModel->info['level'];
            $tempArr = array();
            foreach($chengshiCfg as $v){
                if($v['type'] == 1 && $level >= $v['set']){
                    $tempArr[$v['id']] = $v;
                }
            }
            $cityArr = array();
            $maxCount = Game::getcfg_param('xingshangchengchishu');
            if(count($tempArr) <= $maxCount){
                $cityArr = array_keys($tempArr);
            }else{
                while(count($cityArr) < 9){
                    $cityId = Game::get_rand_key1($tempArr,'rate');
                    if(!in_array($cityId,$cityArr)){
                        array_push($cityArr,$cityId);
                    }
                    unset($tempArr[$cityId]);
                }
            }
            
            $this->info['unlockCity'] = $cityArr;
            //长安城默认给到玩家
            array_push($this->info['unlockCity'],1);
            $index = rand(0,count($this->info['unlockCity'])-1);
           $this->info['businessManId'] = $this->info['unlockCity'][$index];
        }
        $this->save();
    }

    //计算获得商票金叶子
    public function caculateGetResource($chooseArr){
        $sumTicket = 0;
        $sumGoldLeaf = 0;
        $chengshiCfg = Game::getcfg_info('xs_chengshi',$this->info['businessManId']);
        foreach($chooseArr as $baowuId){
            $baowuModel = Master::getBaowu($this->uid);
            $info = $baowuModel->getBaowuInfo($baowuId);
            if(empty($info)){
                Master::error(NO_BAOWU);
            }
            if(in_array($baowuId,$chengshiCfg['qizhen'])){
                $baowuCfg = Game::getcfg_info('baowu',$baowuId);
                $sumTicket += $baowuCfg['quality']-1;
            }
            $sumGoldLeaf += $info['star']*1000;
        }
        $this->getResource($sumTicket,$sumGoldLeaf);
    }

    //获取初始商票
    public function getResource($sumTicket,$sumGoldLeaf){
        $baseTicket = Game::getcfg_param('xingshang_shangpiaochushi');
        $baseLeaf = Game::getcfg_param('xingshang_jinyezichushi');
        $this->info['goldLeaf'] = $baseLeaf + $sumGoldLeaf;
        $this->info['AgTicket'] = $baseTicket + $sumTicket;
    }

    //清除数据
    public function remove_data(){
        // $this->info['goldLeaf'] = 0;
        // $this->info['AgTicket'] = 0;
        // $this->info['currentCity'] = 1;
        // $this->info['isStart'] = 0;
        // $this->info['unlockCity'] = array();
        $this->info = $this->_init;
        $this->save();
    }

    //行商结束 提交订单-领取奖励
    public function pickAward(){
        $xsJiangLiCfg = Game::getcfg('xs_jiangli');
        $cha = $this->info['goldLeaf'] - $this->info['initgoldLeaf'];
        $count = 0;
        if($cha <= 0){
            // Master::error(BUSINESS_AGTICKET_NOT_ENOUGH);
        }else{
            $finalArr = array();
            foreach($xsJiangLiCfg as $v){
                if($cha < $v['set'] ){
                    break;
                }
                $finalArr[] = $v;
                $count++;
            }
            if(empty($finalArr)){
                Master::error_msg(BUSINESS_GOLD_LEAF_NOT_ENOUGH);
            }
            $resultArr = array();
            foreach($finalArr as $v) {
                foreach($v['rwd'] as $items){
                    if(empty($resultArr[$items['id']][$items['kind']])){
                        $resultArr[$items['id']][$items['kind']] = $items['count'];
                    }else{
                        $resultArr[$items['id']][$items['kind']] += $items['count'];
                    }
                }
            }
            foreach($resultArr as $key => $value){
                foreach($value as $k=>$v){
                    Master::add_item($this->uid,$k,$key,$v);
                }
            }
        }
        if($count > 0){
            Game::cmd_other_flow($this->uid,"business","pickAwardGear",array("gear"=>$count),10005,1,1,1);
        }
        $this->remove_data();
        $Act708Model = Master::getAct708($this->uid);
        $Act709Model = Master::getAct709($this->uid);
        $Act708Model->remove_data();
        $Act709Model->remove_data();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}
