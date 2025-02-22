<?php
/*
 * 腊八节活动-boss血量
 */
require_once "SevListBaseModel.php";
class Sev72Model extends SevListBaseModel
{
    public $comment = "腊八节活动-boss血量";
    public $act = 72;//活动标签
    public $hd_id = 'huodong_286';
    public $b_mol = 'LabaDay';
    public $b_ctrl = 'boss';
    public $_init = array(//初始化数据
        /*
         * array(
         * 	'shijian' => '血量'
         * )
         */
    );

    /*
     * 添加伤害
     */
    public function add($hp){
        $time = date('Ymd',time());
        if(empty($this->info[$time])){
            $this->info[$time] = 0;
        }
        $this->info[$time] += $hp;
        $this->save();
    }

    public function back_data(){
        $time = date('Ymd',time());

        if(empty($this->info[$time])){
            $this->info[$time] = 0;
        }

        $boss = $this->info[$time];

        Master::back_data(0,$this->b_mol,$this->b_ctrl,array('boss'=>$boss));
    }

}
