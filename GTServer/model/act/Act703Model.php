<?php

require_once "ActBaseModel.php";

/*
*赴约 
*/

class Act703Model extends ActBaseModel{
    
    public $atype = 703;//活动编号

    public $comment = "赴约";
    public $b_mol = "fuyue";//返回信息 所在模块
    public $b_ctrl = "fuyueInfo";//返回信息 所在控制器
    
    public $_init = array(
        'themeId' => 0,                 //随机主题
        'randStoryIds' => array(),      //随机故事段
        'chooseInfo' => array(),        //选择信息（客户端传过来直接保存）
        'usefreeCount' => 0,            //每天的免费次数
        'randSmallStoryId' => 0,        //随机故事id
        'buyCount' => 0,                //今日购买次数
    );

    //随机主题 每天刷新（每个玩家不同）
    private function randTheme(){
        $zhutiCfg = Game::getcfg('zhu_ti');
        $themeId = Game::get_rand_key1($zhutiCfg,'weight');
        if($this->info['themeId'] == 0){
            $this->info['themeId'] = $themeId;
            $this->save();
        }
    }

    //先随机总故事
    public function randZongGuShi($storyChoose){

        // $this->removeData();
		// $Act705Model = Master::getAct705($this->uid);
		// $Act705Model->removeData();

        if(!empty($this->info['randStoryIds'])){
            Master::error(TODAY_HAS_RAND);
        }

        //优先使用免费
        $freeCount = Game::getcfg_param('fuyue_free');
        $totalCount = $freeCount + $this->info['buyCount'];

        if($totalCount - $this->info['usefreeCount'] <= 0){
            Master::error(FUYUE_TIMES_USE_MAX);
        }

        if($this->info['themeId'] == 0){
            $this->randTheme();
        }

        //根据主题 获取信物 奇珍数量
        $zhutiCfg = Game::getcfg_info('zhu_ti',$this->info['themeId']);
        
        //检测传过来的物品是否拥有 && 检测必选是否足够
        if(!$this->checkIsHave($storyChoose,$zhutiCfg['xinwu_num'],$zhutiCfg['qizhen_num'])){
            Master::error(NOT_HAVE_THE_THING);
        }
        
        //根据势力计算故事需要的积分值
        $SumShiLi = $this->CaculateShili($storyChoose,$zhutiCfg['wupin']);
        $SumShiLi = intval($SumShiLi);
    
        //获取具体随机到那个故事
        $zgushiCfg = Game::getcfg_info('zong_gu_shi',$this->info['themeId']);
        $chooseArr = array();

        foreach($zgushiCfg as $_gushi){
            if($_gushi['pingfen_min'] <= $SumShiLi && $_gushi['hero_id'] == $storyChoose['heroId']){
                $chooseArr[$_gushi['id']] = $_gushi;
            }
        }
        if(empty($chooseArr)){
            Master::error(SHILI_SCORE_NOT_ENOUGH);
        }
        
        //根据权重随机到最终故事
        $finalStoryId = Game::get_rand_key1($chooseArr,'weight');
        $this->info['randSmallStoryId'] = $finalStoryId;
        
        $this->info['usefreeCount']++;    
        $this->info['chooseInfo'] = $storyChoose;
        
        $this->randStory($SumShiLi,$zgushiCfg,$finalStoryId,$storyChoose);

    }

    //随机故事
    public function randStory($SumShiLi,$zgushiCfg,$finalStoryId,$storyChoose){
        $finalStory = $zgushiCfg[$finalStoryId];
        //开头不需要随机
        array_push($this->info['randStoryIds'],$finalStory['start_id']);

        //根据必选id先判断 不符合根据随机id
        foreach($finalStory['gushi_id'] as $k => $requiredId){
            $resultArr = array();
            foreach($requiredId as $id){
                $gushiCfg = Game::getcfg_info('gushi',$id);
                if($this->checkIsMeetConditions($gushiCfg['N_condi'],$storyChoose)){
                    $resultArr[$id] = $gushiCfg;
                }
            }
            if(!empty($resultArr)){
                $storyId = Game::get_rand_key1($resultArr,'weight');
                array_push($this->info['randStoryIds'],$storyId);
            }
            if(empty($requiredId) || empty($resultArr)){
                $pStoryArr = array();
                foreach($finalStory['suiji_id'][$k] as $v){
                    $gushiCfg = Game::getcfg_info('gushi',$v);
                    if($SumShiLi >= $gushiCfg['pingfen_min']){
                        $pStoryArr[$v] = $gushiCfg;    
                    }
                }
                //随机中间不满足必要条件的id
                $pStoryId = Game::get_rand_key1($pStoryArr,'weight');
                array_push($this->info['randStoryIds'],$pStoryId);
            }
        }
        $this->save();
    }

    //检测传过来的物品是否拥有 && 检测必选是否足够
    public function checkIsHave($storyChoose,$tokenNum,$baowuNum){
        $heroCount = 0;
        $totalBaowu = 0;
        $totalToken = 0;
        $cardCount = 0;
        foreach($storyChoose as $key=>$value){
            if(!is_array($value)){
                if($value == 0){
                    continue;
                }
            }
            switch($key){
                case "heroId":
                    $HeroModel = Master::getHero($this->uid);
                    if(!$HeroModel->check_info($value)){
                        return false;
                    }
                    $heroCount++;
                    break;
                case "token":
                case "token1":
                    $Act2001Model = Master::getAct2001($this->uid);
                    $tokens = $Act2001Model->info['tokens'][$storyChoose['heroId']][$value];
                    if(empty($tokens) ||$tokens['isActivation'] == 0){
                        return false;
                    }
                    $totalToken++;
                    break;
                case "usercloth":
                    $Act6140Model = Master::getAct6140($this->uid);
                    if($value['body'] != 0 && !in_array($value['body'], $Act6140Model->info['clothes'])){
                        return false;
                    }
                    break;
                case "card":
                    $CardModel = Master::getCard($this->uid);
                    if(!$CardModel->check_info($value,true)){
                        return false;
                    }
                    $cardCount++;
                    break;
                case "baowu":
                case "baowu1":
                    $BaowuModel = Master::getBaowu($this->uid);
                    if(!$BaowuModel->check_info($value,true)){
                        return false;
                    }
                    $totalBaowu++;
                    break;
                case "herodress":
                    $Act6143Model = Master::getAct6143($this->uid);
                    if(!in_array($value, $Act6143Model->info['clothes'])){
                        return false;
                    }
                break;
            }
        }
        if($heroCount < 1 || $cardCount < 1){
            Master::error(MUST_THING_NOT_ENOUGH);
        }
        if($totalBaowu < $baowuNum || $totalToken < $tokenNum){
            Master::error(MUST_THING_NOT_ENOUGH);
        }
        return true;
    }

    //heroid是zonggushi配置中指定对应的heroid
    //检测是否满足必要条件
    public function checkIsMeetConditions($conditions,$storyChoose){
        $needCount = 0;
        if(empty($conditions)){
            return false;
        }
        foreach($conditions as $cond){
            $heroId = $storyChoose['heroId'];
            switch($cond['type']){
                case 'heroep'://具体伙伴的某个属性
                    $TeamModel = Master::getTeam($this->uid);
                    $prop = $TeamModel->info['heros'][$heroId]['aep']['e'.$cond['id']];
                    if($prop < $cond['count']) {
                        return false;
                    }
                    $needCount++;
                    break;
                case 'userlvl'://身份等级
                    $UserModel = Master::getUser($this->uid);
                    if($UserModel->info['level'] < $cond['count']){
                        return false;
                    }
                    $needCount++;
                    break;
                case 'token'://指定信物达到X级
                case 'token1'://指定信物达到X级
                    $tokenId = $storyChoose['token'];
                    if($cond['type'] == 'token1'){
                        $tokenId = $storyChoose['token1'];    
                    }
                    $Act2001Model = Master::getAct2001($this->uid);
                    //指定hero的信物
                    if($cond['id'] != $tokenId){
                        return false;
                    }
                    $tokens = $Act2001Model->info['tokens'][$heroId][$tokenId];
                    if(empty($tokens) || $tokens['isActivation'] == 0 || $tokens['lv'] < $cond['count']){
                        return false;
                    }
                    $needCount++;
                    break;
                case 'baowu':
                case 'baowu1'://指定奇珍达到X星
                    $baowuId = $storyChoose['baowu'];
                    if($cond['type'] == 'baowu1'){
                        $baowuId = $storyChoose['baowu1'];
                    }
                    if($cond['id'] != $baowuId){
                        return false;
                    }
                    $baowuModel = master::getBaowu($this->uid);
                    $baowuInfo = $baowuModel->getBaowuInfo($cond['id'],false);
                    if ($baowuInfo['star'] < $cond['count']){
                        return false;
                    }
                    $needCount++;
                    break;
                case 'usercloth'://使用指定女主时装
                    $Act6141Model = Master::getAct6141($this->uid);
                    if($storyChoose['usercloth']['body'] != $cond['id']){
                        return false;
                    }
                    $needCount++;
                    break;
                case 'herodress'://使用指定男主时装
                    if($storyChoose['herodress'] != $cond['id']){
                        return false;
                    }
                    $needCount++;
                    break;
                case 'card'://指定卡牌达到X星
                    if($cond['id'] != $storyChoose['card']){
                        return false;
                    }
                    $cardModel = master::getCard($this->uid);
                    $cardInfo = $cardModel->getCardInfo($cond['id'],false);
                    if ($cardInfo['star'] < $cond['count']){
                        return false;
                    }
                    $needCount++;
                    break;
                case 'jiban':
                    $Act6001Model = Master::getAct6001($this->uid);
                    $jbNum = $Act6001Model->getHeroJB($heroId);
                    if($jbNum < $cond['count']){
                        return false;
                    }
                    break;
                case 'suit':
                    $head = $storyChoose['usercloth']['head'];
                    $body = $storyChoose['usercloth']['body'];
                    $ear = $storyChoose['usercloth']['ear'];
                    $userSuit = Game::getcfg('clothe_suit');
                    $chooseArr = array();
                    foreach($userSuit as $v){
                        if($v['type'] == $cond['id']){
                            array_merge($chooseArr,$v['clother']);
                        }
                    }
                    if(!in_array($head,$chooseArr) && !in_array($body,$chooseArr) && !in_array($ear,$chooseArr)){
                        return false;
                    }
                    break;
                case 'herolvl':
                    $HeroModel = Master::getHero($this->uid);
                    $info = $HeroModel->check_info($heroId);
                    if($info['level'] < $cond['count']){
                        return false;
                    }
                    break;
                default:
                    Master::error("checkIsMeetConditions error");                
            }
        }
        if($needCount == count($conditions)){
            $this->info['isPerfect'] = 1;
        }
        return true;
    }

    //计算所选的东西势力值
    public function CaculateShili($storyChoose,$wupin){
        //伙伴属性
        $totalShili = array(1=>0,2=>0,3=>0,4=>0);
        $TeamModel = Master::getTeam($this->uid);
        $heroShiLi =$TeamModel->info['heros'][$storyChoose['heroId']]['aep'];
        $totalShili = Game::epadd($heroShiLi,$totalShili);
        
        //奇珍属性
        if($storyChoose['baowu'] > 0){
            $baowuModel = Master::getBaowu($this->uid);
            $baowuInfo = $baowuModel->getBaowuInfo($storyChoose['baowu'],true);
            $totalShili = Game::epadd(Game::filterep($baowuInfo),$totalShili);
        }
        
        //卡牌属性
        if($storyChoose['card'] > 0){
            $cardModel = Master::getCard($this->uid);
            $cardInfo = $cardModel->getCardInfo($storyChoose['card'],true);
            $totalShili = Game::epadd(Game::filterep($cardInfo),$totalShili);
        }
        
        //用户服装属性
        if($storyChoose['usercloth']['body'] > 0){
            $Act6140Model = Master::getAct6140($this->uid);
            $totalShili = Game::epadd($Act6140Model->getOneProp($storyChoose['usercloth']['body']),$totalShili);
        }
        
        //计算评分公式 故事元素评分=所选物品势力值*0.5+物品符合条件数量*所选物品势力值*0.5*0.1
        $SumShiLi = array_sum($totalShili);
        $count = $this->GetContentCount( $wupin, $storyChoose);
        $score = $SumShiLi*0.5 + $SumShiLi*$count*0.5*0.1;
        return $score;
    }

    //获取满足条件的数量
    public function GetContentCount($wupin,$storyChoose){
        $count = 0;
        foreach($wupin as $v){
            foreach ($storyChoose as $key => $value) {
                if(($key == "token" || $key == "token1") && $v['type'] == "token"){
                    $$Act2001Model = Master::getAct2001($this->uid);
                    $tokens = $Act2001Model->info['tokens'][$storyChoose['heroId']][$value];
                    if(!empty($tokens) && $tokens['isActivation'] == 1 && $v['id'] == $value){
                        $count++;
                    }
                }elseif(($key == "baowu" || $key == "baowu1") && $v['type'] == "baowu"){
                    $BaowuModel = Master::getBaowu($this->uid);
                    if($BaowuModel->check_info($value,true) && $v['id'] == $value){
                        $count++;
                    }
                }elseif($v['type'] == $key){
                    if($v['id'] == $value){
                        $count++;
                        break;
                    }
                }
            }
        }
        return $count;
    }
    //购买次数
    public function buyCount(){
        if(empty($this->info['buyCount'])){
            $this->info['buyCount'] = 0;
        }
        $UserModel = Master::getUser($this->uid);
        $vipCfg = Game::getcfg_info('vip',$UserModel->info['vip']);
        if($this->info['buyCount'] >= $vipCfg['fuyuetime']){
            Master::error(BUY_COUNT_MAX);
        }
        Master::sub_item($this->uid,KIND_ITEM,998,1);
        $this->info['buyCount']++;
        $this->save();
    }

    //清除数据
    public function removeData(){
        $this->info['randStoryIds'] = array();
        $this->info['randSmallStoryId'] = 0;
        $this->info['chooseInfo'] = array();
        $this->info['isPerfect'] = 0;
        $this->save();
    }

    public function make_out(){
        $this->randTheme();
        $this->outf = $this->info;
    }
}