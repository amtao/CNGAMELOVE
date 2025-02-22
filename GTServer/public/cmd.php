<?php
//报错组合
/**
 * 接口入口
 *
 * @category   public
 * @author     kinnus<282024004@qq.com>
 * @version    $Id: command.php 2011-09-27 16:16:36Z $
 */
$use_encode = 0;
if(!empty($_GET['enc']))
{
	require_once dirname( __FILE__ ) . '/../lib'. '/aes.php';
	$str = $_GET['encstr'];
	$str= str_replace(' ','+',$str);
	if(!empty($str))
	{
		$aes = new AES();
		$str = $aes->decrypt($str,$aes->getSecretKey());
		$url_array = explode('&',$str);  
		
		if (is_array($url_array))  
		{  
			foreach ($url_array as $var)  
			{  
				$var_array = explode('=',$var);  
				$_GET[$var_array[0]]=$var_array[1];  
			}  
		}  
	}
	
	$use_encode = 1;
}
//参数 反斜杠统一处理
if (!get_magic_quotes_gpc()) {
    function addslashesDeep($var) {
        return is_array($var) ? array_map('addslashesDeep', $var) : addslashes($var);
    }
    $_GET = addslashesDeep($_GET);
    $_POST = addslashesDeep($_POST);
}

require_once dirname( __FILE__ ) . '/common.inc.php';

//用户端验证
if (Common::isFromHacker()) {
    Master::error(PARAMS_ERROR.'_'.__LINE__);
}

$microtime = microtime(true);
$xhprof_on = false;
if(defined('XHPROF_ON') && XHPROF_ON ) $xhprof_on = true;
if($xhprof_on) xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY); //启动xhprof


//解析URL各参数
$ver = $_GET['ver'];//版本号 2.2.1
$param_json = file_get_contents("php://input");
if($use_encode ==1)
{
	$aes = new AES();
	$param_json = $aes->decrypt($param_json,$aes->getSecretKey());
}

$uid = isset($_GET['uid'])?intval($_GET['uid']):null;
$token = isset($_GET['token'])?$_GET['token']:null;
$sevid = isset($_GET['sevid'])?intval($_GET['sevid']):null;

if (empty($sevid)){
	Master::error(SYSTEM_VERSION_LOWER);
}
//获取/设置 服务器ID
$SevidCfg = Common::getSevidCfg($sevid);

$param = Common::getHttpJson($param_json);
//$param = json_decode($param_json,1);
if (!is_array($param)){
	Master::error(LOGIN_SERVER_DELAY_ENTER_ERROR);
}

//model 容器
Common::loadModel("Master");
//Common::loadModel('ServerModel');
//if (ServerModel::getStatus() == 6) {
//	if(!Common::istestuser()){
//		Master::error('服务器维护中');
//	}
//}

//判定是否登录接口
$is_gamecmd = true;
if (isset($param['login']['loginAccount'])){
	//只在登录接口做判断
	Common::loadModel('ServerModel');
	$openDay = ServerModel::getOpenDays($sevid);
	if(!Common::istestuser()){
		if($openDay == 0){
			Master::error(SERVER_NO_OPEN);
		}
		$sList = ServerModel::getServList();
		if($sList[$sevid]['status'] == 6){//服务器维护中
			Master::error(SERVER_WEIHU);
		}
	}

    //去除本接口下的其他接口
	$param = array(
		'login'=> array(
			'loginAccount' => $param['login']['loginAccount'],
		),
	);
	//登录接口 不属于游戏逻辑
	$is_gamecmd = false;
	$uid = 0;
}

//判定是否获取公告
if (isset($param['login']['getNotice'])){
	//只在登录接口做判断
	Common::loadModel('ServerModel');
	$openDay = ServerModel::getOpenDays($sevid);
	if(!Common::istestuser()){
		if($openDay == 0){
			Master::error(SERVER_NO_OPEN);
		}
		$sList = ServerModel::getServList();
		if($sList[$sevid]['status'] == 6){//服务器维护中
			Master::error(SERVER_WEIHU);
		}
	}

    //去除本接口下的其他接口
	$param = array(
		'login'=> array(
			'getNotice' => $param['login']['getNotice'],
		),
	);
	//获取公告接口 不属于游戏逻辑
	$is_gamecmd = false;
	$uid = 0;
}

if ($is_gamecmd){
	//检查刷小号
	//if (!isset($param['user']['adok'])){//adok接口不做判定 减小压力
    //    //检查参数
    //    Master::checkParam($param);
	//}

    //加用户私锁
	Master::get_lock(1,"user_".$uid);
	
	//验证TOKEN
	$sav_token = Common::getToken($uid);
	if (empty($sav_token)){
		Master::error(LOGIN_GUOQI);
	} elseif (strcmp($sav_token,$token) != 0){
		Master::error(LOGIN_YIDIDENGLU,20000);
	}
	//根据id单向转移
	// $loginlist = Game::get_peizhi('gm_login');
	$loginlist = Game::get_gm_login();
	if(!empty($loginlist)){
	    if(!empty($loginlist[$uid])){
	        $uid = intval($loginlist[$uid]);
	    }
	}

	//检查刷小号
	if (!isset($param['user']['adok'])){//adok接口不做判定 减小压力

		//封设备
		$Redis9Model = Master::getRedis9();
		$sb_info = $Redis9Model->is_exist($uid);
		if(!empty($sb_info)){
			Master::error(SYSTEM_FREEZE_OPENID);
		}

		//封号
		$Sev26Model = Master::getSev26();
		$closure_info = $Sev26Model->isClosure($uid);
		if(!empty($closure_info)){
			Master::error(SYSTEM_FREEZE_UID);
		}
	}
	
	//用户类
	$UserModel = Master::getUser($uid);
	$UserModel->addOnLine();

	Master::set_uid($uid);
}

//遍历协议数组
$mod_arr = array();//控制器

//加锁配置文件
$lock_cfg = Game::getBaseCfg('lock');

// $ta = new ThinkingDataAnalytics(new FileConsumer("/data/logs/logBus"));

unset($param['rsn']);
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
				//接口是否需要加锁 功能锁 锁按照功能进行定义
				if (!empty($lock_cfg[$k_mod][$k_ctrl]['key'])){
					//执行加锁
					Master::get_lock($lock_cfg[$k_mod][$k_ctrl]['type'],$lock_cfg[$k_mod][$k_ctrl]['key']);
				}
				//执行协议
                if (method_exists($mod_arr[$k_mod], 'check')) {
                    $mod_arr[$k_mod]->check($k_ctrl);
                }
				$mod_arr[$k_mod]->$k_ctrl($v_da);
			}else{
				Master::error(GONGNENG_NO_OPEN.$k_mod.'_'.$k_ctrl);
			}

       		$cmd_FlowModel->destroy_now();
	}
}


if ($is_gamecmd){
    
	//检查聊天返回
	$Sev22Model = Master::getSev22();
	$Sev22Model->list_click($uid);
    $Sev6012Model = Master::getSev6012();
    $Sev6012Model->list_click($uid);
    $Sev6013Model = Master::getSev6013();
    $Sev6013Model->list_click($uid);
	//检查跨服聊天返回
	$Sev25Model = Master::getSev25();
	$Sev25Model->list_click($uid);
	//检查跑马灯返回
    $Sev91Model = Master::getSev91();
    $Sev91Model->list_click($uid);
	
	//联盟聊天
	$Act40Model = Master::getAct40($uid);
	if($Act40Model->info['cid'] > 0){
		$Sev24Model = Master::getSev24($Act40Model->info['cid']);
		$Sev24Model->list_click($uid);
	}
	
	Common::loadModel('HoutaiModel');
	$hd300Cfg = HoutaiModel::get_huodong_info('huodong_300');
	if(!empty($hd300Cfg)){
		$Sev62Model = Master::getSev62();
		if(!empty($Sev62Model->info)){
			$Sev62Model->list_click($uid);
		}
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
Master::free_all_lock();

Master::setTime();


//输出返回
Master::output($uid,$use_encode);


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
	$url_this = round($time,2).'-'.$uid.'-'.$param_json;
	
	$url = "http://{$_SERVER['SERVER_NAME']}/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_foo";
	$xhprof_info[$run_id.$id_suffix] = '<a href="'.$url.'" target="_blank">'.$url_this.'</a>';
	$cache->set($key,$xhprof_info);
}

// $ta->flush();
// $ta->close();

exit;
