<?php
require_once "RedisKuaCfgBaseModel.php";
/*
 * 跨服大理寺 - 小分区内积分排名
 */
class Redis306Model extends RedisKuaCfgBaseModel
{
	public $comment = "跨服大理寺-小分区内积分排名";
	public $act = 'huodong_300_score';//活动标签
	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
	public $out_num = 100;//常规输出范围 要获取几个
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "scoreRank";//返回信息 所在控制器
	public $hd_id = "huodong_300";
	protected $_with_decimal_sort = true;//加小数排序
	
	public function __construct($key){
	    $Sev61Model = Master::getSev61();
	    foreach ($Sev61Model->info['list'] as $sid_arr){
	        $this->_server_kua_cfg[] = array(0,0,$sid_arr);
	    }
	    $this->_with_decimal_denominator = time() - 1508083200;
	    parent::__construct($key);
	}
	
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		/*
		'uid' => 0,  //用户id  => 分数
	*/
	);

    /*
     * 返回第一名信息
     * */
    public function back_data_first(){
        $fuid = $this->get_member(1);
        if(empty($fuid))return array();
        $Sev_Cfg = Common::getSevidCfg(Game::get_sevid($fuid));//子服ID
        Common::loadModel('ServerModel');
        $serverInfo = ServerModel::getServInfo($Sev_Cfg['he']);
        $serverZh = $serverInfo['name']['zh'];
        $serverName = explode('|',$serverZh);
        //获取公共基础信息
        $cinfo = Master::fuidData($fuid);
        $cinfo['sevname'] = $serverName[1];
        return $cinfo;
    }

	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		//玩家排名
		$fuidInfo['rid'] = $rid;
		//获取分值
		$fuidInfo['num'] = intval($this->zScore($member));
		
		return $fuidInfo;
	}
	
	
	/*
	 * 返回我的积分信息
	 */
	public function back_data_my($uid){
		//返回我的总伤害 //返回我的排名
		$fuidInfo = Master::fuidInfo($uid);
		$data = array(
		    'myName' => $fuidInfo['name'],
			'myScore' => intval($this->zScore($uid)),
			'myScorerank' => $this->get_rank_id($uid),
		);
		Master::back_data(0,$this->b_mol,'myScore',$data);
		
	}
	
}