<?php
require_once "ActBaseModel.php";
/*
 * 公告
 */
class Act33Model extends ActBaseModel
{
	public $atype = 33;//活动编号
	public $comment = "公告";
	public $b_mol = "notice";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	
	/*
	 * 构造输出结构体
	 */
	public function getGG(){
        $UserModel = Master::getUser($this->uid);
        $cfg = Game::get_peizhi("gonggao_{$UserModel->info['platform']}");
        if (empty($cfg)) {
            $cfg = Game::get_peizhi('gonggao');
            if (empty($cfg)){
                $cfg = array();
            }
        }
	    Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$cfg);
	}
}
















