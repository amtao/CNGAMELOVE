<?php

$params = $_REQUEST;

if (!isset($params['type'])) {
    exit();
}

$type = $params['type'];
$uid = $params['uid'];
$server = floor($uid / 1000000);
define('SERVER_ID', $server);
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
if ($type == 'talk') {
    Common::loadModel("TalkModel");
    $room_msg = TalkModel::get_all_msg(0);
    $talk_user = Common::getselfcfg('talk_user');
    if (!in_array($uid, $talk_user)) {
        $talk_user[] = $uid;
        Common::setselfcfg('talk_user', $talk_user);
    }
    foreach ($room_msg as &$mv) {
        if ($mv['uid'] == $uid) {
            $mv['smg'] = '*';
        }
    }
    TalkModel::set_all_msg($room_msg);
} else if ($type == 'bug') {
    $bug_user = Common::getselfcfg('bug_user');
    if (!in_array($uid, $bug_user)) {
        $bug_user[] = $uid;
        Common::setselfcfg('bug_user', $bug_user);
    }
} else {
    exit();
}