<?php
require_once "ActBaseModel.php";
/*
 * vip福利
 */
class Act67Model extends ActBaseModel
{
	public $atype = 67;//活动编号
	
	public $comment = "vip福利";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "vipfuli";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		/*
		 * vip 档次 => 0//奖励领取状态
		 */
	);
	
	/*
	 * 领取VIP奖励
	 */
	public function rwd($vip){
		//配置
		$cfg = Game::getcfg_info('fuli_vip',$vip);
		
		//判断是否已领取
		if (!empty($this->info[$vip])){
			Master::error(ACT67_HAVE_RECEIVE);
		}
		//设置为已领取
		$this->info[$vip] = Game::get_now();
		$this->save();
		
		//发放奖励
		Master::add_item3($cfg['vipRwd']);
	}

    /*
     * 购买VIP特惠奖励
     */
    public function buyRwd($vip){
        //配置
        $cfg = Game::getcfg_info('fuli_vip',$vip);

        //判断是否已领取
        if (!empty($this->info['buy'][$vip])){
            Master::error(ACT67_HAVE_RECEIVE);
        }

        //扣除砖石
        Master::sub_item($this->uid,KIND_ITEM,1,$cfg['cost']);

        //设置为已领取
        $this->info['buy'][$vip] = Game::get_now();
        $this->save();

        //发放奖励
		Master::add_item3($cfg['vipgifts']);
    }
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		//配置文件
		$rwd_cfg = Game::getcfg('fuli_vip');

		//用户类
		$UserModel = Master::getUser($this->uid);

		$outf = array();
		foreach ($rwd_cfg as $vip => $v){
			if ($UserModel->info['vip'] >= $vip){
				//已达成
				$_out = array(
					'id' => $vip,
				);
				if (empty($this->info[$vip])){
					//未领取
					$_out['type'] = 1;
				}else{
					//已领取
					$_out['type'] = 2;
				}
                if (empty($this->info['buy'][$vip])){
                    //未领取
                    $_out['tehui'] = 1;
                }else{
                    //已领取
                    $_out['tehui'] = 2;
                }
				$outf[] = $_out;
			}
		}
		//输出数据
		$this->outf = $outf;
	}
	
}