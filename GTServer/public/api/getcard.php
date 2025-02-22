<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

/*
获取兑换码：
请求地址：http://{$domain}/api/getcard.php
请求方式：POST
请求参数：
 * @param	int		is_ajax		1
 * @param	int		type		兑换码类型

响应结果：
 * @return	json	结果以json格式返回，格式如下
 * 		state	结果，0：失败 1：成功
 * 		result	兑换码
 */

Common::loadModel("CommonModel");
$caode_pc = CommonModel::getValue('act_caode_pc');
$caode_pc = json_decode($caode_pc,1);
if(empty($caode_pc['is_has'])){
	$caode_pc['is_has'] = array();
}
$pc_zhuanyong = Common::getselfcfg('pc_zhuanyong'); //此处是商务配置的兑换码标识
foreach($pc_zhuanyong as $type){
	if( in_array($type , $caode_pc['is_has'])){ //已存储该类型的兑换码
		continue;
	}
	//没有存储则存储
	$sql = "select * from `act_acode` where `type` = ".$type." and `state` = 0";
	$db = Common::getMyDb();
	$data = $db->fetchArray($sql);
	foreach($data as $v){
		$caode_pc['acode'][] = $v['acode'];
	}
	$caode_pc['is_has'][]   = $type;
}
if(empty($caode_pc['acode'])){
	echo json_encode(array(
		'state' => 0,
		'result' => '兑换码已空',
	),JSON_UNESCAPED_UNICODE);
	exit ();
}
$get_acode = Array_pop($caode_pc['acode']);

Common::set_vo_common('act_caode_pc',json_encode($caode_pc,JSON_UNESCAPED_UNICODE),1);

echo json_encode(array(
	'state' => 1,
	'result' => $get_acode,
),JSON_UNESCAPED_UNICODE);
exit ();







