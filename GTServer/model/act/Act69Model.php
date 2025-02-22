<?php
require_once "ActBaseModel.php";
/*
 * 加微信加QQ
 */
class Act69Model extends ActBaseModel
{
	public $atype = 69;//活动编号
	
	public $comment = "加微信加QQ";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "wxqq";//返回信息 所在控制器
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		$gq_status = Game::get_peizhi('gq_status');   //开关
        $UserModel = Master::getUser($this->uid);
		$wxqq_cfg = Game::get_peizhi("wxqq_{$UserModel->info['platform']}");    //群号
        if (empty($wxqq_cfg)) {
            $wxqq_cfg = Game::get_peizhi("wxqq");
        }
		//0:未开放 1:只开放加微信 2:只开放加Q群 3:开放加微信和加QQ
		$stype = empty($gq_status['wxqq'])?0:intval($gq_status['wxqq']);
		$outf = array();//存放输出信息
		switch($stype){
			case 1: //1:只开放加微信
				$outf['wx'] = empty($wxqq_cfg['wx'])?array():$wxqq_cfg['wx'];
				break;
			case 2: //2:只开放加Q群
				$outf['qq'] = empty($wxqq_cfg['qq'])?array():$wxqq_cfg['qq'];
				break;
			case 3: //3:开放加微信和加QQ
				$outf = empty($wxqq_cfg)?array():$wxqq_cfg;
				break;
		}
		$this->outf = $outf;
	}
	
	
}