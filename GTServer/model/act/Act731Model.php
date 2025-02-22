<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约次数
 */

class Act731Model extends ActBaseModel{
    
    public $atype = 731;//活动编号

	public $comment = "伙伴邀约";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "inviteInfo";//返回信息 所在控制器
    
    public $_init = array(
        'inviteCount' => 3,//邀约次数
        'lastRefreshTime' => 0,//上次恢复时间
    );

    //开始游戏--根据id判断是什么游戏
    public function start($cityId,$id,$heroId){
        if($this->info['inviteCount'] <= 0){
            Master::error(INVITE_NO_COUNT);
        }
        $Act730Model = Master::getAct730($this->uid);
        $cityInfo = $Act730Model->info['events'][$cityId];
        $isFind = false;
        foreach($cityInfo as $gType => $gId){
            if($gId != $id){
                continue;
            }
            if($gType == 'fish'){
                $Act735Model = Master::getAct735($this->uid);
                $Act735Model->startFish($cityId,$id,$heroId,$Act730Model->info['refreshTime']);
                $Act738Model = Master::getAct738($this->uid);
                $Act738Model->giveYur();
            }else{
                $Act732Model = Master::getAct732($this->uid);
                $Act732Model->startFood($cityId,$id,$heroId,$Act730Model->info['refreshTime']);
            }
            $isFind = true;
        }
        if(!$isFind){
            Master::error(INVITE_ID_ERROR);
        }
        $this->info['inviteCount']--;
        if($this->info['lastRefreshTime'] == 0){
            $this->info['lastRefreshTime'] = Game::get_now();
        }
        $this->save();
    }

    /**
     * 刷新回复次数
     * 每一个小时回复一次
     */
    public function refreshCount(){
        //服装升级增加的属性
        $Act756Model = Master::getAct756($this->uid);
        $extraCount = $Act756Model->getPropCount(8);

        if($this->info['inviteCount'] >= (3+$extraCount)){
            $this->info['lastRefreshTime'] = 0;
            $this->save();
            return;
        }
        $now = Game::get_now();
        $lastRefresh = $this->info['lastRefreshTime'];
        $timeSub = $now - $lastRefresh;
        $intval = Game::getcfg_param("game_addtime");
        $count = intval($timeSub/$intval);
        if($count + $this->info['inviteCount'] >= (3+$extraCount)){
            $this->info['inviteCount'] = 3 + $extraCount;
            $this->info['lastRefreshTime'] = 0;
        }else{
            $this->info['inviteCount'] += $count;
            $this->info['lastRefreshTime'] += $count*$intval;
        }

        $this->save();
    }


    public function make_out(){
        $this->outf = $this->info;
    }

}
