<?php
//咸鱼游戏辅助参数配置
define('SNS_BASE', 'xianyu');   //SDK
define('SNS', 'xianyu_ios');   //平台
define('SNS_LOGIN_CLOSE', false);  //登陆关闭  true => 关闭
define('SNS_PAY_CLOSE', false);    //支付关闭  true => 关闭


define('CLIENT_ID', 'yESeURyriVczajgq85DSEFF2ERMt7gVI');//游戏编号client_id
define('SERVER_SECRET', '1E7t2iE1nlwecR28vmXFecg4HoX1Pzk5'); //服务端密钥

define('MSDK_DEBUG', true); //调试模式，记录日志

define('IS_GET_PLATFORM_RETURN_ID', true); //获取第三方平台返回的用户id标识
define('SNS_PF_PREFIX', 'xianyu_ios');   //平台前缀

define('AGENT_CHANNEL_ALIAS_TO', 'XIANYU');

return array(
    'name' => '宫廷秘传M5',
    'channel' => array('XIANYU'),
    'sdk' => SNS_BASE,
    'classify' => 'android',
);
