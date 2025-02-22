<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 * 
 */
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('HoutaiModel');
Common::loadModel('MailModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//服务器过滤
$SevidCfg = Common::getSevidCfg($serverID);//子服ID

		
//活动发放奖励  --   每个区各自发放
do_huodong($SevidCfg);
		
		
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();

/**
 * 活动发放奖励      活动结束预留2个小时的展示时间和发放奖励时间
 */
function do_huodong($SevidCfg){
    $cache 	= Common::getCacheBySevId($SevidCfg['sevid']);
    $hd_key = 'benfu_'.$SevidCfg['sevid'].'_huodong_280';
    $hd_info = $cache->get($hd_key);
    
    $k = 'huodong_280';
//     $k1 = 'huodong_280_1';
    $run_info = HoutaiModel::read_huodong_run($k);
    if( empty($run_info['id'])){
        echo "获取脚本run失败\n";
        return false;
    }
    //存放规则 $run_info['id'] = 活动id
    if($run_info['id'] ==  $hd_info['info']['id']){
        echo $k."奖励已发放\n";
        return false;
    }
    //记录奖励发放脚本标志
    $isok = HoutaiModel::write_huodong_run($k,$hd_info['info']['id']);
    if(!$isok){
        echo "脚本run插入失败\n";
        return false;
    }
    
    if($hd_info){
        huodong_280_rwd($SevidCfg,$hd_info); 
    }

}


/*
 * 发放活动奖励  ---   新官上任奖励
 */
function huodong_280_rwd($SevidCfg,$hd_info){
    $key = 'huodong_280_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key."无排行信息\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = "恭喜您在严刑逼供个人活动中获得第".$rid."名,请收下活动奖励";

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,'严刑逼供奖励',$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
    
    $club_key = 'huodong_280_club_'.$hd_info['info']['id'].'_redis';
	$club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
	if(empty($club_rdata)){
		echo $club_key."无排行信息\n";
		return false;
	}
	$club_rid = 0; //排名
	foreach($club_rdata as $cid => $score){
		$club_rid ++;
		$ClubModel = Master::getClub($cid);
		foreach($ClubModel->info['members'] as $uid => $mem){
			foreach($hd_info['rwd']['club'] as $rwd){
				//如果在排名奖励范围内  发放奖励
				if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
					$mailModel = new MailModel($uid);
					$tip = "恭喜您在严刑逼供帮会活动中获得第".$club_rid."名,请收下活动奖励";
					if($mem['post'] == 1){ //盟主奖励
						 $mailModel->sendMail($uid,'严刑逼供',$tip,1,$rwd['mengzhu']);
					}else{ //非盟主奖励
						$mailModel->sendMail($uid,'严刑逼供',$tip,1,$rwd['member']);
					}
					$mailModel->destroy();
					echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
					break;
				}
			}
			
		}
	}
}

