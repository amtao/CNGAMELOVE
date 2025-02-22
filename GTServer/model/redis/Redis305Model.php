<?php
require_once "RedisKuaCfgBaseModel.php";
/*
 * 跨服大理寺 - 各个服务器积分排行
 */
class Redis305Model extends RedisKuaCfgBaseModel
{
	public $comment = "跨服大理寺 - 跨服服务器积分排行";
	public $act = 'huodong_300_sever';//活动标签
	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
	public $out_num = 100;//常规输出范围 要获取几个
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "severRank";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	public $hd_cfg;
	public $hd_id = "huodong_300";
	
	public function __construct($key){
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    $Sev61Model = Master::getSev61();
	    $this->_with_decimal_denominator = time() - 1508083200;
		if($Sev61Model->info['list']){
			foreach ($Sev61Model->info['list'] as $sid_arr){
				$this->_server_kua_cfg[] = array(0,0,$sid_arr);
			}
			parent::__construct($key);
		}
	}
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		/*
		'sid' => 0,  //服务器id  => 分数
	*/
	);
	/*
	 * 获取除自己的一个服务器id
	 * */
	public function geRandtSevid($sid){
	    $sid_score = $this->azRange(1,0);
	    
	    // 查找和删掉自己
 	    unset($sid_score[array_search($sid,$sid_score)]);
	    
	    if(empty($sid_score)){
	        return 0;
	    }
	    return $sid_score[array_rand($sid_score,1)];
	}
	
	//获取个人信息
	public function getMember($member,$rid){
		$fuidInfo['sid'] = $member;
		$SevCfgObj = Common::getSevCfgObj($member);
		$hid = $this->hd_cfg['info']['id'].'_'.$SevCfgObj->getHE();
		$Redis307Model = Master::getRedis307($hid);
		$uid = $Redis307Model->get_member(1);
		//玩家信息
		$Info = Master::fuidInfo($uid);
		$fuidInfo['name'] = $Info['name'];
		//服务器排名
		$fuidInfo['rid'] = $rid;
		//获取分值
		$fuidInfo['num'] = intval($this->zScore($member));
		
		return $fuidInfo;
	}
	
	/*
	 * 返回我的积分信息
	 */
	public function back_data_my($sevid){
		//返回我的总伤害 //返回我的排名
		$data = array(
		    'myName' => $sevid.'区',
			'myScore' => intval($this->zScore($sevid)),
			'myScorerank' => $this->get_rank_id($sevid),
		);
		Master::back_data(0,$this->b_mol,'severScore',$data);
		
	}
	
	/*
	 * 返回第一名信息
	 * */
	public function back_data_first() {
	    $sid = $this->get_member(1);
	    $data = array(
	        'sid' => $sid,
	        'score' => intval($this->zScore($sid)),
	        'scorerank' => 1
	    );
	    Master::back_data(0,$this->b_mol,'firstScoreRank',$data);
	}
}