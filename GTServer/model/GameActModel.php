<?php
/**
 * 活动配置表
 * 
 */
class GameActModel
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
        if (!isset($this->info[$id]['contentsArr'])) {
            $this->info[$id]['contentsArr'] = eval("return {$this->info[$id]['contents']};");
            $this->info[$id]['auditNote'] = self::$_audit_note[$this->info[$id]['audit']];
        }
	    return $this->info[$id];
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
    public function addBySql($sql)
    {
        $db = $this->_getDb();
        $db->query($sql);

        if ($this->_use_cache) {
            $cache = $this->_getCache();
            $this->info = $cache->delete($this->getKey());
        }
    }
	public function getAllInfo($audit = false)
    {
        foreach ($this->info as $id => &$info) {
            if ($audit !== false && $audit != $info['audit']) {
                continue;
            }
            $info['contentsArr'] = eval("return {$info['contents']};");
            $info['auditNote'] = self::$_audit_note[$info['audit']];
        }
        $allInfo = $this->info;
        krsort($allInfo);
        return $allInfo;
    }
	public function add($info)
	{
	    unset($info['contentsArr']);
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
            unset($info['contentsArr']);
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
	    self::_logChangeVer();

	    $this->sync();
        if ($this->_use_cache) {
            $cache = $this->_getCache();
            $cache->set($this->getKey(), $this->info);
        }
	}
    public function getTable()
    {
        return $this->_table;
    }
	public function getKey()
	{
	    return $this->_key;
	}
    public static function getLastChangeVer()
    {
        $cache = Common::getComMem();
        return $cache->get(self::$_last_change_ver_key);
    }
    public static function setLastChangeVer($ver)
    {
        $cache = Common::getComMem();
        $cache->set(self::$_last_change_ver_key, $ver);
    }
    public static function getChangeVer()
    {
        $cache = Common::getComMem();
        return $cache->get(self::$_change_ver_key);
    }
    public static function check_huodong($hdInfo, $open_day){
        if(empty($hdInfo)){
            return 0;
        }

        //将字符串转数组
        $res = array();
        foreach($hdInfo as $info){
            //自动轮回设置
            $autoDay = isset($info['info']['autoDay']) ? intval($info['info']['autoDay']) : 0;
            if ($autoDay > 0) {
                $autoNum = isset($info['info']['autoNum']) ? intval($info['info']['autoNum']) : 999;
                for ($i=0; $i<=$autoNum; $i++) {
                    $autoReal = $autoDay * $i;
                    $inDay = !empty($info['info']['startDay']) && empty($info['info']['endDay'])
                        && $open_day >= ($info['info']['startDay'] + $autoReal)
                        && $open_day <= ($info['info']['endDay'] + $autoReal);
                    if ($inDay) {
                        $info['info']['id'] += $autoReal;
                        $info['info']['startDay'] += $autoReal;
                        $info['info']['endDay'] += $autoReal;
                        break;
                    }
                    $inTime = !empty($info['info']['startTime']) && !empty($info['info']['endTime'])
                        && $_SERVER['REQUEST_TIME'] >= (strtotime($info['info']['startTime']) + $autoReal * 86400)
                        && $_SERVER['REQUEST_TIME'] <= (strtotime($info['info']['endTime']) + $autoReal * 86400);
                    if ($inTime) {
                        $info['info']['id'] += $autoReal;
                        $info['info']['startTime'] = date("Y-m-d H:i:s", (strtotime($info['info']['startTime']) + $autoReal * 86400));
                        $info['info']['endTime'] = date("Y-m-d H:i:s", (strtotime($info['info']['endTime']) + $autoReal * 86400));
                        break;
                    }
                }
            }
            //自动轮回设置end

            //每天重置活动
            if($info['info']['id'] == 'day'){
                $info['info']['id'] = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
            }
            //开服时间
            if(  !empty($info['info']['startDay']) && !empty($info['info']['endDay']) ){
                $todayt = Game::day_0();  //今天0点的时间戳
                //活动开始时间戳
                $info['info']['sTime'] = $todayt - ($open_day - $info['info']['startDay']) * 86400;
                //活动结束时间戳
                $info['info']['eTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 1;
                //展示结束时间
                $info['info']['showTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 1;


                if ($info['info']['type'] == 3) {  //3:冲榜活动
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 7200;
                }
                if ($info['info']['type'] == 4) {  //4:充值活动
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 1;
                    $info['info']['showTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 1;
                }
                if ($info['info']['type'] == 7) {  // 7:新官上任
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 7200;
                }
                if ($info['info']['type'] == 8) {  //  8：狩猎
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay'] - $open_day + 1) * 86400 - 7200;
                }

                unset($info['info']['startDay']);
                unset($info['info']['endDay']);
                unset($info['info']['startTime']);
                unset($info['info']['endTime']);

                $res[] = $info;
            }

            //固定日期开始时间内
            if( empty($info['info']['startDay']) && empty($info['info']['endDay']) &&
                !empty($info['info']['startTime']) && !empty($info['info']['endTime']) ){
                //活动开始时间戳
                $info['info']['sTime'] = strtotime($info['info']['startTime']);
                //活动结束时间戳
                $info['info']['eTime'] = strtotime($info['info']['endTime']);
                if ($info['info']['type'] == 3) {  //3:冲榜活动
                    $info['info']['eTime'] = strtotime($info['info']['endTime']) - 7200 + 1;
                }
                if ($info['info']['type'] == 7) {  // 7:新官上任
                    $info['info']['eTime'] = strtotime($info['info']['endTime']) - 7200 + 1;
                }
                if ($info['info']['type'] == 8) {  // 8：狩猎
                    $info['info']['eTime'] = strtotime($info['info']['endTime']) - 7200 + 1;
                }
                if ($info['info']['type'] == 4) {  //4:充值活动
                    $info['info']['eTime'] = strtotime($info['info']['endTime']);
                }
                //展示结束时间
                $info['info']['showTime'] = strtotime($info['info']['endTime']);

                unset($info['info']['startDay']);
                unset($info['info']['endDay']);
                unset($info['info']['startTime']);
                unset($info['info']['endTime']);

                $res[] = $info;
            }
        }

        return $res;
    }
    public static function create_cfg($key,$info){
        $str = explode('huodong_',$key);
        $info['info']['no'] = intval($str[1]);
        switch($key){
            case 'huodong_201':
            case 'huodong_202':
            case 'huodong_203':
            case 'huodong_204':
            case 'huodong_205':
            case 'huodong_206':
            case 'huodong_207':
            case 'huodong_208':
            case 'huodong_209':
            case 'huodong_210':
            case 'huodong_211':
            case 'huodong_212':
            case 'huodong_213':
            case 'huodong_214':
            case 'huodong_215':
            case 'huodong_216':
            case 'huodong_217':
            case 'huodong_218':
            case 'huodong_219':
            case 'huodong_220':
            case 'huodong_221':
            case 'huodong_222':
            case 'huodong_223':
            case 'huodong_224':
            case 'huodong_225':

            case 'huodong_260':
            case 'huodong_261':
            case 'huodong_262':
            case 'huodong_6139':
                $info['brwd'] = Game::get_key2id($info['rwd'],'id');
                break;
        }
        return $info;
    }
    protected static function _logChangeVer()
    {
        $cache = Common::getComMem();
        $cache->set(self::$_change_ver_key, time());
    }
    protected static $_change_ver_key = 'model_game_act_change_ver';
    protected static $_last_change_ver_key = 'model_game_act_last_change_ver';
    protected $_table = "game_act";
    protected $_key = 'model_game_act';
    protected $_columns = array(
	    'id', 'act_key', 'server', 'sort', 'audit', 'auser', 'atime', 'contents', 'status'
	);
	public $info;
    protected $_columns_json = array();
    protected $_columns_base64 = array('contents');
	public $_update = false;
	const STATUS_OK = 0;
	const STATUS_DEL = 1;
    const AUDIT_WAIT = 0;
    const AUDIT_PASS = 1;
    const AUDIT_NO_PASS = 2;
    protected static $_audit_note = array(
        0 => '待审核',
        1 => '已审核',
        2 => '审核不过',
    );
    protected function _getCache()
    {
        return Common::getComMem();
    }
    protected function _getDb()
    {
        return Common::getComDb();
    }
}