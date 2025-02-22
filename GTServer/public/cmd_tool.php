<?php
//报错组合
error_reporting(E_ALL);
ini_set('display_errors','on');
/**
 * 接口入口
 *
 * @category   public
 * @author     kinnus<282024004@qq.com>
 * @version    $Id: command.php 2011-09-27 16:16:36Z $
 */
//参数 反斜杠统一处理
if (!get_magic_quotes_gpc()) {
    function addslashesDeep($var) {
        return is_array($var) ? array_map('addslashesDeep', $var) : addslashes($var);
    }
    $_GET = addslashesDeep($_GET);
    $_POST = addslashesDeep($_POST);
}

require_once dirname( __FILE__ ) . '/common.inc.php';


$microtime = microtime(true);
$xhprof_on = false;
if(defined('XHPROF_ON') && XHPROF_ON ) $xhprof_on = true;
if($xhprof_on) xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY); //启动xhprof


//解析URL各参数
$ver = $_GET['ver'];//版本号 2.2.1
$param_json = file_get_contents("php://input");

$uid = isset($_GET['uid'])?intval($_GET['uid']):null;
$token = isset($_GET['token'])?$_GET['token']:null;
$sevid = isset($_GET['sevid'])?intval($_GET['sevid']):null;

if (empty($sevid)){
	Master::error(SYSTEM_VERSION_LOWER);
}
//获取/设置 服务器ID
$SevidCfg = Common::getSevidCfg($sevid);

Common::loadModel('ServerModel');
$openDay = ServerModel::getOpenDays($sevid);
if(!Common::istestuser()){
    if($openDay == 0){
        Master::error(SERVER_NO_OPEN);
    }
    $sList = HoutaiModel::read_servers();
    if($sList[$sevid]['status'] == 6){//服务器维护中
        Master::error(SERVER_WEIHU);
    }
}

$param = json_decode($param_json,1);
if (!is_array($param)){
	Master::error(JSON_CANSHU_CUOWU.$param_json);
}

//model 容器
Common::loadModel("Master");

if (!empty($uid)){
    
	//验证TOKEN
	$sav_token = Common::getToken($uid);
	if (empty($sav_token)){
		Master::error(LOGIN_FAILED);
	} elseif (strcmp($sav_token,$token) != 0){
		Master::error(SERVER_REQUEST_FAIL);
	}
	//根据id单向转移
	$loginlist = Game::get_peizhi('gm_login');
	if(!empty($loginlist)){
	    if(!empty($loginlist[$uid])){
	        $uid = $loginlist[$uid];
	    }
	}
	
	//封设备
	Common::loadModel('HoutaiModel');
	$sb_base_cfg = HoutaiModel::read_all_peizhi('fengsb');
	if(!empty($sb_base_cfg)){
	    $db = Common::getDbBySevId($sevid);
        $sql = "select `ustr` from `gm_sharding` where `uid` = {$uid}";
        $opendata = $db->fetchRow($sql);
	    $sb_base_cfg = json_decode($sb_base_cfg,true);
	    if(!empty($sb_base_cfg) && in_array($opendata['ustr'], $sb_base_cfg)){
	        Master::error(SYSTEM_FREEZE_UID);
	    }
	}
	
	//封号
	Common::loadModel('HoutaiModel');
	$fenglist = HoutaiModel::read_base_peizhi('fenghao');
	if(!empty($fenglist)){
	    $fenglist = json_decode($fenglist,true);
	    if(in_array($uid, $fenglist)){
	        Master::error(SYSTEM_FREEZE_UID);
	    }
	}
	
	//用户类
	$UserModel = Master::getUser($uid);
	
	Master::set_uid($uid);
}

//遍历协议数组
$mod_arr = array();//控制器

foreach ($param as $k_mod => $v_mod){
	//控制器列表
	if (empty($mod_arr)){
		$apifile = API_DIR . '/' . $k_mod . '.php';
		if (!file_exists($apifile)) {
			Master::error('file_exists' . $apifile);
		}
		require $apifile;
		//实例化控制器
		$cmdMod = $k_mod . 'Mod';
		if ($k_mod == 'login'){
			$mod_arr[$k_mod] = new $cmdMod();
		}else{
			if (empty($uid)){
				Master::error('uid_null');
			}
			//验证 如果没有取名字  只能走取名字接口
			$mod_arr[$k_mod] = new $cmdMod($uid);
		}
	}
	
	//遍历协议
	foreach($v_mod as $k_ctrl => $v_da){
	        //执行协议
	        //流水类
	        Common::loadModel("FlowModel");
	        $cmd_FlowModel = new FlowModel($uid,$k_mod,$k_ctrl,$v_da);

			if ( method_exists($mod_arr[$k_mod], $k_ctrl) ) {
				//执行协议
				$mod_arr[$k_mod]->$k_ctrl($v_da);
			}else{
				Master::error(METHOD_ERROR.$k_mod.'_'.$k_ctrl);
			}

       		$cmd_FlowModel->destroy_now();
	}
}


if (!empty($uid)) {
    

	//检查聊天返回
	$Sev22Model = Master::getSev22();
	$Sev22Model->list_click($uid);

    $Sev6012Model = Master::getSev6012();
    $Sev6012Model->list_click($uid);

    $Sev6013Model = Master::getSev6013();
    $Sev6013Model->list_click($uid);

	//联盟聊天
	$Act40Model = Master::getAct40($uid);
	if($Act40Model->info['cid'] > 0){
		$Sev24Model = Master::getSev24($Act40Model->info['cid']);
		$Sev24Model->list_click($uid);
	}
	
	//生效列表信息 => 当前活动版本
	$cache 	= Common::getMyMem();
	$base_ver = 'hd_base_ver_'.$sevid;
	$ver 	= $cache->get($base_ver);
	$Act199Model = Master::getAct199($uid);
	if( !empty($ver['ver']) && $Act199Model->info['ver'] != $ver['ver']){
		$Act199Model->add_ver($ver['ver']); //更新活动版本
		//下发活动生效列表
		$Act200Model = Master::getAct200($uid);
		$Act200Model->back_data();
	}
	
	
	$UserModel->good_morning();
	
	
	//检查活动返回信息
	Master::click_act_udata();
	//返回需要更新的英雄信息
	Master::back_hero_rst($uid);
	//各个基础类 数据写入
	Master::click_destroy();
}

// 解锁

Master::setTime();


//输出返回
Master::output($uid);


$time = microtime(true) - $microtime;
if($xhprof_on && $time >=1)
{
	//停止xhprof
	$xhprof_data = xhprof_disable();
	
	//取得统计数据
	//print_r($xhprof_data);
	
	$XHPROF_ROOT = realpath(dirname(__FILE__) . '/');
	include_once $XHPROF_ROOT . "/xhprof/xhprof_lib/utils/xhprof_lib.php";
	include_once $XHPROF_ROOT . "/xhprof/xhprof_lib/utils/xhprof_runs.php";
	
	//保存统计数据，生成统计ID和source名称
	$xhprof_runs = new XHProfRuns_Default();
	$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo"); //source名称是xhprof_foo
	
	//弹出一个统计窗口，查看统计信息
	$key = "xhprof";
	$cache = Common::getMyMem();
	$xhprof_info = $cache->get($key);
	if(!$xhprof_info) $xhprof_info = array();
	
	$id_suffix = "_".date("Y-m-d H:i:s");
	//$url_this = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$url_this = round($time,2).'-'.$uid.'-'.$func.$param;
	
	$url = "http://{$_SERVER['SERVER_NAME']}/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_foo";
	$xhprof_info[$run_id.$id_suffix] = '<a href="'.$url.'" target="_blank">'.$url_this.'</a>';
	$cache->set($key,$xhprof_info);
}


exit;
