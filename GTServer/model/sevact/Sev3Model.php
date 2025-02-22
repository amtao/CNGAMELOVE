<?php
/*
 * 蒙古战斗道具奖励日志
 */
require_once "SevListBaseModel.php";
class Sev3Model extends SevListBaseModel
{
	public $comment = "蒙古战斗道具奖励日志";
	public $act = 3;//活动标签
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "rwdLog";//返回信息 所在控制器
	protected $_max_chat_num = 20;///最大内部保存数量
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	'uid' => 10086
		 *  'bo' => 12,//击败几百第几波
		 *  'itemid' => 33,//获得什么道具
		 * )
		 */
	);
	
    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
		//新版本
    	if(!empty($v_info['user'])){
    		return $v_info['user'];
    	}
    	//兼容旧版本
    	$fuidInfo = Master::fuidInfo($v_info['uid']);
    	$fuidInfo['num'] = $v_info['bo'];
		$fuidInfo['num2'] = $v_info['itemid'];
        return $fuidInfo;
    }
    
    
	/*
	 * 添加一条奖励信息
	 */
	public function add($uid,$bo,$itemid){
		
		$user = Master::fuidInfo($uid);
		$user['num'] = $bo;
		$user['num2'] = $itemid;
		
		$data = array(
			'user' => $user,
			'uid' => $uid,
			'bo' => $bo,
			'itemid' => $itemid,
		);
		
		parent::list_push($data);
		
	}
	
}
