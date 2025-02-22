<?php
/**
 * 活动配置表
 * 
 */
class GameConfigModel
{
	public function __construct()
	{
	    $cache = $this->_getCache();
	    $this->info = $cache->get($this->getKey());
	    if( $this->info === false ) {
	        $table = $this->getTable();
	        $sql = "select * from `{$table}` where `status`=" . self::STATUS_OK . " order by `id` desc;";
	        $db = $this->_getDb();
	        $data = $db->fetchArray($sql);
	        if(empty($data)) {
	            $this->info = array();return;
	        }
	        $info = array();
	        foreach ($data as $v) {
	            foreach ($this->_columns as $colName) {
	                if (isset($v[$colName])) {
                        $colValue = $v[$colName];
                        if (in_array($colName, $this->_columns_json)) {
                            $colValue = json_decode($v[$colName], true);
                        } else if (in_array($colName, $this->_columns_base64)) {
                            $colValue = base64_decode($v[$colName]);
                        }
	                    $v[$colName] = $colValue;
	                }
	            }
	            $info[$v['id']] = $v;
	        }
	        $this->info = $info;
	        $cache->set($this->getKey(), $this->info);
	    }
	}
	public function searchContents($serverID, $key)
    {
        static $res = array();
        $resKey = "search_{$serverID}_{$key}";
        if (!isset($res[$resKey])) {
            $searchLog = array();
            foreach ($this->info as $info) {
                if ($info['config_key'] != $key) {continue;}
                if (isset($searchLog[$key]) && $info['server'] == 'all') {
                    //防止被同服覆盖
                    continue;
                }
                if ($info['server'] != 'all') {
                    $serList = Game::serves_str_arr($info['server']);
                    if (!in_array($serverID, $serList)) {
                        continue;
                    }
                }
                //范围小的覆盖范围大的
                $searchLog[$key] = $info['contents'];
            }
            $res[$resKey] = $searchLog[$key];
        }
        return $res[$resKey];
    }
	public function existsInfo($id)
    {
	    return isset($this->info[$id]);
	}
	public function getInfo($id)
    {
	    return $this->info[$id];
	}
	public function getAllInfo()
    {
        return $this->info;
    }
	public function add($info)
	{
	    $setArr = array();
        foreach ($this->_columns as $colName) {
        	if (isset($info[$colName])) {
                $colValue = $info[$colName];
                if (in_array($colName, $this->_columns_json)) {
                    $colValue = json_encode($info[$colName], JSON_UNESCAPED_UNICODE);
                } else if (in_array($colName, $this->_columns_base64)) {
                    $colValue = base64_encode($info[$colName]);
                }
        		$setArr[] = "`{$colName}`='{$colValue}'";
        	}
        }
        if (empty($setArr)) {
        	return false;
        }
        $setSql = implode(",", $setArr);	
	    //插入数据库
	    $table = $this->getTable();
	    $sql = "insert into `{$table}` set {$setSql};";
	    $db = $this->_getDb();
	    $db->query($sql);
	    $insertId = $db->insertId();
	    $info['id'] = $insertId;
	    $this->info[$insertId] = $info;
	    $this->_update = true;
	    return $insertId;
	}
	public function delete($id)
	{
	    if (!isset($this->info[$id])) {return false;}

	    $oldInfo = $this->info[$id];
	    $table = $this->getTable();
	    $request_time = $_SERVER['REQUEST_TIME'];
	    $sql = "UPDATE `{$table}` SET `status`=".self::STATUS_DEL." WHERE `id` ={$id};";
	    $db = $this->_getDb();
	    $db->query($sql);	
	    unset($this->info[$id]);
	    $this->_update = true;
        return true;
	}
	public function update($data)
	{
        unset($data['contentsArr']);
	    if (empty($data)) {
	        return true;
	    }
	    //必要字段
	    if (!isset($data['id'])) {
	        exit('缺少id');
	    }
	    if (isset($this->info[$data['id']])) {
	        $info = $this->info[$data['id']];	        	
	        foreach ($data as $colName => $v) {
	            if (in_array($colName, $this->_columns)) {
	                $info[$colName] = $data[$colName];
	            }
	        }
	        $info['_update'] = true;
	        $this->info[$data['id']] = $info;	        		        
	    }
	    $this->_update = true;
        return true;
	}
	public function sync()
	{
	    if (!is_array($this->info)) {
			return false;
		}
		if (!$this->_update){
			return true;
		}
		$this->_update = false;
	    
		foreach ($this->info as &$info) {
		    if (isset($info['_update']) && $info['_update']){
		        $info['_update'] = false;
		        $setArr = array();
		        foreach ($this->_columns as $colName) {
		            if (isset($info[$colName])) {
                        $colValue = $info[$colName];
                        if (in_array($colName, $this->_columns_json)) {
                            $colValue = json_encode($info[$colName], JSON_UNESCAPED_UNICODE);
                        } else if (in_array($colName, $this->_columns_base64)) {
                            $colValue = base64_encode($info[$colName]);
                        }
		                $setArr[] = "`{$colName}`='{$colValue}'";
		            }
		        }
		        if (empty($setArr)) {continue;}
		        $table = $this->getTable();
		        $setSql = implode(",", $setArr);
		        $sql = "update `{$table}` set {$setSql} where `id`={$info['id']} limit 1;";
		        $db = $this->_getDb();
		        $db->query($sql);
		    }
		}	    
	    return true;
	}
	public function destroy()
	{
	    $this->sync();
        $cache = $this->_getCache();
	    $cache->set($this->getKey(), $this->info);
	}
	public function deleteCache()
	{
	    //清理缓存
	    $cache = $this->_getCache();
	    $cache->delete($this->getKey());
	}
    public function getTable()
    {
        return $this->_table;
    }
	public function getKey()
	{
	    return $this->_key;
	}
    protected $_table = "game_config";
    protected $_key = 'model_game_config';
    protected $_columns = array(
	    'id', 'config_key', 'server', 'contents', 'status'
	);
	public $info;
    protected $_columns_json = array();
    protected $_columns_base64 = array('contents');
	public $_update = false;
	const STATUS_OK = 0;
	const STATUS_DEL = 1;
    protected function _getCache()
    {
        return Common::getComMem();
    }
    protected function _getDb()
    {
        return Common::getComDb();
    }
}