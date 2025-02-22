<?php

require_once "ActBaseModel.php";
/**
 * 办差
 */
class Act712Model extends ActBaseModel{
    public $atype = 712;

    public $comment = "办差--基本信息";
    public $b_mol = "office";//返回信息 所在模块
    public $b_ctrl = "work";//返回信息 所在控制器

    public $_init = array(
        'found' => 0,   //地盘
        'force' => 0,   //武力
        'human' => 0,   //人
        'money' => 0,   //钱
        'rounds' => 0,  //回合数
        'reviveCount' => 0,//复活次数
        'isDeath' => 0,//是否死亡
        'deathId' => 0,
        'isStart' => 0,//是否开始
        'endId' => 0,
        'dependRounds'=>0,
    );

    //开始办差
    public function startBanchai(){
        if($this->info['isStart'] == 1){
            Master::error(OFFICE_IS_START);
        }
        $this->setInitValue();

        $Act716Model = Master::getAct716($this->uid);
        if($Act716Model->info['startCount'] <= 0){
            Master::error(OFFICE_COUNT_NOT_ENOUGH);
        }
        $Act716Model->info['startCount'] -= 1;
        if($Act716Model->info['recoverTime'] == 0){
            $Act716Model->info['recoverTime'] = Game::get_now();
        }
        $Act716Model->save();
        $Act713Model = Master::getAct713($this->uid);
        $Act713Model->randIndependentStory();
        $this->info['isStart'] = 1;
        $this->save();
    }

    //选择yes/no 
    public function chooseYN($yes){
        if($this->info['isDeath'] == 1){
            Master::error(OFFICE_IS_DEATH);
        }
        $Act713Model = Master::getAct713($this->uid);
        $cLevel = $Act713Model->info['cLevel'];
        $first = current($Act713Model->info['stories']);
        array_shift($Act713Model->info['stories']);
        $bcJuqingCfg = Game::getcfg_info('bc_juqing_id',$first);
        if($yes == 1){
            $this->addYes($bcJuqingCfg);
        }else{
            $this->addNo($bcJuqingCfg);
        }

        $this->info['rounds']++;
        //死亡
        if($bcJuqingCfg['end'] > 0 || $bcJuqingCfg['endgame'] > 0){
            $this->info['isDeath'] = 1;
            if($bcJuqingCfg['end'] > 0){
                $this->info['deathId'] = $bcJuqingCfg['end'];
            }else{
                $this->info['deathId'] = $bcJuqingCfg['endgame'];
            }  
        }elseif($bcJuqingCfg['type'] == 1 || $bcJuqingCfg['type'] == 2){
            if($yes == 1){
                array_unshift($Act713Model->info['stories'],$this->randYesNoNextId($bcJuqingCfg['yes_custom'],$cLevel));
            }else{
                array_unshift($Act713Model->info['stories'],$this->randYesNoNextId($bcJuqingCfg['no_custom'],$cLevel));
            }
        }elseif(($bcJuqingCfg['type'] == 0 || $bcJuqingCfg['type'] > 2) && $this->checkIsDeath()){
            $Act713Model->randDeathStory();
        }elseif(!$this->checkIsDeath()){//没死
            $bcJiangliCfg = Game::getcfg_info('bc_jiangli',$cLevel);
            //通关
            if($this->info['rounds'] >= $bcJiangliCfg['num'] && $bcJuqingCfg['type'] < 3 ){
                $Act713Model->randContinueStory();
            }
            if($bcJuqingCfg['type'] > 2){
                if($yes == 1){
                    array_unshift($Act713Model->info['stories'],$this->randYesNoNextId($bcJuqingCfg['yes_custom'],$cLevel));
                }else{
                    array_unshift($Act713Model->info['stories'],$this->randYesNoNextId($bcJuqingCfg['no_custom'],$cLevel));
                }
            }
        }
        $Act714Model = Master::getAct714($this->uid);
        if($this->info['deathId'] > 0){
            $this->info['endId'] = $first;
            $Act714Model->setFinalId($this->info['deathId']);
        }
        if($bcJuqingCfg['type'] != 1){
            $this->info['dependRounds']++;  
        }  
        $this->save();
        $Act713Model->save();
    }

    //随机yes no的下一步
    public function randYesNoNextId($stories,$level){
        $resultArr = array();
        foreach($stories as $v){
            $bcJuqingCfg = Game::getcfg_info('bc_juqing_id',$v);
            if($bcJuqingCfg['rounds'] != 0 && $rounds < $bcJuqingCfg['rounds']){
                continue;
            }
            if((!empty($bcJuqingCfg['officerId'])) && $level < $bcJuqingCfg['officerId'][0] && $level > $bcJuqingCfg['officerId'][1]){
                continue;
            }
            $resultArr[$bcJuqingCfg['id']] = $bcJuqingCfg;
        }
        $storyId = Game::get_rand_key1($resultArr,'weight');
        return $storyId;
    }

    //选择yes的答案
    public function addYes($bcJuqingCfg){
        if($bcJuqingCfg['yes_found'] != 0){
            $this->info['found'] += $bcJuqingCfg['yes_found'];
        }
        if($bcJuqingCfg['yes_force'] != 0){
            $this->info['force'] += $bcJuqingCfg['yes_force'];
        }
        if($bcJuqingCfg['yes_human'] != 0){
            $this->info['human'] += $bcJuqingCfg['yes_human'];
        }
        if($bcJuqingCfg['yes_money'] != 0){
            $this->info['money'] += $bcJuqingCfg['yes_money'];
        }
    }

    //选择no的答案
    public function addNo($bcJuqingCfg){
        if($bcJuqingCfg['no_found'] != 0){
            $this->info['found'] += $bcJuqingCfg['no_found'];
        }
        if($bcJuqingCfg['no_force'] != 0){
            $this->info['force'] += $bcJuqingCfg['no_force'];
        }
        if($bcJuqingCfg['no_human'] != 0){
            $this->info['human'] += $bcJuqingCfg['no_human'];
        }
        if($bcJuqingCfg['no_money'] != 0){
            $this->info['money'] += $bcJuqingCfg['no_money'];
        }
    }

    //复活
    public function revive(){
        $maxReviveCount = Game::getcfg_param('banchai_revivetime');
        if($this->info['reviveCount'] >= $maxReviveCount){
            Master::error(OFFICE_REVIVE_COUNT_MAX);
        }
        $Act713Model = Master::getAct713($this->uid);
        $bcJiesuoCfg = Game::getcfg_info('bc_jiangli',$Act713Model->info['cLevel']);
        $current = $this->info['dependRounds'];
        $total = $bcJiesuoCfg['num'];
        $chaValue = $total-$current;
        $base = Game::getcfg_param('banchai_revivecost');
        $cost = Game::getCfg_formula()->banchai_reviviCost($chaValue,$total,$base);
        if($this->info['rounds'] >= $total){
            $cost = $base;
        }
        Master::sub_item($this->uid,KIND_ITEM,1,$cost);
        $finalValue = Game::getcfg_param('banchai_revivevalue');
        if($this->info['found'] < $finalValue){
            $this->info['found'] = $finalValue;
        }
        if($this->info['force'] < $finalValue){
            $this->info['force'] = $finalValue;
        }
        if($this->info['human'] < $finalValue){
            $this->info['human'] = $finalValue;
        }
        if($this->info['money'] < $finalValue){
            $this->info['money'] = $finalValue;
        }
        $this->info['isDeath'] = 0;
        $this->info['deathId'] = 0;
        $this->info['endId'] = 0;
        $this->info['reviveCount']++;
        if(count($Act713Model->info['stories'])==0){
            $Act713Model->randContinueStory();
        }
        $this->save();
    }

    //不复活
    public function dontRevive(){
        $Act713Model = Master::getAct713($this->uid);
        if($this->info['rounds'] == 0){

        }else{
            $bcJiesuoCfg = Game::getcfg_info('bc_jiangli',$Act713Model->info['cLevel']);
            $Act714Model = Master::getAct714($this->uid);
            if($this->info['rounds'] >= $bcJiesuoCfg['num']){
                $Act714Model->pickLevelAward();
            }else{
                $Act714Model->pickRatioAward();
            }
            // if($this->info['deathId'] > 0){
            //     $Act714Model->pickFinalAward($this->info['deathId']);
            // }
 
        }
        $this->remove_data();
        $Act713Model->remove_data();
    }

    //复活次数
    public function remove_data(){
        $this->info = $this->_init;
        $this->save();
    }

    //设置初始值
    public function setInitValue(){
        $initvalue = Game::getcfg_param("banchai_chushi");
        $initArr = explode("|",$initvalue);
        if($this->info['found'] == 0 && $this->info['force'] == 0 &&
        $this->info['human'] == 0 && $this->info['money'] == 0){
            $this->info['found'] = $initArr[0];
            $this->info['force'] = $initArr[1];
            $this->info['human'] = $initArr[2];
            $this->info['money'] = $initArr[3];
        }
    }

    
    //判断是否死亡
    public function checkIsDeath(){
        if($this->info['found'] <= 0 || $this->info['force'] <= 0 || $this->info['human'] <= 0 || $this->info['money'] <= 0){
            $this->info['found'] = $this->info['found'] <= 0?0:$this->info['found'];
            $this->info['force'] = $this->info['force'] <= 0?0:$this->info['force'];
            $this->info['human'] = $this->info['human'] <= 0?0:$this->info['human'];
            $this->info['money'] = $this->info['money'] <= 0?0:$this->info['money'];
            return true;
        }
        // if($this->info['found'] >= 100 || $this->info['force'] >= 100 || $this->info['human'] >= 100 || $this->info['money'] >= 100){
        //     $this->info['found'] = 100;
        //     $this->info['force'] = 100;
        //     $this->info['human'] = 100;
        //     $this->info['money'] = 100;
        //     return true;
        // }
        return false;
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}
