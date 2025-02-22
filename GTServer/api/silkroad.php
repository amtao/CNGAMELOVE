<?php
//丝绸之路
class silkroadMod extends Base{
    
    /*
     * 是否开启
     * */
    public function isOpen($uid){
        $UserModel = Master::getUser($uid);
        if($UserModel->info['level'] < 10){
            Master::error(TRADE_OPEN_LIMIT);
        }
    }
    /*
     * 进入
     * */
    public function trade(){
        $this->isOpen($this->uid);
        
        $Act112Model = Master::getAct112($this->uid);
        $Act112Model->back_data();
    }
    /*
     * pk
     * */
    public function play($params){
        $this->isOpen($this->uid);
        $id = Game::intval($params, 'gid');
        $Act112Model = Master::getAct112($this->uid);
        $Act112Model->play($id);
    }
    
    /*
     * 一键通商
     * */
    public function rootPlay($params){
        $this->isOpen($this->uid);
        $id = Game::intval($params, 'gid');
        
        $Act113Model = Master::getAct113($this->uid);
        if(empty($Act113Model->info['status'])){
            Master::error(TRADE_NOT_OPEN_ROOT_PLAY);
        }
        $Act112Model = Master::getAct112($this->uid);
        $Act112Model->rootPlay($id);
    }
    
    /*
     * 通商排行榜orpheus://orpheus/pub/app.html#/m/artist/?id=12333145
     * */
    public function paihang() {
        $Redis114Model = Master::getRedis114();
        $Redis114Model->back_data();
        $Redis114Model->back_data_my($this->uid);
    }    
}