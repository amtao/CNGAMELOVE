<?php
require_once 'aliyun-php-sdk-core/Config.php';
class RiskControl
{
    private static $_accessKeyId = 'LTAIPZ9TBMVTjjV9';
    private static $_accessKeySecret = 'RpPevfeNrB77nK70yxyCeNJrO5Dt9z';
    /**
     * 是否模拟器
     * @var bool
     */
    private $_isSimulator = false;
    /**
     * 是否人机策略
     * @var bool
     */
    private $_isHumanComputer = false;
    /**
     * 是否白mind
     * @var bool
     */
    private $_isWhiteList = true;
    /**
     * 是否黑名单
     * @var bool
     */
    private $_isBlackList = false;
    /**
     * 是否风险名单
     * @var bool
     */
    private $_isRiskDevice = false;
    /**
     * 是否高净值用户
     * @var bool
     */
    private $_isHighNetWorth = false;
    /**
     * 风险结果
     * @var array
     */
    private $_riskResult = array();
    private $_logPath = '/tmp/';
    private $_debug = true;
    public function  __construct($debug = true, $logPath = '')
    {
        $this->_debug = $debug;
        if (!empty($logPath)) {
            $this->_logPath = $logPath;
        }
        $this->_logPath .= DIRECTORY_SEPARATOR.'RiskControl.log';
    }
    public function validation($raw, $sign)
    {
        try {
            $profile = DefaultProfile::getProfile("cn-hangzhou", self::$_accessKeyId, self::$_accessKeySecret);
            DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "jaq", "jaq.aliyuncs.com");
            $client = new DefaultAcsClient($profile);

            $request = new DeviceRiskControlRequest();
            $request->setRawData($raw);
            $request->setSign($sign);

            $response = $client->getAcsResponse($request);
            if ($response->Code != 200) {
                //请求异常记日志
                Common::logMsg(
                    $this->_logPath,
                    sprintf("code error, time:%s|response:%s".PHP_EOL, date('Y-m-d H:i:s'), var_export($response, true))
                );
                return false;
            }
            if ($response->Data->SignResult->SignStatus != 1) {
                //验证异常异常记日志
                Common::logMsg(
                    $this->_logPath,
                    sprintf("signResult error ,time:%s|response:%s".PHP_EOL, date('Y-m-d H:i:s'), var_export($response, true))
                );
                return false;
            }
            $this->_riskResult = $response->Data->RiskResult;
            $this->_isSimulator = $response->Data->RiskResult->IsSimulator;
            $this->isHumanComputer = $response->Data->RiskResult->HumanComputer;
            $this->_isWhiteList = $response->Data->RiskResult->IsWhiteList;
            $this->_isBlackList = $response->Data->RiskResult->IsBlackList;
            $this->_isRiskDevice = $response->Data->RiskResult->IsRiskDevice;
            $this->_isHighNetWorth = $response->Data->RiskResult->IsHighNetWorth;
            //验证正常，详细判断结果通过其他接口获取
            return true;
        } catch (Exception $e) {
            //抛出异常记日志
            Common::logMsg(
                $this->_logPath,
                sprintf("signResult error ,time:%s|response:%s".PHP_EOL, date('Y-m-d H:i:s'), 'has Exception')
            );
            return false;
        }
    }
    public function isSimulator()
    {
        return $this->_isSimulator;
    }
    public function isHumanComputer()
    {
        return $this->_isHumanComputer;
    }
    public function isWhiteList()
    {
        return $this->_isWhiteList;
    }
    public function isBlackList()
    {
        return $this->_isBlackList;
    }
    public function isRiskDevice()
    {
        return $this->_isRiskDevice;
    }
    public function isHighNetWorth()
    {
        return $this->_isHighNetWorth;
    }
    public function getRiskResult()
    {
        return $this->_riskResult;
    }
}