<?php
/**
 * 后台入口
 *
 * @category   public
 * @author     fisher.lee<63764977@qq.com>
 * @version    $Id: admin.php 22 2011-03-29 21:02:36Z $
 */

ini_set('display_errors', 'on');//把错误输出到浏览器
require_once 'common.inc.php';

//session_start();
define('CONFIG_ADM_DIR', ROOT_DIR . '/administrator/config');
define('CON_DIR', ROOT_DIR . '/administrator/controller');
define('TPL_DIR', ROOT_DIR . '/administrator/tpl/');
Common::loadModel("Master");
Common::loadModel('UserModel');
if (!defined('LOCALHOST')) {


    Common::checkLogin();//验证登陆状态
}

//获取/设置 服务器ID
$sevid = isset($_GET['sevid']) ? intval($_GET['sevid']) : null;
if (empty($sevid)) {
    Master::error(VERSION_LOW);
}
$SevidCfg = Common::getSevidCfg($sevid);

$_SESSION['CURRENT_USER'] = empty($_SESSION['CURRENT_USER']) ?
    trim($_GET['user']) : $_SESSION['CURRENT_USER'];
if (empty($_SESSION['CURRENT_USER'])) {
    echo "<script>alert('请先登录');</script>";
    header('HTTP/1.1 404 Not Found');
    exit();
}
Common::loadModel('OperateLogModel');
// $ta = new ThinkingDataAnalytics(new FileConsumer("/data/logs/logBus"));

//Common::loadLang('admin_lang');

$con = empty($_GET['mod']) ? 'Index' : ucfirst($_GET['mod']);
$act = empty($_GET['act']) ? 'run' : $_GET['act'];

$conFile = CON_DIR . '/' . $con . '.php';
if (!is_file($conFile)) {
    exit('Controller file not exists');
}

require_once $conFile;
$object = new $con();

if (!method_exists($object, $act)) {
    exit('Method not exists');
}

//后台流水
//流水类
Common::loadModel("FlowModel");
$cmd_FlowModel = null;//流水全局变量
$f_uid = isset($_GET['uid']) ? $_GET['uid'] : (isset($_POST['uid']) ? $_POST['uid'] : 0);
if ($f_uid > 0) {
    $cmd_FlowModel = new FlowModel($f_uid, 'admin', $_GET['act'], $_SESSION["CURRENT_USER"]);
}


try {
    $object->$act();
} catch (Exception $ex) {
    echo json_encode(array('NOT FOUND'));
    print $ex;
    die;
}


//流水信息写入
if ($cmd_FlowModel != null) {
    $cmd_FlowModel->destroy_now();
}

Master::click_destroy();
Master::free_all_lock();

