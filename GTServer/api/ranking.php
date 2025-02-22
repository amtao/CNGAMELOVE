<?php
//排行榜
class RankingMod extends Base
{
	/*
	 * 刷新势力榜
	 */
	public function paihang($params){
		$type = Game::intval($params, 'type');
        $rightTypes = array(0=>0, 1=>1, 2=>2, 3=>3, 4=>301, 5=>302, 6=>11);
        if (!isset($rightTypes[$type])) {
            Master::error(PARAMS_ERROR.__CLASS__.__METHOD__.__LINE__);
        }
        $type = $rightTypes[$type];
        if (empty($type)) {
            $Redis1Model = Master::getRedis1();
            $Redis1Model->back_data();
            $Redis1Model->back_data_my($this->uid);//我的排名

            $Redis2Model = Master::getRedis2();
            $Redis2Model->back_data();
            $Redis2Model->back_data_my($this->uid);

            $Redis3Model = Master::getRedis3();
            $Redis3Model->back_data();
            $Redis3Model->back_data_my($this->uid);
        }else {
            $redisName = "getRedis{$type}";
            if (!method_exists('Master', $redisName)) {
                Master::error(PARAMS_ERROR.__CLASS__.__METHOD__.__LINE__);
            }
            $RedisModel = Master::$redisName();
            $RedisModel->back_data();
            if ($type != 302) {
            	if($type == 11){
            		$Act40Model = Master::getAct40($this->uid);
					$cid = $Act40Model->info['cid'];
					if(!empty($cid)){
						$RedisModel->back_data_my($cid);
					}
            	}else{
            		$RedisModel->back_data_my($this->uid);
            	}
            }
        }
	}
	
	/*
	 * 膜拜1:势力榜  2:关卡榜  3:亲密榜
	 */
	public function mobai($params){
		$type = Game::intval($params,'type');
        $rightTypes = array(1,2,3,4,5,6);
        if (!in_array($type,$rightTypes)) {
            Master::error(PARAMS_ERROR.__CLASS__.__METHOD__.__LINE__);
        }
		$Act17Model = Master::getAct17($this->uid);
		$Act17Model->mobai($type);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(19,1);

	}

    /*
     * 刷新对应排行榜
     */
    public function flush($params){
        $type = Game::intval($params, 'type');
        if (!empty($this->needParamsKua[$type])){
            $hd_key = "huodong_{$this->needParamsKua[$type]}";
            Common::loadModel('HoutaiModel');
            $hd_cfg = HoutaiModel::get_huodong_info($hd_key);
            $hd_id = $hd_cfg['info']['id'];
        }elseif (!empty($this->needParams[$type])){
            $hd_key = "huodong_{$type}";
            //获取宫殿信息
            if (in_array($type,array(315))){
                $Act40Model = Master::getAct40($this->uid);
                $cid = $Act40Model->info['cid'];
            }
            Common::loadModel('HoutaiModel');
            $hd_cfg = HoutaiModel::get_huodong_info($hd_key);
            $hd_id = $hd_cfg['info']['id'];
            $type = $this->needParams[$type];
        }
        $redisName = "getRedis{$type}";
        if (!method_exists('Master', $redisName)) {
            Master::error(PARAMS_ERROR.__CLASS__.__METHOD__.__LINE__);
        }

        $RedisModel = $hd_id?Master::$redisName($hd_id):Master::$redisName();
        $RedisModel->back_data_flush();
        $RedisModel->back_data_my(isset($cid)?$cid:$this->uid);

    }

    protected  $noParams = array(
        1 => 1,    //势力榜
        2 => 2,    //关卡榜
        3 => 3,    //好感榜
    );
    protected  $needParamsKua = array(
        131  => 313,  //跨服势力个人冲榜
        132  => 313,  //跨服势力区服冲榜
        137  => 314,  //跨服好感个人冲榜
        138  => 314,  //跨服好感区服冲榜
        305  => 300,  //跨服宫斗个人冲榜
        306  => 300,  //跨服宫斗区服冲榜
    );
    protected  $needParams = array(
        250  => 101,  //联盟冲榜
        251  => 102,  //关卡冲榜
        252  => 103,  //势力冲榜
        253  => 104,  //知己好感涨幅榜
        254  => 105,  //宫斗积分涨幅冲榜
        255  => 109,  //阅历消耗冲榜
        256  => 110,  //宴会积分涨幅榜
        257  => 257,  //名声消耗冲榜
        258  => 258,  //知己能力涨幅榜
        259  => 259,  //银两消耗榜
        315  => 315,  //宫殿宫斗冲榜
        6135 => 6135, //珍宝积分冲榜
        6142 => 6142, //争奇斗艳
        6123 => 6123, //盛装出席
        6166 => 6166, //伙伴羁绊涨幅榜
        6167 => 6167, //伙伴资质涨幅榜
        6179 => 6179, //限时御膳房烹饪次数
        6212 => 6212, //御花园偷取晨露次数
        6213 => 6213, //御花园种植次数
        6216 => 6216, //晨露消耗冲榜
        6217 => 6217, //知己技能经验涨幅冲榜
        6218 => 6218, //徒弟势力冲榜
        6015 => 6015, //抢汤圆
        6222 => 6222, //清明踏青
        6231 => 6231, //抢糕点
        6232 => 6232, //热气球
        6234 => 6234, //荷诞日
        6241 => 6241, //七夕
        8003 => 8003, //许愿池
        6183 => 6183, //堆雪人
        8005 => 8005, //堆雪人
        8018 => 8018, //三消活动
    );
}
