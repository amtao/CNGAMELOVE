<?php
class RedisConfig
{
    public static function getFlowBackend($uid)
    {
        return self::$_flow_cfg['cfg'][GAME_MARK][$uid % self::$_flow_cfg['div']];
    }
    /**
     * TODO 分皮处理
     * @var array
     */
    private static $_flow_cfg = array(
        'div'=>1,
        //username:password@hostname:9090
        'cfg'=>array(
            //预上线
            'lyjxtime'=>array(
                0=>'10.253.19.118:6382',
            ),
        ),
    );
}