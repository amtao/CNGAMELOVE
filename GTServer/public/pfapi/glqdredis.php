<?php

require_once dirname(dirname(__FILE__)) . '/common.inc.php';

// 记录request参数
$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
if (empty($params)) {
    returnRes(array('code' => 201, 'msg' => '参数为空', 'data' => array()), '参数为空');
}
// 验证参数
$params = array_map('trim', $params);
// 查询key的记录是否存在
$SevidCfg = Common::getSevidCfg($params['sid']);
$Redis6208Model = Master::getRedis6208('190501_20190501');
$data = $Redis6208Model->zRevRange();
if (!empty($data)){
    foreach ($data as $v){
        echo $v." ";
    }
}
exit();
