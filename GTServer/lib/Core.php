<?php

/**
 * Core
 *
 * @category   Common
 * @author     fisher.lee <63764977@qq.com>
 * @version    $Id: Api.php 2011-05-22 14:57:20Z fisher.lee$
 *
 *
 * class Controller
 * {
 * public $config = array();
 * public $uid;
 * public $sharding;
 *
 * public function __construct($request)
 * {
 * }
 * }*/
class XHProfTool
{
    private static $_key = 'xhprof';
    private static $_time_step = 1;
    private static $_on = false;
    private static $_start_time = 0;

    public static function setKey($key)
    {
        self::$_key = $key;
    }

    public static function setTimeStep($step)
    {
        self::$_time_step = $step;
    }

    /**
     * 开启分析
     * @param double $startTime 起始时间
     * @param mixed $on 默认null，由defined('XHPROF_ON')决定开启
     */
    public static function start($startTime, $on = null)
    {
        self::$_start_time = $startTime;
        if ($on === null) {
            $on = defined('XHPROF_ON') && XHPROF_ON;
        }
        self::$_on = $on;
        if (self::$_on) {//启动xhprof
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }
    }

    /**
     * 结束分析
     * @param string $param1 额外记录信息1
     * @param string $param2 额外记录信息2
     * @param mixed $severID 指定服记录日志
     */
    public static function end($param1, $param2, $severID = null)
    {
        $time = microtime(true) - self::$_start_time;
        if (self::$_on && $time >= self::$_time_step) {
            //停止xhprof
            $xhprof_data = xhprof_disable();
            include_once PUBLIC_DIR . "/xhprof/xhprof_lib/utils/xhprof_lib.php";
            include_once PUBLIC_DIR . "/xhprof/xhprof_lib/utils/xhprof_runs.php";

            //保存统计数据，生成统计ID和source名称
            $xhprof_runs = new XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo"); //source名称是xhprof_foo

            //弹出一个统计窗口，查看统计信息
            $cache = $severID === null ? Common::getMyMem() : Common::getCacheBySevId($severID);
            $xhprof_info = $cache->get(self::$_key);
            if (!$xhprof_info) {
                $xhprof_info = array();
            }

            $id_suffix = "_" . date("Y-m-d H:i:s");
            $url_this = round($time, 2) . '-' . $param1 . '-' . $param2;

            $url = "http://{$_SERVER['SERVER_NAME']}/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_foo";
            $xhprof_info[$run_id . $id_suffix] = '<a href="' . $url . '" target="_blank">' . $url_this . '</a>';
            $cache->set(self::$_key, $xhprof_info);
        }
    }
}

class SevIdCfgObj
{
    protected $_cfg = array();

    public function __construct($cfg)
    {
        $this->_cfg = $cfg;
    }

    public function getHE()
    {
        return $this->_cfg['he'];
    }

    public function getKUA()
    {
        return $this->_cfg['kua'];
    }

    public function getMY()
    {
        return $this->_cfg['sevid'];
    }
}

class Common
{
    public static $db = array();

    // 加载语言包
    public static function loadLang($filename = 'language', $lang = null)
    {
        if (empty($lang)) {
            if (defined("DEFAULT_LANG")) {
                $lang = DEFAULT_LANG;
            } else {
                $lang = 'zh';
            }
        }
        require_once self::lang_path($filename, $lang);
    }

    //返回语言包
    public static function getLang($filename = 'language', $lang = null)
    {
        if (empty($lang)) {
            if (defined("DEFAULT_LANG")) {
                $lang = DEFAULT_LANG;
            } else {
                $lang = 'zh';
            }
        }
        return require(self::lang_path($filename, $lang));
    }

    //生成语言包路径
    public static function lang_path($filename = 'language', $lang = 'zh')
    {
        $langFile = sprintf('%s/%s.php', strtolower(trim($lang)), $filename);
        if (!file_exists(ROOT_DIR . '/lang/' . $langFile)) {
            $langFile = sprintf('%s/%s.php', 'zh', $filename);
        }
        return ROOT_DIR . '/lang/' . $langFile;
    }

    /**
     * 去掉反斜杠
     *
     * @param array $var
     * @return array
     */
    public static function prepareGPCData(&$var)
    {
        if (is_array($var)) {
            while (list($key, $val) = each($var)) {
                $var[$key] = self::prepareGPCData($val);
            }
        } else {
            $var = stripslashes($var);
        }

        return $var;
    }

    /**
     * 获取用户平台ID
     *
     * @param string $uid 平台ID
     */
    public static function getOpenid($uid)
    {
        $key = $uid . '_openid';
        $cache = Common::getCacheByUid($uid);
        $openid = $cache->get($key);
        if ($openid == false) {
            $db = Common::getDbeByUid($uid);
            $sql = "select * from gm_sharding where uid = '{$uid}'";
            $row = $db->fetchRow($sql);
            $openid = $row['ustr'];
            $cache->set($key, $openid);
        }
        return $openid;
    }

    /**
     * 根据openid获取uid
     * @param $openid
     * @return array
     */
    public static function getUidByOpenid($openid)
    {
        $key = $openid . '_ustr';
        $cache = Common::getMyMem();
        $sharding = $cache->get($key);
        if ($sharding == false) {
            $sharding = self::getUidForDb($openid);
            if (!empty($sharding)) {
                $cache->set($key, $sharding);
            }
        }
        return $sharding;
    }


    public function getUidForDb($openid)
    {
        $sql = "select * from `gm_sharding` where `ustr` = '{$openid}' limit 1";
        $db = Common::getMyDb();
        $sharding = $db->fetchRow($sql);

        return $sharding;
    }

    /*
	 * 名字长度截取
	 */
    public static function utf8_substr($key = 'Config')
    {
        //name
    }

    /*
	 * 字符串长度判定
	 */
    public static function utf8_strlen($string = null)
    {
        // 将字符串分解为单元
        preg_match_all("/./us", $string, $match);
        // 返回单元个数
        return count($match[0]);
    }

    /**
     * 获取系统配置信息
     * @param mixed $key
     * @return array|bool
     */
    public static function getConfig($key)
    {
        if (empty($key)) {
            Master::error('getConfig_err_emptykey');
        }
        static $config = array();
        if (empty($config[$key])) {
            if (!file_exists(CONFIG_DIR . "/{$key}.php")) {
                Master::error('getConfig_err_file:' . CONFIG_DIR . "/{$key}.php");
                return false;
            }
            $config[$key] = require(CONFIG_DIR . "/{$key}.php");
        }
        return $config[$key];
    }


    /*
	 * 获取分库数量
	 */
    public static function get_table_div($sevid = 0, $type = 'game')
    {
        if ($sevid == 0) {
            $SevidCfg = self::getSevidCfg();
            $sevid = $SevidCfg['sevid'];
        }
        $config = self::getConfig(GAME_MARK . "/AllServerDbConfig");
        return $config[$sevid][$type]['table_div'];
    }


    /**
     * 获取管理后台配置信息
     * @param string $key
     * @return array|bool
     */
    public static function getConfigAdmin($key = 'Config')
    {
        static $config;
        if (empty($config[$key])) {
            if (!file_exists(CONFIG_ADM_DIR . "/{$key}.php"))
                return false;

            $config[$key] = require(CONFIG_ADM_DIR . "/{$key}.php");
        }
        return $config[$key];
    }


    /**
     * 获取余数
     * @param int $uid
     * @return int
     */
    public static function computeTableId($uid)
    {
        static $table_id = array();
        if (empty($table_id[$uid])) {
            $table_div = Common::get_table_div();
            $table_id[$uid] = str_pad($uid % $table_div, 2, '0', STR_PAD_LEFT);
        }
        return $table_id[$uid];
    }

    /**
     * 加载Model
     */
    public static function loadModel($name)
    {
        $path = MOD_DIR . '/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载api Model
     */
    public static function loadApiModel($name)
    {
        $path = API_DIR . '/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载act Model
     */
    public static function loadActModel($name)
    {
        $path = MOD_DIR . '/act/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载Combine
     */
    public static function loadCombineModel($name)
    {
        $path = COMBINE_DIR . '/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载redis  Model
     */
    public static function loadRedisModel($name)
    {
        $path = MOD_DIR . '/redis/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载sev  Model
     */
    public static function loadSevModel($name)
    {
        $path = MOD_DIR . '/sevact/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载voCom  Model
     */
    public static function loadVoComModel($name)
    {
        $path = MOD_DIR . '/com/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载lock  Model
     */
    public static function loadLockModel($name)
    {
        $path = MOD_DIR . '/lock/' . $name . '.php';
        require_once($path);
    }

    /**
     * chat  Model
     */
    public static function loadChatModel($name)
    {
        $path = MOD_DIR . '/chat/' . $name . '.php';
        require_once($path);
    }

    /**
     * 加载核心类文件
     *
     * @param string $name
     */
    public static function loadLib($name)
    {
        $path = LIB_DIR . '/' . $name . '.php';
        require_once($path);
    }

    //服务器ID 和 数据库 / 缓存的获取函数-----------------
    /*
	 * 获取各种服务器ID
	 */
    public static function getSevidCfg($in_sevId = null)
    {
        static $sevid_cfg = array();
        //如果已经存在 并且不重设 则返回存在值
        if (!empty($sevid_cfg) && empty($in_sevId)) {
            return $sevid_cfg;
        } elseif (!empty($in_sevId)) {//如果传入了sevid 则重新设置
            //获取服务器ID数据
            $SevIdCfg = self::getConfig(GAME_MARK . "/SevIdCfg");
            $sevid_cfg = $SevIdCfg[$in_sevId];
            $sevid_cfg['sevid'] = $in_sevId;
            if (empty($sevid_cfg)) {
                Master::error("in_sevId_err_" . $in_sevId);
            }
            return $sevid_cfg;
        } else {
            //如果未设置  并且没有传入 报错
            Master::error('getSevidCfg_null');
        }
    }

    /**
     * 通过$sevID获取服务器信息
     * @param $sevID
     * @return SevIdCfgObj
     */
    public static function getSevCfgObj($sevID)
    {
        static $serIDCfg = array();
        if (empty($serIDCfg[$sevID])) {
            //获取服务器ID数据
            $SevIdCfg = self::getConfig(GAME_MARK . "/SevIdCfg");
            $sevid_cfg = $SevIdCfg[$sevID];
            $sevid_cfg['sevid'] = $sevID;
            if (empty($sevid_cfg)) {
                Master::error("sevID_err_" . $sevID);
            }
            $serIDCfg[$sevID] = new SevIdCfgObj($sevid_cfg);
        }
        return $serIDCfg[$sevID];
    }

    /*
	 * 根据服务器UID连接数据库
	 */
    public static function getDbeByUid($uid, $type = 'game')
    {
        return self::getDbBySevId(Game::get_sevid($uid), $type);
    }

    /**
     * 根据服务器ID连接数据库
     * @return Db
     */
    public static function getDbBySevId($sevId, $type = 'game')
    {
        static $db = array();
        if (empty($db[$sevId][$type])) {
            $config = self::getConfig(GAME_MARK . "/AllServerDbConfig");
            self::loadLib('Db');
            $db[$sevId][$type] = new Db($config[$sevId][$type]);
        }
        return $db[$sevId][$type];
    }

    /*
     * 加载通服DB
     */
    public static function getComDb($type = 'game')
    {
        Common::loadModel("ServerModel");
        $com = ServerModel::getDefaultServerId();
        return self::getDbBySevId($com, $type);
    }

    /*
	 * 加载合服DB
	 */
    public static function getDftDb($type = 'game')
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getDbBySevId($SevidCfg['he'], $type);
    }

    /*
	 * 加载跨服DB
	 */
    public static function getKuaDb()
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getDbBySevId($SevidCfg['kua']);
    }

    /*
	 * 加载本服数据库
	 */
    public static function getMyDb($type = 'game')
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getDbBySevId($SevidCfg['sevid'], $type);
    }

    /**
     * 根据服务器UID连接缓存
     * @return MemcachedClass
     */
    public static function getCacheByUid($uid)
    {
        return self::getCacheBySevId(Game::get_sevid($uid));
    }

    /*
	 * 根据服务器ID获取缓存接口
	 */
    public static function getCacheBySevId($sevId)
    {
        static $cache = array();
        if (empty($cache[$sevId])) {
            $config = self::getConfig(GAME_MARK . "/AllServerMemConfig");
            $cache[$sevId] = new MemcachedClass($config[$sevId]);

        }
        return $cache[$sevId];
    }

    /**
     * 跟进服务器编号获取历史缓存
     * @param $sevId
     * @return MemcachedClass
     */
    public static function getHistoryCacheBySevId($sevId)
    {
        static $cacheHis = array();
        if (empty($cacheHis[$sevId])) {
            $config = self::getConfig(GAME_MARK . "/AllServerHistoryMemConfig");
            $cacheHis[$sevId] = new MemcachedClass($config[$sevId]);

        }
        return $cacheHis[$sevId];
    }

    /*
	 * 加载本服缓存
	 */
    public static function getMyMem()
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getCacheBySevId($SevidCfg['sevid']);
    }

    /*
	 * 加载合服缓存
	 */
    public static function getDftMem()
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getCacheBySevId($SevidCfg['he']);
    }

    /*
     * 加载跨服缓存
     */
    public static function getComMem()
    {
        Common::loadModel("ServerModel");
        $com = ServerModel::getDefaultServerId();
        return self::getCacheBySevId($com);
    }

    /*
     * 加载跨服缓存
     */
    public static function getKuaMem()
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getCacheBySevId($SevidCfg['kua']);
    }


    /*
	 * 根据服务器ID连Redis
	 */
    public static function getRedisBySevId($sevId)
    {
        static $rds = array();
        if (empty($rds[$sevId])) {
            $config = self::getConfig(GAME_MARK . "/AllServerRedisConfig");
            require_once LIB_DIR . '/RedisClass.php';
            $redis = new RedisClass();
            $redis->connect($config[$sevId]['host'], $config[$sevId]['port']);
            $redis->auth($config[$sevId]['pass']);
            if (!empty($config[$sevId]['preKey'])) {
                $redis->setOption(Redis::OPT_PREFIX, $config[$sevId]['preKey'] . '_');
            }
            $rds[$sevId] = $redis;
        }
        return $rds[$sevId];
    }

    /*
	 * 根据服务器ID连Redis
	 */
    public static function getRedisNoPrekeyBySevId($sevId)
    {
        static $rds = array();
        if (empty($rds[$sevId])) {
            $config = self::getConfig(GAME_MARK . "/AllServerRedisConfig");
            $redis = new Redis();
            $redis->connect($config[$sevId]['host'], $config[$sevId]['port']);
            $redis->auth($config[$sevId]['pass']);
            $rds[$sevId] = $redis;
        }
        return $rds[$sevId];
    }

    /*
	 * 关闭所有打开的数据库连接
	 */
    public static function closeDb()
    {
        foreach (self::$db as $v) {
            $v->close();
        }
        self::$db = array();
    }

    /*
     * 加载合服rds
     */
    public static function getDftRedis()
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getRedisBySevId($SevidCfg['he']);
    }

    /*
     * 加载合服rds
     */
    public static function getKuaRedis()
    {
        $SevidCfg = Common::getSevidCfg();
        return self::getRedisBySevId($SevidCfg['kua']);
    }

    /**
     * 加载全服rds
     * @return RedisClass
     */
    public static function getComRedis()
    {
        Common::loadModel("ServerModel");
        $com = ServerModel::getDefaultServerId();
        return self::getRedisBySevId($com);
    }

    /**
     * 验证用户登陆状态
     */
    public static function checkLogin()
    {
        if (empty($_SESSION['admin'])) {
            /*if($_GET['sig'] != 'b7d4c3ba403b08844d515d330f92')
	    	{
	    		if(!empty($_COOKIE['USPS']))
	    		{
	    			Common::loadLib('Encrypt');
	    			$encrypt=new Encrypt();
	    			$USPS = $encrypt->decrypt($_COOKIE['USPS'], 'youdongwangluo');
	    			$USPS = explode('@',$USPS);
	    			if(!empty($_COOKIE['admin']) && !empty($USPS[0]) && $USPS[0] == $_COOKIE['admin'])
	    			{
	    				$_SESSION['admin'] = $_COOKIE['admin'];
	    				return true;
	    			}
	    		}
	    	}else{
	    		$_SESSION['admin'] = 1;
	    		return true;
	    	}*/
            echo "<script>alert('11111111111111');</script>";
            header("HTTP/1.1 404 Not Found");
            exit;
        }
    }

    /**
     * 获取sharding
     *
     * @param string $ustr 平台ID
     * $flag true:uid和ustr都相同   false:uid和ustr不相同   默认true
     * @return array
     */
    public static function getUid($ustr, $flag = false)
    {
        static $uids = array();
        if (empty($uids[$ustr])) {
            $key = $ustr . '_ustr';
            $cache = Common::getMyMem();
            $sharding = $cache->get($key);

            if ($sharding == false) {
                $sharding = self::getUidForCache($ustr, $flag);
                $cache->set($key, $sharding);
            }
            $uids[$ustr] = $sharding;
        }

        return $uids[$ustr];
    }


    /**
     * 从DB中获取sharding信息
     *
     * @param array $ustr
     */
    public static function getUidForCache($ustr, $flag)
    {
        $sql = "select * from `gm_sharding` where `ustr` = '{$ustr}' limit 1";
        $db = Common::getMyDb();
        $sharding = $db->fetchRow($sql);

        if (!$sharding)
            $sharding = self::createUidForUstr($ustr, $flag);

        return $sharding;
    }

    /**
     * 通过ustr创建适配uid
     */
    public static function createUidForUstr($ustr, $flag)
    {
        $sharding_rand = Common::getConfig('ShardingRand');
        shuffle($sharding_rand);
        $sharding_id = $sharding_rand[0];

        if ($flag) {
            $sql = "insert into `gm_sharding` set `uid`='{$ustr}', `ustr` ='{$ustr}',`sharding_id`='{$sharding_id}'";
        } else {
            $sql = "insert into `gm_sharding` set `ustr` ='{$ustr}',`sharding_id`='{$sharding_id}'";
        }

        $db = Common::getMyDb();
        $db->query($sql);
        $uid = $db->insertId();
        if ($uid < 10086) {
            exit('sever_error_gm_sharding');
        }

        $sharding = array();
        $sharding['uid'] = $uid;
        $sharding['sharding_id'] = $sharding_id;
        $sharding['ustr'] = $ustr;

        return $sharding;
    }

    /**
     * 获取用户sharding信息
     *
     * @param int $uid
     * @return array
     */
    public static function getSharding($uid)
    {
        static $static = array();
        if (empty($static[$uid])) {
            $key = $uid . '_sharding';
            $cache = Common::getMyMem();
            $sharding = $cache->get($key);
            if ($sharding == false) {
                $sharding = self::getShardingForCache($uid);
                $cache->set($key, $sharding);
            }
            $static[$uid] = $sharding;
        }
        return $static[$uid];
    }

    public static function getShardingFromUstr($ustr)
    {
        static $static = array();
        if (empty($static[$ustr])) {
            $key = $ustr . '_ustr';
            $cache = Common::getMyMem();
            $sharding = $cache->get($key);
            if ($sharding == false) {
                $sharding = self::getShardingFromUstrForCache($ustr);
                $cache->set($key, $sharding);
            }
            $static[$ustr] = $sharding;
        }
        return $static[$ustr];
    }

    /**
     * 从db获取用户sharding
     *
     * @param int $uid
     * @return array
     */
    public static function getShardingForCache($uid)
    {
        $db = Common::getMyDb();
        $sql = "select * from `gm_sharding` where `uid`='$uid'";
        $sharding = $db->fetchRow($sql);

        return $sharding;
    }

    public static function getShardingFromUstrForCache($ustr)
    {
        $db = Common::getMyDb();
        $sql = "select * from `gm_sharding` where `ustr`='$ustr'";
        $sharding = $db->fetchRow($sql);

        return $sharding;
    }

    /**
     * 获取token
     *
     * @param int $uid
     * @return string
     */
    public static function getToken($uid)
    {
        $key = $uid . '_token';
        $cache = Common::getMyMem();
        $token = $cache->get($key);
        return $token['token'];
    }

    /**
     * 设置token
     *
     * @param int $uid
     * @return string / false设置失败 时间过短
     */
    public static function setToken($uid)
    {
        //时间判断
        $key = $uid . '_token';
        $cache = Common::getMyMem();
        $token = $cache->get($key);
        if (isset($token)
            && abs($_SERVER['REQUEST_TIME'] - $token['time']) <= 2) {//0秒内 不能重登 暂时关闭时间限制
            return false;
        } else {
            $token = array(
                'token' => md5(uniqid() . $uid . $_SERVER['REQUEST_TIME']),
                'time' => $_SERVER['REQUEST_TIME'],
            );
            $cache->set($key, $token);
            return $token['token'];
        }
    }

    public static function setSig($uid)
    {
        ksort($_POST);
        $str = '';
        foreach ($_POST as $key => $value) {
            if ($key == 'sig') continue;
            $value = stripslashes($value);
            $str .= "{$key}={$value}&";
        }
        $str = substr($str, 0, -1);

        $token = self::getToken($uid);

        $str .= $token;
        $str .= 'RnQmN8i2UH82kncscrnx';

        return md5($str);
    }

    /*
     * 输出请求和返回信息 到文件
     */
    public static function p_debug($str)
    {
        return;
        $sre_in = "\n\ntime -- " . date('Y-m-d H:i:s') . "\n";
        //$sre_in .= 'url  -- '.print_r($_SERVER['REQUEST_URI'],1);
        //$sre_in .= 'GET  -- '.print_r($_GET,1);
        //$sre_in .= 'POST -- '.print_r($_POST,1);
        $sre_in .= $str;
        if (isset($_GET['debug'])) {
            echo $sre_in;
        } else {
            file_put_contents('/tmp/renzhe_debug2', $sre_in, FILE_APPEND);
        }
    }

    /*
	 * 数值合法化
	 */
    public static function get_int($val)
    {
        return abs(intval($val));
    }

    /*
	 * 获取IP
	 */
    public static function GetIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $cip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $cip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } else {
            $cip = 'Unknown' . Game::get_now();
        }
        return $cip;
    }

    /**
     * ip限制专用的获取ip
     * @return string
     */
    public static function getIPSimple()
    {
        $ip = Common::GetIP();
        //第一步 截取第一个ip
        $ips = explode(',', $ip);
        //第二步 去空格
        $ip = trim($ips[0]);
        return $ip;
    }

    /*
	 * testuser
	 * 测试用户 测试IP信息
	 */
    public static function istestuser($findDb = false)
    {
        /* $ips = array(
	        '58.246.0.50',
            '58.246.5.18',
		);*/
        $ips = include(ROOT_DIR . '/config/test_ip_new.php');
        if (empty($ips)) {
            $ips = array();
        }
        if (in_array(self::getIPSimple(), $ips)) {
            return true;
        } else {
            if ($findDb) {
                //是否测试IP
                $test_ip = Game::get_peizhi('test_ip');
                if (is_array($test_ip)) {
                    if (is_array($test_ip) && in_array(self::getIPSimple(), $test_ip)) {
                        return true;
                    }
                }
            }

        }

        return false;
    }

    /**
     * 判断恶意请求来源，为true不接受处理
     * @return bool
     */
    public static function isFromHacker()
    {
        return !empty($_SERVER['HTTP_USER_AGENT'])
            && stripos($_SERVER['HTTP_USER_AGENT'], 'python-requests') !== false;
    }

    public static function log($fileName, $content)
    {
        if (!defined(FILE_PATH)) {
            define(FILE_PATH, '/tmp/');
        }
        $fileName = FILE_PATH . $fileName;
        if (!file_exists($fileName)) {
            $com = "touch $fileName";
            @exec($com);
            $com = "chmod 777 $fileName";
            @exec($com);
        }
//        print_r($fileName);
//        print_r($content);
        file_put_contents($fileName, $content, FILE_APPEND);
    }

    // 记录日志
    public static function logMsg($logname, $log, $is_file_append = true)
    {
        if (false == Common::createFolders(dirname($logname))) {
            file_put_contents('/tmp/' . strtr(basename(__FILE__), array('.' => '_')) . date('Ymd') . '.log', 'log error:' . $logname, FILE_APPEND);
            return;// 无法构建的情况下跳过
        }
        $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $log . PHP_EOL);
        if ($is_file_append) {
            file_put_contents($logname, $log, FILE_APPEND);
        } else {
            file_put_contents($logname, $log);
        }
    }

    // 记录咸鱼日志
    public static function logXianYuMsg($logname, $log, $is_file_append = true)
    {
        if (false == Common::createFolders(dirname($logname))) {
            file_put_contents('/tmp/' . strtr(basename(__FILE__), array('.' => '_')) . date('Ymd') . '.log', 'log error:' . $logname, FILE_APPEND);
            return;// 无法构建的情况下跳过
        }
        $log .= PHP_EOL;
        if ($is_file_append) {
            file_put_contents($logname, $log, FILE_APPEND);
        } else {
            file_put_contents($logname, $log);
        }
    }

    // 判断目录是否存在，不存在就多级构建目录
    public static function createFolders($dir)
    {
        return is_dir($dir) or (Common::createFolders(dirname($dir)) and mkdir($dir, 0777) and chmod($dir, 0777));
    }

    // 通信请求
    public static function request($url, $params = '', $mode = 'POST', $needHeader = false, $timeout = 8)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curlHandle, CURLOPT_USERAGENT, 'MSDK_PHP_v0.0.3(20131010)');

        if ($needHeader) {
            curl_setopt($curlHandle, CURLOPT_HEADER, true);
        }

        if (strtoupper($mode) == 'POST') {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($curlHandle, CURLOPT_POST, true);
            if (is_array($params)) {
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($params));
            } else {
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $params);
            }
        } else {
            if (is_array($params)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
            } else {
                $url .= (strpos($url, '?') === false ? '?' : '&') . $params;
            }
        }
        curl_setopt($curlHandle, CURLOPT_URL, $url);

        $result = curl_exec($curlHandle);

        if ($needHeader) {
            $tmp = $result;
            $result = array();
            $info = curl_getinfo($curlHandle);
            $result['header'] = substr($tmp, 0, $info['header_size']);
            $result['body'] = trim(substr($tmp, $info['header_size']));  //直接从header之后开始截取，因为 1.body可能为空   2.下载可能不全
            //$info['download_content_length'] > 0 ? substr($tmp, -$info['download_content_length']) : '';
        }
        $errno = curl_errno($curlHandle);
        if ($errno) {
            $result = $errno;
        }
        curl_close($curlHandle);
        return $result;
    }

    // 通信请求 单get
    public static function requestByGet($url, $params, $timeout = 8)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $url .= $params;
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        $result = curl_exec($curlHandle);
        $errno = curl_errno($curlHandle);
        if ($errno) {
            $result = $errno;
        }
        curl_close($curlHandle);
        return $result;
    }

    public static function requestHTTPS($url, $params = '', $mode = 'POST', $needHeader = false, $timeout = 8)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curlHandle, CURLOPT_USERAGENT, 'MSDK_PHP_v0.0.3(20131010)');

        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);


        if ($needHeader) {
            curl_setopt($curlHandle, CURLOPT_HEADER, true);
        }

        if (strtoupper($mode) == 'POST') {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($curlHandle, CURLOPT_POST, true);
            if (is_array($params)) {
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($params));
            } else {
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $params);
            }
        } else {
            if (is_array($params)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
            } else {
                $url .= (strpos($url, '?') === false ? '?' : '&') . $params;
            }
        }
        curl_setopt($curlHandle, CURLOPT_URL, $url);

        $result = curl_exec($curlHandle);

        if ($needHeader) {
            $tmp = $result;
            $result = array();
            $info = curl_getinfo($curlHandle);
            $result['header'] = substr($tmp, 0, $info['header_size']);
            $result['body'] = trim(substr($tmp, $info['header_size']));  //直接从header之后开始截取，因为 1.body可能为空   2.下载可能不全
            //$info['download_content_length'] > 0 ? substr($tmp, -$info['download_content_length']) : '';
        }
        $errno = curl_errno($curlHandle);
        if ($errno) {
            $result = $errno;
        }
        curl_close($curlHandle);
        return $result;
    }

    /*
	 * 导出xls文件
	 * $dataArray: 数据记录，格式如下
	 * $dataArray[tindex][rindex] = rowdata
	 * tindex : 工作表索引，从0开始
	 * rindex ： 对应工作表中的行索引，从0开始
	 * rowdata: 对应行的列记录，一维数组保存
	 * 例如：
	 * $dataArray[0][0] = array('xxx', 'sss') : excle文件中会有一个工作表，表中第一行只有两列，值为xxx和sss
	 *
	 */
    public static function exportExcel($dataArray)
    {
        Common::loadLib('PHPExcel/Classes/PHPExcel');
        if (!class_exists('PHPExcel')) {
            exit('PHPExcel class not exist');
        }
        $objPHPExcel = new PHPExcel();

        // 设置文件属性
        $objPHPExcel->getProperties()
            ->setCreator('baba')
            ->setLastModifiedBy('baba')
            ->setTitle('baba')
            ->setSubject('baba')
            ->setDescription('baba')
            ->setKeywords('baba')
            ->setCategory('baba');

        if (is_array($dataArray)) {
            ini_set('memory_limit', '255M');
            // 设置当前的sheet索引，用于后续的内容操作。
            // 一般只有在使用多个sheet的时候才需要显示调用。
            // 缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0
            $objPHPExcel->setActiveSheetIndex(0);
            $index = 0;
            foreach ($dataArray as $k => $v) {
                if (0 < $index) {
                    $objPHPExcel->createSheet();// 创建工作表
                }
                // 添加表记录
                $objPHPExcel->setActiveSheetIndex($index)->fromArray($v);

                // 设置过滤器
                $objPHPExcel->setActiveSheetIndex($index)->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

                $index++;
            }
            // 设置默认显示在excel的第一张表
            $objPHPExcel->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Excel5)
            $tmpFileName = date('YmdHis') . '.xls';
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"{$tmpFileName}\"");
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            // Save Excel 95 file
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }

        return;
    }

    /**
     * 是否是https请求
     * @return bool
     */
    public static function isHttps()
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
            ? true : false;
    }

    /**
     * 检验账号
     * @param $account
     * @param $pwd
     * @param int $is_new
     * @return int
     */
    public static function checkAccount($account, $pwd, $is_new = 0)
    {
        Common::loadModel('UserAccountModel');
        $UserAccountModel = new UserAccountModel($account);
        return $UserAccountModel->check($pwd, $is_new);
    }

    public static $codeArray = array(
        array('3', '1', '5', '8', '9', '7', '4', '2', '0', '6'),
        array('0', '5', '3', '2', '1', '7', '9', '4', '8', '6'),
        array('1', '0', '6', '7', '3', '8', '2', '5', '4', '9'),
        array('6', '1', '5', '4', '2', '9', '0', '3', '8', '7'),
        array('7', '6', '0', '2', '5', '8', '1', '4', '9', '3'),
        array('6', '5', '3', '4', '0', '2', '8', '1', '7', '9'),
        array('9', '6', '1', '4', '0', '5', '3', '2', '8', '7'),
        array('8', '9', '3', '1', '5', '7', '0', '6', '4', '2'),
        array('6', '2', '4', '9', '1', '5', '3', '8', '0', '7'),
    );
    public static $lastCode = array();

    public static function getHttpJson($str)
    {
        $lstr = strlen($str);

        if ($str[$lstr - 1] == '}' && $str[0] == '{' && $str[$lstr - 14] != '#') {
            $param = json_decode($str, 1);
//            if (empty($param['user']['adok']) && empty($param['login']['loginAccount'])){
//                Master::error(LOGIN_SERVER_DELAY_ENTER_ERROR);
//            }
            return $param;
        }

        if ($str[$lstr - 14] == '#') {
            $code = substr($str, -13);
            $context = substr($str, 0, -14);
            $codeLength = strlen($code);
            $contextLength = strlen($context);
            $randomL = intval($code[2]);
            $isUrlEncode = intval($code[1]);
            $randomYu = intval($code[0]);

            $time = "";
            for ($i = 3; $i < $codeLength; $i++) {
                $index = intval($code[$i]);
                $time = $time . self::$codeArray[$randomL][$index];
            }

            if (Game::is_over(intval($time) + 30)) {
                Master::error(LOGIN_SERVER_DELAY_ENTER_ERROR);
            }

            $row = floor($contextLength / $randomYu);
            $remain = $contextLength - $randomYu * $row;
            $curRow = 0;
            $curLine = 0;
            $remainCount = 0;
            $s = "";

            for ($i = 0; $i < $contextLength; $i++) {
                $s = $s . $context[$curRow * $row + $curLine + $remainCount];
                $remainCount += $curRow < $remain ? 1 : 0;
                $curRow++;
                if ($curRow * $row + $curLine + $remainCount >= $contextLength) {
                    $curLine++;
                    $curRow = 0;
                    $remainCount = 0;
                }
            }

            if ($isUrlEncode == 1) {
                $s = urldecode($s);
            }
            return json_decode($s, 1);
        }

        return null;
    }
}

//游戏计算类
class Game
{
    /*
	 * 修改返回状态
	 */
    public static function bak_type($type)
    {
        global $bak_s_type;
        $bak_s_type = $type;
    }

    /*
	 * 参数安全化 int类型
	 */
    public static function intval($params, $type)
    {
        if (!isset($params[$type])) {
            return 0;
        } else {
            return $params[$type];
        }
    }

    /*
	 * 参数安全化 字符串类型
	 */
    public static function strval($params, $type)
    {
        if (!isset($params[$type])) {
            return '';
        } else {
            //其他安全化操作 / 去空格 引号 斜杠?
            return trim($params[$type]);
        }
    }

    /*
	 * 参数安全化 array类型
	 */
    public static function arrayval($params, $type)
    {
        if (!isset($params[$type]) || !is_array($params[$type])) {
            return array();
        } else {
            return $params[$type];
        }
    }

    /*
	 * 根据资质 计算出属性
	 */
    public static function zzlvep($zzarr, $level)
    {
        $ep = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
        foreach ($zzarr as $e => $v) {
            //((门客等级*门客等级+门客等级)/10+9.8)*总资质   （已废弃）
            //$ep[$e] += floor((($level*$level+$level)/10+9.8)*$v);
            $ep[$e] += floor((10 + $level - 1) * $v);     // 资质属性公式改成 10 + 等级 -1
        }
        return $ep;
    }

    public static function filterep($ep1)
    {
        $er = array();
        foreach ($ep1 as $k => $v) {
            if ($k == 'e1'
                || $k == 'e2'
                || $k == 'e3'
                || $k == 'e4') {
                $er[$k] = $v;
            }
        }
        return $er;
    }

    /*
	 * 属性相加
	 */
    public static function epadd($ep1, $ep2)
    {
        foreach ($ep1 as $k => $v) {
            switch ($k) {
                case 'e1':
                    $k = 1;
                    break;
                case 'e2':
                    $k = 2;
                    break;
                case 'e3':
                    $k = 3;
                    break;
                case 'e4':
                    $k = 4;
                    break;
            }
            if (isset($ep2[$k])) {
                $ep2[$k] += $v;
            } else {
                $ep2[$k] = $v;
            }
        }
        return $ep2;
    }

    /*
     * 属性相减
     */
    public static function epminus($ep1, $ep2)
    {
        foreach ($ep2 as $k => $v) {
            switch ($k) {
                case 1:
                    $k = 'e1';
                    break;
                case 2:
                    $k = 'e2';
                    break;
                case 3:
                    $k = 'e3';
                    break;
                case 4:
                    $k = 'e4';
                    break;
            }
            if (isset($ep1[$k])) {
                $ep1[$k] -= $v;
            } else {
                $ep1[$k] = $v;
            }
        }
        return $ep1;
    }

    /*
     * 属性相加版本2
     */
    public static function epaddr1($ep1, $ep2)
    {
        foreach ($ep2 as $k => $v) {
            if (isset($ep1[$k])) {
                $ep1[$k] += $v;
            } else {
                $ep1[$k] = $v;
            }
        }
        return $ep1;
    }

    /*
     * 属性乘一个数
     */
    public static function epmultiply($ep, $number)
    {
        foreach ($ep as $k => $v) {
            if (!empty($ep[$k])) {
                $ep[$k] *= $number;
            } else {
                $ep[$k] = $v;
            }
        }
        return $ep;
    }

    /*
     * 属性相乘
     */
    public static function epmultiply_arr($ep1, $ep2, $rounding = 'ceil')
    {
        foreach ($ep1 as $k => $v) {
            if (!empty($ep1[$k])) {
                if (!isset($ep2[$k])) $ep2[$k] = 0;
                switch ($rounding) {
                    case 'ceil':
                        $ep1[$k] = ceil($ep1[$k] * $ep2[$k]);
                        break;
                    case 'floor':
                        $ep1[$k] = floor($ep1[$k] * $ep2[$k]);
                        break;
                    case 'round':
                        $ep1[$k] = round($ep1[$k] * $ep2[$k]);
                        break;
                }
            } else {
                $ep1[$k] = $v;
            }
        }
        return $ep1;
    }

    /*
	 * 亲家拜访势力加成
	 * $honor : 孩子官阶
	 * $qjlove : 亲家拜访亲密度
	 */
    public static function qjepadd($honor, $qjlove)
    {
        //亲家有效好感度为100
        $qjlove = min($qjlove, 100);

        $beishu = 1;
        switch ($honor) {
            case 4 :
            case 5 :  //进士 探花 +2(全属性)
                $beishu = 2;
                break;
            case 6 :
            case 7 : //榜眼 状元 +3(全属性)
                $beishu = 3;
                break;
        }

        $ep3 = array(
            'e1' => 1 * $beishu * $qjlove,
            'e2' => 1 * $beishu * $qjlove,
            'e3' => 1 * $beishu * $qjlove,
            'e4' => 1 * $beishu * $qjlove,
        );
        return $ep3;
    }


    /*
	 * 吧四项属性 格式化为输出样式
	 */
    public static function fmt_ep($ep)
    {
        $data = array();
        foreach ($ep as $k => $v) {
            $data['e' . $k] = $v;
        }
        return $data;
    }

    /*
	 * 吧输出格式的 四项属性 转换为数字四项属性
	 */
    public static function det_ep($ep)
    {
        $data = array(
            1 => $ep['e1'],
            2 => $ep['e2'],
            3 => $ep['e3'],
            4 => $ep['e4'],
        );
        return $data;
    }


    /*
	 * 获取用户锁
	 */
    public static function get_user_lock($uid)
    {
        return self::get_lock('uid_' . $uid);
    }

    /*
	 * 通用加锁 key,堵塞时间
	 */
    public static function get_lock($key, $time = 5)
    {
        //服务器ID
        $SevidCfg = Common::getSevidCfg();

        Common::loadModel("PHPLockModel");
        $Lock = new PHPLock(LOCK_PATH . GAME_MARK . '_lock/' . $SevidCfg['sevid'] . '/', $key);
        $Lock->startLock();

        //循环尝试加锁 (0.1 秒轮巡一次 共秒数*10 次)
        $times = $time * 10;

        $i = 0;
        do {
            $status = $Lock->Lock();//加锁
            if ($status) {
                break;
            }
            $i++;
            if ($i >= $times) {
                break;
            }
            usleep(100000);
        } while (1);

        //加锁失败 报错
        if (!$status) {
            return false;
        }
        return $Lock;
    }

    /*
	 * 返回时间戳是不是今天
	 */
    public static function is_today($time)
    {
        if (date('Ymd', $_SERVER['REQUEST_TIME']) == date('Ymd', $time)) {
            return true;
        } else {
            return false;
        }
    }

    /*
	 * 返回某个时间点的年月日
	 */
    public static function is_ymd($time)
    {
        if (empty($time)) {
            Master::error(PARAMS_ERROR);
        }
        return date('Ymd', $time);
    }

    /*
	 * 返回今天0点
	 */
    public static function day_0($hour = 0)
    {
        return strtotime(date('Y-m-d ' . $hour . ':00:00', $_SERVER['REQUEST_TIME']));
    }

    /*
	 * 返回时间戳过了几天
	 */
    public static function day_dur($time)
    {
        return ceil(($_SERVER['REQUEST_TIME'] - $time) / 86400);
    }

    /*
     * 返回今天0点
     */
    public static function get_W()
    {
        $wid = ceil(($_SERVER['REQUEST_TIME'] - strtotime(date('2018-03-05 00:00:00'))) / 604800);
        return $wid;
    }

    public static function get_week_0()
    {
        $weekday = date('w');
        $weekday = ($weekday + 6) % 7;
        return strtotime(date('Y-m-d 00:00:00', strtotime("-{$weekday} day")));
    }

    /**
     * 帮会战id
     * Sev52Model  帮会战-参战资格列表 23    45   601
     * Sev50Model  帮会战-匹配列表   23    45   601
     * Sev51Model  帮会战-参赛阵容   23    45   601
     */
    public static function club_pk_id()
    {
        $wid = ceil(($_SERVER['REQUEST_TIME'] - strtotime(date('2016-08-02 00:00:00'))) / 604800);
        $wday = date("w");//今天周几
        switch ($wday) {
            case 2:
            case 3:
                $wid = $wid * 1000 + 1;
                break;
            case 4:
            case 5:
                $wid = $wid * 1000 + 2;
                break;
            case 6:
            case 0:
            case 1:
                $wid = $wid * 1000 + 3;
                break;
            default:
                Master::error(SEV_54_XITONGMANG);
                break;
        }

        return $wid;
    }

    /**
     * 帮会战id
     * Sev56Model  帮会战-查看更多日志  34   560   12
     * Sev55Model  帮会战-奖励   34   560   12
     * Sev53Model  帮会战-对战日志  34   560   12
     * Sev57Model  帮会战-伤害排行  34    45   601
     */
    public static function club_pk_id1()
    {
        $wid = ceil(($_SERVER['REQUEST_TIME'] - strtotime(date('2016-08-03 00:00:00'))) / 604800);
        $wday = date("w");//今天周几
        switch ($wday) {
            case 3:
            case 4:
                $wid = $wid * 1000 + 1;
                break;
            case 5:
            case 6:
            case 0:
                $wid = $wid * 1000 + 2;
                break;
            case 1:
            case 2:
                $wid = $wid * 1000 + 3;
                break;
            default:
                Master::error(SEV_54_XITONGMANG);
                break;
        }

        return $wid;
    }

    /**
     * 是否在开启服务器范围内,返回主服
     */
    public static function club_pk_serv($servid)
    {

        $clubpk = Game::get_peizhi('clubpk');
        if (empty($clubpk['ksev'])) {
            return false;
        }
        foreach ($clubpk['ksev'] as $v) {
            if ($servid >= $v[0] && $servid <= $v[1]) {
                return $v[0];
            }
        }
        return false;
    }

    /*
	 * 某个时间点到了今天跨过几个0点
	 */
    public static function day_count($time)
    {
        //获取以前的0点时间戳
        $yiqian = strtotime(date('Y-m-d', $time));
        //获取当天0点时间戳
        $day_0 = self::day_0();
        //过了几天(跨过几个0点)
        return intval(($day_0 - $yiqian) / 86400);
    }

    /*
     * 根据UID获取sev ID
     */
    public static function get_sevid($uid)
    {
        if ($uid == 0) {
            if (defined('IS_TEST_SERVER') && IS_TEST_SERVER) {
                return 999;
            }
            return 1;
        }
        if ($uid < 1000000) {
            return 999;
        } else {
            return intval($uid / 1000000);
        }
    }

    /**
     * 根据$clubID获取服务器编号
     * @param $clubID
     * @return int
     */
    public static function get_sevid_club($clubID)
    {
        if ($clubID < 10000) {
            return 999;
        } else {
            return intval($clubID / 10000);
        }
    }

    /**
     * 功能官品限制
     * @param string $key 功能key
     * @param int $uid 玩家uid
     * @param int $level 玩家等级
     * @param int $tip 0:提示报错, 1不提示报错
     * @return int 1 : 被限制;    0 :未被限制     2: 后台未配置 使用默认
     */
    public static function is_limit_level($key, $uid, $level, $tip = 0)
    {

        $level_limit = Game::get_peizhi('level_limit');
        if (!empty($level_limit[$key])) {
            $svid = Game::get_sevid($uid);
            foreach ($level_limit[$key] as $v) {
                //如果在区服范围内
                if ($svid >= $v['s'] && $svid <= $v['e']) {
                    if ($level < $v['level']) {
                        if ($tip) {
                            return 1;
                        }
                        Master::error($v['tip']);
                    }
                    return 0;
                }
            }
            return 0;
        }
        return 2;

    }


    /*
	 * 是否打BOSS
	 * 传入当前小关ID 和 当前BOSS关ID
	 */
    public static function ispvb($smap, $bmap)
    {
        //if (ceil(($smap+1)/40) > $bmap){

        if ($smap == 0) {
            return false;
        }

        $smap_cfg = Game::getcfg_info('pve_smap', intval($smap));
        if ($smap_cfg['bmap'] < $bmap) {
            return false;
        }

        if ($smap_cfg['isboss'] == 1) {
            //打BOSS
            return true;
        } else {
            //打小关
            return false;
        }
    }

    /*
	 * 获取配置文件配置
	 */
    public static function getgamecfg($type)
    {
        //也不好  改为按时间更新
        // 总缓存  + 分支缓存
        static $config = null;
        if (empty($config[$type])) {
            $config[$type] = self::get_file_cash('gamecfg_' . $type);
            if (empty($config[$type])) {
                $cfg_file = require(CONFIG_DIR . '/game/cfg.php');
                $config[$type] = $cfg_file[$type];
                self::set_file_cash('gamecfg_' . $type, $config[$type]);
            }
        }
        return $config[$type];
    }

    /*
	 * 获取一个文件配置
	 */
    public static function get_file_cash($key)
    {
        $filename = 'filecash_' . $key;//文件名

        static $cfg_arr = array();
        if (isset ($cfg_arr[$filename])) {
            return $cfg_arr[$filename];
        }
        $require_file = CONFIG_DIR . '/game/cfg_val_format/' . $filename . '.php';//需要包含的文件
        if (file_exists($require_file)) {//存在的话
            return require($require_file);
        }
        return false;
    }

    /*
	 * 获取服务器配置
	 */
    public static function get_sev_cfg($key)
    {
        //获取服务器配置
    }

    /*
	 * 获取活动配置
	 */
    public static function get_hd_cfg($key)
    {
        //获取服务器配置 get_sev_cfg
        //循环遍历档次 给出当前生效档次
        //当前生效档次 时间字段改写为时间戳
        //缓存1分钟
    }


    /*
	 * 名字检查 & 插入名字索引表
	 * 检查名字是否可用 可用的话 插入数据库 正常返回
	 * 不可用 报错退出
	 */
    public static function chick_name($uid, $name)
    {
//		$db = Common::getMyDb();
        $db = Common::getDftDb();
        if (empty($db)) {
            Master::error('chick_name_dberr');
        }
        //加锁游戏功能锁..
        $index_name_lock = self::get_lock('chick_name');
        if (empty($index_name_lock)) {
            Master::error(NOTE_SYSTEM_BUSY . '_chick_name');
        }

        $success = false;
        $sql = "select * from  `index_name` where `name` = '{$name}'";
        $data = $db->fetchArray($sql);
        if (empty($data)) {
            $sql = "select * from `index_name` where `uid` = '{$uid}'";
            $data_uid = $db->fetchArray($sql);
            if (empty($data_uid)) {
                $sql = "INSERT INTO `index_name` VALUES ('{$name}', {$uid})";
            } else {
                $sql = "UPDATE `index_name` SET `name`='{$name}' where uid = '{$uid}'";
            }
            if ($db->query($sql)) {
                $success = true;
            } else {
                Master::error('chick_name_insert_err');
            }
        } else {
            Master::error(CLUB_NAME_USERED);
        }
        //解锁功能锁
        $index_name_lock->unlock();
        return $success;
    }

    /**
     * 检查联盟名字
     * @param $cid
     * @param $name
     */
    public static function check_club_name($name)
    {
        $SevCfg = Common::getSevidCfg();
        if (empty($SevCfg['ishe'])) {
            $db = Common::getMyDb();
            $sql = "select `cid` from `club`  where `name`='{$name}' ";
            $sql_data = $db->fetchRow($sql);
            if (!empty($sql_data)) {
                Master::error(CLUB_NAME_USERED);
            }
        } else {
            $db = Common::getDftDb();
            if (empty($db)) {
                Master::error('chick_club_name_dberr');
            }
            $sql = "select * from  `club_name` where `name` = '{$name}'";
            $data = $db->fetchArray($sql);
            if (!empty($data)) {
                Master::error(CLUB_NAME_USERED);
            }
        }
    }

    /**
     * 添加联盟名称
     * @param $cid
     * @param $name
     */
    public static function addClubName($cid, $name)
    {
        $SevCfg = Common::getSevidCfg();
        if (empty($SevCfg['ishe'])) {
            return;
        }
        $db = Common::getDftDb();
        if (empty($db)) {
            Master::error('chick_name_dberr');
        }
        $sql = "select * from `club_name` where `cid` = '{$cid}'";
        $data_uid = $db->fetchArray($sql);
        if (empty($data_uid)) {
            $sql = "INSERT INTO `club_name` VALUES ('{$name}', {$cid})";
        } else {
            $sql = "UPDATE `club_name` SET `name`='{$name}' where cid = '{$cid}'";
        }
        if (!$db->query($sql)) {
            Master::error('chick_club_name_insert_err');
        }
    }


    /**
     * 检查uid是否本服uid
     */
    public static function isServerUid($uid)
    {
        $serverId = Game::get_sevid($uid);
        //当前服务器ID
        $nowSeverId = Common::getSevidCfg();
        if ($serverId == $nowSeverId['sevid']) {
            Master::error(USER_999);
        }
        return false;
    }

    /**
     * 检查uid是否合服uid
     */
    public static function isHeServerUid($uid, $is_check = false)
    {
        $uid = intval($uid);
        if ((!defined('IS_TEST_SERVER') || !IS_TEST_SERVER) && $uid < 1000000) {//不在测试服 id小于1000000
            if ($is_check) {
                return false;
            }
            //Master::error(USER_999);
        }
        $serverId = Game::get_sevid($uid);
        $nowSeverId = Common::getSevidCfg();
        $data = Common::getConfig(GAME_MARK . "/SevIdCfg");
        if ($data[$serverId]['he'] != $nowSeverId['he']) {
            if ($is_check == true) {
                return false;
            }
            Master::error(USER_999);
        }
        return true;
    }

    /**
     * 根据uid判断服务器是否存在
     * @param $uid
     * @param bool $is_check
     * @return bool
     */
    public function CheckServerByUid($uid, $is_check = false)
    {
        $uid = intval($uid);
        if ((!defined('IS_TEST_SERVER') || !IS_TEST_SERVER) && $uid < 1000000) {//不在测试服 id小于1000000
            if ($is_check) {
                return false;
            }
            //Master::error(USER_999);
        }
        $serverId = Game::get_sevid($uid);
        $data = Common::getConfig(GAME_MARK . "/SevIdCfg");
        if (!isset($data[$serverId])) {
            if ($is_check) {
                return false;
            }
            Master::error(USER_999);
        }
        return true;
    }

    /**
     * 检查工会ID是否合服工会ID
     */
    public static function isHeServerClubid($cid)
    {
        $serverId = Game::get_sevid_club($cid);
        $nowSeverId = Common::getSevidCfg();
        $data = Common::getConfig(GAME_MARK . "/SevIdCfg");
        if ($data[$serverId]['he'] != $nowSeverId['he']) {
            Master::error(USER_999);
        }
        return true;
    }

    /*
	 * 获取数组指定key为数组下标
	 */
    public static function get_key2id($data, $key)
    {
        $hd_cfg = array();
        if (empty($data)) return $hd_cfg;
        foreach ($data as $v) {
            $hd_cfg[$v[$key]] = $v;
        }
        return $hd_cfg;
    }


    /*
	 * 改名
	 * 返回成功 失败
	 */
    public static function reset_name($uid, $name)
    {
        $db = Common::getMyDb();
        if (empty($db)) {
            Master::error('chick_name_dberr');
        }

        //加锁游戏功能锁..
        $index_name_lock = self::get_lock('chick_name');
        if (empty($index_name_lock)) {
            Master::error(NOTE_SYSTEM_BUSY . '_chick_name');
        }

        //检查名字存在
        $success = false;
        $sql = "select * from  index_name where name = '{$name}'";
        $data = $db->fetchArray($sql);
        if (empty($data)) {
            $sql = "UPDATE index_name SET `name` = '{$name}' where uid = {$uid} limit 1";
            if ($db->query($sql)) {
                $success = true;
            } else {
                Master::error('reset_name_update_err');
            }
            //插入&修改名字
        }
        //解锁功能锁
        $index_name_lock->unlock();
        return $success;
    }

    /*
	 * 判断是否在时间段范围内
	 * 返回距离结束的时间 或者 false
	 */
    public static function is_in_time($beginTime, $endTime)
    {
        $s_time = strtotime($beginTime);
        $e_time = strtotime($endTime);
        if ($_SERVER["REQUEST_TIME"] >= $s_time
            && $_SERVER["REQUEST_TIME"] <= $e_time) {
            return $e_time - $_SERVER["REQUEST_TIME"];
        }
        return false;
    }

    /**
     * 计算恢复点数
     * @param $stime 开始恢复的时间
     * @param $cd 每点的CD
     * @param $num 当前点数
     * @param $max 点数上限
     */
    public static function hf_num($stime, $cd, $num, $max)
    {
        if ($num >= $max) {
            return array(
                'stime' => $_SERVER['REQUEST_TIME'],//开始恢复时间
                'next' => 0,//下次恢复绝对时间
                'num' => $num,//当前点数
            );
        }
        //恢复的时间长度
        $d_time = $_SERVER['REQUEST_TIME'] - $stime;

        //这段时间可以恢复多少点
        $d_num = $cd != 0 ? floor($d_time / $cd) : 0;
        //恢复后总点数
        $sum = $num + $d_num;
        //上限判定
        if ($sum >= $max) {
            $sum = $max;
            $stime = $_SERVER['REQUEST_TIME'];//到达上限 以当前时间为恢复时间
            $next = 0;
        } else {
            //开始恢复时间 += 恢复点数消耗的时间
            $stime += $d_num * $cd;
            //下一点恢复倒计时
            $next = $stime + $cd;
        }
        return array(
            'stime' => $stime,//开始恢复时间
            'next' => $next,//下次恢复绝对时间
            'num' => $sum,//当前点数
        );
    }

    /**
     * 返回服务器当前时间
     * @return int serverTime
     */
    public static function get_now()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    /*
	 * 计算完成时间
	 */
    public static function get_over($time)
    {
        return $_SERVER['REQUEST_TIME'] + $time;
    }

    /*
	 * 判断时间到了没有
	 */
    public static function is_over($time)
    {
        if ($_SERVER['REQUEST_TIME'] >= $time) {
            return true;
        }
        return false;
    }

    /*
	 * 返回结束时间
	 * 如果没结束 返回结束时间
	 * 如果结束 返回0
	 */
    public static function dis_over($time)
    {
        if ($_SERVER['REQUEST_TIME'] < $time) {
            //未结束  返回结束时间
            return $time;
        }
        //已结束 返回0
        return 0;
    }

    /*
	 * 获取日期ID
	 * 按日期编号的ID 180118
	 */
    public static function get_today_id($add_day = 0)
    {
        $a_time = $add_day * 86400;
        return date('ymd', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /*
	 * 获取日期固定时间点
	 * 获取日期固定时间点
	 */
    public static function get_today_id_time($addSecond = 0)
    {
        $d = date("Y-m-d", $_SERVER['REQUEST_TIME']);
        $a_time = strtotime($d) + $addSecond;
        return date('ymdHi', $a_time);
    }

    /*
	 * 获取中午9日期
	 * 按日期编号的ID 180118
	 */
    public static function get_today_id_9($add_day = 0)
    {

        $nowTime = $_SERVER['REQUEST_TIME'];
        $nowHour = date('H');

        if ($nowHour >= 9) {
            return date('ymd9', $nowTime);
        } else {
            return date('ymd9', $nowTime - 86400);
        }
    }

    /*
     * 获取日期ID
     * 按日期编号的ID 180118
     */
    public static function get_today_id_by_time($a_time = 0)
    {
        return date('ymd', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /*
	 * 获取日期ID
	 * 按日期编号的ID  20180118
	 */
    public static function get_today_long_id($add_day = 0)
    {
        $a_time = $add_day * 86400;
        return date('Ymd', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /**
     * 获取月份ID
     * 按月份编号的ID
     */
    public static function get_month_id($add_day = 0)
    {
        $a_time = $add_day * 86400;
        return date('ym', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /**
     * 获取月份ID
     * 按月份编号的ID
     */
    public static function get_week_id($a_time = 0)
    {
        return date('yW', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /**
     * 获取自然周ID
     * 按自然周的ID
     */
    public static function get_week_id_new($a_time = 0)
    {
        return date('oW', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /**
     * 获取月日
     */
    public static function get_month_day_id($a_time = 0)
    {
        return date('md', $_SERVER['REQUEST_TIME'] + $a_time);
    }

    /*
     * 获取今天相对秒数
     */
    public static function get_today_second()
    {

    }

    /**
     * 判断两个时间戳是否为同一天
     * @return true false
     */
    public static function checkIsSameDay($timestamp1)
    {
        $data1 = date('Y-m-d', $timestamp1);
        $data2 = date('Y-m-d', time());
        if ($data1 == $data2) {
            return true;
        }
        return false;
    }

    /*
	 * 往文件写入一个配置
	 */
    public static function set_file_cash($key, $data)
    {
        $filename = 'filecash_' . $key;//文件名
        $require_file = CONFIG_DIR . '/game/cfg_val_format/' . $filename . '.php';//需要包含的文件
        $file = fopen($require_file, 'w');
        fwrite($file, "<?php\nreturn " . var_export($data, 1) . ';');
        fclose($file);
        return true;
    }

    /*
	 * 通用随机函数
	 * 随机上限,配置数组,概率key
	 */
    public static function get_rand_key($rmax, $data, $pkey = null)
    {
        $r = rand(1, $rmax);
        foreach ($data as $k => $v) {
            if (empty($pkey)) {
                $prob = $v;
            } else {
                $prob = $v[$pkey];
            }
            if ($r <= $prob) {
                return $k;
            }
            $r -= $prob;
        }
        return null;
    }

    /*
	 * 通用随机函数
	 * 随机上限,配置数组,概率key
	 */
    public static function get_rand_key1($data, $pkey = null)
    {
        $rmax = 0;
        foreach ($data as $k => $v) {
            if (empty($pkey)) {
                $prob = $v;
            } else {
                $prob = empty($v[$pkey]) ? 1 : $v[$pkey];
            }
            $rmax += $prob;
        }
        $r = rand(1, $rmax);
        foreach ($data as $k => $v) {
            if (empty($pkey)) {
                $prob = $v;
            } else {
                $prob = $v[$pkey];
            }
            if ($r <= $prob) {
                return $k;
            }
            $r -= $prob;
        }
        return null;
    }


    /*
	 * 通用随机函数
	 * 随机上限,配置数组,概率key
	 * 不重复随机
	 */
    public static function get_rand_key2($data, $pkey = null, $num = 1)
    {
        $indexArr = array();
        for ($i = 0; $i < $num; $i++) {
            $rmax = 0;
            foreach ($data as $k => $v) {
                if (empty($pkey)) {
                    $prob = $v;
                } else {
                    $prob = empty($v[$pkey]) ? 1 : $v[$pkey];
                }
                $rmax += $prob;
            }
            $r = rand(1, $rmax);
            foreach ($data as $k => $v) {
                if (empty($pkey)) {
                    $prob = $v;
                } else {
                    $prob = $v[$pkey];
                }
                if ($r <= $prob) {
                    array_push($indexArr, $k);
                    unset($data[$k]);
                    break;
                }
                $r -= $prob;
            }
        }
        return $indexArr;
    }

    /*
	 * 数组随机几个值
	 */
    public static function array_rand($data, $num)
    {
        if (empty($data) || empty($num)) {
            return array();
        }
        if (count($data) <= $num) {
            return $data;
        }
        $keys = array_rand($data, $num);
        if ($num == 1) {
            $keys = array($keys);
        }
        $output = array();
        foreach ($keys as $v) {
            $output[] = $data[$v];
        }
        return $output;
    }

    /*
	 * 子嗣成长属性算法
	 */
    public static function sonepup($level, $love)
    {
        //成长公式  如果要加大红颜影响成长 修改后面的 0.02
        $ep = Game::getCfg_formula()->prentice_prop_add($level, $love);
        //上下偏移百分比 +- 20%
        $ep *= rand(50, 150) / 100;
        //取整
        $ep = round($ep);
        return $ep;
    }

    /*
	 * 记录调试信息
	 */
    public static function debug_cash($msg)
    {
        $cache = Common::getMyMem();
        $debugs = $cache->get('debug');
        if (empty($debugs)) {
            $debugs = array();
        }
        $debugs[] = array(
            'ip' => Common::Getip(),
            'time' => date('Y-m-d H:i:s'),
            'msg' => $msg,
        );
        $max_num = 100;
        if (count($debugs) > $max_num) {
            $debugs = array_slice($debugs, -$max_num, $max_num, 1);
        }
        $cache->set('debug', $debugs);
    }

    /*
	 * 取整 保留N位有效数字
	 */
    public static function my_round($num, $bit)
    {
        $t_bit = strlen($num) - $bit;//去除的位数
        $pwn = pow(10, $t_bit);//除以这个数
        return intval($num / $pwn + 0.5) * $pwn;
    }

    /*
	 * 获取简写的数字描述
	define('NUM_STR_1_STR', '万');
	define('NUM_STR_1_NUM', '10000');
	define('NUM_STR_2_STR', '亿');
	define('NUM_STR_2_NUM', '10000000');
	 */
    public static function get_num_str($num)
    {
        if ($num > intval(NUM_STR_2_NUM)) {
            return intval($num / intval(NUM_STR_2_NUM)) . NUM_STR_2_STR;
        } elseif ($num > intval(NUM_STR_1_NUM)) {
            return intval($num / intval(NUM_STR_1_NUM)) . NUM_STR_1_STR;
        }
        return $num;
    }

    /**
     * 将一个整数随机分成若干份不为0的整数
     * $total_num 总数
     * $total_copies 总份数
     */
    public static function random_splite($total_num, $total_copies)
    {
        $result = array();
        for ($i = $total_copies; $i > 0; $i--) {
            $ls_num = 0;
            $num = 0;
            if ($total_num > 0) {
                if ($i == 1) {
                    $num += $total_num;
                } else {
                    $max_num = floor($total_num / $i);
                    $ls_num = mt_rand(1, $max_num);
                    $num += $ls_num;
                }
            }
            $result[] = $num;
            $total_num -= $ls_num;
        }
        shuffle($result); //打乱数组
        return $result;
    }


    /*
	 * 记录流水
	 * 17	//小关卡流水
		18	//大关卡流水
		6	//添加扣除道具
		29	//活动积分流水
		30	//活动花费积分流水
		42	//活动积分流水
		43	//活动花费积分流水
		38	//世界BOSS积分流水
		33	//衙门流水
		44	//亲密分数排行保存
		24	//个人贡献流水
		23	//个人总贡献流水
		47	//act44
		48	//act44
		19	//act171 act51
		22	//经验值流水
		6001	//增加门客羁绊
		6002	//增加红颜羁绊
		6004	//增加门客声望
		6003	//增加门客声望
		6005	//新增羁绊道具
		6140	//新增服装
		6143	//新增伙伴服装
		6190	//新增晨露
		6192	//新增晨露
		20	//衙门冲榜流水
		99	//回合结束没有门客死亡，判定双方血量
		55	//加上大力丸
		70	//选择一个对手门客 进行战斗
		37	//act86
		7	//势力流水
		9	//level
		11	//senior
		12	//polevel
		8	//伙伴新增或者升级
		25	//zzexp
		26	//pkexp
		28	//邮件领取日志双记录
		49	//联盟-领取红包
		3	//food
		4	//army
		2	//coin
		5	//exp
		14	//新增红颜流水
		15	//亲密
		16	//魅力
		21	//经验
	 * add_record($type,$itemid,$cha,$next)
	 */
    /*
	 * 记录流水
	 * add_record($type,$itemid,$cha,$next)
	 */
    public static function cmd_flow($type, $itemid, $cha, $next)
    {
        if (empty($cha)) {
            return;//0改变值 不记录
        }
        global $cmd_FlowModel;
        if (empty($cmd_FlowModel)) {
            return;
        }
        if ($type == 1 && $cha < 0) {
            Game::cmd_consume_flow($cmd_FlowModel->uid, $cha, $cmd_FlowModel->model, 1, $cmd_FlowModel->ctrl);
        }
        $cmd_FlowModel->add_record($type, $itemid, $cha, $next);
    }

    public static function flow_php_record($uid, $type, $dId, $dNum, $dName = "", $dValue = 0, $tag1 = 0)
    {
        return;
        $area = array(
            "eplocal" => 0,
            "epdevlocal" => 0,
            "epzjfhover" => 1,
            "epzjfhovergat" => 2,
            "epzjfh" => 3,
            "epgtmz" => 4,
            "epgtmzch" => 5,
        );

        if (GAME_MARK == "epzjfhover" || GAME_MARK == "epzjfhovergat" || GAME_MARK == "epzjfh" || GAME_MARK == "epgtmz" || GAME_MARK == "epgtmzch" || $uid < 999999) {
            $data = array(
                'game' => 10001,
                'sign' => null,
                'area' => $area[GAME_MARK],
                'site' => $uid < 1000000 ? 9999 : floor($uid / 1000000),
                'type' => $type,
                'time' => time(),

                'user' => $uid,
                'tag1' => $tag1,
                'tag2' => null,
                'tag3' => null,

                'dataId' => $dId,
                'dataName' => $dName,
                'dataValue' => $dValue,
                'dataNum' => $dNum,

                'roleId' => '',
                'roleName' => null,
                'roleLevel' => 0,
                'roleVip' => 0,
            );

            //$host = 'data-center.kkk-game.com';
            //$port = 9999;
            $host = 'unix:///tmp/kkk.sock';
            $port = -1;
            $path = '/v1/data';
            $data['sign'] = md5('kkk-game' . $data['game'] . '43ebae799c94a0797fabd0ffdcc912ae' . $data['area'] . $data['site'] . $data['type'] . $data['time'] . $data['user'] . $data['dataId'] . $data['roleId']);
            $poststring = json_encode($data);

            error_log("send flow" . $uid . " type" . $type);
            self::async_http_post($host, $port, $path, $poststring);
        }
    }

    private static function async_http_post($host, $port, $path, $poststring)
    {
        $fp = fsockopen($host, $port, $errno, $errstr, 1);
        if (!$fp) {
            return "{$errstr} ({$errno})";
        }

        $url = "http://data-center.kkk-game.com:9999/v1/data";
        $msg = "{$url}\n{$poststring}";

        fputs($fp, $msg);
        fclose($fp);
        return 'ok';
    }

    /**
     * 添加流水
     * @param int $uid 玩家id
     * @param string $model 模块
     * @param string $contrl 控制器
     * @param mixed $param 参数
     * @param mixed $type 流水类型
     * @param mixed $itemid 道具id
     * @param int $cha 差值
     * @param int $next 当前值
     */
    public static function cmd_other_flow($uid, $model, $contrl, $param, $type, $itemid, $cha, $next)
    {
        Common::loadModel("FlowModel");
        $flowModel = new FlowModel($uid, $model, $contrl, $param);
        $flowModel->add_record($type, $itemid, $cha, $next);
        $flowModel->destroy_now();
    }

    /**
     * 添加聊天流水
     * @param int $uid 玩家id
     * @param mixed $type 类型
     * @param string $content 内容
     * @param int $time 时间
     * @param mixed $other 其他参数
     */

    public static function cmd_chat_flow($type, $uid, $name, $vip, $level, $content, $time, $other = 1)
    {
        Common::loadModel("FlowModel");
        FlowModel::chat_flow($type, $uid, $name, $vip, $level, $content, $time, $other);
    }

    /**
     * 消费统计
     * @param $uid
     * @param $num
     * @param $from
     */
    public static function cmd_consume_flow($uid, $num, $from, $type, $other)
    {
        Common::loadModel("FlowModel");
        FlowModel::consume_flow($uid, $num, $from, $type, $other);
    }

    /**
     * 字符串验证   长度   敏感  非法 验证
     * @param string $str
     */
    public static function str_check($str)
    {
        //敏感
        self::str_mingan($str);
        //非法
        self::str_feifa($str);
        //长度验证
        self::str_len($str, 30);
    }

    /**
     * 长度验证 (返回长度)
     * @param string $str
     * @param int $len 0:验证
     */
    public static function str_len($str, $len = 0)
    {
        preg_match_all("/./us", $str, $match);
        $strLen = count($match[0]);
        if ($len > 0 && $strLen > $len) {
            Master::error(COMMON_INPUT_NUM_LIMIT . '|' . $len);
        }
        return $strLen;
    }


    /**
     * 判断敏感   替换敏感
     * @param string $str 字符串
     * @param int $tihuan 默认只判断敏感  1:替换
     */
    public static function str_mingan($str, $tihuan = 0)
    {
        $ti = ' *** ';
        //配置文件获取字库
        $filter = array();
        $filter = Game::getcfg('filter');
        if (empty($filter)) {
            return $str;
        }


        foreach ($filter as $v) {
            if ($tihuan) {
//				$str = str_replace($v,$ti,$str);
                $str = preg_replace($v, $ti, $str);
            } else {
//				if (!empty($v) && !(strpos($str,$v) === FALSE)){
//					Master::error('有敏感字符');
//				}
                if (!empty($v)) {
                    preg_match($v, $str, $matches);
                    if (!empty($matches)) {
                        Master::error(ACT_34_MINGAN);
                    }
                }
            }
        }
        return $str;

    }

    /**
     * 去掉字符串内的空格
     * @param $str
     * @return mixed
     */
    public function trimall($str)
    {
        $str = preg_replace("/\s+/", '', $str);
        return $str;
    }

    private static function _filter_Emoji($str)
    {
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }

    /*
	 * 过滤特殊字符
	 * $str  需检查的字符串
	 * $guolv 出现特殊字符是否报错 1直接过滤不报错 0报错
	 * */
    public static function filter_char($str, $guolv = 1)
    {
        //特殊字符验证
        $heimingdan = array("\t", "\\n", "\n", "\r", "\f", "/", "'", "\\", "\"", "", ",", "|", ";");
        foreach ($heimingdan as $v) {
            if ($guolv) {
                $str = str_replace($v, '', $str);
            } else {
                if (!(strpos($str, $v) === FALSE)) {
                    Master::error(ERROR_NAME_FEIFA . "1");
                }
            }
        }

        //验证
        $tmpStr = json_encode($str); //暴露出unicode
        $tmpStr = preg_replace("#(\\\ue[0-9a-f]{3})#ie", "addslashes('\\1')", $tmpStr); //将emoji的unicode留下，其他不动
        // $msg = json_decode($tmpStr);
        if (preg_match('/\\\u[ed]{1}[0-9a-f]{3}/', $tmpStr)) {//含有emoji
            if ($guolv) {
                //过滤
                $str = self::_filter_Emoji($str);
            } else {
                Master::error(ERROR_NAME_FEIFA);
            }
        }
        // $tmpStr = json_encode($str); //暴露出unicode
        // // $tmpStr = preg_replace("#(\\\ue[0-9a-f]{3})#ie","addslashes('\\1')",$tmpStr); //将emoji的unicode留下，其他不动
        // // $msg = json_decode($tmpStr);
        // if(preg_match('/(\ud83c[\udf00-\udfff])|(\ud83d[\udc00-\ude4f\ude80-\udeff])|[\u2600-\u2B55]/g', $tmpStr)){//含有emoji
        //     if($guolv){
        //         //过滤
        //         $str = self::_filter_Emoji($str);
        //     }else{
        //        Master::error(ERROR_NAME_FEIFA."2");
        //     }
        // }

        return $str;
    }

    /**
     * 过滤掉非法(斜杠引导表情)   判断非法
     * @param string $str
     * @param int $guolv 默认判断非法    1:过滤非法
     * @return string
     */
    public static function str_feifa($str, $guolv = 0)
    {
        $str = str_replace(PHP_EOL, '', $str);
        //去头尾两端的空格
        $str = trim($str);
        //过滤表情
        $tmpStr = json_encode($str); //暴露出unicode
        //将emoji的unicode留下，其他不动
        $tmpStr = preg_replace("#(\\\ue[0-9a-f]{3})#ie", "addslashes('\\1')", $tmpStr);
        $tmpStr = json_decode($tmpStr);
        if (empty($tmpStr)) {
            $tmpStr = '';
        }
        if ($guolv) {
            $str = $tmpStr;
        } else {
            if ($str != $tmpStr) {
                Master::error(ERROR_NAME_FEIFA);
            }
        }

        $ti = '*';
        //特殊字符验证
        $heimingdan = array("\t", "\\n", "\n", "\r", "\f", "/", "'", "\\", "\"");
        foreach ($heimingdan as $v) {
            if ($guolv) {
                $str = str_replace($v, $ti, $str);
            } else {
                if (!(strpos($str, $v) === FALSE)) {
                    Master::error(ERROR_NAME_FEIFA);
                }
            }
        }
        return $str;
    }

    //在一定范围内随机生成不重复随机数
    public function unique_rand($min, $max, $num)
    {
        $count = 0;
        $return = array();
        while ($count < $num) {
            $return[] = mt_rand($min, $max);
            $return = array_flip(array_flip($return));
            $count = count($return);
        }
        //打乱数组，重新赋予数组新的下标
        shuffle($return);
        return $return;
    }

    /**
     * 获取配置文件
     * @param string $filename
     * @param $is_report_error
     * @return array
     */
    public static function getcfg($filename, $is_report_error = true)
    {
        static $config;
        if (defined('GAME_SKIN') && GAME_SKIN) {
            $skinSon = 1;//除1皮外，其他不支持切换数值
        } else {
            Common::loadModel('ServerModel');
            $skinSon = ServerModel::getSkin();
        }
        if (empty($config[$skinSon][$filename])) {

            $cfg_arr = self::get_peizhi('conf_' . $filename);
            if (empty($cfg_arr)) {

                if (defined('GAME_SKIN') && GAME_SKIN) {
                    $require_file = CONFIG_DIR . '/game_' . GAME_SKIN . '/' . $filename . '.php';//需要包含的文件
                } else {
                    //1皮读取配置有特殊要求
                    if ($skinSon == 2) {
                        $require_file = CONFIG_DIR . '/game_1_yc/' . $filename . '.php';//需要包含的文件
                    } else {
                        $require_file = CONFIG_DIR . '/game/' . $filename . '.php';//需要包含的文件
                    }
                }
                if (!file_exists($require_file)) {//没有的话
                    if ($is_report_error) {
                        Master::error($filename . '_error' . $require_file);
                    } else {
                        return array();
                    }
                }
                $cfg_arr = include($require_file);//读取新配置
            }
            $config[$skinSon][$filename] = $cfg_arr;
        }
        return $config[$skinSon][$filename];
    }

    /*
	 * 根据ID获取配置文件 的一段
	 */
    public static function getcfg_info($filename, $id, $errmsg = '')
    {
        if (empty($id)) {
            $id = 0;
        }
        $cfg = self::getcfg($filename);
        if (empty($cfg[$id])) {
            if (empty($errmsg)) {
                Master::error('getcfg_info_err_' . $filename . '_' . $id);
                // Master::error("本次展示内容到此为止，其余内容将在未来开放");
            } else {
                //自定义报错 / 比如等级上限
                Master::error($errmsg);
            }
        }
        return $cfg[$id];
    }

    /*
     * 根据ID获取配置文件 的一段
     */
    public static function getcfg_param($id, $errmsg = '')
    {
        if (empty($id)) {
            $id = 0;
        }
        $cfg = self::getcfg('param');
        if (empty($cfg[$id])) {
            if (empty($errmsg)) {
                Master::error('get_cfg_param_id' . $id . 'error');
            } else {
                //自定义报错 / 比如等级上限
                Master::error($errmsg);
            }
        }
        return $cfg[$id]['param'];
    }

    /*
     * 根据ID获取配置文件 的一段
     */
    public static function getCfg_formula()
    {
        static $config;
        $filename = "formula";
        if (defined('GAME_SKIN') && GAME_SKIN) {
            $skinSon = 1;//除1皮外，其他不支持切换数值
        } else {
            Common::loadModel('ServerModel');
            $skinSon = ServerModel::getSkin();
        }
        if (empty($config[$skinSon][$filename])) {
            if (defined('GAME_SKIN') && GAME_SKIN) {
                $require_file = CONFIG_DIR . '/game_' . GAME_SKIN . '/' . $filename . '.php';//需要包含的文件
            } else {
                //1皮读取配置有特殊要求
                if ($skinSon == 2) {
                    $require_file = CONFIG_DIR . '/game_1_yc/' . $filename . '.php';//需要包含的文件
                } else {
                    $require_file = CONFIG_DIR . '/game/' . $filename . '.php';//需要包含的文件
                }
            }
            if (!file_exists($require_file)) {//没有的话
                return null;
            }

            require_once($require_file);
            $config[$skinSon][$filename] = new $filename();
        }
        return $config[$skinSon][$filename];

    }

    /*
	 * 获取后端配置文件
	 */
    public static function getBaseCfg($filename)
    {
        static $config;
        if (empty($config[$filename])) {
            $require_file = CONFIG_DIR . '/base/' . $filename . '.php';//需要包含的文件
            if (!file_exists($require_file)) {//没有的话
                Master::error($filename . '_config_error');
            }
            $cfg_arr = include($require_file);//读取新配置
            $config[$filename] = $cfg_arr;
        }
        return $config[$filename];
    }

    /*
	 * 格式化 资质技能
	 */
    public static function format_ep_skill($skills)
    {
        $fmt = array();
        foreach ($skills as $skillid => $level) {
            $fmt[] = array(
                'id' => $skillid,
                'level' => $level,
            );
        }
        return $fmt;
    }

    /*
	 * 格式化 PK技能
	 */
    public static function format_pk_skill($skills)
    {
        $fmt = array();
        foreach ($skills as $skillid => $level) {
            $fmt[] = array(
                'id' => $skillid,
                'level' => $level,
            );
        }
        return $fmt;
    }

    /*
	 * 获取开关
	 */
    public static function get_gq_status($key)
    {
        $guanq = Game::get_peizhi('gq_status');
        if (isset($guanq[$key])) {
            return $guanq[$key];
        }
        return 0;
    }

    /**
     * 自动道具数量
     * @param $item
     * @param $e
     * @return array
     */
    public static function auto_count($item, $e)
    {
        if (empty($item['kind'])) {
            $item['kind'] = KIND_ITEM;
        }
        if (!empty($item['type'])) {
            $item['count'] = self::type_to_count($item['type'], $e);
        }
        return $item;
    }

    /*
     * 关卡道具翻倍
     * $type :  pve   pvb
     */
    public static function pv_beishu($type)
    {
        $beishu = 1;
        $guanKaRwd = Game::get_peizhi('guanKaRwd');
        if (empty($guanKaRwd)) {
            return $beishu;
        }
        $SevidCfg = Common::getSevidCfg();
        foreach ($guanKaRwd as $value) {
            //开始时间
            $stime = strtotime($value['stime']);
            //结束时间
            $etime = strtotime($value['etime']);
            if (Game::dis_over($stime) || Game::is_over($etime)) {
                continue;
            }
            ////过滤不在生效服务器内
            if ($value['serv'] != 'all') {
                $servs = Game::serves_str_arr($value['serv']);
                if (!in_array($SevidCfg['he'], $servs)) {
                    continue;
                }
            }
            //不在开服生效时间内
            if ($value['kday'] != 'all') {
                Common::loadModel('ServerModel');
                $open_day = ServerModel::isOpen($SevidCfg['sevid']);
                $kday = Game::serves_str_arr($value['kday']);
                if (!in_array($open_day, $kday)) {
                    continue;
                }
            }

            if ($type == 'guanKaRwd') {
                return $value;
            }

            //是否有配置相应翻倍
            if (!empty($value[$type])) {
                $beishu = max(1, $value[$type]);
                return $beishu;
            }
        }
        return $beishu;

    }


    /**
     * 百服开服充值不断,福利礼包不停
     */
    public static function baifu_rwd()
    {

        $info = array();

        $baifurwd = Game::get_peizhi('baifurwd');
        if (empty($baifurwd)) {
            return 0;
        }
        $SevidCfg = Common::getSevidCfg();
        foreach ($baifurwd as $value) {
            //开始时间
            $stime = strtotime($value['stime']);
            //结束时间
            $etime = strtotime($value['etime']);
            if (Game::dis_over($stime) || Game::is_over($etime)) {
                continue;
            }

            ////过滤不在生效服务器内
            if ($value['serv'] != 'all') {
                $servs = Game::serves_str_arr($value['serv']);
                if (!in_array($SevidCfg['he'], $servs)) {
                    continue;
                }
            }
            //不在开服生效时间内
            if ($value['kday'] != 'all') {
                Common::loadModel('ServerModel');
                $open_day = ServerModel::isOpen($SevidCfg['sevid']);
                $kday = Game::serves_str_arr($value['kday']);
                if (!in_array($open_day, $kday)) {
                    continue;
                }
            }
            //是否有配置相应翻倍
            $info = $value;
            return $info;
        }
        return $info;
    }


    /**
     * 道具数量  type  转   count
     * @param mixed $type type 的值(公式)
     * @param array $e (四属性)
     * @return float
     */
    public static function type_to_count($type, $e)
    {
        $e1 = $e['1'];
        $e2 = $e['2'];
        $e3 = $e['3'];
        $e4 = $e['4'];

        $type = eval("return " . $type . ";");
        return floor($type);
    }

    /**
     * 判断是不是王爷
     * @param int $chid
     * @return bool
     * */
    public static function is_ye($chid)
    {

        //过滤称号错误
        $chenghao_cfg = Game::getcfg_info('chenghao', $chid);
        if (!empty($chenghao_cfg) && in_array($chenghao_cfg['type'], array(1, 3))) {
            return true;
        }
        return false;
    }

    /**
     * 自动赈灾,获取信息
     * @param int $count 赈灾所需道具 => 玩家当前拥有的数量
     * @param int $num 当前已赈灾次数
     * @param int $yunshi 玩家当前运势
     * @param int $ysSet 运势设置
     * @return int
     */
    public static function get_zhenzai_info($count, $num, $yunshi, $ysSet)
    {
        if ($yunshi >= $ysSet) {
            return $num;
        }
        //当前已赈灾次数 +1
        $num++;
        //扣除玩家拥有道具
        $count -= $num * 20000;
        $yunshi += 2;
        if ($count < 0) {
            $num--;
            return $num;
        }
        $num = self:: get_zhenzai_info($count, $num, $yunshi, $ysSet);
        return $num;  //返回最大赈灾次数,包括已赈灾的
    }

    /**
     * 获取后台基础配置 单服优先 再通服
     * @param string $key
     * @return array
     */
    public static function get_peizhi($key)
    {
        Common::loadModel('HoutaiModel');
        $base_pz = HoutaiModel::get_base_pz($key);
        if (empty($base_pz)) {
            $base_pz = HoutaiModel::get_all_pz($key);
        }
        return empty($base_pz) ? array() : $base_pz;
    }

    /**
     * 获取角色转移列表
     * @param string $key
     * @return array
     */
    public static function get_gm_login()
    {

        Common::loadModel('ServerModel');
        $sevid = ServerModel::getDefaultServerId();

        $redis = Common::getRedisBySevId($sevid);
        $gmLoginRes = $redis->zRevRange("gm_login_redis", 0, -1, true);
        return empty($gmLoginRes) ? array() : $gmLoginRes;
    }

    /**
     * 根据平台编号获取配置
     * @param string $key
     * @param $platform
     * @param array $default
     * @return array|mixed
     */
    public static function get_peizhi_by_platform($key, $platform, $default = array())
    {
        static $res = array();
        $resKey = "{$key}_{$platform}";
        if (isset($res[$resKey])) {
            return $res[$resKey];
        }

        //第一次读取
        if (!empty($platform)) {
            Common::loadModel('OrderModel');
            OrderModel::get_platform_cfg($platform);
        }
        //优先按包读取客服，读取不到按渠道，读取不到按默认
        if (empty($default)) {
            $toCover = Game::get_peizhi($key);
            if (isset($toCover[$platform])) {
                $default = $toCover[$platform];
            } else if (defined("SNS_BASE") && isset($toCover[SNS_BASE])) {
                $default = $toCover[SNS_BASE];
            }
        }
        return $res[$resKey] = $default;
    }


    // 记录日志
    public static function login_debug($logname, $log, $is_file_append = true)
    {
        $url = LOCK_PATH . 'login/' . SNS;
        if ($is_file_append) {
            file_put_contents($logname, $log, FILE_APPEND);
        } else {
            file_put_contents($logname, $log);
        }
    }


    // 记录日志
    public static function logMsg($logname, $log, $is_file_append = true)
    {
        if (false == Common::createFolders(dirname($logname))) {
            file_put_contents('/tmp/' . strtr(basename(__FILE__), array('.' => '_')) . date('Ymd') . '.log', 'log error:' . $logname, FILE_APPEND);
            return;// 无法构建的情况下跳过
        }
        $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $log . PHP_EOL);
        if ($is_file_append) {
            file_put_contents($logname, $log, FILE_APPEND);
        } else {
            file_put_contents($logname, $log);
        }
    }

    public static function yihuan_order_success($params)
    {
        $orderid = $params['pOrderId'];
        $userid = $params['userId'];
        $roleid = $params['creditId'];
        $productid = $params['productId'];
        $currency = $params['currency'];
        $amount = $params['amount'];
        $rcurrency = $params['RCurrency'];
        $ramount = $params['RAmount'];
        $gamecode = $params['gameCode'];
        $servercode = $params['serverCode'];
        $stone = $params['stone'];
        $stonetype = $params['stoneType'];
        $activityextra = $params['activityExtra'];
        $paytype = $params['payType'];
        $time = $params['time'];
        $status = 1;
        $extrainfo = explode('_', $params['remark']);
        $expand = $extrainfo[0];
        $sql = "INSERT INTO `yh_order` (`orderid`, `userid`, `roleid`, `productid`, `currency`, 
					`amount`, `rcurrency`, `ramount`, `gamecode`, `servercode`, `stone`, `stonetype`,
					`activityextra`, `paytype`, `time`, `status`, `expand`) VALUES ( 
					'{$orderid}','{$userid}','{$roleid}','{$productid}','{$currency}', 
					'{$amount}','{$rcurrency}','{$ramount}','{$gamecode}','{$servercode}','{$stone}','{$stonetype}',
					'{$activityextra}','{$paytype}','{$time}','{$status}','{$expand}');";
        $db = Common::getDbBySevId($servercode);
        $result = $db->query($sql);
        return $result;
    }

    // 支付日志
    public static function order_debug($log)
    {
        $logfile = LOG_PATH . 'order/' . SNS . '.log';
        if (false == Common::createFolders(dirname($logfile))) {
            return;
        }
        $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $log . PHP_EOL);
        file_put_contents($logfile, $log, FILE_APPEND);
    }

    //其他log
    public static function other_debug($log)
    {
        $logfile = LOG_PATH . 'other_debug.log';
        if (false == Common::createFolders(dirname($logfile))) {
            return;
        }
        $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $log . PHP_EOL);
        file_put_contents($logfile, $log, FILE_APPEND);
    }

    //其他log
    public static function defult_error($log)
    {
        $logfile = LOG_PATH . 'defult_error.log';
        if (false == Common::createFolders(dirname($logfile))) {
            return;
        }
        $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $log . PHP_EOL);
        file_put_contents($logfile, $log, FILE_APPEND);
    }

    //跑批log
    public static function crontab_debug($log, $name = "crontab")
    {
        $logfile = "/data/logs/con/" . date('ymd', time()) . "/" . $name . ".log";
        if (false == Common::createFolders(dirname($logfile))) {
            return;
        }
        $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $log . PHP_EOL);
        file_put_contents($logfile, $log, FILE_APPEND);
    }

    /**
     * 切割字符串，含“,”和“-”符合的字符串
     * @param $str
     * @return array
     */
    public static function serves_str_arr($str)
    {
        $list = array();
        $listTemp = explode(',', $str);
        foreach ($listTemp as $listTemp_v) {
            $listTemp_v_temp = explode('-', $listTemp_v);
            if (count($listTemp_v_temp) == 1) {
                $list = array_merge($list, $listTemp_v_temp);
            } else {
                $list = array_merge($list, range($listTemp_v_temp[0], $listTemp_v_temp[1]));
            }
        }
        return $list;
    }

    public static function isRightDate($date)
    {
        return preg_match(
                "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/",
                $date
            ) > 0;
    }

    public static function isRightNumber($num)
    {
        return preg_match(
                "/^(0|[1-9][0-9]*)$/",
                $num
            ) > 0;
    }

    /**
     * 获取跨服主服
     * @param $kuaCfg
     * @param $sevid
     * @return bool
     */
    public static function getKuaCfgServerID($kuaCfg, $sevid)
    {
        if (empty($kuaCfg)) {
            return false;
        }
        foreach ($kuaCfg as $cfg) {
            if (isset($cfg[0], $cfg[1]) && !empty($cfg[0]) && $sevid >= $cfg[0] && $sevid <= $cfg[1]) {
                //起始服为默认指定跨服
                return $cfg[0];
            }
            if (isset($cfg[2]) && is_array($cfg[2]) && in_array($sevid, $cfg[2])) {
                //有指定起始服则为指定跨服，否则第一个特定服为指定跨服
                return empty($cfg[0]) ? $cfg[2][0] : $cfg[0];
            }
        }
        return false;
    }

    /**
     * @param $str
     * @return string
     */
    public static function getAdminApiSign($str)
    {
        return md5($str . 'gong_ting_admin_api_key');
    }

    /**
     * 检验IP
     * @param $uid
     * @return bool
     */
    public static function checkIPUser()
    {
        $guanq = Game::get_peizhi('gq_status');
        if (!isset($guanq['iplimit']) || $guanq['iplimit'] == 0) {
            return true;
        }
        if ($guanq['iplimittype'] > 12) {
            $guanq['iplimittype'] = 12;
        }
        if (!isset($guanq['iplimittype']) || $guanq['iplimittype'] == 0) {
            $guanq['iplimittype'] = 24;
        }
        $ip = Common::GetIP();

        //禁止注册ip
        $banip = Game::get_peizhi('banip_register');
        if (!empty($banip) && in_array($ip, $banip)) {
            return false;
        }

        $cache = Common::getComMem();
        $id = intval(date('H') / $guanq['iplimittype']);
        $key = 'iplimit_regsiter_' . Game::get_today_id() . '_' . $id . '_' . $ip;
        $ip_uids = $cache->get($key);
        if (count($ip_uids) >= $guanq['iplimit']) {
            $pass_key = 'iplimit_regsiter_pass_standard';
            $passInfo = $cache->get($pass_key);
            if (empty($passInfo) || !in_array($key, $passInfo)) {
                $passInfo[] = $key;
                $cache->set($pass_key, $passInfo, 7 * 24 * 3600);
            }
            return false;
        }
        return true;
    }

    /**
     * 添加IP
     * @param $uid
     * @return bool
     */
    public static function addIPUser($uid)
    {

        $guanq = Game::get_peizhi('gq_status');

        if (!isset($guanq['iplimit']) || $guanq['iplimit'] == 0) {
            return;
        }

        if (isset($guanq['iplimittype']) && $guanq['iplimittype'] > 12) $guanq['iplimittype'] = 12;
        if (!isset($guanq['iplimittype']) || $guanq['iplimittype'] == 0) {
            $guanq['iplimittype'] = 24;
        }
        $ip = Common::GetIP();
        //自己服务器的mem
        $mycache = Common::getCacheByUid($uid);
        $ip_key = $uid . '_ip';
        $my_ip = $mycache->get($ip_key);
        if ($ip != $my_ip) {
            $mycache->set($ip_key, $ip);
        }

        //记录ip限制
        $cache = Common::getComMem();
        $id = intval(date('H') / $guanq['iplimittype']);
        $key = 'iplimit_regsiter_' . Game::get_today_id() . '_' . $id . '_' . $ip;

        $ip_uids = $cache->get($key);
        if (!isset($ip_uids[$uid])) {
            $ip_uids[$uid] = 1;
            $cache->set($key, $ip_uids, 7 * 24 * 3600);
        }
    }


    /**
     * 获取每日剩余时间
     * @return int
     */
    public static function getResttoday()
    {
        return strtotime(date('Ymd 23:59:59')) - Game::get_now();
    }

    /**
     * 获取跨服势力期间-主服匹配
     * @param $need
     * @return array
     */
    public static function kua_zhufu($need)
    {
        static $data;
        if (empty($need)) {
            return array();
        }
        $SevidCfg = Common::getSevidCfg();
        if (empty($data[$SevidCfg['he']])) {
            foreach ($need as $value) {
                if (empty($value[0])) {
                    continue;
                }
                $servs = Game::serves_str_arr($value[0]);
                if (empty($servs) || !in_array($SevidCfg['he'], $servs)) {
                    continue;
                }
                sort($servs);

                $Redis134Model = Master::getRedis134($SevidCfg['he']);
                $key = $Redis134Model->get_rank_id($SevidCfg['he']);//当前位置

                $zf_key = intval(($key - 1) / $value[1]) * $value[1] + 1;   //主服下标

                $zhufu = $Redis134Model->get_member($zf_key);
                $data[$SevidCfg['he']] = empty($zhufu) ? $SevidCfg['he'] : $zhufu;

                break;
            }
        }
        return array(
            'zhufu' => $data[$SevidCfg['he']],
        );
    }

    /**
     * 取出配对服
     * @param $serv
     * @param $sID
     * @return array
     */
    public static function findMatchServers($serv, $sID)
    {
        foreach ($serv as $v) {
            $match = explode(',', $v[1]);
            foreach ($match as $match_v) {
                $matchArr = explode('*', $match_v);
                if (in_array($sID, $matchArr)) {
                    sort($matchArr);
                    return $matchArr;
                }
            }
        }
        return array();
    }

    /**
     * 获取跨服的主服 区服从小到达
     * @param $sev
     * @param int $sID 当前区服合服
     * @param int $num 几个一个区间
     * @return int|mixed
     */
    public static function get_zhu_sev($sev, $sID, $num = 2)
    {
        $sid = 0;
        if (!empty($sev)) {
            //解析活动配置的服务器区间
            $slist = Game::serves_str_arr($sev);
            asort($slist);
            $list = array();
            foreach ($slist as $id) {
                //去掉从服的
                $SevCfgObj = Common::getSevCfgObj($id);
                if (!in_array($SevCfgObj->getHE(), $list)) {
                    $list[] = $SevCfgObj->getHE();
                }
            }
            if (in_array($sID, $list)) {//探索的区服在区间内
                $key = intval(array_search($sID, $list) / $num) * $num;
                $sid = $list[$key];
            }
        }
        return $sid;
    }

    /**
     * 获取匹配区间-七夕活动使用
     * @param $serv
     * @param $sID
     * @return array
     */
    public static function getMatchServers($serv, $sID)
    {
        $match = explode(',', $serv);
        foreach ($match as $match_v) {
            $matchArr = explode('*', $match_v);
            if (in_array($sID, $matchArr)) {
                sort($matchArr);
                return $matchArr;
            }
        }
        return array();
    }

    /**
     * 获取跨服期间-主服匹配
     * @param $need
     * @param int $key 服务器id
     * @return int
     */
    public static function kua_all_zhufu($need, $key)
    {
        $allzhufu = 0;
        if (empty($need)) {
            return $allzhufu;
        }
        foreach ($need as $value) {
            if (empty($value[0])) {
                continue;
            }
            $servs = Game::serves_str_arr($value[0]);
            if (empty($servs) || !in_array($key, $servs)) {
                continue;
            }
            sort($servs);
            $allzhufu = min($servs);
            return $allzhufu;
        }
        return $allzhufu;

    }

    /**
     * 获取跨服期间-所有合服
     * @param $need
     * @param int $key 服务器id
     * @return array
     */
    public static function kua_all_he($need, $key)
    {
        $allhe = array();
        foreach ($need as $value) {
            if (empty($value[0])) {
                continue;
            }
            $servs = Game::serves_str_arr($value[0]);
            if (empty($servs) || !in_array($key, $servs)) {
                continue;
            }
            sort($servs);
            $minServs = min($servs);
        }
        while ($minServs >= $servs[0] && $minServs <= $servs[1]) {
            //TODO 这段代码有问题
            $sevCfg = Common::getSevidCfg($minServs);
            if (!in_array($sevCfg['he'], $allhe)) {
                $allhe[] = $sevCfg['he'];
            }
            $minServs++;
        }
        return $allhe;
    }


    /**
     * 获取跨服亲密期间-主服匹配
     * @param $need
     * @return array
     */
    public static function kua_lovezhufu($need)
    {
        static $data;
        if (empty($need)) {
            return array();
        }
        $SevidCfg = Common::getSevidCfg();
        if (empty($data[$SevidCfg['he']])) {
            foreach ($need as $value) {
                if (empty($value[0])) {
                    continue;
                }
                $servs = Game::serves_str_arr($value[0]);
                if (empty($servs) || !in_array($SevidCfg['he'], $servs)) {
                    continue;
                }
                sort($servs);

                $Redis140Model = Master::getRedis140($SevidCfg['he']);
                $key = $Redis140Model->get_rank_id($SevidCfg['he']);//当前位置

                $zf_key = intval(($key - 1) / $value[1]) * $value[1] + 1;   //主服下标

                $zhufu = $Redis140Model->get_member($zf_key);
                $data[$SevidCfg['he']] = empty($zhufu) ? $SevidCfg['he'] : $zhufu;

                break;
            }
        }
        return array(
            'zhufu' => $data[$SevidCfg['he']],
        );
    }


    /*
     * 判断uid是否合法
     */
    public static function check_uid($uid)
    {
        if (defined('IS_TEST_SERVER') && IS_TEST_SERVER) {
            if ($uid > 1000000) {
                return false;
            }
        } else {
            if ($uid < 1000000) {
                return false;
            }
        }
        return true;
    }

    /**
     * 修改密码
     * @param $account
     * @param $old_pwd
     * @param $new_pwd
     */
    public static function modify_pwd($account, $old_pwd, $new_pwd)
    {
        Common::loadModel('UserAccountModel');
        $UserAccountModel = new UserAccountModel($account);
        $UserAccountModel->modify_pwd($old_pwd, $new_pwd);
    }

    /**
     * 获取太平天国cid
     * @param $type
     * @param $id
     * @return mixed
     */
    public static function getTptgCid($type, $id)
    {
        return ($type + 1) * 10000 + $id;
    }

    /*
	 * 获取后端配置文件
	 */
    public static function getGiftBagCfg()
    {
        $moneyflag = "¥";
        if (defined('OVERSEAS') && OVERSEAS) {
            $moneyflag = '$';
        }

        $giftBag = Game::getCfg('gift_bag');
        $order_shop = Master::getOrderShopCfg();
        foreach ($giftBag as $k => $v) {
            if (!empty($v['dc'])) {
                $data = $order_shop[$v['dc']];
                $giftBag[$k]['krw'] = $data['krw'];
                //$giftBag[$k]['dollar'] = $data['dollar'];
                $giftBag[$k]['sign'] = $moneyflag;
                $giftBag[$k]['symbol'] = $moneyflag;
                //$giftBag['cpId'] = $data['cpId'];
            }
        }
        return $giftBag;
    }


}





