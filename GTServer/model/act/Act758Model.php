<?php 
require_once "ActBaseModel.php";
/*
 * 心忆-刷新
 */

class Act758Model extends ActBaseModel{
    
    public $atype = 758;//活动编号

	public $comment = "心忆-刷新次数";
	public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "refresh";//返回信息 所在控制器
    
    public $_init = array(
        'refreshCount' => array(),//总的刷新次数
    );

    public function refreshConsume($suitId,$bSlot){
        if(empty($this->info['refreshCount'][$suitId][$bSlot])){
            $this->info['refreshCount'][$suitId][$bSlot] = 0;
        }
        $refreshConsume = Game::getcfg_param('xinyi_refresh');
        $items = explode('|',$refreshConsume);
        Master::sub_item($this->uid,KIND_ITEM,$items[0],$items[1]);
        $this->info['refreshCount'][$suitId][$bSlot]++;
        $this->save();
    }


    public function make_out(){
        $this->outf = $this->info;
    }

}
