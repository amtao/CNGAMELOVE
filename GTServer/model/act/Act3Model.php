<?php
//
require_once "ActFlBaseModel.php";
/*
 * 地图关卡BOSS 门客出战列表
 */
class Act3Model extends ActFlBaseModel
{
	public $atype = 3;//活动编号
	
	public $comment = "关卡BOSS出战列表";
	public $b_mol = "user";//返回信息 所在模块
	public $b_ctrl = "pvb";//返回信息 所在控制器

    public function cone_back($hid){
        $hData = $this->info[$hid];
        $c = empty($hData)?0:$hData['b'];
        $cost = floor(pow(1.2, $c)*100);
        Master::sub_item($this->uid,KIND_ITEM,1, $cost);
        parent::cone_back($hid, 10000, 0);
    }

    public function cone_back_all(){
        parent::cone_back_all();
    }
}
