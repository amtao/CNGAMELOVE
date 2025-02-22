<?php
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
require_once dirname( __FILE__ ) . '/common.inc.php';
require_once QUEUE_ROOT_DIR . '/queue/job/'.QUEUE_FLOW_JOB_NAME.'.php';
require QUEUE_ROOT_DIR . '/lib/phpresque/bin/resque';