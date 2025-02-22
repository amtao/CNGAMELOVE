<?php

class BoiteMod extends Base
{
    public function __construct($uid)
    {
        parent::__construct($uid);
        $UserModel = Master::getUser($this->uid);
        $flag = Game::is_limit_level('jiulou', $this->uid, $UserModel->info['level']);
//        if ($flag == 2 && $UserModel->info['level'] < 5) {
//            //默认限制从7开启
//            Master::error(BOITE_NO_OPEN);
//        }

//        //判断新版酒楼是否开启
//        Common::loadModel('SwitchModel');
//        if (SwitchModel::isJiulouRevision($this->uid) == 0) {
//            Master::error(JIULOU_SWITCH);
//        }

    }

    /**
     * 酒楼信息
     * @param $params
     */
    public function jlInfo($params)
    {
        //个人宴会信息
        $Act170Model = Master::getAct170($this->uid);
        $Act170Model->updateJYCount();

        $data = array();
        $data['type'] = $Act170Model->info['type'];
        $data['count'] = $Act170Model->info['count'];
        $data['guancount'] = $Act170Model->info['guancount'];
        Master::back_data($this->uid, 'boite', 'yhType', $data);

        //获取联盟人员可见宴会
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        $yhshow = array();

        //官宴 获取全服可见宴会
        $Sev21Model = Master::getSev21();
        $outf = $Sev21Model->get_outf();
        if (!empty($outf)) {
            foreach ($outf as $v) {
                if ($v['uid'] == $this->uid) {
                    continue;
                }
                $yhshow[] = $v;
            }
        }

        //联盟
        if (!empty($cid)) {
            $Sev20Model = Master::getSev20($cid);
            $outf = $Sev20Model->get_outf();
            if (!empty($outf)) {
                foreach ($outf as $v) {
                    if ($v['uid'] == $this->uid) {
                        continue;
                    }
                    $yhshow[] = $v;
                }
            }

        }

        //家宴公开联盟可见
        $Sev29Model = Master::getSev29();
        $outf = $Sev29Model->get_outf();
        if (!empty($outf)) {
            foreach ($outf as $v) {
                if ($v['uid'] == $this->uid) {
                    continue;
                }
                $yhshow[] = $v;
            }
        }

        //截断只剩6个
//        $yhshow = array_slice($yhshow, 0, 6);

        Master::back_data($this->uid, 'boite', 'yhshow', $yhshow);

        //消息信息-我的历史宴会
        $Act52Model = Master::getAct52($this->uid);
        $Act52Model->back_data();

        //获取商店列表信息
        $Act171Model = Master::getAct171($this->uid);
        $Act171Model->init_data();

        //已出战的门客列表
        $Act172Model = Master::getAct172($this->uid);
        $Act172Model->checkOver();
    }

    /**
     * 举办宴会
     * @param mixed $params
     * $params['type'] :  1:家宴  2:官宴
     */
    public function yhHold($params)
    {
        $type = Game::intval($params, 'type');

        //家宴 是否公开宴会 默认0:不公开   1:公开
        $isOpen = Game::intval($params, 'isOpen');
        $isOpen = empty($isOpen)?0:$isOpen;

        //加成道具
        $addItem1 = Game::intval($params, 'food1');
        $addItem2 = Game::intval($params, 'food2');
        $addItem3 = Game::intval($params, 'food3');
        $addItem1 = empty($addItem1)?0:$addItem1;
        $addItem2 = empty($addItem2)?0:$addItem2;
        $addItem3 = empty($addItem3)?0:$addItem3;

        $Act170Model = Master::getAct170($this->uid);
        $Act170Model->open_yh($type, $isOpen, $addItem1, $addItem2, $addItem3);

        //创建帮会-跑马灯
        $UserInfo = Master::fuidInfo($this->uid);
        $Sev91Model = Master::getSev91();
        $Sev91Model->add_msg(array(108, Game::filter_char($UserInfo['name']), $type));

    }

    /**
     * 编号赴会-查询
     * @param $params
     * $params['fuid'] : 玩家id
     */
    public function yhFind($params)
    {
        $fuid = Game::intval($params, 'fuid');
        if (empty($fuid)) {
            Master::error(BOITE_ATTEND_NO_FIND_OWNER);
        }
        //是否合服范围内
        Game::isHeServerUid($fuid);
        //UID合法
        Master::click_uid($fuid);

        $Act170Model = Master::getAct170($fuid);

        if (empty($Act170Model->info['type'])) {
            Master::error(BOITE_NO_FEAST);
        }
        //获取配置
        $outf = $Act170Model->get_outf();
        if (Game::is_over($outf['ltime']['next'])) {
            Master::error(BOITE_FEAST_END);
        }

        //已占席位个数
        $xiwei = 0;
        foreach ($Act170Model->info['list'] as $k => $v) {
            if (empty($v['uid'])) {
                continue;
            }
            $xiwei++;
        }
        $yanhui_cfg = Game::getcfg_info('boite_yanhui',$Act170Model->info['type']);
        //获取配置
        $data = array(
            'yhname' => $yanhui_cfg['name'],
            'fname' => $outf['name'],
            'xiwei' => $xiwei,
            'maxXiWei' => count($Act170Model->info['list']),
            'addPer' => $outf['addPer'],
            'ltime' => array(
                'next' => $outf['ltime']['next'],//下次绝对时间
                'label' => 'jlyhltime',
            ),
        );
        //输出基础信息
        Master::back_data($this->uid, 'boite', 'yhBaseInfo', $data);
    }

    /**
     * 编号赴会-前往
     * @param $params
     * $params['fuid'] : 玩家id
     */
    public function yhGo($params)
    {
        $fuid = Game::intval($params, 'fuid');
        if (empty($fuid)) {
            Master::error(PARAMS_ERROR);
        }

        //是否合服范围内
        Game::isHeServerUid($fuid);

        $Act170Model = Master::getAct170($fuid);

        if ($fuid == $this->uid) {
            $outDate = $Act170Model->clear_show();
            //弹窗
            if (!empty($outDate['list']) || $outDate['isover'] == 1) {
                Master::$bak_data['a']['boite']['win']['yhnew'] = $outDate;
            }
            //(宴会结束)
            if ($outDate['isover'] == 1) {
                //我的历史宴会
                $Act52Model = Master::getAct52($this->uid);
                $type = $Act170Model->info['type'];
                $allScore = $outDate['allscore'];
                $ctime = $Act170Model->info['ctime'];
                $num = $outDate['maxnum'];

                $Act52Model->add_yanhui($type, $allScore, 0, $ctime, $num, $outDate['allep']);
                //我的积分计算
                if ($allScore > 0) {
                    Master::add_item($this->uid, KIND_OTHER, 50, $allScore);
                }
                //个人宴会清空
                $Act170Model = Master::getAct170($this->uid);
                $Act170Model->close_yh();

                $data = array();
                $data['type'] = $Act170Model->info['type'];
                $data['count'] = $Act170Model->info['count'];
                Master::back_data($this->uid, 'boite', 'yhType', $data);

                //全服排行
                $Redis20Model = Master::getRedis20();
                $Redis20Model->zIncrBy($this->uid, $allScore);  //来宾统计

                $HuodongModel = Master::getHuodong($this->uid);
                $HuodongModel->chongbang_huodong('huodong256', $this->uid, $allScore);

                //活动消耗 - 限时酒楼积分涨幅
                $HuodongModel = Master::getHuodong($this->uid);
                $HuodongModel->xianshi_huodong('huodong225', $allScore);
            }
            else {
                $Act170Model->save();
            }

        } else {
            //获取配置
            $data = $Act170Model->get_outf();
            if (empty($data['ltime']['next'])) {
                Master::error(BOITE_FEAST_END);
            }
            if (Game::is_over($data['ltime']['next'])) {
                Master::error(BOITE_FEAST_END);
            }
            if (empty($data['id'])) {
                Master::error(BOITE_FEAST_END);
            }
            Master::back_data($this->uid, 'boite', 'yhInfo', $data);
        }

    }

    /**
     * 吃宴会
     * @param mixed $params
     * $params['fuid'] :  谁开的宴会
     * $params['xwid'] :  席位id
     * $params['hid'] :  参加的门客id
     */
    public function yhChi($params)
    {

        $fuid = Game::intval($params, 'fuid');
        $xwid = Game::intval($params, 'xwid');
        $hid = Game::intval($params, 'hid');

        //是否合服范围内
        Game::isHeServerUid($fuid);

        //是否放逐
        $HeroModel = Master::getHero($this->uid);
        $hero_info = $HeroModel->check_info($hid);

        //获取势力
        $TeamModel = Master::getTeam($this->uid);
        $shili = empty($TeamModel->info['hero_allep'][$hid])?0:$TeamModel->info['hero_allep'][$hid];
        if ($shili == 0){
            Master::error(COMMON_DATA_ERROR);
        }

        $Act172Model = Master::getAct172($this->uid);
        $Act172Model->add($fuid, $hid);

        $Act170Model = Master::getAct170($fuid);
        //是否已过期/结束
        $Act170Model->check_yh();
        //占席位
        $Act170Model->add_xiwei($xwid, $this->uid, $hid, $shili);

        //输出
        $outf = $Act170Model->get_outf();
        Master::back_data($this->uid,'boite','yhInfo',$outf);

        //积分处理 写死50分
        $score = 50;
        //处理折损和加成
        $lost = 0;
        if ($fuid == $this->uid){
            $cfg_yanhui = Game::getcfg_info('boite_yanhui',$outf['id']);
            $lost = $cfg_yanhui['reduce'];
        }
        $score = ceil($score * (1 - $lost / 10000) * (1 + $outf['addPer'] / 10000));
        //给自己加积分
        Master::add_item($this->uid, KIND_OTHER, 50, $score);

        //活动消耗 - 限时赴宴次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong222', 1);

        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong256', $this->uid, $score);

        //活动消耗 - 限时酒楼积分涨幅
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong225', $score);

        //双旦活动道具产出
        $Act292Model = Master::getAct292($this->uid);
        $Act292Model->chanChu(3);

        //活动293 - 获得骰子-处理政务
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(6, 1);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(6, 1);

        //主线任务 - 刷新
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(54, 1);
        $Act39Model->task_refresh(54);
        $Act39Model->task_refresh(46);

        //舞狮大会 - 参加宴会次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(14,1);

        
        // 好友亲密度-每日赴宴
        $Act8023Model = Master::getAct8023($this->uid);
        $Act8023Model->fuyan($fuid);

    }


    /**
     * 商店积分兑换-兑换
     * @param mixed $params
     * $params['id'] :  物品标识id
     */
    public function shopChange($params)
    {
        $id = Game::intval($params, 'id');
        $Act171Model = Master::getAct171($this->uid);
        $Act171Model->init_data(false);
        $Act171Model->shop_buy($id);
    }

    /**
     * 消息信息-我的宴会
     */
    public function xxInfo()
    {
        $Act170Model = Master::getAct170($this->uid);
        $Act170Model->back_data();
    }

    /**
     * 酒楼-排行榜
     */
    public function jlRanking()
    {
        $Redis20Model = Master::getRedis20();
        //全服排行
        $Redis20Model->back_data();
        //我的排行
        $Redis20Model->back_data_my($this->uid);
    }
}