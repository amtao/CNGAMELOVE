<?php
/**
 * 信息管理
 * Class Buryingport
 */
class Buryingport
{
    /**
     * 全服数据基础界面
     */
    public function index() {
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 消耗道具统计
     */
    public function itemConsumeAll(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $vipList = Game::getcfg("vip");

        $itemList = array();
        if(!empty($_POST)){

            $itemInfo = array();
            $serverid = $_POST['serverid'];
            $vipLevel = $_POST['vipLevel'];
            $table_div = Common::get_table_div();

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

                $dbFlow = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                for ($i = 0; $i < $table_div; $i++) {
                    $index = Common::computeTableId($i);
                    $tableUser = 'user_'. $index;
                    $userSql = "SELECT `uid` FROM {$tableUser} WHERE vip >= {$vipLevel}";
                    $uids = $db->fetchArray($userSql);
                    $uidArr = array();
                    foreach($uids as $uidK => $uidV){
                        $uidArr[] = $uidV['uid'];
                    }
                    $uidStr = implode('","', $uidArr);

                    $table = 'flow_records_' . $index;
                    $table1 = 'flow_event_' . $index;
                    $sql = "SELECT `itemid`, SUM(`cha`) AS count FROM {$table} WHERE `itemid` > 0 AND `cha` < 0 AND `type` = 6 GROUP BY `itemid`;";

                    $sqlVip = "SELECT A.itemid,SUM(A.cha) as cha FROM {$table} as A LEFT JOIN {$table1} as B on A.flowid = B.id WHERE A.type = 6 AND A.cha < 0 AND B.uid IN (".$uidStr.") GROUP BY A.itemid";
                    $result = $dbFlow->fetchArray($sql);
                    $resultVip = $dbFlow->fetchArray($sqlVip);
                    if (is_array($result)){
                        foreach ($result as $k => $v){

                            if (isset($itemInfo[$v["itemid"]]['count'])) {
                                $itemInfo[$v["itemid"]]['count'] += $v["count"];
                            }else{
                                $itemInfo[$v["itemid"]]['count'] = $v["count"];
                            }
                        }
                    }
                    if(is_array($resultVip)){
                        foreach($resultVip as $k => $v){
                            if (isset($itemInfo[$v["itemid"]]['vipCount'])) {
                                $itemInfo[$v["itemid"]]['vipCount'] += $v["cha"];
                            }else{
                                $itemInfo[$v["itemid"]]['vipCount'] = $v["cha"];
                            }
                        }
                    }
                }
            }

            $itemConfig = Game::getcfg('item');
            foreach ($itemInfo as $key => $value) {
                $itemList[] = array("itemid" => $key, "count" => abs($value['count']), "name" => $itemConfig[$key]["name_cn"],"vipcount" => abs($value['vipCount']));
            }
        }
        sort($itemList);

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 卡牌统计
     */
    public function cardAll(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        $total = 0;
        if(!empty($_POST)){

            $serverid = $_POST['serverid'];
            $stype = intval($_POST['stype']);
            $table_div = Common::get_table_div();

            $min = 0;
            $max = 0;
            $cfg_item = Game::getcfg('item');
            foreach ($cfg_item as $k => $v) {
                if (!isset($v["classify"]) || $v["classify"] != 1) continue;

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
                if ( 0 < $serverid && $serverid != $SevidCfg1['sevid'] ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                for ($i = 0; $i < $table_div; $i++) {
                    $table = 'card_' . Common::computeTableId($i);

                    switch ($stype) {
                        case 2:
                            $sql = "SELECT `star` AS id, COUNT(`cardid`) AS count FROM {$table} WHERE `star` >= 0 GROUP BY `star`;";
                            break;
                        case 3:
                            $sql = "SELECT `level` AS id, COUNT(`cardid`) AS count FROM {$table} WHERE `level` > 0 GROUP BY `level`;";
                            break;
                        case 4:
                            $table = 'item_' . Common::computeTableId($i);
                            $sql = "SELECT `itemid` AS id, SUM(`count`) AS count FROM {$table} WHERE `itemid` >= {$min} AND `itemid` <= {$max} GROUP BY `itemid`;";
                            break;
                        default:
                            $sql = "SELECT `cardid` AS id, COUNT(`cardid`) AS count FROM {$table} WHERE `cardid` > 0 GROUP BY `cardid`;";
                            break;
                    }
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach ($result as $k => $v){

                            $total += $v["count"];
                            if (isset($dataInfo[$v["id"]])) {
                                $dataInfo[$v["id"]] += $v["count"];
                            }else{
                                $dataInfo[$v["id"]] = $v["count"];
                            }
                        }
                    }
                }
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
     * 四海奇珍统计
     */
    public function baowuAll(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        $total = 0;
        if(!empty($_POST)){

            $serverid = $_POST['serverid'];
            $stype = intval($_POST['stype']);
            $table_div = Common::get_table_div();

            $min = 0;
            $max = 0;
            $cfg_item = Game::getcfg('item');
            foreach ($cfg_item as $k => $v) {
                if (!isset($v["classify"]) || $v["classify"] != 1) continue;

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
                if ( 0 < $serverid && $serverid != $SevidCfg1['sevid'] ) {
                    continue;
                }

                if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
                    continue;
                }

                $db = Common::getDbBySevId($SevidCfg1['sevid']);
                for ($i = 0; $i < $table_div; $i++) {
                    $table = 'baowu_' . Common::computeTableId($i);

                    switch ($stype) {
                        case 2:
                            $sql = "SELECT `star` AS id, COUNT(`baowuid`) AS count FROM {$table} WHERE `star` >= 0 GROUP BY `star`;";
                            break;
                        case 3:
                            $sql = "SELECT `level` AS id, COUNT(`baowuid`) AS count FROM {$table} WHERE `level` > 0 GROUP BY `level`;";
                            break;
                        case 4:
                            $table = 'item_' . Common::computeTableId($i);
                            $sql = "SELECT `itemid` AS id, SUM(`count`) AS count FROM {$table} WHERE `itemid` >= {$min} AND `itemid` <= {$max} GROUP BY `itemid`;";
                            break;
                        default:
                            $sql = "SELECT `baowuid` AS id, COUNT(`baowuid`) AS count FROM {$table} WHERE `baowuid` > 0 GROUP BY `baowuid`;";
                            break;
                    }
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach ($result as $k => $v){

                            $total += $v["count"];
                            if (isset($dataInfo[$v["id"]])) {
                                $dataInfo[$v["id"]] += $v["count"];
                            }else{
                                $dataInfo[$v["id"]] = $v["count"];
                            }
                        }
                    }
                }
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 兑换商城
     */
    public function exchangeshop(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        $totalCount = 0;
        $totalTimes = 0;
        $stype = intval($_POST['stype']);
        if(!empty($_POST)){

            $serverid = $_POST['serverid'];
            $table_div = Common::get_table_div();
            
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                $cfgArr = array();
                for ($i = 0; $i < $table_div; $i++) {
                    $table = 'flow_event_' . Common::computeTableId($i);
                    switch ($stype) {
                        case 2://赴约
                            $sql = "SELECT `params` FROM {$table} WHERE `model` = 'fuyue' AND `ctrl` = 'exchange';";
                            $cfgArr = Game::getcfg('dui_huan');
                            break;
                        case 3://郊祀献礼
                            $sql = "SELECT `params` FROM {$table} WHERE `model` = 'wordboss' AND `ctrl` = 'shopBuy';";
                            $cfgArr = Game::getcfg('wordboss_shop');
                            break;
                        case 4://商城
                            $sql = "SELECT `params` FROM {$table} WHERE `model` = 'shop' AND `ctrl` = 'shopLimit';";
                            Common::loadModel('HoutaiModel');
                            $hd_cfg = HoutaiModel::get_huodong_info('huodong_81');
                            $cfgArr = $hd_cfg['rwd'];
                            break;
                        default://宫斗
                            $sql = "SELECT `params` FROM {$table} WHERE `model` = 'yamen' AND `ctrl` = 'exchange';";
                            $cfgArr = Game::getcfg('gongdou_exchange');
                            break;
                    }
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach ($result as $k => $v){
                            $totalTimes += 1;
                            $addCount = 0;
                            $jsonParams = json_decode($v['params'], true);
                            if(!isset($jsonParams['num']) && !isset($jsonParams['count'])){
                                $addCount++;
                            }elseif(!empty($jsonParams['num']) && $stype != 4){
                                $addCount = $jsonParams['num'];
                            }else{
                                $addCount = $jsonParams['count'];
                            }
                            if (isset($dataInfo[$jsonParams["id"]]['count'])) {
                                $dataInfo[$jsonParams["id"]]['count'] += $addCount;
                            }else{
                                $dataInfo[$jsonParams["id"]]['count'] = $addCount;
                            }
                            if (isset($dataInfo[$v["id"]]['times'])) {
                                $dataInfo[$jsonParams["id"]]['times'] += 1;
                            }else{
                                $dataInfo[$jsonParams["id"]]['times'] = 1;
                            }
                        }
                    }
                }
            };
            foreach ($dataInfo as $key => $value) {
                if($stype == 4){
                    $itemid = $cfgArr[$key]['item']['id'];
                    $itemCfg = Game::getcfg_info("item",$itemid);
                    $dataList[] = array("itemid" => $itemid,"name" => $itemCfg['name_cn'], "count" => $value['count'],"times" => $value['times']);
                }else{
                    if(empty($cfgArr[$key]['rwd'])){
                        $itemCfg = Game::getcfg_info("item",$cfgArr[$key]['itemid']);
                        $dataList[] = array("itemid" => $cfgArr[$key]['itemid'],"name" => $itemCfg['name_cn'], "count" => $value['count'],"times" => $value['times']);
                    }else{
                        foreach($cfgArr[$key]['rwd'] as $v){
                            $itemCfg = Game::getcfg_info("item",$v['id']);
                            $dataList[] = array("itemid" => $v['id'],"name" => $itemCfg['name_cn'], "count" => $value['count'],"times" => $value['times']);
                        }
                    }
                }                
            }
        }
        if(!empty($dataList)){
            sort($dataList);
        }
        

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //头像-头像框获取
    public function headTotal(){
        //查看
        //拥有的头像框
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        if(!empty($_POST)){

            $serverid = $_POST['serverid'];
            $table_div = Common::get_table_div();
            $headInfo = array();
            $headblankInfo = array();
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
                for ($i = 0; $i < $table_div; $i++) {
                    $table = "act_".Common::computeTableId($i);
                    $sql = "SELECT `tjson` FROM {$table} WHERE `actid` = '6150';";
                    $result = $db->fetchArray($sql);
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            $jsons = json_decode($v['tjson'], true);
                            foreach($jsons['data']['blanks'] as $id){
                                if(empty($headblankInfo[$id])){
                                    $headblankInfo[$id] = 1;
                                }else{
                                    $headblankInfo[$id] += 1;
                                }
                            }
                        }
                    }
                    $sql1 = "SELECT `tjson` FROM {$table} WHERE `actid` = '6151';";
                    $result = $db->fetchArray($sql1);
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            $jsons = json_decode($v['tjson'], true);
                            if(empty($jsons['data']['head'])){
                                $headInfo[$jsons['data']['head']] = 1;
                            }else{
                                $headInfo[$jsons['data']['head']] += 1;
                            }
                        }
                    }
                }
            }
        }
        if(!empty($headblankInfo)){
            ksort($headblankInfo);
        }
        if(!empty($headInfo)){
            ksort($headInfo);   
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //档位领取信息
    public function gearPick(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $dataInfo = array();
        if(!empty($_POST)){
            $startTime = strtotime($_POST['beginDate']);
            $endTime = strtotime($_POST['endDate']);
             if(empty($startTime)){
            	$startTime = 0;
            }
            if(empty($endTime)){
            	$endTime = Game::get_now();
            }
            $serverid = $_POST['serverid'];
            $stype = $_POST['stype'];
            $table_div = Common::get_table_div();
            
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                for ($i = 0; $i < $table_div; $i++) {
                    $table = "flow_event_".Common::computeTableId($i);
                    switch ($stype) {
                        case 2:
                            $sql = "SELECT `model`,`ctrl`,`params` FROM {$table} WHERE `model` = 'daily' AND `ctrl` = 'gettask' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break;
                        case 3:
                            $sql = "SELECT `model`,`ctrl`,`params` FROM {$table} WHERE `model` = 'daily' AND `ctrl` = 'getrwd' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break;
                        default:
                            $sql = "SELECT `model`,`ctrl`,`params` FROM {$table} WHERE `model` = 'chengjiu' AND `ctrl` = 'rwd' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break; 
                    }
                    $result = $db->fetchArray($sql);
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            $jsons = json_decode($v['params'], true);
                            if(empty($dataInfo[$jsons['id']])){
                                $dataInfo[$jsons['id']] = 1;
                            }else{
                                $dataInfo[$jsons['id']] += 1;
                            }
                        }
                    }
                }
            }
        }
        ksort($dataInfo);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function heroInfo(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        $jbInfo = array();
        $clotheInfo = array();
        $tempArr = array();
        $stype = intval($_POST['stype']);
        if(!empty($_POST)){

            $serverid = $_POST['serverid'];
            $table_div = Common::get_table_div();
            
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
                for ($i = 0; $i < $table_div; $i++) {
                    $table = 'act_' . Common::computeTableId($i);
                    switch ($stype) {
                        case 2://伙伴星级
                            $table = 'hero_' . Common::computeTableId($i);
                            $sql = "SELECT `heroid`,`star`,count(`star`) as count FROM {$table} GROUP BY `heroid`,`star`;";
                            break;
                        case 3://伙伴羁绊等级
                            $sql = "SELECT `tjson` FROM {$table} WHERE `actid` = '6001';";
                            break;
                        default://伙伴时装
                            $sql = "SELECT `tjson` FROM {$table} WHERE `actid` = '6143';";
                            break;
                    }
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach($result as $k=>$v){
                            switch ($stype) {
                                case 2://伙伴星级
                                    //dataInfo
                                    if(empty($dataInfo[$v['heroid']][$v['star']])){
                                        $dataInfo[$v['heroid']][$v['star']] = $v['count'];
                                    }else{
                                        $dataInfo[$v['heroid']][$v['star']] += $v['count'];
                                    }
                                    $tempArr = $dataInfo;
                                    break;
                                case 3://伙伴羁绊等级
                                    $jsons = json_decode($v['tjson'], true);
                                    foreach($jsons['data']['heroJB'] as $jb){
                                        $hero = Game::getcfg_info('hero', $jb['id']);
                                        $list = Game::getcfg('jinban_lv');
                                        $lv = 1;
                                        foreach ($list as $v){
                                            if ($v['yoke'] <= $jb['num'] && $v['star'] == $hero['star']){
                                                $lv = intval($v['level']/1000);
                                            }
                                        }
                                        //jbInfo
                                        if(empty($jbInfo[$jb['id']][$lv])){
                                            $jbInfo[$jb['id']][$lv] = 1;
                                        }else{
                                            $jbInfo[$jb['id']][$lv] += 1;
                                        }
                                    }
                                    $tempArr = $jbInfo;
                                    break;
                                default://伙伴时装
                                    $jsons = json_decode($v['tjson'], true);
                                    foreach($jsons['data']['clothes'] as $clotheid){
                                        $heroDress = Game::getcfg_info('hero_dress', $clotheid);
                                        //clotheInfo
                                        if(empty($clotheInfo[$heroDress['heroid']][$clotheid])){
                                            $clotheInfo[$heroDress['heroid']][$clotheid] = 1;
                                        }else{
                                            $clotheInfo[$heroDress['heroid']][$clotheid] += 1;
                                        }
                                    }
                                    $tempArr = $clotheInfo;
                                    break;
                            }
                        }
                    }
                }
            }
            foreach ($tempArr as $key => $value) {
            	foreach($value as $k => $v){
            		$dataList[] = array("heroid" => $key,"id"=> $k,"count"=> $v);	
            	}
            }
        }
        if(!empty($dataList)){
            sort($dataList);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //伙伴出游
    public function travel(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        if(!empty($_POST)){

            $serverid = $_POST['serverid'];
            $table_div = Common::get_table_div();
            
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                for ($i = 0; $i < $table_div; $i++) {
                    $table = 'flow_event_' . Common::computeTableId($i);
                    $table1 = 'flow_records_'.Common::computeTableId($i);
                    $sql = "SELECT A.`ctrl`,A.`params`,B.`flowid`,B.`cha` FROM {$table} as A LEFT JOIN {$table1} as B on A.`id` = B.`flowid` WHERE B.`itemid` = '2' AND A.`model` = 'hero' AND A.`ctrl` = 'xxoo' OR A.`ctrl` = 'xxoonobaby';";
                    $result = $db->fetchArray($sql);
                    if (is_array($result)){
                        foreach ($result as $k => $v){
                            $jsonParams = json_decode($v['params'],true);
                            if($v['ctrl'] == "xxoonobaby"){
                                if(empty($dataInfo[$jsonParams['id']]['wenhou'])){
                                    $dataInfo[$jsonParams['id']]['wenhou'] = $v['cha'];
                                }else{
                                    $dataInfo[$jsonParams['id']]['wenhou'] += $v['cha'];
                                }
                            }else{
                                if(empty($dataInfo[$jsonParams['id']]['chuyou'])){
                                    $dataInfo[$jsonParams['id']]['chuyou'] = $v['cha'];
                                }else{
                                    $dataInfo[$jsonParams['id']]['chuyou'] += $v['cha'];
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach ($dataInfo as $key => $value) {
            $dataList[] = array("heroid" => $key, "wenhou" => abs($value['wenhou']), "chuyou" => abs($value['chuyou']));
        }
        if(!empty($dataList)){
            sort($dataList);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //其他档位领取信息
    public function otherGearPick(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        $zeroInfo = array();
        $signInfo = array();
        if(!empty($_POST)){
            $startTime = strtotime($_POST['beginDate']);
            $endTime = strtotime($_POST['endDate']);
                if(empty($startTime)){
                $startTime = 0;
            }
            if(empty($endTime)){
                $endTime = Game::get_now();
            }
            $serverid = $_POST['serverid'];
            $stype = $_POST['stype'];
            $table_div = Common::get_table_div();
   
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                if($stype == 2){
                    $sql1 = "SELECT `num`,SUM(`num`) AS totalCount FROM flow_consume WHERE `from` = 'fuli' AND `other`= 'buyZeroGift' GROUP BY `num`;";
                    $result1 = $db->fetchArray($sql1);
                    //获取0元购消耗金额
                    if(is_array($result1)){
                        foreach($result1 as $k=>$v){
                            if(empty($zeroInfo[$v['num']])){
                                $zeroInfo[$v['num']] = $v['totalCount'];
                            }else{
                                $zeroInfo[$v['num']] += $v['totalCount'];
                            }
                        }
                    }
                }
                for ($i = 0; $i < $table_div; $i++) {
                    $table = "flow_event_".Common::computeTableId($i);
                    switch ($stype) {
                        case 2://0元购
                            //获取0元购消耗
                            //获取领取状态
                            $sql = "SELECT `params` FROM {$table} WHERE model = 'fuli' AND ctrl = 'pickZeroRebate' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break;
                        case 3://首充
                            //领取首充档位
                            $sql = "SELECT `params` FROM {$table} WHERE `model` = 'fuli' AND `ctrl` = 'fcho_ex' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break;
                        case 4://钱庄
                            $sql = "SELECT `params` FROM {$table} WHERE `model` = 'fuli' AND `ctrl` = 'pickBankAward' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break;
                        default://签到
                            //获取每周签到
                            $sql = "SELECT `params` FROM {$table} WHERE model = 'fuli' AND ctrl = 'monday' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            //获取签到人数
                            $sql2 = "SELECT `ftime` FROM {$table} WHERE model = 'fuli' AND ctrl = 'qiandao' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break; 
                    }
                    $result = $db->fetchArray($sql);
                    $result2 = $db->fetchArray($sql2);

                    //获取每日签到人数
                    if(is_array($result2)){
                        foreach($result2 as $k=>$v){
                            $time = date('Y-m-d', $v['ftime']);
                            if(empty($signInfo[$time])){
                                $signInfo[$time] = 1;
                            }else{
                                $signInfo[$time] += 1;
                            }
                        }
                    }
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            $jsons = json_decode($v['params'], true);
                            if($jsons['id'] == 0){
                            	continue;
                            }
                            if(empty($dataInfo[$jsons['id']])){
                                $dataInfo[$jsons['id']] = 1;
                            }else{
                                $dataInfo[$jsons['id']] += 1;
                            }
                        }
                    }
                }
            }
        }
        ksort($dataInfo);
        ksort($zeroInfo);
        ksort($signInfo);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    //购买礼包
    public function buygift(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $dataInfo = array();
        $zeroInfo = array();
        $signInfo = array();
        $dataList = array();
        if(!empty($_POST)){
            $startTime = strtotime($_POST['beginDate']);
            $endTime = strtotime($_POST['endDate']);
                if(empty($startTime)){
                $startTime = 0;
            }
            if(empty($endTime)){
                $endTime = Game::get_now();
            }
            $serverid = $_POST['serverid'];
            $stype = $_POST['stype'];
            $table_div = Common::get_table_div();
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                if($stype == 2){
                    $sql = "SELECT `num`,count(`id`) AS total FROM flow_consume WHERE `from` = 'fuli' AND `other`= 'buy' GROUP BY `num`;";
                    $result = $db->fetchArray($sql);
                    //vip勋贵礼包购买
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            if(empty($dataInfo[$v['num']])){
                                $dataInfo[$v['num']] = $v['total'];
                            }else{
                                $dataInfo[$v['num']] += $v['total'];
                            }
                        }
                    }
                }else{
                    for ($i = 0; $i < $table_div; $i++) {
                        $table = "flow_event_".Common::computeTableId($i);
                        switch ($stype) {
                            default://签到
                                //冲榜礼包购买
                                $sql = "SELECT `ctrl`,`params` FROM {$table} WHERE `model` = 'huodong' AND `ctrl` LIKE 'hd%buy' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                                break; 
                        }
                        $result = $db->fetchArray($sql);
                        if(is_array($result)){
                            foreach($result as $k=>$v){
                                $jsons = json_decode($v['params'], true);
                                
                                if(empty($dataInfo[$v['ctrl']][$jsons['id']])){
                                    $dataInfo[$v['ctrl']][$jsons['id']] = 1;
                                }else{
                                    $dataInfo[$v['ctrl']][$jsons['id']] += 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach($dataInfo as $key =>$value){
            if(is_array($value)){
                foreach($value as $k => $v){
                    $dataList[] = array("id" => $key,"giftId" => $k,"count"=>$v);
                }
            }else{
                $dataList[] = array("id" => $key,"count"=>$value);
            }
            
        }
        sort($dataList);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //购买月卡周卡
    public function monthweek(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $dataInfo = array();
        $distinctArr = array();
        $dataList = array();
        if(!empty($_POST)){
            $startTime = strtotime($_POST['beginDate']);
            $endTime = strtotime($_POST['endDate']);
                if(empty($startTime)){
                $startTime = 0;
            }
            if(empty($endTime)){
                $endTime = Game::get_now();
            }
            $serverid = $_POST['serverid'];
            $table_div = Common::get_table_div();
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                
                for ($i = 0; $i < $table_div; $i++) {
                    $table = "flow_event_".Common::computeTableId($i);
                    $sql = "SELECT `uid`,`ctrl`,`params` FROM {$table} WHERE `model` = 'fuli' AND (`ctrl`='monthCard' OR `ctrl`='weekCard') AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                    $result = $db->fetchArray($sql);
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            $jsons = json_decode($v['params'], true);          
                            if(empty($dataInfo[$v['ctrl']][$jsons['id']])){
                                $dataInfo[$v['ctrl']][$jsons['id']]['count'] = 1;
                            }else{
                                $dataInfo[$v['ctrl']][$jsons['id']]['count'] += 1;
                            }
                            if(in_array($v['uid'],$distinctArr[$v['ctrl']][$v['uid']])){
                                $dataInfo[$v['ctrl']][$jsons['id']]['total'] += 1;
                                array_push($distinctArr[$v['ctrl']],$v['uid']);
                            }
                        }
                    }
                }
            }
        }
        foreach($dataInfo as $key =>$value){
            if(is_array($value)){
                foreach($value as $k => $v){
                    $dataList[] = array("id" => $key,"count" => $v['count'],"totalCount"=>$v['total']);
                }
            }
        }
        sort($dataList);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //其他的埋点需求
    public function gearConsume(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $dataInfo = array();
        $cityArr = array();
        $qifuArr = array();
        $businessArr = array();
        $dataList = array();
        if(!empty($_POST)){
            $startTime = strtotime($_POST['beginDate']);
            $endTime = strtotime($_POST['endDate']);
                if(empty($startTime)){
                $startTime = 0;
            }
            if(empty($endTime)){
                $endTime = Game::get_now();
            }
            $serverid = $_POST['serverid'];
            $stype = $_POST['stype'];
            $table_div = Common::get_table_div();
   
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

                $db = Common::getDbBySevId($SevidCfg1['sevid'],'flow');
                for ($i = 0; $i < $table_div; $i++) {
                    $table = "flow_event_".Common::computeTableId($i);
                    switch ($stype) {
                        case 2://献礼
                            $sql = "SELECT `params` FROM {$table} WHERE model = 'wordboss' AND ctrl = 'hitgeerdan' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break;
                        case 3://出城
                            $table1 = "flow_records_".Common::computeTableId($i);
                            $sql = "SELECT A.`params`,B.`cha` FROM {$table} AS A LEFT JOIN {$table1} AS B ON A.`id` = B.`flowid` WHERE `model` = 'xunfang' AND `ctrl` = 'xunfan' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime} AND B.`itemid` = 2 AND B.`itemid` = 2 AND B.`cha` < 0;";
                            break;
                        case 4://祈福
                            $table1 = "flow_records_".Common::computeTableId($i);
                            $sql = "SELECT A.`ctrl`,A.`params`,B.`cha` FROM {$table} AS A LEFT JOIN {$table1} AS B ON A.`id` = B.`flowid` WHERE A.`model` = 'user' AND (A.`ctrl` = 'qifu' OR A.`ctrl` = 'qifuTen') AND A.`ftime` >= {$startTime} AND A.`ftime` <= {$endTime} AND B.`itemid` = 2 AND B.`itemid` = 2 AND B.`cha` < 0;";
                            break;
                        default://行商
                            $sql = "SELECT `params` FROM {$table} WHERE model = 'business' AND ctrl = 'pickAwardGear' AND `ftime` >= {$startTime} AND `ftime` <= {$endTime};";
                            break; 
                    }
                    $result = $db->fetchArray($sql);
                    if(is_array($result)){
                        foreach($result as $k=>$v){
                            $jsons = json_decode($v['params'], true);
                            switch ($stype) {
                                case 2://献礼
                                    if(empty($dataInfo[$jsons['type']][$jsons['id']])){
                                        $dataInfo[$jsons['type']][$jsons['id']] = 1;
                                    }else{
                                        $dataInfo[$jsons['type']][$jsons['id']] += 1;
                                    }
                                    break;
                                case 3://出城
                                    $city = $jsons['type']%100;
                                    if(empty($cityArr[$city])){
                                        $cityArr[$city] = $v['cha'];
                                    }else{
                                        $cityArr[$city] += $v['cha'];
                                    }
                                    break;
                                case 4://祈福

                                    if(empty($qifuArr[$v['ctrl']][$jsons['jyid']])){
                                        $qifuArr[$v['ctrl']][$jsons['jyid']] = $v['cha'];
                                    }else{
                                        $qifuArr[$v['ctrl']][$jsons['jyid']] += $v['cha'];
                                    }
                                    break;
                                default://行商
                                    if(empty($businessArr[$jsons['gear']])){
                                        $businessArr[$jsons['gear']] = 1;
                                    }else{
                                        $businessArr[$jsons['gear']] += 1;
                                    }
                                    break; 
                            }
                        }
                    }
                }
            }
        }
        foreach($dataInfo as $key => $value){
            foreach($value as $k => $v){
                $dataList[] = array("type" => $key,"id" => $k,"count" => $v);
            }
        }
        foreach($cityArr as $key => $value){
            $dataList[] = array("type" => $key,"id" => $key,"count" => abs($value));
        }
        foreach($qifuArr as $key => $value){
            foreach($value as $k => $v){
                $dataList[] = array("type" => $key,"id" => $k,"count" => abs($v));
            }
        }
        foreach($businessArr as $key => $value){
            $dataList[] = array("type" => $key,"id" => $key,"count" => $value);
        }
        sort($dataList);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
}