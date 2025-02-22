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
echo PHP_EOL, '账号：', PHP_EOL;

select_hero();

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();


function select_hero(){

    global $Sev_Cfg;
    $cache  = Common::getCacheBySevId($Sev_Cfg['sevid']);
    $db = Common::getDbBySevId($Sev_Cfg['sevid']);
    $key = 'shili_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        return false;
    }
    $msg = array(
        '  醇亲王',
        '  天聪可汗',
        '  醇亲王&天聪可汗'
    );
    $uids = array_keys($rdata);
    unset($rdata);
    $flg = false;
    foreach ($uids as $uid){
        $HeroModel = Master::getHero($uid);
        $hero1 = $HeroModel->check_info(36,true);
        $hero2 = $HeroModel->check_info(46,true);
        if(!$hero1 && !$hero2){
            continue;
        }elseif ($hero1  && !$hero2){
            $flg = true;
            echo "       ".$uid.$msg[1].PHP_EOL;
        }elseif (!$hero1 && $hero2){
            $flg = true;
            echo "       ".$uid.$msg[0].PHP_EOL;
        }else{
            $flg = true;
            echo "       ".$uid.$msg[2].PHP_EOL;
        }
    }
    if (!$flg){
        echo "       ".'未找到符合条件的数据'.PHP_EOL;
    }
}










