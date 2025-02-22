<?php
/**
 * 活动模板配置表
 * 
 */
class GameActTemplateModel
{
    /**
     * 不使用mem存储数据（数据量太大）
     * @var bool
     */
    protected $_use_cache = false;
	public function __construct()
	{
        $this->info = false;
        if ($this->_use_cache) {
            $cache = $this->_getCache();
            $this->info = $cache->get($this->getKey());
        }
	    if( $this->info === false ) {
	        $table = $this->getTable();
	        $sql = "select * from `{$table}` where `status`=" . self::STATUS_OK . ";";
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
            if ($this->_use_cache) {
                $cache->set($this->getKey(), $this->info);
            }
	    }
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
        $allInfo = $this->info;
        krsort($allInfo);
        return $allInfo;
    }
    public function getCategory()
    {
        $list = $this->getAllInfo();
        $category = array('select'=>'请选择', '无分类');
        foreach ($list as $v) {
            $title = explode('-', $v['title']);
            if (isset($title[1])) {
                $category[] = $title[1];
            }
        }
        return array_unique($category);
    }
    public function getInfoByCate($cate)
    {
        $list = $this->getAllInfo();
        $category = array('无分类'=>array());
        foreach ($list as $v) {
            $title = explode('-', $v['title']);
            if (isset($title[1])) {
                if (!isset($category[$title[1]])) {
                    $category[$title[1]] = array();
                }
                $category[$title[1]][] = $v;
            } else {
                $category['无分类'][] = $v;
            }
        }
        return isset($category[$cate]) ? $category[$cate] : array();
    }

    public function getInfoById($id)
    {
        $list = $this->getAllInfo();
        $category = array('无分类'=>array());
        foreach ($list as $v) {
            if (isset($v['id'])) {
                if (!isset($category[$v['id']])) {
                    $category[$v['id']] = array();
                }
                $category[$v['id']][] = $v;
            } else {
                return null;
            }
        }
        return isset($category[$id]) ? $category[$id] : array();
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
	    $this->info[$info['id']] = $info;
	    $this->_update = true;
	    return $info['id'];
	}
    public function addBySql($sql)
    {
        $db = $this->_getDb();
        $db->query($sql);

        if ($this->_use_cache) {
            $cache = $this->_getCache();
            $this->info = $cache->delete($this->getKey());
        }
    }
    public function getAddSql($info)
    {
        $setArr = array();
        foreach ($this->_columns as $colName) {
            if ($colName == 'id') {continue;}
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
        return "insert into `{$table}` set {$setSql};";
    }
	public function delete($id)
	{
	    if (!isset($this->info[$id])) {return false;}
	    $table = $this->getTable();
	    $sql = "UPDATE `{$table}` SET `status`=".self::STATUS_DEL." WHERE `id` ={$id};";
	    $db = $this->_getDb();
	    $db->query($sql);
	    unset($this->info[$id]);
	    $this->_update = true;
        return true;
	}
	public function update($data)
	{
	    if (empty($data)) {
	        return true;
	    }
	    //必要字段
	    if (!isset($data['id'])) {
	        exit('缺少 id');
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
	    if (!$this->_update) {
	        return false;
        }
	    $this->sync();

        if ($this->_use_cache) {
            $cache = $this->_getCache();
            $cache->set($this->getKey(), $this->info);
        }
        return true;
	}
    public function getTable()
    {
        return $this->_table;
    }
	public function getKey()
	{
	    return $this->_key;
	}
    protected $_table = "game_act_template";
    protected $_key = 'model_game_act_template';
    protected $_columns = array(
	   'id', 'title', 'act_key', 'auser', 'atime', 'contents', 'status'
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