<?php
//邮件操作
class MailMod extends Base
{
	
	/**
	 * 发送邮件
	 * @param $params
	 */
	public function sendMail($params){
		$daoju = array();
		
		$daoju[] = array (
		    'id' => 71,
		    'count' => 1000,
		    'kind' => 1,
		  );
		$daoju[] = array (
		    'id' => 72,
    		'count' => 1000,
    		'kind' => 1,
		  );
		$daoju[] = array (
		    'id' => 73,
		    'count' => 1000,
		    'kind' => 1,
		  );


		Master::sendMail(10105,'10105title','10105content',1,$daoju);
	}
	/**
	 * 获得邮件
	 * @param $params
	 */
	public function getMail($params){
		$MailModel = Master::getMail($this->uid);
		$MailModel->getMails();
	}
	
	/**
	 * 读取/领取邮件
	 * @param unknown_type $params   邮件id
	 */
	public function redMails($params){
		$mid = Game::intval($params,'mid');
		$MailModel = Master::getMail($this->uid);
		$MailModel->redMails($mid);
	}

	
	/**
	 * 批量领取邮件奖励
	 */
	public function oneKeyPickMails(){
		$MailModel = Master::getMail($this->uid);
		$MailModel->onekeyRedMails();
		$MailModel->getMails();
	}
	
	/**
	 * 删除邮件
	 * @param unknown_type $params   邮件id
	 */
	public function delMail($params){
		$mid = Game::intval($params,'mid');
		$MailModel = Master::getMail($this->uid);
		$MailModel->removeMail($mid);
		$MailModel->getMails();
	}

	/**
	 * 批量删除邮件
	 */
	public function delMails(){
		$MailModel = Master::getMail($this->uid);
		$MailModel->removeMails();
		$MailModel->getMails();
	}
	
	/**
	 * 打开邮件
	 * @param unknown_type $params   邮件id
	 */
	public function openMails($params){
		$mid = Game::intval($params,'mid');
		
		$MailModel = Master::getMail($this->uid);
		$MailModel->openMails($mid);
	}
	
	/**
	 * 打开邮件
	 * @param unknown_type $params   邮件id
	 */
	public function addMails($params){
		$data = array(
			'mtitle' => "邮件mtitle",
	        'mcontent' => "邮件mcontent",
	        'items' => array(
				array('id' => 11,'count' => 1),
				array('id' => 12,'count' => 1),
				array('id' => 13,'count' => 1),
				array('id' => 14,'count' => 1),
				array('id' => 21,'count' => 1),
				array('id' => 22,'count' => 1),
				array('id' => 23,'count' => 1),
				array('id' => 24,'count' => 1),
				array('id' => 31,'count' => 1),
				array('id' => 32,'count' => 1),
			),  //道具列表
			//邮件类型 mtype 0:无道具列表  1:有道具列表 2:其他
	        'mtype' => 1, 
		
		);
		$MailModel = Master::getMail($this->uid);
		$MailModel->update($data);
	}
	
	/**
	 * 打开邮件
	 * @param unknown_type $params   邮件id
	 */
	public function addMails1($params){
		$data = array(
			'mtitle' => "邮件mtitle",
	        'mcontent' => "邮件mcontent",
			//邮件类型 mtype 0:无道具列表  1:有道具列表 2:其他
	        'mtype' => 0, 
		
		);
		$MailModel = Master::getMail($this->uid);
		$MailModel->update($data);
	}
}
