<?php
require_once "ActBaseModel.php";
/*
 * 活动300 跨服衙门
 */
class Act300Model extends ActBaseModel
{
	public $atype = 300;//活动编号
	public $comment = "跨服衙门-个人数据";
	public $b_mol = "kuayamen";
	public $b_ctrl = "info";//子类配置
	public $hd_id = 'huodong_300';//活动配置文件关键字
    protected $_rank_id = 300;
    public $hd_state;
    public $hd_cfg;

    public function __construct($uid){
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg)){
            parent::__construct($uid,$this->hd_cfg['info']['id'].Game::get_today_id());
        }
    }

    /*
     * 初始化结构体
     */
    public $_init =  array(
        //衙门出战次数 每日重置
        'ftime' => 0,//下次出战时间
        'fitnum' => 0,//正常出战次数
        'chunum' => 0,//出使令出战次数
        'funum' => 0,//复仇次数
        'lkill' => 0,//连续击杀

        'qhid' => 0,//申请出战英雄
        'fuid' => 0,//申请对战玩家
    );
    /*
     * 进入活动
     * */
    public function comehd(){
        $state = self::get_state();
        $outf = array();
        switch ($state){
            case 0://活动结束
                Master::error(KUAYAMEN_HD_END);
                break;
            case 1://预选赛
            case 2://预选赛结算
                //服务器分组
                $outf = array('hd_status' => $state);//指定状态
                break;
            case 3://正常赛
                //是否拥有参赛资格
                $Act306Model = Master::getAct306($this->uid);
                $Act306Model->back_data();
                //返回当前积分排行 各个服务器总积分
                $redis305Model = Master::getRedis305($this->hd_cfg['info']['id']);
                $redis305Model->back_data();
                $outf['hd_status'] = 3;
                break;
            case 4://展示阶段
                //返回当前积分排行 各个服务器总积分
                $redis305Model = Master::getRedis305($this->hd_cfg['info']['id']);
                $redis305Model->back_data();
                
                $outf = array('hd_status' => 4);//指定状态
                break;
            default:
                Master::error(KUAYAMEN_HD_END);
                break;
        }
        $outf['num'] = empty($this->hd_cfg['num']) ? 100 : $this->hd_cfg['num'];
        $outf['rwd'] = $this->hd_cfg['rwd'];
        $outf['yueTime'] = array(
            'cd' => array('next'=>$this->hd_cfg['info']['yueTime'],'label' => 'kuaymYuend')
        );
        $outf['yushowTime'] = array(
            'cd' => array('next'=>$this->hd_cfg['info']['yushowTime'],'label' => 'kuaymYushow')
        );
        $outf['eTime'] = array(
            'cd' => array('next'=>$this->hd_cfg['info']['eTime'],'label' =>'kuaymeTime')
        );
        Master::back_data($this->uid,$this->b_mol,'hdinfo',$outf);
    }
    
    /*
     * 构造输出结构体
     * 修改保存结构体
     */
    public function make_out()
    {
        $state = self::get_state();
        $this->hd_state = $state;
        switch ($state){
            case 0://活动结束
            case 1://预选赛
            case 2://预选赛结算
            case 4://展示阶段
                break;
            case 3://正常赛
                //是否拥有参赛资格
                $Act306Model = Master::getAct306($this->uid);
                if($Act306Model->info['state'] == 1){//有参赛资格
                    self::make_out_data();   
                }
                break;
            default:
                Master::error(KUAYAMEN_HD_END);
                break;
        }
    }
    
    /*
     * 返回活动信息
     */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }
    
    /*
     * 正常赛时返回的数据
     * */
    public function make_out_data(){
        /**
         * 非战斗中
         * 0 冷却完毕 无人能出战(或者无敌人能打) cd = 0
         * 1 自动出战冷却中
         * 2 门客出战申请中 cd > 0
         * 3 自动出战次数用尽 fitnum >= 4
         * 4 出师令出战次数用尽  chunum >= chumax
         *
         * 战斗中
         * 11 自动出战战斗中
         * 12 出征令战斗中
         * 13 挑战战斗中
         * 14 复仇战斗中
         * 15 追杀战斗中
         */
        
        //出战类
        $Act301Model = Master::getAct301($this->uid);
        
        //阵法
        $Team = Master::get_team($this->uid);
        //出师令 使用次数
        $chumax = floor(count($Team['pkhero'])/4);
        
        //衙门状态
        $state = 0;
        $ftime = 0;
        $fuser = array();//对战玩家信息
        
        //获取战斗状态 0: 非战斗中  1: 自动出战 2: 出师令 3: 复仇 4: 追杀
        $fight_state = $Act301Model->get_state();
        if ($fight_state > 0){
            switch($fight_state){
                case 1://自动出战
                    $state = 11;
                    break;
                case 2://出师令
                    $state = 12;
                    break;
                case 3://挑战
                    $state = 13;
                    break;
                case 4://复仇
                    $state = 14;
                    break;
                case 5://追杀
                    $state = 15;
                    break;
                default://出错 异常
                    Master::error("fight_state_err_".$fight_state);
                    break;
            }
        }else{
            //非战斗中
            if($this->info['ftime'] > 0 && !Game::is_over($this->info['ftime'])){
                $ftime = $this->info['ftime'];
            }
             
            if ($chumax > 0 && $this->info['chunum'] >= $chumax){
                //出师令次数用尽
                $state = 4;
            }elseif($this->info['qhid'] > 0){
                //门客出战申请中
                $state = 2;
                //获取申请中 对战玩家信息
                $fuser = Master::fuidInfo($this->info['fuid']);
            }elseif($this->info['fitnum'] >= 4){
                //自动次数用尽 / 显示出师令可使用次数
                $state = 3;
            }elseif($ftime > 0){//自动出战冷却中
                //自动出战冷却中
                $state = 1;
            }else{
                //0 冷却完毕 无人能出战
                $state = 0;
            }
        }

        $this->outf = array(
            'state' => $state,
            'cd' => array(
                'next' => $ftime,//冷却时间
                'label' => "kuayamen",
            ),
            'fitnum' => 4 - $this->info['fitnum'],//剩余 自动出战次数
            'chunum' => $chumax < $this->info['chunum']? 0:$chumax - $this->info['chunum'],//出师令可使用次数
            'chumax' => $chumax,//出师令使用次数上限
            'qhid' => $this->info['qhid'],////申请出战英雄
            'fuser' => $fuser,//申请对战玩家
        );
    }
    
    
    /*
     * CD到了  正常出战
     * 随机一个申请英雄开战
     */
    public function rand_qhid(){
        if ($this->outf['state'] != 0){
            return 0;
        }
        //随机一个敌人
        $fuid = $this->rand_f_uid();
        if ($fuid == 0){
            //没有能打的人
            return 0;
        }

        //随机一个英雄
        $heroid = $this->rand_hero();
        //如果无人能出战
        if ($heroid == 0){
            //无人能出战
            return 0;
        }

        //设置申请出战信息
        $this->info['fitnum'] += 1;//正常出战次数+1
        $this->info['qhid'] += $heroid;//申请出战英雄
        $this->info['fuid'] += $fuid;//申请对战玩家
        $this->save();
    }
    
    /*
     * 随机选择一个门客进行出战
     */
    private function rand_hero(){
        $team = Master::get_team($this->uid);
    
        //衙门正常出战列表
        $Act302Model = Master::getAct302($this->uid);
        //已出战列表
        $dead = array_keys($Act302Model->info);
        if(empty($dead)) $dead = array();
        //剩余可出战列表
        $f_heros = array_diff($team['pkhero'],$dead);

        //扣除流放的人
        $Act129Model = Master::getAct129($this->uid);
        if(!empty($Act129Model->info['list'])){
            $f_heros = array_diff($f_heros,array_keys($Act129Model->info['list']));
        }

        if (empty($f_heros)){
            return 0;//没有可以出战的英雄了
        }
    
        //随机一个英雄
        $heroid = $f_heros[array_rand($f_heros,1)];
    
        $Act302Model->go_fight($heroid);
    
        return $heroid;
    }
    
    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        return $news;
    }
    /*
     * 判断活动状态
     * 0: 未开启
     * 1: 预选赛
     * 2: 预选赛展示
     * 3: 正式赛
     * 4: 正式赛展示
     * */
    public function get_state(){
        $state = 0;  //活动未开启
        if(!empty($this->hd_cfg) ){
            if(Game::is_over($this->hd_cfg['info']['showTime'])){
                $state = 0;  //活动关闭
            }else if(Game::is_over($this->hd_cfg['info']['eTime'])){//正式赛展示阶段
                $state = 4;
            }else if(Game::is_over($this->hd_cfg['info']['yushowTime'])){//正式赛阶段
                $state = 3;
            }else if(Game::is_over($this->hd_cfg['info']['yueTime'])){//预选赛展示阶段
                $state = 2;
            }else{//预选赛阶段
                $state = 1;
            }
        }
        return $state;
    }
    
    /*
     * 判断当前状态 是不是 需要的状态
     */
    public function click_state($state){
        if ($this->outf['state'] == $state){
            return true;
        }
        switch ($this->outf['state']){
            case 0: $msg = KUAYAMEN_NO_ONE_TO_FIGNt; break;
            case 1: $msg = KUAYAMEN_AUTOMATIC_COMBAT_COOLING; break;
            case 2: $msg = KUAYAMEN_IN_HIS_QINGZHAN; break;
            case 3: $msg = KUAYAMEN_AUTOMATIC_WAR_NOTIME; break;
            case 4: $msg = KUAYAMEN_ENVOY_ORDER_NOTIME; break;
            	
            case 11: $msg = KUAYAMEN_HIS_BATTLE; break;
            case 12: $msg = KUAYAMEN_IN_HIS_BATTLE; break;
            case 13: $msg = KUAYAMEN_HIS_CHALLENGE; break;
            case 14: $msg = KUAYAMEN_HIS_REVENGE; break;
            case 15: $msg = KUAYAMEN_HIS_HUNTING; break;
            	
            default: $msg = 'state_error_'.$this->outf['state']; break;
        }
        Master::error($msg);
    }
    
    /*
     * 使用出征令
     */
    public function use_chuzheng(){
        //是否处于 等待使用出征令 状态
        $this->click_state(3);
        //阵法
        $Team = Master::get_team($this->uid);
        $chumax = floor(count($Team['pkhero'])/4);
        if($chumax <= $this->info['chunum']){
            Master::error(OPERATE_TODAY_NO_CASE);
        }
    
        //随机一个敌人
        $fuid = self::rand_f_uid();
        if ($fuid == 0){
            Master::error(YAMUN_UNFUND_ENEMY);
            return;
        }
    
        //随机一个英雄
        $heroid = $this->rand_hero();
        //如果无人能出战
        if ($heroid == 0){
            //无人能出战
            Master::error(YAMUN_NO_PLAY_HERO);
            return;
        }
    
        //扣除道具
        Master::sub_item($this->uid,KIND_ITEM,123,1);
    
        //出证次数+1
        $this->info['chunum'] += 1;
        $this->save();
    
        //设置英雄出征
        //衙门战斗类
        $Act301Model = Master::getAct301($this->uid);
        $Act301Model->start_fight($heroid,$fuid,2);
    }
    
    /*
     * 判断是否可以打
     * */
    public function is_play($uid,$is_my = 0){

        if(empty($is_my)){
            $sid = Game::get_sevid($uid);
            $sevobj = Common::getSevCfgObj($sid);
            $he_id = $sevobj->getHE();
            //不能打自己服的
            $SevCfg = Common::getSevidCfg();
            if($SevCfg['he'] == $he_id){
                Master::error(KUAYAMEN_CANT_ATTACK_THE_PLAYERS);
            }
            
            $hid = $this->hd_cfg['info']['id'].'_'.$he_id;
            $Redis307Model = Master::getRedis307($hid);
            $rank = $Redis307Model->get_rank_id($uid);
            if(empty($rank)){
                Master::error(KUAYAMEN_PLAYER_NOT_INVOLVED);
            }
        }else{
            $Act306Model = Master::getAct306($uid);
            if($Act306Model->info['state'] != 1){
                Master::error(KUAYAMEN_NOT_QUALIFYFOR_COMPETITION);
            }
        }
    }
    
    /*
     * 启动一场指定战斗
     * 挑战,复仇,追杀
     * 对方UID , 我使用的门客ID , 战斗类型
     */
    public function do_take_fight($fuid,$hid,$ftype,$node_id = -1){
        if ($fuid == $this->uid){
            Master::error(YAMUN_CHALLENGE_YOURSELF);
        }
        //UID合法
        Master::click_uid($fuid);

        $Act303Model = Master::getAct303($this->uid);
        $Act303Model->go_fight($hid);

        $this->check_banish($hid);

        //判断状态 是不是 战斗中
        self::isnot_fight();
    
        //进入指定战斗
        $Act301Model = Master::getAct301($this->uid);
        $Act301Model->start_fight($hid,$fuid,$ftype);
        //添加榜单挑战记录
        if($node_id >= 0){
            $Act308Model = Master::getAct308($this->uid);
            $Act308Model->add($node_id);
        }
    }
    
    /*
     * 判断当前状态 是不是非战斗中
     */
    public function isnot_fight(){
        if ($this->outf['state'] > 10){
            Master::error(YAMUN_HAVE_PLAYING_HERO);
        }
        return true;
    }
    /*
     * 获取随机敌人
     * */
    public function rand_f_uid(){
        $SevidCfg = Common::getSevidCfg();
        //随机服务器id
        $redis305Model = Master::getRedis305($this->hd_cfg['info']['id']);
        $fsid = $redis305Model->geRandtSevid($SevidCfg['he']);
        if(empty($fsid)){
            return 0;
        }
        //随机敌人
        $redis307Model = Master::getRedis307($this->hd_cfg['info']['id'].'_'.$fsid);
        $fuid = $redis307Model->rand_f_uid($this->uid);
        return $fuid;
    }

    
    /*
     * 批准一个申请英雄进入战斗
     */
    public function q2f(){
        //是否请战中
        $this->click_state(2);
    
        //衙门战斗类
        $Act301Model = Master::getAct301($this->uid);
        $Act301Model->start_fight($this->info['qhid'],$this->info['fuid'],1);

        //去掉申请信息
        $this->info['qhid'] = 0;
        $this->info['fuid'] = 0;
        $this->save();
    }
    /*
     * 添加积分
     * */
    public function add_score($score){
        $sid = Game::get_sevid($this->uid);
        $SevCfgObj = Common::getSevCfgObj($sid);

        $redis304Model = Master::getRedis304($this->hd_cfg['info']['id']);
        $redis304Model->zIncrBy($SevCfgObj->getHE(), $score);
        
        $redis305Model = Master::getRedis305($this->hd_cfg['info']['id']);
        $redis305Model->zIncrBy($SevCfgObj->getHE(), $score);
        
        $redis306Model = Master::getRedis306($this->hd_cfg['info']['id']);
        $redis306Model->zIncrBy($this->uid, $score);
        $redis306Model->back_data_my($this->uid);
        
        $redis307Model = Master::getRedis307($this->hd_cfg['info']['id'].'_'.$SevCfgObj->getHE());
        $redis307Model->zIncrBy($this->uid, $score);
    }
    
    /*
     * 完成一次战斗
     * 是否胜利0 失败 1 胜利(全歼)
     */
    public function battle_complete($is_win,$kill_num,$ftype){
        //如果还有自动次数 进入冷却
        if ($this->info['fitnum'] < 4 && $ftype == 1){
            $this->info['ftime'] = Game::get_over(3600);
        }
        //击杀 连杀次数
        if ($is_win){
            $this->info['lkill'] = $kill_num;
        }else{
            $this->info['lkill'] = 0;
        }
        $this->save();
    }

    /**
     * 检测门客是否被发配
     * @param $hid
     */
    public function check_banish($hid){
        $Act129Model = Master::getAct129($this->uid);
        $isbanish = $Act129Model->isBanish($hid);
        if($isbanish){
            Master::error(BANISH_011);
        }
    }
}
