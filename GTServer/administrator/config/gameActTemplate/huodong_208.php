<?php
return <<<STRING
//限时-累计登录天数
array(
        array(
            'info' => array (
                'id' => 20170901,
                'title' => '累计登录天数',
                'pindex' => 208,
                'startDay' => 1, //开服第几天开始  从1开始
                'endDay' => 8, //开服第几天结束  从1开始
                'startTime' => '2017-7-25 00:00:00',
                'endTime' => '2017-8-1 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 1,
                    'items' => array(
                        array ( 'id' => 256, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 257, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2,
                    'items' => array(
                        array ( 'id' => 256, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 257, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 3,
                    'items' => array(
                        array ( 'id' => 256, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 257, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 4,
                    'items' => array(
                        array ( 'id' => 256, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 257, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 5,
                    'items' => array(
                        array ( 'id' => 256, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 257, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 6,
                    'items' => array(
                        array ( 'id' => 258, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 259, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 7,
                    'items' => array(
                        array ( 'id' => 258, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 259, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 65, 'count' => 7 ),
                        array ( 'id' => 66, 'count' => 7 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 8,
                    'items' => array(
                        array ( 'id' => 258, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 259, 'count' => 1,'kind' => 11 ),
                        array ( 'id' => 141, 'count' => 1 ),
                        array ( 'id' => 142, 'count' => 1 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
        array(
            'info' => array (
                'id' => 20170909,
                'title' => '累计登录天数',
                'pindex' => 208,
                'startDay' => 9, //开服第几天开始  从1开始
                'endDay' => 15, //开服第几天结束  从1开始
                'startTime' => '2017-8-10 00:00:00',
                'endTime' => '2017-8-16 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 1,
                    'items' => array(
                        array ( 'id' => 72, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2,
                    'items' => array(
                        array ( 'id' => 72, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 3,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 4,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 5,
                    'items' => array(
                        array ( 'id' => 125, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 6,
                    'items' => array(
                        array ( 'id' => 125, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 7,
                    'items' => array(
                        array ( 'id' => 77, 'count' => 1 ),
                        array ( 'id' => 141, 'count' => 1 ),
                        array ( 'id' => 142, 'count' => 1 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
        array(
            'info' => array (
                'id' => 20170916,
                'title' => '累计登录天数',
                'pindex' => 208,
                'startDay' => 16, //开服第几天开始  从1开始
                'endDay' => 22, //开服第几天结束  从1开始
                'startTime' => '2017-8-10 00:00:00',
                'endTime' => '2017-8-16 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 1,
                    'items' => array(
                        array ( 'id' => 72, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2,
                    'items' => array(
                        array ( 'id' => 72, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 3,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 4,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 5,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 6,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 7,
                    'items' => array(
                        array ( 'id' => 77, 'count' => 1 ),
                        array ( 'id' => 141, 'count' => 1 ),
                        array ( 'id' => 142, 'count' => 1 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
        array(
            'info' => array (
                'id' => 20170923,
                'title' => '累计登录天数',
                'pindex' => 208,
                'startDay' => 23, //开服第几天开始  从1开始
                'endDay' => 29, //开服第几天结束  从1开始
                'startTime' => '2017-8-14 00:00:00',
                'endTime' => '2017-8-20 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 1,
                    'items' => array(
                        array ( 'id' => 72, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2,
                    'items' => array(
                        array ( 'id' => 72, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 3,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 4,
                    'items' => array(
                        array ( 'id' => 73, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 5,
                    'items' => array(
                        array ( 'id' => 71, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 6,
                    'items' => array(
                        array ( 'id' => 71, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 7,
                    'items' => array(
                        array ( 'id' => 77, 'count' => 1 ),
                        array ( 'id' => 141, 'count' => 1 ),
                        array ( 'id' => 142, 'count' => 1 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
