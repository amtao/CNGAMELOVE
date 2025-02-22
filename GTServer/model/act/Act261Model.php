<?php
require_once "ActHDBaseModel.php";
/*
 * 活动261
 */
class Act261Model extends ActHDBaseModel
{
	public $atype = 261;//活动编号
	public $comment = "充值活动-累计充值";
	public $b_mol = "czhuodong";//返回信息 所在模块
	public $b_ctrl = "total";//子类配置
	public $hd_id = 'huodong_261';//活动配置文件关键字
	
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
		$this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝
		$this->outf['cons'] = $this->info['cons'];  //活动期间花费多少元宝
		$this->outf['rwd'] = $this->info['rwd'];  //领取到的档次
		
	}



    /*
     * 此函数 不删除了
     * 用于 bug处理
     * 正常逻辑 不使用该函数
     * ps:   用到该函数,准备等死
     */
    public function do_debug($num){

        if( self::get_state() == 1 ){
            $this->info['cons'] = $num;
            $this->save();
        }

    }
	
}
