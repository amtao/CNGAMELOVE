<?php 
require_once "ActBaseModel.php";
/*
 * 心忆
 */

class Act757Model extends ActBaseModel{
    
    public $atype = 757;//活动编号

	public $comment = "心忆";
	public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "equipCard";//返回信息 所在控制器
    
    public $_init = array(
        'slotInfo' => array(),//大槽位解锁信息
        'cardInfo' => array(),//卡牌放置信息
        'activateSmallProp' => array(),//激活的属性
        'activateSkill' => array(),//激活的技能属性
        'starInfo' => array(),//星级
        'addSkillProp' =>array(),//额外加的技能属性
    );

    //检测是否为套装
    public function checkIsSuit($suitId){
        $Act6140Model = Master::getAct6140($this->uid);
        $clothes = $Act6140Model->info['clothes'];
        $clothSuitCfg = Game::getcfg_info('clothe_suit',$suitId);
        $result = array_diff($clothSuitCfg['clother'],$clothes);
        if(!empty($result)){
            return false;
        }
        return true;
    }

    //放置卡牌
    public function putCard($suitId,$cardId,$bSlot){
        if(empty($this->info['slotInfo'][$suitId][$bSlot])){
            Master::error(CLOTHE_SLOT_NOT_UNLOCK);
        }
        if(empty($this->info['cardInfo'][$suitId][$bSlot])){
            $this->info['cardInfo'][$suitId][$bSlot] = 0;
        }
        $CardModel = Master::getCard($this->uid);
        $oldCardId = $this->info['cardInfo'][$suitId][$bSlot];
        $cardData = $CardModel->check_info($cardId);
        if(!empty($cardData) && $cardData['isClotheEquip'] == 1){
            Master::error(CLOTHE_CARD_IS_EQUIP);
        }
        $updateCards = array();
        if($oldCardId != 0){
            $oldCardData = $CardModel->check_info($oldCardId);
            $oldCardData['isClotheEquip'] = 0;
            $CardModel->update_card($oldCardData);
            $updateCards[] = $oldCardData;
        }
        $cardData['isClotheEquip'] = 1;
        if(empty($cardData)){
            $cardData['isClotheEquip'] = 0;
        }
        $CardModel->update_card($cardData);
        $updateCards[] = $cardData;
        $this->info['cardInfo'][$suitId][$bSlot] = $cardId;
        
        Master::back_data($this->uid,"card","equipCard",$updateCards,true);
        
        //放置卡牌之后检查槽位解锁的属性是否激活
        $this->checkSlotUnlock($suitId);
        
        $this->save();
    }

    //检测槽位是否解锁
    public function checkSlotUnlock($suitId){
        if(!$this->checkIsSuit($suitId)){
            return;
        }
        $cardSlotCfg = Game::getcfg_info('cardSlot',$suitId);
        $this->checkBigSlotUnlock($cardSlotCfg,$suitId);
    }

    //检测大槽位是否解锁
    public function checkBigSlotUnlock($cardSlotCfg,$suitId){
        for($i = 1;$i <= 3;$i++){
            $unlock = $cardSlotCfg['unlock'.$i];
            switch ($unlock[0]) {
                case '1':
                    if(empty($this->info['slotInfo'][$suitId][$i])){
                        $this->info['slotInfo'][$suitId][$i] = array();
                    }
                    break;
                case '2':
                    $Act6140Model = Master::getAct6140($this->uid);
                    $suitLv = $Act6140Model->info['suit'][$suitId];
                    if($suitLv >= $unlock[1]){
                        if(empty($this->info['slotInfo'][$suitId][$i])){
                            $this->info['slotInfo'][$suitId][$i] = array();
                        }
                    }
                    break;
                case '3':
                    $Act756Model = Master::getAct756($this->uid);
                    $jyLv = $Act756Model->info['suitBrocadeLv'][$suitId];
                    if($jyLv >= $unlock[1]){
                        if(empty($this->info['slotInfo'][$suitId][$i])){
                            $this->info['slotInfo'][$suitId][$i] = array();
                        }
                    }
                    break;
                default:
                    Master::error('cfg_error');
                    break;
            }
        }
        $this->checkSmallSlotUnlock($cardSlotCfg,$suitId);

        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(4);
        $TeamModel->reset(6);
    }

    //检测卡牌内小槽位的解锁属性
    public function checkSmallSlotUnlock($cardSlotCfg,$suitId){
        $bigSlot = $this->info['slotInfo'][$suitId];
        foreach($bigSlot as $bSlot => $sInfoArr){
            $propInfo[$bSlot] = array();
            $tempArr = array();
            if(empty($sInfoArr)){
                $sInfoArr = array();
            }
            $cardId = $this->info['cardInfo'][$suitId][$bSlot];
            $CardModel = Master::getCard($this->uid);
            $cardInfo = $CardModel->getCardInfo($cardId,true);
            for($i = 1;$i <= 3;$i++){
                $sInfo = $sInfoArr[$i];
                if(empty($sInfo)){
                    $sInfo = array('slot' => 0,'propId' => 0,'isActivated' => 0);
                }
                $randPropId = $this->getPropId($bSlot,$i,$suitId);
                $propId = empty($sInfo['propId']) ? $randPropId : $sInfo['propId'];
                $ep = $cardSlotCfg['ep'.$bSlot.'_'.$i];
                $sInfo['slot'] = $i;
                $sInfo['propId'] = $propId;
                $sInfo['isActivated'] = 0;
                switch ($ep[0]) {
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                        if(!empty($cardInfo) && $cardInfo['e'.$ep[0]] >= $ep[1]){
                            $sInfo['isActivated'] = 1;
                        }
                        break;
                    case '5':
                        if(!empty($cardInfo) && $cardInfo['level'] >= $ep[1]){ 
                            $sInfo['isActivated'] = 1;
                        }
                        break;
                    default:
                        if(!empty($cardInfo)){ 
                            $sInfo['isActivated'] = 1;
                        }
                        break;
                }
                //属性激活之后
                $tempArr[$i] = $sInfo;
            }
            $this->info['slotInfo'][$suitId][$bSlot] = $tempArr;
        }
        //检测小槽位的属性 计算星级 将所有的属性汇总
        $this->checkSlotProp();

        //当一个大槽位的三条小属性都解锁之后 会激活一条属性
        $this->checkSkill($suitId);

        $this->save();
    }

    //检测卡槽属性
    /**
     * 所有套装激活的属性加在一起
     * type1 套装内所有部件的某一属性
     * type2 当前槽位卡牌的属性
     */
    public function checkSlotProp(){
        $slotInfo = $this->info['slotInfo'];
        $activateProp = array(); //激活过得属性
        $starInfo = array();//星级信息
        foreach($slotInfo as $suitId => $bInfo){
            foreach($bInfo as $bSlot => $info){
                $cardId = $this->info['cardInfo'][$suitId][$bSlot];
                if($cardId == 0){
                    continue;
                }
                $star = 0;
                foreach($info as $sSlot => $sInfo){
                    if($sInfo['isActivated'] == 0){
                        continue;
                    }
                    $propertyCfg = Game::getcfg_info('property',$sInfo['propId']);
                    $star += $propertyCfg['star'];
                    $buffType = $propertyCfg['buff'][0];
                    $buffValue = $propertyCfg['buff'][1];
                    switch ($propertyCfg['type']) {
                        case '1':
                            if(empty($activateProp[$propertyCfg['type']])){
                                $activateProp[$propertyCfg['type']] = array(1=>0,2=>0,3=>0,4=>0);
                            }
                            $clotheSuitCfg = Game::getcfg_info('clothe_suit',$suitId);
                            $clotheCount = count($clotheSuitCfg['clother']);
                            $addValue = $buffValue*$clotheCount;
                            $activateProp[$propertyCfg['type']][$buffType] += $addValue;
                            break;
                        case '2':
                            if(empty($activateProp[$propertyCfg['type']][$cardId])){
                                $activateProp[$propertyCfg['type']][$cardId] = array(1=>0,2=>0,3=>0,4=>0);
                            }
                            $activateProp[$propertyCfg['type']][$cardId][$buffType] += $buffValue;
                            break;
                        default:
                            break;
                    }
                }
                $starInfo[$suitId][$bSlot] = $star;
            }
        }
        $this->info['starInfo'] = $starInfo;
        $this->info['activateSmallProp'] = $activateProp;
    }

    //检测技能
    public function checkSkill($suitId){
        $cardSlotCfg = Game::getcfg_info('cardSlot',$suitId);
        $slotInfo = $this->info['slotInfo'][$suitId];
        foreach($slotInfo as $bSlot =>$bInfo){
            $bSlotProp = array();
            //激活小属性的数量
            $activateCount = 0;
            foreach($bInfo as $sSlot => $sInfo){
                if($sInfo['isActivated'] == 1){
                    $activateCount += 1;
                }
            }
            //激活数量不满足条件
            if($activateCount < 3){
                continue;
            }
            $need = $cardSlotCfg['need'.$bSlot];
            $skillType = $cardSlotCfg['type'.$bSlot];
            if(empty($bSlotProp[$skillType])){
                $bSlotProp[$skillType] = array();
            }
            $star = $this->info['starInfo'][$suitId][$bSlot];
            $tempArr = array();
            foreach($need as $index => $v){
                if($star >= $v['star']){
                    $tempArr = $v['buff'];
                }
            }
            $bSlotProp[$skillType] = $tempArr;
            $this->info['activateSkill'][$suitId][$bSlot] = $bSlotProp;
        }
        $this->updateSkill();
    }

    //随机获取属性
    public function getPropId($bSlot,$sSlot,$suitId){
        $propertyCfg = Game::getcfg('property');
        $resultArr = array();
        foreach($propertyCfg as $id => $v){
            if(!in_array($bSlot,$v['cardSlot'])){
                continue;
            }
            if(!in_array($sSlot,$v['epSlot'])){
                continue;
            }
            $resultArr[$id] = $v;
        }

        $propertyCount = Game::getcfg_param('property_count');
        $propsArr = explode('|',$propertyCount);
        $index = 1;
        $refreshCount = $this->info['refreshCount'][$suitId][$bSlot];
        if($refreshCount > $propsArr[1]){
            $index = 3;
        }elseif($refreshCount >= $propsArr[0] && $refreshCount <= $propsArr[1]){
            $index = 2;
        }
        $propId = Game::get_rand_key1($resultArr,'prop'.$index);
        return $propId;
    }

    //刷新单条属性
    public function refreshProp($suitId,$bSlot,$sSlot){
        if(!$this->checkIsSuit($suitId)){
            Master::error(CLOTHE_SUIT_NOT_ENOUGH);
        }
        $prop = $this->info['slotInfo'][$suitId][$bSlot][$sSlot];
        if($prop['isActivated'] == 0){
            Master::error(CLOTHE_PROP_MUST_ACTIVATE);
        }
        $propertyCfg = Game::getcfg('property');
        $oldPropId = $prop['propId'];
        $oldStar = $propertyCfg[$oldPropId]['star'];
        $propId = $this->getPropId($bSlot,$sSlot,$suitId);
        $prop['propId'] = $propId;
        $newStar = $propertyCfg[$propId]['star'];
        $starCha = $newStar - $oldStar;
        $this->info['slotInfo'][$suitId][$bSlot][$sSlot] = $prop;
        $this->info['starInfo'][$suitId][$bSlot] += $starCha;
        $this->checkSlotUnlock($suitId);
        $this->checkSkill($suitId);
        $this->save();
    }

    //更新技能带来的属性 重新计算
    /**
     * 1.当前放置卡牌M数值增加X%
     * 2.政务时资源获得增加X%
     * 3.钓鱼积分增加X%
     * 4.饮食积分增加X%
     * 5.伙伴m才学时间减少X秒
     * 6.徒弟活力恢复时间减少X秒
     * 7.徒弟游历时间减少X秒
     * 8.徒弟游历获取资源M增加X%
     * 9.郊游守护产出增加X%
     * 10.弹劾m产出增加X%
     */
    public function updateSkill(){
        $tempArr = array();
        foreach($this->info['activateSkill'] as $suitId => $bInfo){
            foreach($bInfo as $bSlot =>$skillInfo){
                $cardId = $this->info['cardInfo'][$suitId][$bSlot];
                foreach($skillInfo as $skType => $skInfo){
                    switch ($skType) {
                        case '1':
                            if(empty($tempArr[$skType][$cardId])){
                                $tempArr[$skType][$cardId] = array(1=>0,2=>0,3=>0,4=>0);
                            }
                            $tempArr[$skType][$cardId][$skInfo[0]] += $skInfo[1];
                            break;
                        case '2':
                        case '5':
                        case '8':
                        case '10':
                            $currency = $skInfo[0];
                            if(empty($tempArr[$skType][$currency])){
                                $tempArr[$skType][$currency] = 0;
                            }
                            $tempArr[$skType][$currency] += $skInfo[1];
                            break;
                        case '3':
                        case '4':
                        case '6':
                        case '7':
                        case '9':
                            if(empty($tempArr[$skType])){
                                $tempArr[$skType] = 0;
                            }
                            $tempArr[$skType] += $skInfo[0];
                            break;
                        
                        default:
                            break;
                    }
                }
            }
        }
        $this->info['addSkillProp'] = $tempArr;
        $this->save();
    }

    public function getSkillProp($type,$extraParam = 0){
        if($type == 1){
            if(empty($this->info['addSkillProp'][$type][$extraParam])){
                $this->info['addSkillProp'][$type][$extraParam] = array(1=>0,2=>0,3=>0,4=>0);
            }
            return $this->info['addSkillProp'][$type][$extraParam];
        }
        return $this->info['addSkillProp'][$type];
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
