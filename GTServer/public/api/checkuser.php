<?php

require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

function returnRes($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$data = array();
$uname = trim ( $_REQUEST ['rolename'] );
$roleid = intval($_REQUEST['roleid']);
$sid = intval($_REQUEST['sid']);
if ( empty($sid) ) {
    returnRes(array('code'=>0,'msg'=>'参数错误（sid）'));
}

$SevidCfg = Common::getSevidCfg($sid);// 先加载不然会出错
$db = Common::getMyDb();
// 如果有角色id
if ( !empty($roleid) ) {
    // do nothing
}
// 根据角色名定位角色信息
else if ( !empty($uname) ) {
    $sql = "select * from `index_name` where `name`='{$uname}' ";//查找玩家的姓名
    $info = $db->fetchRow($sql);
    if ( !empty($info) ) {
        $roleid = $info['uid']; //玩家id
    } else {
        returnRes(array('code'=>0,'msg'=>'角色名称不存在'));
    }
}
else {
    returnRes(array('code'=>0,'msg'=>'参数错误（roleid|rolename）'));
}

if ( !empty($roleid) ) {
    $user_serverid = Game::get_sevid($roleid);
    if ($sid != $user_serverid){
        returnRes(array('code'=>0,'msg'=>'角色不存在'));
    }
    Common::loadModel('UserModel');
    $userModel = new UserModel($roleid);
    $user_info = $userModel->info;
    if ( !empty($user_info) ) {
        returnRes(array('code'=>1,'msg'=>'成功','data'=>array('roleID'=>$user_info['uid'], 'roleName'=>$user_info['name'], 'roleLevel'=>$user_info['level'])));
    }
}
returnRes(array('code'=>0,'msg'=>'角色不存在'));
