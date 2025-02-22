<?php 
require_once "ActBaseModel.php";
/*
 * 公会-活跃度
 */

class Act760Model extends ActBaseModel{
    
    public $atype = 760;//活动编号

	public $comment = "公会活跃度";
	public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "active";//返回信息 所在控制器
    
    public $_init = array(
        'score' => 0,//公会活跃值
        'get' => array(),
    );

    //活跃度
    public function add_score($value){
        if(empty($this->info['score'])){
            $this->info['score'] = 0;
        }
        $this->info['score'] += $value;
        $this->save();
    }

    public function get_rwd($id){
        if(empty($this->info['get'])){
            $this->info['get'] = array();
        }
        $clubDailyRwdCfg = Game::getcfg('club_dailyRwd');
        $hasPick = count($this->info['get']);
        $baseAward = count($clubDailyRwdCfg);
        if($hasPick == $baseAward){
            Master::error(CLUB_ACTIVITE_ALL_AWARD_PICK);
        }
        $tempArr = array();
        if(empty($id)){//一键领取
            foreach($clubDailyRwdCfg as $k => $v){
                if(in_array($k,$this->info['get'])){
                   continue;
                }
                if($this->info['score'] >= $v['need']){
                    array_push($tempArr,$k);
                }
            }
        }else{ 
            if(in_array($id,$this->info['get'])){
                Master::error(CLUB_ACTIVITE_AWARD_PICK);
            }
            if($this->info['score'] >= $clubDailyRwdCfg[$id]['need']){
                array_push($tempArr,$id);
            }
        }
        foreach($tempArr as $index => $aid){
            array_push($this->info['get'],$aid);
            Master::add_item3($clubDailyRwdCfg[$aid]['rwd']);
        }
        $this->save();
    }




    public function make_out(){
        $this->outf = $this->info;
    }

}
