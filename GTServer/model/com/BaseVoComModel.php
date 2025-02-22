<?php
/**
 * 公共存储基类，抽象类防止被直接对象化
 * Class BaseVoComModel
 */
abstract class BaseVoComModel
{
    protected $_server_type = 1;//1：合服，2：跨服，3：全服
    protected $_key_pre = "vo_common_";
    protected $_key = null;
    protected $_cache_key = null;
    protected $_default = array();
    protected $_addslashes = false;
    public function __construct($key, $addslashes = false)
    {
        if (empty($key)) {
            Master::error(__CLASS__.'_key_null'.__LINE__);
        }
        $this->_addslashes = $addslashes;
        $key = "{$this->_key_pre}{$key}";
        $this->_cache_key = $key;
        $this->_key = $key;
    }
    public function getValue()
    {
		$cache = $this->_getCache();
		$data = $cache->get($this->_cache_key);
		if ($data === false) {
            $getdata =false;
            $fileName = 'server_list_empty_' . date("Ymd") . '.log';
            $content = (PHP_EOL.date("Ymd H:i:s") . $_SERVER['REQUEST_URI'] . PHP_EOL . 'empty cache data' . PHP_EOL .
                var_export($cache->_mem_config, true) . PHP_EOL);
           Common::log($fileName, $content);

		    $db = $this->_getDB();
    	    $sql = "select * from `vo_common` where `key`='{$this->_key}' limit 1;";
    	    $row = $db->fetchRow($sql);
            if (empty($row)) {
                $fileName = 'server_list_empty_' . date("Ymd") . '.log';
                $content = (PHP_EOL. date("Ymd H:i:s") . $_SERVER['REQUEST_URI'] . PHP_EOL . 'empty db row' . PHP_EOL .
                    var_export($db->_db_config, true) . PHP_EOL . var_export(debug_backtrace(), true) . PHP_EOL);
                Common::log($fileName, $content);
            }
            if (empty($row['value'])) {
                $data = $this->_default;
            } else {
                $data = json_decode($row['value'], true);
                if (empty($data)) {
                    $fileName = 'server_list_empty_' . date("Ymd") . '.log';
                    $content = (PHP_EOL. date("Ymd H:i:s") . $_SERVER['REQUEST_URI'] . 'empty json_decode' . PHP_EOL .
                        var_export($db->_db_config, true) . PHP_EOL . serialize($row) . PHP_EOL . serialize($data) . PHP_EOL);
                    Common::log($fileName, $content);
                }else{
                    $cache->set($this->_cache_key, $data);
                }
            }
    	    
		}
        return $data;
    }
	public function updateValue($data)
	{
	    if (empty($data)) {$data = array();}
        $dataSave = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($this->_addslashes) {
            $dataSave = addslashes($dataSave);
        }
        $db = $this->_getDB();
        $sql = "INSERT INTO `vo_common` VALUES('{$this->_key}', '{$dataSave}') ON DUPLICATE KEY UPDATE `value`='{$dataSave}';";
		$db->query($sql);
		//删除缓存
        $cache = $this->_getCache();
        $cache->set($this->_cache_key, $data);
	}
    protected function _getDB()
    {
        if ($this->_server_type == 3) {
            return Common::getComDb();
        } else if ($this->_server_type == 2) {
            return Common::getKuaDb();
        } else {
            return Common::getDftDb();
        }
    }
    protected function _getCache()
    {
        if ($this->_server_type == 3) {
            return Common::getComMem();
        } else if ($this->_server_type == 2) {
            return Common::getKuaMem();
        } else {
            return Common::getDftMem();
        }
    }
}