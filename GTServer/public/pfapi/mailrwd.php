<?php

/**
 * 运营平台
 * @author wenyj
 * @version
 *   - 20170915, init
 * @return JSON
 *   code : 处理状态，200：成功，201：参数错误，202：签名错误，203：其它错误
 *   msg ： 返回信息
 *   data ： []，数组
 */
//if (file_exists(dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php')) {
//    require_once dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php';
//} else {
//    exit('param invalid(_pf)');
//}

require_once dirname(dirname(__FILE__)) . '/common.inc.php';
$key = "1447d1d7cac7d963d024cba871e600e0";

//define('LOG_API_ACTIVE_REWARD', LOG_PATH . '/apilog/' . SNS . '_' . SERVER_ID . '_' . strtr(basename(__FILE__), array('.' => '_')) . '.log');

function returnRes($data, $msg = '') {
    if (!empty($msg)) {
	Game::logMsg(LOG_API_ACTIVE_REWARD, $msg);
    }
    exit(json_encode($data));
}

function returnStage($data,$stage) {
    $res = array();
    $hd_rwd = $data['stage'];
    if (empty($hd_rwd)){
        returnRes(array('code' => 203, 'msg' => '获取阶段奖励失败1', 'data' => array()), '获取阶段奖励失败');
    }
    foreach ($hd_rwd as $v){
        if ($v['id'] == $stage){
            return $v['rwd'];
        }
    }
    return $res;
}


function get_sevid($uid){
    if($uid == 0){
        if(defined('IS_TEST_SERVER') && IS_TEST_SERVER){
            return 999;
        }
        returnRes(array('code' => 201, 'msg' => '参数错误（角色无效）', 'data' => array()), '参数错误（角色无效）');
    }
    if ($uid < 1000000){
        if(defined('IS_TEST_SERVER') && IS_TEST_SERVER){
            return 999;
        }
        returnRes(array('code' => 201, 'msg' => '参数错误（角色无效）', 'data' => array()), '参数错误（角色无效）');
    }else{
        return intval($uid/1000000);
    }
}

// 记录request参数
$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
Game::logMsg(LOG_API_ACTIVE_REWARD, 'request = ' . var_export($params, 1));
if (empty($params)) {
    returnRes(array('code' => 201, 'msg' => '参数为空', 'data' => array()), '参数为空');
}
// 验证参数
$params = array_map('trim', $params);
if (empty($params['roleid'])) {
    returnRes(array('code' => 201, 'msg' => '参数错误（角色无效）', 'data' => array()), '参数错误（角色无效）');
}
$serverid = get_sevid($params['roleid']);
if ($params['serverid'] != $serverid) {
    returnRes(array('code' => 201, 'msg' => '参数错误（角色和服务器不匹配）', 'data' => array()), '参数错误（角色和服务器不匹配）');
}

if ($params['sign'] != md5($params['roleid'].$params['serverid'].$params['stage'].$params['platform'].$key)) {
    returnRes(array('code' => 205, 'msg' => '参数错误（签名无效）', 'data' => array()), '参数错误（角色无效）');
}

// 查询key的记录是否存在
$SevidCfg = Common::getSevidCfg($params['serverid']);
Common::loadModel('ServerModel');
$serverid = ServerModel::getDefaultServerId();
$db = Common::getDbBySevId(1);
$nowtime = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
if ( 0 < $db->getCount('mailrwd', "`sevid`='{$params['serverid']}' and `roleid`='{$params['roleid']}' and `stage`='{$params['stage']}'") ) {
    returnRes(array('code' => 203, 'msg' => '已兑换过', 'data' => array()), '已兑换过');
}

//获取奖励信息
Common::loadModel('HoutaiModel');
$hd_cfg = HoutaiModel::get_huodong_info('huodong_6214');
if (empty($hd_cfg)){
    returnRes(array('code' => 203, 'msg' => '获取活动信息失败', 'data' => array()), '获取活动信息失败');
}
$items_arr = returnStage($hd_cfg,$params['stage']);
if (empty($items_arr)){
    returnRes(array('code' => 203, 'msg' => '获取阶段奖励失败2', 'data' => array()), '获取阶段奖励失败');
}
// 先将记录添加避免有重复操作
$sql = "insert into `mailrwd` 
    (`sevid`, `roleid`, `platform`, `stage`, `time`)
    values (
    '{$params['serverid']}', '{$params['roleid']}',
    '{$params['platform']}', '{$params['stage']}',
    '{$nowtime}'
    )";
if ($db->query($sql) === false) {
    returnRes(array('code' => 203, 'msg' => '数据更新失败', 'data' => array()), '数据更新失败');
}
// 邮件发放奖励
$title = '运营活动奖励';
$content = '感谢您一直以来对游戏的理解和支持~祝您游戏愉快！';
$mailModel = Master::getMail($params['roleid']);
$mailModel->sendMail($params['roleid'], $title, $content, 1, $items_arr);
Master::click_destroy();

returnRes(array('code' => 200, 'msg' => '成功', 'data' => array()), '成功');

