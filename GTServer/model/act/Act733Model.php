<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约--图鉴
 */

class Act733Model extends ActBaseModel{
    
    public $atype = 733;//活动编号

	public $comment = "伙伴饮食";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "collect";//返回信息 所在控制器
    
    public $_init = array(
        'things' => array(),
        'maxScore' => array(),//最大赏味值
    );

    /**
     * 插入收集物
     * 当获取到新的食物或鱼保存
     */
    public function setThings($itemid){
        if(empty($this->info['things'][$itemid])){
            $this->info['things'][$itemid] = 0;
        }

        $GameItemCfg = Game::getcfg_info("game_item",$itemid);
        $this->info['things'][$itemid]++;
        $min = $GameItemCfg['weight'][0];
        $max = $GameItemCfg['weight'][1];
        if(is_float($GameItemCfg['weight'][0])){
            $min = $min*10;
            $max = $max*10;
        }
        $score = rand($min,$max);
        $itemCount = $this->info['things'][$itemid];
        if(is_float($GameItemCfg['weight'][0])){
            $score = $score/10;
        }

        if(empty($this->info['maxScore'][$itemid])){
            $this->info['maxScore'][$itemid] = array("score" => 0,"pick" => 0);
            
        }
        if($score >= $this->info['maxScore'][$itemid]['score']){
            $this->info['maxScore'][$itemid]['score'] = $score;
        }
        $data = array("itemId" => $itemid,"currentScore" => $score);
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data);
        $Act737Model = Master::getAct737($this->uid);
        if($GameItemCfg['type'] == 1){
            if($itemCount == 1){
                $Act737Model->setTask(1);
            }
            
            if($itemid == 30000){
                $Act737Model->setTask(5);
            }
            $Act737Model->setTwoParamTask(3,$GameItemCfg['star']);
            $fishType = explode('|',$GameItemCfg['fishtype']);
            foreach($fishType as $v){
                $Act737Model->setTwoParamTask(7,$v);
            }
        }else{
            if($itemCount == 1){
                $Act737Model->setTask(2);
            }
            $Act737Model->setTwoParamTask(4,$GameItemCfg['star']);
            $citys = explode('|',$GameItemCfg['city']);
            foreach($citys as $v){
                $Act737Model->setTwoParamTask(6,$v);
            }
        }
        $Act734Model = Master::getAct734($this->uid);
        $Act734Model->setCollects($itemid,$itemCount);
        $this->save();
    }

    //领取最大赏味值奖励 最大重量奖励
    public function maxAward($itemid){
        $maxAwardCfg = Game::getcfg_info("max_rwd",$itemid);
        if($this->info['maxScore'][$itemid]['pick'] == 1){
            Master::error(HAS_PICK_AWARD);
        }
        if($this->info['maxScore'][$itemid]['score'] >= $maxAwardCfg['maxweight']){
            Master::add_item3($maxAwardCfg['rwd']);
        }
        $this->info['maxScore'][$itemid]['pick'] = 1;
        $this->save();
        $Act734Model = Master::getAct734($this->uid);
        $Act734Model->back_data();
    }


    public function make_out(){
        $this->outf = $this->info;
    }

}
