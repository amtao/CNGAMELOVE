<?php 
/**
 * 各种活动补偿
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$start = intval($_SERVER['argv'][1]);
$end = intval($_SERVER['argv'][2]);
if(empty($end)){//没有第二个参数的话就是单服
    $end = $start;
}
if (empty($start) || empty($end)){
    echo "请传入起始跟结束参数!";
    exit();
}
Common::loadModel("Master");

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;



$hd_bug = 201; //限时活动
$text_uid = 0;  //测试uid   0:所有   111000482

//活动时间范围
$startTime = strtotime('2018-08-06 00:00:00');
$endTime = strtotime('2018-08-06 23:59:59');


echo '活动'.$hd_bug."进入脚本\n";
for ($i=$start; $i<=$end; $i++){
    $serverID = $i;
    echo $serverID ;
    switch ($hd_bug){

        case 201 : //活动201(元宝消耗)  查询流水  找回数据
            do_201_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        default:
            echo '对应活动序号  输入错误!';
    }

    Master::click_destroy();
    echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
    echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
}
exit();



//限时
function do_201_debug($serverID,$startTime,$endTime,$text_uid){

    //huodong_206_20180726_redis
    //huodong_206_20180101_redis
    //201，202，203，204，206，207，208，209，210，211
    //huodong_205_20180801_redis
    //huodong_205_20180102_redis
    //201，205，206，208，213\221\226

    $cfg = array(
        array(
            'sid'=>array(6, 12),
            's'=>array(201, 205, 206, 208, 213, 221, 226),
            'nHid'=>'20180102',
            'oHid'=>'20180801',
        ),
        array(
            'sid'=>array(13, 19),
            's'=>array(201,202,203,204,206,207,208,209,210,211),
            'nHid'=>'20180101',
            'oHid'=>'20180726',
        ),
    );

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    foreach ($cfg as $v) {
        echo "server id {$serverID}:\n";
        if ($serverID >= $v['sid'][0] && $serverID <= $v['sid'][1]) {
            foreach ($v['s'] as $hid) {
                echo "hid id {$hid}:\n";
                $oRedisModel = Master::getRedis($hid, $v['oHid']);
                $oldList = $oRedisModel->zRevRange(true);
                $nRedisModel = Master::getRedis($hid, $v['nHid']);
                $newList = $nRedisModel->zRevRange(true);
                /*
                foreach ($newList as $uid => $s) {
                    if (isset($oldList[$uid])) {
                        $newList[$uid] += $oldList[$uid];
                    }
                }
                */
                foreach ($oldList as $uid => $av) {
                    $score = intval($av);
                    if (isset($newList[$uid])) {
                        $score += (int)$newList[$uid];
                    }
                    $actName = "getAct{$hid}";
                    $ActModel = Master::$actName($uid);
                    $ActModel->do_debug($score);
                    $ActModel->ht_destroy();
                    echo $uid . ': ' . $score . "\n";
                }
            }
        }
    }
}

