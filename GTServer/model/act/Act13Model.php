<?php
require_once "ActBaseModel.php";
/*
 * 战斗2冷却时间
 */
class Act13Model extends ActBaseModel
{
	public $atype = 13;//活动编号
	
	public $comment = "关卡BOSS冷却时间";
	public $b_mol = "user";//返回信息 不返回
	public $b_ctrl = "pvb2";//返回信息 所在控制器
	
	
	public $_pvb_cd = 60;//战斗冷却时间 1~10分钟
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//席位数量
		'time' => 0,//上次战斗时间 / 下次可以战斗的时间
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		if (Game::is_over($this->info['time'])){
			$next = 0;
		}else{
			$next = $this->info['time'];
		}
		$this->outf = array(
			"next" => $next,
			"label" => "pvb2cd",
		);
	}
	
	/*
	 * 检查CD时间是不是到了
	 */
	public function click_cd(){
		if (Game::is_over($this->info['time'])){
			return true;
		}
		Master::error(ACT13_CD_FLIGHT);
	}
	
	/*
	 * 战斗失败 设置CD
	 */
	public function set_cd(){
		$this->info['time'] = Game::get_now() + $this->_pvb_cd;
	}
	
	
}
