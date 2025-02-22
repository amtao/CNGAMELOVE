<?php
require_once "ActBaseModel.php";
/*
 * 主角换装
 */
class Act8500Model extends ActBaseModel
{
	public $atype = 8500;//活动编号
	
	public $comment = "记录卡牌特效开关";
    public $b_mol = "card";//返回信息 所在模块
    public $b_ctrl = "cardSpecialEffects";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(
        'cards' => array(),
    );

    public function updCardSpecialEffects($cardId, $status){

        $this->info["cards"][$cardId] = $status;
        $this->save();
    }

}














