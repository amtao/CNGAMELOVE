<?php 
require_once "ActBaseModel.php";
/*
 * 华服等级领取奖励
 */

class Act755Model extends ActBaseModel{
    
    public $atype = 755;//活动编号

	public $comment = "华服等级领取奖励";
	public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "pickAward";//返回信息 所在控制器
    
    public $_init = array(
        'pickLv' => 0,
    );

    public function getAward(){
        if(empty($this->info['pickLv'])){
            $this->info['pickLv'] = 0;
        }
        $pickId = $this->info['pickLv']+1;
        $huafusCfg = Game::getcfg('huafu');
        $maxlv = end($huafusCfg)['lv'];
        if($pickId > $maxlv){
            Master::error(CLOTHE_LEVEL_MAX);
        }
        $huafuCfg = $huafusCfg[$pickId];
      
        $Act6140Model = Master::getAct6140($this->uid);
        $huafuValue = $Act6140Model->info['score'];
        if($huafuValue < $huafuCfg['score']){
            Master::error(CLOTHE_SCORE_NOT_ENOUGH);
        }
        Master::add_item3($huafuCfg['rwd']);
        $this->info['pickLv']++;
        $this->save();
    }


    public function make_out(){
        $this->outf = $this->info;
    }

}
