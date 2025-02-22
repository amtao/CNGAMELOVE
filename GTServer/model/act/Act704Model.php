<?php

require_once "ActBaseModel.php";

/*
*赴约 
*/

class Act704Model extends ActBaseModel{
    
    public $atype = 704;//活动编号

	public $comment = "赴约--回忆录";
	public $b_mol = "fuyue";//返回信息 所在模块
    public $b_ctrl = "memory";//返回信息 所在控制器
    
    public $_init = array(
        //回忆录
    );

    //保存故事
    public function saveStory(){
        $Act703Model = Master::getAct703($this->uid);
        $Act705Model = Master::getAct705($this->uid);

        $UserModel = Master::getUser($this->uid);
        $vipCfg = Game::getcfg_info('vip',$UserModel->info['vip']);
        if(empty($this->info['saveCount'])){
            $this->info['saveCount'] = 0;
        }
        if($this->info['saveCount'] >= $vipCfg['gushi']){
            Master::error(SAVE_STORY_MAX);
        }

        $collectArr = $Act703Model->info['randStoryIds'];
        $heroId = $Act703Model->info['chooseInfo']['heroId'];
        $storyId = $Act703Model->info['randSmallStoryId'];
        $userclothe = $Act703Model->info['chooseInfo']['usercloth'];
        $herodress = $Act703Model->info['chooseInfo']['herodress'];
        $fightArr = $Act705Model->info['fightResult'];
        //不管胜利/失败 战斗id插入到故事中
        for($i = 1;$i<=count($fightArr);$i++){
            array_splice($collectArr,$i*2,0,$fightArr[$i-1]);
        }
        if(empty($this->info['sCount'])){
            $this->info['sCount'] = 1;
        }
        $storyCount = $this->info['sCount'];
        $perfect = $this->calculatePerfect($storyId,$collectArr,$Act703Model->info['isPerfect']);
        if(empty($this->info['cStory'][$storyCount])){
            $this->info['cStory'][$storyCount] = array("storyId"=> $storyId,"storyArr" => $collectArr,
            "usercloth"=> $userclothe,"herodress" =>$herodress,"perfect" => $perfect);
        }
        $this->info['sCount']++;
        $this->info['saveCount']++;

        $this->save();
        $Act703Model->removeData();
        $Act705Model->removeData();
    }

    //计算完美度
    public function calculatePerfect($storyId,$collectArr,$isPerfect){
        $perfect = 0;
        if(empty($this->info['finish'][$storyId])){
            $this->info['finish'][$storyId] = 0;
        }
        if(count($collectArr) == 0){
            Master::error("当前没有故事可保存");
        }
        
        $pingfenCfg = Game::getcfg_info('ping_fen',count($collectArr));
        $perfect += $pingfenCfg['perfect'];
        
        $addPerfect = Game::getcfg_param('fuyue_increment');
        $perfect += $addPerfect*$this->info['finish'][$storyId];
        
        if($isPerfect == 1 && count($collectArr) >= 8){
            $perfect = 100;
        }else{
            if($perfect >= 95){
                $perfect = 95;
            }
        }
        $this->info['finish'][$storyId]++;
        return $perfect;
    } 

    //删除故事
    public function delStory($id){
        if(empty($this->info['cStory'][$id])){
            Master::error(NOT_HAVE_STORY);
        }
        // $index = array_search($id,$this->info['cStory']);
        // array_splice($this->info['cStory'],$index,1);
        unset($this->info['cStory'][$id]);
        $this->info['saveCount']--;
        $this->save();
    }


    public function make_out(){
        $this->outf = $this->info;
    }
}