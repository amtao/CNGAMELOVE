<?php
/**
 * 后台入口
 *
 * @category   public
 * @author     fisher.lee<63764977@qq.com>
 * @version    $Id: admin.php 22 2011-03-29 21:02:36Z $
 */
require_once './common.inc.php';

session_start();

define( 'CONFIG_ADM_DIR' , ROOT_DIR . '/administrator/config' );
define( 'CON_DIR', ROOT_DIR . '/administrator/controller' );
define( 'TPL_DIR', ROOT_DIR . '/administrator/tpl/' );

if ( !defined('LOCALHOST') ) {
	Common::checkLogin();//验证登陆状态
}


$_SESSION['CURRENT_USER'] = empty($_SESSION['CURRENT_USER']) ? trim($_GET['user']) : $_SESSION['CURRENT_USER'];

if ( empty($_SESSION['CURRENT_USER']) ) {
	echo "<script>alert('请先登录');</script>";
	header('HTTP/1.1 404 Not Found'); exit();
}
//账号验证
Common::loadVoComModel('ComVoComModel');
$userKey = 'userAccount';
$ComVoComModel = new ComVoComModel($userKey, true);
$userAccount = $ComVoComModel->getValue();
if (empty($userAccount)){
	$userAccount = include ( ROOT_DIR . '/administrator/config/userAccount.php');
}
//if ( empty($userAccount[$_SESSION['CURRENT_USER']]) ) {
//    echo "<script>alert('请联系管理员开通权限!');</script>";
//    header('HTTP/1.1 404 Not Found'); exit();
//}
Common::loadModel('ServerModel');
$serversList = ServerModel::getServList();

include TPL_DIR . 'index_list.php';
