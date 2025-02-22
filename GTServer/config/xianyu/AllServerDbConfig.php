<?php
//数据库配置
$cfg = array(
    1 => array(
        //游戏数据库
        'game' => array(
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 's1_gtk_xianyu',
            'passwd' => 'rL8XFBEbKf85F2YC',
            'name' => 's1_gtk_xianyu',
            'table_div' => 100
        ),
        //流水数据
        'flow' => array(
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 's1_gtk_flow',
            'passwd' => 'Ms46BWiC3WSxcmiE',
            'name' => 's1_gtk_flow',
            'table_div' => 100
        ),
    ),
//------------end-----------------

);

return $cfg;

