<?php
return <<<STRING
//限时赴宴次数
array(
        array(
            'info' => array (
                'id' => 20170923,
                'title' => '限时赴宴次数',
                'pindex' => 222,
                'startDay' => 23, //开服第几天开始  从1开始
                'endDay' => 29, //开服第几天结束  从1开始
                'startTime' => '2017-08-14 00:00:00',
                'endTime' => '2017-08-20 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 1,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 3,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 4,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 5,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 7,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 9,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 11,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 9,
                    'need' => 13,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 10,
                    'need' => 15,
                    'items' => array(
                        array ( 'id' => 94, 'count' => 3 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
