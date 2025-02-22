<?php
/**
 * Class DeviceModel
 */

class DeviceModel{
    /**
     * @param $uid     玩家UID
     * @param $device  设备号
     * @param $platform  平台
     * @param string $param  其他参数 备用
     */

    public static function add($uid, $platform, $device, $param ='none'){
        $table = 'devices';
        $time = time();
        $device = trim($device);
        $sql = "INSERT INTO `device` (`uid`, `platform`, `device`, `param`, `time`) VALUES 
                (".$uid.", '". $platform."', '". $device."', '". $param."', ".$time.");";
        $db = Common::getMyDb();
        $result = $db->query($sql);
    }

    /**
     * 设备号是否存在
     * @param $device
     * @return bool
     */
    public static function is_exist($uid, $platform, $device){
        $sql = 'SELECT * FROM `device` WHERE `device`="'.$device.'" AND `uid`='.$uid.' AND `platform`="'.$platform.'";';
        $db = Common::getMyDb();
        $result = $db->fetchRow($sql);
        return empty($result)?false:true;
    }

    /**
     * 同个uid存在多个设备号时，取最新的一个设备号
     * @param $uid
     * @return string
     */
    public static function get_device($uid){
        $cache = Common::getCacheByUid($uid);
        $cache_key = $uid . __METHOD__;
        $device = $cache->get($cache_key);
        if($device == false){
            $sql = "SELECT * FROM `device` WHERE `uid`={$uid} order by `time` DESC ";
            $db = Common::getDbeByUid($uid);
            $result = $db->fetchArray($sql);
            $device = empty($result[0]['param']) ? 'none' : $result[0]['param'];
            $cache->set($cache_key, $device);
        }
        return $device;
    }

}