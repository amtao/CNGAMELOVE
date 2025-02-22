<?php
require_once "ActBaseModel.php";
/*
 * 酒楼-消息-我的历史宴会
 */
class Act52Model extends ActBaseModel
{
	public $atype = 52;//活动编号
	
	public $comment = "酒楼-消息-我的历史宴会";
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "yhOld";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		  'type' => 0,  // 1:家宴  2:官宴
		  'score' => 0, //宴会获得 的分数
		  'bad'  => 0, //捣乱人数
		  'ctime' => 0, //宴会创建时间
		  'num' => 0, //宴会人数
		*/
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		
		$this->outf = array();
		
		if(!empty($this->info)){
			foreach($this->info as $k => $v){
				$this->outf[$v['ctime']] = $v;
			}
			krsort($this->outf);
			$this->outf = array_values($this->outf);
		}
	}
	
	/**
	 * 加入历史信息
	 * @param int $type  1:家宴  2:官宴
	 * @param int $score  宴会获得 的分数
	 * @param int $bad  捣乱人数
	 * @param int $ctime  宴会创建时间
	 * @param int $num 参与人数
	 * @param int $ep  总属性
	 */
	public function add_yanhui($type,$score,$bad,$ctime,$num,$ep=0){
		
		$this->info[] = array(
			  'id' => $type,  // 1:家宴  2:官宴
			  'score' => $score, //宴会获得 的分数
			  'bad'  => $bad, //捣乱人数
			  'ctime' => $ctime, //宴会创建时间
			  'num' => $num, //宴会参与人数
			  'ep' => $ep, //总属性
		);
		$this->save();
		
	}
	
}
















