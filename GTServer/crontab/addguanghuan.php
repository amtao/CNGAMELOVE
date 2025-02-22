<?php 
/**
 * 统计留存用户
 * 调用方式：01 00 * * *
 * 
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('UserModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
echo PHP_EOL, 'serverID=', $serverID, PHP_EOL;

$serverList = ServerModel::getServList();

echo PHP_EOL, '----------------begin----------------------', PHP_EOL;

$btime = strtotime('now');
Common::loadModel("Master");
$result = array();

if ( is_array($serverList) ) {
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		$SevidCfg = Common::getSevidCfg($v['id']);//子服ID
		echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}

		$btime1 = strtotime('now');
		$db = Common::getDbBySevId($SevidCfg['sevid']);
		$table_div = Common::get_table_div($SevidCfg['sevid']);
		$u = array();
		// 遍历角色信息表
		for ($i = 0; $i < $table_div; $i++) {
			$table =  'hero_' . Common::computeTableId($i);
			$sql = "select `uid` from `{$table}` where `heroid` in (38,39,40,41)";
			$records = $db->fetchArray($sql);
			if(!empty($records)){
			    foreach ($records as $uids){
			        echo $uids['uid'],PHP_EOL;
			    }
			}
		}
	}
}
Master::click_destroy();

exit();
