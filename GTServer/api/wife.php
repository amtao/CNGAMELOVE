<?php
//知己操作 目前整个系统废弃
class wifeMod extends Base
{
	  
	public function xxoo($params){
		//知己ID
		$wifeId = Game::intval($params,'id');
        $wifetravel=$this->_xxoogetbaby($wifeId,true);
        //出游弹窗
        Master::back_custom_data('wife','travel',$wifetravel);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(53,1);
        //限时-知己出游次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6173',1);
        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
        //重新构造阵法 刷新子嗣数量标签
        if ($wifetravel['type'] == 1)$TeamModel->reset(3);
	}

    /*
     * 随机出游
     */
    public function sjcy(){
        //扣除假期
        $Act6131Model = Master::getAct6131($this->uid);
        $Act6131Model->apao();
        $WifeModel = Master::getWife($this->uid);
        //随机一位知己
        $wifeId=array_rand($WifeModel->info);
        $wifetravel = $this->_xxoogetbaby($wifeId,false);
        //出游弹窗
        Master::back_custom_data('wife','travel',$wifetravel);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(53,1);
        //限时-知己出游次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6173',1);

        //刷新知己缓存
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
        //刷新徒弟缓存
        if ($wifetravel['type'] == 1)$TeamModel->reset(3);


    }

    /*
     * 一键出游
     */
    public function yjxxoogetbaby(){
        //扣除全部假期 返回点数
        $Act6131Model = Master::getAct6131($this->uid);
        $p_num = $Act6131Model->qunp();

        $WifeModel = Master::getWife($this->uid);

        $yjwin = array();
        for($i = 0 ; $i < $p_num ; $i++){
            //随机一个知己
            $wifeId = array_rand($WifeModel->info);
            //宠幸
            $yjwin[] = $this->_xxoogetbaby($wifeId);
        }
        //过滤不是徒弟的
        foreach ($yjwin as $k => $v){
            if ($v['type'] != 1){
                unset($yjwin[$k]);
            }
        }
        sort($yjwin);
        Master::$bak_data['a']["wife"]['win']['yjtravel'] = $yjwin;
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(53,$p_num);
        //限时-知己出游次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6173',$p_num);
        //刷新知己缓存
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
        //刷新徒弟缓存
        if (!empty($yjwin))$TeamModel->reset(3);

        return true;
    }

    /*
	 * 问候
	 */
    public function xxoonobaby($params){
        //知己ID
        $wifeId = Game::intval($params,'id');

        $wifehello=$this->_xxoonobaby($wifeId,true);

        //问候弹窗
        Master::back_custom_data('wife','hello',$wifehello);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(17,1);
        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
        return true;
    }

    /*
	 * 随机问候
	 */
    public function sjxo(){
        //扣除精力
        $Act11Model = Master::getAct11($this->uid);
        $Act11Model->apao();
        //知己ID
        $WifeModel = Master::getWife($this->uid);
        $wifeId = array_rand($WifeModel->info);
        $wifehello=$this->_xxoonobaby($wifeId);

        $Act11Model = Master::getAct11($this->uid);
        $Act11Model->make_out();
        //问候弹窗
        Master::back_custom_data('wife','hello',$wifehello);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(17,1);

        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
        return true;
    }

    /*
     * 一键问候
     */
    public function yjxo(){
        //扣除全部精力 返回点数
        $Act11Model = Master::getAct11($this->uid);
        $p_num = $Act11Model->qunp();

        $WifeModel = Master::getWife($this->uid);

        $yjwin = array();
        for($i = 0 ; $i < $p_num ; $i++){
            //随机一个知己
            $wifeId = array_rand($WifeModel->info);
            //宠幸
            $yjwin[] = $this->_xxoonobaby($wifeId);
        }
        Master::$bak_data['a']["wife"]['win']['yjhello'] = $yjwin;
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(17,$p_num);

        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);

        return true;
    }


    /*
	 * 内部函数
	 * 问候某个知己
	 * 返回 问候信息
	 * 参数 知己ID , 是否需要元宝 , 是否增加亲密度
	 */
    private function _xxoonobaby($wifeId , $needCash = false , $addLove = 1){
        $WifeModel = Master::getWife($this->uid);
        //知己ID合法
        $wife_info = $WifeModel->check_info($wifeId);

        //如果要钱
        if($needCash){
            //所需元宝
            $_need_cash = Game::getCfg_formula()->wife_meet_cost($wife_info['flower'],$wife_info['love']);
            Master::sub_item($this->uid,KIND_ITEM,1,$_need_cash);
        }
        //获得经验值
        $exp_add = floor($wife_info['flower']*0.8 + $wife_info['flower']/10 + 10);

        $shenji = 0;
        //神迹
        $mul = 1;

        for ($i = 1; $i <= $mul; $i++) {
            Master::add_item($this->uid,KIND_OTHER,15,$exp_add);
        }
        $exp_add *= $mul;

        $w_update = array(
            'wifeid' => $wifeId,
            'love' => $addLove,
            'exp' => $exp_add,
        );
        $WifeModel->update($w_update);

        //成就更新
        $Act36Model = Master::getAct36($this->uid);
        $Act36Model->add(15,1);
        //限时-问候知己次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6174',1);
        //舞狮大会 - 随机问候知己
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(3,1);
        return array(
            'wifeid' => $wifeId,
            'exp' => $exp_add,
            'love' => $addLove,
            'isgad' => $shenji,
            'pro' => 0,
            'type' => 0,
        );

    }


    /*
	 * 内部函数
	 * 出游某个知己
	 * 返回 出游信息
	 * 参数 知己ID , 是否需要元宝 ,是否需要收徒
	 */
    private function _xxoogetbaby($wifeId , $needCash = false){
        $WifeModel = Master::getWife($this->uid);
        //知己ID合法
        $wife_info = $WifeModel->check_info($wifeId);

        //如果要钱
        if($needCash){
            $need_cash = Game::getCfg_formula()->wife_chuyou_cost($wife_info['love']);
            Master::sub_item($this->uid,KIND_ITEM,1,$need_cash);
        }

        //获取配置数据
        $wife_chuyou_cfgs = Game::getcfg('wife_chuyou');
        //对应档位数据
        $wife_chuyou=array();
        for ($i = 0;$i<count($wife_chuyou_cfgs);$i++){

            if ($wife_info['flower']<$wife_chuyou_cfgs[$i]){

                $wife_chuyou = $wife_chuyou_cfgs[$i];break;

            }

        }
        $pupil_pro = $wife_chuyou['pupil_pro'];//徒弟概率
        $item_pro = $wife_chuyou['item_pro'];//物品概率
        $item_id = $wife_chuyou['item_id'];//获得的物品
        $rands = rand(1,10000);
        //当前未科举徒弟数量
        $team = Master::get_team($this->uid);
        //徒弟席位
        $Act12Model = Master::getAct12($this->uid);
        $seat = $Act12Model->get_seat();
        //如果席位不足
        if ($seat <= $team['smson']){
            $rands = rand($pupil_pro,10000);
        }
        //记录第一次收徒
        $Act90Model = Master::getAct90($this->uid);
        $isborn = $Act90Model->do_save();
        //第一次出游必得徒弟
        if ($isborn){
            $rands = rand(1,$pupil_pro);
        }

        if ($rands <= $pupil_pro){
            $tmp = 1;
        }elseif ($rands <= $item_pro){
            $tmp = 0;
        }else{
            $tmp = 2;
        }
        $w_update = array(
            'wifeid' => $wifeId,
            'love' => 5,
        );
        $WifeModel->update($w_update);

        //舞狮大会 - 与知己出游次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(15,1);

        switch ($tmp) {//0:物品 1:获得徒弟 2:资源
            case 0:
                $item_id= explode('|',$item_id);

                $addItems = array();
                if(!empty($item_id)){
                    foreach ($item_id as $val){

                        $addItems[] = array('id'=>$val,'kind'=>1,'count'=>1);

                    }
                }
                $num = array_rand($addItems,1);
                if(!empty($addItems)){
                    Master::add_item2($addItems[$num]);//加奖励
                    return array(
                        "type" => $tmp,
                        "itemid" => $addItems[$num]['id'],
                        "itemcount" => $addItems[$num]['count'],
                        "wifeid" => $wifeId,
                    );
                }

            case 1:
                //收徒
                $SonModel = Master::getSon($this->uid);
                $sonuid = $SonModel->addSon($wifeId);
                $son_info = $SonModel->check_info($sonuid);
                return array(
                    "type" => $tmp,
                    "babyid" => $sonuid,
                    "babysex" => $son_info['sex'],
                    "wifeid" => $wifeId,
                );
            case 2:
                //资源
                $res = json_decode($wife_chuyou['res'],1);
                $res = $res[array_rand($res,1)];
                if(!empty($res)){
                    Master::add_item2($res);//加奖励
                    return array(
                        "type" => $tmp,
                        "itemid" => $res['id'],
                        "itemcount" => $res['count'],
                        "wifeid" => $wifeId,
                    );
                }

        }

    }

	/*
	 * 内部函数
	 * 宠幸某个知己
	 * 返回 所需元宝
	 * 参数 知己ID , 是否需要元宝 , 是否增加亲密度
	 */
	private function _xxoo($wifeId , $needCash = false , $addLove = 0){
		$WifeModel = Master::getWife($this->uid);
		//知己ID合法
		$wife_info = $WifeModel->check_info($wifeId);
		
		//如果要钱
		if($needCash){
			//所需元宝
			$_need_cash = $wife_info['love'] * 10;
			$_need_cash = min($_need_cash,1000);
			Master::sub_item($this->uid,KIND_ITEM,1,$_need_cash);
		}
		
		//获得经验值
		$exp_add = $wife_info['flower'] + intval(($wife_info['flower']/10)*($wife_info['flower']/10));
		$shenji = 0;
		//神迹
        $mul = 1;
		$Act65Model = Master::getAct65($this->uid);
		if ($Act65Model->rand(6)){
			//触发神迹: 6.知己赐福
            $mul = 7;
			$shenji = 1;
		}
        for ($i = 1; $i <= $mul; $i++) {
            Master::add_item($this->uid,KIND_OTHER,15,$exp_add);
        }
        $exp_add *= $mul;
			
		$w_update = array(
			'wifeid' => $wifeId,
			'love' => $addLove,
			'exp' => $exp_add,
		);
		$WifeModel->update($w_update);
		
		return array(
			'wifeid' => $wifeId,
			'exp' => $exp_add,
			'shenji' => $shenji,
		);
	}
    /*
     * 尝试生孩子
     * 亲密度 , 是否指定宠幸
     */
    public function madeMan($wifeId,$is_tl = false){
        //当前未科举子嗣数量
        $team = Master::get_team($this->uid);
        //子嗣席位
        $Act12Model = Master::getAct12($this->uid);
        $seat = $Act12Model->get_seat();
        //如果席位不足
        if ($seat <= $team['smson']){
            return;
        }
        //执行生孩子 随机
        $prob_100 = 25;//概率50%
        //如果是指定宠幸 概率增加
        if($is_tl){
            $prob_100 = 50;//概率50%
            $Act90Model = Master::getAct90($this->uid);
            if($Act90Model->do_save()){  //第一次生孩子
                $prob_100 = 100;//概率100%
            }
        }
        $vip_cfg_info = Master::get_vip_cfg_info($this->uid);//获取当前VIP配置
        //知己生娃概率增加
        $prob_100 += $vip_cfg_info['prob_sum_prob100'];

        if (rand(1,100) <= $prob_100){
            //生孩子
            $SonModel = Master::getSon($this->uid);
            $sonuid = $SonModel->addSon($wifeId);

            //生孩子弹窗
            Master::back_win('wife','baby','id',$sonuid);

            //重新构造阵法 刷新子嗣数量标签
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(3);

            //主线任务
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(10,1);
        }
    }
	
	/*
	 * 恢复精力
	 */
	public function weige($params){
		
		//扣除全部精力 返回点数
		$Act11Model = Master::getAct11($this->uid);
		$Act11Model->huifu();
		//限时-精力丹消耗
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6172',1);
		
		return true;
	}
    /*
     * 恢复假期
     */
    public function hfjiaqi($params){
        //消耗数量
        $num = Game::intval($params,'num');
        if ($num < 1){
            Master::error(PARAMS_ERROR);
        }
        $Act6131Model = Master::getAct6131($this->uid);
        $Act6131Model->huifu($num);

        return true;
    }
	
	/*
	 * 赏赐
	 */
	public function reward($params){
		$UserModel = Master::getUser($this->uid);
		//知己ID
		$wifeId = Game::intval($params,'id');
		//道具ID
		$itemId = Game::intval($params,'itemId');

        //道具数量
        $count = Game::intval($params,'count');
        $count = empty($count)?1:$count;
		
		$WifeModel = Master::getWife($this->uid);
		//知己合法
		$wife_info = $WifeModel->check_info($wifeId);
		
		//道具合法
		$itemcfg_info = Game::getcfg_info('item',$itemId);
		if ($itemcfg_info['type'][0] != 'wife'){
			Master::error(WIFE_USE_ITEMS_ERROR);
		}
		
		//减去使用的道具
		Master::sub_item($this->uid,KIND_ITEM,$itemId,1*$count);
		
		//属性种类
		if ($itemcfg_info['type'][1] == 'love'){
			$kind = KIND_LOVE;
		}elseif ($itemcfg_info['type'][1] == 'flower'){
			$kind = KIND_FLOWER;
		}
		//加上知己属性
		Master::add_item($this->uid,$kind,$wifeId,$itemcfg_info['type'][2]*$count);
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(18,1*$count);

        //重新构造阵法 刷新知己数量标签
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(2);
		
		return true;
	}
	
	/*
	 * 升级知己技能
	 */
	public function upskill($params){
		//知己ID
		$wifeId = Game::intval($params,'id');
		//技能ID
		$skillId = Game::intval($params,'skillId');
		
		$UserModel = Master::getUser($this->uid);
		$WifeModel = Master::getWife($this->uid);
		
		//知己合法
		$wife_info = $WifeModel->check_info($wifeId);
		
		//技能ID 合法 (1级以上的技能 (已解锁) 才允许使用经验值升级)
		if (empty($wife_info['skill'][$skillId])){
			Master::error("skill_id_err_".$skillId);
		}
		//所需经验值
		$skill = $wife_info['skill'];
		
		$wife_skill_cfg = Game::getcfg_info('wife_skill',$skillId);
		$exptp = $wife_skill_cfg['add'];
		
		//等级上限判定
		Common::loadModel('SwitchModel');
		$max_level = SwitchModel::getWifeSkillMaxLevel($wife_skill_cfg['type']);
		if ($skill[$skillId] >= $max_level){
			Master::error(HERO_LEVEL_CAP);
		}
		
		$need_exp = 0;
		switch($exptp){
			case 1:
				$need_exp = intval(0.0048*pow($skill[$skillId],3)+0.01298*pow($skill[$skillId],2)+1.0483*$skill[$skillId]+5.851);
				break;
			case 2:
				$need_exp = intval(0.0099*pow($skill[$skillId],3)+0.0345*pow($skill[$skillId],2)+0.9494*$skill[$skillId]+11.276);
				break;
			case 3:
				$need_exp = intval(3.6667*pow($skill[$skillId],3)+16*pow($skill[$skillId],2)+1.3333*$skill[$skillId]+111);
				break;
		}
		//经验值够不够
		if ($wife_info['exp'] < $need_exp){
			Master::error(WIFE_SKILL_LEVEL_SHORT);
		}
		//扣除经验值 加上技能等级
		$skill[$skillId] += 1;
		$w_update = array(
			'wifeid' => $wifeId,
			'exp' => -$need_exp,
			'skill' => $skill,
		);
		$WifeModel->update($w_update);
		
		$TeamModel  = Master::getTeam($this->uid);
		$TeamModel->reset(2);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(26,1);
		
		return true;
	}

    /*
     * 送知己礼物
     *  id 知己id  sid 技能
     * */
    public function giveGift($param) {

        //知己ID
        $WifeId = Game::intval($param,'id');
        //礼物ID
        $gid = Game::intval($param,'gid');
        //礼物数量
        $num = Game::intval($param,'num');

        //知己存在
        $hero_info = Game::getcfg_info('wife',$WifeId);
        if (empty($hero_info)){
            Master::error(PARAMS_ERROR.$WifeId);
        }

        //扣除羁绊需要的道具
        $need_item = Game::getcfg_info('item',$gid);
        if ($need_item['type'][0]!='wife' || $need_item['type'][1]!='jiban' || $need_item['type'][3] != $hero_info['type']){
            Master::error(ITEMS_ERROR);
        }
        $kind = empty($need_item['kind']) ? 1: $need_item['kind'];
        Master::sub_item($this->uid, $kind, $need_item['id'],$num);
        $Act6001Model = Master::getAct6001($this->uid);
        $Act6001Model->addWifeJB($WifeId, $need_item['type'][2]*$num);

        return true;
    }

    /*
     * 知己闲谈
     *  id 知己id
     * */
    public function wchat($param) {

        //知己ID
        $wifeId = Game::intval($param,'id');
        $WifeModel = Master::getWife($this->uid);
        //知己合法
        $wife_info = $WifeModel->check_info($wifeId);
        $Act6138Model = Master::getAct6138($this->uid);
        $Act6138Model->chat($wifeId,'wife');

    }

}
