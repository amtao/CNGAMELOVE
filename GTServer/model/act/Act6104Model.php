<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6104Model extends ActBaseModel
{
	public $atype = 6104;//活动编号
	
	public $comment = "御膳房等级";
    public $b_mol = "kitchen";//返回信息 所在模块
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
        $lvs = Game::getcfg("kitchen_level");
        $this->info['exp'] += $exp;
        $lvMax = 1;
        $flag = false;
        foreach ($lvs as $lSys){
            $lvMax = $lSys['level'] > $lvMax?$lSys['level']:$lvMax;
            if ($lSys['exp'] > $this->info['exp']){
                $this->info['level'] = $lSys['level'];
                $flag = true;
                break;
            }
        }
        if (!$flag){
            $this->info['level'] = $lvMax;
        }
        if ($lastLv != $this->info['level']){
            $Redis6104Model = Master::getRedis6104();
            $Redis6104Model->zAdd($this->uid,$this->info['level']);
        }
        $this->save();
    }
}














