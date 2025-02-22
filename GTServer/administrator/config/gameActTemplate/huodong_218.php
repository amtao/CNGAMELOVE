<?php
return <<<STRING
//限时赈灾次数
array(
        array(
            'info' => array (
                'id' => 20170916,
                'title' => '限时赈灾次数',
                'pindex' => 218,
                'startDay' => 16, //开服第几天开始  从1开始
                'endDay' => 22, //开服第几天结束  从1开始
                'startTime' => '2017-06-30 00:00:00',
                'endTime' => '2017-06-5 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 10,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 20,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 30,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 40,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 50,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 60,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 80,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 100,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 9,
                    'need' => 200,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 10,
                    'need' => 400,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 3 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 11,
                    'need' => 600,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 4 ),
                        array ( 'id' => 65, 'count' => 7 ),
                        array ( 'id' => 66, 'count' => 7 ),
                    ),
                ),
                array(
                    'id' => 12,
                    'need' => 800,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 5 ),
                        array ( 'id' => 65, 'count' => 8 ),
                        array ( 'id' => 66, 'count' => 8 ),
                    ),
                ),
                array(
                    'id' => 13,
                    'need' => 1000,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 6 ),
                        array ( 'id' => 65, 'count' => 9 ),
                        array ( 'id' => 66, 'count' => 9 ),
                    ),
                ),
                array(
                    'id' => 14,
                    'need' => 2000,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 7 ),
                        array ( 'id' => 65, 'count' => 10 ),
                        array ( 'id' => 66, 'count' => 10 ),
                    ),
                ),
                array(
                    'id' => 15,
                    'need' => 3000,
                    'items' => array(
                        array ( 'id' => 92, 'count' => 8 ),
                        array ( 'id' => 65, 'count' => 15 ),
                        array ( 'id' => 66, 'count' => 15 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
