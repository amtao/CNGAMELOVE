<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6107Model extends ActBaseModel
{
	public $atype = 6107;//活动编号
	
	public $comment = "科举等级";
    public $b_mol = "daily";//返回信息 所在模块
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
        $lvs = Game::getcfg("exam_lv");
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
            $Redis6105Model = Master::getRedis6105();
            $Redis6105Model->zAdd($this->uid,$this->info['level']);
        }
        $this->save();
    }

}














