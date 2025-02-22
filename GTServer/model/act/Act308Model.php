<?php
require_once "ActBaseModel.php";
/*
 * 跨服衙门-衙门日志列表挑战记录
 */
class Act308Model extends ActBaseModel
{
    public $atype = 308;//活动编号

    public $comment = "跨服衙门日志列表挑战记录";

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * id
         */
    );

    /*
     * 添加仇人
     */
    public function add($id){
        array_push($this->info,$id);
        $this->save();
    }
    /*
     * 检查是否打过
     */
    public function check($id){
        return in_array($id,$this->info);
    }
}
