<?php
require_once "ActBaseModel.php";
/*
 * 学院学习类
 */
class Act16Model extends ActBaseModel
{
	public $atype = 16;//活动编号
	
	public $comment = "学院学习";
	public $b_mol = "school";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//学院学习信息
		//空  / 没有人在学习
	);
	
	/*
	//每张桌子的初始化信息
	public $_init_info = array(
		'hid' => 1,	//门客ID 0 没人
		'over' => 0,//下课时间
	);
	*/
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		$outf = array();
		$in_heros = array(); //正在学习中的英雄ID列表
		if(isset($this->info['spTime'])){
			if(!Game::is_today($this->info['spTime'])){
				$this->info['spTime'] = Game::get_now();
				$this->info['spCount'] = 0;
				$this->save();
			}
		}else{
			$this->info['spTime'] = Game::get_now();
			$this->save();
		}
		if(empty($this->info['info'])){
			$this->info['info'] = array();
			$outf['info'] = array();
		}
		foreach ($this->info['info'] as $id => $dmsg){
			$h_msg = array(
				'id' => $id,
				'hid' => $dmsg['hid'],
				'cd' => array(
					'next' => Game::dis_over($dmsg['over']),
					'label' => 'school',
				),
			);
			$in_heros[$dmsg['hid']] = 1;
			$outf['info'][] = $h_msg;
		}
		$outf['spCount'] = isset($this->info['spCount'])?$this->info['spCount']:0;
		$this->outf = $outf;
		$this->hids = $in_heros;//开始学习判定有没有在学习中用
	}
	
	/*
	 * 开始学习
	 */
	public function start($id,$hid){
		//这个英雄 是不是正在学习中
		if ($this->hids[$hid]){
			Master::error(COLLEGE_HERO_LEARNING);
		}
		//这个座位 有没有人
		if (isset($this->info['info'][$id])
		&& $this->info['info'][$id]['hid'] > 0){
			Master::error(COLLEGE_SEATE_IS_TAKEN);
		}
		
		//书桌ID 超上限
		$Act15Model = Master::getAct15($this->uid);
		$Act15Model->click_id($id);
		
		//开始学习
		if(empty($this->info['info'][$id])){
			$this->info['info'][$id] = array('hid' => $hid,'over' => Game::get_over(10800));
		}else {
			$this->info['info'][$id] = array(
				'hid' => $hid,	//门客ID 0 没人
				'over' => Game::get_over(10800),//下课时间
//            'over' => Game::get_over(60),//下课时间
			);
		}
	

		$this->save();
	}
	/*
	 * 完成学习
	 */
	public function over($id){
		//这个座位 有没有人
		if (empty($this->info['info'][$id]['hid'])){
			Master::error(COLLEGE_SEATE_UN_TAKEN);
		}
		//完成学习的英雄ID
		$hid = $this->info['info'][$id]['hid'];
		
		//时间完成
		if (!Game::is_over($this->info['info'][$id]['over'])){
			Master::error(COLLEGE_NO_TIME_YET);
		}

		$Act6105Model = Master::getAct6105($this->uid);
		$Act6105Model->addExp(Game::getcfg_param("school_exp"));

		//英雄完成学习操作
		$this->_over_study($hid);
		
		//下课
		$this->info['info'][$id] = array(
			'hid' => 0,	//门客ID 0 没人
			'over' => 0,//下课时间
		);
		
		$this->save();
		return $hid;
	}
	
	/*
	 * 一键完成学习
	 */
	public function allover(){
		$over_num = 0;//完成学习的同学数量
		//遍历座位表
        $exp = Game::getcfg_param("school_exp");
		foreach ($this->info['info'] as $id => $dsk){
			if ($dsk['hid'] > 0
			&& Game::is_over($dsk['over']) ){
				$this->_over_study($dsk['hid']);
				$over_num ++;
				
				//下课
				$this->info['info'][$id] = array(
					'hid' => 0,	//门客ID 0 没人
					'over' => 0,//下课时间
				);
			}
		}

		if (empty($over_num)){
			Master::error(COLLEGE_ALL_HERO_NO_TIME_YET);
		}else{
            $Act6105Model = Master::getAct6105($this->uid);
            $Act6105Model->addExp($exp * $over_num);
			$this->save();
		}

	}

	/*
	 * 一键开始学习
	 */
	public function allstart($arr){
        $desk = 0;//座位ID
        $start = 0;//学习次数
        $hids = empty($this->hids)?array():$this->hids;
        $Act15Model = Master::getAct15($this->uid);
		$Act129Model = Master::getAct129($this->uid);
        for($i=1;$i<=$Act15Model->info['desk'];$i++){
            if(isset($this->info['info'][$i])//座位有人就跳过
                && $this->info['info'][$i]['hid'] > 0){
                $desk++;
                continue;
            }
            foreach($arr as $hid){
                if ($i != $hid['id']){
                    continue;
                }
                //这个英雄 是不是正在学习中
                if ($hids[$hid['hid']]){
                    continue;
                }
				$isBanish = $Act129Model->isBanish($hid['hid']);
				if($isBanish){
					continue;
				}
                //开始学习
                $this->info['info'][$i] = array(
                    'hid' => $hid['hid'],	//门客ID 0 没人
                    'over' => Game::get_over(10800),//下课时间
                );
                $hids[$hid['hid']] = 1;
                $desk++;
                $start++;
                break;
            }
        }

        if($desk == $Act15Model->info['desk']){
            Master::error_msg(COLLEGE_DESK_FULL);
        }else{
            Master::error_msg(COLLEGE_RECORD_IN);
        }
        $this->save();
        return $start;
    }
	
	/*
	 * 一个英雄
	 * 完成学习
	 */
	private function _over_study($hid){

	    //是否有场景触发奖励
        $Act74Model = Master::getAct74($this->uid);
        $add = $Act74Model->get_sAdd(3);

		$scale = $this->getScale($hid);
		
		$Act757Model = Master::getAct757($this->uid);
		$skillRate = $Act757Model->	getSkillProp(5);

        //书籍经验
        $exp = Game::getcfg_param("school_study_exp");
        $skill = Game::getcfg_param("school_skill_exp");
        $sjAdd = ($exp + floor($exp * $add / 10000) + ceil($exp * $skillRate[$hid] / 100)) * $scale;
        //技能经验
        $jnAdd = ($skill + floor($skill * $add / 10000) + ceil($exp * $skillRate[$hid] / 100)) * $scale;

		Master::add_item($this->uid,5,$hid,$sjAdd);//书籍经验
		Master::add_item($this->uid,6,$hid,$jnAdd);//技能经验
		
		//神迹
//		$Act65Model = Master::getAct65($this->uid);
//		if ($Act65Model->rand(2)){
//			//触发神迹:学而不倦 外加两倍?
//			Master::add_item($this->uid,5,$hid,$sjAdd);//书籍经验
//			Master::add_item($this->uid,6,$hid,$jnAdd);//技能经验
//
//			Master::add_item($this->uid,5,$hid,$sjAdd);//书籍经验
//			Master::add_item($this->uid,6,$hid,$jnAdd);//技能经验
//		}
	}

	private function getScale($hid){
        $hData = Game::getcfg_info('hero', $hid);
        $w = date("w");
        $issame = $hData["spec"][0] == 5 || $hData["spec"][0] == 6;
        $issame = $issame || $hData["spec"][0] == $w;
        $s2 = count($hData["spec"]) > 1?$hData["spec"][1]:-1;
        $issame = $issame || $s2 == $w;
        $Act6105Model = Master::getAct6105($this->uid);
        $lvSys = Game::getcfg_info('school_level', $Act6105Model->info['level']);
        $bei = 1;
        if ($lvSys['crit'] > rand(0, 10000)){
            $bei = 2;
        }
        if (!empty($hData) && $w > 0 && $w < 5 && !$issame){
            return 1 * $bei;
        }
        return 2 * $bei;
    }

	//加速完成学习
    public function speedFinish($id){
    	//这个座位 有没有人
		if (empty($this->info['info'][$id]['hid'])){
			Master::error(COLLEGE_SEATE_UN_TAKEN);
		}
		//学习时间完成了
		if (Game::is_over($this->info['info'][$id]['over'])){
			Master::error(COLLEGE_NO_TIME_OVER);
		}
		//今天加速的次数
		if(!isset($this->info['spCount'])){
			$this->info['spCount'] = 0;
		}
		$UserModel = Master::getUser($this->uid);
		$vip = Game::getcfg_info('vip',$UserModel->info['vip']);
		if($this->info['spCount'] >= $vip['shuyuancd']){
			//次数用完
			Master::error(COLLEGE_SPEED_TIMES_MAX);
		}
		$cdConsume = Game::getcfg_info('cd_consume',1);
		//当前次数为0 需要加1 获取一次消耗
		$cost = $cdConsume[$this->info['spCount']+1]['cost'];
		foreach($cost as $v){
			Master::sub_item2($v);
        }
		$this->info['spCount'] += 1;
		$this->info['info'][$id]['over'] = Game::get_now();
		$this->save();
    }

}
