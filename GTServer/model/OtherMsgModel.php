<?php
//异步信息 待发队列
class OtherMsgModel
{
	//返回客户端信息
	//最终是要跟这个结构体组合的
	//消息刷新
	//先用这个结构体 / 再覆盖为 用户结构体
	//这边不定义结构体 结构体 外部传进来
	//由于是覆盖数组 原则上 不进行 U 数据的结合 以免歧义
	/*
	public static $bak_data = array(
		's' => 1,
		'a' => array(),//逻辑返回数据
		'u' => array(),//更新返回数据
	);
	*/
	
	private static $data = array(
		//uid => back_data
	);
	
	private static function get_key($uid){
		return $uid.'_backdata';
	}
	
	/*
	 * 获取某个用户的待发信息
	 */
	private static function get_data($uid){
		if (!isset(self::$data[$uid])){
			$cache = Common::getCacheByUid($uid);
			$ubdata = $cache->get(self::get_key($uid));
			if ($ubdata == false) {
				$ubdata = array();
			}
			self::$data[$uid] = $ubdata;
		}
		return self::$data[$uid];
	}
	/*
	 * 保存某个用户的待发信息
	 */
	private static function save_data($uid,$data){
		self::$data[$uid] = $data;
		$cache = Common::getCacheByUid($uid);
		$cache->set(self::get_key($uid),self::$data[$uid]);
	}
	
	public static function back_data($uid,$mol,$ctrl,$data,$is_u){
		$u_data = self::get_data($uid);
		//本用户信息更新
		if (!is_array($data)){
			Master::error('other_back_data_err:'.$mol.'_'.$ctrl);
		}
		$bag_key = 'a';
		if ($is_u){//如果是要更新
			$bag_key = 'u';
		}elseif(empty($data)){
			//如果是空数组 则清空本项
			$u_data[$bag_key][$mol][$ctrl] = array();
		}
		$is_int = false;
		foreach($data as $k => $v){
			//如果是数字下标 则累加 否则 按KEY覆盖
			if (is_int($k)){
				//刷新异步列表 数组不累加 直接覆盖
				$is_int = true;
				break;
				//$u_data[$bag_key][$mol][$ctrl][] = $v;
			}else{
				$u_data[$bag_key][$mol][$ctrl][$k] = $v;
			}
		}
		//刷新异步列表 数组不累加 直接覆盖
		if ($is_int){
			$u_data[$bag_key][$mol][$ctrl] = $data;
		}
		//保存
		self::save_data($uid,$u_data);
	}
	
	/*
	 * 输出用户信息
	 */
	public static function output_data($uid){
		$data = self::get_data($uid);
		if(!empty($data)){
			self::clear_data($uid);
		}
		return $data;
	}
	/*
	 * 清除用户待发序列
	 */
	public static function clear_data($uid){
		self::$data[$uid] = array();
		$cache = Common::getCacheByUid($uid);
		$cache->delete(self::get_key($uid));
	}
}
