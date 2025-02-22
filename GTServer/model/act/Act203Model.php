<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动203
 */
class Act203Model extends ActHDBaseModel
{
	public $atype = 203;//活动编号
	public $comment = "限时奖励-银两消耗";
	public $b_ctrl = "coin";//子类配置
	public $hd_id = 'huodong_203';//活动配置文件关键字
    protected $_rank_id = 203;

    /*
     * 此函数 不删除了
     * 用于 bug处理
     * 正常逻辑 不使用该函数
     * ps:   用到该函数,准备等死
     */
    public function do_debug($num){

        if( self::get_state() == 1 ){
            $this->info['cons'] = $num;
            $this->save();
        }

    }

}
