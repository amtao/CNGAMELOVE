<?php
/***/
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$type = intval($_SERVER['argv'][1]);// 默认是全部区
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$SevCfg = Common::getSevidCfg($type);
Common::loadModel('Master');

$db = Common::getDbBySevId($SevCfg['sevid']);
$sql = "select * from `sev_act` where `key`=17";
$data = $db->fetchArray($sql);
if(!empty($data)){
    foreach ($data as $val){
        if(empty($val['value'])) continue;
        $info = json_decode($val['value'],true);
        if(empty($info)){//不是今日的不要
            continue;
        }
        $SevModel = Master::getSev17($val['hcid']);
        if(empty($SevModel->info)){
            $SevModel->info = $info;
        }else{
            foreach ($info as $id => $score){
                $SevModel->info[$id] += $score;
            }
        }
        echo $val['hcid'],PHP_EOL;
        print_r($SevModel->info);
        $SevModel->save();
    }
}
echo 18,PHP_EOL;
$sql1 = "select * from `sev_act` where `key`=18";
$data = $db->fetchArray($sql1);
if(!empty($data)){
    foreach ($data as $val){
        if(empty($val['value'])) continue;
        $info = json_decode($val['value'],true);
        if(empty($info)){//不是今日的不要
            continue;
        }
        $SevModel = Master::getSev18($val['hcid']);
        $SevModel->info = $info;
        echo $val['hcid'],PHP_EOL;
        print_r($SevModel->info);
        $SevModel->save();
    }
}


//$uid = 10236;
//switch ($type){
//    case 1://添加车
//        $car_info = Game::getcfg('mounts_car');
//        foreach ($car_info as $cid => $val){
//            $i = 0;
//            for($i = 0;$i < 5;$i++){
//                Master::add_item($uid,14,$cid,1);
//            }
//        }
//        $Act182Model = Master::getAct182($uid);
//        $Act182Model->ht_destroy();
//        break;
//    case 2://加皮肤
//        Master::add_item($uid,15,1,1);
//        $Act167Model = Master::getAct167($uid);
//        $Act167Model->ht_destroy();
//        break;
//    case 3:
//        $cid = 360182;
//        $ClubModel = Master::getClub($cid);
//        print_r($ClubModel->getBase());
//        echo json_encode($ClubModel->getBase());
//        break;
//}

exit();