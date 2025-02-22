<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$SevidCfg1 = Common::getSevidCfg(8);
Common::loadModel('ClubModel');
$db = Common::getMyDb('flow');
$ClubModel = new ClubModel(80001);
$member = $ClubModel->info['members'];
foreach ($member as $key => $value){
    if ($value['uid']){
        $table = 'flow_event_'.Common::computeTableId($value['uid']);
        $sql = "SELECT * FROM ".$table." WHERE `uid`=".$value['uid']." AND `ftime`>1516032000; ";
        $result = $db->fetchArray($sql);
        if (is_array($result)){
            foreach ($result as $k => $v){
                $info  =  json_decode($v['params'], true);
                if (!empty($info['id']) && $info['id']>=280 && $info['id']<=286){
                    var_dump($info);
                    echo $value['uid'].'<br/>';
                    break;
                }
            }
            unset($info, $result);
        }else{
            echo $value['uid'];
        }
    }

}
?>