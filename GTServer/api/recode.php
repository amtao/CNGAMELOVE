<?php

class RecodeMod extends Base{
    /*
     * 兑换
     * */
    public function exchange($params){
         $key = Game::strval($params,'key');
         $AcodeModel = Master::getCode($this->uid);
         $AcodeModel->exchange($key);
         $AcodeModel->back_data();
    }
}
