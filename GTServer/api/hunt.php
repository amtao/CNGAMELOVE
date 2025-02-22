<?php
//狩猎
class huntMod extends Base{
    
    /*
     * 打开狩猎
     * */
    public function hunt() {
        //用户信息
        $Act110Model = Master::getAct110($this->uid);
        $Act110Model->back_data();
        //日志
        $Sev41Model = Master::getSev41($Act110Model->hd_cfg['info']['id']);
        $Sev41Model->bake_data();
        //排行第一的信息
//         $Redis108Model = Master::getRedis108($Act110Model->hd_cfg['info']['id']);
//         $Redis108Model -> back_data_first();
        //全服积分
        $Sev40Model = Master::getSev40($Act110Model->hd_cfg['info']['id']);
        $Sev40Model->back_data();
    }
    /*
     * 打猎物
     * 准度 id 1,2,3,4
     * 
     * */
    public function play($params){
        $id = Game::intval($params,'id');
        $Act110Model = Master::getAct110($this->uid);
        $Act110Model->play($id);
    }
    
    public function isOpen(){
        $Act110Model = Master::getAct110($this->uid);
        $Act110Model->isOpen();
    }
    /*
     * 领取积分奖励
     * */
    public function jf_rwd($params){
        $id = Game::intval($params,'id');
        
        $Act110Model = Master::getAct110($this->uid);
        $Act110Model->jf_rwd($id);
    }
    
    /*
     * 全服奖励
     * */
    public function allDressRwd(){
        //用户信息
        $Act110Model = Master::getAct110($this->uid);
        $Act110Model->back_data();
        //总积分
        $Sev40Model = Master::getSev40($Act110Model->hd_cfg['info']['id']);
        $Sev40Model->back_data();
        //排行前十
        $Redis108Model = Master::getRedis108($Act110Model->hd_cfg['info']['id']);
        //$Redis108Model->out_num = 10;
        $Redis108Model->back_data();
        
    }
    /*
     * 排行榜
     * */
    public function paihang(){
        $Act110Model = Master::getAct110($this->uid);
        $Redis108Model = Master::getRedis108($Act110Model->hd_cfg['info']['id']);
        $Redis108Model->back_data();
        $Redis108Model->back_data_my($this->uid);
    }
}