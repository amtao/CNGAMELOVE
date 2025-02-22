<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

define( 'ZHICHONG_SECRET', 'ef755e338000f703d95e2bf081c050a2' );

function returnRes($data) {
    $rs = json_encode($data, JSON_UNESCAPED_UNICODE);
    Common::logMsg(PAY_LOG_FILE_CALLBACK, '== 返回 == ' . $rs);
    exit($rs);
}
// <feff> bom头去除方法
function replace_utf8bom($str) {
    $charset [1] = substr ( $str, 0, 1 );
    $charset [2] = substr ( $str, 1, 1 );
    $charset [3] = substr ( $str, 2, 1 );
    if (ord ( $charset [1] ) == 239 && ord ( $charset [2] ) == 187 && ord ( $charset [3] ) == 191) {
        return substr ( $str, 3 );
    } else {
        return $str;
    }
}

define('PAY_LOG_FILE_CALLBACK', sprintf("%spay_api_%s_%s", LOG_PATH, strtr(basename(__FILE__), array('.'=>'_')), date('Ymd')));
$msg = sprintf('== payCallback start (%s)== %s', __LINE__, PHP_EOL.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'].PHP_EOL.var_export($_REQUEST, true));
Common::logMsg(PAY_LOG_FILE_CALLBACK, $msg);

$params = $_REQUEST;

// 核实订单

if ( strtolower($params['sign']) !=
    md5($params['sid'] . $params['roleid'] . $params['money']  . $params['gold'] . $params['orderno']  . $params['mail_title'] . $params['mail_content'] . ZHICHONG_SECRET) ) {
    returnRes(array('code'=>0, 'msg'=>'参数错误(sign不匹配)'));
} else {
    $extGold = intval($params['gold']);// 额外赠送游戏币
    $uid = trim($params['roleid']);

    $serverID = Game::get_sevid($uid);// 获取真实的区服
    // 不是本服的情况下

    if ( $params['sid'] != $serverID ) {
        returnRes(array('code'=>0, 'msg'=>'游戏区服错误'));
    } else {
        // 本服处理
        $amount = floatval($params['money']);// 订单金额
        if ( 0.1 > $amount ) {
            exitError(0, '参数错误(订单金额不能小于0.10元)');
        }
        else {
	    $SevidCfg = Common::getSevidCfg($serverID);// 先加载不然会出错
	    Common::loadModel('UserModel');
            $UserModel = new UserModel($uid);
            if ( empty($UserModel->info) ) {
                returnRes(array('code'=>0, 'msg'=>'参数错误(玩家角色不存在)'));
            }
            else {
                // 生成新的订单号
		$data = array();
                $data['servid'] = $serverID;
                $data['roleid'] = $uid;
                $data['money'] = $amount;
                $data['platform'] = $UserModel->info['platform'];
                $data['paytype'] = 'guangwang';
                $data['tradeno'] = $params['orderno'];
                Common::loadModel('OrderModel');
                $is_ok = OrderModel::order_success($data);

                if ($is_ok) {
                        if ($extGold < 0 && empty($params['mail_title']) && empty($params['mail_content'])){
                            returnRes(array('code'=>0, 'msg'=>'标题或内容不能为空'));
                        }
                        $mailModel = Master::getMail($uid);
                        //充值成功发送邮件          //邮件标题                     //邮件内容
                        $mailModel->sendMail($uid, $params['mail_title'], $params['mail_content'], 1, array(
                            array('id'=>1, 'count'=>$extGold),//元宝
                        ));
                        Master::click_destroy();
                    returnRes(array('code'=>1, 'msg'=>'充值成功'));
                }
                else {
                    returnRes(array('code'=>0, 'msg'=>'充值失败'));
                }
            }
        }
    }
}
returnRes(array('code'=>0, 'msg'=>'充值失败'));

