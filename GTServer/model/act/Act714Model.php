<?php

require_once "ActBaseModel.php";
/**
 * 办差-领取奖励信息
 */
class Act714Model extends ActBaseModel{
    public $atype = 714;

    public $comment = "办差-领取奖励";
    public $b_mol = "office";//返回信息 所在模块
    public $b_ctrl = "award";//返回信息 所在控制器

    public $_init = array(
        'pickInfo' => array(),
    );

    //设置最终id
    public function setFinalId($id){
        if(empty($this->info['pickInfo'][$id])){
            $this->info['pickInfo'][$id] = array('triTime' => Game::get_now(),'isPick' => 0);
            $this->save();
        }
    }

    //领取死亡剧情奖励
    public function pickFinalAward($id){
        $bcJiejuCfg = Game::getcfg_info('bc_jieju',$id);
        if(empty($this->info['pickInfo'][$id])){
            Master::error(OFFICE_END_STORY);
        }
        if($this->info['pickInfo'][$id]['isPick'] == 0 && $this->info['pickInfo'][$id]['triTime'] > 0){
            Master::add_item3($bcJiejuCfg['rwd']);
            $this->info['pickInfo'][$id]['isPick'] = 1;
        }
        $this->save();
    }

    //领取系数奖励
    public function pickRatioAward(){
        $Act712Model = Master::getAct712($this->uid);
        $Act713Model = Master::getAct713($this->uid);
        $bcJiangliCfg = Game::getcfg_info('bc_jiangli',$Act713Model->info['cLevel']);
        //计算系数奖励
        $current = $Act712Model->info['dependRounds'];
        $total = $bcJiangliCfg['num'];
        //名声
        $repute = Game::getCfg_formula()->banchai_Award($current,$total,$bcJiangliCfg['mingsheng']);
        //阅历
        $exper = Game::getCfg_formula()->banchai_Award($current,$total,$bcJiangliCfg['yueli']);
        $Act756Model = Master::getAct756($this->uid);
        $addRate = $Act756Model->getPropCount(9);

        //激活技能所加的属性
        $Act757Model = Master::getAct757($this->uid);
        $skillProp = $Act757Model->getSkillProp(2);
        
        $experRate = 0;
        $reputerate = 0;
        if(!empty($addRate)){
            $experRate += $addRate[2];
            $reputerate += $addRate[4];
        }
        if(!empty($skillProp)){
            $experRate += $skillProp[2]/100;
            $reputerate += $skillProp[4]/100;
        }
        $exper = ceil($exper*(1+$experRate));
        $repute = ceil($repute*(1+$reputerate));
        Master::add_item($this->uid,KIND_ITEM,2,$exper);
        Master::add_item($this->uid,KIND_ITEM,4,$repute);
    }

    //领取官位奖励
    public function pickLevelAward(){
        $Act712Model =Master::getAct712($this->uid);
        $Act713Model = Master::getAct713($this->uid);
        $bcJiangliCfg = Game::getcfg_info('bc_jiangli',$Act713Model->info['cLevel']);
        if($Act712Model->info['rounds'] < $bcJiangliCfg['num']){
            return;
        }
        $Act756Model = Master::getAct756($this->uid);
        $addRate = $Act756Model->getPropCount(9);

        
        $Act757Model = Master::getAct757($this->uid);
        $skillProp = $Act757Model->getSkillProp(2);

        foreach($bcJiangliCfg['rwd'] as $item){
            $totalRate = $addRate[$item['id']] + $skillProp[$item['id']]/100;
            $addCount = ceil($item['count']*(1+$totalRate));
            Master::add_item($this->uid,$item['kind'],$item['id'],$addCount);
        }
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}