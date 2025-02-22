<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴郊游--守护次数
 * 每天刷新
 */

class Act741Model extends ActBaseModel{
    
    public $atype = 741;//活动编号

	public $comment = "伙伴郊游";
	public $b_mol = "jiaoyou";//返回信息 所在模块
    public $b_ctrl = "jiaoyou";//返回信息 所在控制器
    
    public $_init = array(
        'defendCount' => 0,//守护次数
    );

    public function addCount(){
        $maxCount = Game::getcfg_param("jiaoyou_guaji_cishu");
        $yueKaCount = 0;
        $Act68Model = Master::getAct68($this->uid);
        if($Act68Model->find_ka(1) == 1){
            $yueKaCount = Game::getcfg_param("jiaoyou_guaji_yueka");
        }
        $Act743Model = Master::getAct743($this->uid);
        $cashCount =  $Act743Model->info['cashBuy'];
        $totalCount = $maxCount + $yueKaCount + $cashCount;
        if($this->info['defendCount'] >= $totalCount ){
            Master::error(JIAOYOU_GUARD_MAX);
        }
        $this->info['defendCount']++;
        $this->save();
        $Act742Model = Master::getAct742($this->uid);
        $Act742Model->info['weekdefendCount']++;
        $Act742Model->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
