<?php
/**
 * 调用方式：每5分钟跑一次
 *
 */

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('HoutaiModel');
Common::loadModel('MailModel');
Common::loadModel('lock/MyLockModel');

set_time_limit(0);
ini_set('memory_limit', '1024M');
$SevidCfg = Common::getSevidCfg(1);

Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();

foreach ($serverList as $key => $value) {

    if (empty($value)) {
        continue;
    }
    $serverID = intval($value['id']);// 默认是全部区

    $crontabName = $serverID."_send_rwd_five";
    $btime = microtime(true);
    $nowTime = date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']);
    // Game::crontab_debug("当前时间:".date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), $crontabName);

    //服务器过滤
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    // Game::crontab_debug("服务器ID：:".$SevidCfg['sevid'], $crontabName);
    if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
        // Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }
    if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
        // Game::crontab_debug(">>>跳过", $crontabName);
        continue;
    }
    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
        && $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
        // Game::crontab_debug(">>>从服跳过", $crontabName);
        continue;
    }

    if($SevidCfg['sevid'] != $SevidCfg['he']){
        // Game::crontab_debug(">>>不是指定合服id跳过", $crontabName);
        continue;
    }

    $open_day = ServerModel::isOpen($serverID);
    //过滤未开服的
    if($open_day <= 0){
        // Game::crontab_debug(">>>open_day：".$open_day, $crontabName);
        continue;
    }

    //活动发放奖励  --   每个区各自发放
    do_huodong($SevidCfg, $crontabName);

    $time = time();

    $parm1 = constant('GAME_MARK');
    $parm2 = constant('DOMAIN_HOST');
    $parm3 = constant('AGENT_CHANNEL_NAME');

    Game::crontab_debug("执行时间:".$nowTime."      耗时(s)=".(microtime(true)-$btime), $crontabName);
    Game::crontab_debug("-------------------------------------------------------------------", $crontabName);
}
exit();

/**
 * 活动发放奖励      活动结束预留2个小时的展示时间和发放奖励时间
 */
function do_huodong($SevidCfg, $crontabName){

    //生效活动列表
    $key_list = 'hd_base_list_'.$SevidCfg['sevid'];
    $cache  = Common::getCacheBySevId($SevidCfg['sevid']);
    $all_list = $cache->get($key_list);
    if(empty($all_list)){

        Game::crontab_debug("无生效活动\n", $crontabName);
        return 0;
    }
    foreach($all_list as $k => $v){
        switch($k){
            case 'huodong_6613' :
                $hd_info = HoutaiModel::get_huodong_info($k);
                $is_time_rwd =  check_huodong($hd_info, $crontabName);
                if($is_time_rwd){
                    huodong_6613_rwd($hd_info, $crontabName);
                }
                break;
        }
    }
}

/**
 * 验证奖励是否可以发放
 * @param array $SevidCfg  活动key
 * @param string $k   活动key
 * @return  bool|array
 */
function check_huodong($hd_info, $crontabName){
    echo "开始检测".PHP_EOL;
    inlogs(PHP_EOL. '开始检测'.PHP_EOL);
    $result = false;
    //检测活动状态
    $Sev6613Model = Master::getSev6613($hd_info['info']['id']);
    //获取活动详细信息
    if(empty($hd_info)){
        echo "未获取到活动信息\n";
        inlogs(PHP_EOL. '未获取到活动信息'.PHP_EOL, $crontabName);
        return false;
    }
    $hd_end = Game::is_over($hd_info['info']['eTime']);
    $time = Game::get_now();
    switch ($Sev6613Model->info['status']){
        case 0:
            if($Sev6613Model->info['now_rwd']['limit_time'] > 0) {
                if ($time - $Sev6613Model->info['start_time'] >= $Sev6613Model->info['now_rwd']['limit_time'] || $hd_end) {
                    //到期封盘
                    $Sev6613Model->info['end_time'] = $time;
                    $Sev6613Model->info['status'] = 1;
                    $Sev6613Model->save();
                    echo "活动封盘" . PHP_EOL;
                    inlogs(PHP_EOL. '活动封盘'.PHP_EOL, $crontabName);
                } else {
                    echo "下单投注中" . PHP_EOL;
                    inlogs(PHP_EOL. '下单投注中'.PHP_EOL, $crontabName);
                }
            }
            break;
        case 1:case 3:
        if ($time - $Sev6613Model->info['end_time'] >= $hd_info['fengpan_time']) {
            //封盘五分钟到期，发奖
            $result = true;
            echo "封盘满五分钟，开始发奖".PHP_EOL;
            inlogs(PHP_EOL. '封盘满五分钟，开始发奖'.PHP_EOL, $crontabName);
        }else{
            echo "封盘中".PHP_EOL;
            inlogs(PHP_EOL. '封盘中'.PHP_EOL, $crontabName);
        }
        break;
        case 2:
            if($hd_end){
                //活动已结束，不进入下一期

            }else {
                if ($time - $Sev6613Model->info['end_time'] >= 600) {
                    //发奖五分钟到期，重新开始
                    $Sev6613Model->reset_info();
                    echo "发奖结束后五分钟，开始新一轮" . PHP_EOL;
                    inlogs(PHP_EOL. '发奖结束后五分钟，开始新一轮'.PHP_EOL, $crontabName);
                }
            }
            break;
    }
    echo "检测结束";
    inlogs(PHP_EOL. '检测结束'.PHP_EOL, $crontabName);
    return $result;
}

/*
 * 发放活动奖励  ---   联盟冲榜奖励
 */
function huodong_6613_rwd($hd_info, $crontabName)
{
    $Sev6613Model = Master::getSev6613($hd_info['info']['id']);
    $issue = $Sev6613Model->info['issue'];
    $key = 'huodong_6613_' . $hd_info['info']['id'] . $Sev6613Model->info['issue'] . '_redis';
    $redis = Common::getDftRedis();
    $rdata = $redis->zRevRange($key, 0, -1, true);  //获取排行数据
    echo "获奖数据：".json_encode($rdata).PHP_EOL;
    inlogs(PHP_EOL. '获奖数据：'.json_encode($rdata).PHP_EOL, $crontabName);
    //随机大奖
    $big_rwd = rand(1, $Sev6613Model->info['now_rwd']['seat_count']);
    $rwd_list = array();
    $big_uid = 0;
    if (empty($rdata)) {
        echo "没有数据，redis_key:".$key . "\n";
        inlogs(PHP_EOL. '没有数据，redis_key'.$key.PHP_EOL, $crontabName);
        $Sev6613Model->add_records($big_uid, $Sev6613Model->info['now_rwd']['items']['id'], $Sev6613Model->info['now_rwd']['items']['kind'], $Sev6613Model->info['now_rwd']['items']['count'],$big_rwd);

        $Sev6613Model->info['status'] = 2;
        $Sev6613Model->save();
        return false;
    }
    foreach ($rdata as $cid => $uid) {
        $Act6613Model = Master::getAct6613($uid);
        //添加大奖记录
        if ($big_rwd == $cid) {
            $big_uid = $uid;
            //发放奖励，判断是否拥有
            $have = 0;
            if ($Sev6613Model->info['now_rwd']['items']['kind'] == 95) {
                $act6140Model = Master::getAct6140($uid);
                if (in_array($Sev6613Model->info['now_rwd']['items']['id'], $act6140Model->info['clothes']) || in_array($Sev6613Model->info['now_rwd']['items']['id'],$Act6613Model->info['mybigrwd'])) {
                    $have = 1;
                    if($rwd_list[$uid]['remark']){
                        $rwd_list[$uid]['remark'] += 1;
                    }else{
                        $rwd_list[$uid]['remark'] = 1;
                    }
                }
            }
            if($have == 0){
                //记录获得大奖
                $Act6613Model->info['mybigrwd'][] = $Sev6613Model->info['now_rwd']['items']['id'];
                $Act6613Model ->save();
                $Act6613Model->ht_destroy();
                if($rwd_list[$uid]['big']){
                    $rwd_list[$uid]['big'] += 1;
                }else{
                    $rwd_list[$uid]['big'] = 1;
                }
            }
        } else {
            if($rwd_list[$uid]['small']){
                $rwd_list[$uid]['small'] += 1;
            }else{
                $rwd_list[$uid]['small'] = 1;
            }
        }
    }
    if($big_uid >0) {

        $UserInfo = Master::fuidInfo($big_uid);
        $Sev6613Model->add_records($UserInfo['name'], $Sev6613Model->info['now_rwd']['items']['id'], $Sev6613Model->info['now_rwd']['items']['kind'], $Sev6613Model->info['now_rwd']['items']['count'],$big_rwd);
    }else{
        $Sev6613Model->add_records($big_uid, $Sev6613Model->info['now_rwd']['items']['id'], $Sev6613Model->info['now_rwd']['items']['kind'], $Sev6613Model->info['now_rwd']['items']['count'],$big_rwd);
    }

    $cfg_blank = Game::getcfg('use_blank');
    $cfg_clothe = Game::getcfg('use_clothe');
    $cfg_item = Game::getcfg('item');
    echo "发邮件数据：".json_encode($rwd_list).PHP_EOL;
    inlogs(PHP_EOL. '发邮件数据：'.json_encode($rwd_list) .PHP_EOL, $crontabName);
    //活动次数打点--大奖小奖
    // Common::loadModel('DotTotalModel');
    //发送邮件
    foreach ($rwd_list as $uid => $value) {
        $tip = "";
        $Act6613Model_mail = Master::getAct6613($uid);
        $my_checked = $Act6613Model_mail->info['my_checked'];
        $count = count($my_checked);
        $mailModel = new MailModel($uid);
        $items = array();
        $is_big = 0;
        if($value['big']){
            $items[] = $Sev6613Model->info['now_rwd']['items'];
            $muqian_item = $Sev6613Model->info['now_rwd']['items'];
            $is_big = 1;
            switch ($muqian_item['kind']){
                case 94:
                    $item_name = $cfg_blank[$muqian_item['id']]['name'];
                    break;
                case 95:
                    $item_name = $cfg_clothe[$muqian_item['id']]['name'];
                    break;
                default:
                    $item_name = $cfg_item[$muqian_item['id']]['name'];
                    break;
            }
            // DotTotalModel::dotHuodong($uid,6613 ,66 ,1 ,$muqian_item, 'big');
            $item_count = $muqian_item['count'];
            $tip = 'MAIL_BIGXINYUNQIAN_CONTENT_1_1|'.$issue.'|MAIL_BIGXINYUNQIAN_CONTENT_1_2|'.$item_name.'|MAIL_BIGXINYUNQIANXXX_CONTENT|'.$item_count.'|MAIL_BIGXINYUNQIAN_CONTENT_1_3|MAIL_BIGXINYUNQIAN_CONTENT_4_1|'.$issue.'|MAIL_BIGXINYUNQIAN_CONTENT_4_2|'.$big_rwd.'|MAIL_BIGXINYUNQIAN_CONTENT_4_3|'.$count.'|MAIL_BIGXINYUNQIAN_CONTENT_4_4|'.json_encode($my_checked);
        }
        if($value['remark']){
            $muqian_item1 = $Sev6613Model->info['now_rwd']['items'];
            switch ($muqian_item1['kind']){
                case 94:
                    $item_name1 = $cfg_blank[$muqian_item1['id']]['name'];
                    break;
                case 95:
                    $item_name1 = $cfg_clothe[$muqian_item1['id']]['name'];
                    break;
                default:
                    $item_name1 = $cfg_item[$muqian_item1['id']]['name'];
                    break;
            }
            $item_count1 =  $muqian_item1['count'];
            $items[] = $Sev6613Model->info['now_rwd']['items_remark'];
            $muqian_item = $Sev6613Model->info['now_rwd']['items_remark'];
            switch ($muqian_item['kind']){
                case 94:
                    $item_name = $cfg_blank[$muqian_item['id']]['name'];
                    break;
                case 95:
                    $item_name = $cfg_clothe[$muqian_item['id']]['name'];
                    break;
                default:
                    $item_name = $cfg_item[$muqian_item['id']]['name'];
                    break;
            }
            // DotTotalModel::dotHuodong($uid,6613 ,66 ,1 ,$muqian_item, 'remark');
            $is_big = 2;
            $tip = 'MAIL_BIGXINYUNQIAN_CONTENT_1_1|'.$issue.'|MAIL_BIGXINYUNQIAN_CONTENT_1_2|'.$item_name1.'|MAIL_BIGXINYUNQIANXXX_CONTENT|'.$item_count1.'|MAIL_BIGXINYUNQIAN_CONTENT_2_3|'.$item_name1.'|MAIL_BIGXINYUNQIANXXX_CONTENT|'.$item_count1.'|MAIL_BIGXINYUNQIAN_CONTENT_2_4|'.$item_name.'|MAIL_BIGXINYUNQIAN_CONTENT_2_5|MAIL_BIGXINYUNQIAN_CONTENT_4_1|'.$issue.'|MAIL_BIGXINYUNQIAN_CONTENT_4_2|'.$big_rwd.'|MAIL_BIGXINYUNQIAN_CONTENT_4_3|'.$count.'|MAIL_BIGXINYUNQIAN_CONTENT_4_4|'.json_encode($my_checked);
        }
        if($value['small']){
            $all_count = array();
            $list = $Sev6613Model->info['now_rwd']['items_small'];
            // $rid = 1;
            for ($i = 0;$i < $value['small'];$i++){
                // $rid =  Game::get_rand_key(10000,$list,'prob');
                // $all_count += $list[$rid]['count'];
                $rid = Game::get_rand_key1($list, 'prob');
                $all_count[$rid] += $list[$rid]['count'];
            }

            foreach ($all_count as $all_key => $all_value) {
                $items_val = array('id' => $list[$all_key]['id'], 'count' => $all_value, 'kind' => $list[$all_key]['kind']);
                $items[] = $items_val;
            }

            if ($is_big == 0) {
                $tip = 'MAIL_BIGXINYUNQIAN_CONTENT_3_1|'.$issue.'|MAIL_BIGXINYUNQIAN_CONTENT_3_2|MAIL_BIGXINYUNQIAN_CONTENT_4_1|'.$issue.'|MAIL_BIGXINYUNQIAN_CONTENT_4_2|'.$big_rwd.'|MAIL_BIGXINYUNQIAN_CONTENT_4_3|'.$count.'|MAIL_BIGXINYUNQIAN_CONTENT_4_4|'.json_encode($my_checked);
            }
        }
        echo "发邮件数据，uid：".$uid."-----物品：".json_encode($items).PHP_EOL;
        inlogs(PHP_EOL. '发邮件数据，uid：'.$uid.'-----物品：'.json_encode($items) .PHP_EOL, $crontabName);
        $mailModel->sendMail($uid, 'LUCKYSIGN_TITLE', $tip, 1, $items);
        $mailModel->destroy();
    }
    $Sev6613Model->info['status'] = 2;
    $Sev6613Model->save();
    echo '第: ' . $issue . '期--已发\n大奖号码：'.$big_rwd.PHP_EOL;
    inlogs(PHP_EOL. '第: '.$issue.'期--已发\n大奖号码：'.$big_rwd .PHP_EOL, $crontabName);
}

function inlogs($msg, $crontabName){
    Game::crontab_debug($msg, $crontabName);
}
