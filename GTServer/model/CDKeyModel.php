<?php
// cdkey
class CDKeyModel {
	const STATE_VALID = 0;// 有效
	const STATE_INVALID = 1;// 无效
	
	public static function loadConfig() {
		// 先从配置加载
		$cfgCdKey = CommonModel::getAllcfg('cdkey');
		if ( empty($cfgCdKey) ) {
			$cfgCdKey = Common::getConfig('bset/cfg_cdkey');
		}
		return (is_array($cfgCdKey)) ? $cfgCdKey : array();
	}

	public static function add($params) {
		$ctime = $_SERVER['REQUEST_TIME'];
		$utime = 0;
    	$db = Common::getMyDb();
    	$params = array_map('mysql_real_escape_string', $params);
    	if ( !isset($params['state']) ) {
    		$params['state'] = self::STATE_VALID;
    	}
    	if ( self::STATE_INVALID == $params['state'] ) {
    		$utime = $ctime;
    	}
    	
		$sql = "insert into `act_acode` 
			(`acode`, `type`, `state`, `comm`, 
			`sevid`, `uid`, `platform`, `ustr`, `mtime`, `utime`)
			values ('{$params['acode']}', '{$params['type']}', '{$params['state']}', 
			'{$params['comm']}', '{$params['sevid']}', '{$params['uid']}', '{$params['platform']}', 
			'{$params['ustr']}', '{$ctime}', '{$utime}')";
		return $db->query($sql);
	}
	
	public static function sign($params) {
		$utime = $_SERVER['REQUEST_TIME'];
		
    	$db = Common::getMyDb();
    	$params = array_map('mysql_real_escape_string', $params);
    	$state = self::STATE_INVALID;
    	
		$sql = "update `act_acode` set
			`state`='{$state}',
			`sevid`='{$params['sevid']}',
			`uid`='{$params['uid']}',
			`platform`='{$params['platform']}',
			`ustr`='{$params['ustr']}',
			`utime`='{$utime}'
			where `acode`='{$params['acode']}' and `state`=" . self::STATE_VALID;
		return $db->query($sql);
	}
	
	public static function info($acode) {
    	$db = Common::getMyDb();
    	$acode = mysql_real_escape_string($acode);
		return $db->fetchRow("select * from `act_acode` where `acode`='{$acode}'");
	}
	
	public static function countByType($type, $uid, $sid) {
    	$db = Common::getMyDb();
    	$type = intval($type);
    	$uid = intval($uid);
    	$sid = intval($sid);
		return $db->getCount('act_acode', "`type`='{$type}' and `uid`='{$uid}' 
			and `sevid`='{$sid}' and `state`=" . self::STATE_INVALID);
	}
}