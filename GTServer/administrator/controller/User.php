<?php
class User
{
    private $uid;
    public function __construct()
    {
        $this->uid = isset($_REQUEST['uid'])?intval($_REQUEST ['uid']):NULL;
    }

	/**
     * 主页
     * */
    public function index(){
        $SevidCfg = Common::getSevidCfg($_GET['sevid']);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
	/**
     * 用户信息
     * */
    public function allinfo(){
        $SevidCfg = Common::getSevidCfg($_GET['sevid']);
  	   //查看
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $info = array();
        $result = Game::get_sevid($this->uid);
        $_GET['sevid'] = $result;
        if ($_GET['sevid'] != $result && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){

			//用户信息
			Common::loadModel('UserModel');
			$userModel = new UserModel($uid);
			$info = $userModel->info;
			$info['regtime'] = date("Y-m-d H:i:s",$info['regtime']);
			$info['lastlogin'] = date("Y-m-d H:i:s",$info['lastlogin']);
			
			//伙伴信息
			Common::loadModel('HeroModel');
			$cfg_hero = Game::getcfg('hero');
			$heroModel = new HeroModel($uid);
			$hero = $heroModel->info;
			$rid = 1;
			foreach($hero as $k => $v){
				$str = '伙伴'.$rid;
				$info[$str] = $cfg_hero[$k]['name'];
				$rid ++;
			}
			
			//知己
			Common::loadModel('WifeModel');
			$cfg_wife = Game::getcfg('wife');
			$wifeModel = new WifeModel($uid);
			$wife = $wifeModel->info;
			$rid = 1;
			foreach($wife as $k => $v){
				$str = '老婆'.$rid;
				$info[$str] = $cfg_wife[$k]['wname'];
				$rid ++;
			}
			
			
			Common::loadModel('SonModel');
			$cfg_son = Game::getcfg('son_yn');
			$SonModel = new SonModel($uid);
			$son = $SonModel->info;
			$rid = 1;
			foreach($son as $k => $v){
				$str = '孩子'.$rid;
				$info[$str] = $cfg_son[$k]['name'];
				$rid ++;
			}
		}
		
		
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    /**
	 * 修改账号信息
	 */
	public function userChange()
	{
		//查看
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $sevid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $sevid;
        if ($_GET['sevid'] != $sevid  && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
			Common::loadModel('UserModel');
			$userModel = new UserModel($uid);
			$info = $userModel->info;
            $Act99Model = Master::getAct99($uid);
            $ep = $Act99Model->info['ep'];
            $shili = array_sum($Act99Model->info['ep']);
            //修改
            if($uid && !empty($_POST['info']) && !empty($_POST[$_POST['info']]))
            {
                Common::loadModel('UserModel');
                $userModel = new UserModel($uid);
                $u_update[$_POST['info']] = $_POST[$_POST['info']];
                $userModel->update($u_update);
                $userModel->destroy();
                $msg = "ok";
                $userModel = new UserModel($uid);
                $info = $userModel->info;
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => array($_POST['info'],$_POST[$_POST['info']])));

                echo "<span style='color: red;'>{$msg}</span>";
            }
		}
        $SevidCfg = Common::getSevidCfg($sevid);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	
	/**
	 * 修改账号道具信息
	 */
	public function userItem()
	{
		//查看
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
		$daoju = array();
		$cfg_item = Game::getcfg('item');
		$all_item = array();
		foreach($cfg_item as $cfg_k => $cfg_v){
			$all_item[$cfg_v['id']] = $cfg_v['name_cn'];
		}
        $sevid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $sevid;
        if ($_GET['sevid'] != $sevid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
			Common::loadModel('UserModel');
			$userModel = new UserModel($uid);
			Common::loadModel('ItemModel');
			$ItemModel = new ItemModel($uid);
			$info = $ItemModel->info;
			if($info){
				foreach($info as $k => $v){
					$daoju[$v['itemid']] = $v['count'];
				}
			}
            //修改
            if($uid && $_POST['add_change'])
            {
                Common::loadModel('ItemModel');
                $ItemModel = new ItemModel($uid);

                foreach($daoju as $itemid => $count){
                    if ($_POST[$itemid] != 0){
                        $ItemModel->add_item($itemid, trim($_POST[$itemid]));
                    }
                }
                $ItemModel->destroy();
                $ItemModel = new ItemModel($uid);
                $info = $ItemModel->info;
                if($info){
                    foreach($info as $k => $v){
                        $daoju[$v['itemid']] = $v['count'];
                    }
                }
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_change'].'=>'.$_POST[$itemid]));

            }
            //新增
            if($uid && $_POST['add_item_key'] && $_POST['add_item_value'])
            {
                Common::loadModel('ItemModel');
                $itemid = $_POST['add_item_key'];
                $count = $_POST['add_item_value'];
                $kind = $cfg_item[$itemid]['kind'];
                if($kind != 1){
                    Master::add_item($uid, $kind, $itemid, $count);
                }else{
                    $ItemModel = new ItemModel($uid);
                    $ItemModel->add_item($_POST['add_item_key'], $_POST['add_item_value']);
                    $ItemModel->destroy();
                }

                $ItemModel = new ItemModel($uid);
                $info = $ItemModel->info;
                if($info){
                    foreach($info as $k => $v){
                        $daoju[$v['itemid']] = $v['count'];
                    }
                }
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_item_key'].'=>'.$_POST['add_item_value']));
            }
            ksort($daoju);
		}
        $SevidCfg = Common::getSevidCfg($sevid);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	/*
	 * 活动
	 */
	public function activitys(){
		//查看
		$uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $sevid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $sevid;
        if ($_GET['sevid'] != $sevid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
			Common::loadModel('ActModel');
            $info = ActModel::getAllInfo($uid);
			ksort($info);
            //修改
            if ( $uid && isset($_POST['info'])){
                Common::loadModel('ActModel');
                $data = eval("return ".stripslashes($_POST['info']).";");
                $ActModel = new ActModel($uid, $data['actid']);
                $ActModel->update($data);
                $ActModel->destroy();

                $info = ActModel::getAllInfo($uid);
                ksort($info);

                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['info']));
            }

            //新增
            if ( $uid && isset($_POST['add_info'])){
                Common::loadModel('ActModel');
                $data = eval("return ".stripslashes($_POST['add_info']).";");
                $ActModel = new ActModel($uid, $data['actid']);
                $ActModel->update($data);
                $ActModel->destroy();

                $info = ActModel::getAllInfo($uid);
                ksort($info);
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_info']));
            }
        }
        $SevidCfg = Common::getSevidCfg($sevid);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	
	/*
	 * 伙伴
	 */
	public function hero()
	{
		//查看
		$uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $sevid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $sevid;
        if ($_GET['sevid'] != $sevid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
			Common::loadModel('HeroModel');
			$heroModel = new HeroModel($uid);
			$info = $heroModel->info;
            //修改
            if ( $uid && isset($_POST['info'])){
                Common::loadModel('HeroModel');
                $heroModel = new HeroModel($uid);
                $data = eval("return ".stripslashes($_POST['info']).";");


                if (!isset($data['heroid'])){
                    exit ('update_hero_itemid_null');
                }

                if (isset($heroModel->info[$data['heroid']])){//存在 则更新
                    $info = $heroModel->info[$data['heroid']];
                    //更新
                    foreach ($data as $k => $v){
                        $info[$k] = $v;
                    }
                    $info['_update'] = true;
                }

                $heroModel->info[$data['heroid']] = $info;
                $heroModel->_update = true;

                $heroModel->destroy();
                $heroModel = new HeroModel($uid);
                $info = $heroModel->info;
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['info']));
            }

            //新增
            if ($_POST['add_hero_key']){
                $add_hero_key = $_POST['add_hero_key'];
                Common::loadModel('HeroModel');
                $heroModel = new HeroModel($uid);
                $heroModel->add_hero($add_hero_key);
                $heroModel->destroy();
                $heroModel = new HeroModel($uid);
                $info = $heroModel->info;
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_hero_key']));
            }

            $Act6000Model = Master::getAct6000($uid);
            foreach($Act6000Model->info['scpoint'] as $v){
                if(empty($herostory[$v['roleid']])){
                    $herostory[$v['roleid']] = 1;
                }else{
                    $herostory[$v['roleid']] += 1;
                }
            }
            if(!empty($herostory)){
                ksort($herostory);
            }
            //var_dump($info);
            $cfg_hero = Game::getcfg('hero');
            // var_dump($cfg_hero);
            $allhero = '';
            $userNoHero = $cfg_hero;
            foreach ($userNoHero as $k => $v){
                foreach ($info as $infoK => $infoV){
                    if ($infoV['heroid'] == $v['heroid']){
                        unset($userNoHero[$k]);
                    }
                }
            }
            if(!empty($cfg_hero)){
                $kid = 1;
                foreach($cfg_hero as $cfg_info){
                    if (strlen($cfg_info['heroid']) == 1){
                        $cfg_info['heroid'] = ' '.$cfg_info['heroid'];
                    }
                    $allhero .= 'id: '.$cfg_info['heroid'].' 名字 : '.$cfg_info['name'].'	';
                    if($kid %5 == 0){
                        $allhero .= "\n";
                    }
                    $kid ++;
                }
            }
            //重置阵法
            //清缓存没成功 //强制重登
            $cache = Common::getCacheByUid($uid);
            $cache->delete($uid.'_team');
            //$cache->delete($val['uid'].'_token');
        }
        $SevidCfg = Common::getSevidCfg($sevid);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	/*
	 * 知己
	 */
	public function wife()
	{
		//查看
		$uid = isset($_POST ['uid'])?$_POST ['uid']:(isset($_GET ['uid'])?$_GET ['uid']:NULL);
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $sevid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $sevid;
        if ($_GET['sevid'] != $sevid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
			Common::loadModel('WifeModel');
			$wifeModel = new WifeModel($uid);
			$info = $wifeModel->info;
            //修改
            if ( $uid && isset($_POST['info'])){
                Common::loadModel('WifeModel');
                $wifeModel = new WifeModel($uid);
                $data = eval("return ".stripslashes($_POST['info']).";");

                if (!isset($data['wifeid'])){
                    exit ('update_wife_id_null');
                }
                if (isset($wifeModel->info[$data['wifeid']])){//存在 则更新
                    $info = $wifeModel->info[$data['wifeid']];
                    //更新
                    foreach ($data as $k => $v){
                        $info[$k] = $v;
                    }
                    $info['_update'] = true;
                }
                $wifeModel->info[$data['wifeid']] = $info;
                $wifeModel->_update = true;

                $wifeModel->destroy();

                $wifeModel = new WifeModel($uid);
                $info = $wifeModel->info;

                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['info']));
            }

            //新增
            if ( $uid && isset($_POST['add_wife_key'])){
                $add_hero_key = $_POST['add_wife_key'];
                Common::loadModel('WifeModel');
                $wifeModel = new WifeModel($uid);
                $wifeModel->add_wife($add_hero_key);
                $wifeModel->destroy();
                $wifeModel = new WifeModel($uid);
                $info = $wifeModel->info;
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_wife_key']));
            }
            $cfg_wife = Game::getcfg('wife');
            $userNoWife = $cfg_wife;
            foreach ($userNoWife as $k => $v){
                foreach ($info as $infoK => $infoV){
                    if ($infoV['wifeid'] == $v['wid']){
                        unset($userNoWife[$k]);
                    }
                }
            }
            $allwife = '';
            if(!empty($cfg_wife)){
                $kid = 1;
                foreach($cfg_wife as $cfg_info){
                    $allwife .= 'id: '.$cfg_info['wid'].' 名字 : '.$cfg_info['wname'].'	';
                    if($kid %5 == 0){
                        $allwife .= "\n";
                    }
                    $kid ++;
                }
            }
		}
        $SevidCfg = Common::getSevidCfg($sevid);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	/*
	 * 徒弟
	 */
	public function son()
	{
		//查看
		$uid = isset($_POST ['uid'])?$_POST ['uid']:(isset($_GET ['uid'])?$_GET ['uid']:NULL);
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
		$cfg_son = Game::getcfg('son_yn');
        $sevid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $sevid;
        if ($_GET['sevid'] != $sevid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
			Common::loadModel('SonModel');
			$SonModel = new SonModel($uid);
			$info = $SonModel->info;
            //修改
            if ( $uid && isset($_POST['info'])){
                Common::loadModel('SonModel');
                $SonModel = new SonModel($uid);
                $data = eval("return ".stripslashes($_POST['info']).";");

                //更新英雄
                if (isset($data['sonuid'])){
                    $info = $SonModel->info[$data['sonuid']];
                    if (empty($info)){
                        exit('admin_update_son_err');
                    }

                    //更新字段
                    foreach ($data as $k => $v){
                        $info[$k] = $v;
                    }
                    $sonuid = $data['sonuid'];
                    $info['_update'] = true;
                }
                $SonModel->info[$sonuid] = $info;
                $SonModel->_update = true;

                $SonModel->destroy();

                $SonModel = new SonModel($uid);
                $info = $SonModel->info;
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['info']));
            }

            //新增
            if ( $uid && isset($_POST['add_info'])){
                $add_hero_key = $_POST['add_wife_key'];
                Common::loadModel('SonModel');
                $SonModel = new SonModel($uid);
                $SonModel->add_son($add_hero_key);
                $SonModel->destroy();

                $SonModel = new SonModel($uid);
                $info = $SonModel->info;
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_wife_key']));
            }
        }
        $SevidCfg = Common::getSevidCfg($sevid);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	
	/*
	 * 伙伴
	 */
	public function xieyi()
	{
		//查看
		$uid = isset($_POST ['uid'])?$_POST ['uid']:(isset($_GET ['uid'])?$_GET ['uid']:NULL);
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
		$cfg_modid = include(CONFIG_DIR . '/modid.php');
		$modcheck = '';
		$rdata = array();
		$post_data = array (
	      
		
	    );

		if($_POST ['modids'] && $_POST ['post_data']){
			
			$modids = explode('__',$_POST ['modids']);
			$post_data = eval("return ".stripslashes($_POST ['post_data']).";");
			$data[$modids[0]][$modids[1]] = $post_data;
			$url = 'http://king.test.tuziyouxi.com/servers/s999.php?ver=1.0&uid='.$uid.'&token=';
			$rdata = self::http_post_data($url, $data);
			$rdata = json_decode($rdata,1);
			$modcheck = $modids[1];
		}

		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}

    /**
     * redis数据
     */
    public function redis () {
        $uid = isset($_REQUEST ['uid'])?$_REQUEST ['uid']:NULL;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $uid = 10086;

        Common::loadModel('RedisModel');
        $RedisModel = new RedisModel($uid);
        $info = $RedisModel->info;

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * sevact数据
     */
    public function sevact () {
        $uid = isset($_REQUEST ['uid'])?$_REQUEST ['uid']:NULL;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $uid = 10086;

        Common::loadModel('ServerModel');
        $ServerModel = new ServerModel($uid);
        $info = $ServerModel->info;

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
	public function findUid(){

        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();

        if($_POST){

            $serverid = isset($_POST['serverid']) ? $_POST['serverid'] : ServerModel::getDefaultServerId();
            $SevidCfg = Common::getSevidCfg($serverid);
            $db = Common::getDftDb();

            Common::loadModel('ServerModel');
            if ($_REQUEST ['ustring']){

                $ustring = trim($_REQUEST['ustring']);
                $ustring = trim ($_REQUEST ['ustring']);
                $sql = "select * from gm_sharding where ustr = '{$ustring}'";
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
                        $row = $db->fetchRow ( $sql );
                        if($row) {
                            break;
                        }
                    }
                }
            }
            if ($_REQUEST ['uid']) {

                $uid = intval(trim($_REQUEST['uid']));
                $sql = "select * from gm_sharding where uid = '{$uid}'";
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
                        $row = $db->fetchRow ( $sql );
                        if($row) {
                            break;
                        }
                    }
                }
            }
            if ($_REQUEST ['uname']) {

                $row = array();
                $uname = trim ( $_REQUEST ['uname'] );
                $data = array ();
                $table = "index_name";
                $sql = "select uid,name from {$table} where `name` like '%{$uname}%'";
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
                        $res = $db->fetchArray ( $sql );
                        if($res) {
                            $row = array_merge($row, $res);
                        }
                    }
                }
            }
            if ($_REQUEST ['openid']) {
                $openid = trim($_REQUEST['openid']);
                $data = array ();
                $sql = "select * from `register` where `openid`='{$openid}'";
                $list = $db->fetchArray($sql);

                foreach ($list as $k => $v) {
                    $row[] = array("servid" => $v["servid"], "uid" => $v["uid"], "regtime" => $v["reg_time"]);

                    if (!empty($v['data'])) {

                        $v['data'] = json_decode($v['data'],true);
                        foreach ($v['data'] as $seid => $d){
                            $row[] = array("servid" => $d["servid"], "uid" => $d["uid"], "regtime" => $d["reg_time"]);
                        }
                    }
                }
            }
            if ($_REQUEST ['rollUid']) {
                $rollUid = trim($_REQUEST['rollUid']);
                $data = array ();
                $sql = "select * from `register` where `uid`='{$rollUid}'";
                $row = $db->fetchArray($sql);
            }
        }
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}

    /**
     * 邮件
     */
    public function mail(){
        $items = Game::getCfg('item');
        $chenghao = Game::getCfg('chenghao');
        $mail_lang = include ROOT_DIR."/administrator/extend/mail_lang.php";
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        Common::loadModel('MailModel');
        $mailModel = new MailModel($uid);
        $data = $mailModel->info;
        if ($_REQUEST['del'] == 1){
            $emailId = $_REQUEST['emailId'];
            $mailModel->delMails($emailId);
            $mailModel->getMails();
            $mailModel->destroy();
            $mailModel = new MailModel($uid);
            $data = $mailModel->info;
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $emailId));
        }
        foreach ($data as $k =>$v){
            if (isset($mail_lang[$v['mtitle']])){
                $data[$k]['mtitle'] = $mail_lang[$v['mtitle']];
            }
            $mail_tmp = explode("|",$v['mcontent']);
            foreach ($mail_tmp as $a => $b){
                if (isset($mail_lang[$b])){
                    $mail_tmp[$a] = $mail_lang[$b];
                }
            }
            $data[$k]['mcontent'] = implode(" ",$mail_tmp);
        }
        $data = array_reverse($data, true);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    /**
     * 聊天
     */
    public function chat(){
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $guan = Game::getCfg('guan');
        $sevid = $_GET['sevid'];
        Common::loadModel('UserModel');
        $sev22Model = Master::getSev22();
        $chatData = array_reverse($sev22Model->info);
        foreach ($chatData as $key => $value){
            if ($value['uid']!=$uid){
                unset($chatData[$key]);
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

	// HTTP json数据请求函数
	public function http_post_data($url,$query_data) {
		 $query_data = json_encode($query_data);
		 $header = array(
	        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
	     );
		 $ch = curl_init(); //初始化curl 
	     curl_setopt($ch, CURLOPT_URL, $url);//设置链接 
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息 
	     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头 
	     curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式 
	     curl_setopt($ch, CURLOPT_POSTFIELDS, $query_data);//POST数据 
	     $response = curl_exec($ch);//接收返回信息 
	     curl_close($ch); //关闭curl链接 
	     
	     return $response;
	}
//角色信息转移
	public function transferRoleData(){
		$sevid = 1;
        $redisKey = "gm_login_redis";
        $db = Common::getDbBySevId($sevid);
        $redis = Common::getRedisBySevId($sevid);
        $list = array();
		if(1 <= $_REQUEST['step']){
			$oldUID = intval($_REQUEST['uid1']);
			$newUID = intval($_REQUEST['uid2']);
			
			if (empty($oldUID) || empty($newUID)){
				echo "<script>alert('参数错误');window.history.go(-1);</script>";
			}
			if($oldUID == $newUID){
				echo "<script>alert('相同角色不需要转移');window.history.go(-1);</script>";
			}
			Common::loadModel("UserModel");
			$oldUserModel = new UserModel($oldUID);
			
			if(empty($oldUserModel->info['uid'])){
				echo "<script>alert('角色({$oldUID})不存在');window.history.go(-1);</script>";
				return ;
			}
			$newUserModel = new UserModel($newUID);
			if(empty($newUserModel->info['uid'])){
				echo "<script>alert('角色({$newUID})不存在');window.history.go(-1);</script>";
				return ;
			}

            $cx_sql = "select * from `gm_login` where `oldUID`={$oldUID}";
            $res = $db->fetchRow($cx_sql);
            if(empty($res)){
                $sql = "insert into `gm_login` (`oldUID`, `newUID`) values({$oldUID},{$newUID})";
                $result = $db->query($sql);
            }else{
                $sql = "update `gm_login` set `newUID`={$newUID} where `oldUID`={$oldUID}";
                $result = $db->query($sql);
            }

            $redis->delete($redisKey);

            $list_sql = "select * from `gm_login` where `id` > 0";
            $list = $db->fetchArray($list_sql);

            foreach ($list as $key => $value) {
                $redis->zAdd($redisKey, $value["newUID"], $value["oldUID"]);
            }

            echo "<script>alert('角色数据已经迁移完毕');</script>";

            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($oldUID => $newUID));
			//include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
			return;
		}

        $list_sql = "select * from `gm_login` where `id` > 0";
        $list = $db->fetchArray($list_sql);

		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	public function flow(){
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
            $startTime = strtotime(date('Y-m-d 00:00:00'));
            $endTime = strtotime(date('Y-m-d 23:59:59'));
            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }
            $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
            if(!empty($_POST['mod'])){
                $where .= " and `model`='{$_POST['mod']}'";
            }
            if (!empty($_POST['models'])){
                $where .= " AND `model`='".trim($_POST['models'])."'";
            }
            if (!empty($_POST['controls'])){
                $where .= " AND `ctrl`='".trim($_POST['controls'])."'";
            }
            if(!empty($_POST['type'])){
                $type = $_POST['type'];

                if($type == 1){
                    $where .= ' AND `ctrl` != "upTask_8016" and `ctrl` != "upTask_8011" ';
                }
            }
            
            //ftype or fitem
            $table = 'flow_event_'.Common::computeTableId($uid);
            $sql = 'SELECT * FROM '.$table.' WHERE `uid`='.$uid.$where.' ORDER BY `id` DESC';

			$svid = Game::get_sevid($this->uid);
			$SevidCfg = Common::getSevidCfg($svid);
	
            $db = Common::getDbBySevId($SevidCfg['sevid'],'flow');
		//var_dump($db);
            $data = $db->fetchArray($sql);
            if (!empty($data)){
                foreach ($data as $key => $value){
                    $id[] = $value['id'];
                }
            
                $fid = implode(',', $id);
                if (!empty($_POST['old'])){
                    $table = 'flow_record_'.Common::computeTableId($uid);
                }else{
                    $table = 'flow_records_'.Common::computeTableId($uid);
                }

                $sql = 'SELECT * FROM '.$table.' WHERE `flowid` IN ('.$fid.')';
                if ($type && is_numeric($type)){
                    $sql .= ' and `type`='.$type;
                }
                if($type == 6 && !empty($_POST['items'])){
                    $sql .= ' and `itemid`='.$_POST['items'];
                }elseif( ( ($type > 7 && $type < 13) || $type == 6001) && !empty($_POST['hero'])){
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
        $UserModel = new UserModel($uid);
        $heroConfig = Game::getcfg('hero');
        $wifeConfig = Game::getcfg('wife');
        $itemConfig = Game::getcfg('item');
        $msg_lang = include ROOT_DIR."/administrator/extend/msg_lang.php";
        $other_lang = include ROOT_DIR."/administrator/extend/other_lang.php";
        $cloher_lang = include ROOT_DIR."/administrator/extend/cloher_lang.php";

        $modelsConfig = array();
        $ctrlConfig = array();
        foreach ($msg_lang as $key => $value) {

            if (in_array($key, array("admin"))) {

            }

            foreach ($value as $k => $v) {

                if ($key == "cs") {
                    $modelsConfig[] = array("ctrl" => $k, "title" => $v);
                }else{
                    $ctrlConfig[] = array("ctrl" => $k, "title" => $v);
                }
            }
        }

        unset($itemConfig[1]);unset($itemConfig[2]);unset($itemConfig[3]);unset($itemConfig[4]);unset($itemConfig[5]);
        $gameConfig = Game::getcfg('flowConfig');
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 流水(后端查看)
     */
    public function flowAdmin(){
    	
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
            $startTime = strtotime(date('Y-m-d 00:00:00'));
            $endTime = strtotime(date('Y-m-d 23:59:59'));
            if ($_POST['startTime'] && $_POST['endTime']){
                $startTime = strtotime($_POST['startTime']);
                $endTime = strtotime($_POST['endTime']);
            }
            $where = ' and `ftime`>'.$startTime.' and `ftime`<'.$endTime;
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
                if (!empty($_POST['old'])){
                    $table = 'flow_record_'.Common::computeTableId($uid);
                }else{
                    $table = 'flow_records_'.Common::computeTableId($uid);
                }
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
        $UserModel = new UserModel($uid);
        $heroConfig = Game::getcfg('hero');
        $wifeConfig = Game::getcfg('wife');
        $itemConfig = Game::getcfg('item');
        $msg_lang = include ROOT_DIR."/administrator/extend/msg_lang.php";
        $cloher_lang = include ROOT_DIR."/administrator/extend/cloher_lang.php";
        unset($itemConfig[1]);unset($itemConfig[2]);unset($itemConfig[3]);unset($itemConfig[4]);unset($itemConfig[5]);
        $gameConfig = Game::getcfg('flowConfig');
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 宫殿信息
     * 
     * */
    public function club() {
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        //当前用户的宫殿信息
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        if(!empty($cid)){
            $ClubModel = Master::getClub($cid);
            $result = $ClubModel->getBase();
            $password = $ClubModel->get_password();
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    
    
    
    /*
     * 一键
     * */
    public function  ghostUp() {
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $cfg_hero = Game::getcfg('hero');
        if(!empty($uid)){
            $result = Game::get_sevid($this->uid);
            if ($_GET['sevid'] != $result){
                echo "<script>alert('不可操作跨服的玩家');</script>";
                return;
            }
            //伙伴一键升级
            $HeroModel = Master::getHero($uid);
            if($_POST['hero']){
                if(empty($_POST['level'])){
                    echo "<script>alert('请输入要升级到的等级');</script>";
                }else{
                    $level = trim($_POST['level']);
                    
                    $min_level = $level>$HeroModel->info[$_POST['hero']]['level'] ? $level : $HeroModel->info[$_POST['hero']]['level'];
                    $herosenior_cfg = Game::getcfg_info('hero_senior', $HeroModel->info[$_POST['hero']]['senior']);
                    $max_level = $herosenior_cfg['max_level'];
                    $bool = true;
                    while ($bool){
                        if($level <= $HeroModel->info[$_POST['hero']]['level']){
                            $bool =  false; break;
                        }
                        
                        if($HeroModel->info[$_POST['hero']]['level'] >= $max_level){
                            echo "<script>alert('已经达到当前爵位的最大等级,请前往游戏升级爵位');</script>"; $bool= false; break;
                        }
                        
                        $bool = self::upgrade(array('uid' => $uid,'id'=>$_POST['hero']));
                    }
                    //后台操作日志
                    Common::loadModel('AdminModel');
                    AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['hero'].'->'.$level));
                }
            }
            
            //关卡一键过关
            $UserModel = Master::getUser($uid);
            if($_POST['guanka']){
                $guanka = trim($_POST['guanka']);
                $gq_bool = true;
                while ($gq_bool){
                    if($guanka <= $UserModel->info['smap']){
                        $gq_bool = false;break;
                    }
                    $gq_bool = $this->pve(array('uid' => $uid));
                }
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $guanka));
            }

            
//             //宫斗一键打
//             $this->fight(array('uid' => $uid,'id'=>1));
            
            
            $userInfo = array('smap' => $UserModel->info['smap'],'bmap' => $UserModel->info['bmap']);
            
            $hero = array();
            if(!empty($HeroModel->info)){
                foreach ($HeroModel->info as $val){
                    $hero[$val['heroid']] = array('name'=> $cfg_hero[$val['heroid']]['name'],'level' => $val['level']);
                }
            }
            
        }
        
        
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
   
    
    /*
     *伙伴升级
     */
    public function upgrade($params){
        $uid = Game::intval($params,'uid');
        $UserModel = Master::getUser($uid);
        $HeroModel = Master::getHero($uid);
    
        //伙伴ID
        $HeroId = Game::intval($params,'id');
        //伙伴ID合法
        $hero_info = $HeroModel->check_info($HeroId);
    
        //数据提取
        $exp = $hero_info['exp'];//当前经验
    
        //伙伴升级所需阅历
        $hero_level_cfg = Game::getcfg_info('hero_level',$hero_info['level']);
        //爵位等级配置
        $hero_senior_cfg = Game::getcfg_info('hero_senior',5);
        //是否达到爵位等级上限
        if ($hero_senior_cfg['max_level'] <= $hero_info['level']){
            Master::error(HERO_LEVEL_CAP);
        }
    
        //当前等级所需阅历
        $need_cost = $hero_level_cfg['cost'] - $hero_info['exp'];
        $need_cost = max($need_cost,0);
    
        //阅历够不够
        if ($UserModel->info['coin'] >= $need_cost){
            	
            $hero_info['level'] += 1;
            //神迹
            if ($hero_info['level'] < 160){
                $Act65Model = Master::getAct65($uid);
                if ($Act65Model->rand(3)){
                    //触发神迹:连升三级
                    $hero_info['level'] += 2;
                    //如果被神迹生了3级 判断等级上限
                    $hero_info['level'] = min($hero_senior_cfg['max_level'],$hero_info['level']);
                }
            }
            	
            //够的话 直接升级 扣除阅历
            $exp = 0;
        }else if ($UserModel->info['coin'] > 0){
            //不够的话 扣除剩余全部阅历 加上对应经验值
            $need_cost = $UserModel->info['coin'];
            $exp += $need_cost;
        }else{
            echo "<script>alert('阅历不足');</script>";
            return false;
        }
    
        //扣钱
        Master::sub_item($uid,KIND_ITEM,2,$need_cost);
    
        //更新伙伴
        $h_update = array(
            'heroid' => $HeroId,
            'level' => $hero_info['level'],
            'exp' => $exp,
        );
        $HeroModel->update($h_update);
    
        //主线任务 - 刷新
        $Act39Model = Master::getAct39($uid);
        $Act39Model->task_refresh(5);
    
        return true;
    }
    
    /*
     * 打地图
     */
    public function pve($params){
        
        $uid = Game::intval($params,'uid');
        
        
        $UserModel = Master::getUser($uid);
    
        /*
         $UserModel->info['bmap']; //大关ID 已经到达的
         //$UserModel->info['mmap']; //中关ID 已经到达的 //中关ID 暂时无用
         $UserModel->info['smap']; //小关ID 已经打过的
         $UserModel->info['mkill']; //剩余兵李/BOSS血量
        */
    
        //如果将要打的小关的大关ID 大于本大关的ID 则认为打到了BOSS
        $hit_smap = $UserModel->info['smap']+1;
        $smap_cfg = Game::getcfg_info('pve_smap',$hit_smap);
        if ($smap_cfg['bmap'] > $UserModel->info['bmap']){
            $HeroModel = Master::getHero($uid);
            if(!empty($HeroModel->info)){
                $play_status = false;
                foreach ($HeroModel->info as $id => $heroInfo){
                    $play_status = $this->pvb(array('uid'=>$uid,'id'=> $id));
                    if($play_status == true){
                        break;
                    }
                }
                if($play_status == false){
                    echo "<script>alert('boss打不过');</script>";
                }
                return $play_status;
            }
            
        }else{
    
            //还有没有小兵
            if ($UserModel->info['army'] <= 0){
                echo "<script>alert('士兵已经不足,打不了了');</script>";
            }
        
            //将要打的关卡信息
            $smap_cfg['ep1'];//武力
            $smap_cfg['army'];//兵力
            $cfg_rwd = array ( //过关奖励  配置数据太大,这边写死
                array ( 'itemid' => 2,  'type' => '$e2*0.12497+1000', ),
                array ( 'itemid' => 5, 'count' => 5, ),
            );
        
            //当前关卡剩下多少兵力 (减去已经干掉的)
            $map_army = $smap_cfg['army'] - $UserModel->info['mkill'];
        
            //获取我的阵法属性
            $team = Master::get_team($uid);
            //我的武力值
            $wuli = $team['allep'][1];
            //$wuli = 100;
        
            //杀光这些小兵需要多少兵力
            $need_army = round($map_army * $smap_cfg['ep1'] / $wuli);
            if($smap_cfg['bmap'] <= 10){
                $need_army = round($map_army/2 +  $map_army/2* $smap_cfg['ep1'] / $wuli);
            }
        
            $u_update = array();
        
            //兵力是否够
            $win = 1;//胜利
            if ($UserModel->info['army'] >= $need_army){
                //足够 胜利 标示这一关已经打过
                $u_update['smap'] = $hit_smap;//关卡ID更新
                $u_update['mkill'] = 0;//已击溃清0
                	
                //加上过关奖励
                foreach ($cfg_rwd as $rv){
                    //构造数量
                    $item = Game::auto_count($rv,$team['allep']);
                    Master::add_item($uid, $item['kind'], $item['itemid'],$item['count']);
//                      Master::add_item2($item,'user','pvewin');
                }
                //通过一个中关卡获得的额外奖励
                if( !empty($smap_cfg['rwd_prob_100']) ){
                    $extra = $smap_cfg['rwd_prob_100'];
                    $rk = Game::get_rand_key(10000,$extra,'prob_10000');
                    if(!empty($extra[$rk])){
                        $item = array(
                            'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
                            'itemid' => $extra[$rk]['itemid'],
                            'count' => $extra[$rk]['count'],
                        );
                        Master::add_item($uid, $item['kind'], $item['itemid'],$item['count']);
//                         Master::add_item2($item,'user','pvewin');
                    }
                }

                $pveNum = $UserModel->info['bmap'] + $UserModel->info['smap'] - 1;
                //成就更新
                $Act36Model = Master::getAct36($uid);
                $Act36Model->set(5, $pveNum);

                //日常任务
                $Act35Model = Master::getAct35($uid);
                $Act35Model->do_act(1,1);

                //更新关卡排行
                $Redis2Model = Master::getRedis2();
                $Redis2Model->zAdd($uid, $pveNum);

        
                //关卡冲榜
                $HuodongModel = Master::getHuodong($uid);
                $HuodongModel->chongbang_huodong('huodong251',$uid,1);
                $bool = true;
        
            }else{
                $win = 0;//失败
                	
                //杀光这些小兵需要多少兵力
                $mkill = round($UserModel->info['army']/ ($smap_cfg['ep1'] / $wuli));
                if($smap_cfg['bmap'] <= 10){
                    $mkill = round($UserModel->info['army']/2 +  $UserModel->info['army']/2* $smap_cfg['ep1'] / $wuli);
                }
                $all_mkill = $UserModel->info['mkill'] + $mkill;//已击溃增加
                if($smap_cfg['army'] <= $all_mkill){
                    $all_mkill = floor( $smap_cfg['army'] -  $map_army * 0.9 );
                }
                	
                $u_update['mkill'] = $all_mkill;//已击溃增加
                $need_army = $UserModel->info['army'];//小兵耗光
                	
                $bool = false;
            }
            $UserModel->update($u_update);
            //主线任务 - 刷新
            $Act39Model = Master::getAct39($uid);
            $Act39Model->task_refresh(7);
            //扣除兵力
            Master::sub_item($uid,KIND_ITEM,4,$need_army);
        
           return $bool;
        }
    }
    
    /*
     * 打BOSS
     */
    public function pvb($params){
        
        $uid = Game::intval($params,'uid');
        $UserModel = Master::getUser($uid);
        /*
         $UserModel->info['bmap']; //大关ID 已经到达的
         //$UserModel->info['mmap']; //中关ID 已经到达的 //中关ID 暂时无用
         $UserModel->info['smap']; //小关ID 已经打过的
         $UserModel->info['mkill']; //剩余兵量/BOSS血量
        */
    
        //如果将要打的小关的大关ID 大于本大关的ID 则认为打到了BOSS
        $hit_smap = $UserModel->info['smap']+1;
        $smap_cfg = Game::getcfg_info('pve_smap',$hit_smap);
        if ($smap_cfg['bmap'] <= $UserModel->info['bmap']){
            Master::error(GAME_LEVER_LT_BMAP);
        }
    
        //出战伙伴ID
        $hero_id = Game::intval($params,'id');
    
        $HeroModel = Master::getHero($uid);
        //伙伴存在
        $hero_info = $HeroModel->check_info($hero_id);
    
        //BOSS配置
        $pve_bmap_cfg_info = Game::getcfg_info('pve_bmap',$UserModel->info['bmap']);
    
        //伙伴出战列表
        $Act3Model = Master::getAct3($uid);
        //这个伙伴 是不是可以出战(活的)
        $Act3Model->go_fight($hero_id);
    
        //获取阵法信息
        
        $TeamModel  = Master::getTeam($uid);
        $hero_damage = $TeamModel->getHerodamage($hero_id);
        $hero_damage = intval($hero_damage);

        $team = Master::get_team($uid);
    
        //当前BOSS血量
        $boss_hp = $pve_bmap_cfg_info['hp'] - $UserModel->info['mkill'];
        if ($hero_damage >= $boss_hp){
            //打死BOSS 过关
            $u_update = array(
                'bmap' => $UserModel->info['bmap'] + 1,
                'mkill' => 0,
            );
            $UserModel->update($u_update);
            //伙伴出战信息清空
            $Act3Model->reset();
            	
            $pve_boss_cfg = Game::getcfg_info('pve_boss',$UserModel->info['bmap']);
            //打死boss就奖励
            foreach ($pve_boss_cfg['rwd'] as $rv){
                //构造数量
                $item = Game::auto_count($rv,$team['allep']);
                Master::add_item($uid, $item['kind'], $item['itemid'],$item['count']);
//                 Master::add_item2($item,'user','pvbwin');
            }
            //通过一个中关卡获得的额外奖励
            if( !empty($pve_boss_cfg['rwd_prob_100']) ){
                $extra = $pve_boss_cfg['rwd_prob_100'];
                $rk = Game::get_rand_key(10000,$extra,'prob_10000');
                if(!empty($extra[$rk])){
                    $item = array(
                        'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
                        'itemid' => $extra[$rk]['itemid'],
                        'count' => $extra[$rk]['count'],
                    );
                    Master::add_item($uid, $item['kind'], $item['itemid'],$item['count']);
//                     Master::add_item2($item,'user','pvbwin');
                }
            }

            $pveNum = $UserModel->info['bmap'] + $UserModel->info['smap'] - 1;
            //成就更新
            $Act36Model = Master::getAct36($uid);
            $Act36Model->set(5, $pveNum);

            //更新关卡排行
            $Redis2Model = Master::getRedis2();
            $Redis2Model->zAdd($uid, $pveNum);

            //犯人
            $cfg_fanren = Game::getcfg('pve_fanren');
            foreach($cfg_fanren as $info){
                if( $UserModel->info['bmap']-1 != $info['bmap']){
                    continue;
                }
                $Act19Model = Master::getAct19($uid);
                $Act19Model->shouya($info['id']);
                break;
            }
            	
            //名望
            $Act20Model = Master::getAct20($uid);
            $Act20Model->update_eday($UserModel->info['bmap']-1);//名望上限
            $Act20Model->add_mw(25);//名望值
            	
            //关卡冲榜
            $HuodongModel = Master::getHuodong($uid);
            $HuodongModel->chongbang_huodong('huodong251',$uid,1);
    
            //主线任务 - 刷新
            $Act39Model = Master::getAct39($uid);
            $Act39Model->task_refresh(7);
            return true;
        }else{
            //减去BOSS血量
            $UserModel->add_sth('mkill',$hero_damage);
            return false;
        }
    }
    
   
    public function levelUp(){

        if ("hlca-gs-ab.tomatogames.com" == $_SERVER ['HTTP_HOST'] || "hlca-gs-ab-gray.tomatogames.com" == $_SERVER ['HTTP_HOST'] || "hlca-gs-ab-admin.tomatogames.com" == $_SERVER ['HTTP_HOST']) {
            echo "<script>alert('灰度服和正式服不允许一键高级号')</script>";
            include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
            return;
        }

        Common::loadModel('ItemModel');
        $uidInfo = trim($_POST['uid']);
        $heroConfig = Game::getcfg('hero');
        $itemConfig = Game::getcfg('item');
        $uids = explode(',', $uidInfo);
        foreach ($uids as $uid){

            $sevid = Game::get_sevid($uid);
            $SevidCfg = Common::getSevidCfg($sevid);
            if($_POST['level']){
                if ($_POST['level'] == 1){
                    $u_update = array(
                        'cash_buy' => 10000,
                        'coin'     => 9000000000,
                        'food'     => 9000000000,
                        'army'     => 9000000000,
                        'vip'      => 2,
                        'level'    => 4,
                    );
                    $guanka = 100;
                    $item = 99999999;
                    $useItem= 200;
                    $level = 100;
                }elseif($_POST['level'] == 2){
                    $u_update = array(
                        'cash_buy' => 50000,
                        'coin'     => 99999000000,
                        'food'     => 99999000000,
                        'army'     => 99999000000,
                        'vip'      => 5,
                        'level'    => 10,
                    );
                    $guanka = 200;
                    $item = 99999999;
                    $useItem= 300;
                    $level = 100;
                }elseif($_POST['level'] == 3){
                    $u_update = array(
                        'cash_buy' => 90000,
                        'coin'     => 9999999900,
                        'food'     => 9999999900,
                        'army'     => 9999999900,
                        'vip'      => 10,
                        'level'    => 16,
                    );
                    $guanka = 300;
                    $item = 99999999;
                    $useItem= 600;
                    $level = 100;
                }
                //玩家信息
                $UserModel = Master::getUser($uid);
                $UserModel->update($u_update);

                //伙伴添加
                $HeroModel = Master::getHero($uid);
                foreach ($heroConfig as $hk => $hv){
                    if (!isset($HeroModel->info[$hv['heroid']])){
                        $HeroModel->add_hero($hv['heroid']);
                    }
                }
                $HeroModel->destroy();

                //道具添加
                // $ItemModel = new ItemModel($uid);
                foreach($itemConfig as $k => $v){
                    if ($v['id']<6 || $v['kind'] == 11 || $v['kind'] == 114 || $v['kind'] == 115 || $v['kind'] == 116 || $v['kind'] == 118 || $v['kind'] == 119 || $v['kind'] == 208 ){
                        continue;
                    }
                    // $ItemModel->add_item($v['id'], $item);
                    Master::add_item($uid,$v['kind'],$v['id'],$item);
                }
                // $ItemModel->destroy();

                //伙伴嗑药
                $HeroModel = Master::getHero($uid);
                foreach ($HeroModel->info as $hkey => $hvalue){
                    // $this->useforhero($uid, 54, $useItem, $hkey);
                    if ( $HeroModel->info[$hkey]['level'] < $level){
                        $bool = true;
                        while ($bool){
                            if($level <= $HeroModel->info[$hkey]['level']){
                                $bool =  false; break;
                            }
                            $bool = self::upgrade(array('uid' => $uid,'id'=>$hkey));
                        }
                    }

                }
                $gq_bool = true;
                while ($gq_bool){
                    if($guanka <= $UserModel->info['smap']){
                        $gq_bool = false;break;
                    }
                    $gq_bool = $this->pve(array('uid' => $uid));
                }
                $Act39Model = Master::getAct39($this->uid);
                $Act39Model->set_final();
                $clotheCfg = Game::getcfg('use_clothe');
                foreach($clotheCfg as $v){
                    $Act6140Model = Master::getAct6140($uid);
                    $Act6140Model ->addSpClothe($v['id']);
                }
                echo "<script>alert('".$uid."升级成功')</script>";
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
	 * 使用道具
	 * 指定伙伴类型道具
	 * id:道具ID
	 * count:使用数量
	 * hero:伙伴ID
	 */
    public function useforhero($uid, $id, $count, $heroid){
        //道具ID
        $item_id = $id;
        //数量
        if ($count <= 0){
            Master::error('num_err_'.$count);
        }

        $HeroModel = Master::getHero($uid);
        //伙伴存在
        $hero_info = $HeroModel->check_info($heroid);

        //减去使用的道具
        Master::sub_item($uid, KIND_ITEM, $item_id, $count);

        //道具配置
        $itemcfg_info = Game::getcfg_info('item',$item_id);

        if ($itemcfg_info['type'][0] != 'hero'){
            Master::error(ITEMS_TYPE_ERROR,$itemcfg_info['type'][0]);
        }

        //$count
        switch($itemcfg_info['type'][1]){
            case 'ep'://嗑药
                if($itemcfg_info['type'][2] != 5){
                    $epstr = 'e'.$itemcfg_info['type'][2];
                    $h_update = array(
                        'heroid' => $heroid,
                        $epstr => $itemcfg_info['type'][3] * $count,
                    );
                    $HeroModel->update($h_update);
                    Master::win_other($uid ,$epstr,$itemcfg_info['type'][3] * $count);
                }else{
                    //分类嗑药
                    $yao = array();
                    for($i=0 ; $i < $count ; $i++){
                        $r_id = rand(1,4);//随机属性
                        $yao[$r_id] = empty($yao[$r_id])?1:$yao[$r_id]+1;
                    }
                    //分类嗑药
                    foreach($yao as $yk => $yv){
                        $epstr = 'e'.$yk;
                        $h_update = array(
                            'heroid' => $heroid,
                            $epstr => $itemcfg_info['type'][3] * $yv,
                        );
                        $HeroModel->update($h_update);
                        Master::win_other($uid, $epstr, $itemcfg_info['type'][3] * $yv);
                    }
                }
                break;
            case 'pkexp'://技能经验书
                $h_update = array(
                    'heroid' => $heroid,
                    'pkexp' => $itemcfg_info['type'][2] * $count,
                );
                $HeroModel->update($h_update);
                Master::win_other($uid, 'pkexp', $itemcfg_info['type'][2] * $count);
                break;
            case 'zzexp'://书籍经验书
                $h_update = array(
                    'heroid' => $heroid,
                    'zzexp' => $itemcfg_info['type'][2] * $count,
                );
                $HeroModel->update($h_update);
                Master::win_other($uid, 'zzexp', $itemcfg_info['type'][2] * $count);
                break;
            //case 'zzlv'://书籍提升 PASS 作为功能道具
            //break;
            default:
                Master::error(ITEMS_USE_HERO_ERROR);
                break;
        }
        $HeroModel->destroy();
        return true;
    }

    /**
     * 当前用户IP所在的IP群
     */
    public function getip(){
        $ip_key = $this->uid.'_ip';
        $uid = $this->uid;
        $mcache = Common::getCacheByUid($this->uid);
        $ip = $mcache->get($ip_key);
        if(empty($ip)) {
            echo "<script>alert('当前用户没有ip');</script>";
        }else{
            $guanq = Game::get_peizhi('gq_status');
            if(!isset($guanq['iplimit']) || $guanq['iplimit'] == 0){
                return true;
            }
            if(isset($guanq['iplimittype']) && $guanq['iplimittype'] > 12) $guanq['iplimittype'] = 12;
            if(!isset($guanq['iplimittype']) || $guanq['iplimittype'] == 0){
                $guanq['iplimittype'] = 24;
            }
            $cache = Common::getComMem();
            $id = intval(date('H')/$guanq['iplimittype']);
            $key = 'iplimit_regsiter_'.Game::get_today_id().'_'.$id.'_'.$ip;
            $ip_uids = $cache->get($key);
            if($_POST['fuid']){
                if(isset($ip_uids[$_POST['fuid']])){
                    echo "<script>alert('当前uid已存在');</script>";
                }else{
                    $ip_uids[$_POST['fuid']] = 1;
                    $cache->set($key,$ip_uids);
                }
            }
            $list = array();
            if(!empty($ip_uids)){
                foreach ($ip_uids as $uid1 => $v){
                    $UserModel = Master::getUser($uid1);
                    $openid = Common::getOpenid($uid1);
                    $list[$uid1] = array(
                        'openid' => $openid,
                        'name'=> $UserModel->info['name'],
                        'level'=>$UserModel->info['level'],
                        'vip' => $UserModel->info['vip']
                    );
                }
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    public function del_uid(){
        if(!empty($_REQUEST['uid'])){
            $uid = $_REQUEST['uid'];
            $UserModel = Master::getUser($uid);
            $userInfo = $UserModel->info;
            $Act1000Model = Master::getAct1000($this->uid);
            $userInfo['is_del'] = $Act1000Model->isDel() === true ? 1 : 0;
        }
        if(!empty($_REQUEST['del_uid'])){
            $uid = $_REQUEST['del_uid'];
            $Act1000Model = Master::getAct1000($uid);
            $bool = $Act1000Model->del();
            $Act1000Model->ht_destroy();
            if($bool === false){
                echo "<script>alert('当前uid已删除');</script>";
            }else{
                echo $this->uid."删除成功";
            }
            $UserModel = Master::getUser($uid);
            $userInfo = $UserModel->info;
            $userInfo['is_del'] = 1;
        }

        if(!empty($_REQUEST['recover_uid'])){
            $uid = $_REQUEST['recover_uid'];
            $Act1000Model = Master::getAct1000($uid);
            $bool = $Act1000Model->recover();
            $Act1000Model->ht_destroy();
            if($bool === false){
                echo "<script>alert('当前uid恢复');</script>";
            }else{
                //将这个账号的其他信息转换为删除角色状态
                $db = Common::getDbeByUid($uid);
                $open_id = Common::getOpenid($uid);
                $sql = "select `uid` from `gm_sharding` WHERE `uid`!='{$uid}' AND `ustr`='{$open_id}'";
                $uids = $db->fetchArray($sql);
                if(!empty($uids)){
                    foreach ($uids as $val){
                        $delAct1000Model = Master::getAct1000($val['uid']);
                        $delAct1000Model->del();
                        $delAct1000Model->ht_destroy();
                        unset($delAct1000Model);
                    }
                }
                echo $this->uid."恢复成功";
            }
            $UserModel = Master::getUser($uid);
            $userInfo = $UserModel->info;
            $userInfo['is_del'] = 0;
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function closure(){
        $uid = $_REQUEST['uid'];
        $Redis12Model = Master::getRedis12();
        $sb_data = $Redis12Model->is_exist($uid);
        if(empty($sb_data)){
            $Act59Model = Master::getAct59($uid);
            $Act59Model->addAccount();

            $sev25Model = Master::getSev25();
            $sev25Model->delete_msg($uid);

            $Sev22Model = Master::getSev22();
            $Sev22Model->delete_msg($uid);

            $Sev6012Model = Master::getSev6012();
            $Sev6012Model->delete_msg($uid);

            $Sev6013Model = Master::getSev6013();
            $Sev6013Model->delete_msg($uid);
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('closureUid' => $uid));
            echo "封号成功";
        }else{
            echo "该用户已经被封号";
        }
    }

    public function delUidList(){
        if(!empty($_REQUEST['uid'])){
            $uid = trim($_REQUEST['uid']);
            $Act1000Model = Master::getAct1000($uid);
            if($Act1000Model->recover() === false){
                echo "<script>alert('当前uid已恢复');</script>";
            }else{
                echo $uid."成功恢复";
            }
        }
        $Redis1000Model = Master::getRedis1000();
        $list = $Redis1000Model->getList();
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function resetPassword(){
        if(!empty($_REQUEST['account']) && !empty($_REQUEST['password']) ){
            $account =  trim($_REQUEST['account']);
            $password = md5(trim($_REQUEST['password']));
            Common::loadModel('UserAccountModel');
            $user = new UserAccountModel($account);
            if (!empty($user)){
                $user->resetPassword($password);
                echo '<span style="color:red;">修改成功!</span>';
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 修改账号卡牌信息
     */
    public function userCard()
    {
        //查看
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $daoju = array();
        $daojuCount = array();
        $cfg_card = Game::getcfg('card');
        $all_item = array();
        foreach($cfg_card as $cfg_k => $cfg_v){
            $all_item[$cfg_v['id']] = $cfg_v['name'];
        }
        $result = Game::get_sevid($this->uid);
        $_GET['sevid'] = $result;
        if ($_GET['sevid'] != $result && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){

            Common::loadModel('CardModel');
            $CardModel = new CardModel($uid);
            $cardInfo = $CardModel->info;
            Common::loadModel('ItemModel');
            $ItemModel = new ItemModel($uid);
            $info = $ItemModel->info;
            if($cardInfo){
                foreach($cardInfo as $k => $v){
                    $daoju[$v['cardid']] = $v;
                    $daoju[$v['cardid']]["count"] = 0;
                    $cardCfg = Game::getcfg_info('card',$v['cardid']);
                    $daoju[$v['cardid']]["quality"] = $cardCfg['quality'];
                    $daojuCount[$cardCfg['quality']]++;
                }

                if($info){
                    foreach($info as $k => $v){

                        if (isset($daoju[$v['itemid']])) {
                            $daoju[$v['itemid']]["count"] = $v['count'];
                        }
                    }
                }
            }
            //修改
            if($uid && $_POST['add_change'])
            {
                foreach($daoju as $itemid => $count){
                    if ($_POST[$itemid] != 0){
                        $ItemModel->add_item($itemid, trim($_POST[$itemid]));
                        $daoju[$itemid]["count"] += $_POST[$itemid];
                    }
                }
                $ItemModel->destroy();
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_change'].'=>'.$_POST[$itemid]));

            }
            //新增
            if($uid && $_POST['add_item_key'])
            {
                $itemid = $_POST['add_item_key'];
                $count = 1;
                $kind = $cfg_card[$itemid]['kind'];

                if (isset($daoju[$itemid])) {

                    echo "<script>alert('玩家已拥有该卡牌');</script>";
                }else{

                    Master::add_item($uid, $kind, $itemid, $count);
                    $daoju[$itemid] = array(
                        'id' => $itemid,
                        'level' => 1,
                        'star' => 0,
                        'count' => 0,
                    );

                    //后台操作日志
                    Common::loadModel('AdminModel');
                    AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_item_key'].'=>1'));
                }
            }
            ksort($daoju);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 修改账号四海奇珍信息
     */
    public function userBaowu()
    {
        //查看
        $uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $daoju = array();
        $cfg_baowu = Game::getcfg('baowu');
        $all_item = array();
        foreach($cfg_baowu as $cfg_k => $cfg_v){
            $all_item[$cfg_v['id']] = $cfg_v['name'];
        }
        $result = Game::get_sevid($this->uid);
        $_GET['sevid'] = $result;
        if ($_GET['sevid'] != $result && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){

            Common::loadModel('BaowuModel');
            $BaowuModel = new BaowuModel($uid);
            $baowuInfo = $BaowuModel->info;
            Common::loadModel('ItemModel');
            $ItemModel = new ItemModel($uid);
            $info = $ItemModel->info;
            if($baowuInfo){
                foreach($baowuInfo as $k => $v){
                    $daoju[$v['baowuid']] = $v;
                    $daoju[$v['baowuid']]["count"] = 0;
                }

                if($info){
                    foreach($info as $k => $v){

                        if (isset($daoju[$v['itemid']])) {
                            $daoju[$v['itemid']]["count"] = $v['count'];
                        }
                    }
                }
            }
            //修改
            if($uid && $_POST['add_change'])
            {
                foreach($daoju as $itemid => $count){
                    if ($_POST[$itemid] != 0){
                        $ItemModel->add_item($itemid, trim($_POST[$itemid]));
                        $daoju[$itemid]["count"] += $_POST[$itemid];
                    }
                }
                $ItemModel->destroy();
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_change'].'=>'.$_POST[$itemid]));

            }
            //新增
            if($uid && $_POST['add_item_key'])
            {
                $itemid = $_POST['add_item_key'];
                $count = 1;
                $kind = $cfg_baowu[$itemid]['kind'];

                if (isset($daoju[$itemid])) {

                    echo "<script>alert('玩家已拥有该卡牌');</script>";
                }else{

                    Master::add_item($uid, $kind, $itemid, $count);
                    $daoju[$itemid] = array(
                        'id' => $itemid,
                        'level' => 1,
                        'star' => 0,
                        'count' => 0,
                    );

                    //后台操作日志
                    Common::loadModel('AdminModel');
                    AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($uid => $_POST['add_item_key'].'=>1'));
                }
            }
            ksort($daoju);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /*
	 * 服装
	 */
	public function clothes(){
		//查看
		$uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $servid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $servid;
        $clothesInfo = array();
        $partInfo = array();
        $suitInfo = array();
        $touchInfo = array();
        $total = 0;
        if ($_GET['sevid'] != $servid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
            $clotheInfo = Master::getAct6140($uid);
            $allClothes = $clotheInfo->info['clothes'];
            foreach($allClothes as $v){
                $clotheCfg = Game::getCfg_info("use_clothe",$v);
                $clothesInfo[] = array("clotheId" => $v,"name" => $clotheCfg['name'], "part" => $clotheCfg['part']);
                if(isset($partInfo[$clotheCfg['part']])){
                    $partInfo[$clotheCfg['part']] += 1;
                }else{
                    $partInfo[$clotheCfg['part']] = 1;
                }
            }
            $suitCfg = Game::getcfg("clothe_suit");
            foreach($suitCfg as $v){
                $ok = true;
                foreach($v['clother'] as $clotheId){
                    if(!in_array($clotheId,$allClothes)){
                        $ok = false;
                        break;
                    }
                }
                if($ok){
                    if(empty($suitInfo[$v['id']])){
                        $suitLv = 1;
                        if(!empty($clotheInfo->info['suit'][$v['id']])){
                            $suitLv = $clotheInfo->info['suit'][$v['id']];
                        } 
                        $suitInfo[$v['id']] = array("name" => $suitCfg[$v['id']]['name'],"lv" => $suitLv);
                    }
                    $total++;
                }
            }
            //计算每天点击多少下
            $db = Common::getDbBySevId($servid,'flow');
            $table_div = Common::get_table_div();
            for($i = 0;$i < $table_div;$i++){
                $nowtime = strtotime(date('Y-m-d 24:00:00',Game::get_now()));
                $chaTime = $nowtime - 30*86400;
                $table = 'flow_event_'.Common::computeTableId($i);
                $sql = "SELECT `ftime` FROM {$table} WHERE `ctrl` = 'setClothe' AND `uid` = {$uid} AND `ftime` > {$chaTime};" ;
                $result = $db->fetchArray($sql);
                if (is_array($result)){
                    foreach ($result as $k => $value){
                        $time = date('Y-m-d', $value['ftime']);
                        $touchInfo[$time] += 1;
                    }
                }
            }
        }
        sort($clothesInfo);
        ksort($partInfo);
        ksort($suitInfo);
        ksort($touchInfo);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    //伙伴信物
    public function herotokens(){
        //查看
		$uid = $this->uid;
        if (!empty($uid) && !is_numeric($uid)){
            echo "<script>alert('uid格式错误!');</script>";
            return false;
        }
        $servid = Game::get_sevid($this->uid);
        $_GET['sevid'] = $servid;
        $tokenInfo = array();
        $loveJbInfo = array();
        if ($_GET['sevid'] != $servid && $uid){
            echo "<script>alert('不可查看跨服的玩家');</script>";
            $noShow = true;
        }elseif($uid){
            $tokenAct = Master::getAct2001($uid);
            $allTokens = $tokenAct->info['tokens'];
            foreach($allTokens as $heroId => $token){
                foreach ($token as $tokenid => $value) {
                    $tokenInfo[] = array("heroid" => $heroId,"tokenid"=>$tokenid,"lv" =>$value['lv'],"isJihuo" => $value['isActivation']);
                }
            }
            Common::loadModel('HeroModel');
			$heroModel = new HeroModel($uid);
            $heroInfo = $heroModel->info;

            $Act6001Model = Master::getAct6001($uid);
            $heroJB = $Act6001Model->info['heroJB'];
            foreach($heroJB as $k=>$value){
                $loveJbInfo[$value['id']] = array("love" => $heroInfo[$value['id']]['love'],"jiban" => $value['num']);
            }
        }
        sort($tokenInfo);
        ksort($loveJbInfo);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
}
