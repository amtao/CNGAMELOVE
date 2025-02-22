<?php
//跨服衙门出现bug备用
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
//$min = 1;
//$max = 125;

addData();

//change($min,$max);

//redistrans($min,$max);

exit();
//
//function modify(){
//    $new_sid = 28;
//    $old_sid = 33;
//    $tran_sid = array(34,35);
//    $hd_id = 20180203;
//    /**
//     * 第一步 ： 获取旧的数据
//     * 1、获取当前区服的排行榜
//     * 2、获取当前区间个人排行榜
//     * 3、获取可以参赛的排行榜
//     * 4、删除个人redis
//     *
//     * 第二步: 添加数据
//     * 1、总积分想加
//     * 2、个人积分想加
//     * 3、添加参赛人员排行
//     */
//
//    //key
//    $qu_key = 'huodong_300_sever_'.$hd_id.'_redis';
//    $score_key = 'huodong_300_score_'.$hd_id.'_redis';
//    foreach ($tran_sid as $sid){
//        $my_key_arr[$sid] = 'huodong_300_my_'.$hd_id.'_'.$sid.'_redis';
//    }
//
//    //第一步
//    $Sev_Cfg = Common::getSevidCfg($old_sid);//子服ID
//    echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
//    $old_redis = Common::getDftRedis();
//    //1.1
//    $old_qu_data = $old_redis->zRevRange($qu_key, 0, -1,true);
//    //1.2
//    $old_score_data = $old_redis->zRevRange($score_key, 0, -1,true);
//    Game::logMsg('/tmp/kuayamen',json_encode($old_score_data));
//    //1.3
//    foreach ($my_key_arr as $sid => $key){
//        $old_my_data[$sid] = $old_redis->zRevRange($key, 0, -1,true);
//    }
//
//    //第二步
//    $Sev_Cfg = Common::getSevidCfg($new_sid);//子服ID
//    echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
//    $new_redis = Common::getDftRedis();
//
//    //2.1
//    if(!empty($old_qu_data)){
//        foreach ($old_qu_data as $sid => $score){
//            if(!in_array($sid,$tran_sid)){
//                continue;
//            }
//            $new_redis->zIncrBy($qu_key,$score,$sid);
//        }
//    }
//    //2.2
//    if(!empty($old_score_data)){
//        foreach ($old_score_data as $uid => $score){
//            $new_redis->zIncrBy($score_key,$score,$uid);
//        }
//    }
//    //2.3
//    if(!empty($old_my_data)){
//        foreach ($old_my_data as $sid => $val){
//            foreach ($val as $uid => $score){
//                $new_redis->zIncrBy($my_key_arr[$sid],$score,$uid);
//            }
//        }
//    }
//}

function addData(){
    $Sev_Cfg = Common::getSevidCfg(1);//子服ID
    echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
    $Sev61Model = Master::getSev61();
    $hd_id = $Sev61Model->info['id'];
    $Sev63Model = Master::getSev63();
    $Sev63Model->info = array(
        'before' => 0,
        'next' => $hd_id,
    );
    print_r($Sev63Model->info);
    $Sev63Model->save();
    echo '结束',PHP_EOL;
}
//
//function change($min,$max){
//    $hd_id = 20171228;//20171228
//    $limit=3;
//    $max_rank = $max;
//    $server =  array(
//        array('mi'=>1,'ma'=>84),
//        array('mi'=>85,'ma'=>125)
//    );
//    $key = '';
//    $list = array();
//
//    //获取服务器列表
//    Common::loadModel('ServerModel');
//    $serverList = ServerModel::getServList();
//
//    for($sid = $min; $sid <= $max; $sid++){
//
//
//        $Sev_Cfg = Common::getSevidCfg($sid);//子服ID
//        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
//
//        //上一轮的排行信息
//        $Redis304Model = Master::getRedis304($hd_id);
//        $before_list = $Redis304Model->out_redis();
//        if(!empty($before_list)){
//            foreach ($before_list as $v){
//                if(!empty($server)){
//                    foreach ($server as $k => $val){
//                        if($v['sid'] >= $val['mi'] && $v['sid'] <= $val['ma']){
//                            $SevCfgObj = Common::getSevCfgObj($v['sid']);
//                            $he = $SevCfgObj->getHE();
//                            if(!in_array($he,$list[$k])){
//                                $list[$k][] = $he;
//                            }
//                            break;
//                        }
//                    }
//                }
//            }
//        }
//
//        $count = 0;
//        if($max_rank > $count) {
//
//            for($i = $count+1;$i<=$max_rank;$i++){
//                if(empty($serverList[$i])){
//                    continue;
//                }
//                if(!empty($server)){
//                    foreach ($server as $k => $val){
//                        if($i >= $val['mi'] && $i <= $val['ma']){
//                            $SevCfgObj = Common::getSevCfgObj($i);
//                            $he = $SevCfgObj->getHE();
//                            if(!in_array($he,$list[$k])){
//                                $list[$k][] = $he;
//                            }
//                            break;
//                        }
//                    }
//                }
//            }
//        }
//
//        if(!empty($list)){
//            foreach ($list as $ks => $sids){
//                foreach ($sids as $k => $sid){
//                    $num = intval($k/$limit);
//                    $result[$ks][$num][] = $sid;
//                }
//            }
//            $res = array();
//            if(!empty($result)){
//                foreach ($result as $ks => $val){
//                    foreach ($val as $sids){
//                        sort($sids,SORT_NUMERIC);
//                        $res[] = $sids;
//                    }
//                }
//            }
//            $Sev61Model = Master::getSev61();
//            $Sev61Model->info  = array(
//                'id' => $hd_id,
//                'list' => $res
//            );
//            print_r($Sev61Model->info);die;
////            $Sev61Model->save();
////            //初始化
////            $Redis305Model = Master::getRedis305($hd_id);
////            $Redis305Model->del_key();
////            foreach ($res as $v){
////                if(in_array($Sev_Cfg['he'],$v)){
////                    foreach ($v as $sid){
////                        $Redis305Model->zAdd($sid, 0);
////                    }
////                }
////            }
//        }
//    }
//}

//function redistrans($min,$max){
//    $hd_id = 20181001;
//    $key = 'huodong_254_20181001_redis';
//    for($sid = $min; $sid <= $max; $sid++) {
//        $Sev_Cfg = Common::getSevidCfg($sid);//子服ID
//        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
//
//        $redis = Common::getDftRedis();
//        $rdata = $redis->zRevRange($key, 0, -1, true);  //获取排行数据
//
//        $Redis307Model = Master::getRedis307($hd_id.'_'.$Sev_Cfg['he']);
//        foreach ($rdata as $uid => $score) {
//            $Redis307Model->zAdd($uid, 0);
//        }
//    }
//}