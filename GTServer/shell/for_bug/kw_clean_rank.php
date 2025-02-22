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
if (empty($start) || empty($end)){
    echo "请传入起始跟结束参数!";
    exit();
}
Common::loadModel("Master");

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;



$hd_bug = 310; //对应活动序号
$text_uid = 0;  //测试uid   0:所有   111000482

//活动时间范围
$startTime = strtotime('2018-05-06 00:00:00');
$endTime = strtotime('2018-05-08 23:59:59');


echo '活动'.$hd_bug."进入脚本\n";
for ($i=$start; $i<=$end; $i++){
    $serverID = $i;
    echo $serverID ;
    switch ($hd_bug){
        case 250:
            do_250_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 310:
            do_310_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        default:
            echo '对应活动序号  输入错误!';
    }

    Master::click_destroy();
    echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
    echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
}

exit();

/**
 * 清除帮会经验冲榜
 * @param $serverID
 * @param $startTime
 * @param $endTime
 * @param $text_uid
 */
function do_250_debug($serverID,$startTime,$endTime,$text_uid)
{
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID

    if ($serverID == 1) {
        $uidInfo = array(1007637, 1010778, 1012003, 1016604, 1021270, 1019841, 2007070);
        $res = array(10041);
    } else if ($serverID == 144)  {
        $uidInfo = array(144000014, 144000039);
        $res = array(1440001);
    } else {
        exit('错误');
    }
    foreach ($res as $cid){
        $table = 'flow_event_'.Common::computeTableId($cid);

        $where = " and `model`='ClubModel' and `ctrl`='add_exp'";
        $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$cid.$where;
        $db_flow = Common::getMyDb('flow');
        $data = $db_flow->fetchArray($sql);
        foreach ($data as $d) {
            $params = json_decode($d['params'], 1);
            foreach ($params as $uu_uid => $uu_score) {
                //过滤不清楚的账号
                if (!in_array($uu_uid, $uidInfo)) {
                    continue;
                }

                if (0) {
                    //扣积分
                    $Act250Model = Master::getAct250($uu_uid);
                    $Act250Model->do_save($cid, -$uu_score);
                }
            }
        }
    }
}

/**
 * 清除帮会势力冲榜
 * @param $serverID
 * @param $startTime
 * @param $endTime
 * @param $text_uid
 */
function do_310_debug($serverID,$startTime,$endTime,$text_uid)
{
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID

    if ($serverID == 131) {
        $uidInfo = array(131000167, 131000124, 131000294, 131000872);
        $res = array(1310001);
    } else {
        exit('错误');
    }
    foreach ($res as $cid){
        $table = 'flow_event_'.Common::computeTableId($cid);

        $where = " and `model`='club' and `ctrl`='huodong_310_20180515'";
        $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$cid.$where;
        $db_flow = Common::getMyDb('flow');
        $data = $db_flow->fetchArray($sql);

        $id = array();
        foreach ($data as $d) {
            //过滤不清楚的账号
            $params = json_decode($d['params'], 1);
            $uu_uid = $params[0];
            if (!in_array($uu_uid, $uidInfo)) {
                continue;
            }

            $id[$uu_uid][] = $d['id'];
        }

        foreach ($id as $uu_uid => $id_v) {
            $fid = implode(',', $id_v);
            $table = 'flow_records_'.Common::computeTableId($cid);
            $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.');';
            $recordData = $db_flow->fetchArray($sql);
            $cha = 0;
            if(!empty($recordData)){
                foreach ($recordData as $rk => $rv){
                    if($rv['cha'] >= 0){
                        continue;
                    }
                    $cha += abs($rv['cha']);
                }
            }
            if ($cha > 0) {
                echo "uid:{$uu_uid}-score:{$cha}";
                if (0) {
                    //扣积分
                    $Act310Model = Master::getAct310($uu_uid);
                    $Act310Model->do_save(-$cha);
                }
            }
        }
    }

}