<?php 
require_once "ActBaseModel.php";
/*
 * 心忆-刷新
 */

class Act759Model extends ActBaseModel{
    
    public $atype = 759;//活动编号

	public $comment = "心忆-刷新次数";
	public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "sepcial";//返回信息 所在控制器
    
    public $_init = array(
        'sepcial' => array(),//特效
    );

    //装备特效
    public function equipSpecial($clotheId,$isEquip){
        if(empty($this->info['sepcial'][$clotheId])){
            $this->info['sepcial'][$clotheId] = 0;
        }
        if($isEquip){
            $Act756Model = Master::getAct756($this->uid);
            $specialId = $Act756Model->info['extraProp'][1][$clotheId];
            if($specialId == 0){
                Master::error(CLOTHE_SPECIAL_NOT_UNLOCK);
            }
            $this->info['sepcial'][$clotheId] = $specialId;
        }else{
            $this->info['sepcial'][$clotheId] = 0;
        }
        $this->save();
    }


    public function make_out(){
        $this->outf = $this->info;
    }

}
