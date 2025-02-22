<?php
/**
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('UserModel');
Common::loadModel("Master");

$serverList = ServerModel::getServList();

echo PHP_EOL, '----------------begin----------------------', PHP_EOL;

if ( is_array($serverList) ) {
    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        if($v['id'] == 999) continue;
        $SevidCfg = Common::getSevidCfg($v['id']);//子服ID
        echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
        $db = Common::getDbBySevId($SevidCfg['sevid']);
        $sql = "select * from `sev_act` where `key`=5";
        $result = $db->fetchRow($sql);
        if(empty($result['value'])) continue;
        $info = json_decode($result['value'],true);
        if(empty($info)) continue;
        $list = array();
        foreach ($info as $val){
            foreach ($val as $v){
                if(!in_array($v['uid'],$list)){
                    $list[] = $v['uid'];
                }
                unset($v);
            }
            unset($val);
        }
        echo json_encode($list),PHP_EOL;
        if(!empty($list)){
            foreach ($list as $uid){
                $Act34Model = Master::getAct34($uid);
                echo 'uid:'.$uid,PHP_EOL;
                if(empty($Act34Model->info)) continue;
                foreach ($Act34Model->info as $id => $msg){
                    echo '王爷id.'.$id.'-------------'.$msg,PHP_EOL;
                    $Act34Model->info[$id] = preg_replace("#u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $msg);
                    echo '修改后:'.$Act34Model->info[$id],PHP_EOL;
                    unset($id,$msg);
                }
                $Act34Model->save();
                $Act34Model->ht_destroy();
            }
        }
    }
}

exit();
