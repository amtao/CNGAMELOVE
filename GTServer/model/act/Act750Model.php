<?php 
require_once "ActBaseModel.php";
/*
 * 购买直购礼包
 * 是否弹出礼包
 */

class Act750Model extends ActBaseModel{
    
    public $atype = 750;//活动编号

	public $comment = "购买直购礼包";
	public $b_mol = "giftBag";//返回信息 所在模块
    public $b_ctrl = "buy";//返回信息 所在控制器
    
    public $_init = array(
        'pop' => array(),//弹出信息
        'buy' => array(),//购买信息
    );


    //根据类型判断是否需要弹出
    public function setIsPop($type,$value = 1){
        //暂时屏蔽掉弹出礼包
        // return;
        //获取当前的所有礼包
        $this->refreshGift();
        $giftArr = $this->queryValidGift();
        $popIds = array();

        //根据类型取到id
        foreach($giftArr[$type] as $giftId){
            $giftCfg = Game::getGiftBagCfg();
            $gifts = $giftCfg[$giftId];
            if($value < $gifts['set']){
                continue;
            }
            if($type == 5){
                if(empty($gifts['pre_gift'])){
                    array_push($popIds,$giftId);
                    continue;
                }
                foreach($gifts['pre_gift'] as $id){
                    if(empty($this->info['pop'][$id])){
                        array_push($popIds,$id);
                    }elseif(empty($this->info['buy'][$id])){
                        array_push($popIds,$id);
                    }elseif($this->info['buy'][$id] >= $gifts['limit']){
                        array_push($popIds,$giftId);
                    }
                }
            }else{
                if(empty($this->info['pop'][$giftId])){
                    array_push($popIds,$giftId);
                }
            }
        }
        foreach($popIds as $popId){
            $Act751Model = Master::getAct751($this->uid);
            if($Act751Model->isMax($popId)){
                continue;
            }
            if(empty($this->info['pop'][$popId])){
                $this->info['pop'][$popId] = array('pop' => 0,'popTime' =>0);
            }else{
                continue;
            }
            $this->info['pop'][$popId]['pop']++;
            $this->info['pop'][$popId]['popTime'] = Game::get_now();
            $backs = array('isPop' => 1,'popId' => $popId);
            Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$backs);
        }
        
        $this->save();
    }

    public function queryValidGift(){
        $giftBagCfg = Game::getGiftBagCfg();
        $giftArr = array();
        $UserModel = Master::getUser($this->uid);
        $vipLv = $UserModel->info['vip'];
        foreach($giftBagCfg as $id => $v){
            if($v['type'] != 1){
                continue;
            }
            if(empty($giftArr[$v['condition']])){
                $giftArr[$v['condition']] = array();
                array_push($giftArr[$v['condition']],$id);
            }
            if($vipLv >= $v['vip']){
                foreach($giftArr[$v['condition']] as $gId){
                    if($giftBagCfg[$gId]['vip'] == $v['vip'] && !in_array($id,$giftArr[$v['condition']])){
                        array_push($giftArr[$v['condition']],$id);
                    }
                    if($giftBagCfg[$gId]['vip'] < $v['vip']){
                        $giftArr[$v['condition']] = array();
                        array_push($giftArr[$v['condition']],$id);
                    }
                }
            }
        }

        return $giftArr;
    }

    public function buyGift($giftId){
        $giftCfg = Game::getGiftBagCfg();
        if($giftCfg[$giftId]['type'] != 1){
            return;
        }
        if(empty($this->info['buy'][$giftId])){
            $this->info['buy'][$giftId] = 0;
        }
        $this->info['buy'][$giftId]++;
        if($this->info['buy'][$giftId] >= $giftCfg[$giftId]['limit'] && $giftCfg[$giftId]['condition'] != 1){
            unset($this->info['buy'][$giftId]);
            unset($this->info['pop'][$giftId]);
            // $this->info['buy'][$giftId] = 0;
            // $this->info['pop'][$giftId] = array();
        }
        Master::add_item3($giftCfg[$giftId]['items']);
        $this->save();
    }

    //时间到了 清掉过期的礼包弹出信息
    public function refreshGift(){
        $giftCfg = Game::getGiftBagCfg();
        foreach($this->info['pop'] as $id => $v){
            if($v['condition'] == 1){
                continue;
            }
            $duration = $giftCfg[$id]['duration'];
            $now = Game::get_now();
            if($v['popTime'] + $duration > $now){
                continue;
            }
            unset($this->info['pop'][$id]);
        }
        $this->save();
    }

    public function exchangeOrderBack(){

        if (!empty($this->info["itemInfo"])) {
            Master::add_item3($this->info["itemInfo"]);
            $this->info["itemInfo"] = array();
            $this->save();
        }
    }

    public function exchangeItem($id, $zc_item){

        $this->info["itemInfo"] = $zc_item['items'];
        $this->save();
    }



    public function make_out(){
        $this->outf = $this->info;
    }

}
