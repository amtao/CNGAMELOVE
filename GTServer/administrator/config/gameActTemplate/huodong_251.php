<?php
return <<<STRING
//关卡冲榜
array(
    array(
        'info' => array (
            'id' => 20170825,
            'title' => '关卡冲榜',
            'pindex' => 251,
            'startDay' => 1, //开服第几天开始  从1开始
            'endDay' => 8, //开服第几天结束  从1开始
            'startTime' => '2017-7-25 00:00:00',
            'endTime' => '2017-8-01 23:59:59',
            'type' => 3,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼
        ),
        'rwd' => array(
            array(
                'rand' => array('rs'=>1,'re'=>1), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 12 ),
                    array ( 'id' => 76, 'count' => 12 ),
                    array ( 'id' => 79, 'count' => 12 ),
                    array ( 'id' => 130, 'count' => 10 ),
                    array ( 'id' => 53, 'count' => 12 ),
                    array ( 'id' => 52, 'count' => 12 ),
                    array ( 'id' => 65, 'count' => 120 ),
                    array ( 'id' => 66, 'count' => 120 ),
                    array ( 'id' => 160, 'count' => 36 ),
                    array ( 'id' => 161, 'count' => 36 ),
                    array ( 'id' => 162, 'count' => 36 ),
                    array ( 'id' => 192, 'count' => 1,'kind' => 10 ),
                ),
            ),
            array(
                'rand' => array('rs'=>2,'re'=>2), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 9 ),
                    array ( 'id' => 76, 'count' => 9 ),
                    array ( 'id' => 79, 'count' => 9 ),
                    array ( 'id' => 130, 'count' => 7 ),
                    array ( 'id' => 53, 'count' => 9 ),
                    array ( 'id' => 52, 'count' => 9 ),
                    array ( 'id' => 65, 'count' => 90 ),
                    array ( 'id' => 66, 'count' => 90 ),
                    array ( 'id' => 160, 'count' => 33 ),
                    array ( 'id' => 161, 'count' => 33 ),
                    array ( 'id' => 162, 'count' => 33 ),
                ),
            ),
            array(
                'rand' => array('rs'=>3,'re'=>3), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 7 ),
                    array ( 'id' => 76, 'count' => 7 ),
                    array ( 'id' => 79, 'count' => 7 ),
                    array ( 'id' => 130, 'count' => 5 ),
                    array ( 'id' => 53, 'count' => 7 ),
                    array ( 'id' => 52, 'count' => 7 ),
                    array ( 'id' => 65, 'count' => 70 ),
                    array ( 'id' => 66, 'count' => 70 ),
                    array ( 'id' => 160, 'count' => 21 ),
                    array ( 'id' => 161, 'count' => 21 ),
                    array ( 'id' => 162, 'count' => 21 ),
                ),
            ),
            array(
                'rand' => array('rs'=>4,'re'=>5), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 6 ),
                    array ( 'id' => 76, 'count' => 6 ),
                    array ( 'id' => 79, 'count' => 6 ),
                    array ( 'id' => 130, 'count' => 4 ),
                    array ( 'id' => 53, 'count' => 6 ),
                    array ( 'id' => 52, 'count' => 6 ),
                    array ( 'id' => 65, 'count' => 60 ),
                    array ( 'id' => 66, 'count' => 60 ),
                    array ( 'id' => 160, 'count' => 9 ),
                    array ( 'id' => 161, 'count' => 9 ),
                    array ( 'id' => 162, 'count' => 9 ),
                ),
            ),
            array(
                'rand' => array('rs'=>6,'re'=>10), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 5 ),
                    array ( 'id' => 76, 'count' => 5 ),
                    array ( 'id' => 79, 'count' => 5 ),
                    array ( 'id' => 130, 'count' => 3 ),
                    array ( 'id' => 53, 'count' => 5 ),
                    array ( 'id' => 52, 'count' => 5 ),
                    array ( 'id' => 65, 'count' => 50 ),
                    array ( 'id' => 66, 'count' => 50 ),
                    array ( 'id' => 160, 'count' => 1 ),
                    array ( 'id' => 161, 'count' => 1 ),
                    array ( 'id' => 162, 'count' => 1 ),
                ),
            ),
            array(
                'rand' => array('rs'=>11,'re'=>20), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 4 ),
                    array ( 'id' => 76, 'count' => 4 ),
                    array ( 'id' => 79, 'count' => 4 ),
                    array ( 'id' => 130, 'count' => 2 ),
                    array ( 'id' => 53, 'count' => 4 ),
                    array ( 'id' => 52, 'count' => 4 ),
                    array ( 'id' => 65, 'count' => 40 ),
                    array ( 'id' => 66, 'count' => 40 ),
                ),
            ),
            array(
                'rand' => array('rs'=>21,'re'=>50), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 3 ),
                    array ( 'id' => 76, 'count' => 3 ),
                    array ( 'id' => 79, 'count' => 3 ),
                    array ( 'id' => 130, 'count' => 1 ),
                    array ( 'id' => 53, 'count' => 3 ),
                    array ( 'id' => 52, 'count' => 3 ),
                    array ( 'id' => 65, 'count' => 30 ),
                    array ( 'id' => 66, 'count' => 30 ),
                ),
            ),
            array(
                'rand' => array('rs'=>51,'re'=>100), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 2 ),
                    array ( 'id' => 76, 'count' => 2 ),
                    array ( 'id' => 79, 'count' => 2 ),
                    array ( 'id' => 130, 'count' => 1 ),
                    array ( 'id' => 53, 'count' => 2 ),
                    array ( 'id' => 52, 'count' => 2 ),
                    array ( 'id' => 65, 'count' => 20 ),
                    array ( 'id' => 66, 'count' => 20 ),
                ),
            ),
            array(
                'rand' => array('rs'=>101,'re'=>200), //排名范围
                'member' => array(   //成员奖励
                    array ( 'id' => 77, 'count' => 1 ),
                    array ( 'id' => 76, 'count' => 1 ),
                    array ( 'id' => 79, 'count' => 1 ),
                    array ( 'id' => 130, 'count' => 1 ),
                    array ( 'id' => 53, 'count' => 1 ),
                    array ( 'id' => 52, 'count' => 1 ),
                    array ( 'id' => 65, 'count' => 10 ),
                    array ( 'id' => 66, 'count' => 10 ),
                ),
            ),
        ),
        'msg' => '老鼠是老鼠,不是猫!!!!'
    ),
);
STRING;
