<?php
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
Common::loadModel('UserModel');
Common::loadModel('PlatFormModel');
PlatFormModel::loadPlatFormCfg('linyouqzgj');


//post的一些参数
$data = json_decode($_POST['data'], 1);
$sign = $_POST['sign'];
$linyou_uid = $data['uid'];
$open_id = 'linyouqzgj_' . $data['uid'];
$uids = Common::getUid($open_id);//获取UID
$uid = $uids['uid'];
if (empty($uid)){
    $return = error();
    echo json_encode($return);
    exit; 
}
$zone = $data['zone'];
//验证参数
if (empty($_POST) || empty($_POST['data']) || empty($_POST['sign'])){
    $return = array(
        'errno' => -1008,
        'errmsg' => "参数错误",
        'data' => array(
        ),
    );
    echo json_encode($return);
    exit;
}
//一些数据
$signKey = SNS_SIGNKEY;
// //不能读取999区的
// if ($zone == 999 || $zone <= 0) {
//     $return = error();
//     echo json_encode($return);
//     exit;
// }
$SevidCfg = Common::getSevidCfg($zone);//子服ID
//验证签名
$auth = md5($_POST['data'] . $signKey);
//签名错误
if ($sign != $auth) {
    $return = array(
        'errno' => 10001,
        'errmsg' => "签名错误",
        'data' => array(
        ),
    );
    echo json_encode($return);
    exit;
} else {
    //验证签名成功，查询玩家等级
    $UserModel = new UserModel($uid);
    $level = $UserModel->info['level'];
    //如果没有选择角色的话，等级为空
    if (empty($level)){
        $return = error();
        echo json_encode($return);
        exit;
    }
    $return = array(
        'errno' => 1000,
        'errmsg' => "成功",
        'data' => array(
            'zone' => $zone,
            'uid' => $linyou_uid,
            'level' => $level
        ),
    );
    echo json_encode($return);
    exit;    
}

//失败时，统一
function error () {
    $return = array(
        'errno' => 1116,
        'errmsg' => "查询失败",
        'data' => array(
        ),
    );
    return $return;
}
