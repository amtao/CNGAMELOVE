<?php
/*
 * 广告检查类
 */
require_once "AModel.php";

require_once LIB_DIR . '/SocketBuffer/BigEndianBytesBuffer.php';

class AdCheckModel extends AModel
{
	/*
	 * 频道,发言 
	 * 一个静态函数搞完
	 * 返回 true 合法发言 false 非法发言
	 */
    public function click($channel,$msg){
		//IP
		$address = '123.207.72.103';
		//端口
		$service_port = 6008;
		
		//创建 TCP/IP socket  
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);  
		if ($socket < 0) {  
			Master::error("ad failed".__LINE__);
		} else {
			//echo "socket创建成功\n";
		}
		
		//设置为指定超时时间
		$timeout = array('sec'=>2,'usec'=>0);
		socket_set_option($socket,SOL_SOCKET,SO_RCVTIMEO,$timeout);
        socket_set_option($socket,SOL_SOCKET,SO_SNDTIMEO,$timeout);
		
		//连接服务器
		$result = socket_connect($socket, $address, $service_port);  
		if ($result < 0) {
			Master::error("ad failed".__LINE__.$result.socket_strerror($result));
		} else {  
			//echo "SOCKET连接成功.\n";  
		}
		
		//构造发送数据
		//每个字段进行格式强转 , 以免格式异常
		$data = array (
			'channel' => strval($channel),	//聊天频道名
			'chat_content' => strval($msg),	//用户发送的聊天信息
			'developer_id' => strval('youdongwangluo'),	//游戏开发商ID，请设置为公司拼音或英文名
			'game_id' => strval(10),	//游戏ID，请设置为游戏拼音或者游戏英文名
			'msg_code' => intval(0),	//消息编码，值为0
			'serial_no' => intval(0),	//预留接口，现在设置为0即可
			'server_id' => strval(GAME_MARK.'_'.$this->_serverID),	//游戏服务器ID，如果分服务器请设置，如果不分则设置为1
			'time_stamp' => strval(Game::get_now() * 1000),	//用户发出聊天信息的Unix时间戳，单位为毫秒。如果用户程序时间戳为秒，则需乘以1000转成毫秒。
			'user_id' => strval(GAME_MARK.'_'.$this->uid),	//游戏内用户的ID，不能不填写，否则监控程序会出错。
			'user_name' => strval($this->uid),	//用户昵称
		);
		
		//构造发送包
		$json_data = json_encode($data);
		$buffer = new BigEndianBytesBuffer('');
		$buffer->writeString($json_data);
		//读取为变量
		$send_str = $buffer->readAllBytes();
		
		//开始发送
		$s_len = socket_write($socket, $send_str, strlen($send_str));  
		//接收返回数据
		$out = socket_read($socket, 2048);
        socket_close($socket);
        //返回超时判错?
		
		//解析返回数据
		$buffer_r = new BigEndianBytesBuffer('');
		$buffer_r->writeBytes($out);
		$tmp = $buffer_r->readString();
		$b_data = json_decode($tmp,true);

		
		/*
		 * {
		 * "msg_code":1,//消息编码
		 * "ret_code":0,//0表示不是广告，1表示是广告，2表示模型已失效
		 * "serial_no":0,//预留无用
		 * "user_id":"10001",//回调UID
		 * "advid_rate":0,//这个用户是广告ID的概率
		 * }
		 */
		
		//如果这个用户是广告ID 就直接封号
		if ($b_data['advid_rate'] >= 0.85){
			//封号
			//$b_data['user_id']
		}
		
		if ($b_data['ret_code'] == 0){
			//不是广告
			return true;
		}else{
			//是广告
			return false;
		}
    }
    
}