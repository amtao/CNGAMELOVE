<?php
return <<<STRING
//限时黄金消耗
array(
    array(
        'info' => array (
            'id' => 20170901,
            'title' => '限时黄金消耗',
            'pindex' => 201,
            'startDay' => 1, //开服第几天开始  从1开始
            'endDay' => 8, //开服第几天结束  从1开始
            'startTime' => '2017-7-25 00:00:00',
            'endTime' => '2017-8-01 23:59:59',  //活动会提前两个小时结束预留发奖励时间
            'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'id' => 1,
                'need' => 500,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 53, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 2,
                'need' => 1000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 53, 'count' => 2 ),
                ),
            ),
            array(
                'id' => 3,
                'need' => 2000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 100 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 53, 'count' => 3 ),
                ),
            ),
            array(
                'id' => 4,
                'need' => 5000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 300 ),
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 53, 'count' => 4 ),
                ),
            ),
            array(
                'id' => 5,
                'need' => 10000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 500 ),
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 53, 'count' => 5 ),
                ),
            ),
            array(
                'id' => 6,
                'need' => 20000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 1000 ),
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 53, 'count' => 6 ),
                ),
            ),
            array(
                'id' => 7,
                'need' => 40000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 77, 'count' => 6 ),
                    array ( 'id' => 53, 'count' => 7 ),
                ),
            ),
            array(
                'id' => 8,
                'need' => 60000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 77, 'count' => 6 ),
                    array ( 'id' => 53, 'count' => 8 ),
                ),
            ),
            array(
                'id' => 9,
                'need' => 100000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 4000 ),
                    array ( 'id' => 77, 'count' => 10 ),
                    array ( 'id' => 53, 'count' => 9 ),
                ),
            ),
            array(
                'id' => 10,
                'need' => 200000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 77, 'count' => 30 ),
                    array ( 'id' => 53, 'count' => 10 ),
                ),
            ),
            array(
                'id' => 11,
                'need' => 300000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 77, 'count' => 30 ),
                    array ( 'id' => 53, 'count' => 11 ),
                ),
            ),
            array(
                'id' => 12,
                'need' => 500000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 77, 'count' => 50 ),
                    array ( 'id' => 53, 'count' => 12 ),
                ),
            ),
            array(
                'id' => 13,
                'need' => 700000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 77, 'count' => 50 ),
                    array ( 'id' => 53, 'count' => 13 ),
                ),
            ),
            array(
                'id' => 14,
                'need' => 1000000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 30000 ),
                    array ( 'id' => 77, 'count' => 70 ),
                    array ( 'id' => 53, 'count' => 15 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    ),
    array(
        'info' => array (
            'id' => 20170909,
            'title' => '限时黄金消耗',
            'pindex' => 201,
            'startDay' => 9, //开服第几天开始  从1开始
            'endDay' => 15, //开服第几天结束  从1开始
            'startTime' => '2017-08-3 00:00:00',
            'endTime' => '2017-08-9 23:59:59',  //活动会提前两个小时结束预留发奖励时间
            'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'id' => 1,
                'need' => 500,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 1 ),
                    array ( 'id' => 66, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 2,
                'need' => 1000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 2 ),
                    array ( 'id' => 66, 'count' => 2 ),
                ),
            ),
            array(
                'id' => 3,
                'need' => 2000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 100 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 3 ),
                    array ( 'id' => 66, 'count' => 3 ),
                ),
            ),
            array(
                'id' => 4,
                'need' => 5000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 300 ),
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 4 ),
                    array ( 'id' => 66, 'count' => 4 ),
                ),
            ),
            array(
                'id' => 5,
                'need' => 10000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 500 ),
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 5 ),
                    array ( 'id' => 66, 'count' => 5 ),
                ),
            ),
            array(
                'id' => 6,
                'need' => 20000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 1000 ),
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 65, 'count' => 6 ),
                    array ( 'id' => 66, 'count' => 6 ),
                ),
            ),
            array(
                'id' => 7,
                'need' => 40000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 65, 'count' => 7 ),
                    array ( 'id' => 66, 'count' => 7 ),
                ),
            ),
            array(
                'id' => 8,
                'need' => 60000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 65, 'count' => 8 ),
                    array ( 'id' => 66, 'count' => 8 ),
                ),
            ),
            array(
                'id' => 9,
                'need' => 100000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 4000 ),
                    array ( 'id' => 77, 'count' => 8 ),
                    array ( 'id' => 65, 'count' => 9 ),
                    array ( 'id' => 66, 'count' => 9 ),
                ),
            ),
            array(
                'id' => 10,
                'need' => 200000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 77, 'count' => 20 ),
                    array ( 'id' => 65, 'count' => 10 ),
                    array ( 'id' => 66, 'count' => 10 ),
                ),
            ),
            array(
                'id' => 11,
                'need' => 300000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 77, 'count' => 20 ),
                    array ( 'id' => 65, 'count' => 11 ),
                    array ( 'id' => 66, 'count' => 11 ),
                ),
            ),
            array(
                'id' => 12,
                'need' => 500000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 77, 'count' => 40 ),
                    array ( 'id' => 65, 'count' => 12 ),
                    array ( 'id' => 66, 'count' => 12 ),
                ),
            ),
            array(
                'id' => 13,
                'need' => 700000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 77, 'count' => 50 ),
                    array ( 'id' => 65, 'count' => 13 ),
                    array ( 'id' => 66, 'count' => 13 ),
                ),
            ),
            array(
                'id' => 14,
                'need' => 1000000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 30000 ),
                    array ( 'id' => 77, 'count' => 70 ),
                    array ( 'id' => 65, 'count' => 15 ),
                    array ( 'id' => 66, 'count' => 15 ),
                ),
            ),
        ),
    ),
    array(
        'info' => array (
            'id' => 20170916,
            'title' => '限时黄金消耗',
            'pindex' => 201,
            'startDay' => 16, //开服第几天开始  从1开始
            'endDay' => 22, //开服第几天结束  从1开始
            'startTime' => '2017-07-30 00:00:00',
            'endTime' => '2017-08-5 23:59:59',  //活动会提前两个小时结束预留发奖励时间
            'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'id' => 1,
                'need' => 500,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 1 ),
                    array ( 'id' => 66, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 2,
                'need' => 1000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 2 ),
                    array ( 'id' => 66, 'count' => 2 ),
                ),
            ),
            array(
                'id' => 3,
                'need' => 2000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 100 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 3 ),
                    array ( 'id' => 66, 'count' => 3 ),
                ),
            ),
            array(
                'id' => 4,
                'need' => 5000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 300 ),
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 4 ),
                    array ( 'id' => 66, 'count' => 4 ),
                ),
            ),
            array(
                'id' => 5,
                'need' => 10000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 500 ),
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 5 ),
                    array ( 'id' => 66, 'count' => 5 ),
                ),
            ),
            array(
                'id' => 6,
                'need' => 20000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 1000 ),
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 65, 'count' => 6 ),
                    array ( 'id' => 66, 'count' => 6 ),
                ),
            ),
            array(
                'id' => 7,
                'need' => 40000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 65, 'count' => 7 ),
                    array ( 'id' => 66, 'count' => 7 ),
                ),
            ),
            array(
                'id' => 8,
                'need' => 60000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 65, 'count' => 8 ),
                    array ( 'id' => 66, 'count' => 8 ),
                ),
            ),
            array(
                'id' => 9,
                'need' => 100000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 4000 ),
                    array ( 'id' => 77, 'count' => 8 ),
                    array ( 'id' => 65, 'count' => 9 ),
                    array ( 'id' => 66, 'count' => 9 ),
                ),
            ),
            array(
                'id' => 10,
                'need' => 200000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 77, 'count' => 20 ),
                    array ( 'id' => 65, 'count' => 10 ),
                    array ( 'id' => 66, 'count' => 10 ),
                ),
            ),
            array(
                'id' => 11,
                'need' => 300000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 77, 'count' => 20 ),
                    array ( 'id' => 65, 'count' => 11 ),
                    array ( 'id' => 66, 'count' => 11 ),
                ),
            ),
            array(
                'id' => 12,
                'need' => 500000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 77, 'count' => 40 ),
                    array ( 'id' => 65, 'count' => 12 ),
                    array ( 'id' => 66, 'count' => 12 ),
                ),
            ),
            array(
                'id' => 13,
                'need' => 700000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 77, 'count' => 50 ),
                    array ( 'id' => 65, 'count' => 13 ),
                    array ( 'id' => 66, 'count' => 13 ),
                ),
            ),
            array(
                'id' => 14,
                'need' => 1000000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 30000 ),
                    array ( 'id' => 77, 'count' => 70 ),
                    array ( 'id' => 65, 'count' => 15 ),
                    array ( 'id' => 66, 'count' => 15 ),
                ),
            ),
        ),
    ),
    array(
        'info' => array (
            'id' => 20170923,
            'title' => '限时黄金消耗',
            'pindex' => 201,
            'startDay' => 23, //开服第几天开始  从1开始
            'endDay' => 29, //开服第几天结束  从1开始
            'startTime' => '2017-08-24 00:00:00',
            'endTime' => '2017-08-31 23:59:59',  //活动会提前两个小时结束预留发奖励时间
            'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'id' => 1,
                'need' => 500,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 81, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 2,
                'need' => 1000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 50 ),
                    array ( 'id' => 81, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 3,
                'need' => 2000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 100 ),
                    array ( 'id' => 81, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 4,
                'need' => 5000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 300 ),
                    array ( 'id' => 81, 'count' => 2 ),
                ),
            ),
            array(
                'id' => 5,
                'need' => 10000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 500 ),
                    array ( 'id' => 81, 'count' => 3 ),
                ),
            ),
            array(
                'id' => 6,
                'need' => 20000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 1000 ),
                    array ( 'id' => 81, 'count' => 4 ),
                ),
            ),
            array(
                'id' => 7,
                'need' => 40000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 2000 ),
                    array ( 'id' => 81, 'count' => 5 ),
                ),
            ),
            array(
                'id' => 8,
                'need' => 60000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 3000 ),
                    array ( 'id' => 81, 'count' => 10 ),
                ),
            ),
            array(
                'id' => 9,
                'need' => 100000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 4000 ),
                    array ( 'id' => 81, 'count' => 20 ),
                ),
            ),
            array(
                'id' => 10,
                'need' => 200000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 81, 'count' => 30 ),
                ),
            ),
            array(
                'id' => 11,
                'need' => 300000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 10000 ),
                    array ( 'id' => 81, 'count' => 50 ),
                ),
            ),
            array(
                'id' => 12,
                'need' => 500000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 81, 'count' => 60 ),
                ),
            ),
            array(
                'id' => 13,
                'need' => 700000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 20000 ),
                    array ( 'id' => 81, 'count' => 70 ),
                ),
            ),
            array(
                'id' => 14,
                'need' => 1000000,
                'items' => array(
                    array ( 'id' => 1, 'count' => 30000 ),
                    array ( 'id' => 81, 'count' => 80 ),
                ),
            ),
        ),
    ),
);
STRING;
