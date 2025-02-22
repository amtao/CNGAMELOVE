<?php
/**
 * 帮会战-查看更多日志
 */
require_once "SevKuaCfgBaseModel.php";
class Sev56Model extends SevKuaCfgBaseModel
{
    public $comment = "帮会战-查看更多日志";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "clubKualooklog";//返回信息 所在控制器
    public $act = 56;//活动标签
    public $_server_kua_key = 'clubpk';//指定跨服配置对应的key
    
    /*
	 * 初始化结构体
	 */
	public $_init = array(
		'log' => array(),
		
	);
    
	/*
     * 添加一条信息
     */
    public function add_msg($data){
    	
    	$sid = $data['power'] + $data['fpower'];
    	$data['pktime'] = $_SERVER['REQUEST_TIME'];
		$this->info['log'][$sid] = $data;
		krsort($this->info['log']);
		$data = array_slice($this->info['log'],0,99,true);
		$this->save();
    }
    

	/**
	 * 构造输出
	 */
	public function mk_outf(){
		$this->outof = array();
		$this->outof = $this->info['log'];
		return $this->outof;
	}
	
	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		$data = $this->get_outf();
		$data = array_values($data);
		Master::back_data(0,'club','clubKualooklog',$data);
	}
	
}



