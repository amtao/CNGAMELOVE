<?php

require_once dirname(dirname(__FILE__)) . '/common.inc.php';

Common::loadModel('OrderModel');

function get_param($param_name)//兼容post/get
{
	$param_value = "";
	if(isset($_POST[$param_name])){
		$param_value = trim($_POST[$param_name]);
	}else if(isset($_GET[$param_name])){
		$param_value = trim($_GET[$param_name]);
	}
	return $param_value;
}
Game::order_debug('!!!!ios call back 回调接收到的数据' . var_export($_REQUEST, 1));

$receipt = get_param('receipt');
/*
$data = array(
    'payid' => get_param('payid'),
    'uid' => get_param('uid'),
    'servid' => get_param('servid'),
    'money' => get_param('money'), //游戏唯一订单号
);
*/


Common::getSevidCfg(get_param('servid'));

if(strlen($receipt)){
    $errCode = getReceiptData($receipt,'https://buy.itunes.apple.com/verifyReceipt','');
    if ($errCode == '21004')
    {
        $errCode = getReceiptData($receipt,'https://buy.itunes.apple.com/verifyReceipt','','');
    }
    if ($errCode == "21007")
    {
        $errCode = getReceiptData($receipt,'https://sandbox.itunes.apple.com/verifyReceipt','','');
    }
}else{
    $databack="";
    $databack = json_encode(array('code' => '1','msg'=>'票据为空'));
    echo $databack;
    exit();
    //$errCode = false;   
}

function getReceiptData($receipt,$appurl,$password)
{
    //测试 不进行验证
    /*
    $pData['receipt-data'] = $receipt;
    if($password != '')
    $pData['password'] = $password;
    $postDataJson = json_encode($pData);
    $opts = array
    (
        'http' => array
        (
            'method' => 'POST',
            'header'=> "Content-type: application/json\r\n".
                        "Content-Length: " . strlen($postDataJson) . "\r\n",
            'content' => $postDataJson,
        )
    );

    //生成请求的句柄文件
    $context = stream_context_create($opts);
    $html = file_get_contents($appurl, false, $context);
    
    $data = json_decode($html);
    Game::order_debug('订单成功处理完成');
    // if(DEBUG_INFO)
    // {		
    //     $appleEndTime = microtime(true);
    //     $debugInfo["apple"] = ($appleEndTime-$appleStartTime)*1000;
    // }

    //判断返回的数据是否是对象
    if (!is_object($data)) 
    {
        return false;
    }

    //判断是否购买成功
    if (!isset($data->status)) 
    {
        return false;
    }
    if ($data->status != 0)
    {
        return $data->status;
    }
    */
    /*
$dataR = array(
    'openid' => (defined('SNS_PF_PREFIX') && SNS_PF_PREFIX) ? SNS_PF_PREFIX . '_' . trim($params['uid']) : trim($params['uid']),
    'newOrder' => 1,
    'servid' => intval(get_param('servid')),
    'orderid' => 0, //游戏唯一订单号
    'money' => $orderAmount, //RMB
    'tradeno' => $params['xyOrderNo'],
    'paytype' => SNS_BASE,
    'roleid' => $extrainfo[0],
    'payid' => $extrainfo[1],
    'actcoin' => $extrainfo[2],
);*/
    
    /*
$data = array(
    'payid' => get_param('payid'),
    'uid' => get_param('uid'),
    'servid' => get_param('servid'),
    'money' => get_param('money'), //游戏唯一订单号
);
*/

    $dataR = array(
        'openid' => (defined('SNS_PF_PREFIX') && SNS_PF_PREFIX) ? SNS_PF_PREFIX . '_' . trim(get_param('uid')) : trim(get_param('uid')),
        'newOrder' => 1,
        'servid' => intval(get_param('servid')),
        'orderid' => 0, 
        'money' => get_param('money'), //RMB
        'tradeno' => get_param('tradeno'),//游戏外部订单号
        'paytype' => SNS_BASE,
        'roleid' => get_param('uid'),
        'payid' => get_param('payid'),
        'actcoin' => get_param('actcoin'),
    );
        
    $is_ok = OrderModel::order_success($dataR);
    if ($is_ok) {
        Game::order_debug('订单成功处理完成');
        $databack="";
        $databack = json_encode(array('code' => '0','msg'=>'success'));
        echo $databack;
        exit();
    } else {
        Game::order_debug('失败:数据更新失败');
        $databack="";
        $databack = json_encode(array('code' => '3','msg'=>'充值失败'));
        echo $databack;
        exit('');
    }
    return true;
}

exit();