<?php
return <<<STRING
//新官上任
array(
    array(
        'info' => array (
            'id' => 20170901,
            'title' => '新官上任',
            'startDay' => 1, //开服第几天开始  从1开始
            'endDay' => 8, //开服第几天结束  从1开始
            'startTime' => '2017-08-23 15:00:00',
            'endTime' => '2017-08-30 24:00:00',   //活动会提前两个小时结束预留发奖励时间
            'type' => 7,//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼 7:新官上任
        ),
        'boss' => '200000', //boss血量
        'kill_boss' => array(
            array(
                'id' => 260,
                'count' => 1,
                'kind' => 1,
                'prob_100' => 25,
            ),
            array(
                'id' => 261,
                'count' => 1,
                'kind' => 1,
                'prob_100' => 25,
            ),
            array(
                'id' => 262,
                'count' => 1,
                'kind' => 1,
                'prob_100' => 25,
            ),
            array(
                'id' => 263,
                'count' => 2,
                'kind' => 1,
                'prob_100' => 25,
            ),
        ),
        'shop' => array(//活动道具购买
            array(
                'id'=>1,
                'need'=> array('id'=>2,'count'=>2000),//购买需要代价
                'items' => array('id'=>256,'count'=>1,'kind' => 11),
                'type' => 1,//类型 1：直接购买 0：去商店
                'is_limit' => 1, //0：不限购  1:限购
                'limit' => 20, //限购数
            ),
            array(
                'id'=>2,
                'need'=> array('id'=>1,'count'=>10),//购买需要代价
                'items' => array('id'=>257,'count'=>1,'kind'=>11), //寒骨针
                'is_limit' => 1, //限购
                'limit' => 20, //限购数
            ),
            array(
                'id'=>3,
                'need'=> 0,//为0时表示不能购买
                'items' => array('id'=>258,'count'=>11), //销魂夹棍
                'is_limit' => 0, //限购
                'limit' => 0, //限购数
            ),
            array(
                'id'=>4,
                'need'=> 0,//为0时表示不能购买
                'items' => array('id'=>259,'count'=>11), //夺命烙铁
                'is_limit' => 0, //限购
                'limit' => 0, //限购数
            ),
        ),

        'exchange' => array(//积分兑换
            array(//香囊
                'id' => 1,
                'need' => 12, //需要积分
                'item' =>array('id'=>93,'count'=>1),
                'limit' => 50,//限购次数
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//玛瑙心
                'id' => 2,
                'need' => 12, //需要积分
                'item' =>array('id'=>91,'count'=>1),
                'limit' => 50,
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//家宴食材
                'id' => 3,
                'need' => 60, //需要积分
                'item' =>array('id'=>141,'count'=>1),
                'limit' => 5,
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//玉佩
                'id' => 4,
                'need' => 20, //需要积分
                'item' =>array('id'=>94,'count'=>1),
                'limit' => 50, // 0表示无限购
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//翡翠心
                'id' => 5,
                'need' => 20, //需要积分
                'item' =>array('id'=>92,'count'=>1),
                'limit' => 50,
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//家宴佐料
                'id' => 6,
                'need' => 60, //需要积分
                'item' =>array('id'=>142,'count'=>1),
                'limit' => 5,
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//书籍经验书
                'id' => 7,
                'need' => 50, //需要积分
                'item' =>array('id'=>81,'count'=>1),
                'limit' => 50,
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
            array(//卷轴礼包
                'id' => 8,
                'need' => 200, //需要积分
                'item' =>array('id'=>77,'count'=>1,'kind' => 1),
                'limit' => 30, // 0表示无限购
                'is_limit' => 1, //是否限购 1限购 0不限购
            ),
        ),

        'rwd' => array(
            'my'=>array(//个人排行奖励
                array(
                    'rand' => array('rs'=>1,'re'=>1), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 8 ),
                        array ( 'id' => 160, 'count' => 10 ),
                        array ( 'id' => 161, 'count' => 10 ),
                        array ( 'id' => 162, 'count' => 10 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>2,'re'=>2), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 6 ),
                        array ( 'id' => 160, 'count' => 8 ),
                        array ( 'id' => 161, 'count' => 8 ),
                        array ( 'id' => 162, 'count' => 8 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>3,'re'=>3), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 5 ),
                        array ( 'id' => 160, 'count' => 6 ),
                        array ( 'id' => 161, 'count' => 6 ),
                        array ( 'id' => 162, 'count' => 6 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>4,'re'=>5), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 4 ),
                        array ( 'id' => 160, 'count' => 5 ),
                        array ( 'id' => 161, 'count' => 5 ),
                        array ( 'id' => 162, 'count' => 5 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>6,'re'=>10), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 3 ),
                        array ( 'id' => 160, 'count' => 4 ),
                        array ( 'id' => 161, 'count' => 4 ),
                        array ( 'id' => 162, 'count' => 4 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>11,'re'=>20), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 2 ),
                        array ( 'id' => 160, 'count' => 3 ),
                        array ( 'id' => 161, 'count' => 3 ),
                        array ( 'id' => 162, 'count' => 3 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>21,'re'=>50), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 54, 'count' => 1 ),
                        array ( 'id' => 160, 'count' => 2 ),
                        array ( 'id' => 161, 'count' => 2 ),
                        array ( 'id' => 162, 'count' => 2 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>51,'re'=>100), //排名范围
                    'member' => array(   //成员奖励
                        array ( 'id' => 160, 'count' => 1 ),
                        array ( 'id' => 161, 'count' => 1 ),
                        array ( 'id' => 162, 'count' => 1 ),
                    ),
                ),
            ),
            'club'=>array(//联盟排行奖励
                array(
                    'rand' => array('rs'=>1,'re'=>1), //排名范围
                    'mengzhu' => array(   //盟主奖励
                        array ( 'id' => 76, 'count' => 10 ),
                        array ( 'id' => 71, 'count' => 8 ),
                        array ( 'id' => 72, 'count' => 8 ),
                        array ( 'id' => 73, 'count' => 8 ),
                    ),
                    'member' => array(   //成员奖励
                        array ( 'id' => 76, 'count' => 8 ),
                        array ( 'id' => 71, 'count' => 6 ),
                        array ( 'id' => 72, 'count' => 6 ),
                        array ( 'id' => 73, 'count' => 6 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>2,'re'=>2), //排名范围
                    'mengzhu' => array(   //盟主奖励
                        array ( 'id' => 76, 'count' => 8 ),
                        array ( 'id' => 71, 'count' => 6 ),
                        array ( 'id' => 72, 'count' => 6 ),
                        array ( 'id' => 73, 'count' => 6 ),
                    ),
                    'member' => array(   //成员奖励
                        array ( 'id' => 76, 'count' => 6 ),
                        array ( 'id' => 71, 'count' => 5 ),
                        array ( 'id' => 72, 'count' => 5 ),
                        array ( 'id' => 73, 'count' => 5 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>3,'re'=>3), //排名范围
                    'mengzhu' => array(   //盟主奖励
                        array ( 'id' => 76, 'count' => 6 ),
                        array ( 'id' => 71, 'count' => 5 ),
                        array ( 'id' => 72, 'count' => 5 ),
                        array ( 'id' => 73, 'count' => 5 ),
                    ),
                    'member' => array(   //成员奖励
                        array ( 'id' => 76, 'count' => 4 ),
                        array ( 'id' => 71, 'count' => 4 ),
                        array ( 'id' => 72, 'count' => 4 ),
                        array ( 'id' => 73, 'count' => 4 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>4,'re'=>5), //排名范围
                    'mengzhu' => array(   //盟主奖励
                        array ( 'id' => 76, 'count' => 4 ),
                        array ( 'id' => 71, 'count' => 4 ),
                        array ( 'id' => 72, 'count' => 4 ),
                        array ( 'id' => 73, 'count' => 4 ),
                    ),
                    'member' => array(   //成员奖励
                        array ( 'id' => 76, 'count' => 3 ),
                        array ( 'id' => 71, 'count' => 3 ),
                        array ( 'id' => 72, 'count' => 3 ),
                        array ( 'id' => 73, 'count' => 3 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>6,'re'=>10), //排名范围
                    'mengzhu' => array(   //盟主奖励
                        array ( 'id' => 76, 'count' => 3 ),
                        array ( 'id' => 71, 'count' => 3 ),
                        array ( 'id' => 72, 'count' => 3 ),
                        array ( 'id' => 73, 'count' => 3 ),
                    ),
                    'member' => array(   //成员奖励
                        array ( 'id' => 76, 'count' => 2 ),
                        array ( 'id' => 71, 'count' => 2 ),
                        array ( 'id' => 72, 'count' => 2 ),
                        array ( 'id' => 73, 'count' => 2 ),
                    ),
                ),
                array(
                    'rand' => array('rs'=>11,'re'=>20), //排名范围
                    'mengzhu' => array(   //盟主奖励
                        array ( 'id' => 76, 'count' => 2 ),
                        array ( 'id' => 71, 'count' => 2 ),
                        array ( 'id' => 72, 'count' => 2 ),
                        array ( 'id' => 73, 'count' => 2 ),
                    ),
                    'member' => array(   //成员奖励
                        array ( 'id' => 76, 'count' => 1 ),
                        array ( 'id' => 71, 'count' => 1 ),
                        array ( 'id' => 72, 'count' => 1 ),
                        array ( 'id' => 73, 'count' => 1 ),
                    ),
                ),
            ),

        ),
        'story' => '一个雷电交加的夜晚。南城外，刀光剑影声，声声入耳！闻讯南城外有女贼寇，已奸杀我城英俊少男数十人。大人亲自带队，在雨夜中经过一番厮杀，最终将其缉拿归案，现等候大人审问！',
        'msg' => '一个雷电交加的夜晚。南城外，刀光剑影声，声声入耳！闻讯南城外有女贼寇，已奸杀我城英俊少男数十人。大人亲自带队，在雨夜中经过一番厮杀，最终将其缉拿归案，现等候大人审问！'
    ),
);
STRING;
