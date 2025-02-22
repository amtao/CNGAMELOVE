<?php
require_once "ActBaseModel.php";
/*
 * 子嗣席位数量类
 */
class Act6132Model extends ActBaseModel
{
	public $atype = 6132;//活动编号
	
	public $comment = "历练席位";
	public $b_mol = "son";//返回信息 所在模块
	public $b_ctrl = "lilianSeatNum";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//席位数量
		'desk' => 1,
	);
	
	/*
	 * 返回当前席位
	 */
	public function get_seat(){
		return $this->info['desk'];
	}

    /*
     * 检查书桌ID 范围合法
     */
    public function click_id($id = 1){
        if ($id <= 0 || $id > $this->info['desk']){
            Master::error("PRACTICE_ID_ERR_".$id);
        }
    }

    /*
     * 加上席位数量
     * 只能一个个加
     */
	public function add_seat(){
		$this->info['desk'] += 1;
		//所需黄金
		$practice_seat_info = Game::getcfg_info('practice_seat',$this->info['desk']);
		//直接在这里扣钱
		Master::sub_item($this->uid,KIND_ITEM,1,$practice_seat_info['cost']);
		$this->save();
	}

}
