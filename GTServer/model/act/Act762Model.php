<?php 
require_once "ActBaseModel.php";
/*
 * 卡牌羁绊
 */

class Act762Model extends ActBaseModel{
    
    public $atype = 762;//活动编号

	public $comment = "卡牌羁绊";
	public $b_mol = "card";//返回信息 所在模块
    public $b_ctrl = "fetter";//返回信息 所在控制器
    
    public $_init = array(
        'cardSks' => array(),//激活卡牌的羁绊id
        'cardForeverProp' => array(),//激活永久属性
    );


    //检测是否有羁绊
    public function checkFetter(){
        $CardModel = Master::getCard($this->uid);

        $this->info['cardSks'] = array();
        $this->info['cardForeverProp'] = array();

        $cardSkillCfg = Game::getcfg('card_skill');
        foreach ($cardSkillCfg as $id => $v) {
            $count = 0;
            $minStar = -1;
            foreach($v['card'] as $k => $cardId){
                if(empty($CardModel->info[$cardId])){
                    break;
                }
                if($minStar == -1 || $CardModel->info[$cardId]['star'] < $minStar){
                    $minStar = $CardModel->info[$cardId]['star'];
                }
                $count++;
            }
            if($count == count($v['card']) && !in_array($id,$this->info['cardSks'])){
                array_push($this->info['cardSks'],$id);
                if($v['type'] == 1){
                    $this->addForeverSkill($cardSkillCfg[$id],$minStar);
                }
            }
        }
        $this->save();
    }

    //判断永久类型的技能
    public function addForeverSkill($skCfg,$minStar){
        $tempArr = $this->info['cardForeverProp'];
        $type = $skCfg['bufftype'];
        $buff = $skCfg['buff'];

        switch($skCfg['bufftype']){
            case 1://永久增加本套卡牌M数值X%
                $epValue = ($buff[1]+($minStar*0.3))/100;
                foreach($skCfg['card'] as $index => $cardId){
                    if(empty($tempArr[$type][$cardId])){
                        $tempArr[$type][$cardId] = array(1=>0,2=>0,3=>0,4=>0);
                    }
                    $tempArr[$type][$cardId][$buff[0]] += $epValue;
                }
                break;
            case 2://（永久）类型为N的卡牌M数值增加X%
                $cardType = $buff[0];
                $cardEp = $buff[1];
                $epValue = ($buff[1]+($minStar*0.3))/100;
                if(empty($tempArr[$type][$cardType])){
                    $tempArr[$type][$cardType] = array(1=>0,2=>0,3=>0,4=>0);
                }
                $tempArr[$type][$cardType][$cardEp] += $epValue;
                break;
            case 3://增加我方所有卡牌战斗X%的血量
                $epValue = ($buff[0]+($minStar*0.2))/100;
                if(empty($tempArr[$type])){
                    $tempArr[$type] = 0;
                }
                $tempArr[$type] += $epValue;
                break;
        }
        $this->info['cardForeverProp'] = $tempArr;
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
