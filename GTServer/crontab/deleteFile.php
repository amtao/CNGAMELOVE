<?php 
/**
 * 定时删除脚本
 * 调用方式：每天凌晨4:30执行
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
define('DELETE_MAIL_LIMIT_DAY', 7);
define('DELETE_ACODE_LIMIT_DAY', 7);
Common::loadModel('ServerModel');
Common::loadModel('DeleteModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
echo PHP_EOL, 'serverID=', $serverID, PHP_EOL;

$serverList = ServerModel::getServList();
$defaultSid = ServerModel::getDefaultServerId();
$btime = microtime(true);
echo PHP_EOL, '----------------默认服务器'.$defaultSid.'----------------------', PHP_EOL;
echo PHP_EOL, '----------------begin----------------------', PHP_EOL;

$date = date('ymd',strtotime('-7 day', time()));

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

		delDirAndFile("/data/logs/con/".$date, true);
	}
}

 /** 
 * 删除目录及目录下所有文件或删除指定文件 
 * @param str $path   待删除目录路径 
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录） 
 * @return bool 返回删除状态 
 */ 
function delDirAndFile($path, $delDir = FALSE) { 
    $handle = opendir($path); 
    if ($handle) { 
        while (false !== ( $item = readdir($handle) )) { 
            if ($item != "." && $item != "..") 
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        } 
        closedir($handle); 
        if ($delDir) 
            return rmdir($path); 
    }else { 
        if (file_exists($path)) { 
            return unlink($path); 
        } else { 
            return FALSE; 
        } 
    } 
}


echo PHP_EOL, '----------------end----------------------', PHP_EOL;
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
exit();
