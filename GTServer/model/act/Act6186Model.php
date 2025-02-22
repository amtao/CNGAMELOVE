<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6186
 */
class Act6186Model extends ActHDBaseModel
{
	public $atype = 6186;//活动编号
	public $comment = "限时奖励-冬至累计登录天数";
	public $b_ctrl = "dzlogin";//子类配置
	public $hd_id = 'huodong_6186';//活动配置文件关键字
    protected $_rank_id = 6186;


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
