<?php
/**
 * 帮会战-伤害排行
 */
require_once "SevBaseModel.php";
class Sev57Model extends SevBaseModel
{
    public $comment = "帮会战-伤害排行";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "clubKuahit";//返回信息 所在控制器
    public $act = 57;//活动标签
    
    /*
	 * 初始化结构体
	 */
	public $_init = array(
		'isWin' => 0,  //0失败 1:获胜  2:平局
		'servid' => 0, //服务器id
		'cname' => '', //公会名字
		'list' => array(
			/*
			 * name 玩家名字
			 * hh  连胜回合数
			 * hit   伤害
			 */
		),
		'fcid' => 0,
	);
    
	/*
     * 添加一条信息
     */
    public function add($win,$hit,$fcid){
    	$cid = $this->cid;
    	$this->info['isWin'] = $win;
    	$this->info['servid'] = Game::get_sevid_club($cid);
    	$ClubModel = Master::getClub($cid);
    	$this->info['cname'] = $ClubModel->info['name'];
    	
    	$hit1 = array();
    	$k = 100;
    	foreach($hit as $uid => $info){
    		$k ++;
    		$hit1[$info['hit'].$k] = array(
    			'name' => $info['name'],
    			'hh' => $info['hh'],
    			'hit' => $info['hit'],
    		);
            unset($info);
    	}
    	krsort($hit1);
    	$this->info['list'] = array_values($hit1);
    	$this->info['fcid'] = $fcid;
    	$this->save();

        unset($ClubModel);
    	
    }
    

	/**
	 * 构造输出
	 */
	public function mk_outf(){
		$this->outof = array();
		$this->outof = $this->info;
		return $this->outof;
	}
	
}



