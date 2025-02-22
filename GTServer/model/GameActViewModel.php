<?php
/**
 * 活动预览配置表
 * 
 */
class GameActViewModel
{
    protected static $_save_key = 'game_act_new_time';
    public static function getNewTime()
    {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $comValue = $ComVoComModel->getValue();
        return empty($comValue['newTime']) ? $_SERVER['REQUEST_TIME'] : strtotime($comValue['newTime']);
    }
    public static function getValue()
    {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        return $ComVoComModel->getValue();
    }
    public static function setValue($value)
    {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        return $ComVoComModel->updateValue($value);
    }
    public static function useNewTime()
    {
        /*
        $SevidCfg = Common::getSevidCfg();
        static $valueStatic = array();
        if (!isset($valueStatic[$SevidCfg['sevid']])) {
            //暂时不用缓存
//            $cache = Common::getDftMem();
//            $key = self::$_save_key . '_value';
//            $value = $cache->get($key);
//            if ($value === false || Game::is_over($value['out_time'])) {
                Common::loadVoComModel('ComVoComModel');
                $ComVoComModel = new ComVoComModel(self::$_save_key);
                $value = $ComVoComModel->getValue();
//                $value['out_time'] = Game::get_now() + 60;
//                $cache->set($key, $value);
//            }
            $valueStatic[$SevidCfg['sevid']] = $value;
        }
        $info = $valueStatic[$SevidCfg['sevid']];
        $infoIP = empty($info['ip']) ? array() : eval("return {$info['ip']};");

        $test_ip = empty($infoIP) || !is_array($infoIP) ? array() : $infoIP;
        return is_array($test_ip) && in_array(Common::GetIP(), $test_ip);
        */
        return in_array(Common::GetIP(), array(/*'27.154.231.94', "202.104.136.208"*/));
    }
}