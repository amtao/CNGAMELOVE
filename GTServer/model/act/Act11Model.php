<?php
require_once "ActBaseModel.php";
/*
 * 伙伴的精力
 */
class Act11Model extends ActBaseModel
{
	public $atype = 11;//活动编号
	public $label = "jingli";//倒计时标记
	
	public $comment = "伙伴精力";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "jingLi";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//政务处理
		'num' => 0,//	精力累计数量
		'time' => 0,//	精力上次恢复时间
	);
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		$UserModel = Master::getUser($this->uid);
		//获得VIP配置
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		
		//计算恢复数据
		$hf_num = Game::hf_num(
				$this->info['time'],//上次恢复时间
				1800,//CD
				$this->info['num'],//上次次数
				$vip_cfg_info['jingli']//次数上限
			);
		
		//保存数据
		$this->info['time'] = $hf_num['stime'];
		$this->info['num'] = $hf_num['num'];
		
		//输出数据
		$this->outf = array(
			'next' => $hf_num['next'],//下次绝对时间
			'num' => $hf_num['num'],//剩余次数
			'label' => $this->label,
		);
	}
	
	/*
	 * XXOO 减去一次数量
	 */
	public function apao(){
		if ($this->outf['num'] <= 0){
			Master::error(WIFE_POWER_EMPTY);
		}
		//减去次数
		$this->info['num'] -= 1;
		
		//保存
		$this->save();
	}
	
	/*
	 * 一键XO 减去全部数量
	 * 返回次数
	 */
	public function qunp(){
		if ($this->outf['num'] <= 0){
			Master::error(WIFE_POWER_EMPTY);
		}
		//减去次数
		$num = $this->info['num'];
		$this->info['num'] = 0;
		
		//保存
		$this->save();
		return $num;
	}
	
	/*
	 * 恢复精力
	 */
	public function huifu(){
		if ($this->outf['num'] > 0){
			Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
			return 0;
		}
		//扣除伟哥
		Master::sub_item($this->uid,KIND_ITEM,71,1);
		
		//获得VIP配置
		$UserModel = Master::getUser($this->uid);
		// $vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		//恢复体力一次恢复10点
		$vip_cfg_info = Game::getcfg_info('vip',0);
		//保存数据
		$this->info['num'] = $vip_cfg_info['jingli'];//恢复满精力
		$this->save();

        //舞狮大会 - 精力丹消耗数量
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(28,1);
	}
	
}
