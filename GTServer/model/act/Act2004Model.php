<?php
require_once "ActBaseModel.php";
/*
 *  羁绊解锁领取奖励
 */
class Act2004Model extends ActBaseModel
{
	public $atype = 2004;//活动编号

	public $comment = "羁绊解锁领取奖励";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "jibanAward";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(

    );

    public function pickAward($id){
        if(empty($this->info['pickInfo'])){
            $this->info['pickInfo'] = array();
        }
        if(in_array($id,$this->info['pickInfo'])){
            Master::error(HAS_PICK_AWARD);
        }
        $jibanCfg = Game::getcfg_info('jiban_unlock_id',$id);
        $Act6001Model = Master::getAct6001($this->uid);
        $lv = $Act6001Model->getHeroJBLv($jibanCfg['hero_id']);
        if($lv < $jibanCfg['yoke_level']){
            Master::error(FETTER_LEVEL_NOT_ENOUGH);
        }
        Master::add_item3($jibanCfg['jiangli']);
        array_push($this->info['pickInfo'],$id);
        $this->save();
    }

    public function make_out(){
		$this->outf = $this->info;
    }
	
}
