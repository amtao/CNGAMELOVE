<?php
require_once dirname( __FILE__ ) . '/common.inc.php';
class Queue
{
    public static function inFlow($uid, $args)
    {
        Resque::setBackend(RedisConfig::getFlowBackend($uid));
        return Resque::enqueue(QUEUE_FLOW_NAME, QUEUE_FLOW_JOB_NAME, $args, true);
    }
}