<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/24
 * Time: 22:43
 */

/**
 * 冲榜活动错误订正
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");

$SevidCfg = Common::getSevidCfg($serverID);//子服ID
$db = Common::getDbBySevId($SevidCfg['sevid']);
$table_div = Common::get_table_div($SevidCfg['sevid']);

$startTime = strtotime('2018-04-24 00:00:00');
for ($i = 0 ; $i < $table_div ; $i++) {

    $table_user = 'user_'.Common::computeTableId($i);
    echo $table_user."\n";
    $sql_user = 'select `uid`,`lastlogin` from '.$table_user .' where `lastlogin` > '.$startTime;
    $res = $db->fetchArray($sql_user);

    if(!empty($res)){
        foreach ($res as $val){
            $uid = $val['uid'];
            echo "   UID:".$uid;

            if(!empty($text_uid) && $uid != $text_uid){
                continue;
            }

            $lastlogin = $val['lastlogin'];

            if($lastlogin < $startTime){
                continue;
            }


            //此处该为对应的限时活动
            $Act202Model = Master::getAct202($uid);
            echo "   cons:".$Act202Model->info['cons'];
            //活动对应的数值
            if($Act202Model->info['cons'] > 0){
                $Act257Model = Master::getAct257($uid);//对应的冲榜活动
                $Act257Model->info['num'] = 0;
                $Act257Model->do_save_NOADD($Act202Model->info['cons']);
                $Act257Model->ht_destroy();
            }

//            $Act221Model = Master::getAct221($uid);
//            echo "   cons:".$Act221Model->info['cons'];
//            //活动对应的数值
//            if($Act221Model->info['cons'] > 0){
//                $Act258Model = Master::getAct258($uid);//对应的冲榜活动
//                $Act258Model->info['num'] = 0;
//                $Act258Model->do_saveNOADD($Act221Model->info['cons']);
//                $Act258Model->ht_destroy();
//            }
        }
    }
}