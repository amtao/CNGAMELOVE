<?php
require_once dirname(__FILE__) . '/common.inc.php';
Common::loadModel('ServerModel');
$serverid = ServerModel::getDefaultServerId();
Common::getSevidCfg($serverid);// 先加载不然会出错
$platformInfos = Common::getPlatformInfo();
echo json_encode($platformInfos,true);
exit;


