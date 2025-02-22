<?php
require_once "ActHDBaseModel.php";

/*
 * 活动314
 */
class Act314Model extends ActHDBaseModel
{
	public $atype = 314;//活动编号
	public $comment = "跨服好感冲榜";
	public $b_mol = "kuacbhuodong";//返回信息 所在模块
	public $b_ctrl = "kualove";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_314';//活动配置文件关键字

    private $kua_zhufu ;//活动配置
    private $tip = 0;// 0:没有红点 1:触发红点
    private $chat = 0;// 0:不能聊天 1:可以聊天
    private $cdtype = array() ;//时间状态

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
        $this->kua_zhufu = Game::kua_lovezhufu($this->hd_cfg['need']['serv']);
        if( !empty($this->kua_zhufu) && !empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
        }
    }

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'comein' => 0,  //0:没有进入正式赛   1:进去正式赛
        'get' => 0, //全服奖励 0:不能领取,1:可领取,2:已领取
    );


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

        $this->outf['rnum'] = $hd_cfg['need']['rnum'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['need']);
        $this->outf['cfg'] = $hd_cfg;

        $this->outf['comein'] = $this->info['comein'];  //0:预选赛阶段, 1:活动进行中, 2:展示阶段

        $this->outf['type'] = empty($this->cdtype['type'])?0:$this->cdtype['type'];  //0:预选赛阶段, 1:活动进行中, 2:展示阶段
        $this->outf['cd'] = array(
            'next' => empty($this->cdtype['time'])?0:$this->cdtype['time'],
            'label' => 'huodong_314_time',
        );

        //展示阶段 + 还没领取
        if(self::get_state() == 2 && $this->info['get'] < 2 && $this->outf['type'] == 2){
            //发放奖励
            $servid = Game::get_sevid($this->uid);
            $Redis138Model = Master::getRedis138($this->hd_cfg['info']['id']);
            $serv_he = Common::getSevCfgObj($servid)->getHE();
            $rid = $Redis138Model->get_rank_id($serv_he);
            if(!empty($rid)){
                //获取最大区服
                $reList = array();
                foreach ($hd_cfg['qrwd'] as $qrwd) {
                    $reList[] = $qrwd['rand']['re'];
                }
                $maxRe = empty($reList) ? -1 : max($reList);
                if ($rid <= $maxRe) {
                    $this->info['get'] = 1;
                    $this->tip = 1;
                }
            }
        }
        $this->outf['get'] = $this->info['get'];  //全服奖励 0:不能领取,1:可领取,2:已领取

    }


    /**
     * 势力分数排行保存
     * @param $num  通过的势力数
     */
     public function do_save($num){

         if ($this->info['comein'] != 1){
            return false;
         }
        //在活动中
        if( self::get_state() == 1 && $num > 0){
            //区间 pk区服 单人排行榜 (单人为一个单位)  =>个人奖励
            $Redis137Model = Master::getRedis137($this->hd_cfg['info']['id']);
            $Redis137Model->zIncrBy($this->uid,$num);

            //区间 pk区服 排行榜  (区服为单位)   =>   整个区奖励
            $servid = Game::get_sevid($this->uid);
            $serv_he = Common::getSevCfgObj($servid)->getHE();
            $Redis138Model = Master::getRedis138($this->hd_cfg['info']['id']);
            $Redis138Model->zIncrBy($serv_he,$num);

            $this->info['kua_zhufu'] = $this->kua_zhufu;
            $this->save();
        }
         return false;
    }

    /**
     * 领取区服奖励
     */
    public function do_get(){

        //在活动中
        if( self::get_state() != 2){
            Master::error(ACTHD_NO_OVERDUE);
        }
        //不能领取
        if( $this->outf['get'] == 0){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //已经领取
        if( $this->info['get'] == 2){
            Master::error(DAILY_IS_RECEIVE);
        }

        //发放奖励
        $servid = Game::get_sevid($this->uid);
        $serv_he = Common::getSevCfgObj($servid)->getHE();
        $Redis138Model = Master::getRedis138($this->hd_cfg['info']['id']);
        $rid = $Redis138Model->get_rank_id($serv_he);
        if(empty($rid)){
            Master::error(ACTHD_NO_REWARD);
        }

        $this->info['get'] = 2;
        $this->save();

        //有配置区服奖励
        if(!empty($this->hd_cfg['qrwd'])){
            foreach ($this->hd_cfg['qrwd'] as $v){
                if($rid >= $v['rand']['rs'] && $rid <= $v['rand']['re']){
                    Master::add_item3($v['member']);
                    return true;
                }
            }
            Master::error(ACTHD_NO_REWARD);
        }
        return false;
    }


    /**
     * 聊天限制
     */
    public function chat_limit(){
        $userModel = Master::getUser($this->uid);
        if($userModel->info['level'] < $this->hd_cfg['need']['limit'] ){
            $level_cfg = Game::getcfg_info('guan',$this->hd_cfg['need']['limit'],CHAT_SPACE_CFG_ERROR);
            Master::error(CHAT_OPEN_LIMIT.$level_cfg['name']);
        }
        return true;
    }

    /**
     * 活动活动状态
     * 返回:
     * 0: 活动未开启
     * 1: 活动中
     * 2: 活动结束,展示中
     */
    public function get_state(){
        $state = 0;  //活动未开启
        $this->chat = 0;
        if(!empty($this->hd_cfg) ){
            if(Game::dis_over($this->hd_cfg['info']['showTime'])){
                $this->cdtype = array(
                    'type' => 2,
                    'time' => $this->hd_cfg['info']['showTime'],
                );
                $state = 2;  //活动结束,展示中
                $this->chat = 1;
            }
            if(Game::dis_over($this->hd_cfg['info']['eTime'])){
                $this->cdtype = array(
                    'type' => 1,
                    'time' => $this->hd_cfg['info']['eTime'],
                );
                $state = 1;  //活动中
                $this->chat = 1;
            }
            if(Game::dis_over($this->hd_cfg['info']['sTime'] + $this->hd_cfg['need']['openday']*86400)){
                $this->cdtype = array(
                    'type' => 0,
                    'time' => $this->hd_cfg['info']['sTime'] + $this->hd_cfg['need']['openday']*86400,
                );
                $state = 2;  //开始第几天进入正式赛 -- 活动展示中
                $this->chat = 0;
            }
        }
        if(!empty($state) && empty($this->kua_zhufu)){//不在指定期间内
            $state = 0;
        }
        return $state;
    }


    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取

        //判断活动是否还持续中
        if( self::get_state() != 2 ){
            return $news;
        }
        //已领取，去掉红点
        if ($this->info['get'] == 2) {
            return $news;
        }

        if($this->tip == 1){
            $news = 1;
        }
        return $news;
    }

    //----------------------------u 下发----------------------
    /*
    * 下发每日任务信息前端
    */
    public function back_data_get_u(){
        $data = array();
        $data['get'] = $this->info['get']; //收花的数量
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }


    /*
     * 返回活动信息
     */
    public function back_data_UserRank(){

        if( self::get_state() == 0){
            Master::error(KUAYAMEN_HD_YX_ERROE);
        }
        //排行信息
        $Redis137Model = Master::getRedis137($this->hd_cfg['info']['id']);
        $Redis137Model->back_data();
        $Redis137Model->back_data_my($this->uid);

    }

    /*
     * 返回活动信息
     */
    public function back_data_QuRank(){

        if( self::get_state() == 0){
            Master::error(KUAYAMEN_HD_YX_ERROE);
        }
        //排行信息
        $Redis138Model = Master::getRedis138($this->hd_cfg['info']['id']);
        $Redis138Model->back_data();
        $Redis138Model->back_data_my($this->uid);

    }


    /**
     * 跨服势力冲榜正式赛资格
     * $rid 排名id
     * $score 分数
     */
    public function add_quan($rid,$score){
        //不在活动中
        if( self::get_state() == 0 ){
            return false;
        }

        //没进入排名
        if($rid > $this->hd_cfg['need']['rnum']){
            return false;
        }

        //获取入场资格
        $this->info['comein'] = 1;
        $this->save();

        return true;
    }

    /**
     *  跨服势力冲榜聊天 - 添加聊天
     * $msg : 语句
     */
    public function add_msg($msg){
        //判断活动是否还持续中
//        if( self::get_state() == 2 ){
//            Master::error(KUAYAMEN_HD_END);  //活动结束
//        }
//        if( self::get_state() != 1 ){
//            Master::error(KUAYAMEN_HD_ZS_NOOPEN);  //正式赛未开启
//        }

        if(!$this->chat){
            Master::error(KUAYAMEN_HD_YX_ERROE);
        }

        //等级限制
        self::chat_limit();
        //禁言
        $Sev39Model = Master::getSev39();
        $bool = $Sev39Model->isBanTalk($this->uid);
        if(empty($bool)){
            $Sev39Model->autoBanTalk($this->uid,$msg);//自动禁言
        }

        //广告判定
        $switch = Game::get_peizhi('gq_status');
        if(!empty($switch['advertise'])){
            //判断是否在白名单内
            $chat_white = Game::get_peizhi('chat_white');//聊天白名单
            if(empty($chat_white) || !in_array($this->uid,$chat_white)){
                Common::loadModel("AdCheckModel");
                $AdCheckModel = new AdCheckModel($this->uid);
                if (!$AdCheckModel->click('sev',$msg)){
                    $msg_arr = array(
                        CHAT_001,
                        CHAT_002,
                        CHAT_003,
                        CHAT_004,
                    );
                    Master::error($msg_arr[array_rand($msg_arr)]);
                }
            }
        }

        //敏感字符判定
        $msg = Game::str_feifa($msg,1);
        $msg = Game::str_mingan($msg,1);

        //敏感词汇
        $Sev28Model = Master::getSev28();
        if($Sev28Model->isSensitify($msg) === false){
            if(empty($Sev39Model->info[$this->uid])){//正常
                $Sev314Model = Master::getSev314($this->hd_cfg['info']['id'],$this->kua_zhufu['zhufu']);
                $Sev314Model->add_msg($this->uid,$msg);
            }else{
                Master::back_s(2);
            }
        }
    }

    /**
     * 跨服势力冲榜聊天 - 重置聊天
     */
    public function list_init(){
        $Sev314Model = Master::getSev314($this->hd_cfg['info']['id'],$this->kua_zhufu['zhufu']);
        $Sev314Model->list_init($this->uid);
    }
    /**
     * 跨服势力冲榜聊天 - 输出聊天
     */
    public function list_click(){
        $Sev314Model = Master::getSev314($this->hd_cfg['info']['id'],$this->kua_zhufu['zhufu']);
        $Sev314Model->list_click($this->uid);
    }

    /**
     * 跨服势力冲榜聊天 - 历史消息
     * $id : 位置
     */
    public function list_history($id){
        $Sev314Model = Master::getSev314($this->hd_cfg['info']['id'],$this->kua_zhufu['zhufu']);
        $Sev314Model->list_history($this->uid,$id);
    }


}
