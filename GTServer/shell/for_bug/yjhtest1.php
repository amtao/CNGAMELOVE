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



$hd_bug = 255; //对应活动序号
$text_uid = 0;  //测试uid   0:所有   111000482

$startTime = strtotime('2018-08-03 00:00:00');
$endTime = strtotime('2018-08-06 23:59:59');
//活动时间范围
switch ($start){
    case 6:
    case 7:
        $startTime = strtotime('2018-08-03 00:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 8:
    case 9:
        $startTime = strtotime('2018-08-04 00:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 10:
    case 11:
        $startTime = strtotime('2018-08-05 00:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 12:
        $startTime = strtotime('2018-08-06 00:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 13:
        $startTime = strtotime('2018-07-30 13:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 14:
        $startTime = strtotime('2018-07-31 14:30:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 15:
        $startTime = strtotime('2018-08-02 10:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 16:
        $startTime = strtotime('2018-08-03 10:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 17:
        $startTime = strtotime('2018-08-04 10:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 18:
        $startTime = strtotime('2018-08-05 10:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
    case 19:
        $startTime = strtotime('2018-08-06 10:00:00');
        $endTime = strtotime('2018-08-06 23:59:59');
        break;
}



echo '活动'.$hd_bug."进入脚本\n";
for ($i=$start; $i<=$end; $i++){
    $serverID = $i;
    echo $serverID ;
    switch ($hd_bug){

        case 201 : //活动201(元宝消耗)  查询流水  找回数据
            do_201_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 202 : //活动202(限时奖励-士兵消耗)  查询流水  找回数据
            do_202_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 203 : //活动203(限时奖励-银两消耗)  查询流水  找回数据
            do_203_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 204 : //活动204(限时奖励-强化卷轴消耗)  查询流水  找回数据
            do_204_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 206 :  //活动206(限时势力涨幅)  查询流水  找回数据
            do_206_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 208 :  //活动208(累天登陆) (先特殊处理)根据最后一次登陆 跟 活动差距多少天计算
            do_208_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 209 :  //活动209  限时奖励-衙门分数涨幅
            do_209_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 210 :  //活动210(限时联姻次数)  查询sql
            do_210_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 216 :  //活动216限时奖励-挑战书消耗
            do_216_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 250 : //帮会经验冲榜
            do_250_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case  251: //关卡冲榜
            do_251_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 252 :  //活动252  势力冲榜  =>临时处理
            do_252_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 253:
            do_253_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 255:
            do_255_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case  257: //士兵冲榜
            do_257_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 258: //魅力冲榜
            do_258_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 259: //粮食冲榜
            do_259_debug_new($serverID,$startTime,$endTime,$text_uid);
//            do_259_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 256 :  //活动256  酒楼冲榜  =>临时处理
            do_256_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 260 :  //活动260(累计充值)  查询sql
            do_260_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 261 :  //活动261(累计充值)  查询sql
            do_261_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 262 :  //活动262(累天充值)  查询sql
            do_262_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 280 ://新官上任
            do_280_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        case 282 ://惩戒来福
//            do_282_debug($serverID,$startTime,$endTime,$text_uid);
            do_282_debug_hf($serverID,$startTime,$endTime,$text_uid);
//            do_282_debug_hd($serverID,$startTime,$endTime,$text_uid);
            break;

        case 330 :  //活动330
            $serverList = ServerModel::getServList();
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

                echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
                    echo PHP_EOL, '>>>跳过', PHP_EOL;
                    continue;
                }

                do_330_debug($Sev_Cfg['sevid'],$startTime,$endTime,$text_uid);
            }
            break;
        case 310: //帮会势力冲榜
            do_310_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 311: //子嗣势力冲榜
            do_311_debug($serverID,$startTime,$endTime,$text_uid);
            break;
        case 315 :  //活动315(帮会衙门冲榜)  查询sql
            do_315_debug($serverID,$startTime,$endTime,$text_uid);
            break;

        default:
            echo '对应活动序号  输入错误!';


    }

    Master::click_destroy();
    echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
    echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
}

exit();




////活动208(累天登陆) 根据最后一次登陆 跟 活动差距多少天计算
function do_208_debug($serverID,$startTime,$endTime,$text_uid){


    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                if(Game::is_today($lastlogin)){
                    $all[$uid] =  2;
                }
            }
        }
    }


    foreach ($all as $uid => $day){

        $Act208Model = Master::getAct208($uid);
        $Act208Model->do_debug($day);
        $Act208Model->ht_destroy();

        echo $uid.': '.$day."\n";
    }


}


//活动330
function do_330_debug($serverID,$startTime,$endTime,$text_uid)
{




    $all = array();

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    //$db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);
    for ($i = 0; $i < $table_div; $i++) {

       // $where = 'uid = 1000009 and `ctrl` = hd330exchange ';

        $table = 'flow_event_' . Common::computeTableId($i);



        $sql = 'SELECT * FROM ' . $table . ' WHERE  ftime > '.$startTime." and ctrl = 'hd330exchange'";
        $db_flow = Common::getMyDb('flow');
        $data = $db_flow->fetchArray($sql);
        foreach ( $data as $item) {
            if(empty($all[$item['uid']])){
                $all[$item['uid']] = 0;
            }
            $all[$item['uid']] += 1;
        }

    }

    print_r($all);
}

//活动216 限时奖励-挑战书消耗
function do_216_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 6;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }

                        foreach ($recordData as $rk => $rv){

                            if(!in_array($rv['itemid'],array(125))){
                                continue;
                            }

                            if($rv['cha'] >= 0){
                                continue;
                            }
                            $all[$uid] += abs($rv['cha']);

                        }
                    }
                }

            }
        }
    }


    foreach ($all as $ak => $av){

        $Act216Model = Master::getAct216($ak);
        $Act216Model->do_debug($av);
        $Act216Model->ht_destroy();

        echo $ak.': '.$av."\n";
    }
}

//活动204(限时奖励-强化卷轴消耗)  查询流水  找回数据
function do_204_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 6;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }

                        foreach ($recordData as $rk => $rv){

                            if(!in_array($rv['itemid'],array(61,62,63,64))){
                                continue;
                            }

                            if($rv['cha'] >= 0){
                                continue;
                            }
                            $all[$uid] += abs($rv['cha']);

                        }
                    }
                }

            }
        }
    }


    foreach ($all as $ak => $av){

        $Act204Model = Master::getAct204($ak);
        $Act204Model->do_debug($av);
        $Act204Model->ht_destroy();

        echo $ak.': '.$av."\n";
    }
}



//活动203(限时奖励-银两消耗)  查询流水  找回数据
function do_203_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 2;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ($recordData as $rk => $rv){
                            if($rv['cha'] >= 0){
                                continue;
                            }
                            $all[$uid] += abs($rv['cha']);
                        }

                    }
                }

            }
        }
    }


    foreach ($all as $ak => $av){

        $Act203Model = Master::getAct203($ak);
        $Act203Model->do_debug($av);
        $Act203Model->ht_destroy();

        echo $ak.': '.$av."\n";
    }
}


//活动202(限时奖励-士兵消耗)  查询流水  找回数据
function do_202_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 4;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ($recordData as $rk => $rv){
                            if($rv['cha'] >= 0){
                                continue;
                            }
                            $all[$uid] += abs($rv['cha']);
                        }

                    }
                }

            }
        }
    }


    foreach ($all as $ak => $av){

        $Act202Model = Master::getAct202($ak);
        $Act202Model->do_debug($av);
        $Act202Model->ht_destroy();

        echo $ak.': '.$av."\n";
    }
}



function do_250_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db_flow = Common::getMyDb('flow');
    $redis_db = Common::getDftRedis();
    $rdata = $redis_db->zRevRange('club_redis', 0, -1,true);

    if(empty($rdata)){
       echo '暂无帮会';exit;
    }

    foreach ($rdata as $cid => $score){
        if(!empty($text_uid) && $cid != $text_uid){
            continue;
        }

        $table = 'flow_event_'.Common::computeTableId($cid);
        $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
        $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$cid.$where.' ORDER BY `id` DESC';

        $data = $db_flow->fetchArray($sql);

        if (!empty($data)){
            $id = array();
            foreach ($data as $key => $value){
                $id[] = $value['id'];
            }

            $fid = implode(',', $id);
            $type = 27;  //道具
            $table = 'flow_records_'.Common::computeTableId($cid);
            $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
            $sql .= ' and `type`='.$type;

            $recordData = $db_flow->fetchArray($sql);


            if(!empty($recordData)){
                if(empty($all[$cid])){
                    $all[$cid] = 0;
                }
                foreach ($recordData as $rk => $rv){
                    $all[$cid] += abs($rv['cha']);
                }

            }
        }
    }

    foreach ($all as $ak => $av){
        $Redis101Model = Master::getRedis101('20180731');
        $old = intval($Redis101Model->zScore($ak));
        $new = $av - $old;
        if($new > 0){
            $Redis101Model->zAdd($ak,$av);
            echo $ak.': '.$new."\n";
        }
    }

}




function do_252_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $key = 'shili_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    foreach($rdata as $uid => $score){

        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }

        echo $uid.': '.$score."\n";

        $Act252Model = Master::getAct252($uid);
        $Act252Model->do_debug(0);
        $Act252Model->ht_destroy();
    }

}


function do_253_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $key = 'huodong_253_20180801_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    foreach($rdata as $uid => $score){
        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }

        $Act205Model = Master::getAct205($uid);
        $Act253Model = Master::getAct253($uid);
        if($Act205Model->info['num'] == $Act253Model->info['num']){
            continue;
        }
        $Act253Model->info['num'] = $Act205Model->info['num'];
        $Act253Model->save();
        $Act253Model->ht_destroy();
        echo $uid.': '.$Act205Model->info['num'].': '.$Act253Model->info['num']."\n";

    }

}



function do_256_debug($serverID,$startTime,$endTime,$text_uid){
    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 19;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type.' and `cha`>0';

                    $recordData = $db_flow->fetchArray($sql);

                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ($recordData as $rk => $rv){
                            if($rv['cha']< 0){
                                continue;
                            }
                            $all[$uid] += abs($rv['cha']);
                        }

                    }
                }

            }
        }
    }


//    foreach ($all as $ak => $av){
//
//        $Act256Model = Master::getAct256($ak);
//        $Act256Model->info = array(
//            'num' => $av,
//            'id' => $Act256Model->hd_cfg['info']['id']
//        );
//        $Act256Model->save();
//        $Act256Model->ht_destroy();
//
//        $Redis110Model = Master::getRedis110($Act256Model->hd_cfg['info']['id']);
//        $Redis110Model->zAdd($ak,$av);
//
//        echo $ak.': '.$av."\n";
//    }
}

function do_210_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'son_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid` from '.$table_user .' where `sptime` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                if(empty($all[$uid])){
                    $all[$uid] = 0;
                }

                $all[$uid] += 1;

            }
        }
    }

    foreach ($all as $ak => $av){

        $Act210Model = Master::getAct210($ak);
        $Act210Model->do_debug($av);
        $Act210Model->ht_destroy();

        echo $ak.': '.$av."\n";
    }


}



function do_262_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);

    $sql_order = 'select * from `t_order` where `ptime` > '.$startTime;
    $res = $db->fetchArray($sql_order);

    if(empty($res)){
        return false;
    }

    foreach ($res as $k => $v){

        $uid = $v['roleid'];

        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }

        if(empty($all[$uid])){
            $all[$uid] = array();
        }
        $all[$uid][] += $v['ptime'];

    }

    foreach ($all as $ak  => $av){

        foreach ( $av as $time ){
            $Act262Model = Master::getAct262($ak);
            $Act262Model->do_debug($time);
            $Act262Model->ht_destroy();
        }

        echo $ak."\n";
    }


}


function do_260_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);

    $sql_order = 'select * from `t_order` where `ptime` > '.$startTime;
    $res = $db->fetchArray($sql_order);

    if(empty($res)){
        return false;
    }

    foreach ($res as $k => $v){

        $uid = $v['roleid'];

        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }

        if(empty($all[$uid])){
            $all[$uid] = 0;
        }
        $all[$uid] += intval($v['money']);

    }
    foreach ($all as $ak  => $av){

        $Act260Model = Master::getAct260($ak);
        $Act260Model->do_debug($av);
        $Act260Model->ht_destroy();

        echo $ak.': '.$av."\n";

    }


}



function do_261_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);

    $sql_order = 'select * from `t_order` where `ptime` > '.$startTime;
    $res = $db->fetchArray($sql_order);

    if(empty($res)){
        return false;
    }

    foreach ($res as $k => $v){

        $uid = $v['roleid'];

        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }

        if(empty($all[$uid])){
            $all[$uid] = 0;
        }
        $all[$uid] += intval($v['money']);

    }
    foreach ($all as $ak  => $av){

        $Act261Model = Master::getAct261($ak);
        $Act261Model->do_debug($av);
        $Act261Model->ht_destroy();

        echo $ak.': '.$av."\n";

    }


}


//活动209  限时奖励-衙门分数涨幅
function do_209_debug($serverID,$startTime,$endTime,$text_uid){


    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 20;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;
                    //$sql .= ' and `itemid`= 2 ';

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ( $recordData as $rv ) {
                            $all[$uid] += $rv['cha'];
                        }

                    }
                }

            }
        }
    }


    foreach ($all as $ak => $av){

        $Act209Model = Master::getAct209($ak);
        $Act209Model->do_debug($av);
        $Act209Model->ht_destroy();

        $Act254Model = Master::getAct254($ak);
        $Act254Model->do_debug($av);

        echo $ak.': '.$av."\n";
    }


}


//活动206(限时势力涨幅)  查询流水  找回数据
function do_206_debug($serverID,$startTime,$endTime,$text_uid){


        $all = array();


        $SevidCfg = Common::getSevidCfg($serverID);//子服ID
        $db = Common::getDbBySevId($SevidCfg['sevid']);
        $table_div = Common::get_table_div($SevidCfg['sevid']);


        for ($i = 0 ; $i < $table_div ; $i++) {

            $table_user = 'user_'.Common::computeTableId($i);
            echo $table_user."\n";
            $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
            $res = $db->fetchArray($sql_user);

            if(!empty($res)){
                foreach ($res as $val){
                    $uid = $val['uid'];

                    if(!empty($text_uid) && $uid != $text_uid){
                        continue;
                    }

                    $lastlogin = $val['lastlogin'];

                    if($lastlogin < $startTime){
                        continue;
                    }

                    $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                    $table = 'flow_event_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                    $db_flow = Common::getMyDb('flow');
                    $data = $db_flow->fetchArray($sql);

                    if (!empty($data)){
                        $id = array();
                        foreach ($data as $key => $value){
                            $id[] = $value['id'];
                        }

                        $fid = implode(',', $id);
                        $type = 7;  //道具
                        $table = 'flow_records_'.Common::computeTableId($uid);
                        $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                        $sql .= ' and `type`='.$type;
                        //$sql .= ' and `itemid`= 2 ';

                        $recordData = $db_flow->fetchArray($sql);


                        if(!empty($recordData)){

                            if(empty($all[$uid])){
                                $all[$uid] = 0;
                            }

                            $all[$uid] = $recordData[0]['next'] - $recordData[0]['cha'];

                        }
                    }

                }
            }
        }


        foreach ($all as $ak => $av){

            $Act206Model = Master::getAct206($ak);
            $Act206Model->do_debug($av);
            $Act206Model->ht_destroy();

            echo $ak.': '.$av."\n";
        }


}






//活动201(元宝消耗)  查询流水  找回数据
function do_201_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 1;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;
                    $sql .= ' and `itemid`= 2 ';

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ($recordData as $rk => $rv){
                            $all[$uid] += abs($rv['cha']);
                        }

                    }
                }

            }
        }
    }


    foreach ($all as $ak => $av){

        $Act201Model = Master::getAct201($ak);
        $Act201Model->do_debug($av);
        $Act201Model->ht_destroy();

        echo $ak.': '.$av."\n";
    }


}



//活动257(士兵消耗)  查询流水  找回数据
function do_257_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];
                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }
                $Act202Model = Master::getAct202($uid);
                if(!empty($Act202Model->info['cons'])){
                    $Redis257Model = Master::getRedis257('20180613');
                    $Redis257Model->zAdd($uid,$Act202Model->info['cons']);
                    echo $uid.': '.$Act202Model->info['cons']."\n";
                }
            }
        }
    }

}

//活动258(魅力消耗)  查询流水  找回数据
function do_258_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];
                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }
                $Act221Model = Master::getAct221($uid);
                if(!empty($Act221Model->info['cons'])){
                    $Redis258Model = Master::getRedis258('20180106');
                    $Redis258Model->zAdd($uid,$Act221Model->info['cons']);
                    echo $uid.': '.$Act221Model->info['cons']."\n";
                }
            }
        }
    }
}


//活动259(粮食消耗)  查询流水  找回数据
function do_259_debug_new($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $key = 'huodong_259_20180801_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    foreach($rdata as $uid => $score){
        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }
        $score = intval($score);
        echo $uid.': '.$score."\n";
        $Redis259Model = Master::getRedis259('20180102');
        $Redis259Model->zIncrBy($uid,$score);
    }

}

//活动259(粮食消耗)  查询流水  找回数据
function do_259_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];
                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }
                $Act226Model = Master::getAct226($uid);
                if(!empty($Act226Model->info['cons'])){
                    $Redis259Model = Master::getRedis259('20180106');
                    $Redis259Model->zAdd($uid,$Act226Model->info['cons']);
                    echo $uid.': '.$Act226Model->info['cons']."\n";
                }
            }
        }
    }
}

//活动315(帮会衙门冲榜)  查询流水  找回数据
function do_315_debug($serverID,$startTime,$endTime,$text_uid){
    $all = array();
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db_flow = Common::getMyDb('flow');
    $redis_db = Common::getDftRedis();
    $rdata = $redis_db->zRevRange('club_redis', 0, -1,true);

    if(empty($rdata)){
        echo '暂无帮会';exit;
    }

    foreach ($rdata as $cid => $score){
        if(!empty($text_uid) && $cid != $text_uid){
            continue;
        }

        $table = 'flow_event_'.Common::computeTableId($cid);
        $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
        $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$cid.$where.' ORDER BY `id` DESC';

        $data = $db_flow->fetchArray($sql);

        if (!empty($data)){
            $id = array();
            foreach ($data as $key => $value){
                $id[] = $value['id'];
            }

            $fid = implode(',', $id);
            $type = 69;  //道具
            $table = 'flow_records_'.Common::computeTableId($cid);
            $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
            $sql .= ' and `type`='.$type;

            $recordData = $db_flow->fetchArray($sql);


            if(!empty($recordData)){
                if(empty($all[$cid])){
                    $all[$cid] = 0;
                }
                foreach ($recordData as $rk => $rv){
                    $all[$cid] += $rv['cha'];
                }

            }
        }
    }


    foreach ($all as $ak => $av){
        $Redis315Model = Master::getRedis315('20180731');
        $old = intval($Redis315Model->zScore($ak));
        $new = $av - $old;
        if($new > 0){
            $Redis315Model->zAdd($ak,$av);
            echo $ak.': '.$new."\n";
        }

    }

}

//活动280(新官上任) 从改错的id排行榜中转移到总榜内  找回数据
function do_280_debug($serverID,$startTime,$endTime,$text_uid){
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $key = 'huodong_280_my_20180726_redis';
    $club_key = 'huodong_280_club_20180726_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(!empty($rdata)){
        $Redis106Model = Master::getRedis106('20180101');
        foreach ($rdata as $uid => $num){
            if(!empty($text_uid) && $text_uid != $uid){
                continue;
            }
            $num = intval($num);
            //加上排行分数
            $Redis106Model->zIncrBy($uid,$num);
            $Act103Model = Master::getAct103($uid);
            $Act103Model->info['hdscore'] = intval($Redis106Model->zScore($uid));
            $Act103Model->save();
            $Act103Model->ht_destroy();
            echo $uid.':'.$num.':'.intval($Redis106Model->zScore($uid)),PHP_EOL;
        }
    }

    //帮会
    $rdata1  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
    if(!empty($rdata1)){
        $Redis107Model = Master::getRedis107('20180101');
        foreach ($rdata1 as $cid => $num){
            if(!empty($text_uid) && $text_uid != $cid){
                continue;
            }
            $num = intval($num);
            $Redis107Model->zIncrBy($cid,$num);
            echo $cid.':'.$num,PHP_EOL;
        }
    }

}

function do_282_debug_hd($serverID,$startTime,$endTime,$text_uid){
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $key = 'huodong_282_my_20180801_redis';
    $club_key = 'huodong_282_club_20180801_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(!empty($rdata)){
        $Redis112Model = Master::getRedis112('20180102');
        foreach ($rdata as $uid => $num){
            if(!empty($text_uid) && $text_uid != $uid){
                continue;
            }
            $num = intval($num);
            //加上排行分数
            $Redis112Model->zIncrBy($uid,$num);
            $Act107Model = Master::getAct107($uid);
            $Act107Model->info['hdscore'] = intval($Redis112Model->zScore($uid));
            $Act107Model->save();
            $Act107Model->ht_destroy();
            echo $uid.':'.$num.':'.intval($Redis112Model->zScore($uid)),PHP_EOL;
        }
    }

    //帮会
    $rdata1  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
    if(!empty($rdata1)){
        $Redis113Model = Master::getRedis113('20180102');
        foreach ($rdata1 as $cid => $num){
            if(!empty($text_uid) && $text_uid != $cid){
                continue;
            }
            $num = intval($num);
            $Redis113Model->zIncrBy($cid,$num);
            echo $cid.':'.$num,PHP_EOL;
        }
    }

}

function do_282_debug_hf($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);

    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 30;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type.' and `cha`>0';

                    $recordData = $db_flow->fetchArray($sql);

                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ($recordData as $rk => $rv){
                            if($rv['cha']< 0){
                                continue;
                            }
                            $all[$uid] += abs($rv['cha']);
                        }

                    }
                }
            }
        }
    }

    if(!empty($all)){
        foreach ($all as $uid => $num){
            $Act107Model = Master::getAct107($uid);
            $Act107Model->info['hfscore'] = $num;
            $Act107Model->save();
            $Act107Model->ht_destroy();
            echo $uid.':'.$num,PHP_EOL;
        }
    }
}

//活动282(惩戒来福)  查询流水  找回数据
function do_282_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];
                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }
                $Act107Model = Master::getAct107($uid);
                if(!empty($Act107Model->info['hdscore'])){
                    $Redis112Model = Master::getRedis112('20180611');
                    $old_score = intval($Redis112Model->zScore($uid));
                    if($Act107Model->info['hdscore'] > $old_score){
                        $incr = $Act107Model->info['hdscore'] - $old_score;
                        $Redis112Model->zIncrBy($uid,$incr);
                        //添加联盟排名信息
                        $Act40Model = Master::getAct40($uid);
                        if(!empty($Act40Model->info['cid'])){
                            $Redis113Model = Master::getRedis113('20180611');
                            $Redis113Model->zIncrBy($Act40Model->info['cid'],$incr);
                        }
                        echo $uid.': '.$incr."\n";
                    }
                }
            }
        }
    }
}
//活动251(关卡冲榜)  查询流水  找回数据
function do_251_debug($serverID,$startTime,$endTime,$text_uid){
    $all = array();
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $redis_db = Common::getDftRedis();
    $rdata = $redis_db->zRevRange('guanka_redis', 0, -1,true);

    if(empty($rdata)){
        echo '暂无帮会';exit;
    }
    $Redis102Model = Master::getRedis102('20180101');
    foreach ($rdata as $uid => $score){

        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }
        $Redis102Model->zAdd($uid,$score);
        echo $uid.': '.$score."\n";
    }
}



//活动311(子嗣势力冲榜)  查询流水  找回数据
function do_311_debug($serverID,$startTime,$endTime,$text_uid){

    $all = array();


    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db = Common::getDbBySevId($SevidCfg['sevid']);
    $table_div = Common::get_table_div($SevidCfg['sevid']);


    for ($i = 0 ; $i < $table_div ; $i++) {

        $table_user = 'user_'.Common::computeTableId($i);
        echo $table_user."\n";
        $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
        $res = $db->fetchArray($sql_user);

        if(!empty($res)){
            foreach ($res as $val){
                $uid = $val['uid'];

                if(!empty($text_uid) && $uid != $text_uid){
                    continue;
                }

                $lastlogin = $val['lastlogin'];

                if($lastlogin < $startTime){
                    continue;
                }

                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;

                $table = 'flow_event_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
                $db_flow = Common::getMyDb('flow');
                $data = $db_flow->fetchArray($sql);

                if (!empty($data)){
                    $id = array();
                    foreach ($data as $key => $value){
                        $id[] = $value['id'];
                    }

                    $fid = implode(',', $id);
                    $type = 44;  //道具
                    $table = 'flow_records_'.Common::computeTableId($uid);
                    $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                    $sql .= ' and `type`='.$type;

                    $recordData = $db_flow->fetchArray($sql);


                    if(!empty($recordData)){

                        if(empty($all[$uid])){
                            $all[$uid] = 0;
                        }
                        foreach ($recordData as $rk => $rv){
                            $all[$uid] += abs($rv['cha']);
                        }

                    }
                }

            }
        }
    }

    $Redis311Model = Master::getRedis311('20170810');
    foreach ($all as $ak => $av){
        $old = intval($Redis311Model->zScore($ak));
        if($av > $old){
            $Redis311Model->zAdd($ak,$av);
            echo $ak.': '.$old.'-----'.$av."\n";
        }
    }


}


//活动310(帮会衙门冲榜)  查询流水  找回数据
function do_310_debug($serverID,$startTime,$endTime,$text_uid){
    $all = array();
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $db_flow = Common::getMyDb('flow');
    $redis_db = Common::getDftRedis();
    $rdata = $redis_db->zRevRange('club_redis', 0, -1,true);

    if(empty($rdata)){
        echo '暂无帮会';exit;
    }

    foreach ($rdata as $cid => $score){
        if(!empty($text_uid) && $cid != $text_uid){
            continue;
        }

        $table = 'flow_event_'.Common::computeTableId($cid);
        $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
        $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$cid.$where.' ORDER BY `id` DESC';

        $data = $db_flow->fetchArray($sql);

        if (!empty($data)){
            $id = array();
            foreach ($data as $key => $value){
                $id[] = $value['id'];
            }

            $fid = implode(',', $id);
            $type = 42;  //道具
            $table = 'flow_records_'.Common::computeTableId($cid);
            $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
            $sql .= ' and `type`='.$type;

            $recordData = $db_flow->fetchArray($sql);


            if(!empty($recordData)){
                if(empty($all[$cid])){
                    $all[$cid] = 0;
                }
                foreach ($recordData as $rk => $rv){
                    $all[$cid] += $rv['cha'];
                }

            }
        }
    }


    foreach ($all as $ak => $av){
        $Redis310Model = Master::getRedis310('20180122');
        $old = intval($Redis310Model->zScore($ak));
        $new = $av - $old;
        if($new > 0){
            $Redis310Model->zAdd($ak,$av);
            echo $ak.': '.$old.'-----'.$av."\n";
        }

    }

}

function do_255_debug($serverID,$startTime,$endTime,$text_uid){

    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $key = 'huodong_255_20180726_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    foreach($rdata as $uid => $score){
        if(!empty($text_uid) && $uid != $text_uid){
            continue;
        }
        $Act203Model = Master::getAct203($uid);
        $Act255Model = Master::getAct255($uid);
        if($Act203Model->info['cons'] == $Act255Model->info['num']){
            continue;
        }
        $Act255Model->info['num'] = 0;
        $Act255Model->do_save($Act203Model->info['cons']);
        $Act255Model->ht_destroy();
        echo $uid.': '.$Act203Model->info['cons'].': '.$Act255Model->info['num']."\n";

    }

}








