<?php
//数据库 增加进攻技能
set_time_limit(0);
ini_set('memory_limit','3000M');
require_once dirname( __FILE__ ) . '/../public/common.inc.php';

$serverid = intval($_SERVER['argv'][1]);// 默认是全部区

$SevidCfg = Common::getSevidCfg($serverid);//子服ID


Common::loadModel("Master");




//活动261

$sTime = '2017-12-05 00:00:00';
$eTime = '2017-12-07 09:56:00';
$startTime = strtotime($sTime);
$endTime = strtotime($eTime);



$sql = 'SELECT * FROM `t_order` WHERE `status`= 1 and `ptime`> '.$startTime. ' and `ptime`< '. $endTime;
$db = Common::getMyDb();
$data = $db->fetchArray($sql);

foreach($data as $v){
	
	$uid = $v['roleid'];
	
		echo $uid."\n";
		$Act262Model = Master::getAct262($uid);
		$Act262Model->reset_bug($v['ptime']);
		$Act262Model->ht_destroy();
		unset($Act262Model,$v);
	
	
}
unset($data);

Master::click_destroy();

exit();


/*
 * 
$sql = 'SELECT * FROM `t_order` WHERE `status`= 1 and `ptime`> '.$startTime. ' and `ptime`< '. $endTime;
$db = Common::getMyDb();
$data = $db->fetchArray($sql);

foreach($data as $v){
	$uid = $v['roleid'];
	echo $uid."\n";
	$Act260Model = Master::getAct260($uid);
	$Act260Model->reset_bug();
	$Act260Model->ht_destroy();
	unset($Act260Model,$v);
}
unset($data);
 */




/*
$qufu = array(
//官方   57
57  => 57012250,
117 => 117015325,
118 => 118017149,
119 => 119017204,
120 => 120016297,
121 => 121017634,
122 => 122016009,

//安峰
43 => 43010554,
44 => 44011109,
45 => 45011346,
46 => 46012823,

96 => 96015654,
97 => 97016859,
98 => 98015480,
99 => 99011204,

//快游
25 => 25010900,
26 => 26005608,


//快玩
23 => 23005716,
24 => 24007189,

);


$suid = $serverid * 1000000 + 1;
$euid = $qufu[$serverid];

$sTime = '2017-12-03 00:00:00';
$eTime = '2017-12-03 23:59:59';
$startTime = strtotime($sTime);
$endTime = strtotime($eTime);


//活动206
for($i = $suid ; $i <= $euid ; $i ++ ){
	
	$fUserModel = Master::getUser($i);
	
	//如果不是今天
	if( !Game::is_today($fUserModel->info['lastlogin']) ){
		unset($fUserModel);
		continue;
	}
    
    		
    
 			$uid = trim($i);
 			
            $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
            
            $table = 'flow_event_'.Common::computeTableId($uid);
            $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
            $db = Common::getMyDb('flow');
            $data = $db->fetchArray($sql);
            
            if (!empty($data)){
            	
            	$id = array();
            	
                foreach ($data as $key => $value){
                    $id[] = $value['id'];
                    unset($key,$value);
                }

                $fid = implode(',', $id);
                $table = 'flow_record_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                $sql .= ' and `type`= 7';

                $recordData = $db->fetchArray($sql);
                
                if(!empty($recordData)){
                	$data_new = 0;
                	
                    foreach ($recordData as $k => $v){
                    	
                            if($data_new == 0){
                            	$data_new = $v['next'] - $v['cha'];
                            }else{
                            	if($data_new > $v['next'] - $v['cha'] ){
                            		$data_new = $v['next'] - $v['cha'];
                            	}
                            }
                        	unset($k,$v);
                    }
                    
                    if($data_new > 0){
                    	echo $uid.': '.abs($data_new)."\n";
                    	
                    	$Act206Model = Master::getAct206($uid);
						$Act206Model->do_save_debug($data_new);
						$Act206Model->ht_destroy();
						
						unset($fid,$table,$sql,$data,$fUserModel,$recordData,$Act206Model);
						
                    }else{
                    	unset($fid,$table,$sql,$data,$fUserModel,$recordData);
                    }
                    
                }else{
                    $data = array();
                    unset($fid,$table,$sql,$data,$fUserModel,$recordData);
                    continue;
                }

            }else{
            	unset($fUserModel);
            }
            
	
}
*/

/*
//活动208
for($i = $suid ; $i <= $euid ; $i ++ ){
	
	$fUserModel = Master::getUser($i);
	
	//如果不是今天
	if( !Game::is_today($fUserModel->info['lastlogin']) ){
		unset($fUserModel);
		continue;
	}
	
	echo $i."\n";
	
	$Act208Model = Master::getAct208($i);
	$Act208Model->add_bug(1);
	$Act208Model->ht_destroy();
	
	unset($fUserModel,$Act208Model);
}
*/

/*
//活动262
$sql = 'SELECT * FROM `t_order` WHERE `status`= 1 and `ptime`> '.$startTime;
$db = Common::getMyDb();
$data = $db->fetchArray($sql);
foreach($data as $v){
	$uid = $v['roleid'];
	
	echo $uid."\n";
	
	$Act262Model = Master::getAct262($uid);
	$Act262Model->add();
	$Act262Model->ht_destroy();
	
	unset($Act262Model,$v);
	
}
unset($data);
*/


/*
 * 
//活动201
for($i = $suid ; $i <= $euid ; $i ++ ){
	
	$fUserModel = Master::getUser($i);
	
	//如果不是今天
	if( !Game::is_today($fUserModel->info['lastlogin']) ){
		unset($fUserModel);
		continue;
	}
    
    		
    
 			$uid = trim($i);
 			
            $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
            
            $table = 'flow_event_'.Common::computeTableId($uid);
            $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
            $db = Common::getMyDb('flow');
            $data = $db->fetchArray($sql);
            
            if (!empty($data)){
            	
            	$id = array();
            	
                foreach ($data as $key => $value){
                    $id[] = $value['id'];
                    unset($key,$value);
                }

                $fid = implode(',', $id);
                $table = 'flow_record_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                $sql .= ' and `type`= 1';

                $recordData = $db->fetchArray($sql);
                if(!empty($recordData)){
                	$data_new = 0;
                    foreach ($recordData as $k => $v){
                        foreach ($data as $dk => $dv){
                            if ($dv['id'] == $v['flowid']){
                            	if($recordData[$k]['cha']<0){
                            		 $data_new += $recordData[$k]['cha'];
                            	}
                               
                            }
                            unset($dk,$dv);
                        }
                        unset($k,$v);
                    }
                    
                    if($data_new < 0){
                    	echo $uid.': '.abs($data_new)."\n";
                    	
                    	$Act201Model = Master::getAct201($uid);
						$Act201Model->add_bug(abs($data_new));
						$Act201Model->ht_destroy();
						
						unset($fid,$table,$sql,$data,$fUserModel,$recordData,$Act201Model);
                    }else{
                    	unset($fid,$table,$sql,$data,$fUserModel,$recordData);
                    }
                    
                }else{
                    $data = array();
                    unset($fid,$table,$sql,$data,$fUserModel,$recordData);
                    continue;
                }

            }else{
            	unset($fUserModel);
            }
            
            
    
    
    

	
}
*/




