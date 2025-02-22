<?php
require_once "ActBaseModel.php";
/*
 *  卡牌羁绊
 */
class Act6006Model extends ActBaseModel
{
	public $atype = 6006;//活动编号

	public $comment = "卡牌羁绊";
	public $b_mol = "scpoint";//返回信息 所在模块
	public $b_ctrl = "cardFetter";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'cardFetter' => array(),
    );
    
    public function addCardFetter($fetterName){
        $cFetter = $this->info['cardFetter'];
        if(empty($cFetter[$fetterName])){
            $cFetter[$fetterName] = 0;
        }
        $cFetter[$fetterName] += 1;
        $this->info['cardFetter'] = $cFetter;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
        Master::$bak_data['u'][$this->b_mol][$this->b_ctrl] = $this->outf;
    }
}
