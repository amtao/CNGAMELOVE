<?php
require_once "ActBaseModel.php";
/*
 * 购买礼包新增vip经验
 */
class Act8012Model extends ActBaseModel
{
    public $atype = 8012;//活动编号
    public $comment = "购买直购礼包新增vip经验";
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//子类配置

    /*
	 * 初始化结构体
	 */
    public $_init =  array(
        'total' => 0,        //直充总计
        'flow'  => array(),  //流水
    );

    /**
     * 直充记录
     * @param $num
     */
    public function add($num){
        $time = Game::get_now();
        $this->info['total'] += $num;
        $this->info['flow'][$time] = $num;
        $this->save();
    }

    /*
     * 返回活动信息
     * 使这个函数 无效
     */
    public function back_data(){
        return;
    }

}
