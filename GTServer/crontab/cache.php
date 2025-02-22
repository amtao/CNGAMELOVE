<?php
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$listKey = 'crontab:testServerList';
$config = Common::getConfig(GAME_MARK."/AllServerMemConfig");
if ( is_array($config) ) {
    $commonCache = Common::getCacheBySevId(1);
    foreach ($config as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if ($k == 999) {
            continue;
        }
        if (!isset($v['host'])) {
            $v = $v[0];
        }
        $cache = Common::getCacheBySevId($k);
        $beginTime = microtime(true);
        $cache->set('crontab:testCache',1);
        $time = microtime(true) - $beginTime;
        if ( $time > 0.01){
            $data = $commonCache->get($listKey);
            $v['dateTime'] = date("Y-m-d H:i:s");
            $v['useTime'] = $time;
            $v['serverId'] = $k;
            $data[md5($v['host'].$v['port'])] = $v;
            foreach ($data as $key => $value){
                $time = strtotime($v['dateTime'])-strtotime($value['dateTime']);
                if ($time>86400*2){
                    unset($data[$key]);
                }
            }
            $commonCache->set($listKey, $data);
        }
    }
}
echo PHP_EOL, '----------------end:'.microtime(true).'----------------------', PHP_EOL;
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
exit();

