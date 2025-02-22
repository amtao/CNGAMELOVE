<?php
// 服务器
class ServerModel {
    public $_statusCfg = array (
        5 => '新服',
        1 => '正常',
        2 => '拥挤',
        3 => '爆满',
        4 => '排队',
        6 => '维护',// 不是以上五个值的默认为维护
    );
    protected static $_save_key = 'server_list';
    //拇指游玩暂时读取文件
    protected static $_read_write_file_game = array();

    public function __construct()
    {

    }

    /**
     * 获取服务器列表
     */
    static public function getServList(){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $sList = $ComVoComModel->getValue();

        if (empty($sList)) {
            //数据读取失败，记录行为
            $fileName = 'server_list_empty_' . date("Ymd") . '.log';
            $content = (PHP_EOL.date("Ymd H:i:s") . $_SERVER['REQUEST_URI'] . PHP_EOL . var_export(debug_backtrace(), true) . PHP_EOL);
            Common::log($fileName, $content);
        }
        return $sList;
    }

    /**
     * 添加服务器列表
     */
    static public function addServList($data){
        $sList = self::getServList();
        $sList[$data['id']] = $data;
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $ComVoComModel->updateValue($sList);
        return $sList;
    }

    /**
     * 删除服务器列表
     */
    static public function delServList($key){
        $sList = self::getServList();
        unset($sList[$key]);
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $ComVoComModel->updateValue($sList);
        return true;
    }

    /**
     * 获取开服第几天
     * @param unknown_type $servid   服务器id
     */
    static public function getOpenDays($servid){
        static $slist;
        if(empty($slist[$servid])){
            $servList = self::getServList();
            if(empty($servList[$servid])){
                $slist[$servid] =  0;
            }else{
                if($servList[$servid]['showtime'] > $_SERVER['REQUEST_TIME']){
                    $slist[$servid] =  0;
                }else{
                    //跨过几个0点
                    $days =   Game::day_count($servList[$servid]['showtime']);
                    $slist[$servid] = $days +1;
                }
            }
        }
        return $slist[$servid];
    }

    /**
     * 获取开服时间
     * @param int $servid   服务器id
     */
    static public function getShowTime($servid){
        static $showTime;
        if(empty($showTime[$servid])){
            $servList = self::getServList();
            if(empty($servList[$servid])){
                $showTime[$servid] =  0;
            }else{
                $showTime[$servid] = $servList[$servid]['showtime'];
            }
        }
        return $showTime[$servid];
    }

    /**
     * 服务器开启天数  当天算一天
     * @param unknown_type $servid   服务器id
     */
    static public function isOpen($servid){
        $hd_slist = self::getServList();
        if(empty($hd_slist[$servid])){
            $serv_day = 0;
        }else{
            $days =   Game::day_count($hd_slist[$servid]['showtime']);
            $serv_day = $days +1;
        }
        return $serv_day;
    }

    // 获取默认服务器ID
    static public function getDefaultServerId() {
        return ( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) ? 999 : 1;
    }

    public static function getSkin()
    {
        $SevidCfg = Common::getSevidCfg();
        static $skinStatic = array();
        if (!isset($skinStatic[$SevidCfg['sevid']])) {
            $skinKey = 'dft_server_skin_arr';
            $cache = Common::getDftMem();
            $skinArr = $cache->get($skinKey);
            if ($skinArr === false || Game::is_over($skinArr['time'])) {
                $serverList = self::getServList();
                $skin = isset($serverList[$SevidCfg['sevid']]['skin']) ?
                    intval($serverList[$SevidCfg['sevid']]['skin']) : 1;
                $skinArr = array(
                    'time'=>(Game::get_over(60)),
                    'skin'=>$skin,
                );
                $cache->set($skinKey, $skinArr);
            }
            $skinStatic[$SevidCfg['sevid']] = $skinArr['skin'];
        }
        return $skinStatic[$SevidCfg['sevid']];
    }
    public static function getStatus()
    {
        static $status = array();
        $SevidCfg = Common::getSevidCfg();
        if (!isset($status[$SevidCfg['sevid']])) {
            $cacheKey = 'dft_server_status';
            $cache = Common::getDftMem();
            $data = $cache->get($cacheKey);
            if ($data === false || Game::is_over($data['time'])) {
                $serverList = self::getServList();
                $data = array(
                    'time'=>Game::get_over(60),
                    's'=>$serverList[$SevidCfg['sevid']]['status']
                );
                $cache->set($cacheKey, $data);
            }
            $status[$SevidCfg['sevid']] = $data['s'];
        }
        return $status[$SevidCfg['sevid']];
    }

    /**
     * 获取服务器列表
     */
    static public function getServInfo($sevid){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $sList = $ComVoComModel->getValue();

        if (empty($sList)) {
            //数据读取失败，记录行为
            $fileName = 'server_list_empty_' . date("Ymd") . '.log';
            $content = (PHP_EOL. date("Ymd H:i:s") . $_SERVER['REQUEST_URI'] . PHP_EOL . var_export(debug_backtrace(), true) . PHP_EOL);
            Common::log($fileName, $content);
        }
        if (empty($sList[$sevid])){
            Master::error(SERVER_ERROR_.$sevid);
        }
        return $sList[$sevid];
    }
}
