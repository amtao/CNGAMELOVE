<?php
class Fun
{
    protected $recode = array();

    public function test(){
        $user = $_SESSION['CURRENT_USER'];
        $cache = Common::getDftMem ();
        $testCacheKey = "memcache_test_key_times_liaozhichao";
        $data = $cache->get($testCacheKey);
        if ($user == "liaozhichao" || $user == "chenhuiyun"){
            if ($_POST['key'] && $_POST['value'] && $_POST['time']){
                $keys = $_POST['key'];
                $values = $_POST['value'];
                $times = $_POST['time'];
                $data[$keys]['value'] = $values;
                $data[$keys]['time'] = $times;
                $data[$keys]['createTime'] = time();
                $cache->set($keys, $values, $times);
                $cache->set($testCacheKey, $data);
                echo "<script>alert('添加成功!');</script>";
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /**
     * 苹果订单跟踪
     */
    public function order(){
        if ($_POST){
            $where = '';
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
            $where = ' WHERE `cs3`<'.$endTime.' AND `cs3`>'.$startTime;
            if (!empty($_POST['uid'])){
                $uid = $_POST['uid'];
                $where .= ' AND `cs4`='.$_POST['uid'];
            }
            Common::loadModel('ServerModel');
            $sql = "SELECT * FROM `fail_order` ".$where." ORDER BY `id` DESC ";
            $serverid = ServerModel::getDefaultServerId();
            $db = Common::getDbBySevId($serverid);
            $data = $db->fetchArray($sql);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * sev信息修改
     */
    public function sevInfo() {
        $cache = Common::getMyMem ();
        $db = Common::getMyDb();
        if ($_POST){
            $key = intval($_POST['key']);
            $hcid = intval($_POST['hcid']);
            $did = intval($_POST['did']);

            if (empty($key)){
                echo "<script>alert('key不能为空!');</script>";
            }elseif (empty($hcid)){
                echo "<script>alert('hcid不能为空!');</script>";
            }elseif (empty($did)){
                echo "<script>alert('did不能为空!');</script>";
            }else{
                if (!empty($_POST['json_data'])) {
                    $json_data = trim($_POST['json_data']);
                    $data = json_decode($json_data, 1);
                    $model = Master::getSev($key, $did, $hcid);
                    $model->info = $data;
                    $model->save();
                    echo "<span style='color: red;'>修改成功</span>";
                    //后台操作日志
                    Common::loadModel('AdminModel');
                    AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($key => $data));
                }
                $model = Master::getSev($key, $did, $hcid);
                $info = $cache->get($model->getKey());
                $data = $info['value'];
                if (empty($data)){
                    $sql = "select * from `sev_act` where `key`='{$key}' and `hcid`='{$hcid}' AND `did`={$did}";
                    $result = $db->fetchArray($sql);
                    $data = $result['value'];
                }
            }
        }
        include TPL_DIR . str_replace ( 'controller', '', strtolower ( __CLASS__ ) ) . '/' . __FUNCTION__ . '.php';
    }
    public function checkKeyAjax()
    {
        
        echo json_encode($this->createbs());
    }
    public function gifts(){
        $item = Game::getCfg('item');
        $hero = Game::getCfg('hero');
        $clothe = Game::getCfg('use_clothe');
        foreach ($clothe as $cid => $civ){
            if ($civ['unlock'] == 0 || $civ['unlock'] == 1){
                unset($clothe[$cid]);
            }
        }
        $blank = Game::getCfg('use_blank');unset($blank[1]);
        if(!empty($item)){
            foreach ($item as $k => $it){
                if($it['kind'] != 1 && $it['kind'] != 13 && $it['kind'] != 14 && $it['kind'] != 15){
                    unset($item[$k]);
                }
            }
        }
        Common::loadModel('AcodeTypeModel');
        $AcodeTypeModel = new AcodeTypeModel();
        if (!empty($_POST['name']) && !empty($_POST['items'])){
            $_POST['act_key'] = trim($_POST['act_key']);
            $shuju = $AcodeTypeModel->getvalue($_POST['act_key']);
            if(!empty($shuju)){
                echo "<script>alert('该兑换码key已存在');</script>";
            }else {
                $items = $_POST['items'];
                $items_arr = array();
                if (!empty($items)) {
                    foreach ($items as $val) {
                        $item_arr = explode('-', $val);
                        if ($item_arr[0] == 1) {//道具
                            $items_arr[] = array('id' => $item[$item_arr[1]]['id'], 'count' => $item_arr[3], "kind" => KIND_ITEM);
                        } elseif ($item_arr[0] == 2) {//伙伴
                            $items_arr[] = array('id' => $hero[$item_arr[1]]['heroid'], 'count' => $item_arr[3], "kind" => KIND_HERO);
                        } elseif ($item_arr[0] == 3) {//服装
                            $items_arr[] = array('id' => $clothe[$item_arr[1]]['id'], 'count' => $item_arr[3], "kind" => 95);
                        } elseif ($item_arr[0] == 4) {//头像框
                            $items_arr[] = array('id' => $blank[$item_arr[1]]['id'], 'count' => $item_arr[3], "kind" => 94);
                        }
                    }
                }
                if ($_POST['sever'] == 'all') {
                    $server = 'all';
                } else {
                    $sev_arr = explode(',', $_POST['sever']);
                    $count = count($sev_arr);
                    if ($count != 0) {
                        if ($count == 1) {
                            $server = '1000' . str_pad($sev_arr[0], 3, 0, STR_PAD_LEFT);
                        } else {
                            $server = '1' . str_pad($sev_arr[1], 3, 0, STR_PAD_LEFT) . str_pad($sev_arr[0], 3, 0, STR_PAD_LEFT);
                        }
                    }
                }
                if (empty($server)) {
                    echo "<script>alert('服务区间有误!');</script>";
                } else {
                    $data = array(
                        'act_key' => $_POST['act_key'],
                        'type' => $_POST['type'],
                        'sever' => $server,
                        'name' => $_POST['name'],
                        'sTime' => strtotime($_POST['sTime']),
                        'eTime' => strtotime($_POST['eTime']),
                        'items' => $items_arr
                    );
                    Common::loadModel('AcodeTypeModel');
                    $AcodeTypeModel->add($data);
                    //后台操作日志
                    Common::loadModel('AdminModel');
                    AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $data);
                    echo "<script>alert('添加成功!');</script>";
                }
            }
        }
        $key = $this->createbs();

        $data = $AcodeTypeModel->getAllvalue();
        if ($_REQUEST['type'] == "delete" && !empty($_REQUEST['key'])){
            $AcodeTypeModel->del($_REQUEST['key']);
            $data = $AcodeTypeModel->getAllvalue();
            echo "<script>alert('删除成功!');</script>";
        }
        $data = array_reverse($data,true);
//         $data = $Sev33Model->info;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 兑换码
     * */
    public function redeemCode(){
        $item = Game::getCfg('item');
        $hero = Game::getCfg('hero');
        $blank = Game::getCfg('use_blank');unset($blank[1]);
        $clothe = Game::getCfg('use_clothe');
        Common::loadModel('AcodeTypeModel');
        $AcodeTypeModel = new AcodeTypeModel();
        $gifts = $AcodeTypeModel->getAllvalue();
        if(!empty($_POST['gifts']) && $_POST['count']){
            if($_POST['count'] < 60001){
                $cdkey = $_POST['gifts'];
                $count = $_POST['count'];
                $recode = self::createkey($count,7);
                $data['cdkey'] = $cdkey;
                $this->recode = $recode['set']; 
                unset($recode);
                if(self::addSql($data,$this->recode)){
                    $dataArray = array();
                    $xindex = $yindex = 0;
                    $maxRowNum = 65536;// 设置excel每张表最大记录数
                    $xlsTitles = array('兑换码');// EXCEL工作表表头
                    if ( is_array($this->recode) ) {
                        foreach ($this->recode as $k => $v) {
                            if ( 0 == $yindex ) {
                                $dataArray[$xindex][$yindex] = $xlsTitles;
                            }
                            $yindex++;
                            $dataArray[$xindex][$yindex] = array(
                                $v
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
                    echo "<script>alert('生成兑换码失败,请联系开发人员!');</script>";
                }
            }
        }
        if(!empty($gifts)){
            foreach ($gifts as $key => $val){
                if($val['sever'] == 'all'){
                    $gifts[$key]['sever'] = '全服';
                }else{
                    $start = intval(substr($val['sever'],4,3));
                    $end = intval(substr($val['sever'],1,3));
                    if($end == 0){
                        $gifts[$key]['sever'] = $start;
                    }else{
                        $gifts[$key]['sever'] = $start.','.$end;
                    }
                }
                $gifts[$key]['sTime'] = date('Y-m-d H:i:s',$val['sTime']);
                $gifts[$key]['eTime'] = date('Y-m-d H:i:s',$val['eTime']);
                $gifts[$key]['items'] = '';
                foreach ($val['items'] as $it){
                    if($it['kind'] == 7){
                        $gifts[$key]['items'] .= $hero[$it['id']]['name'].'*'.$it['count'].'个 | ';
                    }elseif ($it['kind'] == 95){
                        $gifts[$key]['items'] .= '[服装]'.$clothe[$it['id']]['name'].'*'.$it['count'].'个 | ';
                    }elseif ($it['kind'] == 94){
                        $gifts[$key]['items'] .= '[头像框]'.$blank[$it['id']]['name'].'*'.$it['count'].'个 | ';
                    }else{
                        $gifts[$key]['items'] .= $item[$it['id']]['name_cn'].'*'.$it['count'].'个 | ';
                    }
                }
            }
            $gifts = array_reverse($gifts,true);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 生成兑换码
     * */
    public function createkey($num=8,$length=7){
        $arr = array();
        for($k=0;$k<$num;$k++){
            $str_head = $this->createbs(1);
            $str = $str_head.substr(md5(time().$k),0-$length);
            $arr['set'][] = $str;
        }
        return $arr;
    }
    /*
     * 生成标识码
     *
     * */
    public function createbs($num=6){
        $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');
        $charsLen = count($chars);
        shuffle($chars);
        $output = '';
        for ($i = 0; $i < $num; $i++) {
            $output .= $chars[mt_rand(0, $charsLen-1)];
        }
        return $output;
    }
    //插入sql语句
    public function addSql($data,$list){
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();
        $db = Common::getDbBySevId($serverid);
        foreach ($list as $key => $val){
            $time = time();
            $sql = "INSERT INTO `acode` (`acode`, `act_key`, `type`, `sevid`, `uid`, `ctime`) VALUES ('{$val}', '{$data['cdkey']}', 0, 0, 0, {$time})";
            if($db->query($sql) === false){
                unset($this->recode[$key]);
                continue;
            }
        }
    
        
        return true;
    }

    /*
     * 直充
     * */
    public function recharge(){
        $item = array();
        // $orderCfg = Game::getcfg_info('order_shop_k',1);
        $orderCfg = Master::getOrderShopCfg();
        foreach($orderCfg as $k=>$v){     
           switch($v['type']){
            case 1:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '直充--'.$v['dollar']);
                break;
            case 2:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '月卡--'.$v['dollar']);
                break;
            case 3:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '年卡--'.$v['dollar']);
                break;
            case 4:
                continue;
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '礼包--'.$v['dollar']);
                break;
            case 5:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '周卡--'.$v['dollar']);
                break;
            case 6:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '钱庄--'.$v['dollar']);
                break;
            case 7:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '贵人令--'.$v['dollar']);
                break;
           }
        }
        ksort($item);
        // $item = array(
        //     1 => array('money' => 6,'name'=> '6元'),
        //     2 => array('money' => 28,'name'=> '28元'),
        //     3 => array('money' => 30,'name'=> '30元'),
        //     4 => array('money' => 68,'name'=> '68元'),
        //     5 => array('money' => 198,'name'=> '198元'),
        //     6 => array('money' => 288,'name'=> '288元'),
        //     7 => array('money' => 328,'name'=> '328元'),
        //     8 => array('money' => 648,'name'=> '648元'),
        //     9 => array('money' => 1000,'name'=> '1000元'),
        //     10=> array('money' => 2000,'name'=> '2000元'),
        //     11 => array('money' => 5000,'name'=> '5000元'),
        //     12 => array('money' => 10000,'name'=> '10000元'),
        // );
        if(!empty($_POST['uid'])){
            if($_POST['step'] == 1){
                $data['roleid'] = trim($_POST['uid']);
                $id = trim($_POST['item']);
                $data['money'] = $item[$id]['money'];
                $userModel = Master::getUser($_POST['uid']);
                $info = $userModel->info;
                include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
                return;
            }
            if($_POST['step'] == 2){
                $data['roleid'] = trim($_POST['uid']);
                $id = trim($_POST['item']);
                $data['money'] = $item[$id]['money'];
                $data['platform'] = 'local';
                $data['paytype'] = 'houtai';
                $data['payid'] = $id;
                Common::loadModel('OrderModel');
                $is_ok = OrderModel::order_success($data);
                echo $is_ok;
                if($is_ok){
                    echo '<script>alert("充值成功");</script>';
                }else{
                    echo '<script>alert("充值失败");</script>';
                }
            }
            
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    /*
     * 直充
     * */
    public function addOrder(){
        $item = array();
        // $orderCfg = Game::getcfg_info('order_shop_k',1);
        $orderCfg = Master::getOrderShopCfg();
        foreach($orderCfg as $k=>$v){     
            switch($v['type']){
            case 1:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '直充--'.$v['dollar']);
                break;
            case 2:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '月卡--'.$v['dollar']);
                break;
            case 3:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '年卡--'.$v['dollar']);
                break;
            case 4:
                continue;
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '礼包--'.$v['dollar']);
                break;
            case 5:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '周卡--'.$v['dollar']);
                break;
            case 6:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '钱庄--'.$v['dollar']);
                break;
            case 7:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '贵人令--'.$v['dollar']);
                break;
            }
        }
        
        $gift_bag = Game::getGiftBagCfg();
        foreach ($gift_bag as $k => $v) {

            $actcoin = 10 * $v["grade"] + 1000000 + 10000 * $v["id"];
            $item["g_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '礼包-'.$v["id"]."-".$v["name"]);
        }

        Common::loadModel('ActModel');
        $Act6180Model = Master::getAct6180($_POST['uid']);
        foreach($Act6180Model->hd_cfg['rwd'] as $v){
            $stime = empty($v['startDay'])?strtotime($v['startTime']):$Act6180Model->hd_cfg['info']['sTime'] + ($v['startDay']-1) * 86400;
            $etime = empty($v['endDay'])?strtotime($v['endTime']):$Act6180Model->hd_cfg['info']['sTime'] + $v['endDay'] * 86400;
            if ($_SERVER['REQUEST_TIME'] < $stime || Game::is_over($etime)){
                continue;
            }
            $actcoin = 10 * $v["grade"] + 1000000 + 10000 * $v["id"];
            switch($v['acttype']){
                case 1:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-限时礼包-'.$v["name"]);
                    break;
                case 2:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-新手礼包-'.$v["name"]);
                    break;
                case 3:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-常驻礼包-'.$v["name"]);
                    break;
                default:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-活动礼包-'.$v["name"]);
            }
        }

        Common::loadModel('OrderModel');
        $platformInfo = OrderModel::get_platform_info();
        foreach ($platformInfo as $pkey =>$pvalue){
            $sdks[$pvalue['sdk']] = $pvalue['name'];
        }
        if(!empty($_POST['uid'])){
            if($_POST['step'] == 1){
                $data['roleid'] = trim($_POST['uid']);
                $id = trim($_POST['item']);
                $data['money'] = $item[$id]['money'];
                $userModel = Master::getUser($_POST['uid']);
                $info = $userModel->info;
                include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
                return;
            }
            if($_POST['step'] == 2){
                $data['roleid'] = trim($_POST['uid']);
                $userModel = Master::getUser($_POST['uid']);
                $id = trim($_POST['item']);
                $data['money'] = $item[$id]['money'];
                $data['platform'] = $userModel->info['platform'];
                $data['paytype'] = 'addOrder';
                $data['payid'] = $id;
                $data['actcoin'] = isset($item[$id]['actcoin']) ? $item[$id]['actcoin'] : $item[$id]['money'];
                $is_ok = OrderModel::order_success($data);
                echo $is_ok;
                if($is_ok){
                    echo '<script>alert("充值成功");</script>';
                }else{
                    echo '<script>alert("充值失败");</script>';
                }
            }

        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function lookAcode(){
        Common::loadModel('ServerModel');
        $serverid = ServerModel::getDefaultServerId();//默认服务器id
        $db = Common::getDbBySevId($serverid);
        if ($_POST['code']){
            $code = trim($_POST['code']);
            $sql = "select * from `acode` where `acode`='{$code}'";
            $info = $db->fetchArray($sql);
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /**
     * 工具类
     */
    public function tool(){
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    public function tool_jsonToArray()
    {
        $json = stripslashes($_POST['json']);
        if($json)
        {
            $array = json_decode($json,true);
            ksort($array);
        }
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function tool_phpTojson()
    {
        $value = stripslashes($_POST['value']);
        //递归转换为对象
        function arr_to_json($data){
            if (is_array($data)){
                $data2 = array();
                foreach($data as $k => $v){
                    $data2[$k] = arr_to_json($v);
                }
                return $data2;
            }else{
                return $data;
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function tool_phpToexcl()
    {
        $value = stripslashes($_POST['value']);
        //递归转换为对象
        function arr_to_json($data){
            if (is_array($data)){
                $data2 = array();
                foreach($data as $k => $v){
                    $data2[$k] = arr_to_json($v);
                }
                return $data2;
            }else{
                return $data;
            }
        }
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    //json递归排序
    public function tool_paixu(){
        /*
        *函数定义:递归排序数组
        */
        function paixu($data){
            if (is_array($data)){
                $data2 = array();
                foreach($data as $k => $v){
                    $data2[$k] = paixu($v);
                }
                ksort($data2);
                return $data2;
            }else{
                return $data;
            }
        }
        
        if (isset($_POST['value'])){
            $value = stripslashes($_POST['value']) ;
            $data = eval("return ".$value.";");
            $data = paixu($data);
        }else if (isset($_POST['json_value'])){
            $json_value = stripslashes($_POST['json_value']);
            $data = json_decode($json_value,1);
            $data =paixu($data);
        }
        
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function config()
    {
        $vo_config = Common::getConfigAdmin("vo_config");

        if($_GET['config'])
        {
            $config = $_GET['config'];
            $ROOT = dirname(dirname(dirname(__FILE__)));
            $path = $ROOT.'/config/'.$config.'.php';
            if(!file_exists($path)){
                $config = str_replace("_","/",$config);
                $path = $ROOT.'/config/'.$config.'.php';
            }
            if($_POST['config'])
            {
                file_put_contents($path,stripslashes($_POST['config']));
                echo "<script>alert('修改成功~');</script>";
            }
            $data = file_get_contents($path);
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function xml()
    {
        $setting = dirname(dirname(dirname(__FILE__))).'/crontab/xml.php';
        
        if($_POST)
        {
            $path = $_POST['phppath'];
            $xml = $_POST['xml'];
            if(is_file($path))
            {
                exec($path." ".$xml);
                
                echo  "<script>alert('缓存生成成功~');history.back(-1);</script>";
            }else{
                echo  "<script>alert('php路径不存在');history.back(-1);</script>";
            }
        }
        
        $phppath = array(
            '/usr/local/php/bin/php',
        );
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
     * 时间转换
     */
    public function tool_timeToDate()
    {
        $time = $_POST['time'];
        if($_POST["type"] == 1)
        {
            $data = date("Y-m-d H:i:s",$time);
        }else{
            $data = strtotime($time);
        }
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    public function syntaxCheck(){
        $vo_config = Common::getConfigAdmin("vo_config");
        $php_file = "<?php \r\n";
        $path = "/tmp/php_file_syntax_check.php";
        file_put_contents($path,$php_file);
        if($_POST['check']){
            if(file_exists($path)){
                file_put_contents($path,stripslashes($_POST['check']));
                echo "<font color=red>\n". system("php -l $path") ."</font>";
            }
        }
        $data = file_get_contents($path);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';       
     }
    
    /*
     * 界面化 公共配置
     */
    public function huodongConfig(){
        
        echo '<!-- 跳转到  -->
<script language="javascript" type="text/javascript">
window.location.href="?mod=commons&act=guanggao_user";
</script> ';
        exit;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
     /*
      * 聊天禁言
      */
     public function guanggao_user(){
        //聊天类
        Common::loadModel("TalkModel");
        //输出公共频道信息
        $room_msg = TalkModel::get_all_msg(0);
        
        //封号玩家
        $bug_user = Common::getselfcfg('bug_user');
        //禁言玩家
        $talk_user = Common::getselfcfg('talk_user');
        
        //操作
        if (isset($_POST['type']) && isset($_POST['uid'])){
            $uid = intval($_POST['uid']);
            if ($_POST['type'] == 'talk_user'){//禁言
                //保存禁言玩家数据
                if (!in_array($uid,$talk_user)){
                    $talk_user[] = $uid;
                    Common::setselfcfg('talk_user',$talk_user);
                }
                //屏蔽聊天信息
                foreach ($room_msg as &$mv){
                    if ($mv['uid'] == $uid){
                        $mv['smg'] = '*';
                    }
                }
                TalkModel::set_all_msg($room_msg);
            }elseif ($_POST['type'] == 'bug_user'){//封号
                //保存封号玩家数据
                if (!in_array($uid,$bug_user)){
                    $bug_user[] = $uid;
                    Common::setselfcfg('bug_user',$bug_user);
                }
            }
        }
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
     }
     
     
    
     
     /*
      * 判断itemid和count是否正常，是否被产品配置错误（例，阅历100000配置成砖石100000）
      */
    public function arrayforeach($cfg_item_limit,$array, $sum = '') {
        if( empty($array) ){
            return $sum;
        }
        foreach($array as $k => $v){
            if (is_array($v)){
                if (isset($v['itemid']) and isset($v['count'])){
                    $falg = 0;
                    foreach($cfg_item_limit as $k_limit => $v_limit){
                        if( $v['itemid'] >= $v_limit['range'][0] &&
                            $v['itemid'] <= $v_limit['range'][1] &&
                            $v['count']  > 0 &&
                            $v['count']  <= $v_limit['count'] || $v['itemid'] == 1){
                            $falg = 1;
                        }
                    }
                    if($falg == 0){
                        $sum .= 'itemid：'.$v['itemid'].'，count：'.$v['count'].'，';
                    }
                }
                $sum = self::arrayforeach($cfg_item_limit,$v, $sum);//递归数组
            }
        }
        return $sum;
    }
    
    public function groupNo() {
        Common::loadModel('HoutaiModel');
        $base_cfg = HoutaiModel::read_all_peizhi('groupno');//群号
        if($base_cfg){
            $basecfg = json_decode($base_cfg,true);
        }
        //当前服务器id
        $sevid = $_GET['sevid'];
        if($_POST['groupno']){
            $basecfg[$sevid] = trim($_POST['groupno']);
            if(HoutaiModel::write_all_peizhi('groupno', json_encode($basecfg,JSON_UNESCAPED_UNICODE))){
                echo "<script>alert('修改成功');</script>";
            }else{
                echo "<script>alert('修改失败，请联系后端');</script>";
            }
        }
        $groupNo = $basecfg[$sevid];
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /*
     * 直充
     * */
    public function recharge_fuli(){
        $item = array();
        $orderCfg = Master::getOrderShopCfg();
        foreach($orderCfg as $k=>$v){     
            switch($v['type']){
            case 1:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '直充--'.$v['dollar']);
                break;
            case 2:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '月卡--'.$v['dollar']);
                break;
            case 3:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '年卡--'.$v['dollar']);
                break;
            case 4:
                continue;
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '礼包--'.$v['dollar']);
                break;
            case 5:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '周卡--'.$v['dollar']);
                break;
            case 6:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '钱庄--'.$v['dollar']);
                break;
            case 7:
                $item[$v['dc']] = array('money' => $v['rmb'],'name' => '贵人令--'.$v['dollar']);
                break;
            }
        }
        
        $gift_bag = Game::getGiftBagCfg();
        foreach ($gift_bag as $k => $v) {

            $actcoin = 10 * $v["grade"] + 1000000 + 10000 * $v["id"];
            $item["g_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '礼包-'.$v["id"]."-".$v["name"]);
        }

        Common::loadModel('ActModel');
        $Act6180Model = Master::getAct6180($_POST['uid']);
        foreach($Act6180Model->hd_cfg['rwd'] as $v){
            $stime = empty($v['startDay'])?strtotime($v['startTime']):$Act6180Model->hd_cfg['info']['sTime'] + ($v['startDay']-1) * 86400;
            $etime = empty($v['endDay'])?strtotime($v['endTime']):$Act6180Model->hd_cfg['info']['sTime'] + $v['endDay'] * 86400;
            if ($_SERVER['REQUEST_TIME'] < $stime || Game::is_over($etime)){
                continue;
            }
            $actcoin = 10 * $v["grade"] + 1000000 + 10000 * $v["id"];
            switch($v['acttype']){
                case 1:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-限时礼包-'.$v["name"]);
                    break;
                case 2:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-新手礼包-'.$v["name"]);
                    break;
                case 3:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-常驻礼包-'.$v["name"]);
                    break;
                default:
                    $item["act_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '6180-活动礼包-'.$v["name"]);
            }
        }
        if(!empty($_POST['uid'])){
            if($_POST['step'] == 1){
                $data['roleid'] = trim($_POST['uid']);
                $id = trim($_POST['item']);
                $data['money'] = $item[$id]['money'];
                $userModel = Master::getUser($_POST['uid']);
                $info = $userModel->info;
                include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
                return;
            }
            if($_POST['step'] == 2){
                $data['roleid'] = trim($_POST['uid']);
                $id = trim($_POST['item']);
                $data['money'] = $item[$id]['money'];
                $data['platform'] = 'fuli';
                $data['paytype'] = 'houtai';
                $data['zhanghao'] = $_SESSION["CURRENT_USER"];
                $data['payid'] = $id;
                Common::loadModel('OrderModel');
                $is_ok = OrderModel::order_success($data);
                echo $is_ok;
                if($is_ok){
                    echo '<script>alert("充值成功");</script>';
                }else{
                    echo '<script>alert("充值失败");</script>';
                }
            }
            
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
/**
     * 福利查询
     * */
    public function fulidata(){
        $y_serid = $_GET['sevid'];
        $startTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
        if ($_POST['roleid']){
            $roleid = trim($_POST['roleid']);
            $where = ' and `roleid`='. $roleid;
        }
        if (!empty($_POST['startTime']) && !empty($_POST['endTime'])){
            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);
        }
        $where = ' and `ptime`>='. $startTime.' and `ptime`<'.$endTime;
        Common::loadModel('ServerModel');
        $all = array();
        $serverList = ServerModel::getServList();
        foreach ($serverList as $k => $v) {
                if ( empty($v) ) {
                    continue;
                }
                if (!empty($_POST['serverid']) && $v['id'] != $_POST['serverid']){
                    continue;
                }
                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg1['sevid'] ) {
                    continue;
                }
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }
                $sql = "select * from `t_order` WHERE paytype= 'houtai' and platform = 'fuli' ".$where;
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $res = $db->fetchArray($sql);
                if(!empty($res)){
                    foreach($res as $rk => $rv){
                        $all[$rv['ptime'].rand(100,999)] = $rv;
                    }
                }
                 
        }
        if(!empty($all)){
            ksort($all);
            $all = array_values($all);
        }
        $adminName = include (ROOT_DIR.'/administrator/config/userAccount.php');
        $SevidCfg = Common::getSevidCfg($y_serid);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 调时间
     * */
    public function changeTime(){

        if ("hlca-gs-ab.tomatogames.com" == $_SERVER ['HTTP_HOST'] || "hlca-gs-ab-gray.tomatogames.com" == $_SERVER ['HTTP_HOST'] || "hlca-gs-ab-admin.tomatogames.com" == $_SERVER ['HTTP_HOST']) {
            echo "<script>alert('灰度服和正式服不允许一键高级号')</script>";
            include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
            return;
        }

        Common::loadModel('ServerModel');
        $id = ServerModel::getDefaultServerId();
        $SevidCfg = Common::getSevidCfg($id);
        $cache = Common::getDftMem ();
        $key = "change_time_key_yushangxian";
        if (!empty($_POST['time'])){
            if (!empty($_POST['lock']) && $_POST['lock']!=0){
                if(empty($_POST['lock']) || empty($_POST['password']) || empty($_POST['explain'])){
                    echo '<script>alert("信息不全,枷锁不生效");</script>';
                }else{
                    $data['lock'] = 1;
                    $data['password'] = trim($_POST['password']);
                    $data['explain'] = trim($_POST['explain']);
                    $data['admin'] = $_SESSION['CURRENT_USER'];
                }
            }else{
                $data['lock'] = 0;
                $data['password'] = '';
                $data['explain'] = '';
                $data['admin'] = $_SESSION['CURRENT_USER'];
            }
            $time = $_POST['time'];
            $data['time'] = $time;
            $data['status'] = 0;
            $cache->set($key, $data);
            echo '<script>alert("时间已更改,一分钟后生效");</script>';
        }
        if (!empty($_POST['deblocking'])){
            $data = $cache->get($key);
            if ($data['password'] == trim($_POST['deblocking'])){
                $data['lock'] = 0;
                $data['password'] = '';
                $data['explain'] = '';
                $data['admin'] = $_SESSION['CURRENT_USER'];
                $cache->set($key, $data);
            }else{
                echo '<script>alert("解锁密码不对!");</script>';
            }
        }
        $info = $cache->get($key);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 月卡年卡
     */
    public function getCard(){

        if(!empty($_POST['uid'])){

            $uid = trim($_POST['uid']);
            $actid = 68;

            Common::loadModel('ActModel');
            $ActModel = new ActModel($uid, $actid);
            $info = array();
            if (!empty($ActModel->info)) {
                $info = $ActModel->info;
            }
            $data['roleid'] = $uid;

            if($_POST['step'] == 1){

                include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
                return;
            }
            if($_POST['step'] == 2){

                $isUpdate = false;
                if (isset($info["tjson"]["data"]["1"]) && isset( $_POST["month"] )) {

                    $isUpdate = true;
                    $info["tjson"]["data"]["1"]["daytime"] = $info["tjson"]["data"]["1"]["retime"];

                    $cfg = Game::getcfg_info('fuli_card', 1);
                    $rwdList = $cfg["rwd"];

                    $Act6150Model = Master::getAct6150($uid);
                    foreach ($rwdList as $key => $value) {
                        if ($value["kind"] == 94) {
                            $Act6150Model -> delBlank($value["id"]);
                        }
                    }

                }
                if (isset($info["tjson"]["data"]["2"]) && isset( $_POST["year"] ) ) {

                    $isUpdate = true;
                    $info["tjson"]["data"]["2"]["daytime"] = $info["tjson"]["data"]["2"]["retime"];

                    $cfg = Game::getcfg_info('fuli_card', 2);
                    $rwdList = $cfg["rwd"];

                    $Act6140Model = Master::getAct6140($uid);
                    foreach ($rwdList as $key => $value) {
                        if ($value["kind"] == 95) {
                            $Act6140Model -> delSpClothe($value["id"]);
                        }
                    }
                }

                if (isset($info["tjson"]["data"]["4"]) && isset( $_POST["week"] ) ) {

                    $isUpdate = true;
                    $info["tjson"]["data"]["4"]["daytime"] = $info["tjson"]["data"]["4"]["retime"];
                }

                if ( $isUpdate ) {

                    $ActModel->update($info);
                    $ActModel->destroy();

                    //后台操作日志
                    Common::loadModel('AdminModel');
                    AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $info));

                    echo '<script>alert("关闭成功");</script>';
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 版本管理
     */
    public function getVersion(){

        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : 0;
        $type = $_REQUEST["type"];
        $SevidCfg = Common::getSevidCfg(1);
        $db = Common::getDftDb();

        if($type == "select"){


            $versionInfo = array(
                "id" => 0,
                "channel_id" => "",
                "base_ver" => "",
                "cdn_path" => "",
                "is_constraint" => "",
                "constraint_path" => "",
                "all_version" => "",
                "white_version" => "",
                "server_list_url"=> "",
                "is_ts"=> "",
            );
            if ($id > 0) {

                $sql = "select * from `version_management` where `id`=".$id;
                $versionInfo = $db->fetchRow($sql);
            }

            include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
            return;
        }

        if($type == "update"){

            if ($id > 0) {

                $sql = "UPDATE `version_management` SET
                        `channel_id`='{$_POST['channel_id']}',
                        `base_ver`='{$_POST['base_ver']}',
                        `cdn_path`='{$_POST['cdn_path']}',
                        `is_constraint`='{$_POST['is_constraint']}',
                        `constraint_path`='{$_POST['constraint_path']}',
                        `all_version`='{$_POST['all_version']}',
                        `white_version`='{$_POST['white_version']}' ,
                        `server_list_url`='{$_POST['server_list_url']}' ,
                        `is_ts`='{$_POST['is_ts']}' 
                        where `id`=" . $id;
            }else{
                $sql = "INSERT INTO `version_management` 
                    (`channel_id`,`base_ver`,`cdn_path`,`is_constraint`,`constraint_path`,`all_version`,`white_version`,`server_list_url`,`is_ts`)
                    VALUES ('{$_POST['channel_id']}', '{$_POST['base_ver']}', '{$_POST['cdn_path']}', 
                    '{$_POST['is_constraint']}', '{$_POST['constraint_path']}', '{$_POST['all_version']}', '{$_POST['white_version']}', '{$_POST['server_list_url']}', '{$_POST['is_ts']}')";
            }
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "memcache_key_version_list";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($sql));

            echo '<script>alert("操作成功");</script>';
        }

        if($type == "delete"){

            $sql = "DELETE FROM `version_management` where `id`=".$id;
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "memcache_key_version_list";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_POST["id"]));

            echo '<script>alert("删除成功");</script>';
        }

        $sql = "select `value` from `vo_common` where `key`='version'";
        $versionRes = $db->fetchRow($sql);

        $versionInfo = array();
        if ($versionRes) {
            $versionInfo = json_decode($versionRes["value"], true);
        }

        $versionList = array(
            array(
                "id" => 0,
                "channel_id" => "",
                "base_ver" => "",
                "cdn_path" => "",
                "is_constraint" => "",
                "constraint_path" => "",
                "all_version" => "",
                "white_version" => "",
                "server_list_url" => "",
                "is_ts" => "",
            )
        );

        $versionSql = "SELECT * FROM `version_management` WHERE `id` > 0";
        $rt = $db->query($versionSql);
        while($row = mysql_fetch_assoc($rt)){
            $versionList[]=$row;
        }

        if($_POST['step'] == "all"){

            $oldAll = isset($versionInfo["all"]) ? str_replace(".", "", $versionInfo["all"]) : 0;
            $oldWhite = isset($versionInfo["white"]) ? str_replace(".", "", $versionInfo["white"]) : 0;
            $newAll = str_replace(".", "", $_POST["all"]);
            $newWhite = str_replace(".", "", $_POST["white"]);


            if ( $oldAll > $newAll || $oldWhite > $newWhite ) {

                echo '<script>alert("新版本号不能比之前低!");</script>';
                return false;
            }

            if ($versionInfo) {

                $versionInfo = array("all" => $_POST["all"], "white" => $_POST["white"]);
                // 修改
                $sql = "update `vo_common` set
                    `value`='" . json_encode($versionInfo) . "'
                    where `key`='version' ";
            }else{

                $versionInfo = array("all" => $_POST["all"], "white" => $_POST["white"]);
                // 新增
                $sql = "insert into `vo_common` 
                    (`key`, `value`)
                    values ('version', '" . json_encode($versionInfo) . "')";
            }
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "memcache_key_version";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($versionInfo));

            echo '<script>alert("修改成功");</script>';
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 全服元宝查询
     */
    public function wingSearch(){

        $allList = array();
        $sevidList = array();
        $allDiamond = 0;
        $maxNum = 100;

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $serverid = 0;
        if(!empty($_POST)){

            if(!empty($_POST['serverid'])){
                $serverid = $_POST['serverid'];
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

        $list = array();
        $diamondList = array();
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
            $db = Common::getDftDb();
            $table_div = Common::get_table_div();

            $sqls = array();
            $allSqls = array();
            for ($i = 0 ; $i < $table_div ; $i++)
            {
                //用户表
                $table = 'user_'.Common::computeTableId($i);
                $sqls[] = "SELECT `uid`,`name`,`vip`,`lastlogin`,(`cash_sys` + `cash_buy` - `cash_use`) AS diamond FROM ".$table." WHERE `uid` > 0 ORDER BY `diamond` DESC LIMIT ".$maxNum;

                $allSqls[] = "SELECT (SUM(`cash_sys`) + SUM(`cash_buy`) - SUM(`cash_use`)) AS allDiamond FROM ".$table;
            }


            foreach ($allSqls as $allSql){
                $rt = $db->query($allSql);
                while($row = mysql_fetch_assoc($rt)){
                    $allDiamond += $row["allDiamond"];
                }
            }

            foreach ($sqls as $sql){
                $rt = $db->query($sql);
                while($row = mysql_fetch_assoc($rt)){
                    $list[] = $row;
                    $diamondList[] = $row["diamond"];
                }
            }
        }

        array_multisort($diamondList, SORT_DESC, $list);
        for ($j=0; $j < count($list); $j++) {

            if ($j >= $maxNum || $list[$j]['diamond'] <= 0) {
                break;
            }
            $allList[] = $list[$j];
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 删除沙盒充值记录
     */
    public function delPay(){

        $resList = array();
        if ( isset( $_POST["uids"] )) {

            $uids = explode(',', trim($_POST['uids']));
            $uidStr = implode("','", $uids);

            Common::loadModel('ServerModel');
            $serverList = ServerModel::getServList();

            if (!empty($serverList)) {
                foreach ($serverList as $k => $v) {
                    if (empty($v)) {
                        continue;
                    }

                    $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                    if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                        continue;
                    }

                    $db = Common::getDbBySevId($SevidCfg1['sevid']);
                    $orderSql = "DELETE FROM `t_order` where `roleid` IN ('{$uidStr}')";
                    $orderRes = $db->query($orderSql);
                    if($orderRes){
                        $resList[] = $k;
                    }
                }
            }
            $sevIds = implode("','", $resList);
            echo '<script>alert("删除成功");</script>';
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 白名单
     */
    public function whiteList(){

        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : 0;
        $type = $_REQUEST["type"];
        $SevidCfg = Common::getSevidCfg(1);
        $db = Common::getDftDb();

        if($type == "delete"){

            $sql = "DELETE FROM `white_list` where `id`=".$id;
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "memcache_key_white_list";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_POST["id"]));

            echo '<script>alert("删除成功");</script>';
        }

        if($_POST['step'] == "all"){

            // 新增
            $sql = "insert into `white_list` (`ip`) values ('" . $_POST["ip"] . "')";
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "memcache_key_white";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($versionInfo));

            echo '<script>alert("修改成功");</script>';
        }

        $versionList = array();
        $versionSql = "SELECT * FROM `white_list` WHERE `id` > 0";
        $rt = $db->query($versionSql);
        while($row = mysql_fetch_assoc($rt)){
            $versionList[]=$row;
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 客服中心系统
     */
    public function serviceChat(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        if($_REQUEST['type'] == "close"){

            $uid = $_REQUEST['uid'];
            $sevid = Game::get_sevid($uid);
            $SevidCfg1 = Common::getSevidCfg($sevid);
            $db = Common::getDbBySevId($SevidCfg1['sevid']);

            // 新增
            $sql = "update `service_chat_log` set `is_close` = 1 where `uid` = '" . $uid . "' ";
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array(array('is_close' => 1)));

            echo '<script>alert("修改成功");</script>';
        }

        $sql = "select * from (select * from `service_chat_log` order by `send_time` desc) AS c group by c.`uid`";
        if (isset($_REQUEST['step']) && !empty($_REQUEST['uid'])) {
            $uid = $_REQUEST['uid'];
            $sql = "select * from `service_chat_log` where `uid` = ".$uid." order by `send_time` desc LIMIT 1";
        }

        $list = array();
        $noList = array();
        $okList = array();
        if (!empty($serverList)) {
            foreach ($serverList as $k => $v) {
                if (empty($v)) {
                    continue;
                }

                $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                $data = $db->fetchArray($sql);
                if($data == false) $data = array();

                $list = array_merge($list, $data);
            }
        }
        if (count($list) > 0) {

            $userAccount = include(ROOT_DIR . '/administrator/config/userAccount.php');
            Common::loadModel('UserModel');
            $guan = Game::getCfg('guan');
            foreach ($list as $k => $v) {

                $userModel = new UserModel($v["uid"]);
                $list[$k]["name"] = $userModel->info["name"];
                $list[$k]["vip"] = $userModel->info["vip"];
                $list[$k]["level"] = $guan[$userModel->info["level"]]["name"];
            }

            $flag=array();
            foreach($list as $arr2){
                $flag[] = $arr2["send_time"];
            }
            array_multisort($flag, SORT_DESC, $list);

            foreach ($list as $k => $v) {

                if ($v["is_close"] == 0 && $v["is_service"] == 0) {
                    $v["status"] = "待处理";
                    $noList[] = $v;
                }else{

                    if ($v["is_close"] == 0) {
                        $v["status"] = "已回复";
                    }else{
                        $v["status"] = "已关闭";
                    }
                    $okList[] = $v;
                }
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 客服中心系统
     */
    public function serviceChatReply(){

        $uid = $_REQUEST['uid'];
        $sevid = Game::get_sevid($uid);
        $SevidCfg1 = Common::getSevidCfg($sevid);
        $db = Common::getDbBySevId($sevid);
        if($_REQUEST['reply'] == "reply"){

            $content = $_REQUEST['content'];

            //聊天信息
            $chatData = array(
                "info" => array(
                    'uid' => $uid,
                    'is_service' => 1,
                    'content' => $content,
                    'send_time' => $_SERVER['REQUEST_TIME'],
                    'is_read' => 0
                ),
                "news" => 1
            );
            $sql = "insert into `service_chat_log` set `uid`='{$uid}', `content`='{$content}', `is_service` = 1, `send_time`='{$_SERVER['REQUEST_TIME']}', `from`='{$_SESSION['CURRENT_USER']}'";
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            //数据返回
            Master::back_data($uid, 'chat', "serviceChat", $chatData);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($chatData));

            echo '<script>alert("回复成功");</script>';
        }

        $sql = "select * from `service_chat_log` where `uid` = '".$uid."' order by `id` desc limit 100";
        $list = $db->fetchArray($sql);
        if($list == false) $data = array();

        $userAccount = include(ROOT_DIR . '/administrator/config/userAccount.php');

        Common::loadModel('UserModel');
        $guan = Game::getCfg('guan');
        $userModel = new UserModel($uid);
        $userInfo = array(
            "uid" => $uid,
            "name" => $userModel->info["name"],
            "vip" => $userModel->info["vip"],
            "level" => $guan[$userModel->info["level"]]["name"]
        );

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 成长基金退款
     */
    public function czjijin(){

        $item = array();
        // $order_shop_k = Game::getCfg('order_shop_k');
        $order_shop_k = Game::getOrderShopCfg();
        foreach ($order_shop_k["1"] as $k => $v) {

            if ($v["type"] == 1) {

                $item[$v["dc"]] = array('money' => $v["diamond"],'name'=> '直充-'.$v["dollar"], "type" => 1);
            }
        }

        $gift_bag = Game::getGiftBagCfg();
        foreach ($gift_bag as $k => $v) {

            // if (isset($v["actid"])) {

                $actcoin = 10 * $v["grade"] + 1000000 + 10000 * $v["id"];
                $item["g_".$v["id"]] = array('money' => $v["dollar"],'actcoin' => $actcoin,'name'=> '礼包-'.$v["id"]."-".$v["name"], "type" => 4);
            // }
        }

        if(isset($_REQUEST['uid'])){

            $uid = $_REQUEST['uid'];
            $sevid = Game::get_sevid($uid);
            $SevidCfg1 = Common::getSevidCfg($sevid);

            if (isset($_REQUEST["czjj"])) {

                $Act8010Model = Master::getAct8010($uid);
                $get = $Act8010Model->info["get"];

                $fund = Game::getcfg('fund');
                $diamond = 0;
                foreach ($get as $id => $v) {

                    $fundInfo = $fund[$id];
                    if(!empty($fundInfo)){
                        $diamond += $fundInfo["rwd"][0]["count"];
                    }
                }

                if ($diamond > 0) {

                    Master::sub_item($uid,KIND_ITEM,1,$diamond, false, false, 1, 1);

                    $Act8010Model->resetCzjj();
                    $ActModel = Master::getAct($uid,8010);
                    $ActModel->destroy();

                    $title = "성장기금 환불 처리 안내";
                    $content = "안녕하세요 마마님, 
    요청하신 성장기금 환불로 인한 금화 회수 처리는 완료되었습니다.
    다만, 보유중인 충전 금화가 부족하여 회수 처리가 완료 후 금화 수량이 마이너스로 변경된 점 양해 부탁드립니다.
    ※ 상응한 수량의 금화를 충전하면 마이너스가 정상적으로 표시될 수 있는 점 참고해주시기 바랍니다.

    감사합니다.";
                    $mailModel = Master::getMail($uid);
                    $mailModel->sendMail($uid, $title, $content, 1, array());
                    $cache1 = Common::getCacheByUid($uid);
                    $key = $uid.'_mail';
                    $cache1->delete($key);
                }
            }else if (isset($_REQUEST["grl"])) {

                $Act8011Model = Master::getAct8011($uid);
                $info8011 = $Act8011Model->info;
                $elite = isset($info8011["get"]["elite"]) ? $info8011["get"]["elite"] : array();

                if ($info8011["levelUp"] <= 0) {
                    echo '<script>alert("贵人令未购买");</script>';
                    break;
                }

                $magnate_rwd = Game::getcfg('magnate_rwd');
                foreach ($magnate_rwd as $rk => $rv) {
                    if (in_array($rv["id"], $elite)) {

                        foreach ($rv["jj_rwd"] as $jk => $jv) {

                            $kind = isset($jv['kind']) ? $jv['kind'] : 1;
                            Master::sub_item($uid,$kind,$jv["id"],$jv['count'], false, false, 1, 1);
                        }
                    }
                }

                $Act8011Model->resetGrl();
                $ActModel = Master::getAct($uid,8011);
                $ActModel->destroy();
            }else if (isset($_REQUEST["ngrl"])) {

                $Act8016Model = Master::getAct8016($uid);
                $info8016 = $Act8016Model->info;
                $elite = isset($info8016["get"]["elite"]) ? $info8016["get"]["elite"] : array();

                if ($info8016["levelUp"] <= 0) {
                    echo '<script>alert("新贵人令未购买");</script>';
                    break;
                }

                $magnate_new_rwd = Game::getcfg('magnate_new_rwd');
                foreach ($magnate_new_rwd as $rk => $rv) {
                    if (in_array($rv["id"], $elite)) {

                        foreach ($rv["jj_rwd"] as $jk => $jv) {

                            $kind = isset($jv['kind']) ? $jv['kind'] : 1;
                            Master::sub_item($uid,$kind,$jv["id"],$jv['count'], false, false, 1, 1);
                        }
                    }
                }

                $Act8016Model->resetGrl();
                $ActModel = Master::getAct($uid,8016);
                $ActModel->destroy();
            }else if (isset($_REQUEST["zctk"])) {

                $id = trim($_POST['item']);
                if ($item[$id]['type'] == 1) {

                    $diamond = $item[$id]['money'];
                    Master::sub_item($uid,1,1,$diamond, false, false, 1, 1);
                }else{

                    foreach ($gift_bag as $k => $v) {

                        $gid = "g_".$v["id"];
                        if ( $id == $gid ){

                            foreach ($v["items"] as $ik => $iv) {
                                Master::sub_item($uid,$iv["kind"],$iv["id"],$iv["count"], false, false, 1, 1);
                            }
                        }
                    }
                }
            }

            echo '<script>alert("修改成功");</script>';
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * deeplink
     */
    public function getDeeplink(){

        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : 0;
        $type = $_REQUEST["type"];
        $SevidCfg = Common::getSevidCfg(1);
        $db = Common::getDftDb();

        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $category = $GameActTemplateModel->getCategory();
        $list = $GameActTemplateModel->getInfoByCate($category[0]);

        $actList = array();
        $actList["huodong_0"] = array("id" => 0,"act_key" => "huodong_0","title" => "mainHome");
        foreach ($list as $val){

            $actList[$val['act_key']] = array("id" => $val['id'],"act_key" => $val['act_key'],"title" => $val['title']);
        }

        if($type == "select"){

            $deeplinkInfo = array(
                "id" => 0,
                "stime" => "",
                "etime" => "",
                "url_path" => "",
                "actid" => ""
            );
            if ($id > 0) {

                $sql = "select * from `deeplink` where `status` = 0 AND `id`=".$id;
                $deeplinkInfo = $db->fetchRow($sql);
            }

            include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
            return;
        }

        if($type == "update"){

            $startTime = strtotime($_POST['startTime']);
            $endTime = strtotime($_POST['endTime']);

            if ($id > 0) {

                $sql = "UPDATE `deeplink` SET
                        `stime`={$startTime},
                        `etime`={$endTime},
                        `url_path`='{$_POST['url_path']}',
                        `actid`='{$_POST['actid']}'
                        where `id`=" . $id;
            }else{
                $sql = "INSERT INTO `deeplink` (`stime`,`etime`,`url_path`,`actid`)
                    VALUES ('{$startTime}', '{$endTime}', '{$_POST['url_path']}', '{$_POST['actid']}')";
            }

            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "deeplink_list";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($sql));

            echo '<script>alert("操作成功");</script>';
        }

        if($type == "delete"){

            $sql = "UPDATE `deeplink` SET `status` = 1 where `id`=".$id;
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
                return false;
            }

            $cacheKey = "deeplink_list";
            $cache = Common::getDftMem ();
            $cache->delete($cacheKey);

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_POST["id"]));

            echo '<script>alert("删除成功");</script>';
        }

        $deeplinkList = array(
            array(
                "id" => 0,
                "stime" => "",
                "etime" => "",
                "url_path" => "",
                "actid" => "",
            )
        );

        $versionSql = "SELECT * FROM `deeplink` WHERE `status` = 0";
        $rt = $db->query($versionSql);
        while($row = mysql_fetch_assoc($rt)){
            $deeplinkList[]=$row;
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 全服元宝日统计
     */
    public function userDiamond(){

        $list = array();
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime = strtotime(date('Y-m-d 23:59:59'));
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

            $db = Common::getDbBySevId(1);
            $sql = "SELECT * FROM `user_diamond_day_log` WHERE `sevId` >= $sevId1 AND `sevId` <= $sevId2 AND `dayTime` >= $startTime AND `dayTime` <= $endTime";
            $list = $db->fetchArray($sql);
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
}
