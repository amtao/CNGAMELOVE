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
        $sListFromDB = $ComVoComModel->getValue();
        if (empty($sListFromDB)) {
            $fileName = 'server_list_empty_' . date("Ymd") . '.log';
            $content = (PHP_EOL. date("Ymd H:i:s") . $_SERVER['REQUEST_URI'] . PHP_EOL . var_export(debug_backtrace(), true) . PHP_EOL);
            Common::log($fileName, $content);
        }

        Common::loadModel('HoutaiModel');
        $sList = HoutaiModel::read_servers();
        if (empty($sListFromDB)) {
            $ComVoComModel->updateValue($sList);
        }
        return empty($sList)?array():$sList;

    }

    /**
     * 添加服务器列表
     */
    static public function addServList($data){

        Common::loadModel('HoutaiModel');
        $sList = HoutaiModel::read_servers();
        $sList[$data['id']] = $data;

        HoutaiModel::write_servers($sList);

        return $sList;

    }

    /**
     * 删除服务器列表
     */
    static public function delServList($key){

        Common::loadModel('HoutaiModel');
        $sList = HoutaiModel::read_servers();
        unset($sList[$key]);
        HoutaiModel::write_servers($sList);
        return true;
    }

    /**
     * 获取开服第几天
     * @param unknown_type $servid   服务器id
     */
    static public function getOpenDays($servid){
        Common::loadModel('HoutaiModel');
        static $slist;
        if(empty($slist[$servid])){
            $sList = HoutaiModel::read_servers();
            if(empty($sList[$servid])){
                $slist[$servid] =  0;
            }else{
                if($sList[$servid]['showtime'] > $_SERVER['REQUEST_TIME']){
                    $slist[$servid] =  0;
                }else{
                    //跨过几个0点
                    $days =   Game::day_count($sList[$servid]['showtime']);
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
        Common::loadModel('HoutaiModel');
        $serv_day = 0;
        $hd_slist = HoutaiModel::read_servers();
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
