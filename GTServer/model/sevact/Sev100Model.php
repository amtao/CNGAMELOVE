<?php
/*
 * 联盟日志
 */
require_once "SevBaseModel.php";
class Sev100Model extends SevBaseModel
{
	public $comment = "联盟投壶";
	public $act = 100;//活动标签
	public $_init = array(
        'potInfo' => array(),//每个壶的信息
        'awardInfo' => array(),//奖励信息
    );

    //投壶
    public function setThrowPot($id,$uid){
        if(empty($this->info['potInfo'][$id])){
            $this->info['potInfo'][$id] = array();
        }
        if(in_array($uid,$this->info['potInfo'][$id])){
            Master::error(CLUB_PARTY_HAS_THROW);
        }
        array_push($this->info['potInfo'][$id],$uid);
        $this->save();
    }

    //检测奖励信息
    public function checkAwardInfo($cid){
        //根据宴会开启时间判断宴会是否结束
        $Sev17Model = Master::getSev17($cid);
        if(!$Sev17Model->isEnd()){
            return;
        }
        if(!empty($this->info['awardInfo'])){
            return;
        }
        //奖励信息
        $this->info['awardInfo'] = array();
        $rollCfg = Game::getcfg('party_roll');
        $tempArr = array(1,2,3);
        shuffle($tempArr);
        for($i = 1;$i <= 3;$i++){
            $allPeoples = $this->info['potInfo'][$i];
            $peoples = count($allPeoples);
            if($peoples <= 0){
                continue;
            }
            $index = $tempArr[$i-1];

            $items = $rollCfg[$index]['rwd'][0];
            $buff = $rollCfg[$index]['buff']/100;
            $totalCount = ceil($peoples*$items['count']*(1+$buff));
            $randArr = Game::random_splite($totalCount,$peoples);
            
            foreach($randArr as $k => $v){
                $UserModel = Master::getUser($allPeoples[$k]);
                $itemArr = array('name' => $UserModel->info['name'],'itemid' => $items['id'],'count' => $v,'kind' => $items['kind']);
                $this->info['awardInfo'][$index][$allPeoples[$k]] = $itemArr;
            }
        }
        $this->save();
    }

    public function removeData(){
        $this->info = $this->_init;
        $this->save();
    }
    
    /*
     * 返回协议信息
     */
    public function bake_data(){
        $this->outof = $this->info;
        Master::back_data(0,'club','throwPot',$this->outof);
    }
}






