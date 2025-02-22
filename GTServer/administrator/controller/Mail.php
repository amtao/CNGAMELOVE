<?php
/**
 * 信息管理
 * Class Infomation
 */
class Mail
{
    public function index(){
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 不含物品邮件
     * */
    public function giveEmail()
    {
        set_time_limit(0);
        $uid = isset($_REQUEST['uid'])?$_REQUEST['uid']:NULL;
        $title = trim($_POST['title']);
        $content = trim($_POST['message']);
        $msg = "";
        if ($_POST['uids'] && !empty($_POST['uids'])) {
            $uids = explode(',', trim($_POST['uids']));
            $uid_arr = array();
            //循环发送
            foreach ($uids as $uid) {
                //检验uid合法性
                if (!is_numeric($uid)) {
                    continue;
                }
                
                if(empty($uid) || in_array($uid, $uid_arr)){
                    continue;
                }
                $uid_arr[] = $uid;
                
                $mailModel = Master::getMail($uid);
                $mailModel->sendMail($uid, $title, $content, 0, '');
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $title, 'content' => $content));
                echo '<span style="color: red;">'.$uid.'发送成功<br/></span>';
            }
            echo "<script>alert('邮件发送成功');</script>";
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
   /*
    * 含物品的邮件
    * */
    public function giveItemEmail()
    {
        set_time_limit(0);
        $items = Game::getCfg('item');
        $other_lang = include ROOT_DIR."/administrator/extend/cloher_lang.php";
        $content_name = $other_lang['user_cloher'];
        $clothe_sys = Game::getCfg('use_clothe');
        $user_blank = Game::getCfg('use_blank');
        $chenghao = Game::getCfg('chenghao');
        $hero_dress = Game::getCfg('hero_dress');
        $card = Game::getCfg('card');
        $baowu = Game::getCfg('baowu');
        $kind = array(1=>'(头饰)',2=>'(衣服)',3=>'(耳饰)',4=>'(背景)',5=>'(特效)',6=>'(灵宠)');
        foreach ($clothe_sys as $cid => $civ){
            if ($civ['unlock'] == 1){
                continue;
            }
            $items[] = array('id'=>$civ['id'],'name'=>$kind[$civ['part']].$civ['name'],'kind'=>95);
        }
        foreach ($user_blank as $bid => $biv){
            if ($biv['id'] == 1){
                continue;
            }
            $items[] = array('id'=>$biv['id'],'name'=>'(头像框)'.$biv['name'],'kind'=>94);
        }
        foreach ($chenghao as $did => $div){
            $items[] = array('id'=>$div['id'],'name'=>'(称号)'.$div['name'],'kind'=>10);
        }
        foreach ($hero_dress as $hid => $hiv){
            $items[] = array('id'=>$hiv['id'],'name'=>'(伙伴时装)'.$hiv['name'],'kind'=>111);
        }
        foreach ($card as $cid => $civ){
            $items[] = array('id'=>$civ['id'],'name'=>'(卡牌)'.$civ['name'],'kind'=>99);
        }
        foreach ($baowu as $cid => $civ){
            $items[] = array('id'=>$civ['id'],'name'=>'(奇珍)'.$civ['name'],'kind'=>202);
        }
        $title = trim($_POST['title']);
        $content = trim($_POST['message']);
        $itemsInfo = $_POST['items'];
        if(!empty($itemsInfo)){
            foreach ($itemsInfo as $itm){
                $item_arr = explode('-',$itm);
                $kind = $items[$item_arr[0]]['kind'] ? $items[$item_arr[0]]['kind'] : 1;
                $items_arr[] = array('id'=>$items[$item_arr[0]]['id'],'count'=>$item_arr[2],"kind" => $kind);
            }
        }
        $msg = "";
        if ($_POST['uids'] && !empty($_POST['uids'])) {
            $uids = explode(',', trim($_POST['uids']));
            //循环发送
            $uid_arr = array();
            foreach ($uids as $uid) {
                //检验uid合法性
                if (!is_numeric($uid)) {
                    continue;
                }
                
                if(empty($uid) || in_array($uid, $uid_arr)){
                    continue;
                }
                $uid_arr[] = $uid;
                $openid = Common::getOpenid($uid);
                if(empty($openid)){
                    continue;
                }
                $mailModel = Master::getMail($uid);
                if (!empty($items_arr)){
                    $mailModel->sendMail($uid, $title, $content, 1, $items_arr);
                }else{
                    $mailModel->sendMail($uid, $title, $content, 0, '');
                }
                $cache1 = Common::getCacheByUid($uid);
                $key = $uid.'_mail';
                $cache1->delete($key);
                echo '<span style="color: red;">'.$uid.'发送成功<br/></span>';
            }
            $data['user'] = $_SESSION['CURRENT_USER'];
            $data['title'] = $title;
            $data['uids'] = $_POST['uids'];
            $data['items'] = json_encode($items_arr);
            $data['content'] = $content;
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $data);
            echo "<script>alert('邮件发送成功');</script>";
        }

        $allItem = '';
        if(!empty($items)){
            $kid = 1;
            foreach($items as $cfg_info){
                $allItem .= 'id: '.$cfg_info['id'].' 名字 : '.$cfg_info['name'].'   ';
                if($kid %5 == 0){
                    $allItem .= "\n";
                }
                $kid ++;
            }
        }

        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 单服邮件
     * */
    public function serverEmailList() {
        $items = Game::getCfg('item');
        $guan = Game::getCfg('guan');
        $other_lang = include ROOT_DIR."/administrator/extend/cloher_lang.php";
        $content_name = $other_lang['user_cloher'];
        $clothe_sys = Game::getCfg('use_clothe');
        $user_blank = $other_lang['user_blank'];
        foreach ($clothe_sys as $cid => $civ){
            if ($civ['unlock'] == 0 || $civ['unlock'] == 1){
                continue;
            }
            $items[] = array('id'=>$civ['id'],'name'=>$content_name[$civ['id']],'kind'=>95);
        }
        foreach ($user_blank as $bid => $biv){
            $items[] = array('id'=>$biv['id'],'name'=>$biv['name'],'kind'=>$biv['kind']);
        }
        if ($_POST){
            if(empty($_POST['title']) || empty($_POST['message'])){
                echo "<script>alert('邮件标题和邮件内容不能为空!');</script>";
            }
        }
        if (!empty($_POST['title']) && !empty($_POST['message'])) {

            $_GET['sevid'] = trim($_POST['server']);
            $uids = trim($_POST['uids']);
            $title = trim($_POST['title']);
            $level = trim($_POST['level']);
            $registerTime = trim($_POST['registerTime']);
            $startTime = trim($_POST['startTime'])?$_POST['startTime']:date("Y-m-d H:i:s");
            $endTime = trim($_POST['endTime'])?$_POST['endTime']:date("Y-m-d 23:59:59");
            $content = trim($_POST['message']);
            $itemsInfo = $_POST['items'];
            $vipType = trim($_POST['vipType']);
            $vip = trim($_POST['vip']);
            if($vipType == 1){
                $vipData = $vip;
                if (!is_numeric($vipData)){
                    echo "<script>alert('vip格式错误!');</script>";
                    return false;
                }
            }elseif ($vipType == 2){
                $vipData = explode('-',$vip);
                if (empty($vipData[1])){
                    echo "<script>alert('vip格式错误!');</script>";
                    return false;
                }
            }elseif ($vipType == 3){
                $vipData = explode(',',$vip);
                if (empty($vipData[1])){
                    echo "<script>alert('vip格式错误!');</script>";
                    return false;
                }
            }
            if(!empty($itemsInfo)){
                foreach ($itemsInfo as $itm){
                    $item_arr = explode('-',$itm);
                    $kind = $items[$item_arr[0]]['kind'] ? $items[$item_arr[0]]['kind'] : 1;
                    $items_arr[] = array('id'=>$items[$item_arr[0]]['id'],'count'=>$item_arr[2],"kind" => $kind);
                }
            }
            Common::loadModel('ServerModel');
            $serverList = ServerModel::getServList();
            $isTrueServer = false;
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }

                if ($v['id'] == $_GET['sevid']) {
                    $isTrueServer = true;
                    break;
                }
            }

            if (!$isTrueServer){
                echo "<script>alert('区服不存在!');</script>";
                return false;
            }
            $SevidCfg1 = Common::getSevidCfg($_GET['sevid']);
            $cache = Common::getDftMem ();
            $key = self::createbs();
            $maildata = $cache->get('mai_send_content');
            if(!empty($maildata)){
                while ($maildata[$key]){
                    $key = self::createbs();
                }
            }
            $maildata[$key] = $content;
            $data = array(
                'title' => $title,
                'items' => empty($items_arr) ? 0 : $items_arr,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'level' => $level,
                'vipType' => $vipType,
                'vipData' => $vipData,
                'serverStart' => $_GET['sevid'],
                'serverEnd' => $_GET['sevid'],
                'registerTime' => $registerTime?$registerTime:0,
                'ctime' => time(),
            );
            $cache->set('mai_send_content', $maildata);
            $Sev31Model = Master::getSev31();
            $Sev31Model->add($key, $data);
            $dataInfo['user'] = $_SESSION['CURRENT_USER'];
            $dataInfo['title'] = $title;
            $dataInfo['uids'] = '单服邮件:'.$_GET['sevid'];
            $dataInfo['items'] = json_encode($data);
            $dataInfo['content'] = $content;
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $dataInfo);
            echo "<script>alert('邮件发送成功');</script>";
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 生成标识码
     *
     * */
    public function createbs($num=6){
        $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
        $charsLen = count($chars);
        shuffle($chars);
        $output = '';
        for ($i = 0; $i < $num; $i++) {
            $output .= $chars[mt_rand(0, $charsLen-1)];
        }
        return $output;
    }
    /*
     * 全服邮件
     * */
    public function allserverEmailList() {
        $items = Game::getCfg('item');
        $guan = Game::getCfg('guan');
        $other_lang = include ROOT_DIR."/administrator/extend/cloher_lang.php";
        $content_name = $other_lang['user_cloher'];
        $clothe_sys = Game::getCfg('use_clothe');
        $user_blank = $other_lang['user_blank'];
        $chenghao = Game::getCfg('chenghao');
        $hero_dress = Game::getCfg('hero_dress');
        $card = Game::getCfg('card');
        $baowu = Game::getCfg('baowu');
        foreach ($clothe_sys as $cid => $civ){
            if ($civ['unlock'] == 0 || $civ['unlock'] == 1){
                continue;
            }
            $items[] = array('id'=>$civ['id'],'name'=>$content_name[$civ['id']],'kind'=>95);
        }
        foreach ($user_blank as $bid => $biv){
            $items[] = array('id'=>$biv['id'],'name'=>$biv['name'],'kind'=>$biv['kind']);
        }
        foreach ($chenghao as $did => $div){
            $items[] = array('id'=>$div['id'],'name'=>'(称号)'.$div['name'],'kind'=>10);
        }
        foreach ($hero_dress as $hid => $hiv){
            $items[] = array('id'=>$hiv['id'],'name'=>'(伙伴时装)'.$hiv['name'],'kind'=>111);
        }
        foreach ($card as $cid => $civ){
            $items[] = array('id'=>$civ['id'],'name'=>'(卡牌)'.$civ['name'],'kind'=>99);
        }
        foreach ($baowu as $cid => $civ){
            $items[] = array('id'=>$civ['id'],'name'=>'(奇珍)'.$civ['name'],'kind'=>202);
        }
        Common::loadModel('ServerModel');
        Common::loadModel('OrderModel');
        $serverList = ServerModel::getServList();
        $platformList = OrderModel::get_platform();
        $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
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
        if ($_POST){
            if(empty($_POST['title']) || empty($_POST['message'])){
                echo "<script>alert('邮件标题和邮件内容不能为空!');</script>";
                return false;
            }
        }
        if (!empty($_POST['title']) && !empty($_POST['message']) ) {
            $serverid = trim($_GET['sevid']);
            $uids = trim($_POST['uids']);
            $title = trim($_POST['title']);
            $level = trim($_POST['level']);
            $link = trim($_POST['link']);
            $startTime = trim($_POST['startTime'])?$_POST['startTime']:date("Y-m-d H:i:s");
            $endTime = trim($_POST['endTime'])?$_POST['endTime']:date("Y-m-d 23:59:59");
            $content = trim($_POST['message']);
            $sendChannel = $_POST['channels'];
            $registerTime = trim($_POST['registerTime']);
            $itemsInfo = $_POST['items'];
            if(!empty($itemsInfo)){
                foreach ($itemsInfo as $itm){
                    $item_arr = explode('-',$itm);
                    $kind = $items[$item_arr[0]]['kind'] ? $items[$item_arr[0]]['kind'] : 1;
                    $items_arr[] = array('id'=>$items[$item_arr[0]]['id'],'count'=>$item_arr[2],"kind" => $kind);
                }
            }
            $vipType = trim($_POST['vipType']);
            $vip = trim($_POST['vip']);
            if($vipType == 1){
                $vipData = $vip;
                if (!is_numeric($vipData)){
                    echo "<script>alert('vip格式错误!');</script>";
                    return false;
                }
            }elseif ($vipType == 2){
                $vipData = explode('-',$vip);
                if (empty($vipData[1])){
                    echo "<script>alert('vip格式错误!');</script>";
                    return false;
                }
            }elseif ($vipType == 3){
                $vipData = explode(',',$vip);
                if (empty($vipData[1])){
                    echo "<script>alert('vip格式错误!');</script>";
                    return false;
                }
            }
            $serverId = explode('-',trim($_POST['server']));
            $start = $serverId[0];
            $end = $serverId[1];
            if ($start!="all" && empty($end)){
                echo "<script>alert('单服邮件请到本服邮件进行发放!');</script>";
                return false;
            }
            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
            $msg = '';
            $key = self::createbs();
            $he_array = array();
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                if ($start != "all"){
                    if ($v['id']<$start || $v['id']>$end){
                        echo '<span style="color: red">'.$v['id'].' 跳过</span></br>';
                        continue;
                    }
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                $openDays  = ServerModel::getOpenDays($v['id']);
                if ($openDays == 0){
                    continue;
                }
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                    continue;
                }
            
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                if(in_array($SevidCfg1['he'], $he_array)){
                    continue;
                }
                $he_array[] = $SevidCfg1['he'];
                $cache = Common::getDftMem();
                $maildata = $cache->get('mai_send_content');
                if(!empty($maildata)){
                    while ($maildata[$key]){
                        $key = self::createbs();
                    }
                }
                $maildata[$key] = $content;

                $data = array(
                    'title' => $title,
                    'items' => empty($items_arr) ? 0 : $items_arr,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'level' => $level,
                    'vipType' => $vipType,
                    'vipData' => $vipData,
                    'serverStart' => $start,
                    'serverEnd' => $end?$end:0,
                    'channels' => is_array($sendChannel)?$sendChannel:array(),
                    'registerTime' => $registerTime?$registerTime:0,
                    'ctime' => time(),
                    'is_all' => 1,
                    'link' => $link,
                );
                $cache->set('mai_send_content', $maildata);
                $Sev31Model = Master::getSev31();
                $Sev31Model->add($key, $data);
            }
            $dataInfo['user'] = $_SESSION['CURRENT_USER'];
            $dataInfo['title'] = $title;
            $dataInfo['uids'] = '全服邮件';
            $dataInfo['items'] = json_encode($data);
            $dataInfo['content'] = $content;
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $dataInfo);
            echo "<script>alert('邮件发送成功');</script>";
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /**
     * 邮件审核
     */
    public function giveItemEmailAuditing(){
        $items = Game::getCfg('item');
        $guan = Game::getCfg('guan');
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $uid = isset($_REQUEST['uid'])?$_REQUEST['uid']:NULL;
        $type = trim($_POST['type']);
        $title = trim($_POST['title']);
        $content = trim($_POST['message']);
        $itemsInfo = $_POST['items'];
        $registerTime = trim($_POST['registerTime']);
        if(!empty($itemsInfo)){
            foreach ($itemsInfo as $itm){
                $item_arr = explode('-',$itm);
                $kind = $items[$item_arr[0]]['kind'] ? $items[$item_arr[0]]['kind'] : 1;
                $items_arr[] = array('id'=>$item_arr[0],'count'=>$item_arr[2],"kind" => $kind);
            }
        }
        $level = $_POST['level'];
        $startTime = $_POST['startTime']?$_POST['startTime']:date("Y-m-d H:i:s");
        $endTime = $_POST['endTime']?$_POST['endTime']:date("Y-m-d 23:59:59");
        if ($_POST){
            if(empty($title) || empty($content)){
                echo "<script>alert('邮件标题和邮件内容不能为空!');</script>";
                return false;
            }
        }
        $vipType = trim($_POST['vipType']);
        $vip = trim($_POST['vip']);
        if($vipType == 1){
            $vipData = $vip;
            if (!is_numeric($vipData)){
                echo "<script>alert('vip格式错误!');</script>";
                return false;
            }
        }elseif ($vipType == 2){
            $vipData = explode('-',$vip);
            if (empty($vipData[1])){
                echo "<script>alert('vip格式错误!');</script>";
                return false;
            }
        }elseif ($vipType == 3){
            $vipData = explode(',',$vip);
            if (empty($vipData[1])){
                echo "<script>alert('vip格式错误!');</script>";
                return false;
            }
        }
        if (!empty($type)) {
            if ($type == 1){
                $data['type'] = $type;
                $data['title'] = $title;
                $data['uids'] = $_POST['uids'];
                $data['message'] = $content;
                $data['status'] = 0;
                $data['remarks'] = $_POST['remarks'];
                $data['user'] = $_SESSION['CURRENT_USER'];
                $data['time'] = time();
            }elseif ($type == 2){
                $data['type'] = $type;
                $data['title'] = $title;
                $data['uids'] = $_POST['uids'];
                $data['message'] = $content;
                $data['items'] = $items_arr;
                $data['status'] = 0;
                $data['remarks'] = $_POST['remarks'];
                $data['user'] = $_SESSION['CURRENT_USER'];
                $data['time'] = time();
            }elseif ($type == 3){
                $data['type'] = $type;
                $data['startTime'] = $startTime;
                $data['endTime'] = $endTime;
                $data['title'] = $title;
                $data['message'] = $content;
                $data['items'] = $items_arr;
                $data['level'] = $level;
                $data['status'] = 0;
                $data['remarks'] = $_POST['remarks'];
                $data['user'] = $_SESSION['CURRENT_USER'];
                $data['time'] = time();
                $data['vipType'] = $vipType;
                $data['vipData'] = $vipData;
                $data['registerTime'] = $registerTime;
            }elseif ($type == 4){
                $data['type'] = $type;
                $data['startTime'] = $startTime;
                $data['endTime'] = $endTime;
                $data['title'] = $title;
                $data['message'] = $content;
                $data['items'] = $items_arr;
                $data['level'] = $level;
                $data['status'] = 0;
                $data['remarks'] = $_POST['remarks'];
                $data['user'] = $_SESSION['CURRENT_USER'];
                $data['time'] = time();
                $data['vipType'] = $vipType;
                $data['vipData'] = $vipData;
                $data['server'] = $_POST['server'];
                $data['registerTime'] = $registerTime;
            }
            Common::loadVoComModel('ComVoComModel');
            $ComVoComModel = new ComVoComModel($this->key, true);
            $emailInfo = $ComVoComModel->getValue();
            $emailInfo[] = $data;
            $ComVoComModel->updateValue($emailInfo);
            echo "<script>alert('邮件提交成功!');</script>";
        }
        $y_serid = $_GET['sevid'];
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /**
     * 邮件审核
     */
    public function emailAuditing(){
        $y_serid = $_GET['sevid'];
        $items = Game::getCfg('item');
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel($this->key, true);
        $emailData = $ComVoComModel->getValue();
        $getType = $_GET['type']?$_GET['type']:1;
        $getStatus = $_GET['nowStatus']?$_GET['nowStatus']:0;
        $he_array = array();
        $emailKey = $_REQUEST['emailKey'];
        if ($emailKey==0 || !empty($emailKey)){
            if (!empty($emailData[$emailKey])){
                if (!empty($_REQUEST['status'])){
                    if ($_REQUEST['status'] == 1 && $emailData[$emailKey]['status'] != 1){  //本服邮件
                        if ($emailData[$emailKey]['type'] == 3){
                            $cache = Common::getDftMem ();
                            $key = self::createbs();
                            $maildata = $cache->get('mai_send_content');
                            if(!empty($maildata)){
                                while ($maildata[$key]){
                                    $key = self::createbs();
                                }
                            }
                            $maildata[$key] = $emailData[$emailKey]['message'];
                            $data = array(
                                'title' => $emailData[$emailKey]['title'],
                                'items' => $emailData[$emailKey]['items'],
                                'startTime' => $emailData[$emailKey]['startTime'],
                                'endTime' => $emailData[$emailKey]['endTime'],
                                'level' => $emailData[$emailKey]['level'],
                                'vipType' => $emailData[$emailKey]['vipType'],
                                'vipData' => $emailData[$emailKey]['vipData'],
                                'registerTime' => $emailData[$emailKey]['registerTime'],
                                'ctime' => time(),
                            );
                            $cache->set('mai_send_content', $maildata);
                            $Sev31Model = Master::getSev31();
                            $Sev31Model->add($key, $data);
                        }elseif($emailData[$emailKey]['type'] == 4){ //全服邮件
                            Common::loadModel('ServerModel');
                            $serverList = ServerModel::getServList();

                            $serverID = intval($_SERVER['argv'][1]);// 默认是全部区
                            $msg = '';
                            $key = self::createbs();
                            $serverInfo = explode('-', $emailData[$emailKey]['server']);
                            $start = $serverInfo[0];
                            $end = $serverInfo[1];
                            foreach ($serverList as $k => $v) {
                                if ( empty($v) ) {
                                    continue;
                                }
                                echo $v['id'] .$start .$end;
                                if ($emailData[$emailKey]['server'] != "all"){
                                    if ($v['id']<$start || $v['id']>$end){
                                        continue;
                                    }
                                }
                                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                                $openDays  = ServerModel::getOpenDays($v['id']);
                                if ($openDays == 0){
                                    continue;
                                }
                                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                                    continue;
                                }
                                if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
                                    continue;
                                }
                                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                                    continue;
                                }
                                if(in_array($SevidCfg1['he'], $he_array)){
                                    continue;
                                }
                                $he_array[] = $SevidCfg1['he'];
                                $cache = Common::getDftMem();
                                $maildata = $cache->get('mai_send_content');
                                if(!empty($maildata)){
                                    while ($maildata[$key]){
                                        $key = self::createbs();
                                    }
                                }
                                $maildata[$key] = $emailData[$emailKey]['message'];
                                $data = array(
                                    'title' => $emailData[$emailKey]['title'],
                                    'items' => $emailData[$emailKey]['items'],
                                    'startTime' => $emailData[$emailKey]['startTime'],
                                    'endTime' => $emailData[$emailKey]['endTime'],
                                    'level' => $emailData[$emailKey]['level'],
                                    'vipType' => $emailData[$emailKey]['vipType'],
                                    'vipData' => $emailData[$emailKey]['vipData'],
                                    'serverStart' => $start,
                                    'serverEnd' => $end?$end:0,
                                    'ctime' => time(),
                                    'registerTime' => $emailData[$emailKey]['registerTime'],
                                    'is_all' => 1,
                                );
                                $cache->set('mai_send_content', $maildata);
                                $Sev31Model = Master::getSev31();
                                $Sev31Model->add($key, $data);
                            }
                        }else {
                            $uids = explode(',', $emailData[$emailKey]['uids']);
                            $uid_arr = array();
                            //循环发送
                            foreach ($uids as $uid) {
                                //检验uid合法性
                                if (!is_numeric($uid)) {
                                    continue;
                                }

                                if(empty($uid) || in_array($uid, $uid_arr)){
                                    continue;
                                }
                                $uid_arr[] = $uid;

                                $mailModel = Master::getMail($uid);
                                if (!empty($emailData[$emailKey]['items'])) {
                                    $mailModel->sendMail($uid, $emailData[$emailKey]['title'], $emailData[$emailKey]['message'], 1, $emailData[$emailKey]['items']);
                                } else {
                                    $mailModel->sendMail($uid, $emailData[$emailKey]['title'], $emailData[$emailKey]['message'], 0,'');
                                }
                            }
                        }
                        echo '<p style="color: red">审核通过!</p>';
                        $emailData[$emailKey]['status'] = $_REQUEST['status'];
                        $ComVoComModel->updateValue($emailData);
                        //后台操作日志
                        Common::loadModel('AdminModel');
                        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('email' => $emailKey));
                    }elseif ($_REQUEST['status'] == 4 && $emailData[$emailKey]['status'] != 4){
                        unset($emailData[$emailKey]);
                        $ComVoComModel->updateValue($emailData);
                        //后台操作日志
                        Common::loadModel('AdminModel');
                        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('delEmail' => $emailKey));
                        echo '<p style="color: red">已删除!</p>';
                    }else{
                        $emailData[$emailKey]['status'] = $_REQUEST['status'];
                        $ComVoComModel->updateValue($emailData);
                        //后台操作日志
                        Common::loadModel('AdminModel');
                        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('changeEmail' => $emailKey));
                    }
                }
            }
        }
        if (!empty($emailData)){
            arsort($emailData);
        }
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function emailLog(){
        $cache = Common::getDftMem();
        $key = "email_log";
        $data = $cache->get($key);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 邮件列表
     */
    public function emailList(){
        $items = Game::getCfg('item');
        $guan = Game::getCfg('guan');

        Common::loadModel("ServerModel");
        $serverList = ServerModel::getServList();
        $serverid = 1;
        $data = array();

        $SevidCfg = Common::getSevidCfg($serverid);//子服ID
        $db = Common::getDftDb('flow');
        $sql = "SELECT * FROM `admin_log` WHERE `time` > 0 AND `model` = 'mail' AND `control`= 'allserverEmailList'  ORDER BY `time` DESC ";
        $mailList = $db->fetchArray($sql);

        if (!empty($mailList)) {
            foreach ($mailList as $key => $value) {
                $dataArr = json_decode($value["data"], true);
                $dataArr["items"] = json_decode($dataArr["items"], true);
                $dataArr["items"]["title"] = $dataArr["title"];
                $data[] = $dataArr["items"];
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 删除邮件
     * */
    public function delMail(){
        $items = Game::getCfg('item');
        $guan = Game::getCfg('guan');
        $Sev31Model = Master::getSev31();
        $data = $Sev31Model->info;
        if(!empty($_GET['delkey'])){
            $key = trim($_GET['delkey']);
            if(empty($data[$key])){
                echo '<p style="color: red">未找到该邮件</p>';
            }else{
                if($data[$key]['is_all'] == 1){
                    Common::loadModel('ServerModel');
                    $serverList = ServerModel::getServList();
                    $he_array = array();
                    foreach ($serverList as $k => $v) {
                        if ( empty($v) ) {
                            continue;
                        }
                        if($v['id'] == 999){
                            continue;
                        }
                        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                        if(in_array($SevidCfg1['he'], $he_array)){
                            continue;
                        }
                        $he_array[] = $SevidCfg1['he'];
                        $Sev31Model = Master::getSev31();
                        $Sev31Model->del($key);
                    }
                }else{
                    $Sev31Model->del($key);
                }
                unset($data[$key]);
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    protected $key = "emailAuditing_email";
}
