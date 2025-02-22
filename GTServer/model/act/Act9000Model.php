<?php
require_once "ActBaseModel.php";
/*
 * 活动8023
 */
class Act9000Model extends ActBaseModel
{
    public $atype = 9000;//活动编号
    public $comment = "聊天";
    public $b_mol = "chat";//返回信息 所在模块
    public $b_ctrl = "chatmsg";//子类配置

    /*
	 * 初始化结构体
	 */
    public $_init =  array(
        'lastchatTime' => 0,
    );

    public function setChatTime(){
        $this->info['lastchatTime'] = Game::get_now();
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}
