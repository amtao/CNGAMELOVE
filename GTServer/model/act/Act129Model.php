<?php
require_once "ActBaseModel.php";
/*
 * 发配-发配列表
 */
class Act129Model extends ActBaseModel
{
	public $atype = 129;//活动编号
	
	public $comment = "发配-发配列表";
	public $b_mol = "banish";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	public $hd_id = 'banish';//活动配置文件关键字
	public $hd_cfg;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * hid=>time  hid => 发配时间
		*/
	);

	public function make_out()
	{
		$this->hd_cfg = Game::get_peizhi($this->hd_id);//发配
		$end_day = empty($this->hd_cfg['base']['day']) ? 100 : $this->hd_cfg['base']['day'];
		$end = intval($end_day) * 86400;
		$outf= array();
		if(!empty($this->info['desk'])){
			foreach ($this->info['desk'] as $did => $val){
				$outf[] = array(
					'id' => $did,
					'hid' => $val['hid'],
					'cd' => array(
						'next' => empty($val['stime']) ? $val['stime'] : $val['stime']+$end,
						'label' => 'banish',
					),
				);
			}
		}
		$this->outf = $outf;
	}

	/**
	 * 发配
	 * @param $hid
	 * @param $did
	 */
	public function banish($hid,$did){
        $this->check($hid);
		//判断座位能不能坐
		$Act128Model = Master::getAct128($this->uid);
		$Act128Model->click_id($did);
		//座位上有没有人
		if(!empty($this->info['desk'][$did]['hid'])){
			Master::error(BANISH_001);
		}
		//判断门客可不可以发配
		if(isset($this->info['list'][$hid])){
			Master::error(BANISH_002);
		}
		//用户是否可以拥有该门客
		$HeroModel = Master::getHero($this->uid);
		if(empty($HeroModel->info[$hid])){
			Master::error(BANISH_003);
		}

		//判断当前剩余的门客是否够流放
		$base_num = $this->hd_cfg['base']['hnum'] ? intval($this->hd_cfg['base']['hnum']) : 6;
		$exit_num = !empty($this->info['list']) ? count($this->info['list']) : 0;
		if(count($HeroModel->info) - $exit_num - $base_num < 1){
			Master::error(BANISH_004);
		}

		$this->info['desk'][$did] = array(
			'hid' => $hid,
			'stime' => Game::get_now()
		);
		$this->info['list'][$hid] = 1;
		$this->save();
		$this->back_data_hero();

	}

	/**
	 * 召回
	 * @param $did
	 * @param $type
	 */
	public function recall($did,$type){
		//判断位置上有木有人
		if(empty($this->info['desk'][$did]['hid'])){
			Master::error(BANISH_005);
		}
		//到期了没
		$end = empty($this->hd_cfg['base']['day']) ? 100*86400 : intval($this->hd_cfg['base']['day'])*86400;
		$money = 0;
		$expiry_time = $this->info['desk'][$did]['stime']+$end;
		if(Game::dis_over($expiry_time)){
			if($type){//提前结束
				$money = ceil(($expiry_time - Game::get_now())/86400) * $this->hd_cfg['base']['recall'];
			}else{
				Master::error(BANISH_006);
			}
		}

		if(!empty($money)){
			Master::sub_item($this->uid,1,1,$money);
		}
		$hid = $this->info['desk'][$did]['hid'];
		$this->info['desk'][$did] = array(
			'hid' => 0,
			'stime' => 0
		);
		unset($this->info['list'][$hid]);
		$this->save();
		$this->back_data_hero();
	}

	public function back_data_hero()
	{
		$outf = array();
		if(!empty($this->info['list'])){
			foreach ($this->info['list'] as $hid => $v){
				$outf[] = array('hid' => $hid);
			}
		}
		Master::back_data($this->uid,$this->b_mol,'herolist',$outf);
	}

	/**
	 * 判断是否被流放
	 * @param $hid
	 */
	public function isBanish($hid){
		return isset($this->info['list'][$hid]) ? true : false;
	}

    /**
     * 判断是否被流放
     * @param $hid
     */
    public function check($hid){

        //管事不能发配
        $Act6120Model = Master::getAct6120($this->uid);
        if ($Act6120Model->info['id'] == $hid){
            Master::error(BANISH_0013);
        }

        //办差委派不能发配
        $weipais = array();
        $Act6003Model = Master::getAct6003($this->uid);
        if (!empty($Act6003Model->info)){
            foreach ($Act6003Model->info as $j){
                if (!empty($j)){
                    $weipais = array_merge($weipais,$j);
                }
            }
        }
        if (!empty($weipais) && in_array($hid,$weipais)){
            Master::error(BANISH_0014);
        }

        //书院学习不能发配
        $school = array();
        $Act16Model = Master::getAct16($this->uid);
        if (!empty($Act16Model->info['info'])){
            foreach ($Act16Model->info['info'] as $y){
                if (!empty($y)){
                    $school[] = $y['hid'];
                }
            }
        }
        if (!empty($school) && in_array($hid,$school)){
            Master::error(BANISH_0015);
        }

        //宴会不能发配
        $boite = array();
        $Act172Model = Master::getAct172($this->uid);
        if (!empty($Act172Model->info)){
            foreach ($Act172Model->info as $x => $z){
                if (!empty($z['uid'])){
                    $boite[] = $x;
                }
            }
        }
        if (!empty($boite) && in_array($hid,$boite)){
            Master::error(BANISH_0016);
        }
    }

}
