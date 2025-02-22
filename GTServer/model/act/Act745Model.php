<?php 
require_once "ActBaseModel.php";
/*
 * 守护列表
 */

class Act745Model extends ActBaseModel{
    
    public $atype = 745;//活动编号

    public $comment = "伙伴郊游-守护列表";
    public $b_mol = "jiaoyou";//返回信息 所在模块
    public $b_ctrl = "list";//返回信息 所在控制器
    
    public $_init = array(
        'guardList' => array(),//守护列表
    );

    //当完成可守护关时,插入守护列表
    public function setGuardList($id,$heroId,$copyId){
        if(!empty($this->info['guardList'][$heroId][$copyId])){
            return;
        }
        $this->info['guardList'][$heroId][$copyId] = array('id' => 0,'star' => 0,'award' => 0,'refreshTime' => 0,'equipCard'=>array());
        $this->info['guardList'][$heroId][$copyId]['id'] = $id;
        $this->info['guardList'][$heroId][$copyId]['refreshTime'] = 0;
        $initStar = $this->getInitStar();
        if(empty($initStar) || $initStar >= 4){
            $initStar = 1;
        }
        $this->info['guardList'][$heroId][$copyId]['star'] = $initStar;
        $this->info['guardList'][$heroId][$copyId]['award'] = $this->getAward($heroId,$copyId,true);
        $this->save();
    }

    //开始守护
    public function startGuard($heroId,$copyId,$cardEquips){
        $equips = explode("|",$cardEquips);
    
        $jiaoyouCfg = Game::getcfg_info("jiaoyou",$heroId);
        if(count($equips) < $jiaoyouCfg[$copyId]['cardNum']){
            Master::error(JIAOYOU_CARD_NUM_NOT_ENOUGH);
        }
        $CardModel = Master::getCard($this->uid);
        foreach($equips as $v){
            $cardData =  $CardModel->check_info($v);
            if(empty($cardData)){
                Master::error(JIAOYOU_NO_CARD);
            }
            if($cardData['isEquip'] == 1){
                Master::error(JIAOYOU_CARD_IS_EQUIP);
            }
        }
        //判断今天次数是否消耗
        $Act741Model = Master::getAct741($this->uid);
        $Act741Model->addCount();

        $list = $this->info['guardList'][$heroId][$copyId];
        if(empty($list)){
            return;
        }
        if($list['refreshTime'] != 0){
            Master::error(JIAOYOU_GUARDING);
        }
        $list['refreshTime'] = Game::get_now();
        $this->info['guardList'][$heroId][$copyId] = $list;
        $updateCards = array();
        foreach($equips as $v){
            $cardData =  $CardModel->check_info($v);
            $cardData['isEquip'] = 1;
            // $h_update = array(
            //     'cardid' => $v,
            //     'level' => $cardData['level'],
            //     'star' => $cardData['star'],
            //     'isEquip' => 1,
            //     'imprintLv' => $cardData['imprintLv'],
            // );
            $CardModel->update_card($cardData);
            array_push($this->info['guardList'][$heroId][$copyId]['equipCard'],$v);
            $updateCards[] = $cardData;
        }
        Master::back_data($this->uid,"card","equipCard",$updateCards,true);

        $this->save();

        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(153,1);
    }

    //领取守护奖励
    public function pickAward($heroId,$copyId){
        $list = $this->info['guardList'][$heroId][$copyId];
        if(empty($list)){
            return;
        }

        $now = Game::get_now();
        $starCfg = Game::getcfg_info("jiaoyou_star",$list['star']);
        if($list['refreshTime'] + $starCfg['shijian'] > $now){
            Master::error(JIAOYOU_NOT_REACH_TIME);
        }
        
        $CardModel = Master::getCard($this->uid);
        $totalStar = 0;
        $totalQuality = 0;
        foreach($list['equipCard'] as $cardId){
            $cardData =  $CardModel->check_info($cardId);
            $totalStar+=$cardInfo['star'];
            $CardCfg = Game::getcfg_info("card",$cardId);
            $totalQuality+=$CardCfg['quality'];
        }
        $addRate = Game::getCfg_formula()->jiaoyou_rate($totalQuality,$totalStar);
        $guaJiCfg = Game::getcfg_info("jiaoyou_guaji",$heroId);
        $jiaoyouGuajiCfg = $guaJiCfg[$list['award']];
        $jiaoyouCfg = Game::getcfg_info("jiaoyou",$heroId);
        $output = $jiaoyouCfg[$copyId]['output'][$jiaoyouGuajiCfg['output']];
        $totalCount = floor($output*$addRate*$jiaoyouGuajiCfg['baseNum']*$starCfg['starOutput']/100);

        $Act757Model = Master::getAct757($this->uid);
        $skillRate = $Act757Model->getSkillProp(9);
        $totalCount = ceil($totalCount*(1+$skillRate/100));

        Master::add_item($this->uid,$jiaoyouGuajiCfg['kind'],$jiaoyouGuajiCfg['item'],$totalCount);
        $list['refreshTime'] = 0;

        $updateCards = array();
        $CardModel = Master::getCard($this->uid);
        foreach($list['equipCard'] as $cardId){
            $cardData =  $CardModel->check_info($cardId);
            $cardData['isEquip'] = 0;
            // $h_update = array(
            //     'cardid' => $cardId,
            //     'level' => $cardData['level'],
            //     'star' => $cardData['star'],
            //     'isEquip' => 0,
            //     'imprintLv' => $cardData['imprintLv'],
            // );
            $CardModel->update_card($cardData);
            $updateCards[] = $cardData;
        }

        Master::back_data($this->uid,"card","equipCard",$updateCards,true);

        $list['equipCard'] = array();
        $this->info['guardList'][$heroId][$copyId] = $list;
        $this->refreshOne($heroId,$copyId);
        $this->save();
    }

    //刷新守护列表
    public function refreshList($heroId){

        $count = 0;
        $list = $this->info['guardList'][$heroId];
        if(empty($list)){
            Master::error(JIAOYOU_NO_REFRESH_EVENT);
        }
        foreach($list as $copyId => $v){
            $starCfg = Game::getcfg_info("jiaoyou_star",$v['star']);
            if($v['refreshTime'] != 0){
                continue;
            }
            if($this->isCanUpStar($v['star'])){
                if($list[$copyId]['star'] < 5){
                    $list[$copyId]['star']++;
                    $count++;
                }
            }
            $list[$copyId]['award'] = $this->getAward($heroId,$copyId,false);
        }
        if($count > 0){
            $cost = Game::getcfg_param("jiaoyou_guaji_shuaxin");
            Master::sub_item($this->uid,KIND_ITEM,4,$cost);
        }else{
            Master::error(JIAOYOU_NO_REFRESH_EVENT);
        }
        $this->info['guardList'][$heroId] = $list;
        $this->save();
    }

    public function refreshOne($heroId,$copyId){
        $event = $this->info['guardList'][$heroId][$copyId];
        $starCfg = Game::getcfg_info("jiaoyou_star",$event['star']);
        if($list['refreshTime'] != 0){
            return;
        }
        if($this->isCanUpStar($event['star'])){
            if($event['star'] < 5){
                $event['star']++;
            }
        }
        $event['award'] = $this->getAward($heroId,$copyId,false);
        $this->info['guardList'][$heroId][$copyId] = $event;
        // $this->save();
    }

    //获取初始星级
    private function getInitStar(){
        $starCfg = Game::getcfg('jiaoyou_star');
        $star = Game::get_rand_key1($starCfg,'initStar');
        return $star;
    }

    //判断是否可以升星
    private function isCanUpStar($star){
        $starCfg = Game::getcfg_info("jiaoyou_star",$star);
        $rate = rand(1,100);
        if($rate >= $starCfg['refreshStar']){
            return true;
        }
        return false;
    }

    //获取随机奖励id
    private function getAward($heroId,$copyId,$isInit){
        $jyGuajiCfg = Game::getcfg_info("jiaoyou_guaji",$heroId);
        $awardArr = array();
        foreach($jyGuajiCfg as $k => $v){
            if($copyId >= $v['stageCondition']){
                $awardArr[$v['id']] = $v;
            }
        }
        if($isInit){
            $awardId = Game::get_rand_key1($awardArr,'initWeight');
        }else{
            $awardId = Game::get_rand_key1($awardArr,'refreshWeight');
        }
        
        return $awardId;
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
