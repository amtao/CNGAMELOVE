<?php 
require_once "ActBaseModel.php";
/*
 * 战斗编队
 */

class Act763Model extends ActBaseModel{
    
    public $atype = 763;//活动编号

	public $comment = "战斗编队";
	public $b_mol = "fight";//返回信息 所在模块
    public $b_ctrl = "team";//返回信息 所在控制器
    
    public $_init = array(
        'fTroops' => array(),//战斗编队
        'jTroops' => array(),//郊游战斗编队
    );

    //设置编队
    public function setTeams($teams,$heroId){
        
        $minCards = Game::getcfg_param('team_min');
        $teamUnlcokCfg = Game::getcfg('team_unlock');
        
        //根据剧情解锁获取最大槽位
        $UserModel = Master::getUser($this->uid);
        $mapId = $UserModel->info['bmap']+$UserModel->info['smap']-1;
        $maxCards = 0;
        foreach($teamUnlcokCfg as $k => $v){
            if($mapId >= $v['unlock']){
                $maxCards = $k;
            }
        }
        $teamArr = explode('|',$teams);
        if(count($teamArr) < $minCards || count($teamArr) > $maxCards){
            Master::error(TEAMS_COUNT_NOT_ENOUGH);
        }
        $CardModel = Master::getCard($this->uid);

        $tempArr = array();
        foreach ($teamArr as $k => $cardId) {
            if(empty($CardModel->info[$cardId])){
                Master::error(NO_CARD.$cardId);
            }
            if(empty($tempArr[$cardId])){
                $tempArr[$cardId] = 1;
            }elseif($tempArr[$cardId] >= 1){
                Master::error(TEAMS_NOT_SAME);
            }
        }
        if(empty($heroId)){//普通战斗编队
            $this->setFightTeams($teamArr);
        }else{//郊游战斗编队
            $this->setJiaoyouTeams($teamArr,$heroId);
        }
        $this->save();
    }

    //$teams 客户端传编队信息
    public function setFightTeams($teamArr){
        $this->info['fTroops'] = array();
        foreach($teamArr as $k => $cardId){
            if(!in_array($cardId,$this->info['fTroops'])){
                array_push($this->info['fTroops'],$cardId);
            }
        }
    }

    //$teams 客户端传编队信息
    public function setJiaoyouTeams($teamArr,$heroId){
        $cardCfg = Game::getcfg('card');
        $this->info['jTroops'][$heroId] = array();
        foreach($teamArr as $k => $cardId){
            $cfgHeroId = $cardCfg[$cardId]['hero'];
            if($cfgHeroId != 0 && $cfgHeroId != $heroId){
                Master::error(TEAMS_COUNT_HERO_ERR);
            }
            if(!in_array($cardId,$this->info['jTroops'][$heroId])){
                array_push($this->info['jTroops'][$heroId],$cardId);
            }
        }
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
