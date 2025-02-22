<?php 
require_once "ActBaseModel.php";
/*
 * 弹劾外围信息
 */

class Act721Model extends ActBaseModel{
    
    public $atype = 721;//活动编号

	public $comment = "弹劾外围信息";
	public $b_mol = "tanhe";//返回信息 所在模块
    public $b_ctrl = "outside";//返回信息 所在控制器
    
    public $_init = array(
        'maxCopy' => 0,     //最大关卡
        'currentCopy' => 0, //当前关卡
    );

    //扫荡
    public function wipe($copyId){
        $Act720Model = Master::getAct720($this->uid);
        // if($Act720Model->info['wipeCount'] >= 1){
        //     Master::error(TANHE_NO_COUNT);
        // }
        // $maxCopy = $this->info['maxCopy'];
        // if($Act720Model->info['count'] >= 1){
        //     $maxCopy = $this->info['currentCopy'] - 1;
        // }
        $copyId = $copyId-1;
        if($copyId > $this->info['maxCopy']){
            Master::error(TANHE_MAX_COPY_NOT_WIPE);
        }
        $minCopy = 1;
        $maxCopy = $copyId;
        if(empty($Act720Model->info['pickCopy'])){
            $Act720Model->info['pickCopy'] = range(1,$copyId);
        }else{
            if(in_array($copyId,$Act720Model->info['pickCopy'])){
                Master::error(TANHE_NO_COUNT);
            }
            $length = count($Act720Model->info['pickCopy']);
            $minCopy = $Act720Model->info['pickCopy'][$length-1]+1;
            for($i = $minCopy;$i <= $maxCopy;$i++){
                array_push($Act720Model->info['pickCopy'],$i);
            }
            
        }
        
        $this->getWipeAward($minCopy,$maxCopy);
        $Act720Model->info['wipeCount'] += 1;
        $maxCount = Game::getcfg_param("tanhe_max");
        if($copyId < $maxCount){
            $copyId = $copyId + 1;
        }
        $this->info['currentCopy'] = $copyId;
        $this->save();
        $Act720Model->save();
    }

    //周卡扫荡
    public function weekWipe(){
        $Act68Model = Master::getAct68($this->uid);
        if($Act68Model->find_ka(4) == 0){
            Master::error(TANHE_NO_WEEK_CARD);
        }
        $Act720Model = Master::getAct720($this->uid);
        if($Act720Model->info['weekCount'] >= 1){
            Master::error(TANHE_NO_COUNT);
        }
        $maxCopy = $this->info['maxCopy'];
        $cost = $maxCopy*5;
        Master::sub_item($this->uid,KIND_ITEM,1,$cost);
        $this->getWipeAward(1,$maxCopy);
        $Act720Model->info['weekCount'] += 1;
        $Act720Model->save();
    }

    //领取前多少关的奖励
    private function getWipeAward($minCopy,$maxCopy){
        $tanheCfg = Game::getcfg("tanhe");
        $awardArr = array();
        foreach($tanheCfg as $k => $v){
            if($k >= $minCopy && $k <= $maxCopy){
                foreach($v['rwd'] as $_item){
                    $awardArr[$_item['id']][$_item['kind']] += $_item['count'];
                }
            }
        }
        $Act757Model = Master::getAct757($this->uid);
        $skillRate = $Act757Model->getSkillProp(10);
        foreach( $awardArr as $id => $v){
            foreach( $v as $kind => $count){
                $addCount = ceil($count *(1+$skillRate/100));
                Master::add_item($this->uid,$kind,$id,$addCount);
            }
        }
    }


    public function make_out(){
        $this->outf = $this->info;
    }

}
