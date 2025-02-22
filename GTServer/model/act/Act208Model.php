<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动208
 */
class Act208Model extends ActHDBaseModel
{
	public $atype = 208;//活动编号
	public $comment = "限时奖励-累计登录天数";
	public $b_ctrl = "login";//子类配置
	public $hd_id = 'huodong_208';//活动配置文件关键字
    protected $_rank_id = 208;


    /*
     * 累计每日登陆兼容
     */
    public function do_check(){
        if(empty($this->info['cons'])){
            $this->info['cons'] = 1;
            $this->save();
        }
    }

}
