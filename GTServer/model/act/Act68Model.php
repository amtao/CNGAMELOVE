<?php
require_once "ActBaseModel.php";
/*
 * 月卡
 */
class Act68Model extends ActBaseModel
{
	public $atype = 68;//活动编号
	
	public $comment = "年月卡";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "mooncard";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		/*
		 * id => array(
		 * 	daytime => ,//到期时间
		 *  rwdtime => .//领取时间
		 * )
		 */
	);
	
	/*
	 * 领取奖励
	 */
	public function rwd($id){
		//获取配置
		$cfg = Game::getcfg_info('fuli_card',$id);
		
		//是否购买
		if (empty($this->info[$id])){
			Master::error(ACT68_UNBUY);
		}
		//是否过期
		$time = $this->info[$id]['daytime'];
		if(date('His',$time) != 0){//不是整点
			$time = strtotime(date('Y-m-d 24:00:00',$time));
		}
		if (Game::is_over($time)){
			Master::error(ACT68_OVERDUE);
		}
		//是否领过
		if (Game::is_today($this->info[$id]['rwdtime'])){
			Master::error(ACT68_HAVE_RECEIVE);
		}
		
		//设置为已领取
		$this->info[$id]['rwdtime'] = Game::get_now();
		$this->save();
		
		//发放奖励
		Master::add_item3($cfg['rwdday']);

		if ($id == 2){
            $this->addYearClothe($cfg);
        }
        else if ($id == 1){
            $this->addMoonBlank($cfg, $time);
        }
        else if ($id == 4){
            $this->addWeekBlank($cfg, $time);
        }
		// if($id == 1 || $id == 4){
		// 	//日常任务
		// 	$Act35Model = Master::getAct35($this->uid);
		// 	$Act35Model->do_act(14,1);
		// }

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(55,1);
        $Act39Model->task_refresh(55);

	}
	
	/**
	 * 判断年卡月卡是否已经购买
	 * @param unknown_type $id
	 */
	public function find_ka($id){
		if( !empty($this->info[$id]['daytime']) 
		|| !Game::is_over($this->info[$id]['daytime'])){
			return 1;
		}
		return 0;
	}

	private function addYearClothe($cfg){
        $Act6140Model = Master::getAct6140($this->uid);
        $rwds = $cfg['rwd'];
        foreach ($rwds as $r){
            if ($r['kind'] == 95){
                if (!$Act6140Model->isUnlock($r['id'])){
                    Master::add_item3($rwds);
                }
                break;
            }
        }
    }

    private function addMoonBlank($cfg, $time){
        $Act6150Model = Master::getAct6150($this->uid);
        $rwds = $cfg['rwd'];
        $m = date("m", Game::day_0());
        foreach ($rwds as $r){
            if ($r['kind'] == 94 && $m == $r['moon']){
                if (!$Act6150Model->isUnlock($r['id'])){
                    Master::add_item2($r);
                }
                break;
            }
        }
    }

    private function addWeekBlank($cfg, $time){
        $Act6150Model = Master::getAct6150($this->uid);
        $rwds = $cfg['rwd'];
        $m = date("m", Game::day_0());
        foreach ($rwds as $r){
            if ($r['kind'] == 94 && $m == $r['week']){
                if (!$Act6150Model->isUnlock($r['id'])){
                    Master::add_item2($r);
                }
                break;
            }
        }
    }

    /*
     * 购买年月卡
     */
	public function buy($rmb){
		
		$UserModel = Master::getUser($this->uid);
		$channel = $UserModel->info['channel_id'];
		$platform = $UserModel->info['platform'];
		Common::loadModel('OrderModel');
		$list = OrderModel::recharge_list($platform,$channel);
		if( !in_array($list[$rmb]['type'],array(2,3,5))){
			return false;
		}
		$id = $list[$rmb]['type'] - 1;
		//获取配置
		$cfg = Game::getcfg_info('fuli_card',$id);
		//加上过期时间
		if (empty($this->info[$id])){
			$this->info[$id] = array(
				'daytime' => 0,
				'rwdtime' => 0,
			);
		}
		//加上生效时间
		if (Game::is_over($this->info[$id]['daytime'])){
			//如果过期 从今天开始加上
			$this->info[$id]['daytime'] = Game::day_0() + $cfg['days'] * 86400;
		}else{
			//未过期 时间加上
			$this->info[$id]['daytime'] += $cfg['days'] * 86400;
		}
		$this->info[$id]['retime'] = Game::day_0();
		if($id == 1){
			Game::cmd_other_flow($this->uid,"fuli","monthCard",array("id"=>$id),10003,1,1,1);
		}elseif($id == 4){
			Game::cmd_other_flow($this->uid,"fuli","weekCard",array("id"=>$id),10004,1,1,1);
		}

		$this->save();
		return $list[$rmb]['type'];
	}
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		$cfg = Game::getcfg('fuli_card');
		
		$outf = array();
		foreach ($cfg as $id => $v){
			if (isset($this->info[$id])){
				$_out = array();

                $t = $this->info[$id]['daytime'];
				//剩余天数
				$_out['days'] = ceil(($t - Game::day_0())/86400);
				$_out['moon'] = date("m", Game::day_0());
				//如果月卡还未过期
				if ($_out['days'] > 0){
					$_out['id'] = $id;//月卡类型
					if (Game::is_today($this->info[$id]['rwdtime'])){
						$_out['type'] = 2;//已领取
					}else{
						$_out['type'] = 1;//未领取
					}
					$outf[] = $_out;
				}
			}
		}
		$this->outf = $outf;
	}
	
	
	
	
	
}