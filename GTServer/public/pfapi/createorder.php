<?php

/**
 * 创建CP订单接口
 * @author wenyj
 * @version
 *   - 20171201, init
 * @return JSON
 */
error_reporting(E_ALL);
ini_set('display_errors','on');

if (file_exists(dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php')) {
    require_once dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php';
} else {
    exit('param invalid(_pf)');
}

require_once dirname(dirname(__FILE__)) . '/common.inc.php';

define('LOG_API_ACTIVE_REWARD', LOG_PATH . '/apilog/' . SNS . '_' . SERVER_ID . '_' . strtr(basename(__FILE__), array('.' => '_')) . '.log');

// 记录request参数
$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
Game::logMsg(LOG_API_ACTIVE_REWARD, 'request = ' . var_export($params, 1));
if (empty($params)) {
    Game::logMsg(LOG_API_ACTIVE_REWARD, '参数为空');
    exit('参数为空');
}

//平台验证
$platform = defined('SNS') ? SNS : '';
if (empty($platform)) {
    Game::logMsg(LOG_API_ACTIVE_REWARD, '参数错误（未知平台）');
    exit('参数错误（未知平台）');
}

Common::loadModel('OrderModel');
$Api = OrderModel::sdk_func($platform);
if (method_exists($Api, 'getCpOrderId')) {
    $rt = $Api->getCpOrderId($params);
    Game::logMsg(LOG_API_ACTIVE_REWARD, 'api rt=' . var_export($rt, 1));
    echo (is_array($rt)) ? json_encode($rt) : $rt;
    exit();
} else {
    Game::logMsg(LOG_API_ACTIVE_REWARD, '接口未授权');
    exit('接口未授权');
}

