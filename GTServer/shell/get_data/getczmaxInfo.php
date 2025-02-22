<?php
/**
 * 获取最大红颜最大亲密度
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
include(ROOT_DIR . '/administrator/extend/IpLocation.php');
$ipLocation = new IpLocation();

if ( is_array($serverList) ) {
    $total = array();
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $Sev_Cfg['sevid']) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if (0 < $serverID && $serverID != $Sev_Cfg['sevid']) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID
        ) {
            echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if ($open_day <= 0) {
            continue;
        }
        $ls = getdata($Sev_Cfg['sevid']);
        $total = array_merge($total,$ls);
    }
    if(!empty($total)){
        $new = array();
        foreach ($total as $value){
            $new[$value['roleid']] = $value['al'];
        }
        arsort($new);
        $i = 1;
        $back = array();
        $sheng = array('北京市','广东省','山东省','江苏省','河南省','上海市','河北省','浙江省','香港特别行政区','陕西省','湖南省','重庆市','福建省','天津市','云南省','四川省','广西壮族自治区','安徽省','海南省','江西省','湖北省','山西省','辽宁省','台湾省','黑龙江','内蒙古自治区','澳门特别行政区','贵州省','甘肃省','青海省','新疆维吾尔自治区','西藏区','吉林省','宁夏回族自治区');
        foreach ($new as $uid => $money){
            if($i > 1000){
                continue;
            }
            $UserModel = Master::getUser($uid);
            //第一步 截取第一个ip
            $ips = explode(',',$UserModel->info['ip']);
            //第二步 去空格
            $ip = trim($ips[0]);
            $location = $ipLocation->getlocation($ip);
            foreach ($sheng as $sname){
                if(strpos($location['country'],$sname) !== false){
                    $name = $sname;
                    break;
                }
            }
            if(empty($back[$name])){
                $back[$name]['num'] = 0;
                $back[$name]['money'] = 0;
            }
            $back[$name]['num'] += 1;
            $back[$name]['money'] += $money;
            Game::logMsg('/tmp/iplocation111',$uid.','.$money.','.$ip.','.$name);
            $i++;
        }
        foreach ($back as $lo => $val){
            echo $lo.'----'.$val['num'].'----------------'.$val['money'],PHP_EOL;
            Game::logMsg('/tmp/iplocation222',$lo.','.$val['num'].','.$val['money']);
        }
    }
}
function getdata($sid){

    $db = Common::getDbBySevId($sid);

    $sql = "SELECT roleid,SUM(money) as al FROM t_order WHERE status>0 GROUP BY roleid ORDER BY al DESC LIMIT 1000;";

    $res = $db->fetchArray($sql);
    return $res;
}