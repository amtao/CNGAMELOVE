<?php
/**
 * Created by PhpStorm.
 * User: xianyu
 * Date: 2019/7/15
 * Time: 18:46
 */
class payMod extends Base {
    public function gamePrePay($data) {
        Common::loadModel('OrderModel');
        $Api = OrderModel::sdk_login('qq');
        $data['userId'] = $this->uid;
        $res = $Api->gamePrePay($data);
        Master::back_data(0,'pay','gamePrePay',$res);
    }
}