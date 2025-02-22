<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
define( 'CONFIG_ADM_DIR' , ROOT_DIR . '/administrator/config' );
define( 'CON_DIR', ROOT_DIR . '/administrator/controller' );
define( 'TPL_DIR', ROOT_DIR . '/administrator/tpl/' );
Common::loadModel("Master");

$sign = isset($_GET['sign']) ? $_GET['sign'] : '';
unset($_GET['sign']);
ksort($_GET);
$query = http_build_query($_GET);
if (Game::getAdminApiSign($query) != $sign) {
    echo "<script>alert('2222222222222222');</script>";

    header('HTTP/1.1 404 Not Found'); exit();
}

$sevid = isset($_GET['sevid'])?intval($_GET['sevid']):null;
if (empty($sevid)){
    Master::error('sevid_null');
}
$SevidCfg = Common::getSevidCfg($sevid);

$con = empty( $_GET['mod'] ) ? 'Index' : ucfirst($_GET['mod']);
$act = empty( $_GET['act'] ) ? 'run' : $_GET['act'];

$conFile = CON_DIR . '/' . $con . '.php';
if (!is_file($conFile) ) {
    exit('Controller file not exists');
}

require_once $conFile;
$object = new $con();
if ( !method_exists($object,$act) ) {
    exit('Method not exists');
}
$object->$act();