<?php
require_once "ActBaseModel.php";
/*
 * 活动8023
 */
class Act8023Model extends ActBaseModel
{
    public $atype = 8023;//活动编号
    public $comment = "好友送礼";
    public $b_mol = "friends";//返回信息 所在模块
    public $b_ctrl = "flove";//子类配置
    protected $_save_msg = true; //是否更新缓存

    /*
	 * 初始化结构体
	 */
    public $_init =  array(
        "send" => array(),  // 赠送好友礼物
        "fuyan" => 0        // 敷衍增加亲密度
    );

    /*
     * 赠送礼物
     */
    public function sendFriendGift($fUid){

        $this->info["send"][] = $fUid;
        $this->save();
    }

    /*
     * 赴宴
     */
    public function fuyan($fuid){

        $Act130Model = Master::getAct130($this->uid);
        if ( !isset($Act130Model->info['list'][$fuid]) ) {
            return false;
        }

        $FriendModel = Master::getFriend($this->uid);
        $FriendModel->add_love($fuid, 1);

        $FriendModel = Master::getFriend($fuid);
        $FriendModel->add_love($this->uid, 1, false);

        $this->info["fuyan"]++;
        $this->save();
    }

    /*
     * 联姻
     */
    public function lianyin($fuid){

        $Act130Model = Master::getAct130($this->uid);
        if ( !isset($Act130Model->info['list'][$fuid]) ) {
            return false;
        }

        $FriendModel = Master::getFriend($this->uid);
        $FriendModel->add_love($fuid, 2);

        $FriendModel = Master::getFriend($fuid);
        $FriendModel->add_love($this->uid, 2, false);
    }

    /*
     * 构造输出结构体
     */
    public function data_out(){

        //构造输出
        $this->outf = array();
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);

        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd(){
        self::data_out();
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        return 0;
    }
}
