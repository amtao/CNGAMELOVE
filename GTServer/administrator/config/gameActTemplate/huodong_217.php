<?php
return <<<STRING
//限时惩戒犯人次数
array(
        array(
            'info' => array (
                'id' => 20170916,
                'title' => '限时惩戒犯人次数',
                'pindex' => 217,
                'startDay' => 16, //开服第几天开始  从1开始
                'endDay' => 22, //开服第几天结束  从1开始
                'startTime' => '2017-05-24 00:00:00',
                'endTime' => '2017-06-15 23:59:59',   //活动会提前两个小时结束预留发奖励时间
                'type' => 2,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
            ),
            'rwd' => array(
                array(
                    'id' => 1,
                    'need' => 30,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 50,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 70,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 1 ),
                        array ( 'id' => 65, 'count' => 1 ),
                        array ( 'id' => 66, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 100,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 150,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 200,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 2 ),
                        array ( 'id' => 65, 'count' => 2 ),
                        array ( 'id' => 66, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 300,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 3 ),
                        array ( 'id' => 65, 'count' => 3 ),
                        array ( 'id' => 66, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 8,
                    'need' => 500,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 4 ),
                        array ( 'id' => 65, 'count' => 4 ),
                        array ( 'id' => 66, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 9,
                    'need' => 700,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 5 ),
                        array ( 'id' => 65, 'count' => 5 ),
                        array ( 'id' => 66, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 10,
                    'need' => 1000,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 6 ),
                        array ( 'id' => 65, 'count' => 6 ),
                        array ( 'id' => 66, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 11,
                    'need' => 1500,
                    'items' => array(
                        array ( 'id' => 93, 'count' => 7 ),
                        array ( 'id' => 65, 'count' => 7 ),
                        array ( 'id' => 66, 'count' => 7 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
