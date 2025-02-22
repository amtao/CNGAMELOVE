<?php
return <<<STRING
//限时银两消耗
array(
        array(
            'info' => array (
                'id' => 20170901,
                'title' => '限时银两消耗',
                'pindex' => 203,
                'startDay' => 1, //开服第几天开始  从1开始
                'endDay' => 8, //开服第几天结束  从1开始
                'startTime' => '2017-7-25 00:00:00',
                'endTime' => '2017-8-1 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 100000,
                    'items' => array(
                        array ( 'id' => 21, 'count' => 1 ),
                        array ( 'id' => 41, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 500000,
                    'items' => array(
                        array ( 'id' => 21, 'count' => 2 ),
                        array ( 'id' => 41, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 1000000,
                    'items' => array(
                        array ( 'id' => 21, 'count' => 3 ),
                        array ( 'id' => 41, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 2000000,
                    'items' => array(
                        array ( 'id' => 21, 'count' => 4 ),
                        array ( 'id' => 41, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 3000000,
                    'items' => array(
                        array ( 'id' => 21, 'count' => 5 ),
                        array ( 'id' => 41, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 4000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 1 ),
                        array ( 'id' => 42, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 5000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 1 ),
                        array ( 'id' => 42, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 6000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 2 ),
                        array ( 'id' => 42, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 9,
                    'need' => 7000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 2 ),
                        array ( 'id' => 42, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 10,
                    'need' => 8000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 3 ),
                        array ( 'id' => 42, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 11,
                    'need' => 9000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 3 ),
                        array ( 'id' => 42, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 12,
                    'need' => 10000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 4 ),
                        array ( 'id' => 42, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 13,
                    'need' => 12000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 4 ),
                        array ( 'id' => 42, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 14,
                    'need' => 14000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 5 ),
                        array ( 'id' => 42, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 15,
                    'need' => 16000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 5 ),
                        array ( 'id' => 42, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 16,
                    'need' => 18000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 6 ),
                        array ( 'id' => 42, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 17,
                    'need' => 20000000,
                    'items' => array(
                        array ( 'id' => 22, 'count' => 6 ),
                        array ( 'id' => 42, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 18,
                    'need' => 30000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 3 ),
                        array ( 'id' => 43, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 19,
                    'need' => 40000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 3 ),
                        array ( 'id' => 43, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 20,
                    'need' => 50000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 4 ),
                        array ( 'id' => 43, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 21,
                    'need' => 60000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 4 ),
                        array ( 'id' => 43, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 22,
                    'need' => 70000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 5 ),
                        array ( 'id' => 43, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 23,
                    'need' => 80000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 5 ),
                        array ( 'id' => 43, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 24,
                    'need' => 90000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 6 ),
                        array ( 'id' => 43, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 25,
                    'need' => 100000000,
                    'items' => array(
                        array ( 'id' => 23, 'count' => 6 ),
                        array ( 'id' => 43, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 26,
                    'need' => 200000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 1 ),
                        array ( 'id' => 64, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 27,
                    'need' => 300000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 1 ),
                        array ( 'id' => 64, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 28,
                    'need' => 400000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 2 ),
                        array ( 'id' => 64, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 29,
                    'need' => 500000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 2 ),
                        array ( 'id' => 64, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 30,
                    'need' => 700000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 3 ),
                        array ( 'id' => 64, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 31,
                    'need' => 1000000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 3 ),
                        array ( 'id' => 64, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 32,
                    'need' => 1500000000,
                    'items' => array(
                        array ( 'id' => 62, 'count' => 4 ),
                        array ( 'id' => 64, 'count' => 4 ),
                        array ( 'id' => 141, 'count' => 2 ),
                        array ( 'id' => 142, 'count' => 2 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
