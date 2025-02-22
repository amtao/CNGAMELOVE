<?php
/**
 * 流水写入脚本
 * 调用方式：* * * * * /usr/local/php/bin/php /data/www/yipin/crontab/flow.php 1 > /data/logs/yipin_log/flow.log 2>&1
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '----------------begin:'.$btime.'----------------------', PHP_EOL;
Common::loadModel('ServerModel');
Common::loadModel('FlowModel');


$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$SevidCfg = Common::getSevidCfg($serverID);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg['sevid']) {
    echo PHP_EOL, '>>>跳过', PHP_EOL;
    exit();
}
FlowModel::sync();
        
echo PHP_EOL, '----------------end:'.microtime(true).'----------------------', PHP_EOL;
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
exit();

