<?php
require_once "ActHDBaseModel.php";
/*
 * 活动260
 */
class Act260Model extends ActHDBaseModel
{
	public $atype = 260;//活动编号
	public $comment = "充值活动-每日充值";
	public $b_mol = "czhuodong";//返回信息 所在模块
	public $b_ctrl = "day";//子类配置
	public $hd_id = 'huodong_260';//活动配置文件关键字
	
	public $ycdc = 7;    //隐藏档次
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//构造输出
		$this->outf = array();
		if( self::get_state() == 0 ){
			return ;
		}
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['brwd']);
		unset($hd_cfg['info']['no']);
        $hd_cfg['info']['news'] = $this->get_news();
		
		//隐藏档次功能
		if(!empty($hd_cfg['info']['ycdc'])){
			$this->ycdc = $hd_cfg['info']['ycdc'];
		}
		if( $this->info['rwd'] >= $this->ycdc ){
			$hd_cfg['rwd'] = array_slice($hd_cfg['rwd'],0,$this->info['rwd']+1);
		}else{
			$hd_cfg['rwd'] = array_slice($hd_cfg['rwd'],0, $this->ycdc );
		}
		
		$this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝
		$this->outf['cons'] = $this->info['cons'];  //活动期间花费多少元宝
		$this->outf['rwd'] = $this->info['rwd'];  //领取到的档次
		
	}
}
