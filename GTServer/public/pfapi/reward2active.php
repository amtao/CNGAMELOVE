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
if (file_exists(dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php')) {
    require_once dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php';
} else {
    exit('param invalid(_pf)');
}

require_once dirname(dirname(__FILE__)) . '/common.inc.php';

define('LOG_API_ACTIVE_REWARD', LOG_PATH . '/apilog/' . SNS . '_' . SERVER_ID . '_' . strtr(basename(__FILE__), array('.' => '_')) . '.log');

function returnRes($data, $msg = '') {
    if (!empty($msg)) {
	Game::logMsg(LOG_API_ACTIVE_REWARD, $msg);
    }
    exit(json_encode($data));
}

// 记录request参数
$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
Game::logMsg(LOG_API_ACTIVE_REWARD, 'request = ' . var_export($params, 1));
if (empty($params)) {
    returnRes(array('code' => 201, 'msg' => '参数为空', 'data' => array()), '参数为空');
}

//平台验证
$platform = defined('SNS') ? SNS : '';
if (empty($platform)) {
    returnRes(array('code' => 201, 'msg' => '参数错误（未知平台）', 'data' => array()), '参数错误（未知平台）');
}
Common::loadModel('OrderModel');
$Api = OrderModel::sdk_func($platform);
if (method_exists($Api, 'checkActiveSign')) {
    if (!$Api->checkActiveSign($params)) {
	returnRes(array('code' => 202, 'msg' => '签名错误', 'data' => array()), '签名错误');
    }
} else {
    returnRes(array('code' => 203, 'msg' => '接口未授权', 'data' => array()), '接口未授权');
}

// 验证参数
$params = array_map('trim', $params);
if (empty($params['roleid'])) {
    returnRes(array('code' => 201, 'msg' => '参数错误（角色无效）', 'data' => array()), '参数错误（角色无效）');
}
if (empty($params['award_no'])) {
    returnRes(array('code' => 201, 'msg' => '参数错误（奖励流水号无效）', 'data' => array()), '参数错误（奖励流水号无效）');
}
if (empty($params['active_code'])) {
    returnRes(array('code' => 201, 'msg' => '参数错误（活动类型不存在）', 'data' => array()), '参数错误（活动类型不存在）');
}
if ('recharge' != $params['active_code']) {
    returnRes(array('code' => 201, 'msg' => '参数错误（活动类型无效）', 'data' => array()), '参数错误（活动类型无效）');
}
$serverid = Game::get_sevid($params['roleid']);
if ($params['serverid'] != $serverid) {
    returnRes(array('code' => 201, 'msg' => '参数错误（角色和服务器不匹配）', 'data' => array()), '参数错误（角色和服务器不匹配）');
}
// 获取活动配置
$SevidCfg = Common::getSevidCfg($serverid);// 先加载不然会出错
$reward2actCfg = Game::get_peizhi('reward2act');
if(!isset($reward2actCfg['rwd'][$params['award_level']])){
    returnRes(array('code' => 201, 'msg' => '参数错误（奖励档位无效）', 'data' => array()), '参数错误（奖励档位无效）');
}

// 查询key的记录是否存在
Common::loadModel('ServerModel');
$serverid = ServerModel::getDefaultServerId();
$db = Common::getDbBySevId($serverid);
if ( 0 < $db->getCount('reward2active', "`actkey`='{$params['active_code']}' and `awardno`='{$params['award_no']}'") ) {
    returnRes(array('code' => 203, 'msg' => '已兑换过', 'data' => array()), '已兑换过');
}
$nowtime = strtotime('now');
// 先将记录添加避免有重复操作
$sql = "insert into `reward2active` 
    (`actkey`, `awardno`, `type`, `pf`, `sid`, `uid`, `ctime`)
    values (
    '{$params['active_code']}', '{$params['award_no']}', 
    '{$params['award_level']}', '{$platform}',
    '{$params['serverid']}', '{$params['roleid']}',
    '{$nowtime}'
    )";
if ($db->query($sql) === false) {
    returnRes(array('code' => 203, 'msg' => '数据更新失败', 'data' => array()), '数据更新失败');
}

// 邮件发放奖励
$title = !empty($reward2actCfg['info']['title']) ? $reward2actCfg['info']['title'] . '-' . $reward2actCfg['rwd'][$params['award_level']]['name'] : '运营活动奖励';
$content = !empty($reward2actCfg['info']['content']) ? $reward2actCfg['info']['content'] : '感谢您一直以来对游戏的理解和支持~祝您游戏愉快！';
$items_arr = array();
if (is_array($reward2actCfg['rwd'][$params['award_level']]['items'])) {
    $items = Game::getCfg('item');
    foreach ($reward2actCfg['rwd'][$params['award_level']]['items'] as $itm){
	if (0 < $itm['count']) {
	    $items_arr[] = array('id'=>$itm['id'],'count'=>$itm['count'],"kind" => ($items[$itm['id']]['kind'] ? $items[$itm['id']]['kind'] : 1));
	}
    }
}
$mailModel = Master::getMail($params['roleid']);
$mailModel->sendMail($params['roleid'], $title, $content, 1, $items_arr);
Master::click_destroy();

returnRes(array('code' => 200, 'msg' => '成功', 'data' => array()), '成功');

