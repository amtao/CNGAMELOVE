<?php
//咸鱼游戏辅助参数配置
define('SNS_BASE', 'xianyu');   //SDK
define('SNS', 'xianyu_onestore');   //平台
define('SNS_LOGIN_CLOSE', false);  //登陆关闭  true => 关闭
define('SNS_PAY_CLOSE', false);    //支付关闭  true => 关闭


define('CLIENT_ID', 'QJXz1K2UD1fL2K1gt1NIg4ALugjkklq7');//游戏编号client_id
define('SERVER_SECRET', '5NVkb6zC8miBrm2nouBPq3eVyxs2YLCQ'); //服务端密钥

define('MSDK_DEBUG', true); //调试模式，记录日志

define('IS_GET_PLATFORM_RETURN_ID', true); //获取第三方平台返回的用户id标识
define('SNS_PF_PREFIX', 'xianyu');   //平台前缀

define('AGENT_CHANNEL_ALIAS_TO', 'XIANYU');

return array(
    'name' => '宫廷秘传onestore',
    'channel' => array('zjfhghone_and_xytf_01'),
    'sdk' => SNS_BASE,
    'classify' => 'android',
);
