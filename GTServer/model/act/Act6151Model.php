<?php
require_once "ActBaseModel.php";
/*
 * 主角头像
 */
class Act6151Model extends ActBaseModel
{
	public $atype = 6151;//活动编号
	
	public $comment = "主角头像";
    public $b_mol = "userhead";//返回信息 所在模块
    public $b_ctrl = "headavatar";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'head' => 0,
        'blank' => 1,
    );

    public function changeHead($head, $blank){
        $Act6150Model = Master::getAct6150($this->uid);
        if (!$Act6150Model->isUnlock($blank))return;
        $this->info['head'] = $head;
        $this->info['blank'] = $blank;
        $this->save();
    }
}

