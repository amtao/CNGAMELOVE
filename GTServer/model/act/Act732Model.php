<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约
 */

class Act732Model extends ActBaseModel{
    
    public $atype = 732;//活动编号

	public $comment = "伙伴邀约-饮食游戏";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "food";//返回信息 所在控制器
    
    public $_init = array(
        'heroId' => 0,//邀约伙伴
        'pics' => array(),//随机出来的牌子
        'randItems' => array(),//随机出来的道具
        'gate' => 0,//当前关卡
        'score' => 0,//积分
        'step' => 0,//步骤
        'eventId' => 0,//事件id
        'city' => 0,
        'getFood' => array(),
    );

    //开始游戏
    public function startFood($cityId,$id,$heroId,$refreshTime){
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
        $this->genPics($gamesCfg);
        $this->save();
    }

    //随机翻牌子
    private function genPics($gamesCfg){
        $this->info['pics'] = array();

        $gate = $this->info['gate']+1;
        $count = $gamesCfg['food'.$gate];
        $steps = $gamesCfg['step'.$gate];

        $itemsArr = array();
        foreach($gamesCfg['item'] as $v){
            $gameItemCfg = Game::getcfg_info("game_item",$v);
            if($gameItemCfg['type'] != 2){
                continue;
            }
            $itemsArr[$v] = $gameItemCfg;
        }
        $ids = Game::get_rand_key2($itemsArr,'prob',$count);
        $this->info['randItems'] = $ids;
        $this->info['pics'] = array_merge( $this->info['randItems'], $this->info['randItems']);
        $this->info['step'] = $steps;
        shuffle($this->info['pics']);

    }

    /**
     * 翻牌子
     * 全部消除结束之后随机一个食物保存
     */
    public function turnFood($index1,$index2){
        if( $this->info['step'] <= 0){
            Master::error(INVITE_GAME_OVER);
        }
        if($index1 == $index2){
            Master::error(INVITE_SAME_INDEX);
        }
        $gamesCfg = Game::getcfg_info("games",$this->info['eventId']);
        $isover = true;
        $pics = $this->info['pics'];
        /**
         * 食物相同时消除，步数不变
         * 不同时步数-1
         */
        if($pics[$index1] == $pics[$index2] && ($pics[$index1] == 0 || $pics[$index2] == 0)){
            Master::error(INVITE_HAS_TURN);
        }
        if($pics[$index1] == $pics[$index2]){
            $gameItemsCfg = Game::getcfg_info("game_item",$pics[$index1]);

            $Act6001Model = Master::getAct6001($this->uid);
            $jbLv = $Act6001Model->getHeroJBLv($this->info['heroId']);
            $jbCfg = Game::getcfg_info('jinban_lv', $jbLv);
            $heroRate = $jbCfg['gamebuff']/100;
            $cityRate = $gamesCfg['buff']/100;

            $Act757Model = Master::getAct757($this->uid);
            $skillRate = $Act757Model->getSkillProp(4)/100;

            $this->info['score'] += floor($gameItemsCfg['score'] * ( 1 + $heroRate + $cityRate + $skillRate));
            $pics[$index1] = 0;
            $pics[$index2] = 0;
        }else{
            $this->info['step']--;
        }

        /**
         * 判断这一回合是否结束
         * 当位置都为0 并且步数>0 当作结束
         */
        for($i = 0; $i < count($pics); $i++){
            if($pics[$i] != 0){
                $isover = false;
                break;
            }
        }
    
        $this->info['pics'] = $pics;
        if($isover && $this->info['step'] > 0 ){
            $this->info['gate']++;
            $this->info['score'] += $gamesCfg['score'.$this->info['gate']];

            $index = array_rand($this->info['randItems'],1);

            $Act733Model = Master::getAct733($this->uid);
            $Act733Model->setThings($this->info['randItems'][$index]);
            if(empty($this->info['getFood'])){
                $this->info['getFood'] = array();
            }
            array_push($this->info['getFood'],$this->info['randItems'][$index]);
            if($this->info['gate'] < 3){
                $this->genPics($gamesCfg);
            }
        }
        $this->save();
    }
    
    //结束之后领取奖励
    public function pickAward(){
        if($this->info['eventId'] == 0){
            return;
        }
        $gamesCfg = Game::getcfg_info("games",$this->info['eventId']);
        $gate = $this->info['gate'];
        for($i = 1;$i <= $gate;$i++){
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
