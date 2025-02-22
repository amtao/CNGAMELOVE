<?php
//讨伐
class taofaMod extends Base{

    /*
     * 是否可以玩
     * */
    public function isOpen($uid){
        $UserModel = Master::getUser($uid);
        if($UserModel->info['level'] < 9){
            Master::error(KING_LEVEL);
        }
    }
    /*
     * 进入讨伐
     * */
    public function taofa(){
        $this->isOpen($this->uid);
        //当前关卡信息
        $Act111Model = Master::getAct111($this->uid);
        $Act111Model->back_data();
    }
    /*
     * 打
     * params id 打到的关卡数
     * */
    public function play($params) {
        $this->isOpen($this->uid);
        $id = Game::intval($params, 'id');
        $Act111Model = Master::getAct111($this->uid);
        $Act111Model->play($id);
    }
    /*
     * 排行榜
     * */
    public function paihang(){
        $Redis111Model = Master::getRedis111();
        $Redis111Model->back_data();
        $Redis111Model->back_data_my($this->uid);
    }
    /*
     * 一键讨伐
     * @params id 达到的关卡数
     * */
    public function rootPlay($params){
        $this->isOpen($this->uid);
        $id = Game::intval($params, 'gid');
        $Act111Model = Master::getAct111($this->uid);
        $Act111Model->rootPlay($id);
    }

}
