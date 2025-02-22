<?php
require_once "ActBaseModel.php";
/*
 *  羁绊数据
 */
class Act6001Model extends ActBaseModel
{
	public $atype = 6001;//活动编号

	public $comment = "羁绊数据";
	public $b_mol = "scpoint";//返回信息 所在模块
	public $b_ctrl = "heroJB";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'heroJB'=>array(), 	//门客羁绊
		'wifeJB'=>array(), 	//红颜羁绊
		'heroSW'=>array(),	//门客声望
		'belief'=>0,		//个人声望
		'groups' => array(),	//当前领取的奖励组
	);


	/*
	 * 增加门客羁绊
	 */
	public function saveCurGroup($group){
		if (stripos($group, "zw") === 0){
			$act2Model = Master::getAct2($this->uid);
			return $act2Model->canSelectStory($group);
		}
		if (stripos($group, "jy") === 0){
			$act1Model = Master::getAct1($this->uid);
			return $act1Model->canSelectStory($group);
		}
		if (stripos($group, "xunfang") === 0){
			$act29Model = Master::getAct29($this->uid);
			return $act29Model->canSelectStory($group);
		}
		$isEmail = stripos($group, "e") === 0;

		$g = $this->info['groups'];
		$arr = explode('_', $group);
		$key = count($arr) > 1?$arr[0]:'key';
		$num = count($arr) > 1?intval($arr[1]):intval($arr[0]);
		if (!empty($g)){
			foreach($g as $k => $v){
				if ($k == $key && ($num <= $v || $isEmail)){
					return false;
				}
			}
			$g[$key] = $num;
		}
		else {
			$g = array();
			$g[$key] = $num;
		}
		$this->info['groups'] = $g;
		$this->save();
		Master::back_data($this->uid,$this->b_mol,'selectGroup', array('id'=>$this->info['groups']['key']));
        if ($isEmail) {
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_refresh(52);
        }

		return true;
	}

	public function isOverGroup($group){
        $g = $this->info['groups'];
        if (!empty($g)){
            foreach($g as $k => $v){
                if ($k == $group){
                    return true;
                }
            }
        }
        return false;
    }

	/*
	 * 增加门客羁绊
	 */
	public function addHeroJB($id, $num){
        Game::cmd_flow(6001, $id, $num, $num);
        $old_lv = $this->getHeroJBLv($id);
		$this->updateValue($id, $num, 'heroJB');
		$new_lv = $this->getHeroJBLv($id);
		if ($old_lv != $new_lv){
		    Master::add_hero_rst($id);
		}
		$Act2003Model = Master::getAct2003($this->uid);
		$Act2003Model->checkHeroJibanUnlock($id);
        //活动消耗 - 限时伙伴羁绊涨幅
        $HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->chongbang_huodong('huodong6166',$this->uid,$num);
		
		$TeamModel = Master::getTeam($this->uid);
		$TeamModel->reset(1);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(12, $num);
	}

	/*
	 * 增加红颜羁绊
	 */
	public function addWifeJB($id, $num){
        Game::cmd_flow(6002, $id, $num, $num);
        $old_lv = $this->getWifeJBLv($id);
		$this->updateValue($id, $num, 'wifeJB');
        $new_lv = $this->getWifeJBLv($id);
        if ($old_lv != $new_lv){
            Master::add_wife_rst($id);
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(2);
        }
	}

	/*
	 * 增加门客声望
	 */
	public function addHeroSW($id, $num){
		if ($id == 0){
            Game::cmd_flow(6004, $id, $num, $num);
			$this->addRoleSW($num);
		}
		else{
            Game::cmd_flow(6003, $id, $num, $num);
			$this->updateValue($id, $num, 'heroSW');
		}		
	}

	/*
	 * 增加个人声望
	 */
	public function addRoleSW($num){
		$belief = $this->info['belief'];
		$belief += $num;
		$this->info['belief'] = $belief;
		$this->save();
		Master::back_data($this->uid,$this->b_mol,'belief', array('id'=>$this->info['belief']));
	}

	/*
	 * 获取门客声望
	 */
	public function getHeroSW($id){
		if ($id == 0)return $this->info['belief'];
        return $this-> getNum($id, 'heroSW');
	}
	/*
	 * 获取最大声望值
	 */
	public function getMaxSW(){
		$max = $this->info['belief'];
		$arr = $this->info['heroSW'];
		if (!empty($arr)){
			foreach ($arr as $v){
				$max = $v['num'] > $max?$v['num']:$max;
			}
		}
		return $max;
	}

	/*
	 * 增加妃子羁绊
	 */
	public function getWifeJB($id){
        return $this-> getNum($id, 'wifeJB');
	}
	/*
	 * 增加门客羁绊
	 */
	public function getHeroJB($id){
		return $this-> getNum($id, 'heroJB');
	}

    public function getHeroJBLv($id){
	    $jb = $this->getHeroJB($id);
	    $hero = Game::getcfg_info('hero', $id);
        return $this-> getJBlv($jb, empty($hero)?4:$hero['star']);
    }

    public function getWifeJBLv($id){
        $jb = $this->getWifeJB($id);
        return $this-> getJBlv($jb, 99);
    }

    public function getJBlv($jb, $star){
	    $list = Game::getcfg('jinban_lv');
	    $lv = 1;
	    foreach ($list as $v){
	        if ($v['yoke'] <= $jb && $v['star'] == $star){
	            $lv = $v['level'];
            }
        }
        return $lv;
    }

	private function getNum($id, $type){
		$arr = $this->info[$type];
		if (!empty($arr)){
			foreach ($arr as $v) {
				if ($v['id'] == $id){
					return $v['num'];
				}
			}
		}
		return 0;
	}

	private function updateValue($id, $num, $type){
		$arr = $this->info[$type];
		$update = array();
		if ($arr == null){
			$arr = $this->info[$type] = array();
		}
		
		$l = count($arr);
		$isFind = false;
		$u = null;
		for ($i = 0; $i < $l; $i++){
			if ($arr[$i]['id'] == $id){
				$arr[$i]['num'] = $arr[$i]['num'] + $num;
				$isFind = true;
				$u = $arr[$i];
			}
		}
		if (!$isFind){
			$u = array('id'=>$id, 'num'=>$num);
			$arr[] = $u;
		}
		$this->info[$type] = $arr;
		if (!empty($u)){
			$update[] = $u;
			Master::back_data($this->uid,$this->b_mol, $type, $update, true);
			$this->save();
		}
	}

	/*
	 * 保存操作
	 */
	public function save(){
		//基于活动类的存储
		$ActModel = Master::getAct($this->uid,$this->atype);
		$ActModel->setAct($this->atype,array(
			'id'=>$this->hid,
			'data' => $this->info,
		));
		// $this->_update = true;
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		Master::back_data($this->uid,$this->b_mol,'heroJB', $this->info['heroJB']);
		Master::back_data($this->uid,$this->b_mol,'wifeJB', $this->info['wifeJB']);
		Master::back_data($this->uid,$this->b_mol,'heroSW', $this->info['heroSW']);
		Master::back_data($this->uid,$this->b_mol,'belief', array('id'=>$this->info['belief']));
		Master::back_data($this->uid,$this->b_mol,'selectGroup', array('id'=>$this->info['groups']['key']));
	}
}
