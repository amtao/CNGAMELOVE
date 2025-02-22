<?php
/**
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
$total = array();
//$serverList = array(227,228,229);
//$serverList = array(227);
//foreach ($serverList as $sid){
for ($sid = 1;$sid <= 229;$sid++){
    $Sev_Cfg = Common::getSevidCfg($sid);//子服ID
    $linshi = getUser();
    if(!empty($linshi)){
        $total= array_merge($total,$linshi);
    }
}
//}

if(empty($total)){
    echo '未找到数据';
}else{
    $ip_data = array();
    foreach ($total as $val){
        $ip_data[$val['ip']][$val['uid']] = array('name'=> $val['name'],'reg'=>$val['regtime']);
    }
    unset($total);
    $res = array();
    $sum = 0;
    foreach ($ip_data as $ip => $uids){
        if(count($uids) >= 10){
            $sum += count($uids);
            file_put_contents('/tmp/catipTotal',$ip.'------'.count($uids)."\n",FILE_APPEND);
            echo $ip.'------',count($uids);
            foreach ($uids as $uid => $info){
                echo $uid.'  '.$info['name'].'  '.$ip.'  '.date('Y-m-d H:i:s',$info['reg']),PHP_EOL;
            }
        }
    }
    file_put_contents('/tmp/catipTotal','总计:------'.$sum,FILE_APPEND);
}


exit();


function getUser(){
    $db = Common::getMyDb();
    $data = array();
    for($i = 0;$i<100;$i++){
        $table = "user_".Common::computeTableId($i);
        $sql = "select `uid`,`name`,`regtime`,`ip` from {$table} WHERE `regtime`>=1518364800 AND `regtime`<=1518451200";
        $res = $db->fetchArray($sql);
        if(!empty($res)){
            $data = array_merge($data,$res);
        }
    }
    return $data;
}