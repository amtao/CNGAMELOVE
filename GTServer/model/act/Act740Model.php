<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴郊游
 */

class Act740Model extends ActBaseModel{
    
    public $atype = 740;//活动编号

	public $comment = "伙伴郊游";
	public $b_mol = "jiaoyou";//返回信息 所在模块
    public $b_ctrl = "jiaoyou";//返回信息 所在控制器
    
    public $_init = array(
        'copyInfo' => array(),//最大关卡
    );

    public function make_out(){
        $this->outf = $this->info;
    }

}
