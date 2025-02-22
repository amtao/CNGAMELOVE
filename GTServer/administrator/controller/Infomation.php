<?php
/**
 * 信息管理
 * Class Infomation
 */
class Infomation
{
    /**
     * 主页
     * */
    public function index(){
        $SevidCfg = Common::getSevidCfg($_GET['sevid']);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    public function frontString(){
        $db = Common::getDbBySevId(999);
        $table = "`front_log`";
        $time = strtotime(date('Y-m-d H:i:s'))-1800;
        $oldSql = 'SELECT * FROM '.$table.' WHERE `time`>'.$time.' ORDER BY `id` DESC';
        $data = $db->fetchArray($oldSql);
        include TPL_DIR . str_replace ( 'controller', '', strtolower ( __CLASS__ ) ) . '/' . __FUNCTION__ . '.php';
    }
    public function cache(){
        $listKey = 'crontab:testServerList';
        $oldListKey = 'crontab:testServerListed';
        $commonCache = Common::getCacheBySevId(1);
        $data = $commonCache->get($listKey);
        if (!empty($_REQUEST['key'])){
            $info = $commonCache->get($oldListKey);
            $info[] = $data[$_REQUEST['key']];
            unset($data[$_REQUEST['key']]);
            $commonCache->set($listKey, $data);
            $commonCache->set($oldListKey, $info);
            echo "删除成功!";
        }
        include TPL_DIR . str_replace ( 'controller', '', strtolower ( __CLASS__ ) ) . '/' . __FUNCTION__ . '.php';
    }
    public function oldCache(){
        $oldListKey = 'crontab:testServerListed';
        $commonCache = Common::getCacheBySevId(1);
        $data = $commonCache->get($oldListKey);
        if (!empty($_REQUEST['key'])){
            unset($data[$_REQUEST['key']]);
            $commonCache->set($oldListKey, $data);
            echo "删除成功!";
        }
        include TPL_DIR . str_replace ( 'controller', '', strtolower ( __CLASS__ ) ) . '/' . __FUNCTION__ . '.php';
    }
    public function showCache() {
        include TPL_DIR . str_replace ( 'controller', '', strtolower ( __CLASS__ ) ) . '/' . __FUNCTION__ . '.php';
    }
    

    /**
     * reids
     */
    public function showRedis(){
        //查看redis缓存
        if (! empty ( $_POST ['rediskey'] ) && $_POST["submit"] == "查询") {
            $key = $_POST ['rediskey'];
            $serid = $_GET['sevid'];
            $redis = Common::getRedisBySevId($serid);
            $data  = $redis->zRevRange($key, 0, -1,true);
        }
        if (! empty ( $_POST ['rediskey'] ) && $_POST["submit"] == "删除") {
            $key = $_POST ['rediskey'];
            $serid = $_GET['sevid'];
            $redis = Common::getRedisBySevId($serid);
            $data  = $redis->zRevRange($key, 0, -1,true);
            foreach ($data as $dk => $dv){
                $redis->zDelete($key, $dk);
            }
            echo "<script>alert('删除成功!');</script>";
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $data);
        }
        //删除redis缓存单个数据
        if (! empty ( $_GET['redis']) && !empty ($_GET['key'])) {
            $key = $_GET['redis'];
            $serid = $_GET['sevid'];
            $redis = Common::getRedisBySevId($serid);
            $redis->zDelete($key, $_GET['key']);
            $data = $redis->zRevRange($_GET['redis'], 0, -1,true);
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('delete' => $key));
        }
        if(!empty($_POST['key']) && !empty($_POST['json_data'])){
            $serid = $_GET['sevid'];
            $redis = Common::getRedisBySevId($serid);
            $de_data = json_decode($_POST['json_data'], true);
            foreach ($de_data as $k => $val){
                $redis->zAdd(trim($_POST['key']), $val, trim($k));
            }
            $key = $_POST['key'];
            $data  =   $redis->zRevRange($_POST['key'], 0, -1,true);
            echo "<script>alert('提交成功!');</script>";
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $de_data);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function showMemcache(){
        $cache = Common::getMyMem ();
        //查看缓存
        if (! empty ( $_POST ['key'] ) && $_POST["submit"] == "查询") {
            $key = $_POST ['key'];
            $data = $cache->get ( $key );
            //$data = json_decode($data,true);
    
        }
        //修改缓存
        if (! empty ( $_GET ['key'] ) && (! empty ( $_GET ['k'] ) || ! empty ( $_GET ['v'] )) && $_GET["submit"] == "submit") {
            $key = trim ( $_GET ['key'] );
            $k = trim ( $_GET ['k'] );
            $v = trim ( $_GET ['v'] );
            $mccache = Common::getMyMem ();
            $data = $mccache->get ( $key );
            if(is_array($data))
            {
                $oldinfo = $data[$k];
                $data[$k] = $v;
            }else{
                $oldinfo = $data;
                $data = $v;
            }
            $data['updatetime'] = $_SERVER['REQUEST_TIME'];
            $mccache->set ( $key, $data );
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array( $key => $data));
            header ( "Location: ?mod={$_GET['mod']}&act={$_GET['act']}" );
        }
        //修改缓存2 json格式修改
        if (! empty ( $_POST['key'] ) && (! empty ( $_POST['jsontype'] ))) {
            $mccache = Common::getMyMem ();
            $oldinfo = $mccache->get ( $key );
            if(!get_magic_quotes_gpc()){
                $_POST['json_data'] = addslashes($_POST['json_data']);
            }
            $j_data = json_decode(stripslashes($_POST['json_data']),1);
            $key = $_POST['key'];
            $mccache->set ( $key, $j_data );
            $data = $mccache->get ( $key );
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array( $key => $data));
        }
        //删除缓存
        if (! empty ( $_POST ['key'] ) && $_POST["submit"] == "删除") {
            $key = $_POST ['key'];
            $key = trim ( $key );
            $cache = Common::getMyMem ();
            $oldinfo = $cache->get ( $key );
            $cache->delete ( $key );
            $msg = 'Delete success: ' . $key;
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array( 'delete' => $key));
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 聊天系统
     */
    public function chatSystem(){
        $guan = Game::getCfg('guan');
        $sevid = $_GET['sevid'];
        Common::loadModel('UserModel');
        $sev22Model = Master::getSev22();
        $sev35Model = Master::getSev35();
        $data = $sev35Model->info;
        $sev23Model = Master::getSev23();
        $sev26Model = Master::getSev26();
//        $sev27Model = Master::getSev27();
        $Redis9Model = Master::getRedis9();
        if (!empty($_REQUEST['delUid'])){
            if (in_array($_REQUEST['delUid'], $data)){
                $key = $_REQUEST['key'];
                unset($data[$key]);
                $sev35Model->info = $data;
                $sev35Model->save();
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('delGM' => $_REQUEST['delUid']));
            }
        }
        if (!empty($_POST['uids'])){
            if ($_POST['uids'] != 1){
                if (!is_numeric($_POST['uids'])){
                    echo "<script>alert('uid错误');</script>";
                }
                $severId = Game::get_sevid($_POST['uids']);
                if ($sevid != $severId){
                    echo "<script>alert('不可设置跨服账号!');</script>";
                }
            }
            if (is_numeric($_POST['uids']) && !in_array($_POST['uids'], $data)){
                if ($_POST['uids'] == 1){
                    $sev35Model->info = array() ;
                    $data = array() ;
                }else{
                    array_push($data, $_POST['uids']);
                    $sev35Model->info = $data ;
                }
                $sev35Model->save();
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('GM' => $_POST['uids']));
            }
        }
        if (!empty($_POST['uid']) && !empty($_POST['info'])){
            $severId = Game::get_sevid($_POST['uid']);
            if ($sevid != $sevid){
                echo "<script>alert('不可跨服聊天!');</script>";
            }
            if (!is_numeric($_POST['uid'])){
                echo "<script>alert('uid错误!');</script>";
            }else{
                $uid = $_POST['uid'];
                $info = $_POST['info'];
                /*$userModel = new UserModel($uid);
                if (!empty($userModel->info)){
                    echo "<script>alert('玩家不存在!');</script>";
                }else{*/
                    $sev22Model->add_msg($uid, $info);
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('GM' => $uid));
                /*}*/
            }
        }
        //禁言
        if ($_GET['type'] == 'banTalk' && !empty($_GET['banUid'])){
            $uid = $_GET['banUid'];
            $content = $_GET['content'];
            if(empty($sev23Model->info[$uid])){
                $status = 0;
                if($_GET['status'] == 1) $status=1;
                $sev23Model->add($uid,$status);
                if(!empty($content)){
                    $Act98Model = Master::getAct98($uid);
                    $msg = array(
                        'msg' => $content,
                        'count' => 1,
                        'ctime' => $_SERVER['REQUEST_TIME'],
                    );
                    $Act98Model->add($msg);
                }
                echo "<script>alert('禁言成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('banUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被禁言');</script>";
            }
        }

        //封号
        if ($_GET['type'] == 'closure' && !empty($_GET['closureUid'])){
            $uid = $_GET['closureUid'];
            if(empty($sev26Model->info[$uid])){
                $sev26Model->add($uid);

                $sev25Model = Master::getSev25();
                $sev25Model->delete_msg($uid);

                $Sev22Model = Master::getSev22();
                $Sev22Model->delete_msg($uid);

                $Sev6012Model = Master::getSev6012();
                $Sev6012Model->delete_msg($uid);

                $Sev6013Model = Master::getSev6013();
                $Sev6013Model->delete_msg($uid);
                echo "<script>alert('封号成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('closureUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被封号');</script>";
            }
        }

        //封设备
        if ($_GET['type'] == 'sb' && !empty($_GET['sbUid'])){
            $uid = $_GET['sbUid'];
            $sb_data = $Redis9Model->is_exist($uid);
            if(empty($sb_data)){
                $Redis9Model->add_sb($uid);
                echo "<script>alert('封设备成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('sbUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被封设备了');</script>";
            }
        }
        $list = $Redis9Model->getList();

        $chatData = array_reverse($sev22Model->info);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 聊天记录
     */
    public function chat(){
        $guan = Game::getCfg('guan');
        $sevid = $_GET['sevid'];
        Common::loadModel('UserModel');
        Common::loadModel('ServerModel');
        $serverlist = ServerModel::getServList();

        if ($_POST['startTime']){
            $startTime = $_POST['startTime'];
        }else{
            $startTime = date('Y-m-d 00:00:00');
        }
        if ($_POST['endTime']){
            $endTime = $_POST['endTime'];
        }else{
            $endTime = date('Y-m-d 23:59:59');
        }
        $where = '`time`>'.strtotime($startTime).' AND `time`<'.strtotime($endTime);
        if ($_POST['type']){
            $where.= ' AND `type`='.$_POST['type'];
            if ($_POST['type'] == 2){
                $flowDb =  Common::getComDb('flow');
            }
        }else{
            $where.= ' AND `type`=1';
        }
        if (!empty($_POST['toUid']) && !empty($_POST['uid'])){
            $uid = intval($_POST['uid']);
            $toUid = intval($_POST['toUid']);
            $where.= ' AND (`uid`='.$uid.' AND `other`='.$toUid.') OR (`uid`='.$toUid.' AND `other`='.$uid.')';
        }elseif($_POST['uid']){
            $uid = intval($_POST['uid']);
            $where.= ' AND `uid`='.$uid;
        }
        if ($_POST['gid']){
            $gid = intval($_POST['gid']);
            $where.= ' AND `other`='.$gid;
        }
        $sql = "SELECT * FROM `flow_chat` WHERE ".$where.' ORDER BY `time` DESC';
        $chatData = array();
        $sev23 = array();
        $sev26 = array();
        $sev27 = array();
        if ($_POST['server'] != 'all' && is_numeric($_POST['server'])){
            Common::getSevidCfg($_POST['server']);
            $flowDb = Common::getDbBySevId($_POST['server'], 'flow');
            $chatData = $flowDb->fetchArray($sql);
            $sev23Model = Master::getSev23();
            $sev26Model = Master::getSev26();
//            $sev27Model = Master::getSev27();
            $Redis9Model = Master::getRedis9();
            foreach ($sev23Model->info as $key23 =>$value23){
                $sev23[] = $key23;
            }
            foreach ($sev26Model->info as $key26 =>$value26){
                $sev26[] = $key26;
            }
//            foreach ($sev27Model->info as $key27 =>$value27){
//                $sev27[] = $key27;
//            }
            $sev27 = $Redis9Model->getList();

        }elseif (!is_numeric($_POST['server']) && !empty($_POST['server'])){
            $server = explode('-', $_POST['server']);
            foreach ($serverlist  as $key => $value){
                if ( empty($value) ) {
                    continue;
                }
                if ($_POST['server'] != 'all'){
                    if ($value['id']<$server[0] || $value['id']>$server[1]){
                        continue;
                    }
                }
                $SevidCfg1 = Common::getSevidCfg($value['id']);
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $sev23Model = Master::getSev23();
                $sev26Model = Master::getSev26();
//                $sev27Model = Master::getSev27();
                $Redis9Model = Master::getRedis9();
                foreach ($sev23Model->info as $key23 =>$value23){
                    $sev23[] = $key23;
                }
                foreach ($sev26Model->info as $key26 =>$value26){
                    $sev26[] = $key26;
                }
//                foreach ($sev27Model->info as $key27 =>$value27){
//                    $sev27[] = $key27;
//                }
                $sev27 = $Redis9Model->getList();
                $flowDb = Common::getDbBySevId($value['id'], 'flow');
                $result = $flowDb->fetchArray($sql);
                $chatData = array_merge($chatData, $result);
            }
        }else{
            $flowDb =  Common::getMyDb('flow');
            $chatData = $flowDb->fetchArray($sql);
            $sev23Model = Master::getSev23();
            $sev26Model = Master::getSev26();
//            $sev27Model = Master::getSev27();
            $Redis9Model = Master::getRedis9();
            foreach ($sev23Model->info as $key23 =>$value23){
                $sev23[] = $key23;
            }
            foreach ($sev26Model->info as $key26 =>$value26){
                $sev26[] = $key26;
            }
//            foreach ($sev27Model->info as $key27 =>$value27){
//                $sev27[] = $key27;
//            }
            $sev27 = $Redis9Model->getList();
        }


        
        //禁言
        if ($_GET['type'] == 'banTalk' && !empty($_GET['banUid'])){

            $uid = $_GET['banUid'];
            $sid = Game::get_sevid($uid);
            $SevidCfg1 = Common::getSevidCfg($sid);
            $content = $_GET['content'];
            if(empty($sev23Model->info[$uid])){
                $status = 0;
                if($_GET['status'] == 1) $status=1;
                $sev23Model->add($uid,$status);
                if(!empty($content)){
                    $Act98Model = Master::getAct98($uid);
                    $msg = array(
                        'msg' => $content,
                        'count' => 1,
                        'ctime' => $_SERVER['REQUEST_TIME'],
                    );
                    $Act98Model->add($msg);
                }
                echo "<script>alert('禁言成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('banUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被禁言');</script>";
            }
        }
        
        //封号
        if ($_GET['type'] == 'closure' && !empty($_GET['closureUid'])){
            $uid = $_GET['closureUid'];
            $sid = Game::get_sevid($uid);
            $SevidCfg1 = Common::getSevidCfg($sid);

            if(empty($sev26Model->info[$uid])){
                $sev26Model->add($uid);

                $sev25Model = Master::getSev25();
                $sev25Model->delete_msg($uid);

                $Sev22Model = Master::getSev22();
                $Sev22Model->delete_msg($uid);

                $Sev6012Model = Master::getSev6012();
                $Sev6012Model->delete_msg($uid);

                $Sev6013Model = Master::getSev6013();
                $Sev6013Model->delete_msg($uid);
                echo "<script>alert('封号成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('closureUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被封号');</script>";
            }
        }
       
        //封设备
        if ($_GET['type'] == 'sb' && !empty($_GET['sbUid'])){
            $uid = $_GET['sbUid'];
            $sid = Game::get_sevid($uid);
            $sb_data = $Redis9Model->is_exist($uid);
//            $sb_data = $sev27Model->isBandSb($uid);
            if(empty($sb_data)){
                $Redis9Model->add_sb($uid);
//                $sev27Model->add($uid);
                echo "<script>alert('封设备成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('sbUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被封设备了');</script>";
            }
        }
        $SevidCfg1 = Common::getSevidCfg($sevid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 敏感字库
     */
    public function sensitive(){
        $sev28Model = Master::getSev28();
        $data = $sev28Model->info;
        if (!empty($_POST['sensitive'])){
            if (!in_array(trim($_POST['sensitive']),$data)){
                $sev28Model->add(trim($_POST['sensitive']));
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('sensitive' => $_POST['sensitive']));
            }else{
                echo '已存在';
            }
        }
        if (!empty($_GET['key']) || $_GET['key'] == 0){
            unset($sev28Model->info[$_GET['key']]);
            $sev28Model->save();
        }
        $data = $sev28Model->info;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

      /**
     * 敏感字库
     */
    public function autojinyanword(){
        $sev19Model = Master::getSev19();
        $data = $sev19Model->info['words'];
        if (!empty($_POST['sensitive']) && !empty($_POST['percentage'])){
            if (!in_array(trim($_POST['sensitive']),$data)){
                $sev19Model->add(trim($_POST['sensitive']),$_POST['percentage'] );
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('autojinyanword' => $_POST['sensitive'],'percentage' => $_POST['percentage']));
            }else{
                echo '已存在';
            }
        }
        if (!empty($_GET['key']) || $_GET['key'] == 0){
            $sev19Model->remove($_GET['key']);
            $sev19Model->save();
        }
        if (!empty($_POST['time']) && $sev19Model->info['time'] != $_POST['time'] ){
            $sev19Model->info['time'] =$_POST['time'];
            $sev19Model->save();
        }
        $data = $sev19Model->info['words'];
        $time = $sev19Model->info['time'];
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 禁言封号
     * */
    public function transUser() {
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 禁言列表
     * */
    public function jinyan() {
        $sev23Model = Master::getSev23();
        //解禁言
        if ($_GET['type'] == 'jin' && !empty($_GET['banUid'])){
            $uid = $_GET['banUid'];
            if(!empty($sev23Model->info[$uid])){
                $sev23Model->remove($uid);
                echo "<script>alert('解禁成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('banUid' => $uid));
            }else{
                echo "<script>alert('已被解禁');</script>";
            }
        }
        $result = $sev23Model->info;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /*
     * 跨服禁言列表
     * */
    public function jinyankua() {
        $sev39Model = Master::getSev39();
        //解禁言
        if ($_GET['type'] == 'jin' && !empty($_GET['banUid'])){
            $uid = $_GET['banUid'];
            if(!empty($sev39Model->info[$uid])){
                $sev39Model->remove($uid);
                echo "<script>alert('解禁成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('banUid' => $uid));
            }else{
                echo "<script>alert('已被解禁');</script>";
            }
        }
        $result = $sev39Model->info;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /*
     * 封号列表
     * */
    public function fenghao() {
    
        $sev26Model = Master::getSev26();
        //解封号
        if ($_GET['type'] == 'jiefeng' && !empty($_GET['closureUid'])){
            $uid = $_GET['closureUid'];
            if(!empty($sev26Model->info[$uid])){
                
                $sev26Model->remove($uid);
                echo "<script>alert('解封成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('closureUid' => $uid));
            }else{
                echo "<script>alert('已被解封');</script>";
            }
        }
        $result = $sev26Model->info;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /*
     * 封设备列表
     * */
    public function fengsb() {
        $Redis9Model = Master::getRedis9();
        //解封设备
        if ($_GET['type'] == 'jiefeng' && !empty($_GET['sbOpen'])){
            $ustr = $_GET['sbOpen'];
            $sb_info = $Redis9Model->del_sb($ustr);
            if($sb_info){
                echo "<script>alert('解设备成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('sb' => $ustr));
            }else{
                echo "<script>alert('已被解设备');</script>";
            }
        }
        $result = $Redis9Model->getList();
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    
    //聊天记录-----start-----
    public function chatkua(){
        $guan = Game::getCfg('guan');
        $sevid = $_GET['sevid'];
        Common::loadModel('UserModel');
        $sev25Model = Master::getSev25();
        $chatData = array_reverse($sev25Model->info);
        $sev39Model = Master::getSev39();
        $sev26Model = Master::getSev26();
//        $sev27Model = Master::getSev27();
        $Redis9Model = Master::getRedis9();
        $chaKey = $sev25Model->getKey();
        $msgKey = $sev25Model->getMsgKey();
        //封号
        if ($_GET['type'] == 'closure' && !empty($_GET['closureUid'])){
            $uid = $_GET['closureUid'];
            if(empty($sev26Model->info[$uid])){
                $sev26Model->add($uid);
                $Sev22Model = Master::getSev22();
                $Sev22Model->delete_msg($uid);
                $sev25Model = Master::getSev25();
                $sev25Model->delete_msg($uid);

                $Sev6012Model = Master::getSev6012();
                $Sev6012Model->delete_msg($uid);

                $Sev6013Model = Master::getSev6013();
                $Sev6013Model->delete_msg($uid);

                echo "<script>alert('封号成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_GET['type'] => $_GET['closureUid']));
            }else{
                echo "<script>alert('该用户已经被封号');</script>";
            }
        }
         
        //封设备
        if ($_GET['type'] == 'sb' && !empty($_GET['sbUid'])){
            $uid = $_GET['sbUid'];
//            $sb_data = $sev27Model->isBandSb($uid);
            $sb_data = $Redis9Model->is_exist($uid);
            if(empty($sb_data)){
//                $sev27Model->add($uid);
                $Redis9Model->add_sb($uid);
                echo "<script>alert('封设备成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_GET['type'] => $_GET['sbUid']));
            }else{
                echo "<script>alert('该用户已经被封设备了');</script>";
            }
        }

        if ($_POST['startTime']){
            $startTime = $_POST['startTime'];
        }
        if ($_POST['endTime']){
            $endTime = $_POST['endTime'];
        }
        
        //禁言
        if ($_GET['type'] == 'banTalk' && !empty($_GET['banUid'])){
            $uid = $_GET['banUid'];
            $content = $_GET['content'];
            if(empty($sev39Model->info[$uid])){
                $status = 0;
                if($_GET['status'] == 1) $status=1;
                $sev39Model->add($uid,$status);
                if(!empty($content)){
                    $Act98Model = Master::getAct98($uid);
                    $msg = array(
                        'msg' => $content,
                        'count' => 1,
                        'ctime' => $_SERVER['REQUEST_TIME'],
                    );
                    $Act98Model->add_kf_msg($msg);
                }
                echo "<script>alert('禁言成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_GET['type'] => $_GET['banUid']));
            }else{
                echo "<script>alert('该用户已经被禁言');</script>";
            }
        }
        
        $data = $chatData;
        foreach ($data as $key => $value){
            if ($value['uid']){
                $userModel = new UserModel($value['uid']);
                $data[$key]['name'] = $userModel->info['name'];
                if ($startTime && strtotime($startTime)>$value['time']){
                    unset($data[$key]);
                }
                if ($endTime && strtotime($endTime)<$value['time']){
                    unset($data[$key]);
                }
            }
        }
        $SevidCfg = Common::getSevidCfg($sevid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 各平台数据统计
     */
    public function dataStatistics(){
        $begindt = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-86400);
        $enddt = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-1);
        $startTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-86400);
        $endTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d'))-1);
        if (!empty($_REQUEST['startTime']) &&  !empty($_REQUEST['endTime'])){
            $begindt = $_REQUEST['startTime'];
            $endTime =  $_REQUEST['endTime'];
            $startTime = $_REQUEST['startTime'];
            $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['endTime']));
            if ($_REQUEST['startTime'] == $_REQUEST['endTime'] ){
                $begindt = date('Y-m-d 00:00:00', strtotime($_REQUEST['startTime']));
                $enddt = date('Y-m-d 23:59:59', strtotime($_REQUEST['startTime']));
                $startTime = date('Y-m-d 00:00:00', strtotime($_REQUEST['startTime']));
                $endTime = date('Y-m-d 23:59:59', strtotime($_REQUEST['startTime']));
            }
            if (strtotime($enddt)-strtotime($begindt)>86400*2){
                $display = 1;
            }
        }


        $server = include (ROOT_DIR.'/administrator/extend/server.php');
        $type = 1;
        if ($_REQUEST['sort'] == "按注册人数倒序" || $_REQUEST['sort'] == 1){
            $type = 1;
        }elseif ($_REQUEST['sort'] == "按充值倒序" || $_REQUEST['sort'] == 2){
            $type = 2;
        }elseif ($_REQUEST['sort'] == "按登录倒序" || $_REQUEST['sort'] == 3){
            $type = 3;
        }
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_all_platform();
        $platformClassify = OrderModel::get_platform_classify();
        $data = array();
        $datas = array();
        $totalRegister = 0;
        $totalMoney = 0;
        $totalLogin = 0;
        $memcache = Common::getMyMem();
        $key = 'dataStatistics_admin';
        $info = $memcache->get($key);
        if ($info['startTime'] == $startTime && $info['endTime'] == $endTime){
            $datas = $info['data'];
            foreach ($datas as $dk =>$dv){
                if (!empty($_REQUEST['platform']) && $_REQUEST['platform'] != 'all'){
                    if ($platformClassify[$dk] != $_REQUEST['platform']){
                        continue;
                    }
                }
                if ($type == 1) {
                    $data[$dk]['register'] += $dv['register'];
                    $data[$dk]['total'] += $dv['total'];
                    $data[$dk]['totalLogin'] += $dv['totalLogin'];
                }elseif ($type == 2) {
                    $data[$dk]['total'] += $dv['total'];
                    $data[$dk]['register'] += $dv['register'];
                    $data[$dk]['totalLogin'] += $dv['totalLogin'];
                }elseif($type == 3) {
                    $data[$dk]['totalLogin'] += $dv['totalLogin'];
                    $data[$dk]['total'] += $dv['total'];
                    $data[$dk]['register'] += $dv['register'];
                }
                $totalRegister += $dv['register'];
                $totalMoney += $dv['total'];
                $totalLogin += $dv['totalLogin'];
            }
        }else{
            foreach ($server as $value){
                $url = $value.'/api/dataStatistics.php?begindt='.$begindt.'&enddt='.$enddt;
                $result = $this->curl_https($url);
                if (!empty($result)){
                    $dataInfo = json_decode($result, true);
                    if (is_array($dataInfo)){
                        foreach ($dataInfo as $dk => $dv){
                            $datas[$dk]['register'] += $dv['register'];
                            $datas[$dk]['total'] += $dv['total'];
                            $datas[$dk]['totalLogin'] += $dv['totalLogin'];
                            if (!empty($_REQUEST['platform']) && $_REQUEST['platform'] != 'all'){
                                if ($platformClassify[$dk] != $_REQUEST['platform']){
                                    continue;
                                }
                            }
                            if ($type == 1) {
                                $data[$dk]['register'] += $dv['register'];
                                $data[$dk]['total'] += $dv['total'];
                                $data[$dk]['totalLogin'] += $dv['totalLogin'];
                            }elseif ($type == 2) {
                                $data[$dk]['total'] += $dv['total'];
                                $data[$dk]['register'] += $dv['register'];
                                $data[$dk]['totalLogin'] += $dv['totalLogin'];
                            }elseif($type == 3) {
                                $data[$dk]['totalLogin'] += $dv['totalLogin'];
                                $data[$dk]['total'] += $dv['total'];
                                $data[$dk]['register'] += $dv['register'];
                            }
                            $totalRegister += $dv['register'];
                            $totalMoney += $dv['total'];
                            $totalLogin += $dv['totalLogin'];
                        }
                    }
                }
            }
            if (date('Y-m-d', strtotime($endTime)) == date('Y-m-d')){
                $memData['endTime'] = date('Y-m-d H:i:s');
            }else{
                $memData['endTime'] = $endTime;
            }
            $memData['startTime'] = $startTime;
            $memData['data'] = $datas;
            $memcache->set($key, $memData);
        }
        arsort($data);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    
    public function curl_https($url, $data=array(), $header=array(), $timeout=180){
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
     * 封号/解封
     */
    public function closure(){
        $guan = Game::getCfg('guan');
        $sev23Model = Master::getSev23();
        $sev26Model = Master::getSev26();
        $Redis9Model = Master::getRedis9();
//        $sev27Model = Master::getSev27();
        Common::loadModel('UserModel');
        //禁言
        if (!empty($_POST['banUid'])){
            $uid = $_POST['banUid'];
            if(empty($sev23Model->info[$uid])){
                $status = 1;
                $sev23Model->add($uid,$status);
                echo "<script>alert('禁言成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('banUid' => $uid));
            }else{
                echo "<script>alert('该用户已经被禁言');</script>";
            }
        }elseif(!empty($_REQUEST['jieUid'])){
            $uid = $_REQUEST['jieUid'];
            if(!empty($sev23Model->info[$uid])){
                $sev23Model->remove($uid);
                echo "<script>alert('解禁成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('jieUid' => $uid));
            }else{
                echo "<script>alert('已被解禁');</script>";
            }
        }
        //封设备
        if (!empty($_POST['sbUid'])){
            $uid = $_POST['sbUid'];
            $sb_data = $Redis9Model->is_exist($uid);
//            $sb_data = $sev27Model->isBandSb($uid);
            if(empty($sb_data)){
                $Redis9Model->add_sb($uid);
//                $sev27Model->add($uid);
                echo "<script>alert('封设备成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array("sbUid" => $_GET['sbUid']));
            }else{
                echo "<script>alert('该用户已经被封设备了');</script>";
            }
        }elseif ($_GET['type'] == 'jiefeng' && !empty($_GET['sbOpen'])){
            $ustr = $_GET['sbOpen'];
            $sb_data = $Redis9Model->del_sb($ustr);
            if($sb_data){
//                $sev27Model->remove($ustr);
                echo "<script>alert('解设备成功');</script>";
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('sb' => $ustr));
            }else{
                echo "<script>alert('已被解设备');</script>";
            }
        }
        if (!empty($_POST['uids'])){
            $uids = trim($_POST['uids']);
            $uidArray = explode(',', $uids);
            if (is_array($uidArray)){
                foreach ($uidArray as $key => $value){
                    if (is_numeric($value)){
                        //跨服封号
                        $sid = Game::get_sevid($value);
                        $SevidCfg1 = Common::getSevidCfg($sid);
                        $sev26Model_sub = Master::getSev26();
                        $sev26Model_sub->add($value);
                        //还原主服
                        Common::getSevidCfg($_GET['sevid']);
                        $bool = $sev26Model->add($value);
                        //后台操作日志
                        Common::loadModel('AdminModel');
                        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('closure' => $value));
                        if($bool == false){
                            echo "<span style='color: red;'>".$value."封号失败,不能跨服封号</span>";
                        }else{
                            echo "<span style='color: red;'>".$value."封号成功</span>";
                        }
                    }
                }
            }
        }elseif (!empty($_REQUEST['closureUid']) && is_numeric($_REQUEST['closureUid'])){
            $closureUid = intval($_REQUEST['closureUid']);
            $sid = Game::get_sevid($closureUid);
            $SevidCfg1 = Common::getSevidCfg($sid);
            $sev26Model_sub = Master::getSev26();
            $sev26Model_sub->remove($closureUid);
            //还原主服
            Common::getSevidCfg($_GET['sevid']);
            if(!empty($sev26Model->info[$closureUid])){
                $sev26Model->remove($closureUid);
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('remove' => $closureUid));
                echo "<script>alert('解封成功');</script>";
            }else{
                echo "<script>alert('已被解封');</script>";
            }
        }
        $sb_list = $Redis9Model->getList();
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 任务流失率
     */
    public function taskDrain(){
        Common::loadModel("ServerModel");
        $serverlist = ServerModel::getServList();
        $sevid = $_GET['sevid'];
        $time = time() - 86400;
        $task = array();
        $total = 0;
        $task_cfg = Game::getcfg('task_main');
        if (!empty($_POST['server']) && $_POST['server'] != 'all'){
            $sevid = $_POST['server'];
        }
        if ($_POST['server'] == 'all'){
            foreach ($serverlist  as $key => $value){
                if ( empty($value) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($value['id']);
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($value['id']);
                $table_div = Common::get_table_div();
                for ($i = 0 ; $i < $table_div ; $i++){
                    unset($table, $result, $uid);
                    $table = 'user_'.Common::computeTableId($i);
                    $sql = "SELECT `uid` FROM ".$table." WHERE `lastlogin`<".$time;
                    $result = $db->fetchArray($sql);
                    if (!empty($result)){
                        foreach ($result as $k => $v){
                            $uid[] = $v['uid'];
                        }
                    }
                    unset($table, $result);
                    if (is_array($uid) && !empty($uid)){
                        $uids = implode(',', $uid);
                        $table = 'act_'.Common::computeTableId($i);
                        $sql = "SELECT `tjson` FROM ".$table." WHERE `actid`=39 AND `uid` IN (".$uids.");";
                        $result = $db->fetchArray($sql);
                        foreach ($result as $k => $v){
                            if (!empty($v['tjson'])){
                                $info = json_decode($v['tjson'], true);
                                if ($task[$info['data']['id']]){
                                    $task[$info['data']['id']] = $task[$info['data']['id']] + 1;
                                }else{
                                    $task[$info['data']['id']] = 1;
                                }
                                $total += 1;
                            }
                        }
                    }

                }
            }
        }else{
            $db = Common::getDbBySevId($sevid);
            $table_div = Common::get_table_div();
            for ($i = 0 ; $i < $table_div ; $i++){
                unset($table, $result);
                $table = 'user_'.Common::computeTableId($i);
                $sql = "SELECT `uid` FROM ".$table." WHERE `lastlogin`<".$time;
                $result = $db->fetchArray($sql);
                if (!empty($result)) {
                    foreach ($result as $k => $v) {
                        $uid[] = $v['uid'];
                    }
                }
                unset($table, $result);
                if (is_array($uid) && !empty($uid)) {
                    $uids = implode(',', $uid);
                    $table = 'act_' . Common::computeTableId($i);
                    $sql = "SELECT `tjson` FROM " . $table . " WHERE `actid`=39 AND `uid` IN (" . $uids . ");";
                    $result = $db->fetchArray($sql);
                    foreach ($result as $k => $v) {
                        if (!empty($v['tjson'])) {
                            $info = json_decode($v['tjson'], true);
                            if ($task[$info['data']['id']]) {
                                $task[$info['data']['id']] = $task[$info['data']['id']] + 1;
                            } else {
                                $task[$info['data']['id']] = 1;
                            }
                            $total += 1;
                        }
                    }
                }
            }
        }
        ksort($task);
        $SevidCfg = Common::getSevidCfg($sevid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

     /**
     * 注册任务进度
     */
    public function registerTask(){
        Common::loadModel("ServerModel");
        $serverlist = ServerModel::getServList();
        $sevid = $_GET['sevid'];
        $time = time() - 86400*2;

        if ($_POST['startTime']){
            $startTime = $_POST['startTime'];
        }else{
            $startTime = date('Y-m-d 00:00:00');
        }
        if ($_POST['endTime']){
            $endTime = $_POST['endTime'];
        }else{
            $endTime = date('Y-m-d 23:59:59');
        }

        if ($_POST['loginstartTime']){
            $loginstartTime = $_POST['loginstartTime'];
        }else{
            $loginstartTime = date('Y-m-d 00:00:00');
        }
        if ($_POST['loginEndTime']){
            $loginEndTime = $_POST['loginEndTime'];
        }else{
            $loginEndTime = date('Y-m-d 23:59:59');
        }
        //2 注册时间
        $stype  = $_POST['stype'];
        $luid =array();
        if($stype  ==2){
            $dbmain = Common::getDbBySevId(1);
            $sqllogin = "select distinct `uid`  from `login_log`,`register` where `register`.`openid`=`login_log`.`openid` AND  `login_time`>=".strtotime($loginstartTime)." and `login_time`<=".strtotime($loginEndTime);
            $loginLogRes = $dbmain->fetchArray($sqllogin);

            foreach ($loginLogRes as $k => $v){
                $luid[] =  $v["uid"];
            }
     
        }
       

        $task = array();
        $total = 0;
        $task_cfg = Game::getcfg('task_main');
        if (!empty($_POST['server']) && $_POST['server'] != 'all'){
            $sevid = $_POST['server'];
        }
        if ($_POST['server'] == 'all'){
            foreach ($serverlist  as $key => $value){
                if ( empty($value) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($value['id']);
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($value['id']);
                $table_div = Common::get_table_div();
               

                for ($i = 0 ; $i < $table_div ; $i++){
                    unset($table, $result, $uid);
                    $table = 'user_'.Common::computeTableId($i);
                   // $sql = "SELECT `uid` FROM ".$table." WHERE `lastlogin`<".$time;
                   $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".strtotime($startTime)." AND `regtime`<". strtotime($endTime);
                  // $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".$start1." AND `regtime`<". $end1 ." AND `lastlogin`>". $loginstart." AND `lastlogin`<". $loginend;
                    $result = $db->fetchArray($sql);
                    if (!empty($result)){
                        foreach ($result as $k => $v){
                            if($stype==2 &&  !in_array($v['uid'], $luid)){
                                continue;
                            }
                            $uid[] = $v['uid'];
                        }
                    }
                    unset($table, $result);
                    if (is_array($uid) && !empty($uid)){
                        $uids = implode(',', $uid);
                        $table = 'act_'.Common::computeTableId($i);
                        $sql = "SELECT `tjson` FROM ".$table." WHERE `actid`=39 AND `uid` IN (".$uids.");";
                        $result = $db->fetchArray($sql);
                        foreach ($result as $k => $v){
                            if (!empty($v['tjson'])){
                                $info = json_decode($v['tjson'], true);
                                if ($task[$info['data']['id']]){
                                    $task[$info['data']['id']] = $task[$info['data']['id']] + 1;
                                }else{
                                    $task[$info['data']['id']] = 1;
                                }
                                $total += 1;
                            }
                        }
                    }

                }
            }
        }else{
            $db = Common::getDbBySevId($sevid);
            $table_div = Common::get_table_div();
        

            for ($i = 0 ; $i < $table_div ; $i++){
                unset($table, $result);
                $table = 'user_'.Common::computeTableId($i);
               // $sql = "SELECT `uid` FROM ".$table." WHERE `lastlogin`<".$time;
              $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".strtotime($startTime)." AND `regtime`<". strtotime($endTime);
              //  $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".$start1." AND `regtime`<". $end1 ." AND `lastlogin`>". $loginstart." AND `lastlogin`<". $loginend;
            
                $result = $db->fetchArray($sql);
                if (!empty($result)) {
                    foreach ($result as $k => $v) {
                        if($stype==2 &&  !in_array($v['uid'], $luid)){
                            continue;
                        }
                        $uid[] = $v['uid'];
                    }
                }
                unset($table, $result);
                if (is_array($uid) && !empty($uid)) {
                    $uids = implode(',', $uid);
                    $table = 'act_' . Common::computeTableId($i);
                    $sql = "SELECT `tjson` FROM " . $table . " WHERE `actid`=39 AND `uid` IN (" . $uids . ");";
                    $result = $db->fetchArray($sql);
                    foreach ($result as $k => $v) {
                        if (!empty($v['tjson'])) {
                            $info = json_decode($v['tjson'], true);
                            if ($task[$info['data']['id']]) {
                                $task[$info['data']['id']] = $task[$info['data']['id']] + 1;
                            } else {
                                $task[$info['data']['id']] = 1;
                            }
                            $total += 1;
                        }
                    }
                }
            }
        }
        ksort($task);
        $SevidCfg = Common::getSevidCfg($sevid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 注册章节进度
     */
    public function registerStory(){
        Common::loadModel("ServerModel");
        $serverlist = ServerModel::getServList();
        $sevid = $_GET['sevid'];
        $time = time() - 86400*2;

        if ($_POST['startTime']){
            $startTime = $_POST['startTime'];
        }else{
            $startTime = date('Y-m-d 00:00:00');
        }
        if ($_POST['endTime']){
            $endTime = $_POST['endTime'];
        }else{
            $endTime = date('Y-m-d 23:59:59');
        }

        if ($_POST['loginstartTime']){
            $loginstartTime = $_POST['loginstartTime'];
        }else{
            $loginstartTime = date('Y-m-d 00:00:00');
        }
        if ($_POST['loginEndTime']){
            $loginEndTime = $_POST['loginEndTime'];
        }else{
            $loginEndTime = date('Y-m-d 23:59:59');
        }
        //2 注册时间
        $stype  = $_POST['stype'];
        $luid =array();
        if($stype  ==2){
            $dbmain = Common::getDbBySevId(1);
            $sqllogin = "select distinct `uid`  from `login_log`,`register` where `register`.`openid`=`login_log`.`openid` AND  `login_time`>=".strtotime($loginstartTime)." and `login_time`<=".strtotime($loginEndTime);
            $loginLogRes = $dbmain->fetchArray($sqllogin);

            foreach ($loginLogRes as $k => $v){
                $luid[] =  $v["uid"];
            }
     
        }
       

        $task = array();
        $total = 0;
        //$task_cfg = Game::getcfg('task_main');
        if (!empty($_POST['server']) && $_POST['server'] != 'all'){
            $sevid = $_POST['server'];
        }
        if ($_POST['server'] == 'all'){
            foreach ($serverlist  as $key => $value){
                if ( empty($value) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($value['id']);
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($value['id']);
                $table_div = Common::get_table_div();
               

                for ($i = 0 ; $i < $table_div ; $i++){
                    unset($table, $result, $uid);
                    $table = 'user_'.Common::computeTableId($i);
                   // $sql = "SELECT `uid` FROM ".$table." WHERE `lastlogin`<".$time;
                   $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".strtotime($startTime)." AND `regtime`<". strtotime($endTime);
                  // $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".$start1." AND `regtime`<". $end1 ." AND `lastlogin`>". $loginstart." AND `lastlogin`<". $loginend;
                    $result = $db->fetchArray($sql);
                    if (!empty($result)){
                        foreach ($result as $k => $v){
                            if($stype==2 &&  !in_array($v['uid'], $luid)){
                                continue;
                            }
                            $uid[] = $v['uid'];
                        }
                    }
                    unset($table, $result);
                    if (is_array($uid) && !empty($uid)){
                        $uids = implode(',', $uid);
                        $table = 'user_'.Common::computeTableId($i);
                        $sql = "SELECT `smap` FROM ".$table." WHERE  `uid` IN (".$uids.");";
                        $result = $db->fetchArray($sql);
                        foreach ($result as $k => $v){
                       //     if (!empty($v['tjson'])){
                       //         $info = json_decode($v['tjson'], true);
                                if ($task[$v['smap']]){
                                    $task[$v['smap']] = $task[$v['smap']] + 1;
                                }else{
                                    $task[$v['smap']] = 1;
                                }
                                $total += 1;
                        //    }
                        }
                    }

                }
            }
        }else{
            $db = Common::getDbBySevId($sevid);
            $table_div = Common::get_table_div();
        

            for ($i = 0 ; $i < $table_div ; $i++){
                unset($table, $result);
                $table = 'user_'.Common::computeTableId($i);
               // $sql = "SELECT `uid` FROM ".$table." WHERE `lastlogin`<".$time;
              $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".strtotime($startTime)." AND `regtime`<". strtotime($endTime);
              //  $sql = "SELECT `uid` FROM ".$table." WHERE `regtime`>".$start1." AND `regtime`<". $end1 ." AND `lastlogin`>". $loginstart." AND `lastlogin`<". $loginend;
            
                $result = $db->fetchArray($sql);
                if (!empty($result)) {
                    foreach ($result as $k => $v) {
                        if($stype==2 &&  !in_array($v['uid'], $luid)){
                            continue;
                        }
                        $uid[] = $v['uid'];
                    }
                }
                unset($table, $result);
                if (is_array($uid) && !empty($uid)) {
                    $uids = implode(',', $uid);
                    $table = 'user_'.Common::computeTableId($i);
                        $sql = "SELECT `smap` FROM ".$table." WHERE  `uid` IN (".$uids.");";
                        $result = $db->fetchArray($sql);
                        foreach ($result as $k => $v){
                       //     if (!empty($v['tjson'])){
                       //         $info = json_decode($v['tjson'], true);
                                if ($task[$v['smap']]){
                                    $task[$v['smap']] = $task[$v['smap']] + 1;
                                }else{
                                    $task[$v['smap']] = 1;
                                }
                                $total += 1;
                        //    }
                        }
                }
            }
        }
        ksort($task);
        $SevidCfg = Common::getSevidCfg($sevid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 付费注册间隔时间分布
     */
    public function registerToPay(){
        $data = array();
        $dataInfo = array();
        $total = 0;
        Common::loadModel("ServerModel");
        $serverlist = ServerModel::getServList();
        $sevid = $_GET['sevid'];
        if (!empty($_POST['server']) && $_POST['server'] != 'all'){
            $sevid = $_POST['server'];
        }
        if ($_POST['server'] == 'all') {
            foreach ($serverlist as $key => $value) {
                if ( empty($value) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($value['id']);
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $db = Common::getDbBySevId($value['id']);
                $sql = "SELECT `roleid`,`ctime` FROM `t_order` WHERE `status`=1 ORDER BY `ctime` ASC ";
                $result = $db->fetchArray($sql);
                if (!empty($result)){
                    foreach ($result as $key => $value){
                        if (!isset($order[$value['roleid']])){
                            $order[$value['roleid']] = $value['ctime'];
                            $uids[] = $value['roleid'];
                        }
                    }
                }
                unset($result);
            }
        }else{
            $db = Common::getDbBySevId($sevid);
            $sql = "SELECT `roleid`,`ctime` FROM `t_order` WHERE `status`=1 ORDER BY `ctime` ASC ";
            $result = $db->fetchArray($sql);
            if (!empty($result)){
                foreach ($result as $key => $value){
                    if (!isset($order[$value['roleid']])){
                        $order[$value['roleid']] = $value['ctime'];
                        $uids[] = $value['roleid'];
                    }
                }
            }
        }
        if (!empty($uids)){
            $uids = array_unique($uids);
            $uid = implode(',', $uids);
            unset($sql, $result);
            $serverId = ServerModel::getDefaultServerId();
            $db = Common::getDbBySevId($serverId);
            $sql = "SELECT `uid`,`reg_time` FROM `register` WHERE `uid` IN (".$uid.") ";
            $result = $db->fetchArray($sql);
            foreach ($result as $rk => $rv){
                if (isset($order[$rv['uid']])){
                    $hk = ceil(($order[$rv['uid']]-$rv['reg_time'])/86400);
                    $dataInfo[$hk] += 1;
                    $total += 1;
                }
            }
            ksort($dataInfo);
        }
        $SevidCfg = Common::getSevidCfg($sevid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 后台操作日志
     */
    public function admin_log(){
        $adminName = include (ROOT_DIR.'/administrator/config/userAccount.php');
        $where = 'WHERE `time`>0 ';
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if (!empty($_POST['startTime']) && !empty($_POST['endTime'])){
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
        }
        $where .= " AND `time`>".$startTime." AND `time`<".$endTime;
        if (!empty($_POST['admin'])){
            $where .= " AND `admin`='".trim($_POST['admin'])."'";
        }
        if (!empty($_POST['models'])){
            $where .= " AND `model`='".trim($_POST['models'])."'";
        }
        if (!empty($_POST['controls'])){
            $where .= " AND `control`='".trim($_POST['controls'])."'";
        }
        if (!empty($_POST['user'])){
            $where .= " AND `data` like '%".trim($_POST['user'])."%'";
        }
        $db = Common::getComDb('flow');
        $sql = "SELECT * FROM `admin_log` ".$where." ORDER BY `time` DESC ";
        $data = $db->fetchArray($sql);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    /**
     * 超过限制ip监控
     */
    public function ipmonitor(){
        $key = 'iplimit_regsiter_pass_standard';
        $cache = Common::getComMem();
        $passInfo = $cache->get($key);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 头像分布
     */
    public function header(){
        Common::loadModel("ServerModel");
        $serverlist = ServerModel::getServList();
        $sex  = array();
        $job  = array();
        $total = 0;
        foreach ($serverlist as $key => $value) {
            $data = array();
            if ( empty($value) ) {
                continue;
            }
            $SevidCfg1 = Common::getSevidCfg($value['id']);
            if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                continue;
            }
            if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                continue;
            }
            $db = Common::getMyDb();
            $table_div = Common::get_table_div();
            for ($i = 0; $i < $table_div; $i++) {
                $table = 'user_' . Common::computeTableId($i);
                $sql = "SELECT `job`, count(`job`) AS `jobs` FROM {$table} WHERE `sex`=1 GROUP BY `job`;";
                $result = $db->fetchArray($sql);
                if (is_array($result)){
                    foreach ($result as $k => $v){
                        $sex[1] += $v['jobs'];
                        $job[1][$v['job']] += $v['jobs'];
                        $total += $v['jobs'];
                    }
                }
                unset($sql, $result);
                $sql = "SELECT `job`, count(`job`) AS `jobs` FROM {$table} WHERE `sex`=2 GROUP BY `job`;";
                $result = $db->fetchArray($sql);
                if (is_array($result)){
                    foreach ($result as $k => $v){
                        $sex[2] += $v['jobs'];
                        $job[2][$v['job']] += $v['jobs'];
                        $total += $v['jobs'];
                    }
                }
                unset($sql, $result);
            }
        }
        ksort($job[1]);
        ksort($job[2]);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 用户步骤流失率
     */
    public function userStep(){
        $allList = array();
        $total = 0;

        if (!empty($_POST['serverid']) ) {

            $serverid = $_POST['serverid'];
            Common::loadModel('ServerModel');
            $serverList = ServerModel::getServList();
            $isTrueServer = false;
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }

                if ($v['id'] == $serverid) {
                    $isTrueServer = true;
                    break;
                }
            }

            if (!$isTrueServer){
                echo "<script>alert('区服不存在!');</script>";
                return false;
            }

            $SevidCfg = Common::getSevidCfg($serverid);
            $db = Common::getDftDb();

            $totalSql = "SELECT count(`id`) AS total, `step_id` FROM `user_step`";
            $totalRes = $db->fetchRow($totalSql);
            if ($totalRes) {
                $total = $totalRes["total"];
            }

            $totalSql .= " GROUP BY `step_id` ORDER BY `step_id` ASC";
            $res = $db->query($totalSql);
            while($row = mysql_fetch_assoc($res)){

                $allList[] = $row;
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 服装统计
     */
    public function fuzhuang(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $clothesList = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $serverid = 0;
        if($_POST){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $table_div = Common::get_table_div();
            $sqls = array();
            $sqls[] = "SELECT * FROM `act_item_log` WHERE `actid` = 6140 AND `ftime` >= ".$startTime." and `ftime` < ".$endTime;

            $info = array();
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

                $SevidCfg = Common::getSevidCfg($SevidCfg1['sevid']);
                $db = Common::getDftDb("flow");

                foreach ($sqls as $sql){
                    $rt = $db->query($sql);
                    while($row = mysql_fetch_assoc($rt)){

                        $itemid = $row["itemid"];
                        if (isset($info[$itemid])) {
                            $info[$itemid] += $row["num"];
                        }else{
                            $info[$itemid] = $row["num"];
                        }
                    }
                }
            }

            $useClotheConfig = Game::getcfg('use_clothe');
            foreach ($useClotheConfig as $k => $v) {

                if (isset($info[$k])) {
                    $clothesList[] = array("id" => $k, "name" => $v["name"], "count" => $info[$k]);
                }else{
                    $clothesList[] = array("id" => $k, "name" => $v["name"], "count" => 0);
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 知己统计
     */
    public function zhiji(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $clothesList = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $serverid = 0;
        if($_POST){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $table_div = Common::get_table_div();
            $sqls = array();
            $sqls[] = "SELECT * FROM `act_item_log` WHERE `actid` = 14 AND `ftime` >= ".$startTime." and `ftime` < ".$endTime;

            $info = array();
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

                $SevidCfg = Common::getSevidCfg($SevidCfg1['sevid']);
                $db = Common::getDftDb("flow");

                foreach ($sqls as $sql){
                    $rt = $db->query($sql);
                    while($row = mysql_fetch_assoc($rt)){

                        $itemid = $row["itemid"];
                        if (isset($info[$itemid])) {
                            $info[$itemid] += $row["num"];
                        }else{
                            $info[$itemid] = $row["num"];
                        }
                    }
                }
            }

            $wifeConfig = Game::getcfg('wife');
            foreach ($wifeConfig as $k => $v) {

                if (isset($info[$k])) {
                    $clothesList[] = array("id" => $k, "name" => $v["wname"], "count" => $info[$k]);
                }else{
                    $clothesList[] = array("id" => $k, "name" => $v["wname"], "count" => 0);
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 伙伴统计
     */
    public function huoban(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $clothesList = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $serverid = 0;
        if($_POST){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $table_div = Common::get_table_div();
            $sqls = array();
            $sqls[] = "SELECT * FROM `act_item_log` WHERE `actid` = 8 AND `ftime` >= ".$startTime." and `ftime` < ".$endTime;

            $info = array();
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

                $SevidCfg = Common::getSevidCfg($SevidCfg1['sevid']);
                $db = Common::getDftDb("flow");

                foreach ($sqls as $sql){
                    $rt = $db->query($sql);
                    while($row = mysql_fetch_assoc($rt)){

                        $itemid = $row["itemid"];
                        if (isset($info[$itemid])) {
                            $info[$itemid] += $row["num"];
                        }else{
                            $info[$itemid] = $row["num"];
                        }
                    }
                }
            }

            $heroConfig = Game::getcfg('hero');
            foreach ($heroConfig as $k => $v) {

                if (isset($info[$k])) {
                    $clothesList[] = array("id" => $k, "name" => $v["name"], "count" => $info[$k]);
                }else{
                    $clothesList[] = array("id" => $k, "name" => $v["name"], "count" => 0);
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 道具统计
     */
    public function itemAll(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $items = Game::getcfg('item');
        $itemList = array();
        if(!empty($_POST)){

            $sqls = [];
            $itemInfo = array();
            $server = $_POST['server'];
            $itemId = $_POST['itemId'];
            $table_div = Common::get_table_div();
            for ($i = 0; $i < $table_div; $i++) {
                $table = 'item_' . Common::computeTableId($i);
                $sql = "SELECT `itemid`, SUM(`count`) AS count FROM {$table} WHERE ";
                if ($itemId > 0) {
                    $sql .= " `itemid` = ${itemId} ";
                }else{
                    $sql .= " `itemid` > 0 GROUP BY `itemid`; ";
                }
                $sqls[] = $sql;
            }

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( !in_array($SevidCfg1['sevid'], $server) ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                foreach ($sqls as $sql){
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach ($result as $k => $v){

                            if ($v["count"] > 10000) {
                                continue;
                            }

                            if (isset($itemInfo[$v["itemid"]])) {
                                $itemInfo[$v["itemid"]] += $v["count"];
                            }else{
                                $itemInfo[$v["itemid"]] = $v["count"];
                            }
                        }
                    }
                }
            }

            foreach ($itemInfo as $key => $value) {
                $itemList[] = array("itemid" => $key, "count" => $value, "name" => $items[$key]["name_cn"]);
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    /*
     * 服装知己伙伴每日统计
     */
    public function huobanday(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $clothesList = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $serverid = 0;
        if($_POST){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }
            $actid = 6140;
            $conf = Game::getcfg('use_clothe');
            if ($_POST['actid'] == 14) {
                $actid = $_POST['actid'];
                $conf = Game::getcfg('wife');
            }
            if ($_POST['actid'] == 8) {
                $actid = $_POST['actid'];
                $conf = Game::getcfg('hero');
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $table_div = Common::get_table_div();
            $sql = "SELECT ftime,itemid,SUM(num) as count FROM act_item_log where `actid` = {$actid} AND `ftime` >= {$startTime} and `ftime` < {$endTime} GROUP BY ftime,itemid";

            $res = array();
            $keyList = array("date" => 1);
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

                $SevidCfg = Common::getSevidCfg($SevidCfg1['sevid']);
                $db = Common::getDftDb("flow");

                $rt = $db->query($sql);
                while($row = mysql_fetch_assoc($rt)){

                    if (isset($conf[$row["itemid"]])) {

                        $keyList[$row["itemid"]] = 1;
                        $reg = date('Ymd', $row['ftime']);
                        $res[$reg][$row["itemid"]] += $row["count"];
                    }
                }
            }

            $list = array();
            foreach ($res as $key => $value) {

                $info = array($key);
                $num = 0;
                foreach ($keyList as $k => $v) {

                    $num++;
                    if ($num == 1) {
                        continue;
                    }

                    if (isset($value[$k])) {
                        $info[] = $value[$k];
                    }else{
                        $info[] = 0;
                    }
                }
                $list[] = $info;
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 在线统计
     */
    public function onLine(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $total = 0;
        $dataInfo = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $list = array();
        if(!empty($_POST)){

            $server = explode('-', $_POST['server']);
            $sevId1 = $server[0];
            $sevId2 = $server[1];
            if ($sevId1 == "all") {
                $sevId1 = 0;
                $sevId2 = 99999999;
            }

            if (empty($sevId2)) {
                $sevId2 = $sevId1;
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $stype = intval($_POST['stype']);
            $table_div = Common::get_table_div();

            $min = 0;
            $max = 0;
            $cfg_card = Game::getcfg('card');
            foreach ($cfg_card as $k => $v) {

                if ($min == 0) {
                    $min = $v["id"];
                }
                $max = $v["id"];
            }

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $sevId1 && ($sevId1 > $SevidCfg1['sevid'] || $sevId2 < $SevidCfg1['sevid']) ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $sql = "SELECT * FROM `user_on_line_count` WHERE `date` >= ".$startTime." AND `date` < ".$endTime;
                $lineCountList = $db->fetchArray($sql);
                foreach ($lineCountList as $key => $value) {
                    $date = date("YmdH", $value["date"]);
                    if ($value["count"] > 0) {

                        $list[$date]["count"] += intval($value["count"]);
                        if (!isset($list[$date]["time"])) {
                            $list[$date]["time"] = 1;
                        }
                        if (!isset($list[$date]["money"])) {
                            $list[$date]["money"] = 0;
                        }
                    }
                }

                $sql = "SELECT * FROM `user_on_line_time` WHERE `date` >= ".$startTime." AND `date` < ".$endTime;
                $lineTimeList = $db->fetchArray($sql);
                foreach ($lineTimeList as $key => $value) {
                    $date = date("YmdH", $value["date"]);
                    if (isset($list[$date]) && $value["lineTime"] > 0) {
                        $list[$date]["time"] += $value["lineTime"];
                    }
                }

                $orderSql = "SELECT `ptime`, `money` FROM `t_order` WHERE `status` > 0 AND `ptime` >= ".$startTime." AND `ptime` < ".$endTime;
                $orderRes = $db->fetchArray($orderSql);
                foreach ($orderRes as $key => $value) {
                    $date = date("YmdH", $value["ptime"]);
                    if (isset($list[$date])) {
                        $list[$date]["money"] += $value['money'];
                    }
                }
            }

            krsort($list);

            $newList = array();
            foreach ($list as $key => $value) {
                $date = substr($key, 4);
                $time = round($value["time"] / $value["count"],2);
                $newList[] = array("date" => $date, "count" => $value["count"], "time" => $time, "money" => $value["money"]);
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 在线统计
     */
    public function onLineCompany(){

        $allUids = array(
            10286 => "六一",
            10284 => "何俊宏",
            10283 => "李莹",
            10285 => "徐金嵩",
            3000814 => "吕根全",
            3000815 => "杨林",
            2002048 => "熊俊",
            3000823 => "林楚楚",
            2002334 => "戴嘉炜",
            3000829 => "王跃",
            3000670 => "肉肉",
            10288 => "韩辰斌"
        );
        $uids = array();
        foreach ($allUids as $uid => $uv) {
            $uids[] = $uid;
        }
        $uidStr = implode("','", $uids);

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $total = 0;
        $dataInfo = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $list = array();
        $dayList = array();
        if(!empty($_POST)){

            $server = explode('-', $_POST['server']);
            $sevId1 = $server[0];
            $sevId2 = $server[1];
            if ($sevId1 == "all") {
                $sevId1 = 0;
                $sevId2 = 99999999;
            }

            if (empty($sevId2)) {
                $sevId2 = $sevId1;
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $table_div = Common::get_table_div();
            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                if ( 0 < $sevId1 && ($sevId1 > $SevidCfg1['sevid'] || $sevId2 < $SevidCfg1['sevid']) ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $sql = "SELECT * FROM `user_on_line_time` WHERE `date` >= ".$startTime." AND `date` < ".$endTime ." AND `uid` IN ('".$uidStr."') ";
                $lineTimeList = $db->fetchArray($sql);
                foreach ($lineTimeList as $key => $value) {

                    if ($value["lineTime"] > 0) {
                        $list[$value["uid"]] += $value["lineTime"];
                    }
                }
            }

            $newList = array();
            foreach ($allUids as $uk => $uv) {

                if (isset($list[$uk])) {

                    $time = $list[$uk];
                    $timeList = array("hours" => 0, "minutes" => 0, "seconds" => 0);
                    if($time >= 3600){
                      $timeList["hours"] = floor($time/3600);
                      $time = ($time%3600);
                    }
                    if($time >= 60){
                      $timeList["minutes"] = floor($time/60);
                      $time = ($time%60);
                    }
                    $timeList["seconds"] = floor($time);

                    $timeStr = "";
                    if(!empty($timeList["hours"])){
                        $timeStr .= $timeList['hours']."小时";
                    }
                    if(!empty($timeList["minutes"])){
                        $timeStr .= $timeList['minutes']."分";
                    }
                    $timeStr .= $timeList["seconds"]."秒";
                    $newList[] = array("uid" => $uk, "name" => $uv, "time" => $timeStr);
                }else{
                    $newList[] = array("uid" => $uk, "name" => $uv, "time" => 0);
                }
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 客服自动回复
     */
    public function chatAuto(){

        Common::loadModel('ServerModel');
            $serverList = ServerModel::getServList();

        $total = 0;
        $dataInfo = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $list = array();
        if(!empty($_POST)){

            $server = explode('-', $_POST['server']);
            $sevId1 = $server[0];
            $sevId2 = $server[1];
            if ($sevId1 == "all") {
                $sevId1 = 0;
                $sevId2 = 99999999;
            }

            if (empty($sevId2)) {
                $sevId2 = $sevId1;
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            $stype = intval($_POST['stype']);
            $table_div = Common::get_table_div();

            $min = 0;
            $max = 0;
            $cfg_card = Game::getcfg('card');
            foreach ($cfg_card as $k => $v) {

                if ($min == 0) {
                    $min = $v["id"];
                }
                $max = $v["id"];
            }

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if ( 0 < $sevId1 && ($sevId1 > $SevidCfg1['sevid'] || $sevId2 < $SevidCfg1['sevid']) ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $arr = array();
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $sql = "SELECT * FROM `service_chat_automatic` WHERE `click_time` >= ".$startTime." AND `click_time` < ".$endTime;
                $autoList = $db->fetchArray($sql);
                foreach ($autoList as $key => $value) {

                    if (!isset($arr[$value["cId"]])) {
                        $arr[$value["cId"]]["uids"] = array();
                    }

                    if (!in_array($value["uid"], $arr[$value["cId"]]["uids"])){
                        $arr[$value["cId"]]["uids"][] = $value["uid"];
                    }
                }

                foreach ($arr as $key => $value) {

                    $list[$key]["count"] += count($value["uids"]);
                }
            }
        }

        $playerConfig = Game::getcfg('player');
        foreach ($list as $k => $v) {

            if (isset($playerConfig[$k])) {
                $list[$k]["name"] = $playerConfig[$k]["name_cn"];
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 流失用户
     */
    public function lostUser(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $userList = array();
        $infoList = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $serverid = 0;
        if($_POST){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
            }

            if ($_POST['startTime']){
                $startTime = strtotime($_POST['startTime']);
            }

            $table_div = Common::get_table_div();
            for ($i = 0; $i < $table_div; $i++) {
                $table = 'user_' . Common::computeTableId($i);
                $sql = "SELECT `vip`, COUNT(`uid`) AS count FROM {$table} WHERE `lastlogin` <= ${startTime} GROUP BY `vip` ORDER BY `vip` DESC";
                $sqls[] = $sql;
            }

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

                $SevidCfg = Common::getSevidCfg($SevidCfg1['sevid']);
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                foreach ($sqls as $sql){
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach ($result as $k => $v){

                            if (!empty($v)) {
                                $infoList[$v["vip"]] += $v["count"];
                            }
                        }
                    }
                }
            }

            foreach ($infoList as $key => $value) {
                $userList[] = array("vip" => $key, "count" => $value);
            }

            $flag=array();
            foreach($userList as $arr2){
                $flag[] = $arr2["vip"];
            }
            array_multisort($flag, SORT_DESC, $userList);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 活跃用户
     */
    public function activeUser(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $userList = array();
        $infoList = array("0" => 0);
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $serverid = 0;
        if($_POST){

            $server = explode('-', $_POST['server']);
            $sevId1 = $server[0];
            $sevId2 = $server[1];
            if ($sevId1 == "all") {
                $sevId1 = 0;
                $sevId2 = 99999999;
            }

            if (empty($sevId2)) {
                $sevId2 = $sevId1;
            }

            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }

            Common::loadModel('ServerModel');
            $sevId = ServerModel::getDefaultServerId();
            $sql = "SELECT `openid`, `servid` FROM `login_log` WHERE `login_time` >= {$startTime} AND `login_time` <= {$endTime} AND `servid` >= {$sevId1} AND `servid` <= {$sevId2} GROUP BY `openid` ORDER BY `servid`";
            $db = Common::getDbBySevId($sevId);
            $result = $db->fetchArray($sql);
            if (is_array($result)){
                foreach ($result as $k => $v){

                    if (!empty($v)) {
                        $infoList["0"] += 1;
                        $infoList[$v["servid"]] += 1;
                    }
                }
            }

            foreach ($infoList as $key => $value) {
                $userList[] = array("sevid" => $key, "count" => $value);
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 活跃公会用户
     */
    public function activeClubUser(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $userList = array();
        $infoList = array("0" => array("all" => 0, "active" => 0));
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $serverid = 0;
        if($_POST){

            $server = explode('-', $_POST['server']);
            $sevId1 = $server[0];
            $sevId2 = $server[1];
            if ($sevId1 == "all") {
                $sevId1 = 0;
                $sevId2 = 99999999;
            }

            if (empty($sevId2)) {
                $sevId2 = $sevId1;
            }

            if ($_POST['startTime']){
                $startTime = strtotime($_POST['startTime']);
            }
            $oldTime = $startTime - 7 * 86400;

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                $sevId = $SevidCfg1['sevid'];

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $sevId ) {
                    continue;
                }
                if ( 0 < $sevId1 && ($sevId1 > $sevId || $sevId2 < $sevId) ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $sevId > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $redis = Common::getDftRedis();
                $paihang  = $redis->zRevRange('club_redis', 0, -1);  //获取排行数据
                if (!empty($paihang)) {

                    foreach ($paihang as $key => $cid) {

                        $ClubModel = Master::getClub($cid);
                        $result = $ClubModel->getBase();

                        foreach ($result["members"] as $k => $v) {

                            $infoList["0"]["all"] += 1;
                            $infoList[$sevId]["all"] += 1;

                            if ( $v["loginTime"] >= $oldTime && $v["loginTime"] <= $startTime ){

                                $infoList["0"]["active"] += 1;
                                $infoList[$sevId]["active"] += 1;
                            }
                        }
                        unset($result);
                    }
                }
            }

            foreach ($infoList as $key => $value) {
                $userList[] = array("sevid" => $key, "all" => $value["all"], "active" => $value["active"]);
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * VIP充值查询
     */
    public function vipPaySearch(){

        $item = array();
        $item["0"] = array('money' => 0,'name'=> '全部');
        // $order_shop_k = Game::getCfg('order_shop_k');
        $order_shop_k = Master::getOrderShopCfg();
        foreach ($order_shop_k as $k => $v) {

            if ($v['type'] == 1) {

                $item[$v["dc"]] = array('money' => $v["diamond"],'name'=> '直充-'.$v["dollar"].'元');
            }else if ($v['type'] == 2) {

                $item[$v["dc"]] = array('money' => $v["diamond"],'name'=> '月卡-'.$v["dollar"].'元');
            }else if ($v['type'] == 3) {

                $item[$v["dc"]] = array('money' => $v["diamond"],'name'=> '年卡-'.$v["dollar"].'元');
            }else if ($v['type'] == 5) {

                $item[$v["dc"]] = array('money' => $v["diamond"],'name'=> '周卡-'.$v["dollar"].'元');
            }else if ($v['type'] == 6) {

                $item[$v["dc"]] = array('money' => $v["diamond"],'name'=> '成长基金-'.$v["dollar"].'元');
            }
        }

        $gift_bag = Game::getGiftBagCfg();
        foreach ($gift_bag as $k => $v) {

            $actcoin = 10 * $v["grade"] + 1000000 + 10000 * $v["id"];
            $item["g_".$v["id"]] = array('money' => $actcoin,'actcoin' => $actcoin,'name'=> '礼包-'.$v["id"]."-".$v["name"], "type" => 4);
        }

        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $vipList = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        $serverid = 0;
        if($_POST){

            $server = explode('-', $_POST['server']);
            $sevId1 = $server[0];
            $sevId2 = $server[1];
            if ($sevId1 == "all") {
                $sevId1 = 0;
                $sevId2 = 99999999;
            }

            $vips = explode('-', $_POST['vips']);
            $vip1 = $vips[0];
            $vip2 = $vips[1];
            if ($vip1 == "all") {
                $vip1 = 0;
                $vip2 = 99;
            }

            if (empty($vip2)) {
                $vip2 = $vip1;
            }

            if ($_POST['startTime']){
                $startTime = $_POST['startTime'];
            }
            if ($_POST['endTime']){
                $endTime = $_POST['endTime'];
            }

            if(!empty($_POST['startTime'])){
                $start = $_POST['startTime'];
                $startTime = strtotime($start);
            }
            if(!empty($_POST['endTime'])){
                $end = $_POST['endTime'];
                $endTime = strtotime($end);
            }

            $userWhere = " where `vip` >= {$vip1} and `vip` <= {$vip2} ";
            $where = " and `paytype` != 'houtai' and `ctime` >= {$startTime} and `ctime` <= {$endTime} ";
            if(!empty($_POST['channels'])){
                $channels = $_POST['channels'];
                $platforms = implode('","', $_POST['channels']);
                $where .= ' and `platform` IN ("'.$platforms.'")';

                $k1 = 0;
                $userWhere .=  " AND platform in ( ";
                foreach($channels as $pt){
                    if($k1 == 0){
                        $userWhere .=  "'".$pt."'";
                    }else{
                        $userWhere .=  ",'".$pt."'";
                    }
                    $k1 ++;
                }
                $userWhere .=  "  ) ";
            }

            if(!empty($_POST['item'])){
                $money = $item[$_POST['item']]['money'];
                $where .= " AND `diamond` = {$money} ";
            }

            foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                $sevId = $SevidCfg1['sevid'];

                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $sevId ) {
                    continue;
                }
                if ( 0 < $sevId1 && ($sevId1 > $sevId || $sevId2 < $sevId) ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $sevId > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $sql = 'select * from `t_order` where `status` > 0'.$where.' order by `ctime` desc';
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $orderRes = $db->fetchArray($sql);

                $orderList = array();
                if (!empty($orderRes)) {

                    foreach ($orderRes as $ok => $ov) {

                        if (!isset($orderList[$ov["roleid"]])) {
                            $orderList[$ov["roleid"]] = array("count" => 0, "money" => 0);
                        }
                        $orderList[$ov["roleid"]]["count"]++;
                        $orderList[$ov["roleid"]]["money"] += Master::returnDoller($ov['money']);
                    }
                }
                unset($orderRes);

                $table_div = Common::get_table_div($SevidCfg1['sevid']);
                for ($i = 0 ; $i < $table_div ; $i++){
                    $table = '`user_'.Common::computeTableId($i).'`';
                    $sql = "select `vip`, `uid` from {$table} {$userWhere}";
                    $userData = $db->fetchArray($sql);
                    foreach ($userData as $uk => $uv){

                        if (!isset($vipList[$uv["vip"]])) {
                            $vipList[$uv["vip"]] = array("vip" => $uv["vip"], "vipPeople" => 0, "payCount" => 0, "payMoney" => 0);
                        }

                        if (isset($orderList[$uv["uid"]])) {
                            $vipList[$uv["vip"]]["vipPeople"]++;
                            $vipList[$uv["vip"]]["payCount"] += $orderList[$uv["uid"]]["count"];
                            $vipList[$uv["vip"]]["payMoney"] += $orderList[$uv["uid"]]["money"];
                        }
                    }
                    unset($userData);
                }
            }

            $flag=array();
            foreach($vipList as $arr2){
                $flag[] = $arr2["vip"];
            }
            array_multisort($flag, SORT_DESC, $vipList);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
}
