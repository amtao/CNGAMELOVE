<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动201
 */
class Act201Model extends ActHDBaseModel
{
	public $atype = 201;//活动编号
	public $comment = "限时奖励-元宝消耗";
	public $b_ctrl = "cash";//子类配置
	public $hd_id = 'huodong_201';//活动配置文件关键字
    protected $_rank_id = 201;


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