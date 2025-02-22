<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴拜访
 */

class Act725Model extends ActBaseModel{
    
    public $atype = 725;//活动编号

	public $comment = "伙伴拜访";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "visit";//返回信息 所在控制器
    
    public $_init = array(
        'getAwardCount' => 0, //获得奖励次数
        'joinCount' => array(), //每个伙伴参与次数
        'vipCount' => 0 ,//vip参与次数
    );

    //开始游戏 选择的类型 消耗
    public function startGame($type,$heroId,$isPay = true){
        $Act726Model = Master::getAct726($this->uid);
        $Act726Model->info['qaType'] = $type;
        if(empty($this->info['joinCount'][$heroId])){
            $this->info['joinCount'][$heroId] = 0;
        }
        $Act726Model->info['heroId'] = $heroId;
        //需要花钱-说明为定向问候
        if($isPay){
            $Act726Model->info['isOrient'] = 1;

            $UserModel = Master::getUser($this->uid);
            $vipCfg = Game::getcfg_info("vip",$UserModel->info['vip']);
            if($this->info['vipCount'] < $vipCfg['freevisit']){
                $this->info['vipCount']++;
            }else{
                $this->info['joinCount'][$heroId]++;
                $vtcostCfg = Game::getcfg_info("visit_cost",$this->info['joinCount'][$heroId]);
                Master::sub_item($this->uid,KIND_ITEM,1,$vtcostCfg['cost']);
            }
        }
        $this->save();
        $Act726Model->save();
    }

    //获取奖励的次数--只有前三次有奖励
    public function setAwardCount($answerId){
        $this->info['getAwardCount']++;
        $this->save();
    }

    
    //参与消耗
    public function setJoinCount($answerId){
        $this->info['joinCount']++;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
