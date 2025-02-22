<?php
return <<<STRING
//限时联盟副本伤害
array(
        array(
            'info' => array (
                'id' => 20170901,
                'title' => '限时联盟副本伤害',
                'pindex' => 223,
                'startDay' => 9, //开服第几天开始  从1开始
                'endDay' => 15, //开服第几天结束  从1开始
                'startTime' => '2017-8-26 00:00:00',
                'endTime' => '2017-9-01 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 1000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 1 ),
                        array ( 'id' => 93, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 1 ),
                        array ( 'id' => 93, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 5000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 1 ),
                        array ( 'id' => 93, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 10000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 2 ),
                        array ( 'id' => 93, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 20000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 2 ),
                        array ( 'id' => 93, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 50000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 3 ),
                        array ( 'id' => 93, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 100000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 3 ),
                        array ( 'id' => 93, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 200000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 4 ),
                        array ( 'id' => 93, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 9,
                    'need' => 500000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 5 ),
                        array ( 'id' => 93, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 10,
                    'need' => 1000000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 6 ),
                        array ( 'id' => 93, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 11,
                    'need' => 2000000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 7 ),
                        array ( 'id' => 93, 'count' => 7 ),
                    ),
                ),
                array(
                    'id' => 12,
                    'need' => 3000000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 8 ),
                        array ( 'id' => 93, 'count' => 8 ),
                        array ( 'id' => 141, 'count' => 1 ),
                        array ( 'id' => 142, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 13,
                    'need' => 5000000000,
                    'items' => array(
                        array ( 'id' => 91, 'count' => 9 ),
                        array ( 'id' => 93, 'count' => 9 ),
                        array ( 'id' => 141, 'count' => 1 ),
                        array ( 'id' => 142, 'count' => 1 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
