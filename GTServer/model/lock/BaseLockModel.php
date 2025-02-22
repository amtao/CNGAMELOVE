<?php
/**
 * 锁基类
 * Class BaseLockModel
 *
 * 使用说明，如本服锁则，详细参数见参数说明
 * Common::loadLockModel('DftLockModel');
 * $DftLockModel = new DftLockModel('some_lock_key');
 * if ($DftLockModel->hasLock()) {
 *  echo '有锁';return;
 * }
 * //业务逻辑
 * ...
 * //业务逻辑结束
 * $DftLockModel->releaseLock();
 *
 * PS：可以继承的方式固定参数，方便调用
 */
abstract class BaseLockModel
{
    protected $_server_type = 1;//1：合服，2：跨服，3：全服
    protected $_key_pre = "vo_lock_";
    protected $_cache_key = null;
    protected $_time_out = 5;
    protected $_sleep_wait_ms_time = 100000;
    protected $_server_id = null;
    protected static $_lockRes = array();
    /**
     * BaseLockModel constructor.
     * @param string $key 锁钥匙
     * @param int $serverID 指定服务器编号
     */
    public function __construct($key, $serverID = null)
    {
        if (empty($key)) {
            Master::error(__CLASS__.'_key_null'.__LINE__);
        }
        $this->_server_id = empty($serverID) ? $this->_getServerID() : $this->_getServerIDBy($serverID);

        $key = "{$this->_key_pre}{$key}";
        $this->_cache_key = $key;
    }
    /**
     * 取锁
     * @param int $sleepCD，0阻塞，其他值非阻塞$sleepCD秒
     * @return bool true取锁成功，false取锁失败
     */
    public function getLock($sleepCD = 0)
    {
        //同一进程结果一致
        if (!isset(self::$_lockRes[$this->_cache_key])) {
            if ($sleepCD > 0) {
                //轮询200次，避免死锁
                $waitNumMax = $sleepCD > 0 ? intval($sleepCD * 10) : 10;
                $waitNum = 0;
                while ($waitNum < $waitNumMax && $this->_getCache()->add($this->_cache_key, 1, $this->_time_out) === false) {
                    //取不到锁，等待
                    $waitNum++;
                    usleep($this->_sleep_wait_ms_time);
                }
                self::$_lockRes[$this->_cache_key] = $waitNum < $waitNumMax;
            }
            else {
                self::$_lockRes[$this->_cache_key] = $this->_getCache()->add($this->_cache_key, 1, $this->_time_out) !== false;
            }
        }
        return self::$_lockRes[$this->_cache_key];
    }
    public function releaseLock()
    {
        unset(self::$_lockRes[$this->_cache_key]);
        $this->_getCache()->delete($this->_cache_key);
    }
    /**
     * @return MemcachedClass
     */
    protected function _getCache()
    {
        return Common::getCacheBySevId($this->_server_id);
    }
    protected function _getServerID()
    {
        if ($this->_server_type == 3) {
            Common::loadModel("ServerModel");
            return ServerModel::getDefaultServerId();
        } else if ($this->_server_type == 2) {
            $SevidCfg = Common::getSevidCfg();
            return $SevidCfg['kua'];
        } else if ($this->_server_type == 4) {
            $SevidCfg = Common::getSevidCfg();
            return $SevidCfg['sevid'];
        } else {
            $SevidCfg = Common::getSevidCfg();
            return $SevidCfg['he'];
        }
    }
    protected function _getServerIDBy($argServerID)
    {
        if ($this->_server_type == 3) {
            Common::loadModel("ServerModel");
            return ServerModel::getDefaultServerId();
        } else if ($this->_server_type == 2) {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            return $SevCfgObj->getKUA();
        } else if ($this->_server_type == 4) {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            return $SevCfgObj->getMY();
        } else {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            return $SevCfgObj->getHE();
        }
    }
}