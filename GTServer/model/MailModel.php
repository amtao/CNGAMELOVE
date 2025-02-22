<?php
//用户
require_once "AModel.php";
class MailModel extends AModel
{
	public $_key = "_mail";
	public $info;
	protected  $updateSetKey = array(
		'uid','mtitle','mcontent','items','mtype','fts',
		'rts','isdel','link'
	);
    protected $_syn_w = true;
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());
		$table = 'mail_'.Common::computeTableId($this->uid);
		if($this->info == false){
			$this->info = array();
			$sql = "select * from `{$table}` where `uid`={$this->uid} ";
    		$db = $this->_getDb();
    		if ( empty($db) ) {
    			return false;
    		}
			$data = $db->fetchArray($sql);
			if($data == false) {
				return false;
			}
			$info = array();
			foreach ($data as $v){
				if (!empty($v['items'])){
					$v['items'] = json_decode($v['items'],true);
				}else{
					$v['items'] = array();
				}
				$info[$v['mid']] = $v;
			}
			$this->info = $info;
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	/**
	 * 获取邮件
	 */
	public function getMails(){
		//构造输出
		$mails = array();
		foreach($this->info as $mid => $v){
			//如果后台已经删除并且未读,不显示
			if( $v['isdel'] && empty($v['rts']) ){
				continue;
			}
			if( $v['mtype'] == 1 && empty($v['items'])){
				continue;
			}
			if($v['isdel'] == 2){
				continue;
			}
			$mails[] = $this->getBase_buyid($mid);
		}
		
		Master::back_data($this->uid,"mail","mailList",$mails);
	}
	
	public function getBase_buyid($mid)
	{
		$info = $this->info[$mid];
		
		return array(
			'id' => $info['mid'],
			'mtitle' => $info['mtitle'],
			'mcontent' => $info['mcontent'],
			'items' => empty($info['items'])?array():$info['items'],
			'mtype' => $info['mtype'],
			'fts' => intval($info['fts']),
			'rts' => $info['rts'],
			'link' => $info['link'],
		);
	}
	
	/**
	 * 发送邮件
	 * @param $uid  玩家id
	 * @param $title 标题
	 * @param $content 内容
	 * @param $mtype   0:无道具列表  1:有道具列表 2:其他
	 * @param $daoju  道具列表
	 */
	public function sendMail($uid,$title,$content,$mtype,$daoju,$link = ''){
		$data = array(
			'uid' => empty($uid)?$this->uid:$uid,
			'mtitle' => $title,
			'mcontent' => $content,
			'items' => $daoju,
			'mtype' => $mtype,
			'link' => $link,
		);
		$this->update($data);
	}
	
	
	/**
	 * 删除邮件
	 * @param int $mid  邮件id
	 */
	public function delMails($mid){
	    if(empty($this->info[$mid])){
	        Master::error(MAIL_MEIYOUJIAN);
	    }
		$data = array(
			'mid' => $mid,
			'rts' => 0,
			'isdel' => 1,
		);
		$this->update($data,false);
	}
	
	/**
	 * 领取邮件奖励
	 * @param int $mid  邮件id
	 */
	public function redMails($mid){
	    if(empty($this->info[$mid])){
	        Master::error(MAIL_MEIYOUJIAN);
	    }
		if($this->info[$mid]['isdel'] == 1){
			Master::error(MAIL_DELETED);
		}
		if($this->info[$mid]['rts'] > 0){
			Master::error(MAIL_IS_RECEIVE);
		}
		if(empty($this->info[$mid]['items'])){
			Master::error(MAIL_NO_REWARD);
		}
		//添加道具
		foreach($this->info[$mid]['items'] as $v){
            if(empty($v['id'])){
                continue;
            }
            Master::add_item($this->uid,$v['kind'],$v['id'],$v['count'],"msgwin","items",array('num1'=>$v['section']));
		}

		$data = array(
			'mid' => $mid,
			'rts' => $_SERVER['REQUEST_TIME'],
			'isdel' => 1,
		);
		$this->update($data);
	}

	public function onekeyRedMails(){
		$items = array();
		$updateDatas = array();
		foreach($this->info as $mid => $v){
			if($v['isdel'] == 1){
				continue;
			}
			if($v['rts'] > 0){
				continue;
			}
			if(empty($v['items'])){
				continue;
			}
			//添加道具
			foreach($v['items'] as $v){
				if(empty($v['id'])){
					continue;
				}
				if(empty($items[$v['kind']][$v['id']])){
					$items[$v['kind']][$v['id']] = 0;
				}
				$items[$v['kind']][$v['id']] += $v['count'];
			}
			$data = array(
				'mid' => $mid,
				'rts' => $_SERVER['REQUEST_TIME'],
				'isdel' => 1,
			);
			$this->update($data);
			$updateDatas[] = $data;
		}
		
		foreach($items as $kind => $v){
			foreach($v as $itemid => $count){
				Master::add_item($this->uid,$kind,$itemid,$count,"msgwin","items");
			}
		}
		
		Master::back_data($this->uid,"mail","updateMails",$updateDatas,true);
	}
	
	/**
	 * 打开邮件
	 * @param int $mid  邮件id
	 */
	public function openMails($mid){
		
	    if(empty($this->info[$mid])){
	        Master::error(MAIL_MEIYOUJIAN);
	    }
	    
		if($this->info[$mid]['isdel'] == 1){
			Master::error(MAIL_DELETED);
		}
		
		//如果无道具列表  设置为已经读取
		if($this->info[$mid]['mtype'] == 0){
			$data = array(
				'mid' => $mid,
				'rts' => $_SERVER['REQUEST_TIME'],
				'isdel' => 1,
			);
			$this->update($data);
		}
	}
	
	/**
	 * 更新    邮件类型  mtype 0:无道具列表  1:有道具列表 2:其他
	 * @param array $data
	 */
	public function update($data,$check = true)
	{
		if ( isset($data['mid']) && isset($this->info[$data['mid']])){
			$mailInfo = $this->info[$data['mid']];
			foreach($data as $k => $v){
				if(!in_array($k,$this->updateSetKey)){
					continue;
				}
				$mailInfo[$k] = $v;
			}
			$mailInfo['_update'] = true;
			$mid = $data['mid'];
            if ($this->info[$data['mid']]['isdel'] != $data['isdel']){
                //邮件领取日志双记录
                Game::cmd_flow(28, $mid, $data['isdel']-$this->info[$data['mid']]['isdel'], $data['isdel']);

            }
            if ($this->info[$data['mid']]['rts'] != $data['rts']){
                //邮件领取日志双记录
                Game::cmd_flow(28, $mid, $data['rts']-$this->info[$data['mid']]['rts'], $data['rts']);
            }
            
		}else{

			$mailInfo = array(
		        'uid' => empty($data['uid'])?$this->uid:$data['uid'],
		        'mtitle' => $data['mtitle']? addslashes($data['mtitle']) : "",
		        'mcontent' => $data['mcontent']? addslashes($data['mcontent']) : "",
		        'items' => isset($data['items'])? $data['items'] : 0, //道具列表
				//邮件类型 mtype 0:无道具列表  1:有道具列表 2:其他
		        'mtype' => isset($data['mtype'])? $data['mtype'] : 0, 
		        'fts' => isset($data['fts'])? $data['fts'] : $_SERVER['REQUEST_TIME'],//发送时间
		        'rts' => isset($data['rts'])? $data['rts'] : 0,   //读取时间
				'isdel' => isset($data['isdel'])?$data['isdel'] : 0, //0:未删除  1:已删除
				'link' => isset($data['link']) ? $data['link'] : '', // 外部链接
			);
			$table = 'mail_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set ";
			foreach( $mailInfo as $perKey => $perValue ){
				if($perKey == 'items' && $perValue != 0){
					$perValue = json_encode($data['items']);
				}
				$sql .= "`{$perKey}`='{$perValue}',";
			}
			$sql = substr($sql,0,-1);
			$db = $this->_getDb();
    		if ( empty($db) ) {
    			return false;
    		}
			$db->query($sql);
			$mid = $db->insertId();
			$mailInfo['mid'] = $mid;

		}
		$this->info[$mid] = $mailInfo;
		$this->_update = true;
		if($check){
			$mail_info[] = $this->getBase_buyid($mid);
			Master::back_data($this->uid,'mail','mailList',$mail_info,true);
		}
	}

	public function sync()
	{
		if( is_array($this->info) && $this->info ){
			$db = $this->_getDb();
			$table = 'mail_'.Common::computeTableId($this->uid);
			foreach ($this->info as $k=>$v){
				if(empty($v['_update'])) continue;
				$this->info[$k]['_update'] = false;//标志位已经更新过
				$sql="update `{$table}` set `isdel`= {$v['isdel']},`rts`= {$v['rts']} where `uid`={$this->uid} and `mid`={$k} limit 1";
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error mailModel_'.$sql);
				}
			}
			return true;
		}
		return false;
	}


	/**
	 * 删除单个邮件
	 * @param $mid
	 */
	public function removeMail($mid){
		if(empty($this->info[$mid])){
			Master::error(MAIL_MEIYOUJIAN);
		}
		if(empty($this->info[$mid]['isdel'])){
			Master::error(CAN_NOT_DELETE_MAIL);
		}
		$data = array(
			'mid' => $mid,
			'isdel' => 2,
		);
		$this->update($data,false);
	}

	/**
	 * 批量删除邮件
	 */
	public function removeMails(){
		if(!empty($this->info)){
			foreach ($this->info as $mid =>$val){
				if($this->info[$mid]['isdel']==0 || $this->info[$mid]['isdel'] == 2){
					continue;
				}
				$data = array(
					'mid' => $mid,
					'isdel' => 2,
				);
				$this->update($data,false);
			}
		}
	}
}
