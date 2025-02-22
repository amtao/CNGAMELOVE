<?php
require_once dirname ( dirname ( __FILE__ ) ) . '/common.inc.php';
Common::loadModel('OrderModel');
$platformList = OrderModel::get_all_platform();
foreach ($platformList as $key => $value){
    $data[$key]['name'] = $value;
}
echo json_encode($data);