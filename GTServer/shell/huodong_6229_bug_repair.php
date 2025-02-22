<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
ini_set('memory_limit','3000M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
Common::loadModel("Master");
Common::loadModel("MailModel");

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$sev = intval($_SERVER['argv'][1]);// 默认是全部区
$zhid = intval($_SERVER['argv'][2]);// 植树节活动id
$lhid = intval($_SERVER['argv'][3]);// 劳动节活动id
$Sev_Cfg = Common::getSevidCfg($sev);//子服ID
echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

repair_huodong6229_bug($zhid,$lhid);


echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();




function repair_huodong6229_bug($zhid,$hid){
	
	global $Sev_Cfg;
	$cache 	= Common::getCacheBySevId($Sev_Cfg['sevid']);
	$db = Common::getDbBySevId($Sev_Cfg['sevid']);
    $Sev6221Model = Master::getSev6221($zhid);
    $index1 = $Sev6221Model->info['index'];error_log('index1__'.$index1);
    $Sev6229Model = Master::getSev6229($hid);
    $index2 = $Sev6229Model->info['index'];error_log('index2__'.$index2);
    if ($index1 == $index2){
        echo PHP_EOL, '胜负正常不处理', PHP_EOL;
        return false;
    }
	$key = 'huodong_6229_'.$index2.'_'.$hid.'_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	if(empty($rdata)){
		return false;
	}
    $items_arr = array(
        array( 'id' => 1, 'kind' => 1, 'count' => 100 ) ,//元宝
        array( 'id' => 95, 'kind' => 1, 'count' => 40 ) ,//征收手札
        array( 'id' => 96, 'kind' => 1, 'count' => 40 ) ,//征收名帖
        array( 'id' => 97, 'kind' => 1, 'count' => 40 ) ,//征收银票
        array( 'id' => 81, 'kind' => 1, 'count' => 40 ) ,//资质经验书
        array( 'id' => 1122, 'kind' => 1, 'count' => 20 ) ,//幸运散
    );
	foreach($rdata as $uid => $val){
        $title = '【补偿奉上】春耕活动阵营奖励问题补偿';
        $content = '敬爱的小主们，

请查收您的春耕活动阵营获胜补偿，补偿内容为双倍阵营获胜奖励。
给您带来的不便，我们深感歉意！
请收下奴才的一点心意。
谢谢小主对游戏的支持，祝您宠冠六宫！

游戏官方团队';
        $mailModel = Master::getMail($uid);
        $mailModel->sendMail($uid, $title, $content, 1, $items_arr);
        Master::click_destroy();
	}
	return true;
	
}










