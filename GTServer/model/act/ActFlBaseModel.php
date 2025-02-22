<?php
require_once "ActBaseModel.php";

/*
 * 门客出战列表  基类
 */
class ActFlBaseModel extends ActBaseModel
{
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//出战序列
		/*
		 *  array(
		 *    'f' => 1, //打了几次
		 *    'h' => 0, //当前 生1/死0
		 * ),
		 */
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = array();
		$f_date = array(); //战斗列表
		foreach ($this->info as $hid => $fdat){
			$f_date = array(
				'id' => $hid,
				'h' => $fdat['h'],
				'f' => $fdat['f'],
                'b' => $fdat['b'],
			);
			$this->outf[] = $f_date;
		}
	}
	
	/*
	 * 一个英雄出战操作
	 */
	public function go_fight($hid,$ftimes = 1){
		if (isset($this->info[$hid])
		&& $this->info[$hid]['h'] <= 0 && $this->info[$hid]['f'] >= $ftimes){
			Master::error(HERO_RESTING);
		}
		$this->info[$hid]['f'] += 1;//战斗次数 + 1
        $this->info[$hid]['h'] = $this->info[$hid]['f'] >= $ftimes + $this->info[$hid]['b']?0:1;//状态 死
		$this->save();
		return true;
	}
	
	
	/*
	 * 重置一个英雄出战操作
	 */
	public function reset_fight($hid){
		unset($this->info[$hid]);
		$this->save();
		return true;
	}
	
	/*
	 * 复活一个英雄
	 * 英雄ID  每个英雄允许战斗次数
	 */
	public function cone_back($hid,$ftimes = 2, $costItem = 124){
		if ((empty($this->info[$hid])
		|| $this->info[$hid]['h'] > 0) && $costItem != 0){
			Master::error(HERO_CAN_PLAY);
		}
		if($this->info[$hid]['f'] >= $ftimes){
			Master::error(HERO_TODAY_NOT_PLAY);
		}
		//该门客可不可以复活
//		$Act129Model = Master::getAct129($this->uid);
//		$isBanish = $Act129Model->isBanish($hid);
//		if($isBanish){
//			Master::error(BANISH_012);
//		}
		
		//扣除道具 / 出战令
		if ($costItem != 0){
		    Master::sub_item($this->uid,KIND_ITEM, $costItem,1);
        }
		
		$this->info[$hid]['h'] = 1;//状态 活
        $this->info[$hid]['b'] = empty($this->info[$hid]['b'])?1:$this->info[$hid]['b']+ 1;//状态 活
		$this->save();
		return true;
	}

	/*
	 * 复活一个英雄
	 * 英雄ID  每个英雄允许战斗次数
	 */
	public function cone_back_all(){
		$HeroModel = Master::getHero($this->uid);
		foreach($HeroModel->info as $hid => $v){
			if ((empty($this->info[$hid]) || $this->info[$hid]['h'] > 0) ){
				Master::error(HERO_CAN_PLAY);
			}
			// $this->info[$hid]['h'] = 1;//状态 活
			// $this->info[$hid]['b'] = empty($this->info[$hid]['b'])?1:$this->info[$hid]['b']+ 1;//状态 活
			// $this->info[$hid]['f'] = 0;
			$this->reset_fight($hid);
		}
		return true;
	}
	
	/*
	 * 过关
	 * 重置出战列表
	 */
	public function reset($isCheck=false){
	    if ($isCheck){
            foreach ($this->info as $hid => $fdat){
                if ($fdat['h'] == 1 || !empty($fdat['b'])){
                    return false;
                }
            }
        }
		$this->info = $this->_init;
		$this->save();
		return true;
	}
	
}
