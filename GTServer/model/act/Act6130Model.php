<?php
require_once "ActBaseModel.php";
/*
 * 玩家额外属性
 */
class Act6130Model extends ActBaseModel
{
	public $atype = 6130;//活动编号
	
	public $comment = "玩家额外属性";
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(
                'ep'=> array(
                1 => 0, 2 => 0,
                3 => 0, 4 => 0,
                ),
            );

    /*
     * 保存数据
     * $ep   增加的属性值
     * $type 对应属性
     */
    public function seveEp($ep,$type){
        if (empty($this->info)){
            $this->info=$this->_init;
        }
        $this->info['ep'][$type]+=$ep;
        $this->save();
    }

    /*
     * 获取数据
     */
    public function getEp(){
        $addEp=$this->info['ep'];
        return $addEp;
    }


}














