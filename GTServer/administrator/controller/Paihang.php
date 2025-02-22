<?php
class Paihang
{
    
    public function index(){
        $SevidCfg = Common::getSevidCfg($_GET['sevid']);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    public function all () {
        $SevidCfg = Common::getSevidCfg();
    	$redis1Model = Master::getRedis1();
    	$redis1Model->back_data();
    	$redis2Model = Master::getRedis2();
    	$redis2Model->back_data();
    	
    	$redis3Model = Master::getRedis3();
    	$redis3Model->back_data();
    	
//    	$redis8Model = Master::getRedis8();
//    	$redis8Model->back_data();
    	$redis10Model = Master::getRedis10();
    	$redis10Model->back_data();

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

	public function shili(){
		$Redis1Model = Master::getRedis1();
        if (!empty($_REQUEST['uid'])){
            $Redis1Model->del_member($_REQUEST['uid']);
            $cache = Common::getDftMem();
            $cache->delete($Redis1Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $_REQUEST['uid']));
        }
		$SevidCfg = Common::getSevidCfg();
		$Redis1Model->back_data();
		$shili = $Redis1Model->outf;
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	public function qinmi(){
		$Redis3Model = Master::getRedis3();
        if (!empty($_REQUEST['uid'])){
            $Redis3Model->del_member($_REQUEST['uid']);
            $cache = Common::getDftMem();
            $cache->delete($Redis3Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $_REQUEST['uid']));
        }
		$SevidCfg = Common::getSevidCfg();
		$Redis3Model->back_data();
		$qinmi = $Redis3Model->outf;
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	public function guanka(){
		$Redis2Model = Master::getRedis2();
        if (!empty($_REQUEST['uid'])){
            $Redis2Model->del_member($_REQUEST['uid']);
            $cache = Common::getDftMem();
            $cache->delete($Redis2Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $_REQUEST['uid']));
        }
		$SevidCfg = Common::getSevidCfg();
		$Redis2Model->back_data();
		$guanka = $Redis2Model->outf;
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}

    public function yamen(){
        $Redis6Model = Master::getRedis6();
        if (!empty($_REQUEST['uid'])){
            $Redis6Model->del_member($_REQUEST['uid']);
            $cache = Common::getDftMem();
            $cache->delete($Redis6Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $_REQUEST['uid']));
        }
        $SevidCfg = Common::getSevidCfg();
        $Redis6Model->back_data();
        $yamen = $Redis6Model->outf;
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

	public function gonghui(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        $serverid = 1;
        if (!empty($_REQUEST['serverid'])){
            $serverid = $_REQUEST['serverid'];
        }

        $SevidCfg = Common::getSevidCfg($serverid);
        $Redis10Model = Master::getRedis10();
        if (!empty($_REQUEST['id'])){
            Common::loadModel('ClubModel');
            $clubModel =  new ClubModel($_REQUEST['id']);
            $Redis10Model->del_member($_REQUEST['id']);
            $cache = Common::getDftMem();
            $cache->delete($Redis10Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $_REQUEST['id']));
        }
        $Redis10Model->back_data();
        $gonghui = $Redis10Model->outf;

        foreach ($gonghui as $key => $value) {

            $gonghui[$key]["membersDz"] = "";
            $gonghui[$key]["membersSl"] = count($value["members"]);
            foreach ($value["members"] as $k => $v) {
                if ($v["post"] == 1) {
                    $gonghui[$key]["membersDz"] = $v["name"];
                    break;
                }
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function delete(){
        $type = $_REQUEST['type'];
        $id = $_REQUEST['id'];
        if ($type == 'gonghui'){
            $Redis10Model = Master::getRedis10();
            $Redis10Model->del_member($id);
            $cache = Common::getDftMem();
            $cache->delete($Redis10Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($type => $id));
            echo '删除成功';
        }elseif ($type == 'yamen'){
            $Redis6Model = Master::getRedis6();
            $Redis6Model->del_member($id);
            $cache = Common::getDftMem();
            $cache->delete($Redis6Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($type => $id));
            echo '删除成功';

        }elseif ($type == 'qinmi'){
            $Redis3Model = Master::getRedis3();
            $Redis3Model->del_member($id);
            $cache = Common::getDftMem();
            $cache->delete($Redis3Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($type => $id));
            echo '删除成功';

        }elseif ($type == 'shili'){
            $Redis1Model = Master::getRedis1();
            $Redis1Model->del_member($id);
            $cache = Common::getDftMem();
            $cache->delete($Redis1Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($type => $id));
            echo '删除成功';
        }elseif ($type == 'guanka'){
            $Redis2Model = Master::getRedis2();
            $Redis2Model->del_member($id);
            $cache = Common::getDftMem();
            $cache->delete($Redis2Model->getkeyMsg());
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($type => $id));
            echo '删除成功';
        }elseif ($type == 'catgh'){
            $uid = intval($_REQUEST['id']);
            $cid = intval($_REQUEST['cid']);
            $Act40Model = Master::getAct40($uid);
            $Act40Model->info['allgx'] = 0;
            $Act40Model->info['inTime'] = 0;
            $Act40Model->info['cid'] = 0;
            $Act40Model->save();
            $ClubModel = Master::getClub($cid);
            $ClubModel->goout_club($uid);
            $result = $ClubModel->getBase();
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $uid));
            echo '删除成功';
        }else{
            echo '参数错误!';
        }

    }
	/**
	 * 查看工会信息
	 */
	public function catgh(){

	    $cid = $_REQUEST['cid'];
	    if(!empty($cid)){
            $SevidCfg = Common::getSevidCfg();
            $sevId = Game::get_sevid_club($cid);
            if ($SevidCfg['sevid'] != $sevId){
                echo "<script>alert('不可查看跨服的宫殿');</script>";
                return false;
            }
	        $ClubModel = Master::getClub($cid);
	         $result = $ClubModel->getBase();

	         $Sev51Model = Master::getSev51($cid);
            $kua_b = $Sev51Model->info['list'];
	    }
	    if(!empty($_POST['addclubexp'])){
            $addclubexp = intval($_POST['addclubexp']);
            $ClubModel = Master::getClub($cid);
            $addclubexp += $ClubModel->info['exp'];

            $data = array(
                'exp' => $addclubexp,
            );
            $ClubModel->update($data);
            $result = $ClubModel->getBase();

            $Sev51Model = Master::getSev51($cid);
            $kua_b = $Sev51Model->info['list'];
        }
        if(!empty($_POST['addclubfund'])){
            $addclubfund = intval($_POST['addclubfund']);

            $ClubModel = Master::getClub($cid);
            $addclubfund += $ClubModel->info['fund'];
            $data = array(
                'fund' => $addclubfund,
            );
            $ClubModel->update($data);
        }

        if (!empty($_POST['jop'])){
            $jop = intval($_POST['jop']);
            $cid = intval($_POST['cid']);
            $uid = intval($_POST['changeUid']);
            $ClubModel = Master::getClub($cid);
            $ClubModel->info['members'][$uid]['post'] = $jop;
            $data = array(
                'members' => $ClubModel->info['members'],
            );
            $ClubModel->update($data);
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('change' => $uid));
            
            $ClubModel = Master::getClub($cid);
            $result = $ClubModel->getBase();
            echo "<span style='color: red;'>".$uid."委任成功</span>";
        }elseif (!empty($_REQUEST['uid'])&&!empty($cid)){
            $uid = intval($_REQUEST['uid']);
            $Act40Model = Master::getAct40($uid);
            $Act40Model->info['allgx'] = 0;
            $Act40Model->info['inTime'] = 0;
            $Act40Model->info['cid'] = 0;
            $Act40Model->save();
            $ClubModel->goout_club($uid);
            $result = $ClubModel->getBase();
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $uid));
            echo "<span style='color: red;'>".$uid."删除成功</span>";
        }

	    include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}


    public function clubFlow(){
        set_time_limit(0);
        $dir = ROOT_DIR.'/api/';
        $modelConfig = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if(!in_array($file, array('.','..','.svn'))){
                        $file = str_replace('.php', '', $file);
                        $modelConfig[] = $file;
                    }
                }
                closedir($dh);
            }
        }
        $uid = $this->uid;
        if($_POST){
            $uid = trim($_POST['uid']);
            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
                $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
            }
            if(!empty($_POST['mod'])){
                $where .= " and `model`='{$_POST['mod']}'";
            }
            if(!empty($_POST['type'])){
                $type = $_POST['type'];
            }

            //ftype or fitem
            $table = 'flow_event_'.Common::computeTableId($uid);
            $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';
            $db = Common::getMyDb('flow');
            $data = $db->fetchArray($sql);
            if (!empty($data)){
                foreach ($data as $key => $value){
                    $id[] = $value['id'];
                }

                $fid = implode(',', $id);
                $table = 'flow_records_'.Common::computeTableId($uid);
                $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                if ($type && is_numeric($type)){
                    $sql .= ' and `type`='.$type;
                }
                if($type == 6 && !empty($_POST['items'])){
                    $sql .= ' and `itemid`='.$_POST['items'];
                }elseif($type >7 && $type<13 && !empty($_POST['hero'])){
                    $sql .= ' and `itemid`='.$_POST['hero'];
                }
                $recordData = $db->fetchArray($sql);
                $recordData = array_reverse($recordData);
                if(!empty($recordData)){
                    foreach ($recordData as $k => $v){
                        foreach ($data as $dk => $dv){
                            if ($dv['id'] == $v['flowid']){
                                $data[$dk]['record'][] = $recordData[$k];
                            }
                        }
                    }
                }else{
                    $data = array();
                }

            }
        }
        $heroConfig = Game::getcfg('hero');
        $wifeConfig = Game::getcfg('wife');
        $itemConfig = Game::getcfg('item');
        $clubConfig = include ROOT_DIR."/administrator/config/club.php";
        $msg_lang = include ROOT_DIR."/administrator/extend/msg_lang.php";
        $other_lang = include ROOT_DIR."/administrator/extend/other_lang.php";
        unset($itemConfig[1]);unset($itemConfig[2]);unset($itemConfig[3]);unset($itemConfig[4]);unset($itemConfig[5]);
        $gameConfig = Game::getcfg('flowConfig');
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
//	public function dianzan(){
//		$Redis8Model = Master::getRedis8();
//		$SevidCfg = Common::getSevidCfg();
//		$Redis8Model->back_data();
//		$dianzan = $Redis8Model->outf;
//		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
//	}
}
