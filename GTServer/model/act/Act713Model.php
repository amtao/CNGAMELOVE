<?php

require_once "ActBaseModel.php";
/**
 * 办差-剧情信息
 */
class Act713Model extends ActBaseModel{
    public $atype = 713;

    public $comment = "办差-剧情信息";
    public $b_mol = "office";//返回信息 所在模块
    public $b_ctrl = "story";//返回信息 所在控制器

    public $_init = array(
        'stories' => array(),
        'cLevel' => 0,
    );

    //根据官位随机剧情
    public function randIndependentStory(){
        $UserModel = Master::getUser($this->uid);
        $level = $UserModel->info['level'];
        if(empty($this->info['cLevel'])){
            $this->info['cLevel'] = 0;
        }
        $this->info['cLevel'] = $level;

        $bcJiangliCfg = Game::getcfg_info('bc_jiangli',$level);

        $bcJuqingCfg = Game::getcfg_info('bc_juqing',0);
        $resultArr = array();
        foreach($bcJuqingCfg as $v){
            if((!empty($v['officerId'])) && $level >= $v['officerId'][0] && $level <= $v['officerId'][1]){
                $resultArr[$v['id']] = $v;
            }
        }
        if(empty($this->info['stories'])){
            $this->info['stories'] = array();
        }
        if(empty($resultArr)){
            Master::error(OFFICE_EMPTY);
        }
        for($i = 0; $i < $bcJiangliCfg['num']; $i++){
            $storyId = Game::get_rand_key1($resultArr,'weight');
            array_push($this->info['stories'],$storyId);
        }
        $this->save();
    }

    //随机死亡剧情
    public function randDeathStory(){
        $Act712Model = Master::getAct712($this->uid);
        $rounds = $Act712Model->info['rounds'];
        $resultArr = array();
        $bcJuqingCfg = Game::getcfg_info('bc_juqing',1);
        foreach($bcJuqingCfg as $v){
            if($v['isFirst'] != 1){
                continue;
            }
            //cKind 条件类型
            if($v['rounds'] != 0 && $rounds < $v['rounds']){
                continue;
            }
            if((!empty($v['officerId'])) && $this->info['cLevel'] < $v['officerId'][0] && $this->info['cLevel'] > $v['officerId'][1]){
                continue;
            }
            $isContent = true;
            foreach($v['conditions'] as $cKind => $value){
                if($value == 0){
                   if($Act712Model->info[$cKind] <= $value){
                        $resultArr[$v['id']] = $v;
                        continue;
                    } 
                }else{
                    if($Act712Model->info[$cKind] >= $value){
                        $resultArr[$v['id']] = $v;
                        continue;
                    }
                }
                
            }
        }
        if(empty($resultArr)){
            Master::error(OFFICE_EMPTY);
        }
        $storyId = Game::get_rand_key1($resultArr,'weight');
        array_unshift($this->info['stories'],$storyId);
        $this->save();
    }

    //随机系列剧情
    public function randContinueStory(){
        $bcJiesuoCfg = Game::getcfg('bc_jiesuo');
        if(count($this->info['stories']) > 0){
            return;
        }
        $resultArr = array();
        foreach($bcJiesuoCfg as $v){
            if($v['card'] > 0){
                $CardModel = Master::getCard($this->uid);
                $isHave= $CardModel->hasCard($v['card']);
                if(!$isHave){
                    continue;
                }
            }
            $bcJuqingCfg = Game::getcfg_info('bc_juqing',$v['type']);
            foreach($bcJuqingCfg as $juqing){
                if($juqing['type'] >2 && $juqing['isFirst'] != 1){
                    continue;
                }
                if((!empty($juqing['officerId'])) && $this->info['cLevel'] < $juqing['officerId'][0] && $this->info['cLevel'] > $juqing['officerId'][1]){
                    continue;
                }
                $resultArr[$juqing['id']] =  $juqing;
            }
        }
        if(empty($resultArr)){
            Master::error(OFFICE_EMPTY);
        }
        $storyId = Game::get_rand_key1($resultArr,'weight');
        array_unshift($this->info['stories'],$storyId);
        $this->save();
    }

    
    //复活次数
    public function remove_data(){
        $this->info = $this->_init;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}