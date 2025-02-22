<?php
/*
 * 葛二蛋击杀记录
 */
require_once "SevBaseModel.php";
class Sev4Model extends SevBaseModel
{
	public $comment = "葛二蛋击杀记录";
	public $act = 4;//活动标签
	protected $_use_lock = false;//是否加锁
	
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	'uid' => 10086
		 *  'ktime' => 12,//击杀时间
		 * )
		 */
	);
	
	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$outf = array();
		$temparra = array_reverse($this->info);//倒序输出
		$rid = 1;
		foreach($temparra as $v){
			$fuidInfo = isset($v['uinfo']) ? $v['uinfo'] : Master::fuidInfo($v['uid']);
			$fuidInfo['rid'] = $rid++;//序号
			$fuidInfo['num'] = $v['ktime'];
			$outf[] = $fuidInfo;
		}
		return $outf;
	}
	
	/*
	 * 添加一条击杀记录
	 */
	public function add($uid){
		$this->info[] = array(
			'uinfo' => Master::fuidInfo($uid),
			'uid' => $uid,
			'ktime' => $_SERVER['REQUEST_TIME'],
		);
		//截取数据表
		$max_num = 100;
		if (count($this->info) > $max_num){
			$this->info = array_slice($this->info,-$max_num,$max_num,1);
		}
		$this->save();
	}
	
	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		$data = $this->get_outf();
		Master::back_data(0,'wordboss','g2dkill',$data);
	}
	
}
