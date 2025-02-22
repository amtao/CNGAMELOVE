<?php
class BModel
{
    protected $_server_type = 1;//1：合服，2：跨服，3：全服，4：指定跨服，5：本服，6：指定服务器
    protected $_server_id = null;
    /**
     * $_server_kua_cfg 实例：
     * array(
     *  array('起始服编号', '结束服编号'),//大区
     *  array('起始服编号', '结束服编号'),//大区
     *  array('起始服编号', '结束服编号'),//大区
     *  ……
     * )
     */
    protected $_server_kua_cfg = array();//指定跨服配置
    public $_server_kua_key = '';//指定跨服配置对应的key
	public function __construct($serverID = null)
	{
        $this->_server_id = empty($serverID) ? $this->_getServerID() : $this->_getServerIDBy($serverID);
	}

    /**
     * @return Db
     */
    protected function _getDB()
    {
        return Common::getDbBySevId($this->_server_id);
    }

    /**
     * @return MemcachedClass
     */
    protected function _getCache()
    {
        return Common::getCacheBySevId($this->_server_id);
    }

    /**
     * @return RedisClass
     */
    protected function _getRedis()
    {
        return Common::getRedisBySevId($this->_server_id);
    }
    protected function _getKuaCfgServerID($argServerID = null)
    {
        $this->_get_ksev();
        if (empty($this->_server_kua_cfg)) {
            Master::error("server_kua_cfg is empty");
        }
        if (empty($argServerID)) {
            $SevidCfg = Common::getSevidCfg();
            $curServerID = $SevidCfg['he'];
        } else {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            $curServerID = $SevCfgObj->getHE();
        }
        $serverID = Game::getKuaCfgServerID($this->_server_kua_cfg, $curServerID);
        if ($serverID === false) {
            Master::error("server_kua_cfg is error");
        }
        return $serverID;
    }
    /**
     * 获取跨区数组信息
     * @param $data
     */
    protected function _get_ksev()
    {
        if(!empty($this->_server_kua_key)){
            switch($this->_server_kua_key){
                case 'clubpk':  //获取帮会战的跨服信息
                    $clubpk = Game::get_peizhi('clubpk');
                    if(!empty($clubpk['ksev'])){
                        $this->_server_kua_cfg = $clubpk['ksev'];
                    }
                    break;
                case 'cross_section'://跨服势力榜和帮会榜
                    $clubpk = Game::get_peizhi('cross_section');
                    if(!empty($clubpk['ksev'])){
                        $this->_server_kua_cfg = $clubpk['ksev'];
                    }
                    break;
            }
        }
    }
    protected function _getServerID()
    {
        if ($this->_server_type == 6) {
            exit('参数异常');
        }
        else if ($this->_server_type == 5) {
            $SevidCfg = Common::getSevidCfg();
            return $SevidCfg['sevid'];
        }
        else if ($this->_server_type == 4) {
            return $this->_getKuaCfgServerID();
        }
        else if ($this->_server_type == 3) {
            Common::loadModel("ServerModel");
            return ServerModel::getDefaultServerId();
        } else if ($this->_server_type == 2) {
            $SevidCfg = Common::getSevidCfg();
            return $SevidCfg['kua'];
        } else {
            $SevidCfg = Common::getSevidCfg();
            return $SevidCfg['he'];
        }
    }
    protected function _getServerIDBy($argServerID)
    {
        if ($this->_server_type == 6) {
            //指定服务器
            return $argServerID;
        }
        else if ($this->_server_type == 5) {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            return $SevCfgObj->getMY();
        }
        else if ($this->_server_type == 4) {
            return $this->_getKuaCfgServerID($argServerID);
        }
        else if ($this->_server_type == 3) {
            Common::loadModel("ServerModel");
            return ServerModel::getDefaultServerId();
        } else if ($this->_server_type == 2) {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            return $SevCfgObj->getKUA();
        } else {
            $SevCfgObj = Common::getSevCfgObj($argServerID);
            return $SevCfgObj->getHE();
        }
    }
}