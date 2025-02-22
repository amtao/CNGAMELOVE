<?php
class AModel
{
	public $uid;

	public $info;
	
	public $config;
	
	public $_update = false;
	protected $_serverID = null;
    /**
     * 实时写入数据库，默认不实时写入
     * @var bool
     */
    protected $_syn_w = false;

	public function __construct($uid)
	{
		//获取其他玩家交互锁
		//Master::get_fuser_lock($uid);
		
		$this->uid = intval($uid);
		
		$this->_serverID = Game::get_sevid($this->uid);
	}

	public function getKey()
	{
		return $this->uid.$this->_key;
	}
	
	public function destroy()
	{
		$cache = $this->_getCache();
		
		if ($this->_syn_w || (defined('SYNC_W')  && SYNC_W)) {
			$this->sync();
			$cache->set($this->getKey(),$this->info);
			return;
		}else{
			$cache->set($this->getKey(),$this->info);
		}
		
		Common::loadLib("sync");
		$sharding = Common::getSharding($this->uid);
		Sync::toBeSync($this->getKey(),$sharding['sharding_id'].$this->_key);
	}

    /**
     * @return MemcachedClass
     */
	protected function _getCache()
    {
		return Common::getCacheBySevId($this->_serverID);
	}

    /**
     * @return Db
     */
    protected function _getDb()
    {
        return Common::getDbBySevId($this->_serverID);
    }
    /*
    private function _getFlowDb()
    {
        return Common::getDbBySevId($this->_serverID,'flow');
    }
    */
}