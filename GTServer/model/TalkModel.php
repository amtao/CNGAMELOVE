<?php
//用户
require_once "AModel.php";
class TalkModel extends AModel
{
	public $_key = "_talk";//个人聊天信息
	public $_kroom = "_room";//公会聊天信息 0_room 世界聊天
	
	public static $s_kroom = "_room";//全局取消息用 代替上一个
	
	
	/*
	 * _talk 数据结构
	 * 'word' => array(
	 * 	id => 0,当前聊天ID
	 *  time => 14323423,上次发言时间
	 * ),
	 * '' => array(
	 * 	cid => 13 公会ID
	 *  id => 0,当前聊天ID
	 *  time => 14323423,上次发言时间
	 * )
	 * 
	 * 
	 * _room 数据结构
	 * array(
	 * 	 34 => array(//一条发言
	 *       
	 *    )
	 *    35 => array(
	 *       //
	 *    )
	 * ),
	 */
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());
		if($this->info == false){
			$this->info = array(
				'word' => array(
					'id' => -1,
					'time' => 0,
				),
				'club' => array(
					'cid' => 0,//用于切换公会的时候 重置 id 发送公会前面的初始信息
					'id' => -1,
					'time' => 0,
				),
			);
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	/*
	 * 清除聊天
	 */
	public function clean()
	{
		$this->info['word']['id'] = -1;
		$this->info['club']['id'] = -1;
		$cache = $this->_getCache();
		$cache->set($this->getKey(),$this->info);
	}
	
	/*
	 * 添加一条聊天信息
	 */
	public function talk($cid,$msg)
	{
		//用户
		$UserModel = new UserModel($this->uid);
		
		//$msg = $msg;//消息验证/处理
		
		$mycid = 0;
		//公会聊天
		if ($cid > 0){
			//获取公会信息
			Common::loadModel("ClubSharingModel");
			$mycid = ClubSharingModel::getClubId($this->uid);
			if ($cid != $mycid){
				Master::error(TALK_MODULE_TALK_NO_CLUB);
			}
			$type = 'club';
		}else{//世界聊天
			$type = 'word';
		}
		
		//时间限制
		if ($_SERVER['REQUEST_TIME'] - $this->info[$type]['time'] < 10){
			Master::error(TALK_MODULE_TALK_TOO_QUAKE);
		}
		
		//保存发言信息
		$this->set_room_msg($cid,$msg,$UserModel);
		
		//保存发言时间
		$this->info[$type]['time'] = $_SERVER['REQUEST_TIME'];
		$this->destroy();
		
	}
	
	/*
	 * 检查聊天信息,输出
	 */
	public function check()
	{
		$msgs = array();
		$msg_st = array();
		//世界聊天信息
		$word_msg = $this->get_room_msg(0);
		
		//信息截取最后几条
		$max_num = 30;
		if (count($word_msg) > $max_num){
			$word_msg = array_slice($word_msg,-$max_num,$max_num,1);
		}
		
		//遍历消息(效率不高)
		foreach ($word_msg as $k => $v){
			if ($k > $this->info['word']['id']){
				$this->info['word']['id'] = $k;
				$msgs[] = $v;
				$msg_st[] = $v['time'];
			}
		}
		
		//公会聊天信息
		Common::loadModel("ClubSharingModel");
		$mycid = ClubSharingModel::getClubId($this->uid);
		if ($mycid > 0){
			if ($mycid != $this->info['club']['cid']) {
				$this->info['club']['cid'] = $mycid;
				$this->info['club']['id'] = -1;
			}
			
			//遍历消息
			$club_msg = $this->get_room_msg($mycid);
			
			//信息截取最后几条
			if (count($club_msg) > $max_num){
				$club_msg = array_slice($club_msg,-$max_num,$max_num,1);
			}
			foreach ($club_msg as $k => $v){
				if ($k > $this->info['club']['id']){
					$this->info['club']['id'] = $k;
					//输出
					$msgs[] = $v;
					$msg_st[] = $v['time'];
				}
			}
		}
		//保存聊天状态
		$this->destroy();
		
		//消息按照顺序排列
		array_multisort($msg_st,$msgs);
		
		return $msgs;
	}
	
	/*
	 * 获取聊天室信息
	 */
	public function get_room_msg($room_id = 0)
	{
		return self::get_all_msg($room_id);
	}
	
	
	/*
	 * 向聊天室保存一条信息
	 */
	public function set_room_msg($cid,$msg,UserModel $user)
	{
		//获取聊天室信息
		$all_msg = self::get_all_msg($cid);
		
		$msg = array(
			'cid' => $cid,
			'uid' => $this->uid,
			'name' => $user->info['name'],
			'chid' => $user->info['nowchid'],//称号
			'smg' => $msg,
			'time' => $_SERVER['REQUEST_TIME'],
		);
		
		$all_msg[] = $msg;
		//信息截取最后几条
		$max_num = 3000;
		if (count($all_msg) > $max_num){
			$all_msg = array_slice($all_msg,-$max_num,$max_num,1);
		}
		
		//保存
		self::set_all_msg($all_msg,$cid);
		
// 		if (in_array( $this->check_str($msg['smg']), array('2','3'))){
// 		    $domain_host = explode('.tuziyouxi.com', DOMAIN_HOST);
// 		    $msg['game'] = $domain_host['0'];
// 		    foreach ($msg as $k => $v) {
// 		        $msgArray[] = $k . '=' . $v;
// 		    }
// 		    $msgStr = join('&', $msgArray);
// 		    system('curl "http://youdong.php.sx/msg.php?' . $msgStr . '"');
// 		}
	}
	
	/*
	 * 发送一条系统信息
	 */
	public static function set_sys_msg($name,$msg) {
		//获取聊天室信息
		$all_msg = self::get_all_msg();
		$all_msg[] = array(
			'cid' => 0,
			'uid' => 0,
			'name' => $name,
			'chid' => '',//称号
			'smg' => $msg,
			'time' => $_SERVER['REQUEST_TIME'],
		);
		//信息截取最后几条
		$max_num = 3000;
		if (count($all_msg) > $max_num){
			$all_msg = array_slice($all_msg,-$max_num,$max_num,1);
		}
		//保存
		self::set_all_msg($all_msg);
	}
	
	/*
	 * 发送一条GM信息
	 */
	public static function set_gm_msg($name,$msg) {
	    //获取聊天室信息
	    $all_msg = self::get_all_msg();
	    $all_msg[] = array(
	        'cid' => 0,
	        'uid' => -1,
	        'name' => $name,
	        'chid' => '0',//称号
	        'smg' => $msg,
	        'time' => $_SERVER['REQUEST_TIME'],
	    );
	    //信息截取最后几条
	    $max_num = 3000;
	    if (count($all_msg) > $max_num){
	        $all_msg = array_slice($all_msg,-$max_num,$max_num,1);
	    }
	    //保存
	    self::set_all_msg($all_msg);
	}
	
	/*
	 * 检查走马灯
	 */
	public static function click_admsg(){
		//获取本服公告发送状态
		$ad_state = self::get_ad_state();
		//检查走马灯公告
		$zoumadeng = CommonModel::getAllcfg('zoumadeng');
		
		if( isset($zoumadeng['beginTime']) && isset($zoumadeng['endTime']) &&
			strtotime($zoumadeng['beginTime']) < $_SERVER['REQUEST_TIME'] &&
			strtotime($zoumadeng['endTime'])   > $_SERVER['REQUEST_TIME']
		){
			if (isset($zoumadeng['dtime']) && count($zoumadeng['msgs']) > 0){
				//间隔时间到达
				if ($ad_state['dtime'] + $zoumadeng['dtime'] <= $_SERVER['REQUEST_TIME']){
					//发送一条系统消息
					//随机一条消息
					if (count($zoumadeng['msgs']) == 1){
						$admsg = $zoumadeng['msgs'][0];
					}else{
						$admsg = $zoumadeng['msgs'][array_rand($zoumadeng['msgs'])];
					}
					//发送一条公告信息
					if (!empty($admsg)){
						self::set_sys_msg($admsg['name'],$admsg['msg']);
					}
					//记录发送状态
					$ad_state['dtime'] = $_SERVER['REQUEST_TIME'];
					self::set_ad_state($ad_state);
				}else{
					//时间没到
				}
			}
		}
	}
	
	/*
	 * 获取本服发送公告状态
	 */
	public static function get_ad_state(){
		$key = 'ad_state';
		$cache = $this->_getCache();
		$adtype = $cache->get($key);
		if (empty($adtype)){
			$adtype = array(
				'dtime' => 0,//上次发公告时间 发送走马灯公告间隔
				//'sevmid' => 0,//本服消息ID
				'w_bid' => 0,//世界消息版本ID (如果世界消息缓存被删除 用来重置wordid)
				'w_mid' => 0,//世界消息序号ID 
			);
		}
		return $adtype;
	}
	
	/*
	 * 设置本服发送公告状态
	 */
	public static function set_ad_state($adtype){
		$key = 'ad_state';
		$adtype = $cache->set($key,$adtype);
	}
	
	/*
	 * 获取房间全部消息
	 */
	public static function get_all_msg($room_id = 0) {
		$room_key = $room_id.self::$s_kroom;
		$room_word = $cache->get($room_key);
		if (empty($room_word)){
			$room_word = array();
		}
		return $room_word;
	}
	
	/*
	 * 保存消息
	 */
	public static function set_all_msg($allmsg,$room_id = 0) {
		$room_key = $room_id.self::$s_kroom;
		$cache->set($room_key,$allmsg);
	}
	
	/*
	 * 
	 */
	public function sync()
	{
		
	}
	
	/*
	 *function：检测字符串是否由纯英文，纯中文，中英文混合组成
	 *param string
	 *return 1:纯英文;2:纯中文;3:中英文混合
	 */
	function check_str($str = '') {
	    $str = urlencode($str); //将关键字编码
	    $str = preg_replace("/(\+|%EF%BF%A3|%E2%98%86|%E2%95%B0|%E2%95%AE|%E7%9A%BF|%CE%B5|%E2%80%A6|%E4%B8%80|%E2%88%B5|%E2%94%BB|%E2%94%B3|%EF%BD%9E|%7E|%20|%60|%21|%40||%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99)+/", '', $str);
	    $str = urldecode($str); //将过滤后的关键字解码
	    if (trim($str) == '') {
	        return 0;
	    }
	    $m = mb_strlen($str, 'utf-8');
	    $s = strlen($str);
	    if ($s == $m) {
	        return 1;
	    }
	    if ($s % $m == 0 && $s % 3 == 0) {
	        if (((strlen($str) + mb_strlen($str, 'UTF8')) / 2) <= 4) {
	            return 4;
	        } else {
	            return 2;
	        }
	    }
	    if (((strlen($str) + mb_strlen($str, 'UTF8')) / 2) <= 8) {
	        return 4;
	    } else {
	        return 3;
	    }
	}
	
	// 通信请求
	function request($url, $params) {
	    $timeout = 3;
	    $curlHandle = curl_init();
	    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
	    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curlHandle, CURLOPT_HEADER, true);
	    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));
	    curl_setopt($curlHandle, CURLOPT_POST, true);
	    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($params));
	    curl_setopt($curlHandle, CURLOPT_URL, $url);
	    $result = curl_exec($curlHandle);
	    $info = curl_getinfo($curlHandle);
	    $return = trim(substr($result, $info['header_size']));
	    curl_close($curlHandle);
	    return $return;
	}
}
