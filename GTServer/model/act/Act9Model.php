<?php
//
require_once "ActFlBaseModel.php";
/*
 * 全服招亲 获取的 3个限时选择列表
 * 子嗣招亲列表
 */
class Act9Model extends ActBaseModel
{
	public $atype = 9;//活动编号
	
	public $comment = "子嗣招亲列表";
	public $b_mol = "son";//返回信息 所在模块
	public $b_ctrl = "cList";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 * 合服设想 超时自动刷新即可  本数据可以抛弃
	 */
	public $_init =  array(//招亲子嗣列表
		/*
		'子嗣编号' => array(
			'list' =>  array(
				'流水号',
			),
			'outtime' => 13424243512,//自动刷新时间
		),
		*/
	);
	
	/*
	 * 请求一个子嗣的全服招亲信息
	 */
	public function get_zhaoqin($sonid,$reset = false){
		//获取这个子嗣
		$SonModel = Master::getSon($this->uid);
		$son_info = $SonModel->check_info($sonid);
		//是否未婚
		if ($son_info['state'] != 4){
			Master::error(SON_REFRESH_ERROR);
		}
		
		$idarr = array(); //数据库 流水ID
		if (empty($this->info[$sonid]['list']) //如果没有这个表
		|| $reset	//强制重置
		|| Game::is_over($this->info[$sonid]['outtime'])//时间过期
		){
			//使用新的超时时间
			$outtime = Game::get_over(7200);
			//刷新列表
			//$idarr = array();
		}else{
			//使用原有超时时间
			$outtime = $this->info[$sonid]['outtime'];
			
			$idarr = $this->info[$sonid]['list'];
			/*
			 * //使用原有信息
			foreach ($this->info[$sonid]['list'] as $v){
				$idarr[] = $v['id'];
			}*/
		}
		
		//全服联姻信息
		$Sev1Model = Master::getSev1();
		$son_arr = $Sev1Model->zhaoqin($this->uid,$son_info['sex'],$son_info['honor'],$idarr);
		$this->info[$sonid] = array(
			'list' => $son_arr,
			'outtime' => $outtime,//超时时间 2小时 
		);
		$this->save();
		
		//展开子嗣信息
		$zhaoqin = array();
		foreach ($son_arr as $v){
			$zhaoqin[] = Master::getMarryDate($v['uid'],$v['sonuid']);
		}
		
		$list = array(
			'list' => $zhaoqin,
			'otime' => array(
				"next" => $this->info[$sonid]['outtime'],
				"label" => "zhaoqin",
			),
			'rpay' => 100,
		);
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$list);
	}
	
	/*
	 * 结婚
	 * >: 跟全服的人联姻
	 */
	public function lianyin($sonid,$fuid,$fsonid){
		//检查子嗣状态 是否未婚
		
		//检查这个人 是不是在我的招亲列表里面
		$in_zhaoqin = false;
		if (!empty($this->info[$sonid]['list']) //有这个表
		){
			foreach ($this->info[$sonid]['list'] as $v){
				if ($v['uid'] == $fuid
				&& $v['sonid'] == $fsonid){
					$in_zhaoqin = true;
				}
			}
		}
		if (empty($in_zhaoqin)){
			Master::error(SON_NOTIN_JUST_LIST);
		}
		
		//尝试进行结婚
		$Sev1Model = Master::getSev1();
		if ($Sev1Model->jiehun()){
			//成功结婚
			//做结婚的操作
			
			//我的子嗣 状态改为结婚
			//对方子嗣 状态改为结婚等待通知 (异步通知队列?)
			//其他操作?
			
		}else{
			//输出报错  不在结婚列表里面
			//刷新我的 招亲列表
			Master::error_msg(SON_NOTIN_NO_JUST);
			
			//刷新我的招亲列表
			$this->get_zhaoqin($sonid,true);
		}
	}
	
	/*
	 * 删除我的招亲缓存
	 */
	public function delete($sonid){
		if (isset($this->info[$sonid])){ //有这个表
			unset($this->info[$sonid]);
			$this->save();
		}
	}
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		//重写这个方法 , 不构造
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		//重写这个方法 , 无返回
	}
}
