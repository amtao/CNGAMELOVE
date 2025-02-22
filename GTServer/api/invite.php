<?php
//伙伴邀约
class inviteMod extends Base
{
    //获取图鉴信息
    public function getCollectInfo(){
        $Act733Model = Master::getAct733($this->uid);
        $Act733Model->back_data();

        $Act734Model = Master::getAct734($this->uid);
        $Act734Model->back_data();

        $Act737Model = Master::getAct737($this->uid);
        $Act737Model->back_data();
    }

    public function getBaseInfo(){
        $Act732Model = Master::getAct732($this->uid);
        $Act732Model->back_data();
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->back_data();
    }

    //开始邀约
    public function startInvite($params){
        //城市id
        $city = Game::intval($params,"city");
        //选择的事件id
        $id = Game::intval($params,"id");
        //选择的伙伴
        $heroId = Game::intval($params,"heroId");

        $Act731Model = Master::getAct731($this->uid);
        $Act731Model->start($city,$id,$heroId);

        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(152,1);

        // $Act35Model = Master::getAct35($uid);
        // $Act35Model->do_act(22,1);

    }

    //翻牌子
    public function turnFood($params){
        $index1 = Game::intval($params,"index1");
        $index2 = Game::intval($params,"index2");
        $Act732Model = Master::getAct732($this->uid);
        $Act732Model->turnFood($index1,$index2);
    }

    //购买鱼饵
    public function buyBait($params){
        $id = Game::intval($params,"id");
        $count = Game::intval($params,"count");
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->buyBait($id,$count);
    }

    //选择消耗的鱼饵
    public function consumeBait($params){
        $id = Game::intval($params,"id");
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->consumeBait($id);
    }
   
    //获取上钩的鱼
    public function getFakeFish($params){
        $water = Game::intval($params,"water");
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->getRandFish($water);
    }

    //钓鱼是否成功
    public function goFishing($params){
        $isSuccess = Game::intval($params,"isSuccess");
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->fishing($isSuccess);
    }

    //获取随机获取的鱼饵
    public function getRandYur(){
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->getRandYur();
    }

    public function pickRandYur(){
        $Act735Model = Master::getAct735($this->uid);
        $Act735Model->pickRandYur();
    }

    //游戏结束领取奖励
    public function pickEndAward($params){
        $isFood = Game::intval($params,"isFood");
        if( $isFood == 1){
            $Act732Model = Master::getAct732($this->uid);
            $Act732Model->pickAward();
        }else{
            $Act735Model = Master::getAct735($this->uid);
            $Act735Model->pickAward();
        }
    }

    //领取收集奖励
    public function pickCollectAward($params){
        $itemid = Game::intval($params,"itemid");
        
        $Act734Model = Master::getAct734($this->uid);
        $Act734Model->rwd($itemid);
    }


    //领取最大赏味值奖励或者最大重量奖励
    public function pickMaxAward($params){
        $itemid = Game::intval($params,"itemid");
        
        $Act733Model = Master::getAct733($this->uid);
        $Act733Model->maxAward($itemid);
    }

    /**
     * 购买次数
     * 优先使用拜名帖购买次数
     * 后续使用元宝购买次数
     */
    public function buyCount($params){
        $isItem = Game::intval($params,"isItem");
        $Act736Model = Master::getAct736($this->uid);
        $Act736Model->buyCountByItem($isItem);
    }
    
    //领取任务奖励
    public function pickTaskAward($params){
        $id = Game::intval($params,"id");
        $Act737Model = Master::getAct737($this->uid);
        $Act737Model->rwd($id);
    }

}