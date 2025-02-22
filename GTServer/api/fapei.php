<?php
/**
 * 发配
 * User: Administrator
 * Date: 2018/1/15
 * Time: 14:25
 */
class FapeiMod extends Base{

    /**
     * 进入发配
     */
    public function info(){
        $Act128Model = Master::getAct128($this->uid);
        $Act128Model->back_data();
        $Act128Model->back_data_desk();

        $Act129Model = Master::getAct129($this->uid);
        $Act129Model->back_data();
    }
    /**
     * 添加桌子
     */
    public function addDesk(){
        $Act128Model = Master::getAct128($this->uid);
        $Act128Model->add_desk();
    }

    /**
     * 发配
     * @param $params
     */
    public function banish($params){
        $hid = Game::intval($params,'hid');
        $did = Game::intval($params,'did');
        $Act129Model = Master::getAct129($this->uid);
        $Act129Model->banish($hid,$did);
    }

    /**
     * 召回
     */
    public function recall($params){
        $did = Game::intval($params,'did');
        $type = Game::intval($params,'type');
        $Act129Model = Master::getAct129($this->uid);
        $Act129Model->recall($did,$type);
    }
}