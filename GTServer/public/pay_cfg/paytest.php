<?php

//咸鱼游戏辅助参数配置
define('SNS_BASE', 'xianyu');   //SDK
define('SNS', 'paytest');   //平台
define('SNS_LOGIN_CLOSE', false);  //登陆关闭  true => 关闭
define('SNS_PAY_CLOSE', false);    //支付关闭  true => 关闭


define('CLIENT_ID', 'b6ca62a89501209fdbe4cd9b56f34cb8');//游戏编号client_id
define('SERVER_SECRET', 'efc467f1d179a60c897c55dc3e0d7526'); //服务端密钥

define('MSDK_DEBUG', true); //调试模式，记录日志

define('IS_GET_PLATFORM_RETURN_ID', true); //获取第三方平台返回的用户id标识
define('SNS_PF_PREFIX', 'paytest');   //平台前缀

define('AGENT_CHANNEL_ALIAS_TO', 'EPZJFH');

return array(
    'name' => '内网充值测试包',
    'channel' => array('EPZJFH'),
    'sdk' => SNS_BASE,
    'classify' => 'android',
);
