<?php
/**
 * 冲榜活动错误订正
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$params = array(
    'hd' => '314',
    'hd_id' => '20180421',
    'stime' => '2018-04-23 00:00:00',
    'etime' => '',//不写就是跑到现在
    'sevid' => $serverID,
);

modify_data($params);

function modify_data($params){
    if(empty($params['stime'])){
        echo '开始时间不能为空';
        exit;
    }
    if(empty($params['sevid'])){
        echo '请输入需要跑的区服';
        exit;
    }
    if(empty($params['etime'])){
        $params['etime'] = Game::get_now();
    }else{
        $params['etime'] = strtotime($params['etime']);
    }
    $params['stime'] = strtotime($params['stime']);

    if($params['etime'] <= $params['stime']){
        echo '结束时间必须大于开始时间';
        exit;
    }
    switch ($params['hd']){
        case 253://亲密冲榜
            hd253($params);
            break;
        case 314://跨服亲密
            $params['yu_id'] = '20180421';//预选赛排行榜id
            $params['yu_get_num'] = 100;//预选赛选取人数
            hd314($params);
            break;
        default:
            echo '该活动未收录信息';
            break;
    }
}

function hd314($params){

    $SevidCfg = Common::getSevidCfg($params['sevid']);//子服ID
    $redis_db = Common::getDftRedis();
    $key = 'huodong_253_'.$params['yu_id'].'_redis';
    $data = $redis_db->zRevRange($key, 0, -1);
    $uids = array();
    if(!empty($data)){
        foreach ($data as $k => $uid){
            if($k >= $params['yu_get_num']){
                continue;
            }
            if(Game::get_sevid($uid) != $params['sevid']){
                continue;
            }
            $uids[] = $uid;
        }
    }
    $all = array();
    if(!empty($uids)){
        foreach ($uids as $uid){

            if(!empty($text_uid) && $uid != $text_uid){
                continue;
            }
            $table_a = 'flow_event_'.Common::computeTableId($uid);
            $table_b = 'flow_records_'.Common::computeTableId($uid);

            $where = "a.uid={$uid} and b.type=15 and a.ftime > {$params['stime']} AND a.ftime < {$params['etime']}";
            $sql = "select a.uid,SUM(b.cha) as cha from `{$table_a}` a JOIN `{$table_b}` b ON a.id = b.flowid WHERE {$where}";
            $db_flow = Common::getMyDb('flow');
            $data = $db_flow->fetchArray($sql);
            if(!empty($data)){
                foreach ($data as $v){
                    if(empty($v['uid'])){
                        continue;
                    }
                    $all[$v['uid']] = $v['cha'];
                }
            }
        }
    }
    print_r($all);
    $Redis137Model = Master::getRedis137($params['hd_id']);
    $serv_he = Common::getSevCfgObj($params['sevid'])->getHE();
    $Redis138Model = Master::getRedis138($params['hd_id']);
    $cha = array();
    foreach ($all as $uid => $score){
        $now = $Redis137Model->zScore($uid);
        echo $uid.'----'.$score.'----'.$now.'---'.($score-$now),PHP_EOL;
        if($score <= intval($now)){
            continue;
        }
        $num = $score-intval($now);
        $cha[$uid] = $num;

        $Redis137Model->zIncrBy($uid,$num);
        //区间 pk区服 排行榜  (区服为单位)   =>   整个区奖励
        $Redis138Model->zIncrBy($serv_he,$num);
    }

    Game::logMsg('/data/logs/hd314Modify',json_encode($cha));
}




function hd253($params){
//    $text_uid = 19001819;
    $SevidCfg = Common::getSevidCfg($params['sevid']);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $all = array();
    for ($i = 0 ; $i < 100 ; $i++) {
        if($i < 10){
            $table_user = 'user_0'.$i;
        }else{
            $table_user = 'user_'.$i;
        }
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$params['stime'];
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }
                $table_a = 'flow_event_'.Common::computeTableId($uid);
                $table_b = 'flow_records_'.Common::computeTableId($uid);

                $where = "a.uid={$uid} and b.type=15 and a.ftime > {$params['stime']} AND a.ftime < {$params['etime']}";
                $sql = "select a.uid,SUM(b.cha) as cha from `{$table_a}` a JOIN `{$table_b}` b ON a.id = b.flowid WHERE {$where}";
//                echo $sql,PHP_EOL;
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);
                if(!empty($data)){
                    foreach ($data as $v){
                        if(empty($v['uid'])){
                            continue;
                        }
                        $all[$val['uid']] = $v['cha'];
                    }
                }
            }
        }
    }
    print_r($all);
    Game::logMsg('/data/logs/hd253Modify',json_encode($all));
    $Redis3Model = Master::getRedis3();

    foreach ($all as $uid => $score){
        $all_score = $Redis3Model->zScore($uid);
        if($all_score <= $score){
            continue;
        }

        $Redis104Model = Master::getRedis104($params['hd_id']);
        $Redis104Model->zAdd($uid,$score);

        $Act253Model = Master::getAct253($uid);
        $Act253Model->info = array(
            'num' => intval($all_score)-intval($score),
            'id' => $params['hd_id'],
        );
        $Act253Model->save();
        $Act253Model->ht_destroy();
    }
}

