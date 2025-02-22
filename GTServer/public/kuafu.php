<?php
/**
 * 跨服  服务器 接口入口
 *
 * @category   public
 * @author     kinnus<282024004@qq.com>
 * @version    $Id: command.php 2011-09-27 16:16:36Z $
 */

if (!get_magic_quotes_gpc()) {
    function addslashesDeep($var) {
        return is_array($var) ? array_map('addslashesDeep', $var) : addslashes($var);
    }
    $_GET = addslashesDeep($_GET);
    $_POST = addslashesDeep($_POST);
}

require_once dirname( __FILE__ ) . '/common.inc.php';

require( CONFIG_DIR . '/modid.php' );//控制器路由配置

$microtime = microtime(true);
$xhprof_on = false;
if(defined('XHPROF_ON') && XHPROF_ON ) $xhprof_on = true;
if($xhprof_on) xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY); //启动xhprof

//分解参数
$sev_id = $_GET['sevid'];//来源 服务器ID
$tosevid = $_GET['tosevid'];//目标 服务器ID
$func = $_GET['func'];//功能状态机 函数名
$param = $_GET['param'];//功能参数
$param_data = json_decode(stripslashes($param),1);
$key = $_GET['key'];//验证秘钥

require_once dirname(dirname( __FILE__ )) . '/public/servers/s'.$tosevid.'_config.php';

$SevidCfg = Common::getSevidCfg($sev_id);
//检查验证参数 加载配置秘钥
//.....暂时不写...
//罗列所有GET参数 , 去掉KEY字段 排序 序列化 加上配置秘钥 MD5 然后对比 key

//获取服务器状态

$cache = Common::getCacheBySevId($SevidCfg['sevid']);
$debug = $cache->get('urldebug1');
if (empty($debug)){
	$send_data = array();
}
$debug[] = array(
	'get' => $_GET,
	'post' => $_POST,
);
$debug[] = $_SERVER;

//$cache->set('urldebug1',$debug);

//功能路由
Common::loadModel("KuafuSevModel");//跨服类
//KuafuSevModel::
//执行跨服 主服务器功能
if ( method_exists(KuafuSevModel, $func) ) {
	$bak_data = KuafuSevModel::$func($param_data);
	//返回格式	
	$opt_data = array(
		0 => 1,
		1 => $bak_data,
	);
} else {
	$opt_data = array(
		0 => 0,
		1 => 'func_err_' . $func,
	);
}

//输出返回
echo json_encode($opt_data);

$time = microtime(true) - $microtime;
if($xhprof_on && $time >=2)
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
	$cache = Common::getCacheBySevId($SevidCfg['sevid']);
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
