<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$serverList = include(ROOT_DIR.'/config/server.php');

Common::loadModel('HoutaiModel');
Common::loadModel('MailModel');
Common::loadModel('ServerModel');

foreach ($serverList as $key => $value) {

    $serverID = intval($value["sevId"]);// 默认是全部区
    if ($value["isOpen"] == 0) {
        continue;   // 未开服
    }

    $crontabName = $serverID."_buchan";
    $btime = microtime(true);
    Game::crontab_debug("当前时间:".date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), $crontabName);

    //服务器过滤
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    Game::crontab_debug("服务器ID：:".$SevidCfg['sevid'], $crontabName);

    $open_day = ServerModel::isOpen($serverID);
    //过滤未开服的
    if($open_day <= 0){
        continue;
    }

    //活动发放奖励  --   每个区各自发放
    do_huodong($SevidCfg, $crontabName);

    $time = time();

    Game::crontab_debug("耗时(s)=".(microtime(true)-$btime), $crontabName);
    Game::crontab_debug("-------------------------------------------------------------------", $crontabName);
}
exit();

/**
 * 活动发放奖励      活动结束预留2个小时的展示时间和发放奖励时间
 */
function do_huodong($SevidCfg, $crontabName){
    $cache 	= Common::getCacheBySevId($SevidCfg['sevid']);
    $hd_key = 'benfu_'.$SevidCfg['sevid'].'_huodong_280';
    $hd_info = $cache->get($hd_key);
    
    $k = 'huodong_280';
//     $k1 = 'huodong_280_1';
    $run_info = HoutaiModel::read_huodong_run($k);
    if( empty($run_info['id'])){

        Game::crontab_debug("获取脚本run失败\n", $crontabName);
        return false;
    }
    //存放规则 $run_info['id'] = 活动id
    if($run_info['id'] ==  $hd_info['info']['id']){
        Game::crontab_debug($k."奖励已发放\n", $crontabName);
        return false;
    }
    //记录奖励发放脚本标志
    $isok = HoutaiModel::write_huodong_run($k,$hd_info['info']['id']);
    if(!$isok){
        Game::crontab_debug("脚本run插入失败\n", $crontabName);
        return false;
    }
    
    if($hd_info){
        huodong_280_rwd($SevidCfg,$hd_info, $crontabName); 
    }

}

/*
 * 发放活动奖励  ---   新官上任奖励
 */
function huodong_280_rwd($SevidCfg,$hd_info, $crontabName){
    $key = 'huodong_280_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        Game::crontab_debug($key."无排行信息\n", $crontabName);
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = MAIL_HUODONG280_1.'|'.$rid.'|'.MAIL_HUODONG280_2;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,MAIL_HUODONG280_REWARD,$tip,1,$rwd['member']);
                $mailModel->destroy();

                Game::crontab_debug(' 玩家id: '.$uid."--已发\n", $crontabName);
                break;
            }
        }
    }
    
    $club_key = 'huodong_280_club_'.$hd_info['info']['id'].'_redis';
	$club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
	if(empty($club_rdata)){
        Game::crontab_debug($club_key."无排行信息\n", $crontabName);
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
					$tip = MAIL_HUODONG280_3.'|'.$club_rid.'|'.MAIL_HUODONG280_2;
					if($mem['post'] == 1){ //盟主奖励
						 $mailModel->sendMail($uid,MAIL_HUODONG280_REWARD,$tip,1,$rwd['mengzhu']);
					}else{ //非盟主奖励
						$mailModel->sendMail($uid,MAIL_HUODONG280_REWARD,$tip,1,$rwd['member']);
					}
					$mailModel->destroy();
                    Game::crontab_debug('联盟: '.$cid.' 玩家id: '.$uid."--已发\n", $crontabName);
					break;
				}
			}
			
		}
	}
}

