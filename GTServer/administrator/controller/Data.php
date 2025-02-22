<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 2017/6/27
 * Time: 15:28
 */
class Data
{
    /**
     * 主页
     * */
    public function index(){
        $SevidCfg = Common::getSevidCfg($_GET['sevid']);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 美元转换
     *
     * */
    public function returnDoller($money){
        $dc = array(
            // '6' => 0.99, '28' => 4.99, '30' => 4.99, '68' => 9.99, '198' => 29.99, '288' => 49.99, '328' => 49.99, '648' => 99.99,
            '5' => 0.99, '6' => 2.99, '18' => 2.99, '28' => 6.99, '30' => 4.99, '35' => 2.99, '36' => 19.99, '68' => 9.99, '198' => 29.99, '288' => 69.99, '328' => 49.99, '648' => 99.99, '25' => 7.99, '26' => 2.99, '27' => 4.99, '29' => 9.99, '31' => 19.99, '32' => 29.99, '33' => 49.99, '34' => 99.99,
        );
        $shopcfg = Master::getOrderShopCfg();
        foreach($shopcfg as $v){
            if($v['rmb'] == $money){
                return $v['dollar'];
            }
        }

        // $newMoney = $money * 100;
        // $newMoney2 = intval($money) * 100;

        // if(empty($dc[intval($money)]) || $newMoney != $newMoney2){
        //     return $money;
        // }else{
        //     return $dc[intval($money)];
        // }
    }

    /**
     * 充值查询(后台)
     * */
    public function showdata(){
        $y_serid = $_GET['sevid'];
        $data = '';
        $data .= "status>0 and paytype='houtai'";
        if(!empty($_POST)){
            if(!empty($_POST['uid'])){
                $uid = $_POST['uid'];
                $data .= ' and roleid='.$uid;
            }
            if(!empty($_POST['startTime']) and !empty($_POST['endTime'])){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
                $data .= " and ptime>={$startTime} and ptime<={$endTime}";
            }
            $db = Common::getDbeByUid($uid);
            $sql = 'select * from `t_order` WHERE '.$data.' order by `ctime` desc';
            $searchRecords = $db->fetchArray($sql);
        }else{
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startTime = strtotime($start);
            $endTime = strtotime($end);
            $data .= " and ptime>={$startTime} and ptime<={$endTime}";
            $searchRecords = array();
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            Common::loadModel('ServerModel');
            $serverList = ServerModel::getServList();
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $table = '`t_order`';
                $sql = 'select * from '.$table.' WHERE '.$data.' order by `ctime` desc';
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $res = $db->fetchArray($sql);
                if($SevidCfg1['sevid'] == 999){
                    if($SevidCfg1['sevid'] == SERVER_ID) {
                        $searchRecords = $res;
                        break;
                    }
                }else{
                    $searchRecords = array_merge($searchRecords,$res);
                }
            }
        }
        if(!empty($searchRecords)){
            $total = 0;
            foreach ($searchRecords as $key => &$val){
                // $searchRecords[$key]['money'] = Master::returnDoller($val['money']);
                // $val['server_id'] = Game::get_sevid($val['roleid']);
                // $total += Master::returnDoller($val['money']);
                $searchRecords[$key]['money'] = $val['money'];
                $val['server_id'] = Game::get_sevid($val['roleid']);
                $total += $val['money'];
            } 
        }
       
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    
    /**
     * 充值查询(非后台)
     */
    public function paySearch() {
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        Common::loadModel('UserModel');
        $serverList = ServerModel::getServList();
        
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        $gift_bag = Game::getGiftBagCfg();
        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }

        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }

        $item = array(
            1 => array('money' => 6,'name'=> '6元'),
	        2 => array('money' => 28,'name'=> '28元'),
	        3 => array('money' => 30,'name'=> '30元'),
	        4 => array('money' => 68,'name'=> '68元'),
	        5 => array('money' => 198,'name'=> '198元'),
	        6 => array('money' => 288,'name'=> '288元'),
	        7 => array('money' => 328,'name'=> '328元'),
	        8 => array('money' => 648,'name'=> '648元'),
	        9 => array('money' => 1000,'name'=> '1000元'),
	        10 => array('money' => 2000,'name'=> '2000元'),
        );
        $where = " and `paytype` != 'houtai'";
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $total =0;
        if(!empty($_POST)){
            if($_POST['uid']){
                $where .= ' and roleid='.$_POST['uid'];
            }
            if(!empty($_POST['channels'])){
                 $channels = $_POST['channels'];
                 $platforms = implode('","', $_POST['channels']);
                 $where .= ' and `platform` IN ("'.$platforms.'")';
            }
            if(!empty($_POST['startTime'])){
                $start = $_POST['startTime'];
                $startTime = strtotime($start);
            }
            if(!empty($_POST['endTime'])){
                $end = $_POST['endTime'];
                $endTime = strtotime($end);
            }

            if(!empty($_POST['item'])){
                $money = $item[$_POST['item']]['money'];
                // $doller = Master::returnDoller($money);

                if ($item[$_POST['item']]['name'] == 'G') {
                    $where .= " AND `money` = {$doller} AND `diamond` > 10000 ";
                }else{
                    $where .= " AND (`money` = {$money} OR `money` = {$doller}) AND `diamond` < 10000 ";
                }
            }
         }
         $where .= " and ptime>={$startTime} and ptime<={$endTime}";
         $list = array();

        $server = array("all");
        if (!empty($_POST['server'])) {
            $server = explode('-',trim($_POST['server']));
        }
        $startS = $server[0];
        $endS = $server[1];
        if ($startS!="all" && empty($endS)){
            echo "<script>alert('区服输入错误!');</script>";
            return false;
        }

        $regList = array();
        if (isset($_POST['stype']) && $_POST['stype'] == 2) {

            $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`>={$startTime} and `reg_time`<={$endTime}";
            $db = Common::getDbBySevId(1);
            $reg_info = $db->fetchArray($cx_zc_sql);

            if (!empty($reg_info)) {

                foreach ($reg_info as $val) {
                    $regtime = date('Ymd', $val['r']);
                    $regList[$regtime][] = $val['u'];

                    if (!empty($val['d'])) {
                        $val['d'] = json_decode($val['d'], true);
                        foreach ($val['d'] as $seid => $data) {

                            $reg = date('Ymd', $data['reg_time']);
                            if ($reg == $regtime) {

                                $regList[$regtime][] = $data['uid'];
                            }
                        }
                    }
                    unset($val);
                }
                //用完释放
                unset($reg_info);
            }
        }

        $userList = array();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }

            if ( $startS!="all" && ($v['id'] < $startS || $v['id'] > $endS) ){
                continue;
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                continue;
            }

            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $sql = 'select * from `t_order` where `status`>0'.$where.' order by `ctime` desc';
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $res = $db->fetchArray($sql);

            $newList = array();
            if (isset($_POST['stype']) && $_POST['stype'] == 2) {

                foreach ($res as $val) {
                    $ptime = date('Ymd', $val['ptime']);
                    if (!in_array($val['roleid'], $regList[$ptime])) {
                        continue;
                    }
                    $newList[] = $val;
                }

            }else{

                $newList = $res;
            }

            foreach ($newList as $nk => $nv) {

                if (!isset($userList[$nv['roleid']])) {

                    $orderSql = "select * from `t_order` where `status`>0 and `roleid` = {$nv['roleid']}";
                    $orderRes = $db->fetchArray($orderSql);

                    $pay = 0;
                    foreach ($orderRes as $ok => $ov) {
                        $pay += $ov['money'];
                    }

                    $userData = new UserModel($nv['roleid']);
                    $userList[$nv['roleid']] = array(
                        "regtime" => $userData->info["regtime"],
                        "vip" => $userData->info["vip"],
                        "pay" => round($pay,2),
                    );
                }
            }

            $list = array_merge($list,$newList);
        }

        if(!empty($list)){
             foreach ($list as $key => $val){
                $list[$key]['rmb'] = $val['money'];

                if ($val['money'] == 35) {
                    $list[$key]['diamond'] = "周卡";
                }else if ($val['money'] == 28) {
                    $list[$key]['diamond'] = "月卡";
                }else if ($val['money'] == 288) {
                    $list[$key]['diamond'] = "年卡";
                }

                $val['money'] = $val['money'];
                $list[$key]['money'] = $val['money'];
                $list[$key]['regtime'] = $userList[$val['roleid']]["regtime"];
                $list[$key]['vip'] = $userList[$val['roleid']]["vip"];
                $list[$key]['pay'] = $userList[$val['roleid']]["pay"];
                $total += $val['money'];

                if ($val['diamond'] > 10000) {

                    $temp= $val['diamond'] /10000;
                    if( $temp >= 200){
                        $hid = intval($temp - 100);
                    }else{
                        $hid = $temp % 100;
                    }
                    $list[$key]['diamond'] = $gift_bag[$hid]["name_cn"];
                }
            }
        }

        $flag=array();
        foreach($list as $arr2){
            $flag[] = $arr2["ptime"];
        }
        array_multisort($flag, SORT_DESC, $list);

        if(isset($_POST['excel'])){
            $dataArray = array();
            $xindex = $yindex = 0;
            $maxRowNum = 65536;// 设置excel每张表最大记录数
            $xlsTitles = array('日期', '订单号','平台订单号','平台','openID','角色ID','充值金额(美元)','充值金额(RMB)','充值元宝','注册时间','vip','总充值金额');// EXCEL工作表表头
            if (is_array($list)) {
                foreach ($list as $k => $v) {
                    if ( 0 == $yindex ) {
                        $dataArray[$xindex][$yindex] = $xlsTitles;
                    }
                    $yindex++;
                    $dataArray[$xindex][$yindex] = array(
                        date('Y-m-d H:i:s',$v["ptime"]),
                        $v['orderid'],
                        $v['tradeno'],
                        $v['platform'],
                        $v['openid'],
                        $v['roleid'],
                        $v['money'],
                        $v['rmb'],
                        $v['diamond'],
                        date('Y-m-d H:i:s',$v["regtime"]),
                        $v['vip'],
                        $v['pay']
                    );
                    if ( $yindex >= $maxRowNum ) {
                        $xindex++;
                        $yindex = 0;
                    }
                }
                if ( !empty($dataArray) ) {
                    Common::exportExcel($dataArray);
                }
            }
        }else{

            $SevidCfg = Common::getSevidCfg($y_serid);
            include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
        }
    }

    /**
     * 充值情况
     */
    public function payInfo() {
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }

        $item = array(
            1 => array('money' => 6,'name'=> '6元'),
            2 => array('money' => 28,'name'=> '28元'),
            3 => array('money' => 30,'name'=> '30元'),
            4 => array('money' => 68,'name'=> '68元'),
            5 => array('money' => 198,'name'=> '198元'),
            6 => array('money' => 288,'name'=> '288元'),
            7 => array('money' => 328,'name'=> '328元'),
            8 => array('money' => 648,'name'=> '648元'),
            9 => array('money' => 26,'name'=> '26元'),
            10 => array('money' => 27,'name'=> '27元'),
            11 => array('money' => 29,'name'=> '29元'),
            12 => array('money' => 31,'name'=> '31元'),
            13 => array('money' => 32,'name'=> '32元'),
            14 => array('money' => 33,'name'=> '33元'),
            15 => array('money' => 34,'name'=> '34元'),
            16 => array('money' => 1000,'name'=> '1000元'),
            17 => array('money' => 2000,'name'=> '2000元'),
        );
        $where = " and `paytype` != ''";
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $total =0;
        if(!empty($_POST)){
            if($_POST['uid']){
                $where .= ' and roleid='.$_POST['uid'];
            }
            if(!empty($_POST['platForms']) && $_POST['platForms'] != 'all'){
                $where .= ' and `platform` like \''.$_POST['platForms'].'\'';
            }
            if(!empty($_POST['startTime'])){
                $start = $_POST['startTime'];
                $startTime = strtotime($start);
            }
            if(!empty($_POST['endTime'])){
                $end = $_POST['endTime'];
                $endTime = strtotime($end);
            }

            if(!empty($_POST['item'])){
                $where .= " and `money`={$item[$_POST['item']]['money']}";
            }

        }
        $where .= " and ptime>={$startTime} and ptime<={$endTime}";
        $dataInfo = array();
        $total = 0;
        $totalDollors = 0;
        if($_POST['serverid'] <= 0){ //全服数据查询
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                if ($_POST['serverid'] == -1){
                    if ($v['id']%2 == 0){
                        continue;
                    }
                }
                if ($_POST['serverid'] == -2){
                    if ($v['id']%2 != 0){
                        continue;
                    }
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                // 公共用的前缀
                $sql = "select `ptime`,`money` from `t_order` 
            where `status`>0 ".$where.'  order by `ptime` asc';
                $result = $db->fetchArray($sql);
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $rk => $rv) {
                        $dataInfo[date('Y-m-d', $rv['ptime'])]['rmb'] += (float) $rv['money'];
                        $total += (float) $rv['money'];
                        $dataInfo[date('Y-m-d', $rv['ptime'])]['dollor'] += Master::returnDoller($rv['money']);
                        $totalDollors  += Master::returnDoller($rv['money']);
                    }
                }
            }

        }else{ //单服数据
            $serverid = $_POST['serverid'];
            $db = Common::getDbBySevId($serverid);
            $sql = "select `ptime`,`money` from `t_order` 
            where `status`>0 ".$where.' order by `ptime` asc';
            $result = $db->fetchArray($sql);
            if (is_array($result) && !empty($result)) {
                foreach ($result as $rk => $rv) {
                    $dataInfo[date('Y-m-d', $rv['ptime'])]['rmb'] += (float) $rv['money'];
                    $total += (float) $rv['money'];
                    $dataInfo[date('Y-m-d', $rv['ptime'])]['dollor'] += Master::returnDoller($rv['money']);
                    $totalDollors  += Master::returnDoller($rv['money']);
                }
            }
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 总览简易版
     */
    public function pandect(){
        if (empty($_POST['beginDate'])) {
            $_POST['beginDate'] = date("Y-m-01 00:00:00");
        }
        if (empty($_POST['endDate'])) {
            $_POST['endDate'] = date("Y-m-d 23:59:59", strtotime("-1 day"));
        }
        $beginDateTime = strtotime($_POST['beginDate']);
        $endDateTime = strtotime($_POST['endDate']);
        Common::loadModel('ServerModel');
        $id = ServerModel::getDefaultServerId();
        $flowDb = Common::getDbBySevId($id, 'flow');

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }
        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "本地";
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }
        $platform = implode("','", $channels);
        $sql = "SElECT * FROM `pandect` WHERE `time`>=".$beginDateTime." AND `time`<".$endDateTime." AND `platform` IN ('{$platform}') ORDER BY `time` ASC;";
        $result = $flowDb->fetchArray($sql);
        $data = array();
        if (!empty($result)){
            foreach ($result as $key => $value){
                $time = date('Y-m-d', $value['time']);
                $data[$time]['register'] += $value['register'];
                $data[$time]['income'] += $value['income'];
                $data[$time]['pay_man'] += $value['pay_man'];
                $data[$time]['pay_count'] += $value['pay_count'];
                $data[$time]['new_pay'] += $value['new_pay'];
                $data[$time]['new_income'] += $value['new_income'];
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
        * 总览
        *
        * */
    public function totalCXLiuCun(){
        //不限制执行时间
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        if (empty($_POST['startTime'])) {
            $_POST['startTime'] = date("Y-m-01 00:00:00");
        }
        if (empty($_POST['endTime'])) {
            $_POST['endTime'] = date("Y-m-d 23:59:59");
        }
        $beginDateTime = $startTime = strtotime($_POST['startTime']);
        $endDateTime = $endTime = strtotime($_POST['endTime']);

        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $serverList = ServerModel::getServList();

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }
        if (empty($_POST['select_type'])) {
            $_POST['select_type'] = 1;
        }

        $server = array("all");
        if (!empty($_POST['server'])) {
            $server = explode('-',trim($_POST['server']));
        }

        $startS = $server[0];
        $endS = $server[1];
        if ($startS!="all" && empty($endS)){
            echo "<script>alert('区服输入错误!');</script>";
            return false;
        }

        if ($startS!="all") {

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }

                if ( $k < $startS || $k > $endS ){
                    continue;
                }
                $sid[] = $k;
            }
        }

        $cache = Common::getCacheBySevId($serverid);
        //相同查询条件缓存起来
        $cacheKeyArr = $_POST;
        unset($cacheKeyArr['select_type']);
        $cacheKey = 'ADMIN:TOTALCXLIUCUN'.md5(json_encode($cacheKeyArr));
        //结果集
        $volist = array();
        $table_div = Common::get_table_div();
        if ($_POST['select_type'] == 1) {
            $volist = $cache->get($cacheKey);
        } else {
            $cache->delete($cacheKey);
        }
        $mUse = memory_get_usage();
        if (empty($volist)) {
            //时间
            $result = array();
            $resultLog = array();
            $login_where = '';
            if ($_POST['excel-all'] == 1){
                if ($_POST['server']){
                    $server = explode('-',$_POST['server']);
                }
                foreach ($serverList as $skey => $svalue){
                    if ( $startS!="all" && ($skey < $startS || $skey > $endS) ){
                        continue;
                    }
                    if(!empty($skey)) {
                        $login_where = " and `servid`={$skey}";
                    }else{
                        continue;
                    }
                    if (empty($result)) {
                        //获取注册信息
                        /********************注册统计***********************/
                        $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`<{$endDateTime} and `reg_time`>={$beginDateTime} {$login_where}";
                        $db = Common::getDbBySevId($serverid);
                        $reg_info = $db->fetchArray($cx_zc_sql);
                        /*echo 'reg use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        if (!empty($reg_info)) {

                            foreach ($reg_info as $val) {
                                $regtime = date('Ymd', $val['r']);
                                $platform = $val['p'];

                                if (empty($skey)) {
                                    $result[$regtime][$platform]['openid'][] = $val['o'];
                                    $result[$regtime][$platform]['reg_pnum'] += 1;

                                    $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                    $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                                } elseif ($val['s'] == $skey) {
                                    $result[$regtime][$platform]['openid'][] = $val['o'];
                                    $result[$regtime][$platform]['reg_pnum'] += 1;

                                    $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                    $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                                } elseif (!empty($val['d'])) {
                                    $val['d'] = json_decode($val['d'], true);
                                    foreach ($val['d'] as $seid => $data) {
                                        if ($seid == $skey) {
                                            $result[$data['reg_time']][$platform]['openid'][] = $val['o'];
                                            $result[$data['reg_time']][$platform]['reg_pnum'] += 1;

                                            $resultLog[$data['reg_time']][$platform]['openid'][$val['o']] = 1;
                                            $resultLog[$data['reg_time']][$platform]['uid'][$val['u']] = 1;
                                            continue;
                                        }
                                    }
                                }
                                unset($val);
                            }
                            //用完释放
                            unset($reg_info);
                        }
                        /*echo 'reg cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/

                        /*********************登录统计*************************/
                        /*
                        $login_info = array();
                        $startDateTimeTemp = $beginDateTime;
                        while ($startDateTimeTemp <$endDateTime) {
                            //分开查询
                            $endDateTimeTemp = $startDateTimeTemp + 86400;
                            $cx_login_sql = "select * from `login_log` where `login_time`<{$endDateTimeTemp} and `login_time`>={$startDateTimeTemp} {$login_where}";
                            $login_info = array_merge($login_info, $db->fetchArray($cx_login_sql));
                            $startDateTimeTemp = $endDateTimeTemp;
                        }
                        */
                        if($_POST['select_type_lc'] == 2){
                            $cx_login_sql = "select `openid` AS o,`login_time` AS l,`platform` AS p from `login_log` where `login_time`<{$endDateTime} and `login_time`>={$beginDateTime} {$login_where}";
                            $login_info = $db->fetchArray($cx_login_sql);

                            /*echo 'log use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                            $login_result = array();
                            if (!empty($login_info)) {
                                foreach ($login_info as $val) {
                                    $logintime = date('Ymd', $val['l']);
                                    $platform = $val['p'];
                                    if (empty($login_result[$logintime][$platform]['openid'])) {
                                        $login_result[$logintime][$platform]['openid'] = array();
                                    }
                                    if (!isset($login_result[$logintime][$platform]['openid'][$val['o']])) {
                                        $login_result[$logintime][$platform]['openid'][$val['o']] = $val['o'];
                                        $result[$logintime][$platform]['login_pnum'] += 1;
                                    }
                                    unset($val);
                                }
                                //用完释放
                                unset($login_info);
                            }
                            /*echo 'log cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                            //统计留存问题
                            if (!empty($result)) {
                                foreach ($result as $time => $res) {
                                    $two_time = date('Ymd', strtotime('+1 day', strtotime($time)));
                                    $three_time = date('Ymd', strtotime('+2 day', strtotime($time)));
                                    $day4_time = date('Ymd', strtotime('+3 day', strtotime($time)));
                                    $five_time = date('Ymd', strtotime('+4 day', strtotime($time)));
                                    $day6_time = date('Ymd', strtotime('+5 day', strtotime($time)));
                                    $week_time = date('Ymd', strtotime('+6 day', strtotime($time)));
                                    $day8_time = date('Ymd', strtotime('+7 day', strtotime($time)));
                                    $day9_time = date('Ymd', strtotime('+8 day', strtotime($time)));
                                    $day10_time = date('Ymd', strtotime('+9 day', strtotime($time)));
                                    $day11_time = date('Ymd', strtotime('+10 day', strtotime($time)));
                                    $day12_time = date('Ymd', strtotime('+11 day', strtotime($time)));
                                    $day13_time = date('Ymd', strtotime('+12 day', strtotime($time)));
                                    $two_week_time = date('Ymd', strtotime('+13 day', strtotime($time)));
                                    $day15_time = date('Ymd', strtotime('+14 day', strtotime($time)));
                                    $day16_time = date('Ymd', strtotime('+15 day', strtotime($time)));
                                    $day17_time = date('Ymd', strtotime('+16 day', strtotime($time)));
                                    $day18_time = date('Ymd', strtotime('+17 day', strtotime($time)));
                                    $day19_time = date('Ymd', strtotime('+18 day', strtotime($time)));
                                    $day20_time = date('Ymd', strtotime('+19 day', strtotime($time)));
                                    $day21_time = date('Ymd', strtotime('+20 day', strtotime($time)));
                                    $day22_time = date('Ymd', strtotime('+21 day', strtotime($time)));
                                    $day23_time = date('Ymd', strtotime('+22 day', strtotime($time)));
                                    $day24_time = date('Ymd', strtotime('+23 day', strtotime($time)));
                                    $day25_time = date('Ymd', strtotime('+24 day', strtotime($time)));
                                    $day26_time = date('Ymd', strtotime('+25 day', strtotime($time)));
                                    $day27_time = date('Ymd', strtotime('+26 day', strtotime($time)));
                                    $day28_time = date('Ymd', strtotime('+27 day', strtotime($time)));
                                    $day29_time = date('Ymd', strtotime('+28 day', strtotime($time)));
                                    $day30_time = date('Ymd', strtotime('+29 day', strtotime($time)));
                                    foreach ($res as $plat => $info) {
                                        $result[$time][$plat]['two_pnum'] = 0;
                                        $result[$time][$plat]['three_time'] = 0;
                                        $result[$time][$plat]['day4_pnum'] = 0;
                                        $result[$time][$plat]['five_time'] = 0;
                                        $result[$time][$plat]['day6_pnum'] = 0;
                                        $result[$time][$plat]['week_pnum'] = 0;
                                        $result[$time][$plat]['day8_pnum'] = 0;
                                        $result[$time][$plat]['day9_pnum'] = 0;
                                        $result[$time][$plat]['day10_pnum'] = 0;
                                        $result[$time][$plat]['day11_pnum'] = 0;
                                        $result[$time][$plat]['day12_pnum'] = 0;
                                        $result[$time][$plat]['day13_pnum'] = 0;
                                        $result[$time][$plat]['two_week_time'] = 0;
                                        $result[$time][$plat]['day15_time'] = 0;
                                        $result[$time][$plat]['day16_time'] = 0;
                                        $result[$time][$plat]['day17_time'] = 0;
                                        $result[$time][$plat]['day18_time'] = 0;
                                        $result[$time][$plat]['day19_time'] = 0;
                                        $result[$time][$plat]['day20_time'] = 0;
                                        $result[$time][$plat]['day21_time'] = 0;
                                        $result[$time][$plat]['day22_time'] = 0;
                                        $result[$time][$plat]['day23_time'] = 0;
                                        $result[$time][$plat]['day24_time'] = 0;
                                        $result[$time][$plat]['day25_time'] = 0;
                                        $result[$time][$plat]['day26_time'] = 0;
                                        $result[$time][$plat]['day27_time'] = 0;
                                        $result[$time][$plat]['day28_time'] = 0;
                                        $result[$time][$plat]['day29_time'] = 0;
                                        $result[$time][$plat]['day30_time'] = 0;
                                        //两天
                                        if (!empty($info['openid']) && !empty($login_result[$two_time][$plat]['openid'])) {
                                            $two_pnum = array_intersect($info['openid'], $login_result[$two_time][$plat]['openid']);
                                            $result[$time][$plat]['two_pnum'] = count($two_pnum);
                                        }
                                        //三天
                                        if (!empty($info['openid']) && !empty($login_result[$three_time][$plat]['openid'])) {
                                            $three_pnum = array_intersect($info['openid'], $login_result[$three_time][$plat]['openid']);
                                            $result[$time][$plat]['three_pnum'] = count($three_pnum);
                                        }
                                        //四天
                                        if (!empty($info['openid']) && !empty($login_result[$day4_time][$plat]['openid'])) {
                                            $day4_pnum = array_intersect($info['openid'], $login_result[$day4_time][$plat]['openid']);
                                            $result[$time][$plat]['day4_pnum'] = count($day4_pnum);
                                        }
                                        //五天
                                        if (!empty($info['openid']) && !empty($login_result[$five_time][$plat]['openid'])) {
                                            $five_pnum = array_intersect($info['openid'], $login_result[$five_time][$plat]['openid']);
                                            $result[$time][$plat]['five_pnum'] = count($five_pnum);
                                        }
                                        //六天
                                        if (!empty($info['openid']) && !empty($login_result[$day6_time][$plat]['openid'])) {
                                            $day6_pnum = array_intersect($info['openid'], $login_result[$day6_time][$plat]['openid']);
                                            $result[$time][$plat]['day6_pnum'] = count($day6_pnum);
                                        }

                                        //一周
                                        if (!empty($info['openid']) && !empty($login_result[$week_time][$plat]['openid'])) {
                                            $week_pnum = array_intersect($info['openid'], $login_result[$week_time][$plat]['openid']);
                                            $result[$time][$plat]['week_pnum'] = count($week_pnum);
                                        }



                                        //8天
                                        if (!empty($info['openid']) && !empty($login_result[$day8_time][$plat]['openid'])) {
                                            $day8_pnum = array_intersect($info['openid'], $login_result[$day8_time][$plat]['openid']);
                                            $result[$time][$plat]['day8_pnum'] = count($day8_pnum);
                                        }
                                        //9天
                                        if (!empty($info['openid']) && !empty($login_result[$day9_time][$plat]['openid'])) {
                                            $day9_pnum = array_intersect($info['openid'], $login_result[$day9_time][$plat]['openid']);
                                            $result[$time][$plat]['day9_pnum'] = count($day9_pnum);
                                        }
                                        //10天
                                        if (!empty($info['openid']) && !empty($login_result[$day10_time][$plat]['openid'])) {
                                            $day10_pnum = array_intersect($info['openid'], $login_result[$day10_time][$plat]['openid']);
                                            $result[$time][$plat]['day10_pnum'] = count($day10_pnum);
                                        }
                                        //11天
                                        if (!empty($info['openid']) && !empty($login_result[$day11_time][$plat]['openid'])) {
                                            $day11_pnum = array_intersect($info['openid'], $login_result[$day11_time][$plat]['openid']);
                                            $result[$time][$plat]['day11_pnum'] = count($day11_pnum);
                                        }
                                        //12天
                                        if (!empty($info['openid']) && !empty($login_result[$day12_time][$plat]['openid'])) {
                                            $day12_pnum = array_intersect($info['openid'], $login_result[$day12_time][$plat]['openid']);
                                            $result[$time][$plat]['day12_pnum'] = count($day12_pnum);
                                        }
                                        //13天
                                        if (!empty($info['openid']) && !empty($login_result[$day13_time][$plat]['openid'])) {
                                            $day13_pnum = array_intersect($info['openid'], $login_result[$day13_time][$plat]['openid']);
                                            $result[$time][$plat]['day13_pnum'] = count($day13_pnum);
                                        }




                                        //两周
                                        if (!empty($info['openid']) && !empty($login_result[$two_week_time][$plat]['openid'])) {
                                            $two_week_pnum = array_intersect($info['openid'], $login_result[$two_week_time][$plat]['openid']);
                                            $result[$time][$plat]['two_week_pnum'] = count($two_week_pnum);
                                        }

                                        //15天
                                        if (!empty($info['openid']) && !empty($login_result[$day15_time][$plat]['openid'])) {
                                            $day15_pnum = array_intersect($info['openid'], $login_result[$day15_time][$plat]['openid']);
                                            $result[$time][$plat]['day15_pnum'] = count($day15_pnum);
                                        }
                                        //16天
                                        if (!empty($info['openid']) && !empty($login_result[$day16_time][$plat]['openid'])) {
                                            $day16_pnum = array_intersect($info['openid'], $login_result[$day16_time][$plat]['openid']);
                                            $result[$time][$plat]['day16_pnum'] = count($day16_pnum);
                                        }
                                        //17天
                                        if (!empty($info['openid']) && !empty($login_result[$day17_time][$plat]['openid'])) {
                                            $day17_pnum = array_intersect($info['openid'], $login_result[$day17_time][$plat]['openid']);
                                            $result[$time][$plat]['day17_pnum'] = count($day17_pnum);
                                        }
                                        //18天
                                        if (!empty($info['openid']) && !empty($login_result[$day18_time][$plat]['openid'])) {
                                            $day18_pnum = array_intersect($info['openid'], $login_result[$day18_time][$plat]['openid']);
                                            $result[$time][$plat]['day18_pnum'] = count($day18_pnum);
                                        }
                                        //19天
                                        if (!empty($info['openid']) && !empty($login_result[$day19_time][$plat]['openid'])) {
                                            $day19_pnum = array_intersect($info['openid'], $login_result[$day19_time][$plat]['openid']);
                                            $result[$time][$plat]['day19_pnum'] = count($day19_pnum);
                                        }
                                        //20天
                                        if (!empty($info['openid']) && !empty($login_result[$day20_time][$plat]['openid'])) {
                                            $day20_pnum = array_intersect($info['openid'], $login_result[$day20_time][$plat]['openid']);
                                            $result[$time][$plat]['day20_pnum'] = count($day20_pnum);
                                        }
                                        //21天
                                        if (!empty($info['openid']) && !empty($login_result[$day21_time][$plat]['openid'])) {
                                            $day21_pnum = array_intersect($info['openid'], $login_result[$day21_time][$plat]['openid']);
                                            $result[$time][$plat]['day21_pnum'] = count($day21_pnum);
                                        }
                                        //22天
                                        if (!empty($info['openid']) && !empty($login_result[$day22_time][$plat]['openid'])) {
                                            $day22_pnum = array_intersect($info['openid'], $login_result[$day22_time][$plat]['openid']);
                                            $result[$time][$plat]['day22_pnum'] = count($day22_pnum);
                                        }
                                        //23天
                                        if (!empty($info['openid']) && !empty($login_result[$day23_time][$plat]['openid'])) {
                                            $day23_pnum = array_intersect($info['openid'], $login_result[$day23_time][$plat]['openid']);
                                            $result[$time][$plat]['day23_pnum'] = count($day23_pnum);
                                        }
                                        //24天
                                        if (!empty($info['openid']) && !empty($login_result[$day24_time][$plat]['openid'])) {
                                            $day24_pnum = array_intersect($info['openid'], $login_result[$day24_time][$plat]['openid']);
                                            $result[$time][$plat]['day24_pnum'] = count($day24_pnum);
                                        }
                                        //25天
                                        if (!empty($info['openid']) && !empty($login_result[$day25_time][$plat]['openid'])) {
                                            $day25_pnum = array_intersect($info['openid'], $login_result[$day25_time][$plat]['openid']);
                                            $result[$time][$plat]['day25_pnum'] = count($day25_pnum);
                                        }
                                        //26天
                                        if (!empty($info['openid']) && !empty($login_result[$day26_time][$plat]['openid'])) {
                                            $day26_pnum = array_intersect($info['openid'], $login_result[$day26_time][$plat]['openid']);
                                            $result[$time][$plat]['day26_pnum'] = count($day26_pnum);
                                        }
                                        //27天
                                        if (!empty($info['openid']) && !empty($login_result[$day27_time][$plat]['openid'])) {
                                            $day27_pnum = array_intersect($info['openid'], $login_result[$day27_time][$plat]['openid']);
                                            $result[$time][$plat]['day27_pnum'] = count($day27_pnum);
                                        }
                                        //28天
                                        if (!empty($info['openid']) && !empty($login_result[$day28_time][$plat]['openid'])) {
                                            $day28_pnum = array_intersect($info['openid'], $login_result[$day28_time][$plat]['openid']);
                                            $result[$time][$plat]['day28_pnum'] = count($day28_pnum);
                                        }
                                        //29天
                                        if (!empty($info['openid']) && !empty($login_result[$day29_time][$plat]['openid'])) {
                                            $day29_pnum = array_intersect($info['openid'], $login_result[$day29_time][$plat]['openid']);
                                            $result[$time][$plat]['day29_pnum'] = count($day29_pnum);
                                        }
                                        //30天
                                        if (!empty($info['openid']) && !empty($login_result[$day30_time][$plat]['openid'])) {
                                            $day30_pnum = array_intersect($info['openid'], $login_result[$day30_time][$plat]['openid']);
                                            $result[$time][$plat]['day30_pnum'] = count($day30_pnum);
                                        }
                                        unset($result[$time][$plat]['openid']);
                                    }
                                }
                                unset($login_result);
                            }
                        }
                        /* echo '留存 cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        /******************订单*******************/
                        $list = array();
                        if (!empty($serverList)) {
                            foreach ($serverList as $k => $v) {
                                if (empty($v)) {
                                    continue;
                                }

                                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                                if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg1['sevid']) {
                                    continue;
                                }

                                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                    continue;
                                }

                                if (!empty($skey) && $SevidCfg1['sevid'] != $skey) {
                                    continue;
                                }
                                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                                $cx_order_sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`roleid` AS r,`platform` AS p from `t_order` where `status`>0 and `paytype` != 'houtai' and `ptime`<{$endDateTime} and `ptime`>={$beginDateTime};";
                                $order_list = $db->fetchArray($cx_order_sql);
                                if (!empty($order_list)) {
                                    $list = array_merge($list, $order_list);
                                }
                            }
                        }
                        if (!empty($list)) {
                            $ls_total_arr = array();//临时总存放
                            $roleid_total_arr = array();
                            foreach ($list as $value) {
                                $ptime = date('Ymd', $value['pt']);
                                $oldtime = date('Ymd', $value['pt'] - 86400);
                                $platform = $value['p'];
                                if (empty($ls_total_arr[$ptime])) {
                                    $ls_total_arr[$ptime] = array();
                                }
                                if (!isset($ls_total_arr[$ptime][$value['r']])) {
                                    $result[$ptime][$platform]['total_rechange_pnum'] += 1;//总充值人数
                                    $ls_total_arr[$ptime][$value['r']] = $value['r'];
                                }
                                if (!in_array($value['r'], $roleid_total_arr)) {

                                    array_push($roleid_total_arr, $value['r']);
                                    $result[$ptime][$platform]['max_rechange_pnum'] += 1;//总充值人数
                                }
                                $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                                // $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                                $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                                $result[$ptime][$platform]['total_doller'] += Master::returnDoller($value['m']);//总金额

                                //新增统计
                                if (isset($resultLog[$ptime][$platform]['uid'][$value['r']])) {

                                    $result[$ptime][$platform]['new_money'] += $value['m'];
                                    if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1 || $resultLog[$ptime][$platform]['uid'][$value['r']] == 1 ) {
                                        $result[$ptime][$platform]['new_rechange_pnum'] += 1;
                                        $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
                                        $resultLog[$ptime][$platform]['uid'][$value['r']] = 2;
                                        // $result[$ptime][$platform]['new_money'] += $value['m'];
                                    }
                                    // $result[$ptime][$platform]['new_money'] += $value['m'];
                                }
                            }
                        }
                        if (!empty($result)){
                            ksort($result);
                        }

                    }
                    $platforms = $_POST['channels'];
                    if (!empty($result)) {
                        foreach ($result as $key => $val) {
                            foreach ($val as $plat => $val1) {
                                /*//临时做限制,防止少统计后台充值
                                if (empty($auth[$_SESSION['CURRENT_USER']]) && $plat == 'local') {
                                    continue;
                                }*/
                                if (!in_array($plat, $platforms)) {
                                    unset($result[$key][$plat]);
                                }
                            }
                        }
                        if ($result) {
                            $max_rechange_pnum = 0;//累计充值人数
                            foreach ($result as $time => $info) {
                                if (empty($start)) {
                                    $start = $time;
                                }
                                $end = $time;
                                $reg_pnum = 0;//注册人数
                                $login_pnum = 0;//登录人数
                                $day_pnum = 0;//次日留存人数
                                $three_pnum = 0;//3日留存人数
                                $day4_pnum = 0;//4日留存人数
                                $five_pnum = 0;//5日留存人数
                                $day6_pnum = 0;//5日留存人数
                                $week_pnum = 0;//7日留存人数
                                $day8_pnum = 0;//8日留存人数
                                $day9_pnum = 0;//9日留存人数
                                $day10_pnum = 0;//10日留存人数
                                $day11_pnum = 0;//11日留存人数
                                $day12_pnum = 0;//12日留存人数
                                $day13_pnum = 0;//13日留存人数
                                $two_week_pnum = 0;//14日留存人数
                                $day15_pnum = 0;//15日留存人数
                                $day16_pnum = 0;//16日留存人数
                                $day17_pnum = 0;//17日留存人数
                                $day18_pnum = 0;//18日留存人数
                                $day19_pnum = 0;//19日留存人数
                                $day20_pnum = 0;//20日留存人数
                                $day21_pnum = 0;//21日留存人数
                                $day22_pnum = 0;//22日留存人数
                                $day23_pnum = 0;//23日留存人数
                                $day24_pnum = 0;//24日留存人数
                                $day25_pnum = 0;//25日留存人数
                                $day26_pnum = 0;//26日留存人数
                                $day27_pnum = 0;//27日留存人数
                                $day28_pnum = 0;//28日留存人数
                                $day29_pnum = 0;//29日留存人数
                                $day30_pnum = 0;//30日留存人数
                                $rechange_num = 0; //付费笔数
                                $total_rechange_pnum = 0;//单日充值人数
                                $total_money = 0;//单日充值金额
                                $new_rechange_pnum = 0;//单日新增充值人数
                                $new_money = 0;//单日新增充值金额
                                $total_doller = 0;
                                foreach ($info as $plat => $val) {
                                    $reg_pnum += $val['reg_pnum'];
                                    $login_pnum += $val['login_pnum'];
                                    $day_pnum += $val['two_pnum'] ? $val['two_pnum'] : 0;
                                    $three_pnum += $val['three_pnum'] ? $val['three_pnum'] : 0;
                                    $day4_pnum += $val['day4_pnum'] ? $val['day4_pnum'] : 0;
                                    $five_pnum += $val['five_pnum'] ? $val['five_pnum'] : 0;
                                    $day6_pnum += $val['day6_pnum'] ? $val['day6_pnum'] : 0;
                                    $week_pnum += $val['week_pnum'] ? $val['week_pnum'] : 0;
                                    $day8_pnum += $val['day8_pnum'] ? $val['day8_pnum'] : 0;
                                    $day9_pnum += $val['day9_pnum'] ? $val['day9_pnum'] : 0;
                                    $day10_pnum += $val['day10_pnum'] ? $val['day10_pnum'] : 0;
                                    $day11_pnum += $val['day11_pnum'] ? $val['day11_pnum'] : 0;
                                    $day12_pnum += $val['day12_pnum'] ? $val['day12_pnum'] : 0;
                                    $day13_pnum += $val['day13_pnum'] ? $val['day13_pnum'] : 0;
                                    $two_week_pnum += $val['two_week_pnum'] ? $val['two_week_pnum'] : 0;
                                    $day15_pnum += $val['day15_pnum'] ? $val['day15_pnum'] : 0;
                                    $day16_pnum += $val['day16_pnum'] ? $val['day16_pnum'] : 0;
                                    $day17_pnum += $val['day17_pnum'] ? $val['day17_pnum'] : 0;
                                    $day18_pnum += $val['day18_pnum'] ? $val['day18_pnum'] : 0;
                                    $day19_pnum += $val['day19_pnum'] ? $val['day19_pnum'] : 0;
                                    $day20_pnum += $val['day20_pnum'] ? $val['day20_pnum'] : 0;
                                    $day21_pnum += $val['day21_pnum'] ? $val['day21_pnum'] : 0;
                                    $day22_pnum += $val['day22_pnum'] ? $val['day22_pnum'] : 0;
                                    $day23_pnum += $val['day23_pnum'] ? $val['day23_pnum'] : 0;
                                    $day24_pnum += $val['day24_pnum'] ? $val['day24_pnum'] : 0;
                                    $day25_pnum += $val['day25_pnum'] ? $val['day25_pnum'] : 0;
                                    $day26_pnum += $val['day26_pnum'] ? $val['day26_pnum'] : 0;
                                    $day27_pnum += $val['day27_pnum'] ? $val['day27_pnum'] : 0;
                                    $day28_pnum += $val['day28_pnum'] ? $val['day28_pnum'] : 0;
                                    $day29_pnum += $val['day29_pnum'] ? $val['day29_pnum'] : 0;
                                    $day30_pnum += $val['day30_pnum'] ? $val['day30_pnum'] : 0;
                                    $rechange_num += $val['rechange_num'] ? $val['rechange_num'] : 0;
                                    $total_rechange_pnum += $val['total_rechange_pnum'] ? $val['total_rechange_pnum'] : 0;
                                    $max_rechange_pnum += $val['max_rechange_pnum'] ? $val['max_rechange_pnum'] : 0;
                                    $total_money += $val['total_money'] ? $val['total_money'] : 0;
                                    $new_rechange_pnum += $val['new_rechange_pnum'] ? $val['new_rechange_pnum'] : 0;
                                    $new_money += $val['new_money'] ? $val['new_money'] : 0;
                                    $total_doller += $val['total_doller'] ? $val['total_doller'] : 0;
                                }
                                $volist[] = array(
                                    'time' => $time,
                                    'reg_pnum' => $reg_pnum,
                                    'login_pnum' => $login_pnum,
                                    'two_pnum' => $day_pnum,
                                    'three_pnum' => $three_pnum,
                                    'day4_pnum' => $day4_pnum,
                                    'five_pnum' => $five_pnum,
                                    'day6_pnum' => $day6_pnum,
                                    'week_pnum' => $week_pnum,
                                    'day8_pnum' => $day8_pnum,
                                    'day9_pnum' => $day9_pnum,
                                    'day10_pnum' => $day10_pnum,
                                    'day11_pnum' => $day11_pnum,
                                    'day12_pnum' => $day12_pnum,
                                    'day13_pnum' => $day13_pnum,
                                    'two_week_pnum' => $two_week_pnum,
                                    'day15_pnum' => $day15_pnum,
                                    'day16_pnum' => $day16_pnum,
                                    'day17_pnum' => $day17_pnum,
                                    'day18_pnum' => $day18_pnum,
                                    'day19_pnum' => $day19_pnum,
                                    'day20_pnum' => $day20_pnum,
                                    'day21_pnum' => $day21_pnum,
                                    'day22_pnum' => $day22_pnum,
                                    'day23_pnum' => $day23_pnum,
                                    'day24_pnum' => $day24_pnum,
                                    'day25_pnum' => $day25_pnum,
                                    'day26_pnum' => $day26_pnum,
                                    'day27_pnum' => $day27_pnum,
                                    'day28_pnum' => $day28_pnum,
                                    'day29_pnum' => $day29_pnum,
                                    'day30_pnum' => $day30_pnum,
                                    'rechange_num' => $rechange_num,
                                    'total_rechange_pnum' => $total_rechange_pnum,
                                    'max_rechange_pnum' => $max_rechange_pnum,
                                    'total_money' => $total_money,
                                    'total_doller' => $total_doller,
                                    'new_rechange_pnum' => $new_rechange_pnum,
                                    'new_money' => $new_money,
                                    'rechange_rate' => $login_pnum == 0 ? 0 : number_format($total_rechange_pnum * 100 / $login_pnum, 2),
                                    'new_rechange_rate' => $reg_pnum == 0 ? 0 : number_format($new_rechange_pnum * 100 / $reg_pnum, 2),
                                    'aup_rate' => $login_pnum == 0 ? 0 : number_format($total_money/$login_pnum, 2),
                                );
                            }
                            $dataInfo[$skey] = $volist;
                            unset($volist, $result);
                        }
                    }
                }
                $dataArray = array();
                $xindex = $yindex = 0;
                $maxRowNum = 65536;// 设置excel每张表最大记录数
                $xlsTitles = array('区服', '日期', '新增注册','登录用户','营收','付费人数','付费笔数','付费率','新增营收','新增付费人数','新增付费率','ARPPU','次日留存','七日留存','累计注册','累计营收','累计营收(美元)','累计LTV');// EXCEL工作表表头
                if (is_array($dataInfo)) {
                    foreach ($dataInfo as $dkey => $volist ){
                        $add_reg = 0;
                        $add_money = 0;
                        $add_doller=0;
                        foreach ($volist as $k => $v) {
                            if ( 0 == $yindex ) {
                                $dataArray[$xindex][$yindex] = $xlsTitles;
                            }
                            $yindex++;
                            $dataArray[$xindex][$yindex] = array(
                                $dkey,
                                $v['time'],
                                $v['reg_pnum'],
                                $v['login_pnum'],
                                $v['total_money'],
                                $v['total_rechange_pnum'],
                                $v['max_rechange_pnum'],
                                $v['rechange_num'],
                                $v['rechange_rate'],
                                $v['new_money'],
                                $v['new_rechange_pnum'],
                                $v['new_rechange_rate'],
                                $v['total_rechange_pnum'] == 0 ? 0 : number_format($v['total_money']/$v['total_rechange_pnum'],2),
                                $v['two_pnum'],
                                $v['week_pnum'],
                                $add_reg +=$v['reg_pnum'],
                                $add_money += $v['total_money'],
                                $add_doller += $v['total_doller'],
                                $add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)
                            );
                            if ( $yindex >= $maxRowNum ) {
                                $xindex++;
                                $yindex = 0;
                            }
                        }
                    }

                    if ( !empty($dataArray) ) {
                        Common::exportExcel($dataArray);
                    }
                }
            }else{
                if(!empty($sid)) {
                    $sidStr = implode("','", $sid);
                    $login_where = " and `servid` IN ('{$sidStr}')";
                }

                if (empty($result)) {
                    //获取注册信息
                    /********************注册统计***********************/
                    $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`<{$endDateTime} and `reg_time`>={$beginDateTime} {$login_where}";
                    $db = Common::getDbBySevId($serverid);
                    $reg_info = $db->fetchArray($cx_zc_sql);
                    /*echo 'reg use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    if (!empty($reg_info)) {
                        foreach ($reg_info as $val) {
                            $regtime = date('Ymd', $val['r']);
                            $platform = $val['p'];
                            if (empty($sid)) {
                                $result[$regtime][$platform]['openid'][] = $val['o'];
                                $result[$regtime][$platform]['reg_pnum'] += 1;

                                $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                            } elseif (in_array($val['s'], $sid)) {
                                $result[$regtime][$platform]['openid'][] = $val['o'];
                                $result[$regtime][$platform]['reg_pnum'] += 1;

                                $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                            } elseif (!empty($val['d'])) {
                                $val['d'] = json_decode($val['d'], true);
                                foreach ($val['d'] as $seid => $data) {
                                    if (in_array($seid, $sid)) {
                                        $result[$data['reg_time']][$platform]['openid'][] = $val['o'];
                                        $result[$data['reg_time']][$platform]['reg_pnum'] += 1;

                                        $resultLog[$data['reg_time']][$platform]['openid'][$val['o']] = 1;
                                        continue;
                                    }
                                }
                            }
                        }
                        //用完释放
                        unset($reg_info);
                    }
                    /*echo 'reg cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/

                    /*********************登录统计*************************/
                    /*
                    $login_info = array();
                    $startDateTimeTemp = $beginDateTime;
                    while ($startDateTimeTemp <$endDateTime) {
                        //分开查询
                        $endDateTimeTemp = $startDateTimeTemp + 86400;
                        $cx_login_sql = "select * from `login_log` where `login_time`<{$endDateTimeTemp} and `login_time`>={$startDateTimeTemp} {$login_where}";
                        $login_info = array_merge($login_info, $db->fetchArray($cx_login_sql));
                        $startDateTimeTemp = $endDateTimeTemp;
                    }
                    */
                    if($_POST['select_type_lc'] == 2){
                        $cx_login_sql = "select `openid` AS o,`login_time` AS l,`platform` AS p from `login_log` where `login_time`<{$endDateTime} and `login_time`>={$beginDateTime} {$login_where}";
                        $login_info = $db->fetchArray($cx_login_sql);

                        /*echo 'log use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        $login_result = array();
                        if (!empty($login_info)) {
                            foreach ($login_info as $val) {
                                $logintime = date('Ymd', $val['l']);
                                $platform = $val['p'];
                                if (empty($login_result[$logintime][$platform]['openid'])) {
                                    $login_result[$logintime][$platform]['openid'] = array();
                                }
                                if (!isset($login_result[$logintime][$platform]['openid'][$val['o']])) {
                                    $login_result[$logintime][$platform]['openid'][$val['o']] = $val['o'];
                                    $result[$logintime][$platform]['login_pnum'] += 1;
                                }
                            }
                            //用完释放
                            unset($login_info);
                        }
                        /*echo 'log cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        //统计留存问题
                        if (!empty($result)) {
                            foreach ($result as $time => $res) {
                                $two_time = date('Ymd', strtotime('+1 day', strtotime($time)));
                                $three_time = date('Ymd', strtotime('+2 day', strtotime($time)));
                                $day4_time = date('Ymd', strtotime('+3 day', strtotime($time)));
                                $five_time = date('Ymd', strtotime('+4 day', strtotime($time)));
                                $day6_time = date('Ymd', strtotime('+5 day', strtotime($time)));
                                $week_time = date('Ymd', strtotime('+6 day', strtotime($time)));
                                $day8_time = date('Ymd', strtotime('+7 day', strtotime($time)));
                                $day9_time = date('Ymd', strtotime('+8 day', strtotime($time)));
                                $day10_time = date('Ymd', strtotime('+9 day', strtotime($time)));
                                $day11_time = date('Ymd', strtotime('+10 day', strtotime($time)));
                                $day12_time = date('Ymd', strtotime('+11 day', strtotime($time)));
                                $day13_time = date('Ymd', strtotime('+12 day', strtotime($time)));
                                $two_week_time = date('Ymd', strtotime('+13 day', strtotime($time)));
                                $day15_time = date('Ymd', strtotime('+14 day', strtotime($time)));
                                $day16_time = date('Ymd', strtotime('+15 day', strtotime($time)));
                                $day17_time = date('Ymd', strtotime('+16 day', strtotime($time)));
                                $day18_time = date('Ymd', strtotime('+17 day', strtotime($time)));
                                $day19_time = date('Ymd', strtotime('+18 day', strtotime($time)));
                                $day20_time = date('Ymd', strtotime('+19 day', strtotime($time)));
                                $day21_time = date('Ymd', strtotime('+20 day', strtotime($time)));
                                $day22_time = date('Ymd', strtotime('+21 day', strtotime($time)));
                                $day23_time = date('Ymd', strtotime('+22 day', strtotime($time)));
                                $day24_time = date('Ymd', strtotime('+23 day', strtotime($time)));
                                $day25_time = date('Ymd', strtotime('+24 day', strtotime($time)));
                                $day26_time = date('Ymd', strtotime('+25 day', strtotime($time)));
                                $day27_time = date('Ymd', strtotime('+26 day', strtotime($time)));
                                $day28_time = date('Ymd', strtotime('+27 day', strtotime($time)));
                                $day29_time = date('Ymd', strtotime('+28 day', strtotime($time)));
                                $day30_time = date('Ymd', strtotime('+29 day', strtotime($time)));
                                foreach ($res as $plat => $info) {
                                    $result[$time][$plat]['two_pnum'] = 0;
                                    $result[$time][$plat]['three_time'] = 0;
                                    $result[$time][$plat]['day4_pnum'] = 0;
                                    $result[$time][$plat]['five_time'] = 0;
                                    $result[$time][$plat]['day6_pnum'] = 0;
                                    $result[$time][$plat]['week_pnum'] = 0;
                                    $result[$time][$plat]['day8_pnum'] = 0;
                                    $result[$time][$plat]['day9_pnum'] = 0;
                                    $result[$time][$plat]['day10_pnum'] = 0;
                                    $result[$time][$plat]['day11_pnum'] = 0;
                                    $result[$time][$plat]['day12_pnum'] = 0;
                                    $result[$time][$plat]['day13_pnum'] = 0;
                                    $result[$time][$plat]['two_week_time'] = 0;
                                    $result[$time][$plat]['day15_time'] = 0;
                                    $result[$time][$plat]['day16_time'] = 0;
                                    $result[$time][$plat]['day17_time'] = 0;
                                    $result[$time][$plat]['day18_time'] = 0;
                                    $result[$time][$plat]['day19_time'] = 0;
                                    $result[$time][$plat]['day20_time'] = 0;
                                    $result[$time][$plat]['day21_time'] = 0;
                                    $result[$time][$plat]['day22_time'] = 0;
                                    $result[$time][$plat]['day23_time'] = 0;
                                    $result[$time][$plat]['day24_time'] = 0;
                                    $result[$time][$plat]['day25_time'] = 0;
                                    $result[$time][$plat]['day26_time'] = 0;
                                    $result[$time][$plat]['day27_time'] = 0;
                                    $result[$time][$plat]['day28_time'] = 0;
                                    $result[$time][$plat]['day29_time'] = 0;
                                    $result[$time][$plat]['day30_time'] = 0;
                                    //两天
                                    if (!empty($info['openid']) && !empty($login_result[$two_time][$plat]['openid'])) {
                                        $two_pnum = array_intersect($info['openid'], $login_result[$two_time][$plat]['openid']);
                                        $result[$time][$plat]['two_pnum'] = count($two_pnum);
                                    }
                                    //三天
                                    if (!empty($info['openid']) && !empty($login_result[$three_time][$plat]['openid'])) {
                                        $three_pnum = array_intersect($info['openid'], $login_result[$three_time][$plat]['openid']);
                                        $result[$time][$plat]['three_pnum'] = count($three_pnum);
                                    }
                                    //四天
                                    if (!empty($info['openid']) && !empty($login_result[$day4_time][$plat]['openid'])) {
                                        $day4_pnum = array_intersect($info['openid'], $login_result[$day4_time][$plat]['openid']);
                                        $result[$time][$plat]['day4_pnum'] = count($day4_pnum);
                                    }
                                    //五天
                                    if (!empty($info['openid']) && !empty($login_result[$five_time][$plat]['openid'])) {
                                        $five_pnum = array_intersect($info['openid'], $login_result[$five_time][$plat]['openid']);
                                        $result[$time][$plat]['five_pnum'] = count($five_pnum);
                                    }
                                    //六天
                                    if (!empty($info['openid']) && !empty($login_result[$day6_time][$plat]['openid'])) {
                                        $day6_pnum = array_intersect($info['openid'], $login_result[$day6_time][$plat]['openid']);
                                        $result[$time][$plat]['day6_pnum'] = count($day6_pnum);
                                    }

                                    //一周
                                    if (!empty($info['openid']) && !empty($login_result[$week_time][$plat]['openid'])) {
                                        $week_pnum = array_intersect($info['openid'], $login_result[$week_time][$plat]['openid']);
                                        $result[$time][$plat]['week_pnum'] = count($week_pnum);
                                    }



                                    //8天
                                    if (!empty($info['openid']) && !empty($login_result[$day8_time][$plat]['openid'])) {
                                        $day8_pnum = array_intersect($info['openid'], $login_result[$day8_time][$plat]['openid']);
                                        $result[$time][$plat]['day8_pnum'] = count($day8_pnum);
                                    }
                                    //9天
                                    if (!empty($info['openid']) && !empty($login_result[$day9_time][$plat]['openid'])) {
                                        $day9_pnum = array_intersect($info['openid'], $login_result[$day9_time][$plat]['openid']);
                                        $result[$time][$plat]['day9_pnum'] = count($day9_pnum);
                                    }
                                    //10天
                                    if (!empty($info['openid']) && !empty($login_result[$day10_time][$plat]['openid'])) {
                                        $day10_pnum = array_intersect($info['openid'], $login_result[$day10_time][$plat]['openid']);
                                        $result[$time][$plat]['day10_pnum'] = count($day10_pnum);
                                    }
                                    //11天
                                    if (!empty($info['openid']) && !empty($login_result[$day11_time][$plat]['openid'])) {
                                        $day11_pnum = array_intersect($info['openid'], $login_result[$day11_time][$plat]['openid']);
                                        $result[$time][$plat]['day11_pnum'] = count($day11_pnum);
                                    }
                                    //12天
                                    if (!empty($info['openid']) && !empty($login_result[$day12_time][$plat]['openid'])) {
                                        $day12_pnum = array_intersect($info['openid'], $login_result[$day12_time][$plat]['openid']);
                                        $result[$time][$plat]['day12_pnum'] = count($day12_pnum);
                                    }
                                    //13天
                                    if (!empty($info['openid']) && !empty($login_result[$day13_time][$plat]['openid'])) {
                                        $day13_pnum = array_intersect($info['openid'], $login_result[$day13_time][$plat]['openid']);
                                        $result[$time][$plat]['day13_pnum'] = count($day13_pnum);
                                    }

                                    //两周
                                    if (!empty($info['openid']) && !empty($login_result[$two_week_time][$plat]['openid'])) {
                                        $two_week_pnum = array_intersect($info['openid'], $login_result[$two_week_time][$plat]['openid']);
                                        $result[$time][$plat]['two_week_pnum'] = count($two_week_pnum);
                                    }

                                    //15天
                                    if (!empty($info['openid']) && !empty($login_result[$day15_time][$plat]['openid'])) {
                                        $day15_pnum = array_intersect($info['openid'], $login_result[$day15_time][$plat]['openid']);
                                        $result[$time][$plat]['day15_pnum'] = count($day15_pnum);
                                    }
                                    //16天
                                    if (!empty($info['openid']) && !empty($login_result[$day16_time][$plat]['openid'])) {
                                        $day16_pnum = array_intersect($info['openid'], $login_result[$day16_time][$plat]['openid']);
                                        $result[$time][$plat]['day16_pnum'] = count($day16_pnum);
                                    }
                                    //17天
                                    if (!empty($info['openid']) && !empty($login_result[$day17_time][$plat]['openid'])) {
                                        $day17_pnum = array_intersect($info['openid'], $login_result[$day17_time][$plat]['openid']);
                                        $result[$time][$plat]['day17_pnum'] = count($day17_pnum);
                                    }
                                    //18天
                                    if (!empty($info['openid']) && !empty($login_result[$day18_time][$plat]['openid'])) {
                                        $day18_pnum = array_intersect($info['openid'], $login_result[$day18_time][$plat]['openid']);
                                        $result[$time][$plat]['day18_pnum'] = count($day18_pnum);
                                    }
                                    //19天
                                    if (!empty($info['openid']) && !empty($login_result[$day19_time][$plat]['openid'])) {
                                        $day19_pnum = array_intersect($info['openid'], $login_result[$day19_time][$plat]['openid']);
                                        $result[$time][$plat]['day19_pnum'] = count($day19_pnum);
                                    }
                                    //20天
                                    if (!empty($info['openid']) && !empty($login_result[$day20_time][$plat]['openid'])) {
                                        $day20_pnum = array_intersect($info['openid'], $login_result[$day20_time][$plat]['openid']);
                                        $result[$time][$plat]['day20_pnum'] = count($day20_pnum);
                                    }
                                    //21天
                                    if (!empty($info['openid']) && !empty($login_result[$day21_time][$plat]['openid'])) {
                                        $day21_pnum = array_intersect($info['openid'], $login_result[$day21_time][$plat]['openid']);
                                        $result[$time][$plat]['day21_pnum'] = count($day21_pnum);
                                    }
                                    //22天
                                    if (!empty($info['openid']) && !empty($login_result[$day22_time][$plat]['openid'])) {
                                        $day22_pnum = array_intersect($info['openid'], $login_result[$day22_time][$plat]['openid']);
                                        $result[$time][$plat]['day22_pnum'] = count($day22_pnum);
                                    }
                                    //23天
                                    if (!empty($info['openid']) && !empty($login_result[$day23_time][$plat]['openid'])) {
                                        $day23_pnum = array_intersect($info['openid'], $login_result[$day23_time][$plat]['openid']);
                                        $result[$time][$plat]['day23_pnum'] = count($day23_pnum);
                                    }
                                    //24天
                                    if (!empty($info['openid']) && !empty($login_result[$day24_time][$plat]['openid'])) {
                                        $day24_pnum = array_intersect($info['openid'], $login_result[$day24_time][$plat]['openid']);
                                        $result[$time][$plat]['day24_pnum'] = count($day24_pnum);
                                    }
                                    //25天
                                    if (!empty($info['openid']) && !empty($login_result[$day25_time][$plat]['openid'])) {
                                        $day25_pnum = array_intersect($info['openid'], $login_result[$day25_time][$plat]['openid']);
                                        $result[$time][$plat]['day25_pnum'] = count($day25_pnum);
                                    }
                                    //26天
                                    if (!empty($info['openid']) && !empty($login_result[$day26_time][$plat]['openid'])) {
                                        $day26_pnum = array_intersect($info['openid'], $login_result[$day26_time][$plat]['openid']);
                                        $result[$time][$plat]['day26_pnum'] = count($day26_pnum);
                                    }
                                    //27天
                                    if (!empty($info['openid']) && !empty($login_result[$day27_time][$plat]['openid'])) {
                                        $day27_pnum = array_intersect($info['openid'], $login_result[$day27_time][$plat]['openid']);
                                        $result[$time][$plat]['day27_pnum'] = count($day27_pnum);
                                    }
                                    //28天
                                    if (!empty($info['openid']) && !empty($login_result[$day28_time][$plat]['openid'])) {
                                        $day28_pnum = array_intersect($info['openid'], $login_result[$day28_time][$plat]['openid']);
                                        $result[$time][$plat]['day28_pnum'] = count($day28_pnum);
                                    }
                                    //29天
                                    if (!empty($info['openid']) && !empty($login_result[$day29_time][$plat]['openid'])) {
                                        $day29_pnum = array_intersect($info['openid'], $login_result[$day29_time][$plat]['openid']);
                                        $result[$time][$plat]['day29_pnum'] = count($day29_pnum);
                                    }
                                    //30天
                                    if (!empty($info['openid']) && !empty($login_result[$day30_time][$plat]['openid'])) {
                                        $day30_pnum = array_intersect($info['openid'], $login_result[$day30_time][$plat]['openid']);
                                        $result[$time][$plat]['day30_pnum'] = count($day30_pnum);
                                    }
                                    unset($result[$time][$plat]['openid']);
                                }
                            }
                            unset($login_result);
                        }
                    }
                    /* echo '留存 cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    /******************订单*******************/
                    $list = array();
                    if (!empty($serverList)) {
                        foreach ($serverList as $k => $v) {
                            if (empty($v)) {
                                continue;
                            }

                            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                            if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg1['sevid']) {
                                continue;
                            }

                            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                continue;
                            }

                            if (!empty($sid) && !in_array($SevidCfg1['sevid'], $sid)) {
                                continue;
                            }
                            $db = Common::getDbBySevId($SevidCfg1['sevid']);
                            $cx_order_sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`roleid` AS r,`platform` AS p from `t_order` where `status`>0 and `paytype` != 'houtai' and `ptime`<{$endDateTime} and `ptime`>={$beginDateTime};";
                            $order_list = $db->fetchArray($cx_order_sql);
                            if (!empty($order_list)) {
                                $list = array_merge($list, $order_list);
                            }
                        }
                    }
                    if (!empty($list)) {
                        $ls_total_arr = array();//临时总存放
                        $roleid_total_arr = array();//临时总存放
                        foreach ($list as $value) {
                            $ptime = date('Ymd', $value['pt']);
                            $platform = $value['p'];
                            if (empty($ls_total_arr[$ptime])) {
                                $ls_total_arr[$ptime] = array();
                            }
                            if (!isset($ls_total_arr[$ptime][$value['r']])) {
                                $result[$ptime][$platform]['total_rechange_pnum'] += 1;//总充值人数
                                $ls_total_arr[$ptime][$value['r']] = $value['r'];
                            }
                            if (!in_array($value['r'], $roleid_total_arr)) {

                                array_push($roleid_total_arr, $value['r']);
                                $result[$ptime][$platform]['max_rechange_pnum'] += 1;//总充值人数
                            }
                            $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                            // $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                            $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                            $result[$ptime][$platform]['total_doller'] += Master::returnDoller($value['m']);//总金额


                            //新增统计
                            if (isset($resultLog[$ptime][$platform]['uid'][$value['r']])) {

                                $result[$ptime][$platform]['new_money'] += $value['m'];
                                if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1 || $resultLog[$ptime][$platform]['uid'][$value['r']] == 1) {
                                    $result[$ptime][$platform]['new_rechange_pnum'] += 1;
                                    $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
                                    $resultLog[$ptime][$platform]['uid'][$value['r']] = 2;
                                }
                            }
                        }
                    }
                    ksort($result);
                }
                $platforms = $_POST['channels'];
                foreach ($result as $key => $val) {
                    foreach ($val as $plat => $val1) {
                       /* //临时做限制,防止少统计后台充值
                        if (empty($auth[$_SESSION['CURRENT_USER']]) && $plat == 'local'){
                            continue;
                        }*/
                        if (!in_array($plat, $platforms)) {
                            unset($result[$key][$plat]);
                        }
                    }
                }
                if ($result) {
                    $max_rechange_pnum = 0;//累计充值人数
                    foreach ($result as $time => $info) {
                        if (empty($start)) {
                            $start = $time;
                        }
                        $end = $time;
                        $reg_pnum = 0;//注册人数
                        $login_pnum = 0;//登录人数
                        $day_pnum = 0;//次日留存人数
                        $three_pnum = 0;//3日留存人数
                        $day4_pnum = 0;//4日留存人数
                        $five_pnum = 0;//5日留存人数
                        $day6_pnum = 0;//5日留存人数
                        $week_pnum = 0;//7日留存人数
                        $day8_pnum = 0;//8日留存人数
                        $day9_pnum = 0;//9日留存人数
                        $day10_pnum = 0;//10日留存人数
                        $day11_pnum = 0;//11日留存人数
                        $day12_pnum = 0;//12日留存人数
                        $day13_pnum = 0;//13日留存人数
                        $two_week_pnum = 0;//14日留存人数
                        $day15_pnum = 0;//15日留存人数
                        $day16_pnum = 0;//16日留存人数
                        $day17_pnum = 0;//17日留存人数
                        $day18_pnum = 0;//18日留存人数
                        $day19_pnum = 0;//19日留存人数
                        $day20_pnum = 0;//20日留存人数
                        $day21_pnum = 0;//21日留存人数
                        $day22_pnum = 0;//22日留存人数
                        $day23_pnum = 0;//23日留存人数
                        $day24_pnum = 0;//24日留存人数
                        $day25_pnum = 0;//25日留存人数
                        $day26_pnum = 0;//26日留存人数
                        $day27_pnum = 0;//27日留存人数
                        $day28_pnum = 0;//28日留存人数
                        $day29_pnum = 0;//29日留存人数
                        $day30_pnum = 0;//30日留存人数
                        $rechange_num = 0; //付费笔数
                        $total_rechange_pnum = 0;//单日充值人数
                        $total_money = 0;//单日充值金额
                        $new_rechange_pnum = 0;//单日新增充值人数
                        $new_money = 0;//单日新增充值金额
                        $total_doller = 0;
                        foreach ($info as $plat => $val) {
                            $reg_pnum += $val['reg_pnum'];
                            $login_pnum += $val['login_pnum'];
                            $day_pnum += $val['two_pnum'] ? $val['two_pnum'] : 0;
                            $three_pnum += $val['three_pnum'] ? $val['three_pnum'] : 0;
                            $day4_pnum += $val['day4_pnum'] ? $val['day4_pnum'] : 0;
                            $five_pnum += $val['five_pnum'] ? $val['five_pnum'] : 0;
                            $day6_pnum += $val['day6_pnum'] ? $val['day6_pnum'] : 0;
                            $week_pnum += $val['week_pnum'] ? $val['week_pnum'] : 0;
                            $day8_pnum += $val['day8_pnum'] ? $val['day8_pnum'] : 0;
                            $day9_pnum += $val['day9_pnum'] ? $val['day9_pnum'] : 0;
                            $day10_pnum += $val['day10_pnum'] ? $val['day10_pnum'] : 0;
                            $day11_pnum += $val['day11_pnum'] ? $val['day11_pnum'] : 0;
                            $day12_pnum += $val['day12_pnum'] ? $val['day12_pnum'] : 0;
                            $day13_pnum += $val['day13_pnum'] ? $val['day13_pnum'] : 0;
                            $two_week_pnum += $val['two_week_pnum'] ? $val['two_week_pnum'] : 0;
                            $day15_pnum += $val['day15_pnum'] ? $val['day15_pnum'] : 0;
                            $day16_pnum += $val['day16_pnum'] ? $val['day16_pnum'] : 0;
                            $day17_pnum += $val['day17_pnum'] ? $val['day17_pnum'] : 0;
                            $day18_pnum += $val['day18_pnum'] ? $val['day18_pnum'] : 0;
                            $day19_pnum += $val['day19_pnum'] ? $val['day19_pnum'] : 0;
                            $day20_pnum += $val['day20_pnum'] ? $val['day20_pnum'] : 0;
                            $day21_pnum += $val['day21_pnum'] ? $val['day21_pnum'] : 0;
                            $day22_pnum += $val['day22_pnum'] ? $val['day22_pnum'] : 0;
                            $day23_pnum += $val['day23_pnum'] ? $val['day23_pnum'] : 0;
                            $day24_pnum += $val['day24_pnum'] ? $val['day24_pnum'] : 0;
                            $day25_pnum += $val['day25_pnum'] ? $val['day25_pnum'] : 0;
                            $day26_pnum += $val['day26_pnum'] ? $val['day26_pnum'] : 0;
                            $day27_pnum += $val['day27_pnum'] ? $val['day27_pnum'] : 0;
                            $day28_pnum += $val['day28_pnum'] ? $val['day28_pnum'] : 0;
                            $day29_pnum += $val['day29_pnum'] ? $val['day29_pnum'] : 0;
                            $day30_pnum += $val['day30_pnum'] ? $val['day30_pnum'] : 0;
                            $rechange_num += $val['rechange_num'] ? $val['rechange_num'] : 0;
                            $total_rechange_pnum += $val['total_rechange_pnum'] ? $val['total_rechange_pnum'] : 0;
                            $max_rechange_pnum += $val['max_rechange_pnum'] ? $val['max_rechange_pnum'] : 0;
                            $total_money += $val['total_money'] ? $val['total_money'] : 0;
                            $new_rechange_pnum += $val['new_rechange_pnum'] ? $val['new_rechange_pnum'] : 0;
                            $new_money += $val['new_money'] ? $val['new_money'] : 0;
                            $total_doller += $val['total_doller'] ? $val['total_doller'] : 0;
                        }
                        $volist[] = array(
                            'time' => $time,
                            'reg_pnum' => $reg_pnum,
                            'login_pnum' => $login_pnum,
                            'two_pnum' => $day_pnum,
                            'three_pnum' => $three_pnum,
                            'day4_pnum' => $day4_pnum,
                            'five_pnum' => $five_pnum,
                            'day6_pnum' => $day6_pnum,
                            'week_pnum' => $week_pnum,
                            'day8_pnum' => $day8_pnum,
                            'day9_pnum' => $day9_pnum,
                            'day10_pnum' => $day10_pnum,
                            'day11_pnum' => $day11_pnum,
                            'day12_pnum' => $day12_pnum,
                            'day13_pnum' => $day13_pnum,
                            'two_week_pnum' => $two_week_pnum,
                            'day15_pnum' => $day15_pnum,
                            'day16_pnum' => $day16_pnum,
                            'day17_pnum' => $day17_pnum,
                            'day18_pnum' => $day18_pnum,
                            'day19_pnum' => $day19_pnum,
                            'day20_pnum' => $day20_pnum,
                            'day21_pnum' => $day21_pnum,
                            'day22_pnum' => $day22_pnum,
                            'day23_pnum' => $day23_pnum,
                            'day24_pnum' => $day24_pnum,
                            'day25_pnum' => $day25_pnum,
                            'day26_pnum' => $day26_pnum,
                            'day27_pnum' => $day27_pnum,
                            'day28_pnum' => $day28_pnum,
                            'day29_pnum' => $day29_pnum,
                            'day30_pnum' => $day30_pnum,
                            'rechange_num' => $rechange_num,
                            'total_rechange_pnum' => $total_rechange_pnum,
                            'max_rechange_pnum' => $max_rechange_pnum,
                            'total_money' => $total_money,
                            'total_doller' => $total_doller,
                            'new_rechange_pnum' => $new_rechange_pnum,
                            'new_money' => $new_money,
                            'rechange_rate' => $login_pnum == 0 ? 0 : number_format($total_rechange_pnum * 100 / $login_pnum, 2),
                            'new_rechange_rate' => $reg_pnum == 0 ? 0 : number_format($new_rechange_pnum * 100 / $reg_pnum, 2),
                            'aup_rate' => $login_pnum == 0 ? 0 : number_format($total_money/$login_pnum, 2),
                        );
                    }
                }
                if (!empty($volist)) {
                    //缓存两个小时
                    $cache->set($cacheKey, serialize($volist), 7200);
                }
            }
        }//end volist
        else {
            $volist = unserialize($volist);
        }
        if($_POST['excel'] == 1){
            $dataArray = array();
            $xindex = $yindex = 0;
            $maxRowNum = 65536;// 设置excel每张表最大记录数
            $xlsTitles = array('日期', '新增注册','登录用户','营收','付费人数','付费笔数','付费率','新增营收','新增付费人数','新增付费率','ARPPU','次日留存','七日留存','累计注册','累计营收','累计营收(美元)','累计LTV');// EXCEL工作表表头
            if (is_array($volist)) {
                $add_reg = 0;
                $add_money = 0;
                $add_doller=0;
                foreach ($volist as $k => $v) {
                    if ( 0 == $yindex ) {
                        $dataArray[$xindex][$yindex] = $xlsTitles;
                    }
                    $yindex++;
                    $dataArray[$xindex][$yindex] = array(
                        $v['time'],
                        $v['reg_pnum'],
                        $v['login_pnum'],
                        $v['total_money'],
                        $v['total_rechange_pnum'],
                        $v['max_rechange_pnum'],
                        $v['rechange_num'],
                        $v['rechange_rate'],
                        $v['new_money'],
                        $v['new_rechange_pnum'],
                        $v['new_rechange_rate'],
                        $v['total_rechange_pnum'] == 0 ? 0 : number_format($v['total_money']/$v['total_rechange_pnum'],2),
                        $v['two_pnum'],
                        $v['week_pnum'],
                        $add_reg +=$v['reg_pnum'],
                        $add_money += $v['total_money'],
                        $add_doller += $v['total_doller'],
                        $add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)
                    );
                    if ( $yindex >= $maxRowNum ) {
                        $xindex++;
                        $yindex = 0;
                    }
                }
                if ( !empty($dataArray) ) {
                    Common::exportExcel($dataArray);
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
        * 总览
        *
        * */
    public function totalCX(){
        //不限制执行时间
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        if (empty($_POST['startTime'])) {
            $_POST['startTime'] = date("Y-m-01 00:00:00");
        }
        if (empty($_POST['endTime'])) {
            $_POST['endTime'] = date("Y-m-d 23:59:59");
        }
        $beginDateTime = $startTime = strtotime($_POST['startTime']);
        $endDateTime = $endTime = strtotime($_POST['endTime']);

        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $serverList = ServerModel::getServList();

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }
        if (empty($_POST['select_type'])) {
            $_POST['select_type'] = 1;
        }

        $server = array("all");
        if (!empty($_POST['server'])) {
            $server = explode('-',trim($_POST['server']));
        }

        $startS = $server[0];
        $endS = $server[1];
        if ($startS!="all" && empty($endS)){
            echo "<script>alert('区服输入错误!');</script>";
            return false;
        }

        if ($startS!="all") {

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }

                if ( $k < $startS || $k > $endS ){
                    continue;
                }
                $sid[] = $k;
            }
        }

        $cache = Common::getCacheBySevId($serverid);
        //相同查询条件缓存起来
        $cacheKeyArr = $_POST;
        unset($cacheKeyArr['select_type']);
        $cacheKey = 'ADMIN:TOTALCX'.md5(json_encode($cacheKeyArr));
        //结果集
        $volist = array();
        $table_div = Common::get_table_div();
        if ($_POST['select_type'] == 1) {
            $volist = $cache->get($cacheKey);
        } else {
            $cache->delete($cacheKey);
        }
        $mUse = memory_get_usage();
        if (empty($volist)) {
            //时间
            $result = array();
            $resultLog = array();
            $login_where = '';
            if ($_POST['excel-all'] == 1){
                if ($_POST['server']){
                    $server = explode('-',$_POST['server']);
                }
                foreach ($serverList as $skey => $svalue){
                    if ( $startS!="all" && ($skey < $startS || $skey > $endS) ){
                        continue;
                    }
                    if(!empty($skey)) {
                        $login_where = " and `servid`={$skey}";
                    }else{
                        continue;
                    }
                    if (empty($result)) {
                        //获取注册信息
                        /********************注册统计***********************/
                        $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`<{$endDateTime} and `reg_time`>={$beginDateTime} {$login_where}";
                        $db = Common::getDbBySevId($serverid);
                        $reg_info = $db->fetchArray($cx_zc_sql);
                        /*echo 'reg use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        if (!empty($reg_info)) {

                            foreach ($reg_info as $val) {
                                $regtime = date('Ymd', $val['r']);
                                $platform = $val['p'];

                                if (empty($skey)) {
                                    $result[$regtime][$platform]['reg_pnum'] += 1;

                                    $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                    $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                                } elseif ($val['s'] == $skey) {
                                    $result[$regtime][$platform]['reg_pnum'] += 1;

                                    $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                    $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                                }
                                if (!empty($val['d'])) {
                                    $val['d'] = json_decode($val['d'], true);
                                    foreach ($val['d'] as $seid => $data) {
                                        $reg = date('Ymd', $data['reg_time']);
                                        if ($reg == $regtime) {

                                            $resultLog[$reg][$platform]['uid'][$data['uid']] = 1;
                                        }
                                    }
                                }
                                unset($val);
                            }
                            //用完释放
                            unset($reg_info);
                        }
                        /*echo 'reg cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/

                        /*********************登录统计*************************/
                        /*
                        $login_info = array();
                        $startDateTimeTemp = $beginDateTime;
                        while ($startDateTimeTemp <$endDateTime) {
                            //分开查询
                            $endDateTimeTemp = $startDateTimeTemp + 86400;
                            $cx_login_sql = "select * from `login_log` where `login_time`<{$endDateTimeTemp} and `login_time`>={$startDateTimeTemp} {$login_where}";
                            $login_info = array_merge($login_info, $db->fetchArray($cx_login_sql));
                            $startDateTimeTemp = $endDateTimeTemp;
                        }
                        */
                        if($_POST['select_type_lc'] == 2){
                            $cx_login_sql = "select `openid` AS o,`login_time` AS l,`platform` AS p from `login_log` where `login_time`<{$endDateTime} and `login_time`>={$beginDateTime} {$login_where}";
                            $login_info = $db->fetchArray($cx_login_sql);

                            /*echo 'log use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                            $login_result = array();
                            if (!empty($login_info)) {
                                foreach ($login_info as $val) {
                                    $logintime = date('Ymd', $val['l']);
                                    $platform = $val['p'];
                                    if (empty($login_result[$logintime][$platform]['openid'])) {
                                        $login_result[$logintime][$platform]['openid'] = array();
                                    }
                                    if (!isset($login_result[$logintime][$platform]['openid'][$val['o']])) {
                                        $login_result[$logintime][$platform]['openid'][$val['o']] = $val['o'];
                                        $result[$logintime][$platform]['login_pnum'] += 1;
                                    }
                                    unset($val);
                                }
                                //用完释放
                                unset($login_info);
                            }
                        }
                        /* echo '留存 cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        /******************订单*******************/
                        $list = array();
                        if (!empty($serverList)) {
                            foreach ($serverList as $k => $v) {
                                if (empty($v)) {
                                    continue;
                                }

                                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                                if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg1['sevid']) {
                                    continue;
                                }

                                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                    continue;
                                }

                                if (!empty($skey) && $SevidCfg1['sevid'] != $skey) {
                                    continue;
                                }
                                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                                $cx_order_sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`roleid` AS r,`platform` AS p from `t_order` where `status`>0 and `paytype` != 'houtai' and `ptime`<{$endDateTime} and `ptime`>={$beginDateTime};";
                                $order_list = $db->fetchArray($cx_order_sql);
                                if (!empty($order_list)) {
                                    $list = array_merge($list, $order_list);
                                }
                            }
                        }
                        if (!empty($list)) {
                            $ls_total_arr = array();//临时总存放
                            $roleid_total_arr = array();
                            foreach ($list as $value) {
                                $ptime = date('Ymd', $value['pt']);
                                $oldtime = date('Ymd', $value['pt'] - 86400);
                                $platform = $value['p'];
                                if (empty($ls_total_arr[$ptime])) {
                                    $ls_total_arr[$ptime] = array();
                                }
                                if (!isset($ls_total_arr[$ptime][$value['r']])) {
                                    $result[$ptime][$platform]['total_rechange_pnum'] += 1;//总充值人数
                                    $ls_total_arr[$ptime][$value['r']] = $value['r'];
                                }
                                if (!in_array($value['r'], $roleid_total_arr)) {

                                    array_push($roleid_total_arr, $value['r']);
                                    $result[$ptime][$platform]['max_rechange_pnum'] += 1;//总充值人数
                                }
                                $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                                // $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                                $result[$ptime][$platform]['total_money'] += Master::returnDoller($value['m']);//总金额
                                $result[$ptime][$platform]['total_doller'] += Master::returnDoller($value['m']);//总金额

                                //新增统计
                                if (isset($resultLog[$ptime][$platform]['uid'][$value['r']])) {

                                    $result[$ptime][$platform]['new_money'] += Master::returnDoller($value['m']);
                                    if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1 || $resultLog[$ptime][$platform]['uid'][$value['r']] == 1 ) {
                                        $result[$ptime][$platform]['new_rechange_pnum'] += 1;
                                        $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
                                        $resultLog[$ptime][$platform]['uid'][$value['r']] = 2;
                                        // $result[$ptime][$platform]['new_money'] += $value['m'];
                                    }
                                    // $result[$ptime][$platform]['new_money'] += $value['m'];
                                }
                            }
                        }
                        if (!empty($result)){
                            ksort($result);
                        }

                    }
                    $platforms = $_POST['channels'];
                    if (!empty($result)) {
                        foreach ($result as $key => $val) {
                            foreach ($val as $plat => $val1) {
                                /*//临时做限制,防止少统计后台充值
                                if (empty($auth[$_SESSION['CURRENT_USER']]) && $plat == 'local') {
                                    continue;
                                }*/
                                if (!in_array($plat, $platforms)) {
                                    unset($result[$key][$plat]);
                                }
                            }
                        }
                        if ($result) {
                            $max_rechange_pnum = 0;//累计充值人数
                            foreach ($result as $time => $info) {
                                if (empty($start)) {
                                    $start = $time;
                                }
                                $end = $time;
                                $reg_pnum = 0;//注册人数
                                $login_pnum = 0;//登录人数
                                $rechange_num = 0; //付费笔数
                                $total_rechange_pnum = 0;//单日充值人数
                                $total_money = 0;//单日充值金额
                                $new_rechange_pnum = 0;//单日新增充值人数
                                $new_money = 0;//单日新增充值金额
                                $total_doller = 0;
                                foreach ($info as $plat => $val) {
                                    $reg_pnum += $val['reg_pnum'];
                                    $login_pnum += $val['login_pnum'];
                                    $rechange_num += $val['rechange_num'] ? $val['rechange_num'] : 0;
                                    $total_rechange_pnum += $val['total_rechange_pnum'] ? $val['total_rechange_pnum'] : 0;
                                    $max_rechange_pnum += $val['max_rechange_pnum'] ? $val['max_rechange_pnum'] : 0;
                                    $total_money += $val['total_money'] ? $val['total_money'] : 0;
                                    $new_rechange_pnum += $val['new_rechange_pnum'] ? $val['new_rechange_pnum'] : 0;
                                    $new_money += $val['new_money'] ? $val['new_money'] : 0;
                                    $total_doller += $val['total_doller'] ? $val['total_doller'] : 0;
                                }
                                $volist[] = array(
                                    'time' => $time,
                                    'reg_pnum' => $reg_pnum,
                                    'login_pnum' => $login_pnum,
                                    'rechange_num' => $rechange_num,
                                    'total_rechange_pnum' => $total_rechange_pnum,
                                    'max_rechange_pnum' => $max_rechange_pnum,
                                    'total_money' => $total_money,
                                    'total_doller' => $total_doller,
                                    'new_rechange_pnum' => $new_rechange_pnum,
                                    'new_money' => $new_money,
                                    'rechange_rate' => $login_pnum == 0 ? 0 : number_format($total_rechange_pnum * 100 / $login_pnum, 2),
                                    'new_rechange_rate' => $reg_pnum == 0 ? 0 : number_format($new_rechange_pnum * 100 / $reg_pnum, 2),
                                    'aup_rate' => $login_pnum == 0 ? 0 : number_format($total_money/$login_pnum, 2),
                                );
                            }
                            $dataInfo[$skey] = $volist;
                            unset($volist, $result);
                        }
                    }
                }
                $dataArray = array();
                $xindex = $yindex = 0;
                $maxRowNum = 65536;// 设置excel每张表最大记录数
                $xlsTitles = array('区服', '日期', '新增注册','登录用户','营收','付费人数','付费笔数','付费率','新增营收','新增付费人数','新增付费率','ARPPU','累计注册','累计营收','累计营收(美元)','累计LTV');// EXCEL工作表表头
                if (is_array($dataInfo)) {
                    foreach ($dataInfo as $dkey => $volist ){
                        $add_reg = 0;
                        $add_money = 0;
                        $add_doller=0;
                        foreach ($volist as $k => $v) {
                            if ( 0 == $yindex ) {
                                $dataArray[$xindex][$yindex] = $xlsTitles;
                            }
                            $yindex++;
                            $dataArray[$xindex][$yindex] = array(
                                $dkey,
                                $v['time'],
                                $v['reg_pnum'],
                                $v['login_pnum'],
                                $v['total_money'],
                                $v['total_rechange_pnum'],
                                $v['max_rechange_pnum'],
                                $v['rechange_num'],
                                $v['rechange_rate'],
                                $v['new_money'],
                                $v['new_rechange_pnum'],
                                $v['new_rechange_rate'],
                                $v['total_rechange_pnum'] == 0 ? 0 : number_format($v['total_money']/$v['total_rechange_pnum'],2),
                                $v['two_pnum'],
                                $v['week_pnum'],
                                $add_reg +=$v['reg_pnum'],
                                $add_money += $v['total_money'],
                                $add_doller += $v['total_doller'],
                                $add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)
                            );
                            if ( $yindex >= $maxRowNum ) {
                                $xindex++;
                                $yindex = 0;
                            }
                        }
                    }

                    if ( !empty($dataArray) ) {
                        Common::exportExcel($dataArray);
                    }
                }
            }else{
                if(!empty($sid)) {
                    $sidStr = implode("','", $sid);
                    $login_where = " and `servid` IN ('{$sidStr}')";
                }

                if (empty($result)) {
                    //获取注册信息
                    /********************注册统计***********************/
                    $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`<{$endDateTime} and `reg_time`>={$beginDateTime} {$login_where}";
                    $db = Common::getDbBySevId($serverid);
                    $reg_info = $db->fetchArray($cx_zc_sql);
                    /*echo 'reg use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    if (!empty($reg_info)) {
                        foreach ($reg_info as $val) {
                            $regtime = date('Ymd', $val['r']);
                            $platform = $val['p'];
                            if (empty($sid)) {
                                $result[$regtime][$platform]['reg_pnum'] += 1;

                                $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                            } elseif (in_array($val['s'], $sid)) {
                                $result[$regtime][$platform]['reg_pnum'] += 1;

                                $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                            }
                            if (!empty($val['d'])) {
                                $val['d'] = json_decode($val['d'], true);
                                foreach ($val['d'] as $seid => $data) {
                                    $reg = date('Ymd', $data['reg_time']);
                                    if ($reg == $regtime) {

                                        $resultLog[$reg][$platform]['uid'][$data['uid']] = 1;
                                    }
                                }
                            }
                        }
                        //用完释放
                        unset($reg_info);
                    }

                    /*********************登录统计*************************/
                    /*
                    $login_info = array();
                    $startDateTimeTemp = $beginDateTime;
                    while ($startDateTimeTemp <$endDateTime) {
                        //分开查询
                        $endDateTimeTemp = $startDateTimeTemp + 86400;
                        $cx_login_sql = "select * from `login_log` where `login_time`<{$endDateTimeTemp} and `login_time`>={$startDateTimeTemp} {$login_where}";
                        $login_info = array_merge($login_info, $db->fetchArray($cx_login_sql));
                        $startDateTimeTemp = $endDateTimeTemp;
                    }
                    */
                    if($_POST['select_type_lc'] == 2){
                        $cx_login_sql = "select `openid` AS o,`login_time` AS l,`platform` AS p from `login_log` where `login_time`<{$endDateTime} and `login_time`>={$beginDateTime} {$login_where}";
                        $login_info = $db->fetchArray($cx_login_sql);

                        /*echo 'log use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        $login_result = array();
                        if (!empty($login_info)) {
                            foreach ($login_info as $val) {
                                $logintime = date('Ymd', $val['l']);
                                $platform = $val['p'];
                                if (empty($login_result[$logintime][$platform]['openid'])) {
                                    $login_result[$logintime][$platform]['openid'] = array();
                                }
                                if (!isset($login_result[$logintime][$platform]['openid'][$val['o']])) {
                                    $login_result[$logintime][$platform]['openid'][$val['o']] = $val['o'];
                                    $result[$logintime][$platform]['login_pnum'] += 1;
                                }
                            }
                            //用完释放
                            unset($login_info);
                        }
                    }
                    /* echo '留存 cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    /******************订单*******************/
                    $list = array();
                    if (!empty($serverList)) {
                        foreach ($serverList as $k => $v) {
                            if (empty($v)) {
                                continue;
                            }

                            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                            if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg1['sevid']) {
                                continue;
                            }

                            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                continue;
                            }

                            if (!empty($sid) && !in_array($SevidCfg1['sevid'], $sid)) {
                                continue;
                            }
                            $db = Common::getDbBySevId($SevidCfg1['sevid']);
                            $cx_order_sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`roleid` AS r,`platform` AS p from `t_order` where `status`>0 and `paytype` != 'houtai' and `ptime`<{$endDateTime} and `ptime`>={$beginDateTime};";
                            $order_list = $db->fetchArray($cx_order_sql);
                            if (!empty($order_list)) {
                                $list = array_merge($list, $order_list);
                            }
                        }
                    }
                    if (!empty($list)) {
                        $ls_total_arr = array();//临时总存放
                        $roleid_total_arr = array();//临时总存放
                        foreach ($list as $value) {
                            $ptime = date('Ymd', $value['pt']);
                            $platform = $value['p'];
                            if (empty($ls_total_arr[$ptime])) {
                                $ls_total_arr[$ptime] = array();
                            }
                            if (!isset($ls_total_arr[$ptime][$value['r']])) {
                                $result[$ptime][$platform]['total_rechange_pnum'] += 1;//总充值人数
                                $ls_total_arr[$ptime][$value['r']] = $value['r'];
                            }
                            if (!in_array($value['r'], $roleid_total_arr)) {

                                array_push($roleid_total_arr, $value['r']);
                                $result[$ptime][$platform]['max_rechange_pnum'] += 1;//总充值人数
                            }
                            $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                            // $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                            $result[$ptime][$platform]['total_money'] += Master::returnDoller($value['m']);//总金额
                            $result[$ptime][$platform]['total_doller'] += Master::returnDoller($value['m']);//总金额


                            //新增统计
                            if (isset($resultLog[$ptime][$platform]['uid'][$value['r']])) {

                                $result[$ptime][$platform]['new_money'] += Master::returnDoller($value['m']);
                                if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1 || $resultLog[$ptime][$platform]['uid'][$value['r']] == 1) {
                                    $result[$ptime][$platform]['new_rechange_pnum'] += 1;
                                    $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
                                    $resultLog[$ptime][$platform]['uid'][$value['r']] = 2;
                                }
                            }
                        }
                    }
                    ksort($result);
                }
                $platforms = $_POST['channels'];
                foreach ($result as $key => $val) {
                    foreach ($val as $plat => $val1) {
                       /* //临时做限制,防止少统计后台充值
                        if (empty($auth[$_SESSION['CURRENT_USER']]) && $plat == 'local'){
                            continue;
                        }*/
                        if (!in_array($plat, $platforms)) {
                            unset($result[$key][$plat]);
                        }
                    }
                }
                if ($result) {
                    $max_rechange_pnum = 0;//累计充值人数
                    foreach ($result as $time => $info) {
                        if (empty($start)) {
                            $start = $time;
                        }
                        $end = $time;
                        $reg_pnum = 0;//注册人数
                        $login_pnum = 0;//登录人数
                        $rechange_num = 0; //付费笔数
                        $total_rechange_pnum = 0;//单日充值人数
                        $total_money = 0;//单日充值金额
                        $new_rechange_pnum = 0;//单日新增充值人数
                        $new_money = 0;//单日新增充值金额
                        $total_doller = 0;
                        foreach ($info as $plat => $val) {
                            $reg_pnum += $val['reg_pnum'];
                            $login_pnum += $val['login_pnum'];
                            $rechange_num += $val['rechange_num'] ? $val['rechange_num'] : 0;
                            $total_rechange_pnum += $val['total_rechange_pnum'] ? $val['total_rechange_pnum'] : 0;
                            $max_rechange_pnum += $val['max_rechange_pnum'] ? $val['max_rechange_pnum'] : 0;
                            $total_money += $val['total_money'] ? $val['total_money'] : 0;
                            $new_rechange_pnum += $val['new_rechange_pnum'] ? $val['new_rechange_pnum'] : 0;
                            $new_money += $val['new_money'] ? $val['new_money'] : 0;
                            $total_doller += $val['total_doller'] ? $val['total_doller'] : 0;
                        }
                        $volist[] = array(
                            'time' => $time,
                            'reg_pnum' => $reg_pnum,
                            'login_pnum' => $login_pnum,
                            'rechange_num' => $rechange_num,
                            'total_rechange_pnum' => $total_rechange_pnum,
                            'max_rechange_pnum' => $max_rechange_pnum,
                            'total_money' => $total_money,
                            'total_doller' => $total_doller,
                            'new_rechange_pnum' => $new_rechange_pnum,
                            'new_money' => $new_money,
                            'rechange_rate' => $login_pnum == 0 ? 0 : number_format($total_rechange_pnum * 100 / $login_pnum, 2),
                            'new_rechange_rate' => $reg_pnum == 0 ? 0 : number_format($new_rechange_pnum * 100 / $reg_pnum, 2),
                            'aup_rate' => $login_pnum == 0 ? 0 : number_format($total_money/$login_pnum, 2),
                        );
                    }
                }
                if (!empty($volist)) {
                    //缓存两个小时
                    $cache->set($cacheKey, serialize($volist), 7200);
                }
            }
        }//end volist
        else {
            $volist = unserialize($volist);
        }
        if($_POST['excel'] == 1){
            $dataArray = array();
            $xindex = $yindex = 0;
            $maxRowNum = 65536;// 设置excel每张表最大记录数
            $xlsTitles = array('日期', '新增注册','登录用户','营收','付费人数','付费笔数','付费率','新增营收','新增付费人数','新增付费率','ARPPU','累计注册','累计营收','累计营收(美元)','累计LTV');// EXCEL工作表表头
            if (is_array($volist)) {
                $add_reg = 0;
                $add_money = 0;
                $add_doller=0;
                foreach ($volist as $k => $v) {
                    if ( 0 == $yindex ) {
                        $dataArray[$xindex][$yindex] = $xlsTitles;
                    }
                    $yindex++;
                    $dataArray[$xindex][$yindex] = array(
                        $v['time'],
                        $v['reg_pnum'],
                        $v['login_pnum'],
                        $v['total_money'],
                        $v['total_rechange_pnum'],
                        $v['max_rechange_pnum'],
                        $v['rechange_num'],
                        $v['rechange_rate'],
                        $v['new_money'],
                        $v['new_rechange_pnum'],
                        $v['new_rechange_rate'],
                        $v['total_rechange_pnum'] == 0 ? 0 : number_format($v['total_money']/$v['total_rechange_pnum'],2),
                        $v['two_pnum'],
                        $v['week_pnum'],
                        $add_reg +=$v['reg_pnum'],
                        $add_money += $v['total_money'],
                        $add_doller += $v['total_doller'],
                        $add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)
                    );
                    if ( $yindex >= $maxRowNum ) {
                        $xindex++;
                        $yindex = 0;
                    }
                }
                if ( !empty($dataArray) ) {
                    Common::exportExcel($dataArray);
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
        * 付费查询
        *
        * */
    public function totalCX3(){
        //不限制执行时间
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        if (empty($_POST['beginDate'])) {
            $_POST['beginDate'] = date("Y-m-01 00:00:00");
        }
        if (empty($_POST['endDate'])) {
            $_POST['endDate'] = date("Y-m-d 23:59:59");
        }
        $beginDateTime = strtotime($_POST['beginDate']);
        $endDateTime = strtotime($_POST['endDate']);
        if (!empty($_POST['servid'])) {
            $sid = $_POST['servid'];
        } else {
            $sid = $_POST['servid'] = 0;
        }

        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();

        $serverList = ServerModel::getServList();

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }
        if (empty($_POST['select_type'])) {
            $_POST['select_type'] = 1;
        }
        $cache = Common::getCacheBySevId($serverid);
        //相同查询条件缓存起来
        $cacheKeyArr = $_POST;
        unset($cacheKeyArr['select_type']);
        $cacheKey = 'ADMIN:TOTALCX3'.md5(json_encode($cacheKeyArr));
        //结果集
        $volist = array();
        if ($_POST['select_type'] == 1) {
            $volist = $cache->get($cacheKey);
        } else {
            $cache->delete($cacheKey);
        }
        $mUse = memory_get_usage();
        if (empty($volist)) {
            //时间
            $result = array();
            $resultLog = array();
            $login_where = '';
            if ($_POST['excel-all'] == 1){
                if ($_POST['server']){
                    $server = explode('-',$_POST['server']);
                }
                foreach ($serverList as $skey => $svalue){
                    if ($server[0] > $skey || $skey > $server[1]){
                        continue;
                    }
                    if(!empty($skey)) {
                        $login_where = " and `servid`={$skey}";
                    }else{
                        continue;
                    }
                    if (empty($result)) {
                        //获取注册信息
                        /********************注册统计***********************/
                        $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`<{$endDateTime} and `reg_time`>={$beginDateTime} {$login_where}";
                        $db = Common::getDbBySevId($serverid);
                        $reg_info = $db->fetchArray($cx_zc_sql);
                        /*echo 'reg use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        if (!empty($reg_info)) {
                            foreach ($reg_info as $val) {
                                $regtime = date('Ymd', $val['r']);
                                $platform = $val['p'];

                                if (empty($skey)) {
                                    $result[$regtime][$platform]['openid'][] = $val['o'];
                                    $result[$regtime][$platform]['reg_pnum'] += 1;

                                    $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                    $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                                } elseif ($val['s'] == $skey) {
                                    $result[$regtime][$platform]['openid'][] = $val['o'];
                                    $result[$regtime][$platform]['reg_pnum'] += 1;

                                    $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                    $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                                } elseif (!empty($val['d'])) {
                                    $val['d'] = json_decode($val['d'], true);
                                    foreach ($val['d'] as $seid => $data) {
                                        if ($seid == $skey) {
                                            $result[$data['reg_time']][$platform]['openid'][] = $val['o'];
                                            $result[$data['reg_time']][$platform]['reg_pnum'] += 1;

                                            $resultLog[$data['reg_time']][$platform]['openid'][$val['o']] = 1;
                                            $resultLog[$data['reg_time']][$platform]['uid'][$val['u']] = 1;
                                            continue;
                                        }
                                    }
                                }
                                unset($val);
                            }
                            //用完释放
                            unset($reg_info);
                        }

                        $cx_login_sql = "select `openid` AS o,`login_time` AS l,`platform` AS p from `login_log` where `login_time`<{$endDateTime} and `login_time`>={$beginDateTime} {$login_where}";
                        $login_info = $db->fetchArray($cx_login_sql);

                        $login_result = array();
                        if (!empty($login_info)) {
                            foreach ($login_info as $val) {
                                $logintime = date('Ymd', $val['l']);
                                $platform = $val['p'];
                                if (empty($login_result[$logintime][$platform]['openid'])) {
                                    $login_result[$logintime][$platform]['openid'] = array();
                                }
                                if (!isset($login_result[$logintime][$platform]['openid'][$val['o']])) {
                                    $login_result[$logintime][$platform]['openid'][$val['o']] = $val['o'];
                                    $result[$logintime][$platform]['login_pnum'] += 1;
                                }
                                unset($val);
                            }
                            //用完释放
                            unset($login_info);
                        }

                        $list = array();
                        if (!empty($serverList)) {
                            foreach ($serverList as $k => $v) {
                                if (empty($v)) {
                                    continue;
                                }

                                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                                if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg1['sevid']) {
                                    continue;
                                }

                                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                    continue;
                                }

                                if (!empty($skey) && $SevidCfg1['sevid'] != $skey) {
                                    continue;
                                }
                                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                                $cx_order_sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`roleid` AS r,`platform` AS p from `t_order` where `status`>0 and `ptime`<{$endDateTime} and `ptime`>={$beginDateTime};";
                                $order_list = $db->fetchArray($cx_order_sql);
                                if (!empty($order_list)) {
                                    $list = array_merge($list, $order_list);
                                }
                            }
                        }
                        if (!empty($list)) {
                            $ls_total_arr = array();//临时总存放
                            foreach ($list as $value) {
                                $ptime = date('Ymd', $value['pt']);
                                $platform = $value['p'];
                                if (empty($ls_total_arr[$ptime])) {
                                    $ls_total_arr[$ptime] = array();
                                }
                                if (!isset($ls_total_arr[$ptime][$value['r']])) {
                                    $result[$ptime][$platform]['total_rechange_pnum'] += 1;//总充值人数
                                    $ls_total_arr[$ptime][$value['r']] = $value['r'];
                                }
                                $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                                // $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                                $result[$ptime][$platform]['total_money'] += Master::returnDoller($value['m']);//总金额
                                $result[$ptime][$platform]['total_doller'] += Master::returnDoller($value['m']);//总金额

                                //新增统计
                                if (isset($resultLog[$ptime][$platform]['openid'][$value['o']]) || isset($resultLog[$ptime][$platform]['uid'][$value['r']])) {
                                    if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1 || $resultLog[$ptime][$platform]['uid'][$value['r']] == 1 ) {
                                        $result[$ptime][$platform]['new_rechange_pnum'] += 1;
                                        $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
                                        $resultLog[$ptime][$platform]['uid'][$value['r']] = 2;
                                        // $result[$ptime][$platform]['new_money'] += $value['m'];
                                        $result[$ptime][$platform]['new_money'] += Master::returnDoller($value['m']);
                                    }
                                    // $result[$ptime][$platform]['new_money'] += $value['m'];
                                }
                            }
                        }
                        if (!empty($result)){
                            ksort($result);
                        }

                    }
                    $platforms = $_POST['channels'];
                    if (!empty($result)) {
                        foreach ($result as $key => $val) {
                            foreach ($val as $plat => $val1) {
                                /*//临时做限制,防止少统计后台充值
                                if (empty($auth[$_SESSION['CURRENT_USER']]) && $plat == 'local') {
                                    continue;
                                }*/
                                if (!in_array($plat, $platforms)) {
                                    unset($result[$key][$plat]);
                                }
                            }
                        }
                        if ($result) {
                            foreach ($result as $time => $info) {
                                if (empty($start)) {
                                    $start = $time;
                                }
                                $end = $time;
                                $reg_pnum = 0;//注册人数
                                $login_pnum = 0;//登录人数
                                $rechange_num = 0; //付费笔数
                                $total_rechange_pnum = 0;//单日充值人数
                                $total_money = 0;//单日充值金额
                                $new_rechange_pnum = 0;//单日新增充值人数
                                $new_money = 0;//单日新增充值金额
                                $total_doller = 0;
                                foreach ($info as $plat => $val) {
                                    $reg_pnum += $val['reg_pnum'];
                                    $login_pnum += $val['login_pnum'];
                                    $rechange_num += $val['rechange_num'] ? $val['rechange_num'] : 0;
                                    $total_rechange_pnum += $val['total_rechange_pnum'] ? $val['total_rechange_pnum'] : 0;
                                    $total_money += $val['total_money'] ? $val['total_money'] : 0;
                                    $new_rechange_pnum += $val['new_rechange_pnum'] ? $val['new_rechange_pnum'] : 0;
                                    $new_money += $val['new_money'] ? $val['new_money'] : 0;
                                    $total_doller += $val['total_doller'] ? $val['total_doller'] : 0;
                                }
                                $volist[] = array(
                                    'time' => $time,
                                    'reg_pnum' => $reg_pnum,
                                    'login_pnum' => $login_pnum,
                                    'rechange_num' => $rechange_num,
                                    'total_rechange_pnum' => $total_rechange_pnum,
                                    'total_money' => $total_money,
                                    'total_doller' => $total_doller,
                                    'new_rechange_pnum' => $new_rechange_pnum,
                                    'new_money' => $new_money,
                                    'rechange_rate' => $login_pnum == 0 ? 0 : number_format($total_rechange_pnum * 100 / $login_pnum, 2),
                                    'new_rechange_rate' => $reg_pnum == 0 ? 0 : number_format($new_rechange_pnum * 100 / $reg_pnum, 2),
                                    'aup_rate' => $login_pnum == 0 ? 0 : number_format($total_money/$login_pnum, 2),
                                );
                            }
                            $dataInfo[$skey] = $volist;
                            unset($volist, $result);
                        }
                    }
                }
                $dataArray = array();
                $xindex = $yindex = 0;
                $maxRowNum = 65536;// 设置excel每张表最大记录数
                $xlsTitles = array('区服', '日期','活跃付费','付费率','新增付费人数','新增付费率','ARPPU');// EXCEL工作表表头
                if (is_array($dataInfo)) {
                    foreach ($dataInfo as $dkey => $volist ){
                        foreach ($volist as $k => $v) {
                            if ( 0 == $yindex ) {
                                $dataArray[$xindex][$yindex] = $xlsTitles;
                            }
                            $yindex++;
                            $dataArray[$xindex][$yindex] = array(
                                $dkey,
                                $v['time'],
                                $v['login_pnum'] == 0 ?0: number_format($v['total_money'] * 100 / $v['login_pnum'], 2),
                                $v['rechange_rate'] == 0 ?0: number_format($v['total_rechange_pnum'] * 100 / $v['login_pnum'], 2),
                                $v['new_rechange_pnum'],
                                $v['new_rechange_rate'],
                                $v['total_rechange_pnum'] == 0?0:number_format($v['total_money'] * 100 / $v['total_rechange_pnum'], 2)
                            );
                            if ( $yindex >= $maxRowNum ) {
                                $xindex++;
                                $yindex = 0;
                            }
                        }
                    }

                    if ( !empty($dataArray) ) {
                        Common::exportExcel($dataArray);
                    }
                }
            }else{
                if(!empty($sid)) {
                    $login_where = " and `servid`={$sid}";
                }

                if (empty($result)) {
                    //获取注册信息
                    /********************注册统计***********************/
                    $cx_zc_sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`uid` AS u,`data` AS d from `register` where `reg_time`<{$endDateTime} and `reg_time`>={$beginDateTime} {$login_where}";
                    $db = Common::getDbBySevId($serverid);
                    $reg_info = $db->fetchArray($cx_zc_sql);
                    /*echo 'reg use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    if (!empty($reg_info)) {
                        foreach ($reg_info as $val) {
                            $regtime = date('Ymd', $val['r']);
                            $platform = $val['p'];

                            if (empty($sid)) {
                                $result[$regtime][$platform]['openid'][] = $val['o'];
                                $result[$regtime][$platform]['reg_pnum'] += 1;

                                $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                            } elseif ($val['s'] == $sid) {
                                $result[$regtime][$platform]['openid'][] = $val['o'];
                                $result[$regtime][$platform]['reg_pnum'] += 1;

                                $resultLog[$regtime][$platform]['openid'][$val['o']] = 1;
                                $resultLog[$regtime][$platform]['uid'][$val['u']] = 1;
                            } elseif (!empty($val['d'])) {
                                $val['d'] = json_decode($val['d'], true);
                                foreach ($val['d'] as $seid => $data) {
                                    if ($seid == $sid) {
                                        $result[$data['reg_time']][$platform]['openid'][] = $val['o'];
                                        $result[$data['reg_time']][$platform]['reg_pnum'] += 1;

                                        $resultLog[$data['reg_time']][$platform]['openid'][$val['o']] = 1;
                                        $resultLog[$data['reg_time']][$platform]['uid'][$val['u']] = 1;
                                        continue;
                                    }
                                }
                            }
                        }
                        //用完释放
                        unset($reg_info);
                    }

                    $cx_login_sql = "select `openid` AS o,`login_time` AS l,`platform` AS p from `login_log` where `login_time`<{$endDateTime} and `login_time`>={$beginDateTime} {$login_where}";
                    $login_info = $db->fetchArray($cx_login_sql);

                    /*echo 'log use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    $login_result = array();
                    if (!empty($login_info)) {
                        foreach ($login_info as $val) {
                            $logintime = date('Ymd', $val['l']);
                            $platform = $val['p'];
                            if (empty($login_result[$logintime][$platform]['openid'])) {
                                $login_result[$logintime][$platform]['openid'] = array();
                            }
                            if (!isset($login_result[$logintime][$platform]['openid'][$val['o']])) {
                                $login_result[$logintime][$platform]['openid'][$val['o']] = $val['o'];
                                $result[$logintime][$platform]['login_pnum'] += 1;
                            }
                        }
                        //用完释放
                        unset($login_info);
                    }
                        /*echo 'log cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                        //统计留存问题

                    /* echo '留存 cal use:'.((memory_get_usage() - $mUse) / 1000000).'<br />';*/
                    /******************订单*******************/
                    $list = array();
                    if (!empty($serverList)) {
                        foreach ($serverList as $k => $v) {
                            if (empty($v)) {
                                continue;
                            }

                            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                            if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $SevidCfg1['sevid']) {
                                continue;
                            }

                            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                continue;
                            }

                            if (!empty($sid) && $SevidCfg1['sevid'] != $sid) {
                                continue;
                            }
                            $db = Common::getDbBySevId($SevidCfg1['sevid']);
                            $cx_order_sql = "select `openid` AS o,`money` AS m,`ptime` AS pt,`roleid` AS r,`platform` AS p from `t_order` where `status`>0 and `ptime`<{$endDateTime} and `ptime`>={$beginDateTime};";
                            $order_list = $db->fetchArray($cx_order_sql);
                            if (!empty($order_list)) {
                                $list = array_merge($list, $order_list);
                            }
                        }
                    }
                    if (!empty($list)) {
                        $ls_total_arr = array();//临时总存放
                        foreach ($list as $value) {
                            $ptime = date('Ymd', $value['pt']);
                            $platform = $value['p'];
                            if (empty($ls_total_arr[$ptime])) {
                                $ls_total_arr[$ptime] = array();
                            }
                            if (!isset($ls_total_arr[$ptime][$value['r']])) {
                                $result[$ptime][$platform]['total_rechange_pnum'] += 1;//总充值人数
                                $ls_total_arr[$ptime][$value['r']] = $value['r'];
                            }
                            $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                            // $result[$ptime][$platform]['total_money'] += $value['m'];//总金额
                            $result[$ptime][$platform]['total_money'] += Master::returnDoller($value['m']);//总金额
                            $result[$ptime][$platform]['total_doller'] += Master::returnDoller($value['m']);//总金额


                            //新增统计
                            if (isset($resultLog[$ptime][$platform]['openid'][$value['o']]) || isset($resultLog[$ptime][$platform]['uid'][$value['r']])) {
                                if ($resultLog[$ptime][$platform]['openid'][$value['o']] == 1 || $resultLog[$ptime][$platform]['uid'][$value['r']] == 1) {
                                    $result[$ptime][$platform]['new_rechange_pnum'] += 1;
                                    $resultLog[$ptime][$platform]['openid'][$value['o']] = 2;
                                    $resultLog[$ptime][$platform]['uid'][$value['r']] = 2;
                                    // $result[$ptime][$platform]['new_money'] += $value['m'];
                                    $result[$ptime][$platform]['new_money'] += Master::returnDoller($value['m']);
                                }
                                // $result[$ptime][$platform]['new_money'] += $value['m'];
                            }
                        }
                    }
                    ksort($result);
                }
                $platforms = $_POST['channels'];
                foreach ($result as $key => $val) {
                    foreach ($val as $plat => $val1) {
                        /* //临时做限制,防止少统计后台充值
                         if (empty($auth[$_SESSION['CURRENT_USER']]) && $plat == 'local'){
                             continue;
                         }*/
                        if (!in_array($plat, $platforms)) {
                            unset($result[$key][$plat]);
                        }
                    }
                }
                if ($result) {
                    foreach ($result as $time => $info) {
                        if (empty($start)) {
                            $start = $time;
                        }
                        $end = $time;
                        $reg_pnum = 0;//注册人数
                        $login_pnum = 0;//登录人数
                        $rechange_num = 0; //付费笔数
                        $total_rechange_pnum = 0;//单日充值人数
                        $total_money = 0;//单日充值金额
                        $new_rechange_pnum = 0;//单日新增充值人数
                        $new_money = 0;//单日新增充值金额
                        $total_doller = 0;
                        foreach ($info as $plat => $val) {
                            $reg_pnum += $val['reg_pnum'];
                            $login_pnum += $val['login_pnum'];
                            $rechange_num += $val['rechange_num'] ? $val['rechange_num'] : 0;
                            $total_rechange_pnum += $val['total_rechange_pnum'] ? $val['total_rechange_pnum'] : 0;
                            $total_money += $val['total_money'] ? $val['total_money'] : 0;
                            $new_rechange_pnum += $val['new_rechange_pnum'] ? $val['new_rechange_pnum'] : 0;
                            $new_money += $val['new_money'] ? $val['new_money'] : 0;
                            $total_doller += $val['total_doller'] ? $val['total_doller'] : 0;
                        }
                        $volist[] = array(
                            'time' => $time,
                            'reg_pnum' => $reg_pnum,
                            'login_pnum' => $login_pnum,
                            'rechange_num' => $rechange_num,
                            'total_rechange_pnum' => $total_rechange_pnum,
                            'total_money' => $total_money,
                            'total_doller' => $total_doller,
                            'new_rechange_pnum' => $new_rechange_pnum,
                            'new_money' => $new_money,
                        );
                    }
                }
                if (!empty($volist)) {
                    //缓存两个小时
                    $cache->set($cacheKey, serialize($volist), 7200);
                }
            }
        }//end volist
        else {
            $volist = unserialize($volist);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * remain
     * 留存详情
     */
    public function remain(){
        //不限制执行时间
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        if (empty($_POST['startTime'])) {
            $_POST['startTime'] = date("Y-m-01 00:00:00");
        }
        if (empty($_POST['endTime'])) {
            $_POST['endTime'] = date("Y-m-d 23:59:59");
        }

        $startTime = date('Ymd', strtotime($_POST['startTime']));
        $endTime = date('Ymd', strtotime($_POST['endTime']));
        if (!empty($_POST['servid'])) {
            $sid = $_POST['servid'];
        } else {
            $sid = $_POST['servid'] = 0;
        }

        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $serverList = ServerModel::getServList();
        $db = Common::getDbBySevId($serverid, 'flow');

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }

        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }

        $sql = "select * from `remain` where `date` >= {$startTime} AND `date` <= {$endTime} ORDER BY `date`";
        $remainData = $db->fetchArray($sql);

        $volist = array();
        foreach ($remainData as $key => $value) {

            $login = json_decode($value["login"], true);
            $register = json_decode($value["register"], true);
            $info = json_decode($value["info"], true);

            $voInfo = array(
                "t" => $value["date"],
                "r" => 0,
                "l" => 0,
                "d1" => 0,
                "d2" => 0,
                "d3" => 0,
                "d4" => 0,
                "d5" => 0,
                "d6" => 0,
                "d7" => 0,
                "d8" => 0,
                "d9" => 0,
                "d10" => 0,
                "d11" => 0,
                "d12" => 0,
                "d13" => 0,
                "d14" => 0,
                "d15" => 0,
                "d16" => 0,
                "d17" => 0,
                "d18" => 0,
                "d19" => 0,
                "d20" => 0,
                "d21" => 0,
                "d22" => 0,
                "d23" => 0,
                "d24" => 0,
                "d25" => 0,
                "d26" => 0,
                "d27" => 0,
                "d28" => 0,
                "d29" => 0,
            );

            foreach ($login as $lk => $lv) {

                if ( !in_array($lv["p"], $channels) ) {
                    continue;
                }
                $voInfo["l"] += $lv["l"];
            }

            foreach ($register as $rk => $rv) {

                if ( !in_array($rv["p"], $channels) ) {
                    continue;
                }
                $voInfo["r"] += $rv["r"];
            }

            foreach ($info as $ik => $iv) {

                foreach ($iv as $iik => $iiv) {

                    if ( !in_array($iik, $channels) ) {
                        continue;
                    }
                    $voInfo[$ik] += $iiv;
                }
            }

            $volist[] = $voInfo;
        }

        if($_POST['excel'] == 1){
            $dataArray = array();
            $xindex = $yindex = 0;
            $maxRowNum = 65536;// 设置excel每张表最大记录数
            $xlsTitles = array('日期', '新增注册','登录用户','次日留存','三日留存','四日留存','五日留存','六日留存','七日留存','八日留存','九日留存','10日留存','11日留存','12日留存','13日留存','14日留存','15日留存','16日留存','17日留存','18日留存','19日留存','20日留存','21日留存','22日留存','23日留存','24日留存','25日留存','26日留存','27日留存','28日留存','29日留存','30日留存');// EXCEL工作表表头
            if (is_array($volist)) {
                foreach ($volist as $k => $v) {
                    if ( 0 == $yindex ) {
                        $dataArray[$xindex][$yindex] = $xlsTitles;
                    }
                    $yindex++;
                    $dataArray[$xindex][$yindex] = array(
                        $v['t'],
                        $v['r'],
                        $v['l'],
                        (empty($v['r']) ? 0 :number_format($v['d1']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d2']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d3']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d4']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d5']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d6']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d7']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d8']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d9']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d10']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d11']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d12']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d13']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d14']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d15']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d16']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d17']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d18']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d19']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d20']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d21']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d22']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d23']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d24']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d25']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d26']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d27']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d28']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d29']*100/$v['r'],2)),
                        (empty($v['r']) ? 0 :number_format($v['d30']*100/$v['r'],2)),
                    );
                    if ( $yindex >= $maxRowNum ) {
                        $xindex++;
                        $yindex = 0;
                    }
                }
                if ( !empty($dataArray) ) {
                    Common::exportExcel($dataArray);
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

     /*
     * 今日新增付费
     *
     * */
    public function total1(){
        $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $channels = array();
        $serverList = ServerModel::getServList();

        //今天
        $day = date('Ymd',time());
        $startTime = strtotime($day);
        $endTime = $startTime+86400;
        if(!empty($_POST['startTime'])){
            $startTime = strtotime($_POST['startTime']);
            $endTime = $startTime+86400;
        }
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        $where = '';
        if(!empty($auth['qd']['pt'])){
                $k1 = 0;
                $where .=  " and `platform` in ( ";
                foreach($auth['qd']['pt'] as $pt){
                    if($k1 == 0){
                        $where .=  "'".$pt."'";
                    }else{
                        $where .=  ",'".$pt."'";
                    }
                    $k1 ++;
                }
                $where .=  "  ) ";
        }
        $where1 = '';
        if(!empty($auth['qd']['sdk'])){
                $k1 = 0;
                $where1 .=  " and `paytype` in ( ";
                foreach($auth['qd']['sdk'] as $sdk){
                    if($k1 == 0){
                        $where1 .=  "'".$sdk."'";
                    }else{
                        $where1 .=  ",'".$sdk."'";
                    }
                    $k1 ++;
                }
                $where1 .=  "  ) ";
        }
        $serverId = ServerModel::getDefaultServerId();
        $db = Common::getDbBySevId($serverId);
        //新增注册
        $zc_count = array();
        $sqlzc = "select * from `register` where `reg_time` >= {$startTime} AND `reg_time` < {$endTime} {$where}  ";
        $zc_data = $db->fetchArray($sqlzc);
        
        foreach($zc_data as $k => $v){
            if(empty($zc_count[$v['servid']])){
                $zc_count[$v['servid']] = 0;
            }
            $zc_count[$v['servid']] += 1;
        }
        $id = ServerModel::getDefaultServerId();
        $db = Common::getDbBySevId($id);
        //登录用户
        $dl_count = array();
        $sqldl = "select * from `login_log` where `login_time` >= {$startTime} AND `login_time`<{$endTime} {$where}  ";
        $dl_data = $db->fetchArray($sqldl);
        foreach($dl_data as $k => $v){
            if(empty($dl_count[$v['servid']])){
                $dl_count[$v['servid']] = 0;
            }
            $dl_count[$v['servid']] += 1;
        }
        
        $zc = 0;
        $dl = 0;
        $ys = 0;
        $rs = 0;
        $bs = 0;
        $total = array();
        if(!empty($serverList)){
            foreach ($serverList as $k => $v) {
                $SevidCfg = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                   continue;
                }
                if($v['showtime'] > $_SERVER['REQUEST_TIME']){
                     continue;
                }
                
                //营收     付费人数   付费笔数     付费率
                $yingshou = 0;
                $renshu = 0;
                $bishu = 0;
                $rate = 0;
                $list = array();
                $db1 = Common::getDbBySevId($SevidCfg['sevid']);
                $order_sql = "select * from `t_order` where `status` > 0 and `ptime` >= {$startTime} and `ptime` < {$endTime} {$where} {$where1}";
                $order_list = $db1->fetchArray($order_sql);
                if(!empty($order_list)){
                    $bishu += count($order_list);
                    foreach ($order_list as $value){
                        $yingshou += Master::returnDoller($value['money']);
                        $list[$value['roleid']] = $value;
                    }
                }
                $renshu = count($list);
                
                $new_id = $SevidCfg['sevid'];
                
                $total[$new_id] = array(
                    'sevid' => $new_id,
                    'zc_count' => empty($zc_count[$new_id])?0:$zc_count[$new_id],
                    'dl_count' => empty($dl_count[$new_id])?0:$dl_count[$new_id],
                    'yingshou' => $yingshou,
                    'renshu' => $renshu,
                    'bishu' => $bishu,
                );
                $zc += $total[$new_id]['zc_count'];
                $dl += $total[$new_id]['dl_count'];
                $ys += $total[$new_id]['yingshou'];
                $rs += $total[$new_id]['renshu'];
                $bs += $total[$new_id]['bishu'];
                
            }
             arsort($total);
        }
        
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /*
     * 充值统计分类
     * 
     * */
    public function totalType(){
        
        $y_serid = $_GET['sevid'];
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        
        $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $where = '';

        if(!empty($_POST['startTime']) && !empty($_POST['endTime'])){
            $start = $_POST['startTime'];
            $end = $_POST['endTime'];
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
        }
        $where .= " and ptime>={$startTime} and ptime<={$endTime}";
        if(!empty($_POST['platForms'])){
            $where .= " and `platform` = '{$_POST['platForms']}'";
        }

        $serverid = ServerModel::getDefaultServerId();
        $db = Common::getDbBySevId($serverid);
        $login_sql = "select COUNT(DISTINCT `openid`) as num from `login_log` where {$where}";
        $login_info = $db->fetchArray($login_sql);
        $login_num = $login_info[0]['num'];
        $list = array();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
        
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                continue;
            }
        
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $cx_order_sql = "select `openid`,`money`,`ptime`,`platform`,`paytype` from `t_order` where `status`>0 {$where}";
            $order_list = $db->fetchArray($cx_order_sql);
            if(!empty($order_list)){
                $list = array_merge($list,$order_list);
            }
        }
        $result = array();
        $plat = array();
        if(!empty($list)){
            $p_array = array();
            foreach($list as $val){
                $ptime = date('Y-m-d',$val['ptime']);
                switch ($val['paytype']){
                    case 'houtai':
                        $result[$ptime]['resorce']['houtai'] += Master::returnDoller($val['money']);
                        break;
                    case 'wx':
                        $result[$ptime]['resorce']['wx'] += Master::returnDoller($val['money']);
                        break;
                    case 'zfb':
                        $result[$ptime]['resorce']['zfb'] += Master::returnDoller($val['money']);
                        break;
                    case 'appstore':
                        $result[$ptime]['resorce']['appstore'] += Master::returnDoller($val['money']);
                        break;
                     default:
                         $result[$ptime]['resorce']['android'] += Master::returnDoller($val['money']);
                         break;
                }
                $result[$ptime]['dnum'] +=1;
                $result[$ptime]['total'] += Master::returnDoller($val['money']);
                if(!in_array($val['openid'],$p_array)){
                    $p_array[] = $val['openid'];
                    $result[$ptime]['pnum'] +=1;
                }
                $plat[$val['platform']] += Master::returnDoller($val['money']);
            }
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 今日数据
     * */
    public function todayData($sid){
        
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $db = Common::getDbBySevId($serverid);
        
        $time = Game::day_0();
        $cx_zc_sql = "select * from `register` where `reg_time`>={$time}";
        $reg_info = $db->fetchArray($cx_zc_sql);
        
        if(!empty($reg_info)){
            foreach ($reg_info as $val){
                $regtime = date('Ymd',$val['reg_time']);
                $platform = $val['platform'];
                if(empty($sid)){
                        $result[$regtime][$platform]['reg_pnum'] += 1;
                    }elseif($val['servid'] == $sid){
                        $result[$regtime][$platform]['reg_pnum'] += 1;
                    }elseif(!empty($val['data'])){
                        $val['data'] = json_decode($val['data'],true);
                        foreach ($val['data'] as $seid => $data){
                            if($seid == $sid){
                                $result[$data['reg_time']][$platform]['reg_pnum'] += 1;
                                continue;
                            }
                        }                      
                    }
            }
        }
        
        $login_where = '';
        if(!empty($sid)){
            $login_where = " and `servid`={$sid}";
        }
        
        $cx_login_sql = "select * from `login_log` where `login_time`>={$time} {$login_where}";
        $login_info = $db->fetchArray($cx_login_sql);
        
        if(!empty($login_info)){
            foreach ($login_info as $val){
                $logintime = date('Ymd',$val['login_time']);
                $platform = $val['platform'];
                
                if(empty($login_result[$logintime][$platform]['openid'])){
                    $login_result[$logintime][$platform]['openid'] = array();
                }
                if(!in_array($val['openid'], $login_result[$logintime][$platform]['openid'])){
                    $login_result[$logintime][$platform]['openid'][] = $val['openid'];
                    $result[$logintime][$platform]['login_pnum'] += 1;
                }
            }
        }
        
        $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
        
        $serverList = ServerModel::getServList();
        $list = array();
        if(!empty($serverList)){
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
        
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }
        
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                
                if(!empty($sid) && $SevidCfg1['sevid'] != $sid){
                    continue;
                }
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $cx_order_sql = "select `openid`,`money`,`ptime`,`platform` from `t_order` where `status`>0 and `ptime`>={$time}";
                $order_list = $db->fetchArray($cx_order_sql);
                if(!empty($order_list)){
                    $list = array_merge($list,$order_list);
                }
            }
        }
        if(!empty($list)){
            $ls_total_arr = array();//临时总存放
            $ls_new_arr = array();//临时总存放
            $new_arr = array();
            foreach ($list as $value){
                $ptime = date('Ymd',$value['ptime']);
                $platform = $value['platform'];
                if(empty($ls_total_arr[$ptime])){
                    $ls_total_arr[$ptime] = array();
                }
                if(!in_array($value['openid'], $ls_total_arr[$ptime])){
                    $result[$ptime][$platform]['total_rechange_pnum'] +=1;//总充值人数
                    $ls_total_arr[$ptime][] = $value['openid'];
                }
                $result[$ptime][$platform]['rechange_num'] += 1;//付费笔数
                $result[$ptime][$platform]['total_money'] += Master::returnDoller($value['money']);//总金额

                //新增
                if(!in_array($value['openid'], $ls_new_arr)){
                    $ls_new_arr[] = $value['openid'];
                    $new_arr[$value['openid']]['money'][] = Master::returnDoller($value['money']);
                    $new_arr[$value['openid']]['data'] = array('ptime' => $ptime,'platform'=> $platform);
                }else{
                    if($ptime == $new_arr[$value['openid']]['data']['ptime']){
                        $new_arr[$value['openid']]['money'][] = Master::returnDoller($value['money']);
                    }elseif($ptime < $new_arr[$value['openid']]['data']['ptime']){
                        unset($new_arr[$value['openid']]);
                        $new_arr[$value['openid']]['money'][] = Master::returnDoller($value['money']);
                        $new_arr[$value['openid']]['data'] = array('ptime' => $ptime,'platform'=> $platform);
                    }
                }
            }
        }
        //新增投资统计
        if(!empty($new_arr)){
            foreach ($new_arr as $open_id => $val){
                $result[$val['data']['ptime']][$val['data']['platform']]['new_rechange_pnum'] +=1;
                foreach ($val['money'] as $mon){
                    $result[$val['data']['ptime']][$val['data']['platform']]['new_money'] += Master::returnDoller($mon);
                }
            }
        }
        return $result;
    }
    
    /**
     * 身份分布
     **/
    public function goverpost(){
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (empty($_POST['channels'])) {
            $_POST['channels'] = $channels;
        } else {
            $channels = array_intersect($channels, $_POST['channels']);
        }
        $serverid = ServerModel::getDefaultServerId();
        $mccache = Common::getCacheBySevId($serverid);
        $mkey = 'CX_GOVER_POST_LIST';
        $result = $mccache->get ($mkey);
        if(empty($result) || $_POST['type'] == 2){
            $k1 = 0;
            $where = '';
            if(!empty($channels)){
                $where .=  " where platform in ( ";
                foreach($channels as $pt){
                    if($k1 == 0){
                        $where .=  "'".$pt."'";
                    }else{
                        $where .=  ",'".$pt."'";
                    }
                    $k1 ++;
                }
                $where .=  "  ) ";
            }
            $list = array();
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            $k1 = 0;
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                if ($_POST['servid'] != 0 && !empty($_POST['servid'])){
                    if ($v['id'] != $_POST['servid']){
                        continue;
                    }
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                for ($j = 0; $j<19; $j++){
                    $dataInfo[$v['id']][$j] = 0;
                }
                $dataInfo[$v['id']]['total'] = 0;
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $table_div = Common::get_table_div($SevidCfg1['sevid']);
                for ($i = 0 ; $i < $table_div ; $i++){
                    $table = '`user_'.Common::computeTableId($i).'`';
                    $sql = "select `level`,count(`level`) as levelTotal from {$table} {$where} GROUP BY `level`";
                    $userData = $db->fetchArray($sql);
                    foreach ($userData as $key => $value){
                        $dataInfo[$v['id']][$value['level']] = $dataInfo[$v['id']][$value['level']] + $value["levelTotal"];
                        $dataInfo[$v['id']]['total'] = $dataInfo[$v['id']]['total'] + $value["levelTotal"];
                    }
                    unset($userData);
                }
            }
            $mccache->set($mkey, $dataInfo, 86400);
        }else{
            $dataInfo = $result;
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /*
     * 用户统计
     * */
    public function totalUser(){
        $y_serid = $_GET['sevid'];
        
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }
        
        if(!empty($auth['qd']['pt'])){
            foreach ($platformList as $key => $pl){
                if(!in_array($key, $auth['qd']['pt'])){
                    unset($platformList[$key]);
                }
            }
        }
        if(!empty($platformList)){
            foreach($platformList as $k => $pl){
                $channels[] = $k;
            }
        }
        if(!empty($_POST['channels'])){
            $platforms = $channels = $_POST['channels'];
        }else{
            $platforms = $channels;
        }
        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());
        
        if(!empty($_POST['beginDate']) && !empty($_POST['endDate'])){
            $start = $_POST['beginDate'];
            $end = $_POST['endDate'];
        }
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $where = "where `reg_time`>='{$startTime}' and `reg_time`<='{$endTime}'";
        
//         if(empty($_POST['serverid'])){
//             $_POST['serverid'] = $y_serid;
//         }elseif($_POST['serverid'] == 'all'){
//             $_POST['serverid'] = array();
//         }
        
        if(!empty($_POST['serverid'])){
            $where .= " and `servid`='{$_POST['serverid']}'";
        }
        
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $db = Common::getDbBySevId($serverid);
        $sql = "select DISTINCT `openid`,`platform`,`reg_time`,`uid`,`servid`,`data` from `register` {$where} ORDER BY `reg_time` ASC ";
        $data = $db->fetchArray($sql);
        $totals = 0;
        if(!empty($data)){
            foreach ($data as $k => $val){
                if(!empty($platforms)){
                    if (!in_array($val['platform'], $platforms)){
                        unset($data[$k]);
                        continue;
                    }
                }
                if (!empty($val['data'])){

                    $count = count(json_decode($val['data']));
                    $totals += $count;
                    $totals += 1;
                    unset($count);
                }else{
                    $totals +=1;
                }
                $result[$val['servid']][] = $val;
            }
        }
      
        $list = array();
        $lists = array();
        if(!empty($result)){
            foreach ($result as $serid => $val){
                foreach ($val as $v){
                    if(!empty($platforms)){
                        if(in_array($v['platform'], $platforms)){
                            if (!empty($v['data'])){
                                $count = count(json_decode($v['data']));
                                $lists[date('Ymd',$v['reg_time'])] += $count;
                                $lists[date('Ymd',$v['reg_time'])] += 1;
                            }else{
                                $lists[date('Ymd',$v['reg_time'])] += 1;
                            }
                            $list[date('Ymd',$v['reg_time'])] += 1;
                        }
                    }
                }
            }
        }
        ksort($list);
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 充值排序
     */
    public function paysort(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        Common::loadModel('UserModel');
        Common::loadModel('OrderModel');
        $guan = Game::getCfg('guan');
        $platformList = OrderModel::get_platform();
        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }
        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }

        $where = " `paytype` != ''";
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if(!empty($_POST['platForms']) && $_POST['platForms'] != 'all'){
            $where .= ' and `platform` like \''.$_POST['platForms'].'\'';
        }elseif (!empty($auth['qd']['pt'])){
            $k1 = 0;
            $where .=  "  and `platform` in ( ";
            foreach($auth['qd']['pt'] as $value){
                if($k1 == 0){
                    $where .=  "'".$value."'";
                }else{
                    $where .=  ",'".$value."'";
                }
                $k1 ++;
            }
            $where .=  "  ) ";
        }
        if(!empty($_POST)){
            if($_POST['uid']){
                $where .= ' and `roleid`='.$_POST['uid'];
            }
            if(!empty($_POST['startTime'])){
                $start = $_POST['startTime'];
                $startTime = strtotime($start);
            }
            if(!empty($_POST['endTime'])){
                $end = $_POST['endTime'];
                $endTime = strtotime($end);
            }
        }
        $where .= " and `status` > 0 " ;
        $where .= " and `ptime`>={$startTime} and `ptime`<={$endTime}";

        $sql = "select * from `t_order` where ".$where;
        // $moneysql = "select `roleid`,sum(`money`) as `totalMoney` from `t_order` where ".$where." GROUP BY `roleid` ORDER BY sum(`money`) DESC";
        $moneysql = "select `roleid`,`money` from `t_order` where ".$where."";
        $list = array();
        $data = array();
        Common::loadModel('ServerModel');
        if (!empty($_POST['serverid'])){
            $serverid = $_POST['serverid'];
            $serverList = ServerModel::getServList();
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            foreach ($serverList as $k => $v) {
                if ($serverid != 'all'){
                    if (is_numeric($serverid)){
                        if($serverid != $v['id']){
                            continue;
                        }
                    }else{
                        $region = explode('-',$serverid);
                        if ($v['id'] < $region[0] || $v['id'] > $region[1]){
                            continue;
                        }
                    }
                }
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $res = $db->fetchArray($sql);
                $list = array_merge($list,$res);
                $info = $db->fetchArray($moneysql);
                $data = array_merge($data, $info);
            }
        }else{
            $serverID = intval($_POST['serverid']);
            $SevidCfg1 = Common::getSevidCfg($serverID);//子服ID
            if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                Master::error('error1');
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                Master::error('error2');
            }
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $res = $db->fetchArray($sql);
            $list = array_merge($list,$res);
            $data = $db->fetchArray($moneysql);

        }
        if(is_array($data)&& !empty($data)){
            foreach ($data as $dkey){
                $userModel = new UserModel($dkey['roleid']);
                $momeyInfo[$dkey['roleid']]['money'] = $this->returnDollar($dkey['totalMoney']);
                $momeyInfo[$dkey['roleid']]['name'] = $userModel->info['name'];
                $momeyInfo[$dkey['roleid']]['vip'] = $userModel->info['vip'];
                $momeyInfo[$dkey['roleid']]['cash'] = $userModel->info['cash'];
                $momeyInfo[$dkey['roleid']]['lastlogin'] = $userModel->info['lastlogin'];
                $momeyInfo[$dkey['roleid']]['level'] = $guan[$userModel->info['level']]['name'];
            }
            arsort($momeyInfo);
        }
        if($_POST['excel'] == 1){
            $dataArray = array();
            $xindex = $yindex = 0;
            $maxRowNum = 65536;// 设置excel每张表最大记录数
            $xlsTitles = array('玩家','名称', 'vip','身份','金额');// EXCEL工作表表头
            if (!empty($momeyInfo) && is_array($momeyInfo)) {
                $add_reg = 0;
                $add_money = 0;
                foreach ($momeyInfo as $k => $v) {
                    if ( 0 == $yindex ) {
                        $dataArray[$xindex][$yindex] = $xlsTitles;
                    }
                    $yindex++;
                    $dataArray[$xindex][$yindex] = array(
                        $k,
                        $v['name'],
                        $v['vip'],
                        $v['level'],
                        $v['money'],
                    );
                    if ( $yindex >= $maxRowNum ) {
                        $xindex++;
                        $yindex = 0;
                    }
                }
                if ( !empty($dataArray) ) {
                    Common::exportExcel($dataArray);
                }
            }
        }
        $total = array();
        $all_money = 0; //总计
        if(!empty($list)){
            foreach ($list as $val){
                //以日期计算
                $ctime = date('ymd',$val['ptime']);
                if(empty($total[$ctime])){
                    $total[$ctime] = 0;
                }
                $total[$ctime] +=  $this->returnDollar($val['money']);
                $all_money += $this->returnDollar($val['money']);
            }
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /**
     * 玩家充值详情
     */
    public function userPayInfo(){
        $uid = $_REQUEST['uid'];
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if ($uid){
            $Act6180Model = Master::getAct6180($this->uid);
            if(!empty($_POST['startTime'])){
                $startTime = strtotime($_POST['startTime']);
            }
            if(!empty($_POST['endTime'])){
                $endTime = strtotime($_POST['endTime']);
            }
            Common::loadModel('UserModel');
            $userModel = new UserModel($uid);
            $guan = Game::getCfg('guan');
            $serverId = Game::get_sevid($uid);
            $SevidCfg1 = Common::getSevidCfg($serverId);
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $where = " `roleid`=".$uid;
            $where .= " and `status` > 0 " ;
            $where .= " and `ptime`>={$startTime} and `ptime`<={$endTime}";
            $sql = "select * from `t_order` where ".$where;
            $data = $db->fetchArray($sql);
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
/**
     * 充值=>渠道查询
     * */
    public function qudaodata(){

        $gift_bag = Game::getGiftBagCfg();
        $y_serid = $_GET['sevid'];
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        
        if(!empty($_POST)){
            if(!empty($_POST['startTime'])){
                $startTime = strtotime($_POST['startTime']);
            }
            if(!empty($_POST['endTime'])){
                $endTime = strtotime($_POST['endTime']);
            }
         }
         
        $where = " `status` > 0 " ;
        $where .= " and ptime>={$startTime} and ptime<={$endTime}";
        
        //平台
        $k1 = 0;

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if(!empty($auth['qd']['pt'])){
            $where .=  "  and platform in ( ";
            foreach($auth['qd']['pt'] as $pt){
                if($k1 == 0){
                    $where .=  "'".$pt."'";
                }else{
                    $where .=  ",'".$pt."'";
                }
                $k1 ++;
            }
            $where .=  "  ) ";
        }
        
        //支付方式
        $k1 = 0;
        if(!empty($auth['qd']['sdk'])){
            $where .=  "  and paytype in ( ";
            foreach($auth['qd']['sdk'] as $sdk){
                if($k1 == 0){
                    $where .=  "'".$sdk."'";
                }else{
                    $where .=  ",'".$sdk."'";
                }
                $k1 ++;
            }
            $where .=  "  ) ";
        }
        
        if(!empty($_POST['uid'])){
            $uid = $_POST['uid'];
            $where .= " and roleid = {$uid} ";
            if (is_numeric($_POST['uid'])){
                $userInfo =  new UserModel(intval($_POST['uid']));
            }
        }

        $sql = "select * from `t_order` where ".$where;
        $list = array();
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
        $server = explode('-', $_POST['server']);
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            if ($_POST['server'] != 'all' && !is_numeric($_POST['server'])){
                if ($v['id']<$server[0] || $v['id']>$server[1]){
                    continue;
                }
            }
            if (is_numeric($_POST['server'])){
                if ($v['id'] != $_POST['server']){
                    continue;
                }
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                continue;
            }

            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $res = $db->fetchArray($sql);
            $list = array_merge($list,$res);
        }
        $total = array();
        $all_money = 0; //总计
        if(!empty($list)){
            foreach ($list as $vl => $val){
                //以日期计算
                $ctime = date('ymd',$val['ptime']);
                if(empty($total[$ctime])){
                    $total[$ctime] = 0;
                }
                $temp= $val['diamond'] / 10000;
                if( $temp >= 200){
                    $hid = intval($temp - 100);
                }else{
                    $hid = $temp % 100;
                }
                $list[$vl]["diamondType"] = isset($gift_bag[$hid]) ? $gift_bag[$hid]["name"] : "";

                $total[$ctime] +=  $this->returnDollar($val['money']);
                $all_money += $this->returnDollar($val['money']);
            }
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    public function newLtv(){
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }
        if(!empty($platformList)){
            foreach($platformList as $k => $pl){
                $channels[] = $k;
            }
        }
        if(!empty($_POST['channels'])){
            $channels = $_POST['channels'];
        }
        $startTime = strtotime(date('Y-m-d'))-30*86400;
        //注册信息
        $db = Common::getDbBySevId($serverid);
        $sql = "select `openid`,`reg_time`,`platform` from `register` WHERE `reg_time`>".$startTime;
        $register = $db->fetchArray($sql);
        //每天注册的openid组成一个数组
        foreach ($register as $rk => $rv){
            $dayRegister[date('Y-m-d', $rv['reg_time'])]['openid'][] = $rv['openid'];
        }
        unset($register, $sql);
        $serverList = ServerModel::getServList();
        $order = array();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $sql = "SELECT  date_format(from_unixtime(`ptime`), '%Y-%m-%d') AS `time`,`openid`,sum(`money`) AS `totalMoney`,
                    `platform` FROM `t_order` where `ptime`>". $startTime.' GROUP BY `openid`,`time` ORDER BY `time`;';
            $result = $db->fetchArray($sql);
            $order = array_merge($result, $order);
        }
        $data = array();
        if (!empty($channels)) {
            foreach ($order as $ok => $ov) {
                if (in_array($ov['platform'], $channels)) {
                    foreach ($dayRegister as $dk => $dv) {
                        if (!isset($data[$dk]['register'])) {
                            $data[$dk]['register'] = count($dv['openid']);
                        }

                        if (in_array($ov['openid'], $dv['openid'])) {
                            $data[$dk]['money'][$ov['time']] += $ov['totalMoney'];
                        }
                    }
                }
            }
        }
        ksort($data);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 新ltv
     * */
    public function ltv(){
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $serverList = ServerModel::getServList();
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        $platformClassify = OrderModel::get_platform_classify();
        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }
        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }
        if(!empty($platformList)){
            foreach($platformList as $k => $pl){
                $channels[] = $k;
            }
        }
        if(!empty($_POST['channels'])){
            $channels = $_POST['channels'];
        }
        $platforms = empty($channels)?'':implode('","', $channels);
        $platformWhere = ' and `platform` IN ("'.$platforms.'")';
        if(!empty($_POST['beginDate']) && !empty($_POST['endDate'])){
            $yesday = strtotime($_POST['endDate']);
            $startTime =  strtotime($_POST['beginDate']);
        }else{
            $yesday = strtotime(date('Ymd',time()));
            $startTime = $yesday - 7*24*3600;
        }
        $reg_sql_where = " `reg_time`>={$startTime} AND `reg_time`<={$yesday} {$platformWhere}";
        $order_sql_where = " `status`>0 and `ptime`<={$yesday} and `ptime`>={$startTime}  {$platformWhere} ";
        $mccache = Common::getCacheBySevId($serverid);
        //时间
        if (!empty($_POST)){
            //相同条件
            $keyInfo['beginDate'] = $_POST['beginDate'];
            $keyInfo['endDate'] = $_POST['endDate'];
            $keyInfo['channels'] = $_POST['channels'];
            $keyInfo['servid'] = $_POST['servid'];
            //获取缓存数据
            $info_key = 'ADMAIN_DATA_LTV_INFO_RECODE'.md5(json_encode($keyInfo));//记录ltv上次记录的信息
            $recode = $mccache->get($info_key);
        }else{
            //获取缓存数据
            $info_key = 'ADMAIN_DATA_LTV_INFO_RECODE';//记录ltv上次记录的信息
            $recode = $mccache->get($info_key);
        }

        if (!empty($_POST['servid'])){
            $reg_sql_where .= ' AND `servid`='.$_POST['servid'];
        }
        if(empty($recode)){
           // unset($recode, $recode_list);
            $db = Common::getDbBySevId($serverid);
            $reg_sql = "select `openid`,`reg_time`,`platform` from `register` where {$reg_sql_where}";
            $reg_info = $db->fetchArray($reg_sql);
            if(!empty($reg_info)){
                foreach ($reg_info as $val){
                    $regtime = date('Ymd',$val['reg_time']);
                    $platform = $val['platform'];
                    $register[$regtime][$platform]['openid'][] = $val['openid'];
                    $register[$regtime][$platform]['reg_pnum'] += 1;
                }
                ksort($register);
                $serverList = ServerModel::getServList();
                $list = array();
                foreach ($serverList as $k => $v) {
                    if ( empty($v) ) {
                        continue;
                    }
                    if (!empty($_POST['servid']) && $_POST['servid']!=$v['id'] ){
                        continue;
                    }
                    $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                    if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                        continue;
                    }
                    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                        continue;
                    }
                    $db = Common::getDbBySevId($SevidCfg1['sevid']);
                    $cx_order_sql = "select `openid`,`money`,`ptime`,`platform` from `t_order` where {$order_sql_where}";
                    $order_list = $db->fetchArray($cx_order_sql);
                    if(!empty($order_list)){
                        $list = array_merge($list,$order_list);
                    }
                }

                if(!empty($list)){
                    foreach ($list as $orderData){
                        $time = date('Ymd',$orderData['ptime']);
                        if ($this->isExchange){
                            $order[$time][$orderData['openid']][] = $this->exchange($orderData['money']) ;
                        }else{
                            $order[$time][$orderData['openid']][] = $orderData['money'] ;
                        }

                    }
                }
                $list =array();
                foreach ($register as $time => $value){
                    $times =  array();
                    for ($itime = 1;$itime<150; $itime++){
                        $times[$itime] = date('Ymd',strtotime('+'.$itime.' day',strtotime($time)));
                    }
                    foreach ($value as $plat => $dva){
                        //注册人数
                        $list[$time][$plat]['reg_num'] += $dva['reg_pnum'];
                        foreach ($dva['openid'] as $open){
                            if(!empty($order[$time][$open])){
                                for ($i=1;$i<=150;$i++){
                                    $list[$time][$plat]['money'][$i] += array_sum($order[$time][$open]);
                                }
                            }
                            for ($itime = 1;$itime<150; $itime++){
                                if(!empty($order[$times[$itime]][$open])){
                                    $i = $itime+1;
                                    for ($i;$i<=150;$i++){
                                        $list[$time][$plat]['money'][$i] += array_sum($order[$times[$itime]][$open]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $recode['time'] = $yesday;
            if(!empty($list)){
               foreach ($list as $time => $val){
                   if(empty($val)) continue;
                   foreach ($val as $p => $v){
                       $recode['info'][$time][$p]['reg_num'] = $v['reg_num'];
                       if(empty($v['money'])) continue;
                       foreach ($v['money'] as $day => $m){
                           if(empty($recode['info'][$time][$p]['money'][$day])){
                               $recode['info'][$time][$p]['money'][$day] = $m;
                               $recode['startTime'] = $startTime;
                               $recode['endTime'] = $yesday;
                           }
                       }
                   }
               }
            }
            $mccache->set($info_key, $recode, 7*24*3600);
        }else{
            $recode = $mccache->get($info_key);
            if(!empty($recode)){
                $recode_list = $recode['info'];
            }
        }
        $list = array();
        //返回前端数据
        if($_POST){//过滤
            if(!empty($_POST['channels'])){
                $channels = $_POST['channels'];
            }
        }
        $platforms = $channels;

        if(!empty($recode['info'])){
            foreach ($recode['info'] as $key => $val){
                foreach ($val as $plat => $val1){
                    if(!empty($platforms) && !in_array($plat, $platforms)){
                        unset($recode['info'][$key][$plat]);
                    }
                }
            }
        }

        //整合
        if (!empty($recode['info'])){
            foreach ($recode['info'] as $time => $val){
                if(empty($start)){
                    $start = $time;
                }
                $end = $time;
                if (!empty($val)){
                    foreach ($val as $p => $v){
                        $list[$time]['reg_num'] = empty($list[$time]['reg_num']) ? $v['reg_num'] : $list[$time]['reg_num']+$v['reg_num'];
                        if(empty($v['money'])) continue;
                        foreach ($v['money'] as $day => $m){
                            $list[$time]['money'][$day] = empty($list[$time]['money'][$day]) ? $m : $list[$time]['money'][$day] + $m;
                        }
                    }
                }
            }
        }
       if(empty($_POST['beginDate']) && empty($_POST['endDate'])){
           $_POST['beginDate'] = date('Y-m-d 00:00:00', $startTime);
           $_POST['endDate'] = date('Y-m-d 23:59:59', $yesday );
       }
       $SevidCfg = Common::getSevidCfg($y_serid);

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 新ltv(美元)
     * */
    public function ltvUSD(){
        set_time_limit(0);
        ini_set('memory_limit','4000M');
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $serverList = ServerModel::getServList();
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        $platformClassify = OrderModel::get_platform_classify();
        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }
        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }
        if(!empty($platformList)){
            foreach($platformList as $k => $pl){
                $channels[] = $k;
            }
        }
        if(!empty($_POST['channels'])){
            $channels = $_POST['channels'];
        }
        $platforms = empty($channels)?'':implode('","', $channels);
        $platformWhere = ' and `platform` IN ("'.$platforms.'")';
        if(!empty($_POST['beginDate']) && !empty($_POST['endDate'])){
            $yesday = strtotime($_POST['endDate']);
            $startTime =  strtotime($_POST['beginDate']);

            $reg_sql_where = " `reg_time`>={$startTime} AND `reg_time`<={$yesday} {$platformWhere}";
            $order_sql_where = " `status`>0 and `ptime`<={$yesday} and `ptime`>={$startTime}  {$platformWhere} ";
            $mccache = Common::getCacheBySevId($serverid);
            //时间
            if (!empty($_POST)){
                //相同条件
                $keyInfo['beginDate'] = $_POST['beginDate'];
                $keyInfo['endDate'] = $_POST['endDate'];
                $keyInfo['channels'] = $_POST['channels'];
                $keyInfo['servid'] = $_POST['servid'];
                //获取缓存数据
                $info_key = 'ADMAIN_DATA_USDLTV_INFO_RECODE'.md5(json_encode($keyInfo));//记录ltv上次记录的信息
                $recode = $mccache->get($info_key);
            }else{
                //获取缓存数据
                $info_key = 'ADMAIN_DATA_USDLTV_INFO_RECODE';//记录ltv上次记录的信息
                $recode = $mccache->get($info_key);
            }

            if (!empty($_POST['servid'])){
                $reg_sql_where .= ' AND `servid`='.$_POST['servid'];
            }
            if(empty($recode) || true){
                // unset($recode, $recode_list);
                $db = Common::getDbBySevId($serverid);
                $reg_sql = "select `reg_time`,`platform`,`uid` from `register` where {$reg_sql_where}";
                $reg_info = $db->fetchArray($reg_sql);
                if(!empty($reg_info)){
                    foreach ($reg_info as $val){
                        $regtime = date('Ymd',$val['reg_time']);
                        $platform = $val['platform'];
                        $register[$regtime][$platform]['uid'][] = $val['uid'];
                        $register[$regtime][$platform]['reg_pnum'] += 1;
                    }
                    ksort($register);
                    $serverList = ServerModel::getServList();
                    $list = array();
                    foreach ($serverList as $k => $v) {
                        if ( empty($v) ) {
                            continue;
                        }
                        if (!empty($_POST['servid']) && $_POST['servid']!=$v['id'] ){
                            continue;
                        }
                        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                            continue;
                        }
                        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                            continue;
                        }
                        $db = Common::getDbBySevId($SevidCfg1['sevid']);
                        $cx_order_sql = "select `roleid`,`money`,`ptime`,`platform` from `t_order` where {$order_sql_where}";
                        $order_list = $db->fetchArray($cx_order_sql);
                        if(!empty($order_list)){
                            $list = array_merge($list,$order_list);
                        }
                    }

                    if(!empty($list)){
                        foreach ($list as $orderData){
                            $time = date('Ymd',$orderData['ptime']);
                            if ($this->isExchange){
                                $order[$time][$orderData['roleid']] += $this->exchange($orderData['money']) ;
                            }else{
                                $order[$time][$orderData['roleid']] += $orderData['money'];
                            }

                        }
                    }
                    $list =array();
                    foreach ($register as $time => $value){
                        $times =  array();
                        for ($itime = 1;$itime<150; $itime++){
                            $times[$itime] = date('Ymd',strtotime('+'.$itime.' day',strtotime($time)));
                        }
                        foreach ($value as $plat => $dva){
                            //注册人数
                            $list[$time][$plat]['reg_num'] += $dva['reg_pnum'];
                            foreach ($dva['uid'] as $open){
                                if(!empty($order[$time][$open])){
                                    for ($i=1;$i<=150;$i++){
                                        $list[$time][$plat]['money'][$i] += $order[$time][$open];
                                    }
                                }
                                for ($itime = 1;$itime<150; $itime++){
                                    if(!empty($order[$times[$itime]][$open])){
                                        $i = $itime+1;
                                        for ($i;$i<=150;$i++){
                                            $list[$time][$plat]['money'][$i] += $order[$times[$itime]][$open];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $recode['time'] = $yesday;
                if(!empty($list)){
                    foreach ($list as $time => $val){
                        if(empty($val)) continue;
                        foreach ($val as $p => $v){
                            $recode['info'][$time][$p]['reg_num'] = $v['reg_num'];
                            if(empty($v['money'])) continue;
                            foreach ($v['money'] as $day => $m){
                                if(empty($recode['info'][$time][$p]['money'][$day])){
                                    $recode['info'][$time][$p]['money'][$day] = $m;
                                    $recode['startTime'] = $startTime;
                                    $recode['endTime'] = $yesday;
                                }
                            }
                        }
                    }
                }
                $mccache->set($info_key, $recode, 300);
            }else{
                $recode = $mccache->get($info_key);
                if(!empty($recode)){
                    $recode_list = $recode['info'];
                }
            }
            $list = array();
            //返回前端数据
            if($_POST){//过滤
                if(!empty($_POST['channels'])){
                    $channels = $_POST['channels'];
                }
            }
            $platforms = $channels;

            if(!empty($recode['info'])){
                foreach ($recode['info'] as $key => $val){
                    foreach ($val as $plat => $val1){
                        if(!empty($platforms) && !in_array($plat, $platforms)){
                            unset($recode['info'][$key][$plat]);
                        }
                    }
                }
            }

            //整合
            if (!empty($recode['info'])){
                foreach ($recode['info'] as $time => $val){
                    if(empty($start)){
                        $start = $time;
                    }
                    $end = $time;
                    if (!empty($val)){
                        foreach ($val as $p => $v){
                            $list[$time]['reg_num'] = empty($list[$time]['reg_num']) ? $v['reg_num'] : $list[$time]['reg_num']+$v['reg_num'];
                            if(empty($v['money'])) continue;
                            foreach ($v['money'] as $day => $m){
                                $list[$time]['money'][$day] = empty($list[$time]['money'][$day]) ? $m : $list[$time]['money'][$day] + $m;
                            }
                        }
                    }
                }
            }
        }else{
            $yesday = time();
            $startTime = $yesday - 7*24*3600;
        }

        if(empty($_POST['beginDate']) && empty($_POST['endDate'])){
            $_POST['beginDate'] = date('Y-m-d 00:00:00', $startTime);
            $_POST['endDate'] = date('Y-m-d 23:59:59', $yesday );
        }
        $SevidCfg = Common::getSevidCfg($y_serid);

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * ltv
     * */
    public function oldltv() {  
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }
        
        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }
        if(!empty($platformList)){
            foreach($platformList as $k => $pl){
                $channels[] = $k;
            }
        }
        
        $mccache = Common::getCacheBySevId($serverid);
        //时间
        $yesday = strtotime(date('Ymd',time()));
        $yesdaytime = $yesday;
        //获取注册信息
        /********************注册统计***********************/
        $reg_key = 'REGISTER_CX_'.$yesday;
        $reg_info = $mccache->get ($reg_key);
        if(empty($reg_info)){
            $cx_zc_sql = "select * from `register` where `reg_time`<{$yesday}";
            $db = Common::getDbBySevId($serverid);
            $reg_info = $db->fetchArray($cx_zc_sql);
            if(!empty($reg_info)){
                $mccache->set($reg_key, json_encode($reg_info));
            }
        }else{
            $reg_info = json_decode($reg_info,true);
        }
        
        
        //订单信息
        $order_key = 'ORDER_CX_'.$yesday;
        $list = $mccache->get ($order_key);
        if(empty($list)){
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            $serverList = ServerModel::getServList();
            $list = array();
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
            
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }
            
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $cx_order_sql = "select `openid`,`money`,`ptime`,`platform` from `t_order` where `status`>0 and `ptime`<{$yesdaytime}";
                $order_list = $db->fetchArray($cx_order_sql);
                if(!empty($order_list)){
                    $list = array_merge($list,$order_list);
                }
            }
            if(!empty($list)){
                $mccache->set($order_key,json_encode($list));
            }       
         }else{
              $list = json_decode($list,true);
         }
         /*
          * 信息组装
          * */   
        if(!empty($reg_info)){
            foreach ($reg_info as $val){
                $regtime = date('Ymd',$val['reg_time']);
                $platform = $val['platform'];
                $register[$regtime][$platform]['openid'][] = $val['openid'];
                $register[$regtime][$platform]['reg_pnum'] += 1;
            }
            ksort($register);
            
            if(!empty($list)){
                foreach ($list as $orderData){
                    $time = date('Ymd',$orderData['ptime']);
                    $order[$time][$orderData['openid']][] = $orderData['money'];
                }
            }
            //信息过滤
            if($_POST){//过滤
                if(!empty($_POST['beginDate']) && !empty($_POST['endDate'])){
                    $beginDate = strtotime($_POST['beginDate']);
                    $endDate = strtotime($_POST['endDate']);
                    //过滤掉其他时间
                    if(!empty($register)){
                        foreach ($register as $key => $v){
                            $time = strtotime($key);
                            if($time<$beginDate || $time>$endDate){
                                unset($register[$key]);
                            }
                        }
                    }
                }
                if(!empty($_POST['channels'])){
                    $channels = $_POST['channels'];
                }
            }
            $platforms = $channels;
            if(!empty($register)){
                foreach ($register as $key => $val){
                    foreach ($val as $plat => $val1){
                        if(!in_array($plat, $platforms)){
                            unset($register[$key][$plat]);
                        }
                    }
                }
            }
        }
        
        
        
        
        //信息整合
        $list = array();
        if(!empty($register)){
            foreach ($register as $time => $value){
                if(empty($start)){
                    $start = $time;
                }
                $end = $time;
                $two_time = date('Ymd',strtotime('+1 day',strtotime($time)));
                $three_time = date('Ymd',strtotime('+2 day',strtotime($time)));
                $four_time = date('Ymd',strtotime('+3 day',strtotime($time)));
                $five_time = date('Ymd',strtotime('+4 day',strtotime($time)));
                $six_time = date('Ymd',strtotime('+5 day',strtotime($time)));
                $seven_time = date('Ymd',strtotime('+6 day',strtotime($time)));
                foreach ($value as $plat => $dva){
                    $list[$time]['reg_num'] += $dva['reg_pnum'];
                    foreach ($dva['openid'] as $open){
                        if(!empty($order[$time][$open])){
                            $list[$time]['money'][1] += array_sum($order[$time][$open]);
                        }
                        if(!empty($order[$two_time][$open])){
                            $list[$time]['money'][2] += array_sum($order[$two_time][$open]);
                        }
                        if(!empty($order[$three_time][$open])){
                            $list[$time]['money'][3] += array_sum($order[$three_time][$open]);
                        }
                        if(!empty($order[$four_time][$open])){
                            $list[$time]['money'][4] += array_sum($order[$four_time][$open]);
                        }
                        if(!empty($order[$five_time][$open])){
                            $list[$time]['money'][5] += array_sum($order[$five_time][$open]);
                        }
                        if(!empty($order[$six_time][$open])){
                            $list[$time]['money'][6] += array_sum($order[$six_time][$open]);
                        }
                        if(!empty($order[$seven_time][$open])){
                            $list[$time]['money'][7] += array_sum($order[$seven_time][$open]);
                        }
                    }
                }
            }
        }
        if(empty($_POST['beginDate']) && empty($_POST['endDate'])){
            $_POST['beginDate'] = date('Y-m-d 00:00:00',strtotime($start));
            $_POST['endDate'] = date('Y-m-d 23:59:59',strtotime($end));
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    
    /**
     * 每日数据
     * */
    public function everydayData() {
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        
        if(empty($_POST['beginDate']) || empty($_POST['endDate'])){
            $_POST['beginDate'] = date('Y-m-d 00:00:00');
            $_POST['endDate'] = date('Y-m-d 23:59:59');
        }
        
        $start = strtotime($_POST['beginDate']);
        $end = strtotime($_POST['endDate']);
        $where = " where `regtime`>={$start} and `regtime`<={$end}";
        $list = array();
        $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
//         $time = strtotime(date('Ymd',time()));
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
    
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                continue;
            }
    
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $table_div = Common::get_table_div($SevidCfg1['sevid']);
            for ($i = 0 ; $i < $table_div ; $i++){
                $table = '`user_'.Common::computeTableId($i).'`';
                $sql = "select `uid`,`name`,`regtime`,`platform` from {$table} {$where}";
                $userData = $db->fetchArray($sql);
                foreach ($userData as $uk => $uv){
                    $uidArray[] = $uv['uid'];
                    $dataInfo[$uv['uid']]['name'] = $uv['name'];
                    $dataInfo[$uv['uid']]['regtime'] = $uv['regtime'];
                    $dataInfo[$uv['uid']]['platform'] = $uv['platform'];
                }
                //释放变量
                unset($userData, $table, $sql, $uk, $uv);
            }
            if (!empty($uidArray)){
                $uids = implode(',', $uidArray);
            }
            //释放变量
            unset($uidArray);
            //取出相应的账号
            $ustr_sql = "SELECT `uid`,`ustr` FROM gm_sharding WHERE `uid` IN (".$uids.")";
            $ustr = $db->fetchArray($ustr_sql);
            if (!empty($ustr)){
                foreach ($ustr as $uk =>$uv){
                    $dataInfo[$uv['uid']]['ustr'] = $uv['ustr'];
                }
            }
            //释放变量
            unset($ustr, $uids);
        }

        if($_POST['excel'] == 1){
            $dataArray = array();
            $xindex = $yindex = 0;
            $maxRowNum = 65536;// 设置excel每张表最大记录数
            $xlsTitles = array('账号','uid', '名称','注册时间','平台');// EXCEL工作表表头
            if (!empty($dataInfo) && is_array($dataInfo)) {
                $add_reg = 0;
                $add_money = 0;
                foreach ($dataInfo as $k => $v) {
                    if ( 0 == $yindex ) {
                        $dataArray[$xindex][$yindex] = $xlsTitles;
                    }
                    $yindex++;
                    $dataArray[$xindex][$yindex] = array(
                        $v['openid'],
                        $k,
                        $v['name'],
                        date('Y-m-d H:i:s',$v['regtime']),
                        $v['platform'],
                    );
                    if ( $yindex >= $maxRowNum ) {
                        $xindex++;
                        $yindex = 0;
                    }
                }
                if ( !empty($dataArray) ) {
                    Common::exportExcel($dataArray);
                }
            }  
        }
     
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 游戏充值统计
     */
    public function paytotal(){
        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');
        if (!empty($_POST['startTime']) &&  !empty($_POST['endTime'])){
            $startTime = $_POST['startTime'];
            $endTime = $_POST['endTime'];
        }
        $url = 'king.test.zhisnet.cn/api/allTotalMoney.php?begindt='.$startTime.'&enddt='.$endTime;
        $totalMoney = $this->curl_https($url);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 滚服数据
     */
    public function rollServerData(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getDbBySevId($SevidCfg1['sevid']);
            $sql = 'SELECT `uid` FROM `gm_sharding`';
            $result = $db->fetchArray($sql);
            $count = count($result); //总人数
            unset($sql, $result);
            $sql = "select sum(`money`) as totalMoney from `t_order` where `status`>0";
            // $sql = "select money from `t_order` where `status`>0";
            $result = $db->fetchArray($sql);
            // $money = 0;
            // foreach($result as $val){
            //     $money += Master::returnDoller(floatval($val['money']));
            // }
            $money = $result[0]["totalMoney"]; //总充值
            unset($sql, $result);
            $data[$SevidCfg1['sevid']]['count'] = $count;
            $data[$SevidCfg1['sevid']]['money'] = $money;
            unset($count, $money, $result);
        }
        $serverID = ServerModel::getDefaultServerId();
        $SevidCfg1 = Common::getSevidCfg($serverID);
        $db = Common::getDbBySevId($SevidCfg1['sevid']);
        $sql = "SELECT * FROM `register` WHERE  `data`<>''";
        $register = $db->fetchArray($sql);
        foreach ($register as $key => $value){
            if ($value['servid']){
                $dataInfo = json_decode($value['data'],1);
                if (empty($dataInfo)){
                    continue;
                }
                foreach ($dataInfo as $dk => $dv){
                    if (is_numeric($dk)){
                        if (!isset($data[$dk]['rollCount'])){
                            $data[$dk]['rollCount'] = 0;
                        }
                        $data[$dk]['rollCount'] = $data[$dk]['rollCount'] + 1;
                        $data[$dk]['uids'][] = $dv['uid'];
                    }
                }
            }
        }
        foreach ($data as $dk => $dv){
            unset($uids, $sql, $userMoney);
            $db = Common::getDbBySevId($dk);
            if (empty($dv['uids'])){
                continue;
            }
            $uids = implode(',',$dv['uids']);
            $sql = "SELECT `money` FROM `t_order` WHERE `status`>0 AND `roleid` IN (".$uids.')';
            $userMoney = $db->fetchArray($sql);
            $data[$dk]['rollMoney'] = 0;
            foreach($userMoney as $val){
                $data[$dk]['rollMoney'] += floatval($val['money']);
            }
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function datangPayTotal(){
        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');
        if (!empty($_POST['startTime']) &&  !empty($_POST['endTime'])){
            $startTime = $_POST['startTime'];
            $endTime = $_POST['endTime'];
        }
        $totalMoney = 0;
        $url = 'http://datang.zhisnet.cn/api/allserverList.php?&begindt='.$startTime.'&enddt='.$endTime;
        $totalMoney = $this->curl_https($url);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 充值异常
     */
    public function payAbnormal(){
        $y_serid = $_GET['sevid'];
        $money = $_POST['money']?$_POST['money']:10000;
        $vip = $_POST['vip']?$_POST['vip']:15;
        $level = $_POST['level']?$_POST['level']:8;
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getMyDb();
            $sql = "SELECT `roleid`,sum(`money`) AS totalMoney FROM `t_order` WHERE `status`>0 GROUP BY `roleid`";
            $data = $db->fetchArray($sql);
            foreach ($data as $key => $value){
                if ($value['totalMoney'] > $money){
                    $user[$value['roleid']] = $value['totalMoney'];
                }
            }
            unset($data);
        }

        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 消费统计
     */
    public function consume(){
        $msg_lang = include ROOT_DIR."/administrator/extend/msg_lang.php";
        $stype = $_POST['stype'];
        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 1;
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if (!empty($_POST['startTime']) &&  !empty($_POST['endTime'])){
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
        }
        $where = " WHERE `time`>".$startTime.' AND `time`<'.$endTime.' AND `type`='.$itemId;
        if (!empty($_POST['from'])){
            $where .= ' AND `other`='.$_POST['from'];
        }

        if (!empty($_POST['server'])){
            $stype = 1;
        }

        $sql = "SELECT `from`,`other`,sum(`num`) AS total FROM `flow_consume` ".$where." GROUP BY `other`";
        if ($stype != 1) {

            $sql = "SELECT `uid`, `from`,`other`,sum(`num`) AS total FROM `flow_consume` ".$where." GROUP BY `uid`,`other`";
        }

        Common::loadModel('ServerModel');
        $total = 0;
        $consume = array();
        $serverList = ServerModel::getServList();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            if (!empty($_POST['server'])){
                if (!in_array($v['id'], $_POST['server'])){
                    continue;
                }
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $flowDb = Common::getMyDb('flow');
            $data = $flowDb->fetchArray($sql);

            $roleIdList = array();
            if ($stype != 1) {

                $db = Common::getDbBySevId($SevidCfg1['sevid']);

                $orderRes = $db->fetchArray("SELECT `roleid` as uid FROM `t_order` WHERE `diamond` < 10000 and `diamond` != 280 and `diamond` != 2880 group by uid ");
                foreach ($orderRes as $k => $value) {

                    if (in_array($value["uid"], $roleIdList)) {
                        continue;
                    }
                    $roleIdList[] = $value["uid"];
                }
            }

            foreach ($data as $k => $v) {
                if($v['from'] == 'userflow' || $v['other'] == 'hd6180Buy'){
                    continue;
                }

                if ($stype == 2 && !in_array($v['uid'], $roleIdList) ) {
                    continue;
                }

                if ($stype == 3 && in_array($v['uid'], $roleIdList) ) {
                    continue;
                }

                if (!isset($from[$v['other']])){
                    $from[$v['other']] = isset($msg_lang[$v['from']][$v['other']]) ? $msg_lang[$v['from']][$v['other']] : $v['from']."=".$v['other'];
                }
                $consume[$v['other']]['money'] += $v['total'];
                if (!isset($consume[$v['other']]['name'])){
                    $consume[$v['other']]['name'] = $msg_lang[$v['from']][$v['other']] ? $msg_lang[$v['from']][$v['other']] : $v['other'];
                }

                $total += $v['total'];
            }

        }
        if (!empty($consume)){
            arsort($consume);
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 消费人次统计
     */
    public function consumpTimes(){
        $msg_lang = include ROOT_DIR."/administrator/extend/msg_lang.php";
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if (!empty($_POST['startTime']) &&  !empty($_POST['endTime'])){
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
        }
        $where = " WHERE `time`>".$startTime.' AND `time`<'.$endTime;
        if (!empty($_POST['from'])){
            $where .= ' AND `other`='.$_POST['from'];
        }
        $sql = "SELECT `from`,`other`,COUNT(`uid`) AS times FROM `flow_consume` ".$where." GROUP BY `other`";
        Common::loadModel('ServerModel');
        $times = 0;
        $consume = array();
        $serverList = ServerModel::getServList();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            if (!empty($_POST['server'])){
                if (!in_array($v['id'], $_POST['server'])){
                    continue;
                }
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getMyDb('flow');
            $data = $db->fetchArray($sql);

            foreach ($data as $k => $v) {
                if($v['from'] == 'userflow'){
                    continue;
                }
                if (!isset($from[$v['other']])){
                    $from[$v['other']] = $msg_lang[$v['from']][$v['other']];
                }
                $consume[$v['other']]['times'] += $v['times'];
                if (!isset($consume[$v['other']]['name'])){
                    $consume[$v['other']]['name'] = $msg_lang[$v['from']][$v['other']] ? $msg_lang[$v['from']][$v['other']] : $v['other'];
                }

                $times += $v['times'];
            }

        }
        if (!empty($consume)){
            arsort($consume);
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 消费人数统计
     */
    public function consumeNums(){
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $SevidCfg1 = Common::getSevidCfg($serverid);
        $db = Common::getDbBySevId($SevidCfg1['sevid']);
        $msg_lang = include ROOT_DIR."/administrator/extend/msg_lang.php";
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if (!empty($_POST['startTime']) &&  !empty($_POST['endTime'])){
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
        }
        $where = " WHERE `time`>".$startTime.' AND `time`<'.$endTime;

        $loginWhere = " WHERE `login_time`>".$startTime.' AND `login_time`<'.$endTime;
        if(!empty($_POST['server'])){
            $sevid = implode(',',$_POST);
            $loginWhere = $loginWhere.' AND `servid` IN('.$sevid.')';
        }
        $loginSql = "select COUNT(DISTINCT `openid`) as num from `login_log` ".$loginWhere;
        $loginNum = $db->fetchArray($loginSql);

        if (!empty($_POST['from'])){
            $where .= ' AND `other`='.$_POST['from'];
        }
        $sql = "SELECT `from`,`other`,COUNT(DISTINCT `uid`) as num FROM `flow_consume` ".$where." GROUP BY `other`";

        $nums = 0;
        $consume = array();
        $serverList = ServerModel::getServList();
        foreach ($serverList as $k => $v) {
            if ( empty($v) ) {
                continue;
            }
            if (!empty($_POST['server'])){
                if (!in_array($v['id'], $_POST['server'])){
                    continue;
                }
            }
            $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getMyDb('flow');
            $data = $db->fetchArray($sql);
            foreach ($data as $k => $v) {
                if($v['from'] == 'userflow'){
                    continue;
                }
                if (!isset($from[$v['other']])){
                    $from[$v['other']] = isset($msg_lang[$v['from']][$v['other']]) ? $msg_lang[$v['from']][$v['other']] : $v['other'];
                }
                $consume[$v['other']]['num'] += $v['num'];
                if (!isset($consume[$v['other']]['name'])){
                    $consume[$v['other']]['name'] = $msg_lang[$v['from']][$v['other']] ? $msg_lang[$v['from']][$v['other']] : $v['other'];
                }

                $nums += $v['num'];
            }

        }
        if (!empty($consume)){
            arsort($consume);
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 直充
     */
    public function zhichong(){

        $items = Game::getCfg('item');
        $y_serid = $_GET['sevid'];
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadVoComModel('ComVoComModel');
        $authKey = 'authConfig';
        $ComVoComModel = new ComVoComModel($authKey, true);
        $userConfig = $ComVoComModel->getValue();
        $auth = $userConfig[$_SESSION["CURRENT_USER"]];
        if (empty($auth)){
            $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
        }

        if(!empty($auth['qd']['pt'])){
            if(!empty($platformList)){
                foreach ($platformList as $key => $pl){
                    if(!in_array($key, $auth['qd']['pt'])){
                        unset($platformList[$key]);
                    }
                }
            }
        }

        $rwd = Game::getGiftBagCfg();
        $where = " and `paytype` != ''";
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $total =0;
        if(!empty($_POST)){
            if(!empty($_POST['platForms']) && $_POST['platForms'] != 'all'){
                $where .= ' and `platform` like \''.$_POST['platForms'].'\'';
            }
            if(!empty($_POST['startTime'])){
                $start = $_POST['startTime'];
                $startTime = strtotime($start);
            }
            if(!empty($_POST['endTime'])){
                $end = $_POST['endTime'];
                $endTime = strtotime($end);
            }
        }
        $where .= " and ptime>={$startTime} and ptime<={$endTime}";
        $dataInfo = array();
        $total = 0;
        if($_POST['serverid'] <= 0){ //全服数据查询
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                if ($_POST['serverid'] == -1){
                    if ($v['id']%2 == 0){
                        continue;
                    }
                }
                if ($_POST['serverid'] == -2){
                    if ($v['id']%2 != 0){
                        continue;
                    }
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                if($_POST['stype'] == 1){
                    $sql = "select `money`,`diamond` from `t_order` where `status`>0 and `diamond`>10000 ".$where.'  order by `ptime` asc';
                    $result = $db->fetchArray($sql);
                    if (is_array($result) && !empty($result)) {
                        foreach ($result as $rk => $rv) {

                            $temp= $rv['diamond'] /10000;
                            if( $temp >= 200){
                                $hid = intval($temp - 100);
                            }else{
                                $hid = $temp % 100;
                            }
                            $dataInfo[$hid]['rmb'] += $rv['money'];
                            $total += $rv['money'];
                        }
                    }
                }elseif ($_POST['stype'] ==2){
                    $sql = "select `money`,`diamond`,COUNT(`roleid`) as times from `t_order` where `status`>0 and `diamond`>10000 ".$where.'  GROUP BY `diamond`';
                    $result = $db->fetchArray($sql);
                    if (is_array($result) && !empty($result)) {
                        foreach ($result as $rk => $rv) {

                            $temp= $rv['diamond'] /10000;
                            if( $temp >= 200){
                                $hid = intval($temp - 100);
                            }else{
                                $hid = $temp % 100;
                            }
                            $dataInfo[$hid]['rmb'] += $rv['times'];
                            $total += (float)$rv['times'];
                        }
                    }
                }else{
                    $sql = "select `money`,`diamond`,COUNT(DISTINCT `roleid`) as num from `t_order` where `status`>0 and `diamond`>10000 ".$where.'  GROUP BY `diamond`';
                    $result = $db->fetchArray($sql);
                    if (is_array($result) && !empty($result)) {
                        foreach ($result as $rk => $rv) {
                            $temp= $rv['diamond'] /10000;
                            if( $temp >= 200){
                                $hid = intval($temp - 100);
                            }else{
                                $hid = $temp % 100;
                            }
                            $dataInfo[$hid]['rmb'] += $rv['num'];
                            $total += $rv['num'];
                        }
                    }
                }
            }

        }else{ //单服数据
            $serverid = $_POST['serverid'];
            $db = Common::getDbBySevId($serverid);
            $sql = "select `money`,`diamond` from `t_order` where `status`>0 and `diamond`>10000 ".$where.'  order by `ptime` asc';
            $result = $db->fetchArray($sql);
            if (is_array($result) && !empty($result)) {
                foreach ($result as $rk => $rv) {
                    $temp= $rv['diamond'] /10000;
                    if( $temp >= 200){
                        $hid = intval($temp - 100);
                    }else{
                        $hid = $temp % 100;
                    }
                    $dataInfo[$hid]['rmb'] += $rv['money'];
                    $total += $rv['money'];
                }
            }
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 档位充值查询
     */
    public function dangwei(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $rmbInfo = array();
        $diamondInfo = array();
        if(!empty($_POST)){
            if(!empty($_POST['startTime'])){
                $start = $_POST['startTime'];
                $startTime = strtotime($start);
            }
            if(!empty($_POST['endTime'])){
                $end = $_POST['endTime'];
                $endTime = strtotime($end);
            }

            $serverid = $_POST['serverid'];
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverid && $serverid != $SevidCfg1['sevid'] ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $sql = "select `money`, COUNT(`roleid`) AS count from `t_order` where `status`>0 and `diamond` < 10000 and ptime >= {$startTime} and ptime <= {$endTime} GROUP BY `money`";
                $result = $db->fetchArray($sql);
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $rk => $rv) {

                        $money = intval($rv["money"]);
                        $rmbInfo[$money] += $rv["count"];
                    }
                }

                $sql = "select `diamond`, COUNT(`roleid`) AS count from `t_order` where `status`>0 and `diamond` > 10000 and ptime >= {$startTime} and ptime <= {$endTime} GROUP BY `diamond`";
                $result = $db->fetchArray($sql);
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $rk => $rv) {

                        $diamondInfo[$rv["diamond"]] += $rv["count"];
                    }
                }
            }
        }

        $dataInfo = array();
        // $order_shop_k = Game::getCfg('order_shop_k');
        $order_shop_k = Master::getOrderShopCfg();
        $gift_bag = Game::getGiftBagCfg();

        foreach ($order_shop_k as $k => $v) {

            $count = isset($rmbInfo[$v["rmb"]]) ? $rmbInfo[$v["rmb"]] : 0;
            $name = $v["cpId"];
            if ($v["type"] == 5) {
                $name = "周卡";
            }else if ($v["type"] == 2) {
                $name = "月卡";
            }else if ($v["type"] == 3) {
                $name = "年卡";
            }

            $dataInfo[] = array("name" => $name, "dollar" => $v["dollar"], "count" => $count, "money" => $v["dollar"] * $count);
        }

        foreach ($gift_bag as $k => $v) {

            $diamond = ($v["grade"] * 10) + 1000000 + (10000 * $v["id"]);
            $count = isset($diamondInfo[$diamond]) ? $diamondInfo[$diamond] : 0;
            $dataInfo[] = array("name" => $v["name_cn"], "dollar" => $v["dollar"], "count" => $count, "money" => $v["dollar"] * $count);
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * @param $url
     * @param array $data
     * @param array $header
     * @param int $timeout
     * @return mixed
     */
    public function curl_https($url, $data=array(), $header=array(), $timeout=30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $response = curl_exec($ch);
        if($error=curl_error($ch)){
            die($error);
        }
        curl_close($ch);
        return $response;
    }

    /**
     * 贵人令充值查询
     * */
    public function guirenling(){

        $total = 0;
        $people = 0;
        $list = array();
        $uids = array();

        $serverid = 0;
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $y_serid = $_GET['sevid'];
        $data = '';
        $data .= "`status` > 0 and `paytype` != 'houtai' AND `money` = 24 ";
        if(!empty($_POST)){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }

            if(!empty($_POST['startTime']) and !empty($_POST['endTime'])){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
                $data .= " and ptime>={$startTime} and ptime<={$endTime}";
            }

            foreach ($serverList as $k => $v) {
                if (empty($v)) {
                    continue;
                }

                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                $sevid = $SevidCfg1['sevid'];
                if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $sevid) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $sevid > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                if ( 0 < $serverid && $serverid != $SevidCfg1['sevid'] ) {
                    continue;
                }

                $db = Common::getDbBySevId($sevid);
                $sql = 'select * from `t_order` WHERE '.$data.' order by `ctime` desc';
                $searchRecords = $db->fetchArray($sql);

                if(!empty($searchRecords)){
                    foreach ($searchRecords as $key => $val){

                        $riqi = Game::is_ymd($val['ctime']);
                        $list[$riqi]++;
                        $total++;

                        if (!in_array($val["roleid"], $uids)) {

                            $people++;
                            $uids[] = $val["roleid"];
                        }
                    }
                }
            }
        }

        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 礼包充值查询
     * */
    public function giftbag(){

        $total = 0;
        $money = 0;
        $people = 0;
        $list = array();
        $uids = array();

        $serverid = 0;
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $y_serid = $_GET['sevid'];
        $data = '';
        $data .= "`status` > 0 and `paytype` != 'houtai' ";
        $present = 0;
        if(!empty($_POST)){
            if(!empty($_POST['gid'])){
                $gid = $_POST['gid'];

                $giftBag = Game::getCfg_info('gift_bag', $gid);
                $present = $giftBag["present"];
                $actcoin = 10 * $giftBag["grade"] + 1000000 + 10000 * $giftBag["id"];
                $data .= ' and `diamond` ='.$actcoin;
            }else{

                echo "<script>alert('请输入礼包ID');</script>";
                return false;
            }
            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }
            if(!empty($_POST['startTime']) and !empty($_POST['endTime'])){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
                $data .= " and ptime>={$startTime} and ptime<={$endTime}";
            }

            Common::loadModel('ServerModel');
            $serverList = ServerModel::getServList();
            foreach ($serverList as $k => $v) {
                if (empty($v)) {
                    continue;
                }

                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                $sevid = $SevidCfg1['sevid'];
                if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $sevid) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $sevid > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                if ( 0 < $serverid && $serverid != $SevidCfg1['sevid'] ) {
                    continue;
                }

                $db = Common::getDbBySevId($sevid);
                $sql = 'select * from `t_order` WHERE '.$data.' order by `ctime` desc';
                $searchRecords = $db->fetchArray($sql);

                if(!empty($searchRecords)){
                    foreach ($searchRecords as $key => $val){

                        $riqi = Game::is_ymd($val['ctime']);
                        $list[$riqi]["num"]++;
                        $list[$riqi]["money"] += $present;
                        $total++;
                        $money += $present;

                        if (!in_array($val["roleid"], $uids)) {

                            $people++;
                            $uids[] = $val["roleid"];
                        }
                    }
                }
            }
        }

        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
}