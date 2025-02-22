<?php

require_once dirname(dirname(__FILE__)) . '/common.inc.php';

function getServerList() {
    Common::loadModel('ServerModel');
    $serverid = ServerModel::getDefaultServerId();
    $SevidCfg = Common::getSevidCfg($serverid);// 先加载不然会出错
    $serverList= ServerModel::getServList();
    ksort($serverList);
    //服务器列表
    $newServerList = array();
    foreach ($serverList as $k => $v) {
	//不是白名单
	if (!Common::istestuser()) {
	    if ($sevid != 999 && $k == 999) {
		continue;
	    }
	    if (!Game::is_over($v['showtime'])) {
		continue;
	    }
	}
	$newServerList['servers'][$v ['id']] = array(
	    'server_id' => $v ['id'],
	    'name' => $v['name']['zh']
	);
    }

    echo json_encode($newServerList, JSON_UNESCAPED_UNICODE);
}

getServerList();
exit();
