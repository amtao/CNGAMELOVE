<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29
 * Time: 10:42
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('Master');
$serverList = ServerModel::getServList();

if ( is_array($serverList) ) {
    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }

        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }
        $Redis315Model = Master::getRedis315(190622);
        $rdata = $Redis315Model->get_member(1);
        if (empty($rdata)) {
            echo $key . CRONTAB_NO_RANK . "\n";
            return false;
        }
        $ClubModel = Master::getClub($rdata);
        $members = $ClubModel->info['members'];
        if (!empty($members)){
            foreach ($members as $uid => $mem){
                if ($mem['post'] == 1) {
                    echo "success:{$Sev_Cfg['sevid']}区|{$uid};".PHP_EOL;
                }
            }
        }
    }
}


