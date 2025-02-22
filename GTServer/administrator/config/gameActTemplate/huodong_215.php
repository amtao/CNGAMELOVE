<?php
return <<<STRING
//限时击杀匈奴王次数
array(
        array(
            'info' => array (
                'id' => 20170909,
                'title' => '限时击杀葛尔丹次数',
                'pindex' => 215,
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
                        array ( 'id' => 111, 'count' => 1 ),
                    ),
                ),
                array(
                    'id' => 2,
                    'need' => 2,
                    'items' => array(
                        array ( 'id' => 111, 'count' => 2 ),
                    ),
                ),
                array(
                    'id' => 3,
                    'need' => 3,
                    'items' => array(
                        array ( 'id' => 111, 'count' => 3 ),
                    ),
                ),
                array(
                    'id' => 4,
                    'need' => 4,
                    'items' => array(
                        array ( 'id' => 111, 'count' => 4 ),
                    ),
                ),
                array(
                    'id' => 5,
                    'need' => 5,
                    'items' => array(
                        array ( 'id' => 111, 'count' => 5 ),
                    ),
                ),
                array(
                    'id' => 6,
                    'need' => 10,
                    'items' => array(
                        array ( 'id' => 111, 'count' => 6 ),
                    ),
                ),
                array(
                    'id' => 7,
                    'need' => 20,
                    'items' => array(
                        array ( 'id' => 111, 'count' => 7 ),
                    ),
                ),
            ),
            'msg' => '老鼠是老鼠,不是猫!!!!'
        ),
    );
STRING;
