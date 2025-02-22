<?php
return <<<STRING
//每日充值
array(
    array(
        'info' => array (
            'id' => 'day',  //每日更新id
            'title' => '每日充值',
            'pindex' => 260,
            'startDay' => 0, //开服第几天开始  从1开始
            'endDay' => 0, //开服第几天结束  从1开始
            'startTime' => '2017-9-7 00:00:00',
            'endTime' => '2017-9-13 23:59:59',  //活动会提前两个小时结束预留发奖励时间
            'type' => 4,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'id' => 1,  //档次
                'need' => 6,  //今日充值满6元
                'items' => array(   //获得的奖励
                    array ( 'id' => 111, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 10 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 121, 'count' => 1 ),
                    array ( 'id' => 122, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 2,
                'need' => 36,
                'items' => array(
                    array ( 'id' => 111, 'count' => 2 ),
                    array ( 'id' => 65, 'count' => 20 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 121, 'count' => 1 ),
                    array ( 'id' => 122, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                    array ( 'id' => 141, 'count' => 1 ),
                    array ( 'id' => 142, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 3,
                'need' => 104,
                'items' => array(
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 30 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 121, 'count' => 2 ),
                    array ( 'id' => 122, 'count' => 2 ),
                    array ( 'id' => 160, 'count' => 2 ),
                    array ( 'id' => 161, 'count' => 2 ),
                    array ( 'id' => 162, 'count' => 2 ),
                    array ( 'id' => 141, 'count' => 2 ),
                    array ( 'id' => 142, 'count' => 2 ),
                ),
            ),
            array(
                'id' => 4,
                'need' => 232,
                'items' => array(
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 65, 'count' => 50 ),
                    array ( 'id' => 79, 'count' => 4 ),
                    array ( 'id' => 121, 'count' => 4 ),
                    array ( 'id' => 122, 'count' => 4 ),
                    array ( 'id' => 160, 'count' => 4 ),
                    array ( 'id' => 161, 'count' => 4 ),
                    array ( 'id' => 162, 'count' => 4 ),
                    array ( 'id' => 141, 'count' => 4 ),
                    array ( 'id' => 142, 'count' => 4 ),
                ),
            ),
            array(
                'id' => 5,
                'need' => 560,
                'items' => array(
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 65, 'count' => 80 ),
                    array ( 'id' => 79, 'count' => 5 ),
                    array ( 'id' => 121, 'count' => 5 ),
                    array ( 'id' => 122, 'count' => 5 ),
                    array ( 'id' => 160, 'count' => 5 ),
                    array ( 'id' => 161, 'count' => 5 ),
                    array ( 'id' => 162, 'count' => 5 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 6,
                'need' => 1000,
                'items' => array(
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 65, 'count' => 120 ),
                    array ( 'id' => 79, 'count' => 7 ),
                    array ( 'id' => 121, 'count' => 7 ),
                    array ( 'id' => 122, 'count' => 7 ),
                    array ( 'id' => 160, 'count' => 7 ),
                    array ( 'id' => 161, 'count' => 7 ),
                    array ( 'id' => 162, 'count' => 7 ),
                    array ( 'id' => 146, 'count' => 1 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 7,
                'need' => 2000,
                'items' => array(
                    array ( 'id' => 77, 'count' => 10 ),
                    array ( 'id' => 65, 'count' => 300 ),
                    array ( 'id' => 79, 'count' => 20 ),
                    array ( 'id' => 121, 'count' => 20 ),
                    array ( 'id' => 122, 'count' => 20 ),
                    array ( 'id' => 160, 'count' => 20 ),
                    array ( 'id' => 161, 'count' => 20 ),
                    array ( 'id' => 162, 'count' => 20 ),
                    array ( 'id' => 143, 'count' => 1 ),
                    array ( 'id' => 144, 'count' => 1 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    ),
    array(
        'info' => array (
            'id' => 'day',  //每日更新id
            'title' => '每日充值',
            'pindex' => 201,
            'startDay' => 0, //开服第几天开始  从1开始
            'endDay' => 0, //开服第几天结束  从1开始
            'startTime' => '2017-9-14 00:00:00',
            'endTime' => '2017-9-20 23:59:59',  //活动会提前两个小时结束预留发奖励时间
            'type' => 4,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'id' => 1,  //档次
                'need' => 6,  //今日充值满6元
                'items' => array(   //获得的奖励
                    array ( 'id' => 141, 'count' => 1 ),
                    array ( 'id' => 142, 'count' => 1 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 53, 'count' => 10 ),
                ),
            ),
            array(
                'id' => 2,
                'need' => 36,
                'items' => array(
                    array ( 'id' => 141, 'count' => 2 ),
                    array ( 'id' => 142, 'count' => 2 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 53, 'count' => 2 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 3,
                'need' => 104,
                'items' => array(
                    array ( 'id' => 141, 'count' => 2 ),
                    array ( 'id' => 142, 'count' => 2 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 146, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 4,
                'need' => 198,
                'items' => array(
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 141, 'count' => 2 ),
                    array ( 'id' => 142, 'count' => 2 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 146, 'count' => 1 ),
                    array ( 'id' => 143, 'count' => 1 ),
                    array ( 'id' => 144, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 5,
                'need' => 328,
                'items' => array(
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 141, 'count' => 3 ),
                    array ( 'id' => 142, 'count' => 3 ),
                    array ( 'id' => 79, 'count' => 3 ),
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 4 ),
                    array ( 'id' => 161, 'count' => 4 ),
                    array ( 'id' => 162, 'count' => 4 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 146, 'count' => 1 ),
                    array ( 'id' => 143, 'count' => 1 ),
                    array ( 'id' => 144, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 6,
                'need' => 648,
                'items' => array(
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 141, 'count' => 5 ),
                    array ( 'id' => 142, 'count' => 5 ),
                    array ( 'id' => 79, 'count' => 5 ),
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 5 ),
                    array ( 'id' => 161, 'count' => 5 ),
                    array ( 'id' => 162, 'count' => 5 ),
                    array ( 'id' => 130, 'count' => 1 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 143, 'count' => 1 ),
                    array ( 'id' => 144, 'count' => 1 ),
                    array ( 'id' => 68, 'count' => 5 ),
                    array ( 'id' => 69, 'count' => 5 ),
                    array ( 'id' => 146, 'count' => 1 ),

                ),
            ),
            array(
                'id' => 7,
                'need' => 1000,
                'items' => array(
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 141, 'count' => 5 ),
                    array ( 'id' => 142, 'count' => 5 ),
                    array ( 'id' => 79, 'count' => 5 ),
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 5 ),
                    array ( 'id' => 161, 'count' => 5 ),
                    array ( 'id' => 162, 'count' => 5 ),
                    array ( 'id' => 130, 'count' => 1 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 143, 'count' => 1 ),
                    array ( 'id' => 144, 'count' => 1 ),
                    array ( 'id' => 68, 'count' => 10 ),
                    array ( 'id' => 69, 'count' => 10 ),
                    array ( 'id' => 146, 'count' => 1 ),
                ),
            ),
            array(
                'id' => 8,
                'need' => 2000,
                'items' => array(
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 141, 'count' => 5 ),
                    array ( 'id' => 142, 'count' => 5 ),
                    array ( 'id' => 79, 'count' => 5 ),
                    array ( 'id' => 54, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 5 ),
                    array ( 'id' => 161, 'count' => 5 ),
                    array ( 'id' => 162, 'count' => 5 ),
                    array ( 'id' => 130, 'count' => 1 ),
                    array ( 'id' => 145, 'count' => 1 ),
                    array ( 'id' => 143, 'count' => 1 ),
                    array ( 'id' => 144, 'count' => 1 ),
                    array ( 'id' => 68, 'count' => 20 ),
                    array ( 'id' => 69, 'count' => 20 ),
                    array ( 'id' => 146, 'count' => 1 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    ),
);
STRING;
