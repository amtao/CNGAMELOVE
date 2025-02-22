<?php
/*
 * 全服邮件列表
 */
require_once "SevListBaseModel.php";
class Sev31Model extends SevListBaseModel
{
	public $comment = "全服邮件列表";
	public $act = 31;//活动标签
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	'title' => '邮件标题'，
		 *  'items' => 0,//道具
		 *  'startTime' => 开始时间,
         *  'endTime' => 结束时间,
		 *  'time' => 0,//创建时间
		 * )
		 */
	);
	
	/*
	 * 添加一条奖励信息
	 */
	public function add($key,$content){
	    if(!empty($this->info)){
	        $cache = Common::getDftMem ();
	        $maildata = $cache->get('mai_send_content');
	        foreach ($this->info as $key1=>$val){
	            if(time()> strtotime($val['endTime'])){
	                unset($this->info[$key1]);
	                unset($maildata[$key1]);
	            }
	        }
	        if(empty($maildata)){
	            $maildata = array();
	        }
	        $cache->set('mai_send_content', $maildata);
	    }
	   
		$this->info[$key] = $content;
		$this->save();
	}
	/*
	 * 删除邮件
	 * */
	public function del($key){
		if(empty($this->info[$key])) return false;
		$cache = Common::getDftMem ();
		$maildata = $cache->get('mai_send_content');
		if(isset($maildata[$key])){
			unset($maildata[$key]);
			$cache->set('mai_send_content', $maildata);
		}
		unset($this->info[$key]);
		$this->save();
		return true;
	}
}
