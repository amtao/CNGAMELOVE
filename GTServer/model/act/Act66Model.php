<?php
require_once "ActBaseModel.php";
/*
 * 首充福利
 */
class Act66Model extends ActBaseModel
{
	public $atype = 66;//活动编号
	
	public $comment = "首充福利";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "fchofuli";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'ctime' => 0,//充值时间
		'rwd' => 0,//领奖时间
        'isClotheRwd' => 0,//老玩家首充未获取服饰
	);
	
	/*
	 * 首充
	 */
	public function do_save(){
		if(empty($this->info['ctime'])){
			$this->info['ctime'] = Game::get_now();
			$this->save();
		}
	}
	
	
	/*
	 * 充值检查
	 */
	public function click($value){
		//如果状态为 为充值
		if ($this->outf['type'] == 0 && $value > 0){
			$this->info = array(
				'ctime' => Game::get_now(),
				'rwd' => 0,
                'isClotheRwd' => 0,
			);
			$this->save();
		}
	}
	
	/*
	 * 领取首充奖励
	 */
	public function rwd(){
		//领取首冲奖励
		if ($this->outf['type'] == 0){
			Master::error(ACT66_UNRECHARGE);
		}elseif ($this->outf['type'] != 1){
			Master::error(ACT66_HAVE_RECEIVE);
		}
		
		//领奖记录
		$this->info['rwd'] = Game::get_now();
		$this->info['isClotheRwd'] = 1;
		$this->save();
		
		//发奖励 配置
		$rwd_cfg = Game::getcfg_info('fuli_fc',1);
		Master::add_item3($rwd_cfg['firstRwd']);
	}

	public function getRwd(){
	    if ($this->info['rwd'] > 0 && empty($this->info['isClotheRwd'])){//老玩家首充未获取服饰
            $rwd_cfg = Game::getcfg_info('fuli_fc',1);
            $Act6140Model = Master::getAct6140($this->uid);
            foreach ($rwd_cfg['firstRwd'] as $rwd){
                if ($rwd['kind'] == 95 && !$Act6140Model->isUnlock($rwd['id'])){
                    Master::add_item2($rwd);
                }
            }
            $this->info['isClotheRwd'] = 1;
            $this->_save();
        }
    }

	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		$type = 0;
		if ($this->info['ctime'] > 0){
			if ($this->info['rwd'] > 0){
				$type = 2;//已领取
			}else{
				$type = 1;//未领取
			}
		}
		
		//输出数据 
		$this->outf = array(
			'type' => $type,//0未充值,1已充值,2已领取
		);
	}
	
}