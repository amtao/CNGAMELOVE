<?php
require_once "ActBaseModel.php";
/*
 * 免费给一次鱼饵
 */
class Act738Model extends ActBaseModel
{
	public $atype = 738;//活动编号
	
	public $comment = "免费鱼饵";
	public $b_mol = "invite";//返回信息 所在模块
	public $b_ctrl = "freeYur";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'isGive' => 0,//是否给过鱼饵
    );

    public function giveYur(){
        if($this->info['isGive'] == 1){
            return;
        }
        $first = Game::getcfg_param("game_firstgift");
        $yur = explode("|",$first);
        Master::add_item($this->uid,KIND_ITEM,$yur[0],$yur[1]);
        $this->info['isGive'] = 1;
        $this->save();
    }
    
}