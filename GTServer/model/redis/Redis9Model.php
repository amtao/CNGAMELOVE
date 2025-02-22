<?php
require_once "RedisBaseModel.php";
/*
 * 封号排行
 */
class Redis9Model extends RedisBaseModel
{
	public $comment = "封号排行";
	public $act = 'fsb_openid';//活动标签
	protected $_server_type = 3;
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		//openid => time
	);

	/**
	 * 添加被封设备
	 * @param $uid
	 */
	public function add_sb($uid){
		$openid = Common::getOpenid($uid);
		return $this->zAdd($openid,Game::get_now());
	}

	/**
	 * 添加被封设备
	 * @param $openid
	 */
	public function del_sb($openid){
		return $this->zDelete($openid);
	}

	/**
	 * 判断是否存在
	 * @param $uid
	 * @return array
	 */
	public function is_exist($uid){
		$openid = Common::getOpenid($uid);
		return $this->zScore($openid);
	}

	/**
	 * 获取列表信息
	 * @return mixed
	 */
	public function getList(){
		return $this->zRevRange();
	}

	/**
	 * 删除指定区间的数据
	 * @param $min
	 * @param $max
	 * @return mixed
	 */
	public function delRandData($min,$max){
		return $this->zRemRangeByScore($min,$max);
	}
}
