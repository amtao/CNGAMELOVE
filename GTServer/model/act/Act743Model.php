<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴郊游--守护次数
 * 每周刷新
 */

class Act743Model extends ActBaseModel{
    
    public $atype = 743;//活动编号

	public $comment = "伙伴郊游";
	public $b_mol = "jiaoyou";//返回信息 所在模块
    public $b_ctrl = "jiaoyou";//返回信息 所在控制器
    
    public $_init = array(
        'cashBuy' => 0,//元宝购买次数
    );

    //花费元宝购买次数
    public function cashBuy(){
        if($this->info['cashBuy'] >= 3){
            Master::error(JIAOYOU_GUARD_CASH_BUY_MAX);
        }
        $guajiParam = Game::getCfg_param("jiaoyou_guaji_yuanbao");
        $costArr = explode("|",$guajiParam);
        foreach($costArr as $costs){
            $ct = explode(",",$costs);
            if($ct[0] == ($this->info['cashBuy']+1)){
                Master::sub_item($this->uid,KIND_ITEM,1,$ct[1]);
            }
        }
        $this->info['cashBuy']++;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
