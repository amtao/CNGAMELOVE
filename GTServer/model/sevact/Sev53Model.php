<?php
/**
 * 帮会战-对战日志
 */
require_once "SevBaseModel.php";
class Sev53Model extends SevBaseModel
{
    public $comment = "帮会战-对战日志";
    public $act = 53;//活动标签
    
     /*
	 * 初始化结构体
	 */
	public $_init = array(
		'pklog' => array(),  //pk存放日志
	);
	
	/**
	 * 构造输出
	 */
	public function out_data(){
		$this->outof = array();
		
		$this->outof = array_values($this->info['pklog']);
		
		return $this->outof;
	}
	
	/**
	 * 输出阵容
	 */
	public function out_zr(){
		$this->outof = array();
		
		$this->outof['myclub'] = $this->info['myclub'];
		$this->outof['diclub'] = $this->info['diclub'];
		
		return $this->outof;
	}
	
	
	
	/*
	myname  名字
	mypost  职位
	mypadd  职位加成
	myhid   门客id
	myuse   使用锦囊
	mydx    使用对象   0:自己   1对手
	myadd    使用加成  对手减
	mypower 战力
	myis_win  0:失败   1: 获胜 
	myhuihe   连胜回合数
	myout    0:退场  1:继续  
	
	fname  名字
	fpost  职位
	fpadd  职位加成
	fhid   门客id
	fuse   使用锦囊
	fdx    使用对象   0:自己   1对手
	fadd    使用加成  对手减
	fpower 战力
	fis_win  0:失败   1: 获胜 
	fhuihe   连胜回合数
	fout    0:退场  1:继续  
	 */
	
	
	/**
	 * 添加一条pk日志
	 * @param $cid   帮会id
	 */
	public function add($data,$cid,$fcid){
		$Sev51Model = Master::getSev51($cid);
		$fSev51Model = Master::getSev51($fcid);
		$this->info['pklog'] = $data;
		$this->info['myclub'] = $Sev51Model->get_outf();
		$this->info['diclub'] = $fSev51Model->get_outf();
		$this->save();

        unset($Sev51Model,$fSev51Model);

	}
	
}






