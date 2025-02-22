<?php 
/**
 * 各种活动补偿
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
Common::loadModel('HoutaiModel');
Common::loadModel('MailModel');
Common::loadModel('ServerModel');
Common::loadModel('lock/MyLockModel');
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
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    huodong_252_rwd($SevidCfg);
    huodong_255_rwd($SevidCfg);

    Master::click_destroy();
    echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
    echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
}
exit();


/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_252_rwd($SevidCfg){
    $hd_info = get255HoudongInfo();
    $key = 'huodong_252_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = MAIL_FORCES_LIST_CONTENT_HEAD.'|'.$rid.'|'.MAIL_FORCES_LIST_CONTENT_FOOT;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,MAIL_FORCES_LIST,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }

    }
}
function huodong_255_rwd($SevidCfg){
    $hd_info = get255HoudongInfo();
    $key = 'huodong_255_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key."无排行信息\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = MAIL_YINLIANG_LIST_CONTENT_HEAD.'|'.$rid.'|'.MAIL_YINLIANG_LIST_CONTENT_FOOT;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,MAIL_YINLIANG_LIST,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }

    }
}

function get252HoudongInfo() {
    return array(
        'info' => array (
            'id' => 20180669,
            'title' => '势力冲榜',
            'pindex' => 252,
            'startDay' => 0, //开服第几天开始  从1开始
            'endDay' => 0, //开服第几天结束  从1开始
            'startTime' => '2018-06-21 00:00:00',
            'endTime' => '2018-06-24 23:59:59',
            'type' => 3,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动
            'autoDay' => 24,//自动轮回间隔天数
            'autoNum' => 999,//轮回次数上限，不设置则默认999
        ),
        'showNeed' => array(
            'wang' => 1,
        ),
        'rwd' => array(
            array(
                'rand' => array('rs'=>1,'re'=>1), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 20 ),
                    array ( 'id' => 79, 'count' => 20 ),
                    array ( 'id' => 82, 'count' => 60 ),
                    array ( 'id' => 83, 'count' => 60 ),

                    array ( 'id' => 160, 'count' => 63 ),
                    array ( 'id' => 161, 'count' => 63 ),
                    array ( 'id' => 162, 'count' => 63 ),
                    array ( 'id' => 130, 'count' => 10 ),

                    array ( 'id' => 143, 'count' => 5 ),
                    array ( 'id' => 144, 'count' => 5 ),
                    array ( 'id' => 133, 'count' => 1 ),
                    array ( 'id' => 190, 'count' => 1,'kind' => 10 ),
                ),
            ),
            array(
                'rand' => array('rs'=>2,'re'=>2), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 15 ),
                    array ( 'id' => 79, 'count' => 18 ),
                    array ( 'id' => 82, 'count' => 40 ),
                    array ( 'id' => 83, 'count' => 40 ),

                    array ( 'id' => 160, 'count' => 45 ),
                    array ( 'id' => 161, 'count' => 45 ),
                    array ( 'id' => 162, 'count' => 45 ),
                    array ( 'id' => 130, 'count' => 7 ),
                ),
            ),
            array(
                'rand' => array('rs'=>3,'re'=>3), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 10 ),
                    array ( 'id' => 79, 'count' => 15 ),
                    array ( 'id' => 82, 'count' => 30 ),
                    array ( 'id' => 83, 'count' => 30 ),

                    array ( 'id' => 160, 'count' => 36 ),
                    array ( 'id' => 161, 'count' => 36 ),
                    array ( 'id' => 162, 'count' => 36 ),
                    array ( 'id' => 130, 'count' => 5 ),
                ),
            ),
            array(
                'rand' => array('rs'=>4,'re'=>5), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 8 ),
                    array ( 'id' => 79, 'count' => 10 ),
                    array ( 'id' => 82, 'count' => 20 ),
                    array ( 'id' => 83, 'count' => 20 ),

                    array ( 'id' => 160, 'count' => 27 ),
                    array ( 'id' => 161, 'count' => 27 ),
                    array ( 'id' => 162, 'count' => 27 ),
                    array ( 'id' => 130, 'count' => 4 ),
                ),
            ),
            array(
                'rand' => array('rs'=>6,'re'=>10), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 6 ),
                    array ( 'id' => 79, 'count' => 8 ),
                    array ( 'id' => 82, 'count' => 15 ),
                    array ( 'id' => 83, 'count' => 15 ),

                    array ( 'id' => 160, 'count' => 18 ),
                    array ( 'id' => 161, 'count' => 18 ),
                    array ( 'id' => 162, 'count' => 18 ),
                    array ( 'id' => 130, 'count' => 3 ),
                ),
            ),
            array(
                'rand' => array('rs'=>11,'re'=>20), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 79, 'count' => 6 ),
                    array ( 'id' => 82, 'count' => 10 ),
                    array ( 'id' => 83, 'count' => 10 ),

                    array ( 'id' => 160, 'count' => 12 ),
                    array ( 'id' => 161, 'count' => 12 ),
                    array ( 'id' => 162, 'count' => 12 ),
                    array ( 'id' => 130, 'count' => 2 ),
                ),
            ),
            array(
                'rand' => array('rs'=>21,'re'=>50), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 79, 'count' => 4 ),
                    array ( 'id' => 82, 'count' => 8 ),
                    array ( 'id' => 83, 'count' => 8 ),

                    array ( 'id' => 160, 'count' => 9 ),
                    array ( 'id' => 161, 'count' => 9 ),
                    array ( 'id' => 162, 'count' => 9 ),
                    array ( 'id' => 130, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>51,'re'=>100), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 82, 'count' => 6 ),
                    array ( 'id' => 83, 'count' => 6 ),

                    array ( 'id' => 160, 'count' => 6 ),
                    array ( 'id' => 161, 'count' => 6 ),
                    array ( 'id' => 162, 'count' => 6 ),
                    array ( 'id' => 130, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>101,'re'=>200), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 82, 'count' => 4 ),
                    array ( 'id' => 83, 'count' => 4 ),

                    array ( 'id' => 160, 'count' => 3 ),
                    array ( 'id' => 161, 'count' => 3 ),
                    array ( 'id' => 162, 'count' => 3 ),
                    array ( 'id' => 130, 'count' => 1 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    );
}
function get255HoudongInfo() {
    return array(
        'info' => array (
            'id' => 20180669,
            'title' => '银两消耗冲榜',
            'pindex' => 255,
            'startDay' => 0, //开服第几天开始  从1开始
            'endDay' => 0, //开服第几天结束  从1开始
            'startTime' => '2018-06-21 00:00:00',
            'endTime' => '2018-06-23 23:59:59',
            'type' => 3,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            'autoDay' => 24,//自动轮回间隔天数
            'autoNum' => 999,//轮回次数上限，不设置则默认999
        ),
        'showNeed' => array(
            'wang' => 7,
        ),
        'rwd' => array(
            array(
                'rand' => array('rs'=>1,'re'=>1), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 20 ),
                    array ( 'id' => 72, 'count' => 20 ),
                    array ( 'id' => 71, 'count' => 20 ),
                    array ( 'id' => 73, 'count' => 20 ),

                    array ( 'id' => 76, 'count' => 20 ),
                    array ( 'id' => 160, 'count' => 36 ),
                    array ( 'id' => 161, 'count' => 36 ),
                    array ( 'id' => 162, 'count' => 36 ),

                    array ( 'id' => 196, 'count' => 1,'kind' => 10 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 146, 'count' => 1 ),
                    array ( 'id' => 55, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>2,'re'=>2), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 15 ),
                    array ( 'id' => 72, 'count' => 15 ),
                    array ( 'id' => 71, 'count' => 15 ),
                    array ( 'id' => 73, 'count' => 15 ),

                    array ( 'id' => 76, 'count' => 15 ),
                    array ( 'id' => 160, 'count' => 27 ),
                    array ( 'id' => 161, 'count' => 27 ),
                    array ( 'id' => 162, 'count' => 27 ),
                ),
            ),
            array(
                'rand' => array('rs'=>3,'re'=>3), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 10 ),
                    array ( 'id' => 72, 'count' => 10 ),
                    array ( 'id' => 71, 'count' => 10 ),
                    array ( 'id' => 73, 'count' => 10 ),

                    array ( 'id' => 76, 'count' => 10 ),
                    array ( 'id' => 160, 'count' => 21 ),
                    array ( 'id' => 161, 'count' => 21 ),
                    array ( 'id' => 162, 'count' => 21 ),
                ),
            ),
            array(
                'rand' => array('rs'=>4,'re'=>5), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 8 ),
                    array ( 'id' => 72, 'count' => 8 ),
                    array ( 'id' => 71, 'count' => 8 ),
                    array ( 'id' => 73, 'count' => 8 ),

                    array ( 'id' => 76, 'count' => 8 ),
                    array ( 'id' => 160, 'count' => 15 ),
                    array ( 'id' => 161, 'count' => 15 ),
                    array ( 'id' => 162, 'count' => 15 ),
                ),
            ),
            array(
                'rand' => array('rs'=>6,'re'=>10), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 6 ),
                    array ( 'id' => 72, 'count' => 6 ),
                    array ( 'id' => 71, 'count' => 6 ),
                    array ( 'id' => 73, 'count' => 6 ),

                    array ( 'id' => 76, 'count' => 6 ),
                    array ( 'id' => 160, 'count' => 12 ),
                    array ( 'id' => 161, 'count' => 12 ),
                    array ( 'id' => 162, 'count' => 12 ),
                ),
            ),
            array(
                'rand' => array('rs'=>11,'re'=>20), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 4 ),
                    array ( 'id' => 72, 'count' => 4 ),
                    array ( 'id' => 71, 'count' => 4 ),
                    array ( 'id' => 73, 'count' => 4 ),

                    array ( 'id' => 76, 'count' => 4 ),
                    array ( 'id' => 160, 'count' => 9 ),
                    array ( 'id' => 161, 'count' => 9 ),
                    array ( 'id' => 162, 'count' => 9 ),
                ),
            ),
            array(
                'rand' => array('rs'=>21,'re'=>50), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 3 ),
                    array ( 'id' => 72, 'count' => 3 ),
                    array ( 'id' => 71, 'count' => 3 ),
                    array ( 'id' => 73, 'count' => 3 ),

                    array ( 'id' => 76, 'count' => 3 ),
                    array ( 'id' => 160, 'count' => 6 ),
                    array ( 'id' => 161, 'count' => 6 ),
                    array ( 'id' => 162, 'count' => 6 ),
                ),
            ),
            array(
                'rand' => array('rs'=>51,'re'=>100), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 2 ),
                    array ( 'id' => 72, 'count' => 2 ),
                    array ( 'id' => 71, 'count' => 2 ),
                    array ( 'id' => 73, 'count' => 2 ),

                    array ( 'id' => 76, 'count' => 2 ),
                    array ( 'id' => 160, 'count' => 3 ),
                    array ( 'id' => 161, 'count' => 3 ),
                    array ( 'id' => 162, 'count' => 3 ),
                ),
            ),
            array(
                'rand' => array('rs'=>101,'re'=>200), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 72, 'count' => 1 ),
                    array ( 'id' => 71, 'count' => 1 ),
                    array ( 'id' => 73, 'count' => 1 ),

                    array ( 'id' => 76, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    );
}