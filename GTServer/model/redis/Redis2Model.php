<?php
require_once "RedisBaseModel.php";
/*
 * 关卡排行
 */
class Redis2Model extends RedisBaseModel
{
	public $comment = "关卡排行";
	public $act = 'guanka';//活动标签
	
	public $b_mol = "ranking";//返回信息 所在模块
	public $b_ctrl = "guanka";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	
//	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
//	public $out_num = 100;//常规输出范围 要获取几个
//	public $out_time = 60;//输出缓存过期时间
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		/*
		'id' => 0,  //玩家UID
		'name' => 0,  //名字
		'level' => 0,  //官阶
		'vip' => 0,  //VIP
		'chenghao' => 0,  //称号
		'rid'	=> 0, //排名
		'num'	=> 0, //附加字段  -- 关卡
	*/
	);

	public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1506528000;
        $this->_with_decimal_number = 1000000000;
    }
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		
		//玩家排名
		$fuidInfo['rid'] = $rid;
		
		//关卡小关数据
		$fuidModel = Master::getUser($member);
		$fuidInfo['num'] = $fuidModel->info['smap'];
		
		return $fuidInfo;
	}
	/*
	 * 返回排行信息
	 */
	public function back_data_my($uid){
		$rid = parent::get_rank_id($uid);
		Master::back_data(0,$this->b_mol,"selfRid",array("guanka"=>$rid));
	}
	
}

/*
 * 通用联盟 势力 排行榜
 */
