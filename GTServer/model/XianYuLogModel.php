<?php
/**
 * 咸鱼日志模型
 * Class XianYuLogModel
 */
class XianYuLogModel
{
    const PATH = '/data/logs/xianyujingfen/log/';
    const KEY_DAMING = 'ny8p6wl0umhfncz';
    const KEY_H5 = '9gqp2o09r1';
    const EPZJFHOVER_KEY = '7265bdaf2a1a4412ba48ad4518ba5384';

    public static function isOpenLog()
    {
        $marks = array(
            'epzjfhover'=>self::EPZJFHOVER_KEY,
        );
        if(defined("GAME_MARK") && isset($marks[GAME_MARK]) && $marks[GAME_MARK]){
            return $marks[GAME_MARK];
        }

        return false;
    }

    public static function getXianyuPath()
    {
        return self::PATH;
    }

    public static function role($platform, $uid)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'role/' . date('Ymd') . '/role_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //创角时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function loginserver($platform, $uid)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'loginserver/' . date('Ymd') . '/loginserver_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //登录角色时间
            'aid' => $openid, //账号ID
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function loginsdk($platform, $open_id)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'loginsdk/' . date('Ymd') . '/loginsdk_' . date('Ymd_Hi') . '.log.temp';
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'ts' => $_SERVER['REQUEST_TIME'], //登录sdk时间
            'aid' => $open_id, //账号ID
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function charge($platform, $uid, $orderid, $money, $tradeno, $diamond, $gn)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'charge/' . date('Ymd') . '/charge_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //充值时间
            'oid' => $orderid, //订单ID
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'mny' => $money * 100, //充值金额（分）
            'gc' => $tradeno, //商品编码
            'gn' =>$gn, //商品名称
            'ga' => $diamond, //充值直接获取一级代币数量
            'did' => 'did', //设备id
            'ip' => Common::getIPSimple(), //客户端ip
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function online($platform, $svr, $cnt)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'online/' . date('Ymd') . '/online_' . date('Ymd_Hi') . '.log.temp';
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'svr' => "{$svr}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //统计时间
            'cnt' => $cnt, //在线数
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function shop($platform, $uid, $lv , $vip, $item, $num, $need)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'shop/' . date('Ymd') . '/shop_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'svr' => "{$serverID}", //区服标识
            'aid' => $openid, //账号ID
            'ts' => $_SERVER['REQUEST_TIME'], //统计时间
            'rid' => "{$uid}",  //角色ID
            'lv' => $lv,        //角色等级
            'grd' => $vip,      //vip等级
            'tc' => $item,      //道具名称
            'ta' => $num,       //购买数量
            'ne' => $need,      //物品价格
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function consume($platform, $uid, $num, $cash, $tc, $ta, $tn)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'consume/' . date('Ymd') . '/consume_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //消耗时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'cnt' => $num, //消耗数量
            'cnta' => $cash, //一级代币消耗后当前值，非负整数
            'tc' => "{$tc}", //道具编码(如不是道具，则填“0”)
            'tn' => $tn, //道具名称(如不是道具，则填消耗原因)
            'ta' => $ta, //道具数量，非负整数(如不是道具，则填0)
            'did' => 'did', //设备id
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function output($platform, $uid, $num, $cash, $owt, $src)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'output/' . date('Ymd') . '/output_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //产出时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'cnt' => $num, //产出一级代币数量，非负整数
            'cnta' => $cash, //一级代币产出后当前值，非负整数
            'owt' => $owt, //	产出途径类型，取值：0 - 其他（默认值）；1 - 充值基础获得； 2 - 充值赠送获得； 3 - 充值翻倍获得
            'src' => $src, //产出途径，对应不同产出途径类型
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function loginrole($platform, $uid)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'loginrole/' . date('Ymd') . '/loginrole_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //登录服务器时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function rolelevel($platform, $uid, $next_level)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'rolelevel/' . date('Ymd') . '/rolelevel_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //等级变化时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'lv' => $next_level, //角色等级
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function vipgrade($platform, $uid, $vip)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'vipgrade/' . date('Ymd') . '/vipgrade_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //VIP等级变化时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'grd' => $vip, //VIP等级
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function tutorial($uid, $step)
    {
        $key = self::isOpenLog();
        if(!$key){
            return;
        }
        $UserModel = Master::getUser($uid);
        $platform = $UserModel->info['platform'];
        if($platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'tutorial/' . date('Ymd') . '/tutorial_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //到达指引页时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'step' => $step, //新手指引步骤序号
            'stat' => $step > 0 ? 1 : 0 , //用户在当前步骤状态，0-进入页面 1-通过页面
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function roleinfo($platform, $uid, $regtime, $name, $lastlogin, $level, $cash_buy, $cash)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local' || empty($name)){
            return;
        }
        $logpath = self::PATH . 'roleinfo/' . date('Ymd') . '/roleinfo_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'svr' => "{$serverID}", //区服标识
            'tsr' => $regtime, //注册时间
            'rid' => "{$uid}", //角色ID
            'rn' => $name, //角色名
            'aid' => $openid, //账号ID
            'tsl' => $lastlogin, //最后登录时间
            'lv' => $level, //角色等级
            'ip' => Common::getIPSimple(), //ip
            'tpc' => $cash_buy, //累计购买一级代币数量
            'rc' => $cash, //剩余一级代币数量
            'ts' => $_SERVER['REQUEST_TIME'], //角色信息统计时间
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function item($uid, $itemid, $quab, $count, $op)
    {
        $key = self::isOpenLog();
        if(!$key){
            return;
        }
        $UserModel = Master::getUser($uid);
        $platform = $UserModel->info['platform'];
        if($platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'item/' . date('Ymd') . '/item_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $user_item = array('coin'=>'银两', 'army'=>'士兵', 'food'=>'粮草');
        if(array_key_exists($itemid, $user_item)){
            $qua = -1 * $count < 0 ? 0 : -1 * $count;
            $quaa = $quab + $count < 0 ? 0 : $quab + $count;
            $name = $user_item[$itemid];
        }else{
            $qua = $count < 0 ? 0 : $count;
            $quaa = $quab - $count < 0 ? 0 : $quab - $count;
            $itemcfg_info = Game::getcfg_info('item', $itemid);
            $name = $itemcfg_info['name'];
        }

        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'svr' => $serverID, //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //道具变化时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'op' => $op, //操作
            'nm' => $name, //物品
            'ipos' => 'ipos', //物品所属位置
            'quab' => $quab, //操作前数量，非负整数
            'qua' => $qua, //当次操作数量，非负整数
            'quaa' => $quaa, //操作后数量，非负整数

        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function money($platform, $uid)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'money/' . date('Ymd') . '/money_' . date('Ymd_Hi') . '.log.temp';
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识

        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    public static function copy($platform, $uid, $cid, $name)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }
        $logpath = self::PATH . 'copy/' . date('Ymd') . '/copy_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //到达或者通过副本时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'nm' => $name, //副本名称
            'cid' => $cid, //副本id
            'sta' => 1, //用户在当前副本状态
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }
    //充值类型
    public static function Pay($platform, $uid, $money,$type,$isFirst)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }

        $logpath = self::PATH . 'pay/' . date('Ymd') . '/pay_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $cx_zc_sql = "select `reg_time` from `register` where `uid`={$uid}";
        $db = Common::getDbBySevId($serverID);
        $reg_time = $db->fetchArray($cx_zc_sql);
        $regday = strtotime(date('Y-m-d 00:00:00',$reg_time[0]['reg_time']));
        $today = strtotime(date('Y-m-d 00:00:00',$_SERVER['REQUEST_TIME']));
        $day = intval(($today - $regday)/86400)+1;

        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform, //注册渠道标识
            'chl' => $platform, //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //记录时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid, //账号ID
            'mny' => $money, //充值金额(人民币)
            'typ' => $type, //充值类型  1:普通档次充值 2:月卡 3:年卡 4:直冲
            'day' => $day, //注册天数
            'fit' => $isFirst === true?1:0,
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    //祈福
    public static function qifu($platform, $uid,$type,$iscash)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }

        $logpath = self::PATH . 'qifu/' . date('Ymd') . '/qifu_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform,     //注册渠道标识
            'chl' => $platform,     //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //记录时间
            'rid' => "{$uid}",  //角色ID
            'aid' => $openid,   //账号ID
            'ism' => $iscash,   //是否使用元宝 0:否 1:是
            'typ' => $type,     //祈福类型  1:阅历 2:银两 3:名声
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    //每日任务
    public static function daily($platform, $uid,$dailyid,$type)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }

        $logpath = self::PATH . 'daily/' . date('Ymd') . '/daily_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform,     //注册渠道标识
            'chl' => $platform,     //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //记录时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid,  //账号ID
            'dai' => $dailyid, //每日任务id or 每日任务活跃档次id
            'typ' => $type,    //类型  1:每日任务 2:活跃档次
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }

    //珍宝阁整理
    public static function treasure($platform, $uid,$guanka,$iscash,$type)
    {
        $key = self::isOpenLog();
        if(!$key || $platform == 'local'){
            return;
        }

        $logpath = self::PATH . 'treasure/' . date('Ymd') . '/treasure_' . date('Ymd_Hi') . '.log.temp';
        $serverID = Game::get_sevid($uid);
        $openid = Common::getOpenid($uid);
        $xianyu_data = array(
            'key' => $key,
            'chr' => $platform,     //注册渠道标识
            'chl' => $platform,     //登录渠道标识
            'svr' => "{$serverID}", //区服标识
            'ts' => $_SERVER['REQUEST_TIME'], //记录时间
            'rid' => "{$uid}", //角色ID
            'aid' => $openid,  //账号ID
            'gak' => $guanka,  //珍宝阁关卡
            'isc' => $iscash,  //是否使用元宝 0:否 1:是
            'typ' => $type,    //类型  1:正常通关 2:元宝通关 3:重置关卡
        );
        Common::logXianYuMsg($logpath, json_encode($xianyu_data));
    }





}