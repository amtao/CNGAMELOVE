<?php
require_once "ActBaseModel.php";
/*
 * 获取称号
 */
class Act73Model extends ActBaseModel
{
	public $atype = 73;//活动编号
	
	public $comment = "获取称号";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
	
	
	/*
	 * 构造输出结构体
	 */
	public function get_chlist($fuid){
		
		$this->outf = array();
		$Act25Model = Master::getAct25($fuid);
		if(!empty($Act25Model->info['list'])){
			
			foreach($Act25Model->info['list'] as $k => $v){
				if(Game::is_over($v['endT'])){
					continue;
				}
				//王的称号排前面   公的称号排后面
				$rk = 100;
				if( Game::is_ye($k) ){
					$rk = 200;
				}
				
				$this->outf[$k+$rk] = array(
					'id' => $k
				);
			}
			krsort($this->outf) ;
			$this->outf = array_values($this->outf);
			
		}
		return $this->outf;
	}
	
}


