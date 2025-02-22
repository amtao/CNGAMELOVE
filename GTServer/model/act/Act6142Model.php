<?php
require_once "ActHDBaseModel.php";
/*
 * 主角换装
 */
class Act6142Model extends ActHDBaseModel
{
    public $atype = 6142;//活动编号
    public $comment = "盛装出席pvp";
    public $b_mol = "clothepvp";//返回信息 所在模块
    public $b_ctrl = "info";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6142';//活动配置文件关键字
    public $label = "clothepvp";//倒计时标记

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'num' => 0,//	累计次数
        'time' => 0,//	上次时间

        'score' => 0, //分数
        'ping' => 0, //点赞次数

        'body' => 0,
        'head' => 0,
        'ear' => 0,
        'background' => 0,
        'effect' => 0,
        'animal' => 0,

        'uid2' => 0,
        'uid1' => 0,

        'lastChangeTime' => 0,
    );


    /**
     * @param unknown_type $uid   玩家id
     * @param unknown_type $id    活动id
     */
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
        }
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $state = self::get_state();
        if( $state  == 0 || $state == 2){
            return 0;
        }
        $news = 0; //不可领取
        $this->updateCount();
        if ($this->info['num'] > 0){
            $news = 1;
        }

        $rwd = $this->hd_cfg['rwd'][0];
        if (!empty($rwd) && $rwd['need'] <= $this->info['ping']){
            $news = 1;
        }

        return $news;
    }

    public function get_rwd()
    {
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $rwd = $this->hd_cfg['rwd'][0];
        if ($rwd['need'] > $this->info['ping']){
            Master::error();
        }

        $this->info['ping'] -= $rwd['need'];
        Master::add_rwd_singe($this->uid, $rwd['items'], 'prop', 1, 10000);

        $this -> save();
        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
    }

    public function addScore($add = 1){
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $this->info['score'] += $add;
        $this -> save();

        $Redis6142Model = Master::getRedis6142($this->hd_cfg['info']['id']);
        $Redis6142Model->zAdd($this->uid,$this->info['score']);

        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
    }

    private function isParts($id, $part){
        if ($id == 0)return true;
        $sys = Game::getcfg_info("use_clothe", $id);
        $count = empty($sys['money']['count'])?0:$sys['money']['count'];
        return $sys['part'] == $part?$count:-1;
    }

    public function saveClothe($head, $body, $ear, $bg, $eff, $ani){
        $info = $this->info;
        $Act6140Model = Master::getAct6140($this->uid);

        $score = 0;
        $s = $this->isParts($head, 1);
        $score += $s == -1?0:$s;
        if ($Act6140Model->isUnlock($head) && $s != -1){
            $info['head'] = $head;
        }

        $s = $this->isParts($body, 2);
        $score += $s == -1?0:$s;
        if ($Act6140Model->isUnlock($body) && $s != -1){
            $info['body'] = $body;
        }

        $s = $this->isParts($ear,3);
        $score += $s == -1?0:$s;
        if ($Act6140Model->isUnlock($ear) && $s != -1){
            $info['ear'] = $ear;
        }

        $s = $this->isParts($bg, 4);
        $score += $s == -1?0:$s;
        if ($Act6140Model->isUnlock($bg) && $s != -1){
            $info['background'] = $bg;
        }

        $s = $this->isParts($eff, 5);
        $score += $s == -1?0:$s;
        if ($Act6140Model->isUnlock($eff) && $s != -1){
            $info['effect'] = $eff;
        }

        $s = $this->isParts($ani, 6);
        $score += $s == -1?0:$s;
        if ($Act6140Model->isUnlock($ani) && $s != -1){
            $info['animal'] = $ani;
        }

        $info['lastChangeTime'] = Game::get_now();
        $info['score'] = $score / 10 < 4?0:ceil($score / 10);
        $this->info = $info;
        $this->save();

        $base = $this->getBase();
        $base['score'] = 0;
        Master::back_data($this->uid,$this->b_mol,'clothe', $this->getClothe());
        Master::back_data($this->uid,$this->b_mol,'base', $base);
        $Redis6142Model = Master::getRedis6142($this->hd_cfg['info']['id']);
        $Redis6142Model->zAdd($this->uid,$this->info['score']);
    }

    private function getBase(){
        $data = array();

        $max = Game::getcfg_param("clothepvp_count");
        $cd = Game::getcfg_param("clothepvp_time");
        $hf_num = Game::hf_num($this->info['time'],$cd,$this->info['num'],$max);

        $data['count'] = array(
            'next' => $hf_num['next'],//下次绝对时间
            'num' => $this->info['num'],//剩余次数
            'label' => $this->label,
        );
        $data['ping'] = $this->info['ping'];
        $data['score'] = $this->info['score'];
        return $data;
    }

    private function getClothe(){
        $data = array();
        $data['body'] = $this->info['body'];
        $data['head'] = $this->info['head'];
        $data['ear'] = $this->info['ear'];
        $data['background'] = $this->info['background'];
        $data['effect'] = $this->info['effect'];
        $data['animal'] = $this->info['animal'];
        return $data;
    }

    public function updateCount($issend = false){
        $max = Game::getcfg_param("clothepvp_count");
        $cd = Game::getcfg_param("clothepvp_time");

        $hf_num = Game::hf_num($this->info['time'],$cd,$this->info['num'],$max);
        $this->info['time'] = $hf_num['stime'];
        $this->info['num'] = $hf_num['num'];

        if ($issend){
            $this->save();
            Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
        }
    }

    public function getMath($count = 0){
        if ($this->hd_cfg['info']['sTime'] + $this->hd_cfg['start_time'] * 3600 > Game::get_now()){
            Master::error(CLOTHE_PVP_ZAN_TIME_LIMIT);
        }

        if ($this->info['uid1'] == 0 || $this->info['uid2'] == 0){
            $Redis6142Model = Master::getRedis6142($this->hd_cfg['info']['id']);
            $math = $Redis6142Model->rand_f_uid($this->uid);
            $this->info['uid1'] = $math['uid1'];
            $this->info['uid2'] = $math['uid2'];
            $this->save();
        }
        if ($this->info['uid1'] == 0 || $this->info['uid2'] == 0){
            if ($count > 5){
                Master::error(CLOTHE_PVP_MATH_ERROR);
            }
            $this->getMath($count + 1);
            return;
        }

        $data = array(
            "user"=>array(),
            "fuser"=>array(),
            "score1"=>0,
            "score2"=>0,
        );

        $uid1 = $this->info['uid1'];
        $Act6142Model = Master::getAct6142($uid1);
        $v_info = $Act6142Model->info;
        $userInfo = Master::fuidInfo($uid1);
        $userInfo['clothe']['head'] = $v_info['head'];
        $userInfo['clothe']['body'] = $v_info['body'];
        $userInfo['clothe']['ear'] = $v_info['ear'];
        $userInfo['clothe']['background'] = $v_info['background'];
        $userInfo['clothe']['effect'] = $v_info['effect'];
        $userInfo['clothe']['animal'] = $v_info['animal'];

        $data['user'] = $userInfo;
        $data['score1'] = $v_info['score'];

        $uid1 = $this->info['uid2'];
        $Act6142Model = Master::getAct6142($uid1);
        $v_info = $Act6142Model->info;
        $userInfo = Master::fuidInfo($uid1);
        $userInfo['clothe']['head'] = $v_info['head'];
        $userInfo['clothe']['body'] = $v_info['body'];
        $userInfo['clothe']['ear'] = $v_info['ear'];
        $userInfo['clothe']['background'] = $v_info['background'];
        $userInfo['clothe']['effect'] = $v_info['effect'];
        $userInfo['clothe']['animal'] = $v_info['animal'];

        $data['fuser'] = $userInfo;
        $data['score2'] = $v_info['score'];

        Master::back_data($this->uid,$this->b_mol,'math', $data);
    }

    public function zan($uid){
        $state = self::get_state();
        if( $state  == 0 || $state == 2){
            Master::error(ACTHD_OVERDUE);
        }
        if ($this->info['uid1'] != $uid && $this->info['uid2'] != $uid){
            return;
        }

        $this->updateCount();
        if ($this->info['num'] < 1){
            Master::error(CLOTHE_PVP_COUNT_LIMIT);
        }

        $this->info['uid1'] = 0;
        $this->info['uid2'] = 0;
        $this->info['ping'] +=1;
        $this->info['num'] -= 1;
        $this->getMath();

        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
        $Act6142Model = Master::getAct6142($uid);
        $Act6142Model->addScore();
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);

        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;
    }

    public function back_data_hd($isSendRank = false){
        if( empty($this->outf) ){
            $this->outf = array();
        }
        $out = $this->outf;
        if (!$isSendRank){
            unset($out['rank']);
            unset($out['info']);
        }
        $this->updateCount();
        Master::back_data($this->uid,$this->b_mol,'clothe', $this->getClothe());
        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$out);
    }
}














