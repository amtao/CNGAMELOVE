<?php
require_once dirname ( dirname ( __FILE__ ) ) . '/common.inc.php';
function getTwoServerList() {
	$serversList = CommonModel::getAllcfg ( 'server_list', 1 );
	$serversList_JsonDecode = json_decode ( $serversList, 1 );
	echo json_encode ( $serversList_JsonDecode ,JSON_UNESCAPED_UNICODE);
}
getTwoServerList ();
exit ();