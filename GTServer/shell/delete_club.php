<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('ClubModel');
Common::loadModel('HoutaiModel');
$Sev_Cfg = Common::getSevidCfg(1);
$Redis302Model = Master::getRedis302();
$data = $Redis302Model->azRange(1,99999);
foreach ($data as  $cid){
   $sevid =  Game::get_sevid_club($cid);
   $db = Common::getDbBySevId($sevid);
   $sql = "select * from `club` where `cid` = {$cid};";
   $row = $db->fetchRow($sql);
   if(empty($row)){
       echo 'cid:',$cid,PHP_EOL;
       $Redis302Model->del_member($cid);
   }

}

exit();

function delete_club(){
    
    $db = Common::getMyDb();
    $sql = "select `cid`,`name` from `club` where `members` = '[]' or `members` is null ;";
    $data = $db->fetchArray($sql);
    foreach ($data as $k => $v){

        
        $sql = "delete from `club` where `cid` = '{$v['cid']}' ";
		if($db->query($sql)){
            echo '联盟为空：',$v['cid'],'联盟名：',$v['name'],PHP_EOL;
        }
		
		//帮会排行
		$Redis10Model = Master::getRedis10();
		$Redis10Model->del_member($v['cid']);
		
		//跨服帮会排行
		$Redis302Model = Master::getRedis302();
        $Redis302Model->del_member($v['cid']);
        
        unset($Redis10Model,$Redis302Model,$k,$v);
    }
    echo mysql_error();
}

















