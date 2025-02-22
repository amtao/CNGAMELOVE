<?php 
/**
 * 活动280数据错误
 * 调用方式：手动跑
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('HoutaiModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//服务器过滤

$SevidCfg = Common::getSevidCfg($serverID);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit();
}
if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit();
}
if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
	&& $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
	echo PHP_EOL, '>>>从服跳过', PHP_EOL;
	exit();
}

// if($serverID != 3){
//     echo PHP_EOL, '>>>不是3服跳过', PHP_EOL;
// }
		
//活动发放奖励  --   每个区各自发放
$old_key = 'huodong_282_my_20171008_redis';
$redis = Common::getDftRedis();
$rdata  = $redis->zRevRange($old_key, 1, -1,true);  //获取排行数据
if(empty($rdata)){
    echo $old_key."无排行信息\n";
    return false;
}

foreach($rdata as $uid => $score){
    $ItemsModel = Master::getItem($uid);
	$Act106Model = Master::getAct106($uid);
    if(!empty($ItemsModel->info)){
        foreach ($ItemsModel->info as $key => $v){
            if(in_array($key, array(271,272,273,274)) && $v['count']>0){
				echo $uid,PHP_EOL;
				$i_update = array(
					'itemid' => $v['itemid'],
					'count' => -$v['count']
				);
				$ItemsModel->update($i_update);
				$Act106Model->add($v['itemid'],$v['count']);
            }
        }
    }
}
// $hd_id = '20170901';
// $Redis106Model = Master::getRedis106($hd_id);
// $Redis107Model = Master::getRedis107($hd_id);
// echo '个人', PHP_EOL;
// foreach($rdata as $uid => $score){
    
//     echo $uid.'-----------'.$score, PHP_EOL;
//     //排行榜加积分
// //     $Redis106Model->zIncrBy($uid, $score);
    
// //     //活动全加
// //     $sc = $Redis106Model->zScore($uid);
//     $Act103Model = Master::getAct103($uid);
//     $Act103Model->add_score($sc);
// }

// $club_key = 'huodong_280_club_20171001_redis';
// $club_rdata  = $redis->zRevRange($club_key, 0, 1,true);  //获取排行数据
// if(empty($club_rdata)){
//     echo $club_key."无排行信息\n";
//     return false;
// }
// echo '联盟', PHP_EOL;
// foreach($club_rdata as $cid => $score){
//     echo $cid.'-----------'.$score, PHP_EOL;
// //     $Redis107Model->zIncrBy($cid, $score);
// }

Master::click_destroy();
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();