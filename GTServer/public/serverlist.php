<?php

//报错组合
//error_reporting(E_ALL);
//ini_set('display_errors','on');

/**
 * 获取服务器列表
 *
 * @category   public
 * @author     fisher.lee<63764977@qq.com>
 * @version    $Id: admin.php 22 2011-03-29 21:02:36Z $
 */
require_once dirname(__FILE__) . '/common.inc.php';
$params = $_REQUEST;
/**
 * 服务器显示范围限制
 * beginser 为 开始显示的服
 * endser 为 结束显示的服
 * 若前端只传 beginser 则只显示从 beginser 开始直到最后一服
 * 若前端只传 endser 则只显示从第一服开始直到 endser 服
 * 若前端传 beginser 和 endser 这两个参数，则只显示从 beginser 到 endser 的服
 * 若前端都没传 beginser 和 endser 这两个参数，则默认没有限制
 *
 */

$beginser = isset($params['beginser']) && is_numeric($params['beginser']) ? intval($params['beginser']) : 0;
$endser = isset($params['endser']) && is_numeric($params['endser']) ? intval($params['endser']) : 0;
// 没有指定服务器id的情况下默认以1服作为入口服读取
$sevid = 1;
if (!defined('SERVER_ID')) {
    $sevid = ( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) ? 999 : 1;
} else {
    $sevid = intval(SERVER_ID);
}
$SevidCfg = Common::getSevidCfg($sevid);

Common::loadModel('ServerModel');
$serverList['server_list'] = ServerModel::getServList();
ksort($serverList['server_list']);

$list = array();

//服务器列表详情
$data = Common::getConfig(GAME_MARK . "/SevIdCfg");
if (!empty($data)) {
	$list['server_info'] = array();
	foreach ($data as $sid => $val) {
		$list['server_info'][] = array('id' => $sid, 'he' => $val['he'], 'kua' => $val['kua']);
	}
}


print_r($serverList);

//服务器列表
foreach ($serverList['server_list'] as $k => $v) {
    //不是白名单
    if (!Common::istestuser()) {
		if ($sevid != 999 && $k == 999) {
			continue;
		}

		if (!Game::is_over($v['showtime'])) {
			continue;
		}

		if ($beginser > 0 && $beginser > $v['id'] && $v['id'] != 999) {
			continue;
		}

		if ($endser > 0 && $endser < $v['id'] && $v['id'] != 999) {
			continue;
		}
	}

    $list['server_list'][] = array(
		'id' => $v['id'],
		'url' => sprintf('%s/servers/s%s.php', $v['url'], $v['id']),
		'name' => $v['id'] . '    ' . $v['name']['zh'],
		'state' => intval($v['status']),
		'showtime' => $v['showtime'],
		'skin' => isset($v['skin']) ? intval($v['skin']) : 1,
		'he' => isset($data[$v['id']]['ishe']) ? $data[$v['id']]['ishe'] : 0,
    );
}
/*
// 强加999测试服
if(Common::istestuser()) {
	$url999 = 'http://150.109.237.244/servers/s999.php';
	if(defined('QA_SERVER') && QA_SERVER){
		$url999 = QA_SERVER;
	}
	$list['server_list'][] = array(
		'id' => '999',
		'url' => $url999,
		'name' => '灰度测试服',
		'state' => 1,
		'showtime' => 1571364000,
		'skin' => 1,
		'he' => 0,
	);
}
*/


//客服列表
$platform = Game::strval($_REQUEST, 'platform');
if ($platform) {
    $kefu = Game::get_peizhi("kefu_{$platform}");
}
if (empty($kefu)) {
    $kefu = Game::get_peizhi('kefu');
}
if (!empty($kefu)) {
    $list['kefu'] = $kefu;
}

//客服列表
$platform = Game::strval($_REQUEST, 'platform');
if ($platform) {
    $unopen_notice = Game::get_peizhi("unopen_notice_{$platform}");
}
if (empty($unopen_notice)) {
    $unopen_notice = Game::get_peizhi('unopen_notice');
}
if (!empty($unopen_notice)) {
    $list['unopen_notice'] = $unopen_notice;
}

if (empty($unconn_notice)) {
    $unconn_notice = Game::get_peizhi('unconn_notice');
}
if (!empty($unconn_notice)) {
    $list['unconn_notice'] = $unconn_notice;
}






$out_data = array(
    'a' => array('system' => $list),
);


echo json_encode($out_data);
/*
require_once LIB_DIR . '/aes.php';
$aes = new AES();
$rtn_data = $aes->encrypt(json_encode($out_data,true),$aes->getSecretKey());
echo $rtn_data;*/
exit();
