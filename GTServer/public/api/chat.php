<?php
// if ($_SERVER['REMOTE_ADDR'] != "47.89.29.174"){
//     exit();
// }

$params = $_REQUEST;

$chat_msg = $params['chat_msg'];
$uid = $params['uid'];
$server = floor($uid / 1000000);
define('SERVER_ID', $server);
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
common::loadModel('TalkModel');
$TalkModel = new TalkModel('-1');
common::loadModel('UserModel');
$UserModel = new UserModel($uid);
$name = $UserModel->info['name'];
$TalkModel->set_gm_msg('游戏管理员', '@' . $name . '，' . $chat_msg);
