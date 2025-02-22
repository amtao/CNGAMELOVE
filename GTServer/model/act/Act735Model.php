<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约
 */

class Act735Model extends ActBaseModel{
    
    public $atype = 735;//活动编号

	public $comment = "伙伴邀约-钓鱼游戏";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "fish";//返回信息 所在控制器
    
    public $_init = array(
        'heroId' => 0,//邀约伙伴
        'gameCount' => 0,//进行次数
        'score' => 0,//积分
        'eventId' => 0,//事件id
        'getFish' => array(),//获得的鱼
        'bait' => 0,//当前使用的鱼饵
        'fakeFish' => 0,//假鱼
        'fakeYur' => 0,
        'isPickYur' => 0,
        'city' => 0,
    );

    /**
     *  开始游戏 
     *  判断是否为限时
     *  超出限时时间报错
     */
    public function startFish($cityId,$id,$heroId,$refreshTime){
        $gamesCfg = Game::getcfg_info("games", $id);
        if($gamesCfg['start'] != 0){
            $Act730Model = Master::getAct730($this->uid);
            if(empty($Act730Model->info['joinLimitEvent'][$cityId][$id])){
                $Act730Model->info['joinLimitEvent'][$cityId][$id] = 0;
            }
            if($Act730Model->info['joinLimitEvent'][$cityId][$id] >= 1){
                Master::error(INVITE_ONLY_ONE);
            }
            $now = Game::get_now();
            if( $refreshTime + $gamesCfg['start'] < $now ){
                Master::error(INVITE_TIME_OUT);
            }
            $Act730Model->info['joinLimitEvent'][$cityId][$id] += 1;
            $Act730Model->save();
        }
        $this->info['eventId'] = $id;
        $this->info['city'] = $cityId;
        $this->info['heroId'] = $heroId;
        $this->save();
    }

    public function getRandFish($water){
        if($this->info['bait'] == 0){
            Master::error("大人请先去选择鱼饵");
        }
        $gamesCfg = Game::getcfg_info("games", $this->info['eventId']);
        $tempArr = array();
        /**
         * 将同一水域的鱼取出
         * 根据选择的鱼饵拿到加成
         * 随机一个获取到的鱼
         */
        foreach($gamesCfg['item'] as $v){
            $gameitemCfg = Game::getcfg_info("game_item",$v);
            $waterArr = explode("|",$gameitemCfg['water']);
            if(in_array($water,$waterArr)){
                $yuerCfg = Game::getcfg_info("yuer",$this->info['bait']);
                $prob = $yuerCfg['prop'.$gameitemCfg['star']]/100;
                $gameitemCfg['prob'] = ceil($gameitemCfg['prob']*$prob);
                $tempArr[$gameitemCfg['id']] = $gameitemCfg;
            }
            $fishId = Game::get_rand_key1($tempArr,'prob');
        }
        $this->info['fakeFish'] = $fishId;
        $this->save();
    }
    /**
     * isSuccess 是否钓鱼成功
     * water 水域
    */
    public function fishing($isSuccess){
        //30000 钓鱼失败时获取鱼骨头
        $fishId = 30000;
        $gamesCfg = Game::getcfg_info("games", $this->info['eventId']);
        
        //钓鱼成功
        if($isSuccess == 1 && $this->info['fakeFish'] > 0){
            $fishId = $this->info['fakeFish'];
        }
        $Act733Model = Master::getAct733($this->uid);
        $Act733Model->setThings($fishId);
        array_push($this->info['getFish'],$fishId);
        $this->info['gameCount']++;
        /**
         * 计算获取到的积分公式
         * 总积分=（游戏中物品获得积分+当前回合完成奖励积分）*（1+积分加成）
         * 积分加成=男主羁绊加成+城市活动加成
         */
        $gameitemCfg = Game::getcfg_info("game_item",$fishId);
        
        $Act6001Model = Master::getAct6001($this->uid);
        $jbLv = $Act6001Model->getHeroJBLv($this->info['heroId']);
        $jbCfg = Game::getcfg_info('jinban_lv', $jbLv);
        $heroRate = $jbCfg['gamebuff']/100;
        $cityRate = $gamesCfg['buff']/100;

        $Act757Model = Master::getAct757($this->uid);
        $skillRate = $Act757Model->getSkillProp(3)/100;

        $this->info['score'] += floor($gameitemCfg['score'] * ( 1 + $heroRate + $cityRate + $skillRate));

        $this->info['bait'] = 0;
        $this->info['fakeFish'] = 0;
        $this->save();
    }

    public function getRandYur(){
        if($this->info['isPickYur'] == 1){
            return;
        }
        $Act6001Model = Master::getAct6001($this->uid);
        $jbLv = $Act6001Model->getHeroJBLv($this->info['heroId']);
        $jbCfg = Game::getcfg_info('jinban_lv', $jbLv);
        $index = Game::get_rand_key1($jbCfg['prop_yuer'],'prop');
        $yuerId = $jbCfg['prop_yuer'][$index]['id'];
        $this->info['fakeYur'] = $yuerId;
        $this->save();
    }

    public function pickRandYur(){
        if($this->info['isPickYur'] == 1){
            return;
        }
        if($this->info['fakeYur'] <= 0){
            return;
        }
        $yurCfg = Game::getcfg_info("yuer",$this->info['fakeYur']);
        Master::add_item($this->uid,KIND_ITEM,$this->info['fakeYur'],1);
        $this->info['isPickYur'] = 1;
        $this->save();
    }

    //消耗的鱼饵
    public function consumeBait($id){
        $yuerCfg = Game::getcfg_info("yuer",$id);
        Master::sub_item($this->uid,KIND_ITEM,$id,1);
        $this->info['bait'] = $id;
        $this->save();

    }

    //购买的鱼饵
    public function buyBait($id,$num){
        $yuerCfg = Game::getcfg_info("yuer",$id);
        foreach($yuerCfg['price'] as $items){
            Master::sub_item($this->uid,$items['kind'],$items['id'],$items['count']*$num);
        }
        //鱼饵永久拥有 背包中显示
        Master::add_item($this->uid,KIND_ITEM,$id,$num);
    }

    //结束之后领取奖励
    public function pickAward(){
        if($this->info['eventId'] == 0){
            return;
        }
        $gamesCfg = Game::getcfg_info("games",$this->info['eventId']);
        $count = $this->info['gameCount'];
        for($i = 1;$i <= $count;$i++){
            Master::add_item3($gamesCfg['rwd'.$i]);
        }
        $gameRwdCfg = Game::getcfg_info("game_rwd",$gamesCfg['type']);
        $myScore = $this->info['score'];
        $awardArr = array();
        foreach($gameRwdCfg as $v){
            if($myScore < $v['score']){
                break;
            }
            if($myScore >= $v['score']){
                $awardArr = $v['rwd'];
            }
        }
        foreach($awardArr as $_items){
            Master::add_item($this->uid,$_items['kind'],$_items['id'],$_items['count']);
        }
        $this->clearGame();
    }

    public function clearGame(){
        $this->info = $this->_init;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
