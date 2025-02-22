<?php
/**
 * 服饰兑换bug修复
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
$Sev_Cfg = Common::getSevidCfg($sev);//子服ID
echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
echo PHP_EOL, '问题账号：', PHP_EOL;

huodong_6232_bug_select();

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();


function huodong_6232_bug_select(){

    global $Sev_Cfg;
    $cache 	= Common::getCacheBySevId($Sev_Cfg['sevid']);
    $db = Common::getDbBySevId($Sev_Cfg['sevid']);
    $key = 'clothe_6140_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        return false;
    }
    $uids = array_keys($rdata);
    unset($rdata);
    $bugs = array(
        61  => array(1,'body',1),  //array(兑换商城对应id,服饰部位,替换id)
        261 => array(4,'head',201),
        361 => array(5,'ear',0)
    );//修复方案
    foreach ($uids as $uid){
        $suit = '';//套装等级信息
        $flg = false;
        $Act6140Model = Master::getAct6140($uid);//服装
        $Act6232Model = Master::getAct6232($uid);//热气球活动
        foreach ($bugs as $cid => $val){
            if (in_array($cid,$Act6140Model->info['clothes']) && empty($Act6232Model->info['exchange'][$val[0]])){
                $flg = true;
                $suit = empty($Act6140Model->info['suit'][9])?$suit:'  套装等级：'.$Act6140Model->info['suit'][9];
            }

        }
        if ($flg){
            echo "       ".$uid.$suit.PHP_EOL;
        }
    }
}










