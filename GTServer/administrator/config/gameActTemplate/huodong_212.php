<?php
return <<<STRING
//限时经营商产次数
array(
        array(
            'info' => array (
                'id' => 20170909,
                'title' => '限时经营商产次数',
                'pindex' => 212,
                'startDay' => 9, //开服第几天开始  从1开始
                'endDay' => 15, //开服第几天结束  从1开始
                'startTime' => '2017-8-10 00:00:00',
                'endTime' => '2017-8-13 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 10,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 1 ),
                        array ( 'id' => 32, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 20,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 1 ),
                        array ( 'id' => 32, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 40,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 1 ),
                        array ( 'id' => 32, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 70,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 2 ),
                        array ( 'id' => 32, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 100,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 2 ),
                        array ( 'id' => 32, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 150,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 2 ),
                        array ( 'id' => 32, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 200,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 3 ),
                        array ( 'id' => 32, 'count' => 3 ),
                        array ( 'id' => 65, 'count' => 7 ),
                        array ( 'id' => 66, 'count' => 7 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 250,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 3 ),
                        array ( 'id' => 32, 'count' => 3 ),
                        array ( 'id' => 65, 'count' => 8 ),
                        array ( 'id' => 66, 'count' => 8 ),
                    ),
                ),
                array(
                    'id' => 9,
                    'need' => 300,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 3 ),
                        array ( 'id' => 32, 'count' => 3 ),
                        array ( 'id' => 65, 'count' => 9 ),
                        array ( 'id' => 66, 'count' => 9 ),
                    ),
                ),
                array(
                    'id' => 10,
                    'need' => 350,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 4 ),
                        array ( 'id' => 32, 'count' => 4 ),
                        array ( 'id' => 65, 'count' => 10 ),
                        array ( 'id' => 66, 'count' => 10 ),
                    ),
                ),
                array(
                    'id' => 11,
                    'need' => 400,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 5 ),
                        array ( 'id' => 32, 'count' => 5 ),
                        array ( 'id' => 65, 'count' => 11 ),
                        array ( 'id' => 66, 'count' => 11 ),
                    ),
                ),
                array(
                    'id' => 12,
                    'need' => 500,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 6 ),
                        array ( 'id' => 32, 'count' => 6 ),
                        array ( 'id' => 65, 'count' => 12 ),
                        array ( 'id' => 66, 'count' => 12 ),
                    ),
                ),
                array(
                    'id' => 13,
                    'need' => 600,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 7 ),
                        array ( 'id' => 32, 'count' => 7 ),
                        array ( 'id' => 65, 'count' => 13 ),
                        array ( 'id' => 66, 'count' => 13 ),
                    ),
                ),
                array(
                    'id' => 14,
                    'need' => 700,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 8 ),
                        array ( 'id' => 32, 'count' => 8 ),
                        array ( 'id' => 65, 'count' => 14 ),
                        array ( 'id' => 66, 'count' => 14 ),
                    ),
                ),
                array(
                    'id' => 15,
                    'need' => 800,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 9 ),
                        array ( 'id' => 32, 'count' => 9 ),
                        array ( 'id' => 65, 'count' => 15 ),
                        array ( 'id' => 66, 'count' => 15 ),
                    ),
                ),
                array(
                    'id' => 16,
                    'need' => 1000,
                    'items' => array(
                        array ( 'id' => 12, 'count' => 10 ),
                        array ( 'id' => 32, 'count' => 10 ),
                        array ( 'id' => 65, 'count' => 20 ),
                        array ( 'id' => 66, 'count' => 20),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
