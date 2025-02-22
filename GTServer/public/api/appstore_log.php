<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;

if (!empty($params)){
	
    	$params['type'] = empty($params['type'])?0:$params['type'];
		$params['cs1'] = empty($params['cs1'])?'':$params['cs1'];
		$params['cs2'] = empty($params['cs2'])?'':$params['cs2'];
		$params['cs3'] = empty($params['cs3'])?'':$params['cs3'];
		$params['cs4'] = empty($params['cs4'])?'':$params['cs4'];
		$params['cs5'] = empty($params['cs5'])?'':$params['cs5'];
		$params['cs6'] = empty($params['cs6'])?'':$params['cs6'];
		$params['cs7'] = empty($params['cs7'])?'':$params['cs7'];
		$params['cs8'] = empty($params['cs8'])?'':$params['cs8'];
		
		$db = Common::getComDb();
		$sql = "insert into `fail_order` 
			(`type`, `cs1`,`cs2`,`cs3`,`cs4`,`cs5`,`cs6`,`cs7`,`cs8`)
			values (
			'{$params['type']}', 
			'{$params['cs1']}', 
			'{$params['cs2']}', 
			'{$params['cs3']}', 
			'{$params['cs4']}', 
			'{$params['cs5']}', 
			'{$params['cs6']}', 
			'{$params['cs7']}', 
			'{$params['cs8']}'
			 )";
		$db->query($sql);
		
}
