<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6105Model extends ActBaseModel
{
	public $atype = 6105;//活动编号
	
	public $comment = "书院等级";
    public $b_mol = "school";//返回信息 所在模块
    public $b_ctrl = "level";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'level' => 1,
        'exp' => 0,
    );


    public function addExp($exp){
        $lastLv = $this->info['level'];
        $lvs = Game::getcfg("school_level");
        $this->info['exp'] += $exp;
        $lvMax = 1;
        $flag = false;
        foreach ($lvs as $lSys){
            $lvMax = $lSys['school_lv'] > $lvMax?$lSys['school_lv']:$lvMax;
            if ($lSys['school_exp'] > $this->info['exp']){
                $this->info['level'] = $lSys['school_lv'];
                $flag = true;
                break;
            }
        }
        if (!$flag){
            $this->info['level'] = $lvMax;
        }
        if ($lastLv != $this->info['level']){
            $Redis6105Model = Master::getRedis6105();
            $Redis6105Model->zAdd($this->uid,$this->info['level']);
        }
        $this->save();
    }

}














