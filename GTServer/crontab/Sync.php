<?php
/**
 * 定时计划异步脚本
 * 调用方式：* * * * * /usr/local/php/bin/php /data/www/guaji/crontab/Sync.php 1 > /data/logs/guaji_log/guaji_s999_1_sync_all 2>&1
 */
set_time_limit(0);
$start = microtime(true);

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('FlowModel');
Common::loadActModel('ActBaseModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

//echo var_export($serverList, 1);
echo PHP_EOL, '----------------begin sync----------------------', PHP_EOL;

Common::loadLib('sync');
$keys = array('user', 'act', 'item' ,'hero','wife','son','mail','acode','huodong','card','baowu');
$time1 = microtime(true);
$SevidCfg = Common::getSevidCfg($serverID);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;

foreach ($keys as $key) {
	echo '流水纪录：',$key ,PHP_EOL;
    if ($key == 'act') {
        foreach (ActBaseModel::$rightActTypes as $actType => $actComments) {
            $updateNum += Sync::doSync('1_'.$key.'_'.$actType);
        }
    } else {
        $updateNum += Sync::doSync('1_' . $key);
    }
}

echo '异步写入：' ,PHP_EOL;
//异步写入
//$flowRecordNum += FlowModel::sync();

$time2 = microtime(true);
echo PHP_EOL, '>>>执行完毕。time=', $time2-$time1, PHP_EOL;
		
		
echo PHP_EOL, '全部执行完毕。time=', microtime(true)-$start, PHP_EOL;
echo PHP_EOL,'更新条数：' ,$updateNum, PHP_EOL;
//echo '流水纪录：',$flowRecordNum ,PHP_EOL;

echo PHP_EOL, '----------------end sync----------------------', PHP_EOL;
exit();
