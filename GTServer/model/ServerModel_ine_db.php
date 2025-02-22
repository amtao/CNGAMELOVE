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
        if (0&&empty($sList)) {
            Common::loadModel('HoutaiModel');
            $sList = HoutaiModel::read_servers();
            $sList = empty($sList)?array():$sList;
            $ComVoComModel->updateValue($sList);
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


}
