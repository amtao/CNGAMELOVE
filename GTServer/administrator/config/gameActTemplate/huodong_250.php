<?php
return <<<STRING
//帮会冲榜
array(
    array(
        'info' => array (
            'id' => 20170823,
            'title' => '帮会冲榜',
            'pindex' => 250,
            'startDay' => 9, //开服第几天开始  从1开始
            'endDay' => 15, //开服第几天结束  从1开始
            'startTime' => '2017-8-2 00:00:00',
            'endTime' => '2017-8-2 23:59:59',   //活动会提前两个小时结束预留发奖励时间
            'type' => 3,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动
        ),
        'rwd' => array(
            array(
                'rand' => array('rs'=>1,'re'=>1), //排名范围
                'mengzhu' => array(   //盟主奖励
                    array ( 'id' => 76, 'count' => 12 ),
                    array ( 'id' => 77, 'count' => 20 ),
                    array ( 'id' => 91, 'count' => 20 ),
                    array ( 'id' => 94, 'count' => 20 ),
                    array ( 'id' => 160, 'count' => 45 ),
                    array ( 'id' => 161, 'count' => 45 ),
                    array ( 'id' => 162, 'count' => 45 ),
                    array ( 'id' => 129, 'count' => 5 ),
                    array ( 'id' => 194, 'count' => 1,'kind' => 10 ),
                    array ( 'id' => 143, 'count' => 5 ),
                    array ( 'id' => 144, 'count' => 5 ),
                    array ( 'id' => 145, 'count' => 1 ),
                ),
                'member' => array(   //成员奖励
                    array ( 'id' => 76, 'count' => 8 ),
                    array ( 'id' => 77, 'count' => 15 ),
                    array ( 'id' => 91, 'count' => 15 ),
                    array ( 'id' => 94, 'count' => 15 ),
                    array ( 'id' => 160, 'count' => 33 ),
                    array ( 'id' => 161, 'count' => 33 ),
                    array ( 'id' => 162, 'count' => 33 ),
                    array ( 'id' => 129, 'count' => 4 ),
                    array ( 'id' => 195, 'count' => 1,'kind' => 10 ),
                    array ( 'id' => 141, 'count' => 1 ),
                    array ( 'id' => 142, 'count' => 1 ),
                    array ( 'id' => 54, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>2,'re'=>2), //排名范围
                'mengzhu' => array(   //盟主奖励
                    array ( 'id' => 76, 'count' => 10 ),
                    array ( 'id' => 77, 'count' => 15 ),
                    array ( 'id' => 91, 'count' => 15 ),
                    array ( 'id' => 94, 'count' => 15 ),
                    array ( 'id' => 160, 'count' => 33 ),
                    array ( 'id' => 161, 'count' => 33 ),
                    array ( 'id' => 162, 'count' => 33 ),
                    array ( 'id' => 129, 'count' => 4 ),
                ),
                'member' => array(   //成员奖励
                    array ( 'id' => 76, 'count' => 6 ),
                    array ( 'id' => 77, 'count' => 10 ),
                    array ( 'id' => 91, 'count' => 10 ),
                    array ( 'id' => 94, 'count' => 10 ),
                    array ( 'id' => 160, 'count' => 24 ),
                    array ( 'id' => 161, 'count' => 24 ),
                    array ( 'id' => 162, 'count' => 24 ),
                    array ( 'id' => 129, 'count' => 3 ),
                ),
            ),
            array(
                'rand' => array('rs'=>3,'re'=>3), //排名范围
                'mengzhu' => array(   //盟主奖励
                    array ( 'id' => 76, 'count' => 8 ),
                    array ( 'id' => 77, 'count' => 10 ),
                    array ( 'id' => 91, 'count' => 10 ),
                    array ( 'id' => 94, 'count' => 10 ),
                    array ( 'id' => 160, 'count' => 24 ),
                    array ( 'id' => 161, 'count' => 24 ),
                    array ( 'id' => 162, 'count' => 24 ),
                    array ( 'id' => 129, 'count' => 3 ),
                ),
                'member' => array(   //成员奖励
                    array ( 'id' => 76, 'count' => 4 ),
                    array ( 'id' => 77, 'count' => 8 ),
                    array ( 'id' => 91, 'count' => 8 ),
                    array ( 'id' => 94, 'count' => 8 ),
                    array ( 'id' => 160, 'count' => 15 ),
                    array ( 'id' => 161, 'count' => 15 ),
                    array ( 'id' => 162, 'count' => 15 ),
                    array ( 'id' => 129, 'count' => 2 ),
                ),
            ),
            array(
                'rand' => array('rs'=>4,'re'=>5), //排名范围
                'mengzhu' => array(   //盟主奖励
                    array ( 'id' => 76, 'count' => 6 ),
                    array ( 'id' => 77, 'count' => 8 ),
                    array ( 'id' => 91, 'count' => 8 ),
                    array ( 'id' => 94, 'count' => 8 ),
                    array ( 'id' => 160, 'count' => 15 ),
                    array ( 'id' => 161, 'count' => 15 ),
                    array ( 'id' => 162, 'count' => 15 ),
                    array ( 'id' => 129, 'count' => 2 ),
                ),
                'member' => array(   //成员奖励
                    array ( 'id' => 76, 'count' => 3 ),
                    array ( 'id' => 77, 'count' => 6 ),
                    array ( 'id' => 91, 'count' => 6 ),
                    array ( 'id' => 94, 'count' => 6 ),
                    array ( 'id' => 160, 'count' => 9 ),
                    array ( 'id' => 161, 'count' => 9 ),
                    array ( 'id' => 162, 'count' => 9 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>6,'re'=>10), //排名范围
                'mengzhu' => array(   //盟主奖励
                    array ( 'id' => 76, 'count' => 4 ),
                    array ( 'id' => 77, 'count' => 6 ),
                    array ( 'id' => 91, 'count' => 6 ),
                    array ( 'id' => 94, 'count' => 6 ),
                    array ( 'id' => 160, 'count' => 9 ),
                    array ( 'id' => 161, 'count' => 9 ),
                    array ( 'id' => 162, 'count' => 9 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
                'member' => array(   //成员奖励
                    array ( 'id' => 76, 'count' => 2 ),
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 91, 'count' => 3 ),
                    array ( 'id' => 94, 'count' => 3 ),
                    array ( 'id' => 160, 'count' => 3 ),
                    array ( 'id' => 161, 'count' => 3 ),
                    array ( 'id' => 162, 'count' => 3 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>11,'re'=>20), //排名范围
                'mengzhu' => array(   //盟主奖励
                    array ( 'id' => 76, 'count' => 3 ),
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 91, 'count' => 3 ),
                    array ( 'id' => 94, 'count' => 3 ),
                    array ( 'id' => 160, 'count' => 3 ),
                    array ( 'id' => 161, 'count' => 3 ),
                    array ( 'id' => 162, 'count' => 3 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
                'member' => array(   //成员奖励
                    array ( 'id' => 76, 'count' => 1 ),
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 91, 'count' => 1 ),
                    array ( 'id' => 94, 'count' => 1 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                    array ( 'id' => 129, 'count' => 1 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    ),
);
STRING;
