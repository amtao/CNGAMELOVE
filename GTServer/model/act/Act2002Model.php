<?php
require_once "ActBaseModel.php";
/*
 *  门客激活信物
 */
class Act2002Model extends ActBaseModel
{
	public $atype = 2002;//活动编号

	public $comment = "门客信物羁绊";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "tokenFetters";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'fetterInfo' => array(),
  );
	
	public function tokenFetter($heroId,$fetterId){
		$Act2001Model = Master::getAct2001($this->uid);
		$fIds = $Act2001Model->getTokenIds($heroId);

		$fetterCfg = Game::getcfg_info("tokenFetters",$fetterId);
		if (empty($fetterCfg)){
			Master::error(PARAMS_ERROR.'tokenFetters'.$fetterId);
		}
		$count = 0;
		foreach($fetterCfg['xinwuid'] as $v){
			foreach($fIds as $fv){
				if ($fv == $v){
					$count++;
				}
			}	
		}
		if($count != $fetterCfg['xinwuid'].length){
			Master::error(HERO_UNLOCK_FETTER_NOT_ENOUGH);
		}
		
		return self::addFetter($heroId,$fetterId);
	}
	
	public function addFetter($heroId,$fetterId){
		$fetters = $this->info['fetterInfo'];
		if(empty($fetters[$heroId])){
			$fetters[$heroId] = array($fetterId);
			self::addFetterProp($heroId,$fetterId);
		}
		$isOk = false;
		foreach($fetters[$heroId] as $v){
			if($v==$fetterId){
				$isOk = true;
				break;
			}
		}
		if(!$isOk){
			array_push($fetters[$heroId],$fetterId);
			self::addFetterProp($heroId,$fetterId);
		}
		$this->info['fetterInfo'] = $fetters;
		$this->save();
	}

	public function addFetterProp($heroId,$fetterId){
		$fetterCfg = Game::getcfg_info("tokenFetters",$fetterId);
		if (empty($fetterCfg)){
			Master::error(PARAMS_ERROR.'tokenFetters'.$fetterId);
		}
		$Act2001Model = Master::getAct2001($this->uid);
		foreach($fetterCfg['attri'] as $v){
			foreach($fetterCfg['xinwuid'] as $xid){
				$propInfo = $Act2001Model->info[$heroId][$xid]['prop'];
				$propInfo[$v['prop']] += Game::getCfg_formula()->token_lvUp($propInfo[$v['prop']],$v['value']);
				$Act2001Model->info[$heroId][$xid]['prop'] = $propInfo;
			}
		}
		$Act2001Model->save();
	}

    public function make_out(){
		$this->outf = $this->info;
		Master::$bak_data['u'][$this->b_mol][$this->b_ctrl] = $this->outf;
    }
	
}
