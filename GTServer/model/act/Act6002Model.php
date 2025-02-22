<?php
require_once "ActBaseModel.php";
/*
 *  羁绊数据
 */
class Act6002Model extends ActBaseModel
{
	public $atype = 6002;//活动编号

	public $comment = "玩家首次找到门客寻访";
	public $b_mol = "xunfang";//返回信息 所在模块
	public $b_ctrl = "firstHero";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'heros'=>array(), 	//门客
		'wifes'=>array(), 	//红颜
	);

	/*
	 * 判断是否有门客寻访
	 */
	public function getHeroXF(){
		$HeroModel = Master::getHero($this->uid);
		$cfg_xf_sp = Game::getcfg('xf_clientevent');
		$heros = $this->info['heros'];
        $act6001Model = Master::getAct6001($this->uid);
        //寻访bug  和珅好感5000+获取不到凌玉环
        $WifeModel = Master::getWife($this->uid);
        if (in_array(40,$heros) && ($act6001Model->getHeroJB(40) >= 5000) && ($WifeModel->check_info(30,true) == false)){
            foreach ($heros as $key => $hid){
                if ($hid == 40){
                    unset($heros[$key]);
                }
            }
        }
		foreach($cfg_xf_sp as $k => $v){
			if ($v['type'] == 6 && $HeroModel->check_info($v['object'], true) && !in_array($v['object'], $heros)){
			    if ($v['jibang'] != 0){
                    if ($act6001Model->getHeroJB($v['object']) < $v['jibang'])continue;
                }
				$this->saveHero($v['object']);
				return $v['id'];
			}
		}
		return 0;
	}
	
	/*
	 * 判断是否有门客寻访
	 */
	public function getWifeXF(){
		$WifeModel = Master::getWife($this->uid);
		$cfg_xf_sp = Game::getcfg('xf_clientevent');
		$wifes = $this->info['wifes'];
        $act6001Model = Master::getAct6001($this->uid);
		foreach($cfg_xf_sp as $k => $v){
			if ($v['type'] == 7 && !$WifeModel->check_info($v['object'], true) && !in_array($v['object'], $wifes)){
                if ($v['jibang'] != 0){
                    if ($act6001Model->getWifeJB($v['object']) < $v['jibang'])continue;
                }
				$this->saveWife($v['object']);
				return $v['id'];
			}
		}
		return 0;
	}

	public function saveHero($id){
		$heros = $this->info['heros'];
		if (!in_array($id, $heros)){
			$heros[] = $id;
		}
		$this->info['heros'] = $heros;
		$this->save();
	}

	public function saveWife($id){
		$wifes = $this->info['wifes'];
		if (!in_array($id, $wifes)){
			$wifes[] = $id;
		}
		$this->info['wifes'] = $wifes;
		$this->save();
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
	}
}
