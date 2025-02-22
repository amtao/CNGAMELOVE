<?php
require_once "ActBaseModel.php";
/*
 * 书院学习记录
 */
class Act6103Model extends ActBaseModel
{
	public $atype = 6103;//活动编号
	
	public $comment = "御膳房菜单图鉴";
    public $b_mol = "kitchen";//返回信息 所在模块
    public $b_ctrl = "foods";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'foods' => array(), 
    );

    public function addFood($id){
        $foods = $this->info['foods'];
        if (!in_array($id, $foods)){
            $foods[] = $id;
        }
        $this->info['foods'] = $foods;
        $this->save();
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = $this->info['foods'];
    }
}














