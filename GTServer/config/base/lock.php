<?php
//加锁配置
$cfg = array(
	/*
	'模块' => array(
		'接口' => array(
			'type' => '锁类型 1本服 2合服,3跨服,4通服,5指定跨服',
			'key' => '锁关键字wordBoss',
		),
	),
	*/
	'hanlin' => array(//翰林院模块
		'opendesk' => array('type' => 2,'key' => 'hanlin'),//新建房间
		'sitdown' => array('type' => 2,'key' => 'hanlin'),//加入空位置
		'ti' => array('type' => 2,'key' => 'hanlin'),//踢人打架
        'suoding' => array('type' => 2,'key' => 'hanlin'),//锁定
	),
	'wordboss' => array(//世界BOSS
		'hitgeerdan' => array('type' => 2,'key' => 'wordboss'),//葛二蛋打
	),
	/*
	'chat' => array(//聊天模块
		'sev' => array('type' => 2,'key' => 'chatsev'),//公共频道聊天
		'kuafu' => array('type' => 3,'key' => 'chatkuafu'),//跨服频道聊天
	),
	*/
	'jiulou' => array(//酒楼
		'yhHold' => array('type' => 2,'key' => 'jiulou'),//举办宴会
		'yhChi' => array('type' => 2,'key' => 'jiulou'),//吃宴会
        'yhGo' => array('type' => 2,'key' => 'jiulou'),//前往宴会(可能触发结算)
	),
	'son' => array(//子嗣联姻
		'tiqin' => array('type' => 2,'key' => 'sonmar'),//提亲
		'jiehun' => array('type' => 2,'key' => 'sonmar'),//全服结婚
		'cancel' => array('type' => 2,'key' => 'sonmar'),//撤回提亲
	),
	'yamen' => array(//衙门战
		'fight' => array('type' => 2,'key' => 'yamen'),//衙门战打
		'getrwd' => array('type' => 2,'key' => 'yamen'),//抽奖
	),
	
	//工会操作
    'club' => array(//工会
        'memberPost' => array('type' => 2,'key_arg' => 'club_lock'),//职位变更/逐出联盟
        'outClub' => array('type' => 2,'key_arg' => 'club_lock'),//退出联盟
        'delClub' => array('type' => 2,'key_arg' => 'club_lock'),//删除联盟
        'dayGongXian' => array('type' => 2,'key_arg' => 'club_lock'),//每日贡献
        'clubBossOpen' => array('type' => 2,'key_arg' => 'club_lock'),//开启联盟副本
        'clubBossPK' => array('type' => 2,'key_arg' => 'club_boss_lock'),//联盟boss-pk
        'kuaPKAdd' => array('type' => 2,'key' => 'club_boss_lock'),//帮会战门客派遣/更
        'kuaPKCszr' => array('type' => 2,'key' => 'club_boss_lock'),//帮会战参赛阵容
        'kuaPKusejn' => array('type' => 2,'key' => 'club_boss_lock'),//帮会战帮会战pk使用锦囊
        'kuaPKrwdget' => array('type' => 2,'key' => 'club_lock'),//帮会战帮会战pk奖励领取
    ),
	//活动操作
	'huodong' => array(
		'hd295sendHb' => array('type' => 2,'key_arg' => 'club_hb_lock'),//发红包
		'hd295getHb' => array('type' => 2,'key_arg' => 'club_hb_lock'),//领红包
		'hd298play' => array('type' => 2,'key_arg' => 'new_year_lock'),//新年打boss
	),
);

return $cfg;

