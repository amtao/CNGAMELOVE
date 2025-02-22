<?php 

//游戏辅助参数配置
define('SNS_BASE', 'local');   //SDK
define('SNS', 'local');   //平台
define('SNS_LOGIN_CLOSE', false);  //登陆关闭  true => 关闭
define('SNS_PAY_CLOSE', false);    //支付关闭  true => 关闭
define('AGENT_CHANNEL_ALIAS_TO', 'KING');

return array(
	'name' => '本地',
	'channel' => array('KING'),
	'classify' => 'android',
	'sdk' => SNS_BASE,
);



/*



//SDK参数配置
define('SNS_APPID', '38');
define('SNS_APPKEY', '4c2c269d2105eb5cdf418266b24cc9dc'); 
define('SNS_SANBOX_OPEN', 'false');  //false表示关闭
define('SNS_PRODUCTID_PREFIX', 'com.jiayou.guajimircqwsapp.'); //商品前缀
define('SNS_SANBOX_OPEN', 'false');  // 沙箱模式开启
*/






