<?php
/**
 * 风险设备模型
 */
class RiskDeviceModel
{
    public static function check($deviceNum, $rawSign)
    {
        $guanq = Game::get_peizhi('gq_status');
        if(!isset($guanq['risk_device_limit']) || $guanq['risk_device_limit'] == 0){
            return true;
        }
        if (Common::istestuser()) {
            return true;
        }

        if (empty($deviceNum)) {
            return true;
        }

        $riskInfo = self::getData($deviceNum);
        if (empty($riskInfo)) {
            require_once LIB_DIR.'/sdk/aliyun/RiskControl.php';
            $RiskControl = new RiskControl(true, FILE_PATH);
            if ($RiskControl->validation($rawSign['raw'], $rawSign['sign'])) {
                $riskInfo = $RiskControl->getRiskResult();
                self::addData($deviceNum, $riskInfo);
            }
            else {
                Master::error(CHECK_ERR);
            }
        }
        if ($riskInfo['isBlackList']) {
            Master::error('isBlackList');
        }
        if ($riskInfo['humanComputer']) {
            Master::error('humanComputer');
        }
        return true;
    }
    public static function getData($deviceNum)
    {
        $cache = Common::getComMem();
        $data = $cache->get(self::_getKey($deviceNum));
        if (empty($data)) {
            $db = Common::getComDb();
            $sql = sprintf("select * from `%s` where `key`='%s' limit 1;", self::$_table, $deviceNum);
            $row = $db->fetchRow($sql);
            $data = empty($row) ? array() : json_decode($row['value'], true);
            $cache->set(self::_getKey($deviceNum), $data);
        }
        return $data;
    }
    public static function addData($deviceNum, $riskInfo)
    {
        if (empty($deviceNum) || empty($riskInfo)) {
            return true;
        }
        $riskInfoSave = json_encode($riskInfo, JSON_UNESCAPED_UNICODE);
        $db = Common::getComDb();
        $sql = sprintf(
            "INSERT INTO `%s` VALUES('%s', '%s') ON DUPLICATE KEY UPDATE `value`='%s';",
            self::$_table,
            $deviceNum,
            $riskInfoSave
        );
        $db->query($sql);

        $cache = Common::getComMem();
        $cache->set(self::_getKey($deviceNum), $riskInfo);

        return true;
    }
    private static function _getKey($deviceNum)
    {
        return self::$_key.$deviceNum;
    }
    private static $_key = 'RiskDeviceModel_';
    private static $_table = 'risk_device';
}