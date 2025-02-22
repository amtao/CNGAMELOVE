<?php
//掌娱
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

Common::loadModel('ServerModel');
$sList = ServerModel::getServList();
$url = 'http://api.wyx365.com/api/opgroup/open_plan.php';
$game_id = 186;
$key = '57cd0d955012fd36f4d96146469849ba';
foreach ($sList as $v) {
    //未开服和测试服不提交
    if($v['showtime']< time() && $v['id'] != 999) {
        $time = time();
        $sign = md5($game_id.$time.$key);
        $data = array(
            'game_id' => 186,
            'server_id' => $v['id'],
            'version_id' => 3,
            'open_date' => date('Y-m-d',$v['showtime']),
            'open_time' => date('H:i:s',$v['showtime']),
        );
        $data = json_encode(array($data));
        $params = array(
            'game_id' => $game_id,
            'time' => $time,
            'sign' => $sign,
            'data' => $data
        );
        $result = Common::request($url,$params);
        echo '-------------------------'.$v['id'].'区完成发送'.$result.'-----------------------'.PHP_EOL;

    }
}

echo "---------------------完成，哇哈哈哈----------------------------".PHP_EOL;