<?php
require_once "ActBaseModel.php";
/*
 * 徒弟历练
 */
class Act6133Model extends ActBaseModel
{
    public $atype = 6133;//活动编号

    public $comment = "徒弟历练-历练列表";
    public $b_mol = "son";//返回信息 所在模块
    public $b_ctrl = "lilianList";//返回信息 所在控制器
    public $hd_mail = array();//节日邮件配置
    public $s_outback = array();

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * 'practices'=>
         *            array(
         *              'did'=>   array('sid'=>0,'time'=>0),          历练时间
         *            ),
         * 'city' => array('sid'=>array('city'=>0,'time'=>0)),        城市路线
         * 'list'=>  array(''=>0),                                    历练状态 0:未历练 1:历练中或历练结束未领取奖励
         * 'score'=> array('sid'=>0),                                 历练积分
        */
    );

    public function make_out()
    {

        $outf= array();
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
        $Act6134Model = Master::getAct6134($this->uid);
        if(!empty($this->info['practices'])){

            foreach ($this->info['practices'] as $did => $val){
                $end = 0; $cityid = 1; $msgId = 0;
                //如果位置上有人 获取数据

                if (!empty($val['sid']) && !empty($val['stime'])){

                    foreach ($this->info['city'][$val['sid']] as $ck => $cv){

                        //最后一个元素的key
                        $maxkey = count($this->info['city'][$val['sid']])-1;

                        //历练结束时间
                        $end = $this->info['city'][$val['sid']][$maxkey]['time']*60;

                        //如果历练时间小于15分钟 城市停留在顺天府  书信不提示和发送
                        if ($_SERVER['REQUEST_TIME']>($val['stime']+60*15)){

                            if ($cv['msgId'] != 0){

                                //判断书信是否存在

                                $isExist = $Act6134Model->isExist($val['sid'],$val['stime'] + $cv['time']*60);

                                if (!$isExist){
                                    $Act6134Model->mailDelivery($val['sid'],$cv['msgId'],$cv['city'],$val['stime']+$cv['time']*60);
                                }

                            }

                            //用时间判断当前所在城市  如果到最后一个就直接返回城市
                            if ( Game::dis_over($cv['time']*60+$val['stime'] ) != 0 || $ck == $maxkey){
                                $msgId = $cv['msgId'];
                                //当前所在城市
                                $cityid = $cv['city'];break;
                            }
                        }
                    }
                }

                $outf['info'][] = array(
                    'id' => $did,
                    'sid' => $val['sid'],
                    'msgId' => $msgId,
                    'cityId'=>$cityid,
                    'cd' => array(
                        'next' => empty($val['stime']) ? $val['stime'] : Game::dis_over($val['stime'] + $end),
                        'label' => 'lilian',
                    ),
                );
            }
        }
        $outf['spCount'] = $this->info['spCount'];
        $this->outf = $outf;

    }

    /**
     * 选择徒弟历练
     * @param $sid
     * @param $did
     * @param $travel
     * @param $luggage
     */
    public function play($sid,$did,$travel,$luggage,$localep2){
        //判断席位能不能坐
        $Act6132Model = Master::getAct6132($this->uid);
        $Act6132Model->click_id($did);
        //席位上有没有人
        if(!empty($this->info['practices'][$did]['sid'])){
            Master::error(CALMYUAN_001);
        }
        //判断徒弟可不可以历练
        if(isset($this->info['list'][$sid])){
            Master::error(CALMYUAN_002);
        }
        //属性校准
        $TeamModel  = Master::getTeam($this->uid);
        if ($TeamModel->info['allep'][2] != $localep2){
            Master::error(CALMYUAN_004);
        }
        $this->_lilian($sid,$did,$travel,$luggage,$localep2);
        $this->save();

        //返回徒弟信息
        Master::back_data($this->uid,"son","sonList",$this->s_outback,true);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(59,1);
    }

    /**
     * 一键历练
     * @param $arr
     */
    public function oneKeyPlay($arr){
        $num = 0;
        foreach ($arr as $v){
            $sid        = $v['sid'];
            $did        = $v['did'];
            $travel     = $v['travel'];
            $luggage    = $v['luggage'];
            $localep2   = $v['localep2'];
            //判断席位能不能坐
            $Act6132Model = Master::getAct6132($this->uid);
            $Act6132Model->click_id($did);
            //席位上有没有人
            if(!empty($this->info['practices'][$did]['sid'])){
                continue;
            }
            //判断徒弟可不可以历练
            if(isset($this->info['list'][$sid])){
                Master::error(CALMYUAN_002);
            }
            //属性校准
            $TeamModel  = Master::getTeam($this->uid);
            if ($TeamModel->info['allep'][2] !=$localep2){
                Master::error(CALMYUAN_004);
            }
            $this->_lilian($sid,$did,$travel,$luggage,$localep2);
            $num++;
        }
        if($num == 0){
            Master::error(CALMYUAN_006);
        }
        $this->save();
        Master::back_data($this->uid,"son","sonList",$this->s_outback,true);
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(59,$num);
        return $num;
    }

    /**
     * 徒弟历练归来领取奖励
     * @param $sid
     * @param $did
     */
    public function recall($sid,$did){
        //判断徒弟是否历练
        if(empty($this->info['list'][$sid]) && empty($this->info['city'][$sid]) && empty($this->info['score'][$sid])){
            Master::error(CALMYUAN_005);
        }
        //判断位置上有木有人
        if(empty($this->info['practices'][$did]['sid']) || $this->info['practices'][$did]['sid']!=$sid){
            Master::error(CALMYUAN_005);
        }
        $end = $this->info['city'][$sid][count($this->info['city'][$sid])-1] ['time']*60;
        //到期了没
        $expiry_time = $this->info['practices'][$did]['stime']+$end;
        if(Game::dis_over($expiry_time)){

            Master::error(LILIAN_NOT_ERR);
        }
        //领取奖励
        $score = $this->info['score'][$sid];
        $this->_addReward($score);
        $this->info['practices'][$did] = array(
            'sid' => 0,
            'stime' => 0
        );

        unset($this->info['list'][$sid]);
        unset($this->info['city'][$sid]);
        unset($this->info['score'][$sid]);
        $this->save();
        $SonModel = Master::getSon($this->uid);
        $this->s_outback[] = $SonModel->getBase_buyid($sid);
        Master::back_data($this->uid,"son","sonList",$this->s_outback,true);
    }

    /**
     * 一键徒弟历练归来领取奖励
     * @param $sid
     * @param $did
     */
    public function oneKeyRecall(){
        if (!empty($this->info['list'])){
            $sids = array_keys($this->info['list']);
        }
        $isUp = true;
        $SonModel = Master::getSon($this->uid);

        foreach ($this->info['practices'] as $k=>$v){
            $sid = $v['sid'];
            if ($sid != 0 && in_array($sid,$sids)){
                $end = $this->info['city'][$sid][count($this->info['city'][$sid])-1]['time']*60;
                $expiry_time = $v['stime']+$end;
                if(Game::dis_over($expiry_time)){
                    continue ;
                }
                //领取奖励
                $score = $this->info['score'][$sid];
                $this->_addReward($score);
                $this->info['practices'][$k]['sid'] = 0;
                $this->info['practices'][$k]['stime'] = 0;
                
                unset($this->info['list'][$sid]);
                unset($this->info['city'][$sid]);
                unset($this->info['score'][$sid]);
                $this->s_outback[] = $SonModel->getBase_buyid($sid);
                $isUp = false;
            }
        }
        if ($isUp){
            Master::error(SON_LI_LIAN_ZHENG_ZAI);
        }
        $this->save();
        Master::back_data($this->uid,"son","sonList",$this->s_outback,true);
        
    }

    /**
     * 判断是否在历练
     * @param $sid
     */
    public function isPlay($sid){
        return isset($this->info['list'][$sid])&&
               isset($this->info['city'][$sid])&&
               isset($this->info['score'][$sid])? true : false;
    }

    /**
     * 改变已读书信记录的状态
     * @param $sid        //徒弟id
     */
    public function clearMsgId($sid,$time){
        if(isset($this->info['city'][$sid]) && !empty($this->info['city'][$sid])){
            $stime = 0; $index = 0;
            foreach ($this->info['practices'] as $x){
                if($x['sid'] == $sid){
                    $stime = $x['stime'];
                }
            }
            foreach ($this->info['city'][$sid] as $k=>$v){
                if (($v['time']*60+$stime) == $time){
                    $index=$k;
                }
            }
            $this->info['city'][$sid][$index]['msgId'] = 0;
            $this->save();
        }

    }

    /**
     * 内部函数 发放奖励
     * @param $score
     */
    private function _addReward($score){
        $practice_reward = Game::getcfg('practice_reward');
        $Act757Model = Master::getAct757($this->uid);
        $skillRate = $Act757Model->getSkillProp(8);
        for ($i = 1;$i <= count($practice_reward); $i++){
            if ($score == 0 || $score < $practice_reward[$i]['score']){
                $rewards = json_decode($practice_reward[$i]['reward'],true);
                $index = Game::get_rand_key1($rewards,'weight');
                $reward = $rewards[$index];
                // $reward = $rewards[rand(0,count($rewards)-1)];
                $addRate = $skillRate[$reward['id']]/100;
                $addCount = ceil($reward['count']*(1+$addRate));
                Master::add_item($this->uid,KIND_ITEM, $reward['id'], $addCount);
                break;
            }
        }
    }

    /**
     * 内部函数 匹配信件数据
     * @param $city
     * @param $sex
     */
    private function _randMail($city,$sex){
        $practice_mail = Game::getcfg('practice_mail');
        $kind = array($sex,3);
        $mailID = array();
        //特殊节日邮件
        Common::loadModel('HoutaiModel');
        foreach ($this->hd_mail as $m=>$n){
            $hd_cfg = HoutaiModel::get_huodong_info($n);
            if (!empty($hd_cfg)){
                $kind[] = $m;
            }
        }
        foreach ($practice_mail as $r){
            $citys = explode('|',$r['city']);
            if (in_array($r['sex'],$kind) && in_array($city,$citys) ){
                $mails  = explode('|',$r['mail']);
                $mailID = array_merge($mailID,$mails);
            }
        }
        shuffle($mailID);
        return $mailID[0];
    }

    /**
     * 内部函数 随机历练城市路线
     * @param $prob
     * @param $direction
     * @param $cityNum
     * @param $sex
     */
    private function _randCity($prob,$direction,$cityNum,$sex){
        $Act6134Model = Master::getAct6134($this->uid);
        $judge = $Act6134Model->mailTotal();
        $practice_city = Game::getcfg('practice_city');//城市配置
        $option = array(1=>'east',2=>'south',3=>'west',4=>'north');
        $randctiy = array();
        $citys_score=0;
        $temp = 1;//存上个城市的临时变量
        for ($i = 0; $i < $cityNum; $i++) {

            if ($prob == 100){
                //如果是全随机
                $rand = rand(1,4);
            }else{
                if (rand(1,100)<=$prob){
                    //偏一个方向
                    $rand = $direction;
                }else{
                    //主方向随不到 就全随机
                    $rand = rand(1,4);
                }
            }

            $randctiy[$i]=array(
                'city'=>  $practice_city[$temp][$option[$rand]],
                'time'=>  $practice_city[$practice_city[$temp][$option[$rand]]]['time'],
                'score'=> $practice_city[$practice_city[$temp][$option[$rand]]]['score'],
            );
            $temp = $randctiy[$i]['city'];
        }

        //各个城市的时间点和信件id

        foreach ($randctiy as $k => $v){
            if (count($randctiy) == 1){
                $randctiy[$k]['msgId'] = $judge ? $this->_randMail($v['city'],$sex) : 0;
                $citys_score += $randctiy[$k]['score'];
            }else{
                if (rand(0,50)<100){
                    $randctiy[$k]['msgId'] = $judge ? $this->_randMail($v['city'],$sex) : 0;
                }else{
                    $randctiy[$k]['msgId'] = 0;
                }
                $randctiy[$k]['time'] += $randctiy[$k-1]['time'];
                $citys_score += $randctiy[$k]['score'];
            }
            unset($randctiy[$k]['score']);
        }

        $randctiy['score'] = $citys_score;
        return $randctiy;

    }

    /**
     * 历练内部函数
     * @param $sid
     * @param $did
     * @param $travel
     * @param $luggage
     */
    private function _lilian($sid,$did,$travel,$luggage,$localep2){
        $SonModel = Master::getSon($this->uid);
        $son_info = $SonModel->check_info($sid);
        if (!in_array($son_info['state'],array(1,2,3,4,5,9,10))){
            Master::error(SON_LI_LIAN_ERROR);
        }
        $score = 0;//得分
        //徒弟品质
        $practice_score_info = Game::getcfg_info('practice_score',$son_info['talent']);
        //徒弟品质等分
        $score += $practice_score_info['score'];
        //行李
        $practice_luggage_info = Game::getcfg_info('practice_luggage',$luggage);
        //行李得分
        $score += $practice_luggage_info['score'];
        //经过几个城市
        $cityNum = rand($practice_luggage_info['min'],$practice_luggage_info['max']);
        //出行方式
        $practice_travel_info = Game::getcfg_info('practice_travel',$travel);
        //出行得分
        $score += $practice_travel_info['score'];
        //出行方式扣钱
        if ($practice_travel_info['type'] == 1){
            //元宝
            Master::sub_item($this->uid,KIND_ITEM,1,$practice_travel_info['money']);
        }else{
            //银两(粮食)
            Master::sub_item($this->uid,KIND_ITEM,3,$practice_travel_info['money']);

        }

        //行李  判断是消耗金钱还是道具
        if ($practice_luggage_info['itemid'] == 0){
            $money = ceil($practice_luggage_info['max']*30/ceil($localep2/800)*0.5*$localep2*$son_info['talent']*0.3);
            //扣钱
            Master::sub_item($this->uid,KIND_ITEM,3,$money);
        }else{
            //扣除道具
            Master::sub_item($this->uid,KIND_ITEM,$practice_luggage_info['itemid'],$practice_luggage_info['num']);
        }
        //出行方向概率
        $prob = $practice_travel_info['prob'];
        //方向
        $direction = $practice_travel_info['direction'];
        //城市路线
        $cityData = $this->_randCity($prob,$direction,$cityNum,$son_info['sex']);
        //计算总积分
        $cityData['score'] += $score;
        $this->info['score'][$sid] = $cityData['score'];
        unset($cityData['score']);
        //储存城市路线
        $this->info['city'][$sid] = $cityData;
        //
        $this->info['practices'][$did] = array(
            'sid' => $sid,
            'stime' => Game::get_now()
        );
        $this->info['list'][$sid] = 1;
        $this->s_outback[] = $SonModel->getBase_buyid($sid);
    }

    public function speedFinish($sid,$did){
        if(empty($this->info['list'][$sid]) && empty($this->info['city'][$sid]) && empty($this->info['score'][$sid])){
            Master::error(CALMYUAN_005);
        }
        //判断位置上有木有人
        if(empty($this->info['practices'][$did]['sid']) || $this->info['practices'][$did]['sid']!=$sid){
            Master::error(CALMYUAN_005);
        }
        $end = $this->info['city'][$sid][count($this->info['city'][$sid])-1] ['time']*60;
        //到期了没
        $this->info['practices'][$did]['stime'] = Game::get_now() - $end;

        $UserModel = Master::getUser($this->uid);
        $vip = Game::getcfg_info('vip',$UserModel->info['vip']);
        if($this->info['spCount'] >= $vip['lilian']){
            //次数用完
            Master::error(LILIAN_SPEED_TIMES_MAX);
        }
        $cdConsume = Game::getcfg_info('cd_consume',2);
        //当前次数为0 需要加1 获取一次消耗
        $cost = $cdConsume[$this->info['spCount']+1]['cost'];
        foreach($cost as $v){
            Master::sub_item2($v);
        }
        $this->info['spCount'] += 1;

        $this->save();
    }
}
