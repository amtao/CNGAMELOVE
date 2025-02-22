<?php
require_once "ActBaseModel.php";
/*
 * 徒弟历练
 */
class Act766Model extends ActBaseModel
{
    public $atype = 766;//活动编号

    public $comment = "徒弟历练-推荐方向";
    public $b_mol = "son";//返回信息 所在模块
    public $b_ctrl = "tdDrec";//返回信息 所在控制器

    public $_init = array(
        'direction' => 0,//今日推荐方向
    );

    public function getTodayDirection(){
        $direction = 0;
        if(empty($this->info['direction'])){
            $this->info['direction'] = 0;
            $direction = rand(1,5);  
        }
        $this->info['direction'] = empty($this->info['direction']) ? $direction : $this->info['direction'];
        $this->save();
    }

}