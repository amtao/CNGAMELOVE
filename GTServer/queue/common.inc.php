<?php
define( 'QUEUE_ROOT_DIR' , dirname( dirname( __FILE__ ) ) );
require_once QUEUE_ROOT_DIR . '/config.php';
//自动载入
require_once QUEUE_ROOT_DIR . '/lib/phpresque/vendor/autoload.php';
//载入配置
require_once QUEUE_ROOT_DIR . '/queue/config/RedisConfig.php';

//流水队列名称
define('QUEUE_FLOW_NAME' , 'QUEUE_FLOW');
//流水任务名称
define('QUEUE_FLOW_JOB_NAME' , 'QueueFlowJob');