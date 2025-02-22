<?php
return <<<STRING
//狩猎
array(
    array(
        'info' => array (
            'id' => 201709127,
            'title' => '狩猎',
            'index' => 110,
            'startDay' => 0, //开服第几天开始  从1开始
            'endDay' => 0, //开服第几天结束  从1开始
            'startTime' => '2017-09-11 10:00:00',
            'endTime' => '2017-10-12 22:50:00',
            'type' => 8,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼 7:严刑拷打 8：狩猎
        ),
        'hurt_level' => array(//攻击加成
            1 => 0,
            2 => 10,
            3 => 20,
            4 => 30,
        ),

    ),
);
STRING;
