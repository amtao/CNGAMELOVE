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
    $cache  = Common::getCacheBySevId($Sev_Cfg['sevid']);
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
    $clothe_sys = Game::getcfg("use_clothe");
    // 邮件发放
    $title = '【補償奉上】蔚藍薔薇刪除補償';
    $content = '親愛的小主們：

因非法獲得的蔚藍薔薇被刪除，將退還升級套裝所使用的玉如意數量。請查收~

';
    foreach ($uids as $uid){
        $flg = false;//更新标识
        $sendMailLog = '';
        $Act6140Model = Master::getAct6140($uid);//服装
        $Act6141Model = Master::getAct6141($uid);//换装
        $Act6232Model = Master::getAct6232($uid);//热气球活动
        foreach ($bugs as $cid => $val){
            if (in_array($cid,$Act6140Model->info['clothes']) && empty($Act6232Model->info['exchange'][$val[0]])){
                $flg = true;
                //扣除对应积分
                $Act6140Model->info['score'] -= $clothe_sys[$cid]['score'];
                ////清除对应bug服装数据
                $key = array_search($cid,$Act6140Model->info['clothes']);
                unset($Act6140Model->info['clothes'][$key]);
                //已穿上的换未最初服装
                if ($Act6141Model->info[$val[1]] == $cid){
                    $Act6141Model->info[$val[1]] = $val[2];
                }
            }
        }
        //返回已升级套装的玉如意
        $num = 0;
        if (!empty($Act6140Model->info['suit'][9]) && $flg){
            for ($i=1;$i<$Act6140Model->info['suit'][9];$i++){
                $lvSys = Game::getcfg_info('clothe_suit_prop', 9000 + $i);
                $num += $lvSys['cost'];
            }
            $itemArr = array(array('id'=>1001,'count'=>$num));
            $mailModel = Master::getMail($uid);
            $mailModel->sendMail($uid, $title, $content, 1, $itemArr);
            //清除套装数据
            unset($Act6140Model->info['suit'][9]);
            $sendMailLog = '  套装升级已补偿';
        }
        //更新
        if ($flg){
            //保存数据
            $Act6140Model->save();
            $Act6141Model->save();
            //刷新缓存
            $TeamModel = Master::getTeam($uid);
            $TeamModel->reset(5);
            $TeamModel->back_hero();
            Master::click_destroy();
            echo "       ".$uid.$sendMailLog.PHP_EOL;
        }
    }
}










