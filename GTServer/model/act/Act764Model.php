<?php 
require_once "ActBaseModel.php";
/*
 * 战斗中上阵卡牌
 */

class Act764Model extends ActBaseModel{
    
    public $atype = 764;//活动编号

	public $comment = "战斗中卡牌";
	public $b_mol = "card";//返回信息 所在模块
    public $b_ctrl = "fight";//返回信息 所在控制器
    
    public $_init = array(
        'equipCards' => array(),//当前上阵卡牌
        'useCard' => array(),//使用过的卡牌
        'saveSkill' => array(),//没有使用过的技能
    );

    //随机上阵卡牌
    public function randCards($heroId = 0){
        //默认普通战斗
        $Act763Model = Master::getAct763($this->uid);
        $teams = $Act763Model->info['fTroops'];
        if(!empty($heroId)){//郊游的情况下
            $teams = $Act763Model->info['jTroops'][$heroId];
        }
                
        $minCards = Game::getcfg_param('team_min');
        $teamUnlcokCfg = Game::getcfg('team_unlock');
        
        //根据剧情解锁获取最大槽位
        $UserModel = Master::getUser($this->uid);
        $mapId = $UserModel->info['bmap']+$UserModel->info['smap']-1;
        $maxCards = 0;
        foreach($teamUnlcokCfg as $k => $v){
            if($mapId >= $v['unlock']){
                $maxCards = $k;
            }
        }
        if(count($teams) < $minCards || count($teams) > $maxCards){
            Master::error(TEAMS_COUNT_NOT_ENOUGH);
        }

        $chaTeams = array_diff($teams,$this->info['useCard']);
        $chaTeams = array_diff($chaTeams,$this->info['equipCards']);
        // $sortArr = $this->sortTeams($chaTeams);
        $equipNum = Game::getcfg_param('team_number');
        if(empty($this->info['equipCards'])){
            $this->info['equipCards'] = array();
        }
        $totalCount = count($chaTeams);
        $hasEquip = count($this->info['equipCards']);
        $cha = $equipNum - $hasEquip;

        if( $totalCount > $cha){
            // $chaArr = array_slice($chaTeams,0,$cha);
            $chaArr = Game::array_rand($chaTeams,$cha);
            $this->info['equipCards'] = array_merge($this->info['equipCards'],$chaArr);
        }else{
            $this->info['equipCards'] = array_merge($this->info['equipCards'],$chaTeams);
        }    

        $this->save();

    }

    //设置用过的卡牌
    public function setUseCard($cardIds,$heroId = 0){
        $cardIdArr = explode('|',$cardIds);
        if(empty($this->info['useCard'])){
            $this->info['useCard'] = array();
        }
        foreach($cardIdArr as $k => $cardId){
            if($cardId == 0){
                continue;
            }
            array_push($this->info['useCard'],intval($cardId));
            
        }
        $this->info['equipCards'] = array_values(array_diff($this->info['equipCards'],$cardIdArr));
        $this->randCards($heroId);
    }

    //对编队进行排序
    public function sortTeams($teams){
        $CardModel = Master::getCard($this->uid);
        $sortArr = array();
        foreach($teams as $k => $cardId){
            $info = $CardModel->getEasyBase_buyid($cardId);
            $cardCfg = Game::getcfg_info('card',$cardId);
            $sortArr[] = array('cardId' => $cardId,'level' =>$info['level'],'quality' => $cardCfg['quality']);
        }
        foreach($sortArr as $key => $row){
            $idArr[$key] = $row['cardId'];
            $lvArr[$key] = $row['level'];
            $qtArr[$key] = $row['quality'];
        }
        array_multisort($lvArr,SORT_DESC,$qtArr,SORT_DESC,$idArr,SORT_ASC,$sortArr);
        $result = array();
        foreach($sortArr as $k => $v){
            array_push($result,$v['cardId']);
        }
        return $result;
    }

    //获取战斗的血量
    public function getFightHp($heroId = 0){
        $CardModel = Master::getCard($this->uid);
        $totalHp = 0;
        $Act763Model = Master::getAct763($this->uid);

        $teams = $Act763Model->info['fTroops'];
        if(!empty($heroId)){
            $teams = $Act763Model->info['jTroops'][$heroId];
        }
        foreach($teams as $k => $cardId){
            $cardInfo = $CardModel->getCardInfo($cardId,true);
            $totalHp += $cardInfo['e1'];
        }
        //卡牌羁绊属性加成
        $Act762Model = Master::getAct762($this->uid);
        $rate = $Act762Model->info['cardForeverProp'][3];
        $totalHp = $totalHp*(1+$rate);

        return $totalHp;
    }

    //获取战斗的血量
    public function releaseSkill($cardId,$round,$enemyEp,$enemyHurt,$skillPoint,$heroId = 0){
        $cardSkillCfg = Game::getcfg('card_skill');
        $tempArr = array();
        $skillArr = array();
        foreach($cardSkillCfg as $k => $v){
            if(in_array($cardId,$v['card']) && $v['unlock'] == 2){
                $tempArr = $v['card'];
                $skillArr[$v['bufftype']] = $v['buff'];
                break;
            }
        }
        $count = 0;
        foreach($tempArr as $k => $v){
            if(in_array($v,$this->info['equipCards'])){
                $count++;
                $cardIds .= $v.'|';
            }
        }
        $CardModel = Master::getCard($this->uid);
        $cardCfg = Game::getcfg_info('card',$cardId);

        if(empty($skillPoint) || end($skillPoint) == $cardCfg['shuxing']){
           array_push($skillPoint,$cardCfg['shuxing']);
        }

        $data = array();
        //上阵卡牌中有羁绊存在
        if($count >0 && $count == count($tempArr)){
            $data = $this->skillInfo($skillArr,$tempArr,$round,$CardModel,$skillPoint,$enemyEp,$enemyHurt,$cardCfg['shuxing']);
            $this->setUseCard($cardIds,$heroId);
        }else{
            $cardInfo = $CardModel->getCardInfo($cardId,true);
            $totalDamage = $cardInfo['e'.$cardCfg['shuxing']];
            $skillRate = $this->backSkillRate($skillPoint);
            $totalDamage = ceil($totalDamage*(1+$skillRate));
            $data = array('totalDamage' => $totalDamage,'hurt' => $enemyHurt,'isRestrain' => false,'skillPoint' => $skillPoint,'enemyEp' => $enemyEp,'myEp' => $cardCfg['shuxing']);
            $this->setUseCard($cardId,$heroId);
        }
        foreach($this->info['saveSkill'] as $buffType => $bInfo){
            if($buffType == 4){
                if($round <= $bInfo[0]){
                    $rate = $bInfo[1]/100;
                    if(Master::checkRestraint($data['myEp'],$data['enemyEp'])){
                        $data['totalDamage'] = ceil($data['totalDamage']*(1+$rate));
                        unset($this->info['saveSkill'][$buffType]);
                    }
                }else{
                    unset($this->info['saveSkill'][$buffType]);
                }
            }else{
                if($round <= $bInfo[0]){
                    $rate = $bInfo[1]/100;
                    $skillRate = $this->backSkillRate($skillPoint);
                    if($skillRate > 0){
                        $data['totalDamage'] = ceil($data['totalDamage']*(1+$rate));
                        unset($this->info['saveSkill'][$buffType]);
                    }
                }else{
                    unset($this->info['saveSkill'][$buffType]);
                }
            }
            $this->save();
        }
        return $data;
    }

    /**
     * skillArr 技能信息
     * tempArr  羁绊卡牌id组
     * round    回合数
     * skillPoint 技能点
     */
    public function skillInfo($skillArr,$tempArr,$round,$CardModel,$skillPoint,$enemyEp,$enemyHurt,$myEp){
        $totalDamage = 0;
        $isRestrain = false;

        $beforeSkillRate = 0;
        foreach($tempArr as $k => $cardId){
            $cardInfo = $CardModel->getCardInfo($cardId,true);
            $cardCfg = Game::getcfg_info('card',$cardId);
            $totalDamage += $cardInfo['e'.$cardCfg['shuxing']];
        }
        $skillRate = $this->backSkillRate($skillPoint);
        $totalDamage = ceil($totalDamage*(1+$skillRate));
        $beforeSkillRate = $skillRate;

        foreach ($skillArr as $bType => $bInfo) {
            switch ($bType) {
                case '1'://本套羁绊可以额外打出总伤害外X%的伤害
                    $rate = $bInfo[0]/100;
                    $totalDamage = ceil($totalDamage*(1+$rate));
                    break;
                case '2'://本回合将敌方属性转化为[M]，并降低其[X]的攻击力
                    $enemyEp = $bInfo[0];
                    $rate = $bInfo[1]/100;
                    $enemyHurt = ceil($enemyHurt*(1-$rate));
                    break;
                case '3'://本回合忽视对方[X%]的伤害
                    $rate = $bInfo[0]/100;
                    $enemyHurt = ceil($enemyHurt*(1-$rate));
                    break;
                case '4'://使用技能后N回合内（包含本回合），若触发克制，克制额外增加X%伤害，触发一次后该效果消失
                    $rate = $bInfo[1]/100;
                    if(Master::checkRestraint($myEp,$enemyEp)){
                        $totalDamage = ceil($totalDamage*(1+$rate));
                    }else{
                        if(empty($this->info['saveSkill'][$bType])){
                            $bInfo[0] += $round;
                            $this->info['saveSkill'][$bType] = $bInfo;
                        }
                    }
                    break;
                case '5'://使用技能后N回合内（包含本回合），若触发连招，连招增加X%伤害，触发一次后该效果消失
                        $rate = $bInfo[1]/100;
                        
                        if($skillRate > 0){
                            $totalDamage = ceil($totalDamage*(1+$rate));
                        }else{
                            if(empty($this->info['saveSkill'][$bType])){
                                $bInfo[0] += $round;
                                $this->info['saveSkill'][$bType] = $bInfo;
                            }
                        }
                    break;
                case '6'://本回合必定按照克制伤害计算
                    $isRestrain = true;
                    break;
                case '7'://使用技能后触发连招，并清空连击点
                    $epValue = $skillPoint[0];
                    $skillPoint = array();
                    $skillPoint = array($epValue,$epValue,$epValue);
                    $skillRate = $this->backSkillRate($skillPoint);
                    if($skillRate > 0 && $beforeSkillRate == 0){
                        $totalDamage = ceil($totalDamage*(1+$skillRate));
                    }
                    break;
                case '8'://使用技能后，将当前所有连击点属性变为[M]
                    foreach($skillPoint as $k => $v){
                        $skillPoint[$k] = $bInfo[0];
                    }
                    break;
                default:
                    break;
            }
        }
        $this->save();
        $data = array('totalDamage' => $totalDamage,'hurt' => $enemyHurt,'isRestrain' => $isRestrain,'skillPoint' => $skillPoint,'enemyEp' => $enemyEp,'myEp' => $myEp);
        
        return $data;
    }

    //技能释放的时候返回系数
    public function backSkillRate($skillPoint){
        if(empty($skillPoint)){
            return 0;
        }
        $skills = Game::getcfg_param("tanhe_jineng");
        $skArr = explode('|',$skills);

        $skillRate = Game::getcfg_param("tanhe_jineng_xishu");
        $rates = explode('|',$skillRate);
        $rate = 0;

        $myArr = $skillPoint;
        foreach($skArr as $k => $v){
            $vArr = explode(",",$v);
            if($vArr[0] == $myArr[0] && $vArr[1] == $myArr[1] && $vArr[2] == $myArr[2]){
                $rate = $rates[$k]/100;
                return $rate;
            }

        }
        return $rate;
    }

    /**
     * 判断输赢
     * 就结束了战斗
     * 10. 当玩家手中卡牌大于等于0，且敌方血量属性小于等于0时，战斗胜利
     * 11. 当玩家手中卡牌等于0，且敌方血量大于0时，战斗失败
     * 12. 当玩家手中血量小于等于0，且敌方血量大于0时，战斗失败

     */
    public function checkIsWin($myHp,$enemyHp){
        if(count($this->info['equipCards'] >= 0) && $enemyHp <= 0){
            return array('isFinish' => true,'isWin' => 1);
        }
        if(count($this->info['equipCards']) == 0 && $enemyHp > 0){
            return array('isFinish' => true,'isWin' => 0);
        }
        if($myHp <= 0 && $enemyHp > 0){
            return array('isFinish' => true,'isWin' => 0);
        }
        return array('isFinish' => false,'isWin' => 0);
    }

    /**
     * 判断是否玩家先出手
     * $epId 属性id
     * $damage 选择的属性id对应的属性值
     */
    public function judgeIsMyFirst($epId,$damage,$npcEp,$npcValue){
        /**
         * 克制关系
         * 相同属性--值大先出手
         * 克制--先出手
         * 相同属性相同值-玩家先出手
         */
        if($epId == $npcEp){
            if($damage >= $npcValue){
                return 1;
            }
        }elseif(Master::checkRestraint($epId,$npcEp)){
            return 1;
        }
        return 0;
    }

    public function removeData(){
        $this->info = $this->_init;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
