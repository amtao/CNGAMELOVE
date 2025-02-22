<?php
//用户
require_once "AModel.php";
class UserModel extends AModel
{
    protected $_syn_w = true;
        //这个作为USER内部类 不独立?
        private $_team_key = "_team";//阵法缓存
        private $team = null;//阵法信息 内部变量 阵法类去掉

        public $_key = "_user";
        protected  $updateSetKey = array(
                'name','job','sex','level','vip','step',
                'bmap','smap','mkill',
                'baby_num',
                'cb_time',
                'clubid','mw_num','mw_day',
                'voice','music','loginday','lastlogin',
                'platform','channel_id','ip','xuanyan',

        );
        protected $updateAddKey =  array(
                'exp','coin','food','army',
                'cash_sys','cash_buy','cash_use',
        );
        public function __construct($uid)
        {
                parent::__construct($uid);
                $cache = $this->_getCache();
                $this->info = $cache->get($this->getKey());
                if($this->info == false){
                        $table = 'user_'.Common::computeTableId($this->uid);
                        $sql = "select * from `{$table}` where `uid`='{$this->uid}'";
                        $db = $this->_getDb();
                        if (empty($db))
                        {
                                Master::error(USER_ACCOUNT_NO_EXIT);
                                return false;
                        }
                        $this->info = $db->fetchRow($sql);
                        if($this->info == false) {
                                $this->info = array();
                                return;
                        }
                        $this->info['name'] = stripslashes($this->info['name']);
                        $this->_rfcash();
                        $cache->set($this->getKey(),$this->info);
                }
        }

        /*
         * 各个类 数据写入数据库
         */
        public function click_destroy(){
                foreach($this->models as $mol){
                        $Model = $mol.'Model';
                        if(isset($this->$Model)
                        && $this->$Model->_update == true){
                                $this->$Model->destroy();
                        }
                }

                if ($this->_update == true){
                        $this->destroy();
                }
                return;
        }

        /*
         * 刷新钻石数量
         */
        public function _rfcash(){
                $this->info['cash'] = $this->info['cash_sys'] + $this->info['cash_buy'] - $this->info['cash_use'];
        }


        /*
         * 登陆处理
         */
        public function good_morning(){
                //如果是新的一天登陆
                if (!Game::is_today($this->info['lastlogin'])){

            //活动88 -  用户回归奖励
            $Act88Model = Master::getAct88($this->uid);
            $Act88Model->do_login($this->info['lastlogin'],$this->info['regtime']);

                        $u_update = array(
                                'lastlogin' => $_SERVER['REQUEST_TIME'],
                        );
                        $this->update($u_update);

                        /*
                         * 每日重置的项目  该业务本身没有 时间戳 使用这个时间作为时间
                         * 然后在这里重置次数
                         * 声望+
                         * 朝拜时间
                         * 免费转运次数
                         *
                         * 其他?
                         */

                        //累计登陆天数增加
                        $this->info['loginday'] ++ ;

                        //活动消耗 - 累计登录天数
                        $HuodongModel = Master::getHuodong($this->uid);
                        $HuodongModel->xianshi_huodong('huodong208',1);

            //活动消耗 - 冬至累计登录天数
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->xianshi_huodong('huodong6186',1);

                        //成就 - 累计登录天数
                        $Act36Model = Master::getAct36($this->uid);
                        $Act36Model->add(1,1);

                        //活动293 - 获得骰子-每日登陆
            $Act293Model = Master::getAct293($this->uid);
            $Act293Model->get_touzi_task(1,1);

            //活动296 - 挖宝锄头-每日任务
            $Act296Model = Master::getAct296($this->uid);
            $Act296Model->get_chutou_task(1,1);

                }
                //更新最后一次登陆时间
                $this->info['lastlogin'] = $_SERVER['REQUEST_TIME'];
        }

        /*
         * 获取输出值
         */
        public function getBase()
        {
            $name = Game::filter_char($this->info['name']);
        $Act6185Model = Master::getAct6185($this->uid);
        $zhichong = empty($Act6185Model->info['total'])?0:$Act6185Model->info['total']*10;
                //计算次数信息
                $data = array(
                        'uid' => $this->info['uid'],    //UID
                        'name' => $name,        //名字
                        'job' => $this->info['job'],    //头像ID
                        'sex' => $this->info['sex'],    //性别
                        'level' => $this->info['level'],//官品级
                        'exp' => $this->info['exp'],    //政绩
                        'vip' => $this->info['vip'],    //VIP
                        'cashbuy' => $this->info['cash_buy'] + $zhichong,       //充值钻石
                        'step' => $this->info['step'],  //账号进度(1未取名)
                        'guide' => $this->info['guide'],//引导步骤

                        'cash' => $this->info['cash'],  //元宝数量
                        'coin' => $this->info['coin'],  //金币
                        'food' => $this->info['food'],  //粮草
                        'army' => $this->info['army'],  //军队

                        'bmap' => $this->info['bmap'],  //地图大关ID
                        'smap' => intval($this->info['smap']),  //地图小关ID
                        'mkill' => $this->info['mkill'],        //已经打掉的小兵数量/BOSS血量
                        'xuanyan' => isset($this->info['xuanyan'])?$this->info['xuanyan']:"",   //宣言

                        'voice' => 0,//声音开关
                        'music' => 0,//音乐开关
                        'regtime' =>  $this->info['regtime'],//创建角色时间
                );
                //返回PVB战斗列表
                //关卡副本出战信息
                $Act3Model = Master::getAct3($this->uid);
                $Act3Model->back_data();

                //中地图ID加入返回信息里面
                //$data['mmap'] = ceil(($data['smap']+1)/8);
        $smap_cfg = Game::getcfg_info('pve_smap',intval($data['smap']) + 1);
        $data['mmap'] = intval($smap_cfg['mmap']);


                //插入称号返回
                $Act25Model = Master::getAct25($this->uid);
                $data['chenghao'] = $Act25Model->outf['setid'];

                Master::back_data($this->uid,'user','user',$data);
                return true;
        }



        /*
         * 创建用户
         */
        public function newUser($profile)
        {
                $uid = $profile['uid'];

                //渠道信息
                $platform = $profile['platform'];
                $channel_id = $profile['channel_id'];
                $ip = Common::GetIP();
                if(empty($ip)) $ip = 0;

                $time = $_SERVER['REQUEST_TIME'];

                //获取初始化配置信息
                /*
                $vip_cfg = Game::getGameCfg('userbase');
                $vip_cfg = Game::getGameCfg('vip');
                */

                $db = $this->_getDb();
                //用户表数据
                $table = 'user_'.Common::computeTableId($uid);
                $sql = <<<SQL
INSERT INTO `{$table}` set
        `uid` = '{$uid}',
        `name` = '',
        `level` = '0',
        `coin` = '20000',
        `food` = '20000',
        `army` = '20000',
        `step` = '{$profile['step']}',
        `loginday` = 1,
        `lastlogin` = '0',
        `regtime` = '{$time}',
        `clothe` = '0',

        `platform` = '{$platform}',
        `channel_id` = '{$channel_id}',

        /* 初始化地图信息 */
        `bmap` = '1',
        `smap` = '0',
        `mkill` = '0',

        `ip` = '{$ip}'
SQL;

                if (!$db->query($sql)){
                        Master::error(NOTE_SYSTEM_ERROR.'USERMODEL');
                }
                //清缓存重新生成
                $this->clear_mem();
                return $uid;
        }

        /*
         * 删除缓存重新读取
         */
        public function clear_mem()
        {
                $cache = $this->_getCache();
                $this->info = $cache->delete($this->getKey());
                $this->__construct($this->uid);
        }

        /*
         * 扣除钻石 成功返回true ,不足返回false
         * $is_click 是否单纯检查 不扣除
         */
        public function sub_cash($num,$is_click = false,$is_dun = false,$is_off = 0){
                if ($this->info['cash'] < $num
                || $num < 0
                || empty($num)){
                        if ($is_click){
                                return false;
                        }
                        Master::error(RES_SHORT.'|'."1");
                }
                //如果只是检查的话
                if ($is_click){
                        return true;
                }
                $u_update = array(
                        'cash_use' => $num,
                );
                $this->update($u_update);

                //活动消耗钻石/元宝
                if(!$is_off){
                        $HuodongModel = Master::getHuodong($this->uid);
                        $HuodongModel->xianshi_huodong('cash',$num);

            //活动296 - 挖宝锄头-每日任务
            $Act296Model = Master::getAct296($this->uid);
            $Act296Model->get_chutou_task(9,$num);
            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::consume($this->info['platform'], $this->uid, $num, $this->info['cash'], 0, 0, '扣除元宝');
            XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);
                }

                return true;
        }

        /*
         * 添加钻石
         */
        public function add_cash($num){
                $u_update = array(
                        'cash_sys' => $num,
                );
                $this->update($u_update);
        }

        /*
         * 添加元宝  充值
         */
        public function add_cash_buy($num,$money){
        Common::loadModel('XianYuLogModel');
            $oldVip = $this->info['vip']; //以前的vip等级

                $num = intval($num);
                $money = intval($money);
                if ($num <= 0){
                        return 0;
                }
        //更新vip等级
        $vip = 0;
        //累计vip经验
        $Act6185Model = Master::getAct6185($this->uid);

        $cash_buy = $this->info['cash_buy'] + $money*10 + $Act6185Model->info['total']*10;
        Common::loadModel('OrderModel');
        $channel = $this->info['channel_id'];
        $platform = $this->info['platform'];
        $list = OrderModel::vipexp_list($platform,$channel);
        if(!empty($list)){
            foreach($list as $v){
                if($v['recharge'] <= $cash_buy){
                    $vip = $v['level'];
                }
            }
        }
                //购买年月卡
                $Act68Model = Master::getAct68($this->uid);
                $flag = $Act68Model->buy($money);
                //直充礼包处理
        if ($num > 6480){
            $temp= $num /10000;
            $hid = $temp % 100;
            $flag = 4;
        }
        //第一次充值 记录等级和任务id流水
                $isFirst = $this->isFirstConsume();
        $Act39Model = Master::getAct39($this->uid);
                if ($isFirst && empty($Act6185Model->info['total'])){
                    $flowData = json_encode(array('lv'=>$this->info['level'],'task'=>$Act39Model->info['id']));
            Game::cmd_consume_flow($this->uid,0, 'userflow', 1, $flowData);
        }

                 if($flag == 2 || $flag == 3) {
             $u_update = array(
                 'cash_sys' => $num,
             );
             $this->update($u_update);
         }elseif($flag == 4 ){
             //直充不送元宝 但要记录
             $Act6185Model->add($money);
         }
         else {
             $u_update = array(
                 'cash_buy' => $num,
             );
             $this->update($u_update);
         }

                $ctip = MAIL_RECHANGE_CONTENT_HEAD."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
                $title = MAIL_RECHANGE;
                if($flag == 2 ){
                        //月卡
                        $title = MAIL_RECHANGE_YUEKA;
                        $ctip = MAIL_RECHANGE_CONTENT_YUEKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,2,$isFirst);
                }elseif($flag == 3 ){
                        //年卡
                        $title = MAIL_RECHANGE_NIANKA;
                        $ctip = MAIL_RECHANGE_CONTENT_NIANKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
        }
        else {  // 年卡月卡不增加vip
            if ($flag == 4){
                //直充
                $Act6180Model = Master::getAct6180($this->uid);
                $Act6180Model->Buy($hid);
                $price = $Act6180Model->show($hid);
                $zc_item = $Act6180Model->resItem($hid);
                $title = LEVEL_GIFT_CHAO_ZHI_LI_BAO;
                $ctip = MAIL_RECHANGE_CONTENT_DIRECT."|".$price."|".MAIL_RECHANGE_CONTENT_DIRECT_FOOT;
                Master::sendMail($this->uid,$title,$ctip,1,$zc_item);
                XianYuLogModel::pay($this->info['platform'], $this->uid, $money,4,$isFirst);
            }else{
                Master::sendMail($this->uid,$title,$ctip,0,array());
                XianYuLogModel::pay($this->info['platform'], $this->uid, $money,1,$isFirst);
            }
            //如果vip升级-跑马灯
            if($vip > $oldVip){
                $Sev91Model = Master::getSev91();
                $Sev91Model->add_msg(array(101,Game::filter_char($this->info['name']),$vip));
            }

            $u_update = array(
                'vip'   => $vip,
            );
            $this->update($u_update);
            
            //更新vip成就任务
            if($vip >0){
                $Act36Model = Master::getAct36($this->uid);
                $Act36Model->set(10,$vip);
            }

        }


        if($flag != 2 && $flag != 3 && $flag != 4) {
            //额外翻倍奖励
            $Act72Model = Master::getAct72($this->uid);
            $beishu = $Act72Model->do_save($money,$num);
            if($beishu > 0 ){
                $items = array();
                $items[] = array(
                    'id' => 1,
                    'count' => $num * $beishu,
                );
                Master::sendMail($this->uid,MAIL_RECHANGE_EXTRA,MAIL_RECHANGE_EXTRA_CONTENT,1,$items);
            }

            //特殊翻倍奖励  (首充翻倍不参与加成)
            $sys_beishu = Game::pv_beishu('order');
            if($sys_beishu > 1){
                $sysCount = ceil($num * ($sys_beishu - 1));
                $sys_items = array();
                $sys_items[] = array(
                    'id' => 1,
                    'count' => $sysCount,
                );

                $sys_title = MAIL_RECHANGE_SYSTEM;
                $sys_content = MAIL_RECHANGE_SYSTEM_CONTENT_1."|".$num."|".MAIL_RECHANGE_SYSTEM_CONTENT_2;
                $s_guanKaRwd = Game::pv_beishu('guanKaRwd');
                if(!empty($s_guanKaRwd) ){
                    $sys_title = $s_guanKaRwd['title'];
                    $sys_content = sprintf($s_guanKaRwd['content'],$num);
                }
                Master::sendMail($this->uid,$sys_title,$sys_content,1,$sys_items);
            }

            //百服开服充值不断,福利礼包不停
            $Act152Model = Master::getAct152($this->uid);
            $Act152Model->add($num);

            //首充
            $Act66Model = Master::getAct66($this->uid);
            $Act66Model->do_save();
            //累计充值多少钻石
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->order_diamond($money * 10, $num);

            //日常任务
            $Act35Model = Master::getAct35($this->uid);
            $Act35Model->do_act(12,1);
            //主线任务
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(56,1);
            $Act39Model->task_refresh(56);
        }

                //充值流水
        Game::cmd_other_flow($this->uid, 'chongzhi', 'cash_buy', 'cash_buy', 1, 1, $flag!=4?$num:$num+100000, $this->info['cash']);

        //咸鱼日志
        XianYuLogModel::output($this->info['platform'], $this->uid, $flag!=4?$num:$num+100000, $this->info['cash'], 1, '充值基础获得');
        XianYuLogModel::vipgrade($this->info['platform'], $this->uid, $vip);
        XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);

                $cache = $this->_getCache();
        $cache->set('order_back_'.$this->uid,array('cs' => 2),120);
                return 1;
        }

        /*
         * 扣除 某种东西
         */
        public function sub_sth($type,$num,$is_click = false,$is_off = 0){
                if ($this->info[$type] < $num || $num < 0 || empty($num)){
                        if ($is_click){
                                return false;
                        }
                        $err_type = 0;
                        switch ($type){
                            case 'cash':
                    $err_type = 1;
                                break;
                            case 'coin':
                    $err_type = 2;
                                break;
                            case 'food':
                    $err_type = 3;
                                break;
                            case 'army':
                    $err_type = 4;
                                break;
                            case 'exp':
                    $err_type = 5;
                                break;
                        }
                        Master::error($err_type != 0?(RES_SHORT.'|'.$err_type):ITEMS_NUMBER_SHORT);
                }else{
                        if ($is_click){
                                return true;
                        }
                        //如果在增加序列里面
                        if ($type == "cash"){
                                return $this->sub_cash($num,$is_click = false,$is_dun = false,$is_off);
                        }elseif (in_array($type,$this->updateAddKey)){
                                //限时活动
                                if(in_array($type,array('coin','army'))){
                                        $HuodongModel = Master::getHuodong($this->uid);
                                        $HuodongModel->xianshi_huodong($type,$num);
                                }

                                //冲榜活动 - 银两冲榜
                                if($type == 'coin'){
                                        $HuodongModel = Master::getHuodong($this->uid);
                                        $HuodongModel->chongbang_huodong('huodong255',$this->uid,$num);
                                }
                                //冲榜活动 - 士兵冲榜
                                if($type == 'army'){
                                        $HuodongModel = Master::getHuodong($this->uid);
                                        $HuodongModel->chongbang_huodong('huodong257',$this->uid,$num);
                                }
                //冲榜活动 - 粮食冲榜
                if($type == 'food'){
                    $HuodongModel = Master::getHuodong($this->uid);
                    $HuodongModel->chongbang_huodong('huodong259',$this->uid,$num);

                    //限时活动  限时粮食消耗
                    $HuodongModel = Master::getHuodong($this->uid);
                    $HuodongModel->xianshi_huodong('huodong226',$num);
                }

                                $num *= -1;

                //咸鱼日志
                $user_item = array('coin', 'army', 'food');
                if(in_array($type, $user_item)){
                    Common::loadModel('XianYuLogModel');
                    XianYuLogModel::item($this->uid, $type, $this->info[$type], $num, '扣除用户类型道具');
                }
                        } else if (in_array($type,$this->updateSetKey)){
                                $num = $this->info[$type] - $num;
                        } else{
                                Master::error('sub_sth_err_'.$type);
                        }

                        $u_update = array(
                                $type => $num,
                        );
                        $this->update($u_update);
                }
                return true;
        }

        /*
         * 添加 银两 粮食 军队
         */
        public function add_sth($type,$num){
                if ($type == 'cash'){
                        return $this->add_cash($num);
                }
                //如果在增加序列里面
                if (in_array($type,$this->updateAddKey)){
                        //
                } else if (in_array($type,$this->updateSetKey)){
                        $num += $this->info[$type];
                } else{
                        Master::error('add_sth_err_'.$type);
                }

                $u_update = array(
                        $type => $num,
                );
                $this->update($u_update);
        }

        /*
         * 根据ID扣除/检查 项目
         * 成功返回true
         * 如果没有此ID 返回false
         */
        public function subitem($itemid,$num,$is_click = false){

                return false;
        }

        // 判断首冲状态
        // 已经冲过 返回 false
        //还没冲过  返回 true
        public function isFirstConsume() {
                //先用老方法判断从没冲过钱的人
                if ($this->info['cash_buy'] <= 0){
                        return true;
                }

                //首冲重置?

                return false;
        }

        /////---------------活动相关函数----------------

        /*
         * 升官
         */
        public function shengguan(){
                //判断满级
                $next_level = $this->info['level']+1;
                Game::getCfg_info('guan',$next_level, DASUANMOUFAN);

                //经验够不够 / 是不是已经满级
                $guan_cfg_info = Game::getCfg_info('guan',$this->info['level']);
                $exp = $guan_cfg_info['need_exp'];

                if (!$this->sub_sth('exp',$exp,true)){
                        Master::error(RES_SHORT.'|'."5");
                }
                $this->sub_sth('exp',$exp);
                //加上等级
                $this->add_sth('level',1);

                //成就更新
                $Act36Model = Master::getAct36($this->uid);
                $Act36Model->set(2,$this->info['level']);

        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        XianYuLogModel::rolelevel($this->info['platform'], $this->uid, $next_level);
        XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);

                //if($this->info['level'] == 2){
                //    Master::add_item($this->uid,KIND_WIFE,1);
                //}
        }

        /*
         * 更新
         */
        public function update($data)
        {
                foreach ($data as $k => $v){
                        if (in_array($k,$this->updateSetKey)){
                                //数值被改变
                                $this->info[$k] = $v;
                        } else if (in_array($k,$this->updateAddKey)){
                                //数值被增减
                                if (isset($this->info[$k])){
                                        $this->info[$k] += $v;
                                }else{
                                        $this->info[$k] = $v;
                                }
                        }
                        //设置返回信息
                        switch ($k){
                                //这些数值被更新的话 返回原字段更新信息
                                case 'name':    case 'job':     case 'sex':
                                case 'level':   case 'vip':     case 'exp':
                                case 'step':
                                case 'bmap':    case 'smap':    case 'mkill':
                                case 'coin':    case 'food':    case 'army':
                                case 'baby_num':
                                        Master::back_data($this->uid,'user','user',array($k => $this->info[$k]),true);
                                        if ($k == 'smap'){
                                                //$mmap = ceil(($this->info['smap']+1)/8);
                        $smap_cfg = Game::getcfg_info('pve_smap',intval($this->info['smap']) + 1);
                        $mmap = intval($smap_cfg['mmap']);

                                                Master::back_data($this->uid,'user','user',array('mmap' => $mmap),true);
                                        }
                                        break;
                                //更新元宝
                                case 'cash_sys':
                                case 'cash_buy':
                                case 'cash_use':
                                        $this->_rfcash();//刷新元宝
                                        Master::back_data($this->uid,'user','user',
                                                array('cash' => $this->info['cash'],'cashbuy' => $this->info['cash_buy']),true);
                                        break;
                        }

//流水
            switch ($k){
                case 'food':
                    Game::cmd_flow(3, 3, $v, $this->info[$k]);
                    break;
                case 'army':
                    Game::cmd_flow(4, 4, $v, $this->info[$k]);
                    break;
                case 'coin':
                    Game::cmd_flow(2, 2, $v, $this->info[$k]);
                    break;
                case 'cash_sys':
                    if($v > 15000000){
                        Master::error(ITEMS_ERROR);
                    }
                    Game::cmd_flow(1, 1, $v, $this->info['cash']);
                    break;
                case 'cash_use':
                    Game::cmd_flow(1, 2, -$v, $this->info['cash']);
                    break;
                case 'exp':
                    Game::cmd_flow(5, 5, $v, $this->info[$k]);
                    break;
            }

                        /*
                'coin_time','coin_num',
                'food_time','food_num',
                'army_time','army_num',
                'exp_time','exp_num',
                'love_time','love_num',
                'cb_time',
                'pk_time','pk_num','clubid','mw_num','mw_time',
                'fr_id','fr_da','fr_time','xf_num','xf_time','ys_num','ys_time',
                'voice','music','uptime','loginday','lastlogin','loginday',
                'platform','channel_id','ip',
        );
                         */
                }
                $this->_update = true;
        }

        /*
         */
        public function sync()
        {
                if(is_array($this->info) && $this->info){
                        $table = 'user_'.Common::computeTableId($this->uid);
                        $updateKeysToDb = array_merge($this->updateAddKey,$this->updateSetKey);
                        $updateKeysToDb = array_unique($updateKeysToDb);//去重复
                        $sql = "update `{$table}` set ";
                        foreach( $updateKeysToDb as  $perKey){
                                $perValue = $this->info[$perKey];
                                if(is_numeric($perValue)){
                                        $sql .= "`{$perKey}`={$perValue},";
                                }else{
                                        $perValue = addslashes($perValue);
                                        $sql .= "`{$perKey}`='{$perValue}',";
                                }
                        }
                        $sql = substr($sql,0,-1) ." where `uid`={$this->uid}";
                        $db = $this->_getDb();
                        $flag = $db->query($sql);
                        if(!$flag){
                                Master::error('db error UserModel_'.$sql);
                        }
                        return true;
                }
                return false;
        }
}

