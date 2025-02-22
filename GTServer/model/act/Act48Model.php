<?php
require_once "ActBaseModel.php";
/*
 * 玩家记录特殊信息
 */
class Act48Model extends ActBaseModel
{
	public $atype = 48;//活动编号
	
	public $comment = "玩家记录特殊信息";
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "otherInfo";//返回信息 所在控制器

    public $_init = array(
        'ltime' => 0, //最后一次登陆时间
    );

    /*
     * 添加  最后一次登陆时间
     * */
    public function reset_ltime()
    {
        $this->info['ltime'] = Game::get_now();
        $this->save();
    }

    /*
     * 获取  最后一次登陆时间
     * */
    public function get_ltime()
    {
        return empty($this->info['ltime'])?0:$this->info['ltime'];
    }

    /*
     * 返回活动信息
     */
    public function back_data(){


    }

}














