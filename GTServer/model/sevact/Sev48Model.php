<?php
/*
 * 子嗣全服提亲列表
 */
require_once "SevBaseModel.php";
class Sev48Model extends SevBaseModel
{
    public $comment = "跑马灯-跑脚本记录";
    public $act = 48;//活动标签
    public $_init = array(
        // array('id' =>)  //id 跑脚本记录唯一标识
    );

    /*
	 * 记录脚本ID
	 */
    public function add_id($id){
        $this->info[] = array('id'=>$id);
        $this->save();
    }
    /*
     * 检查是否已经发送
     */
    public function check($id){
        $check = false;
        foreach ($this->info as $value){
            if($value['id'] == $id){
                $check = true;
            }
        }
        return $check;
    }


}
