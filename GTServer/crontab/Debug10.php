<?php
/**
 */
set_time_limit(0);
$start = microtime(true);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';


$sevid = 999;

//获取/设置 服务器ID
$SevidCfg = Common::getSevidCfg($sevid);

//活动表
$act_cfg= array(
	1 => '3种类资源经营信息',
	2 => '政务处理',
	3 => '门客出战列表地图关卡BOSS',
	4 => '门客出战列表世界BOSS-蒙古军来袭',
	5 => '门客出战列表世界BOSS-葛二蛋来袭',
	6 => '门客出战列表联盟boss',
	7 => '门客出战列表衙门战正常出战+出使令',
	8 => '门客出战列表衙门战挑战令挑战+复仇',
	9 => '子嗣招亲列表',
	10 => '子嗣提亲列表',
	11 => '红颜的精力',
	12 => '子嗣席位数量类',
	13 => '战斗2冷却时间',
	14 => '道具合成类',
	15 => '书院桌子数量类',
	16 => '学院学习类',
	17 => '排行膜拜',
	18 => '皇宫请安',
	19 => '牢房',
	20 => '名望',
	21 => '蒙古军来袭',
	22 => '割二蛋来袭',
	23 => '世界BOSS积分兑换',
	24 => '通商',
	25 => '称号',
	26 => '寻访',
	27 => '寻访-赈灾-运势恢复',
	28 => '寻访',
	29 => '寻访--NPC',
	30 => '称号',
	31 => '勤政爱民',
	32 => '新手引导步骤保存',
	33 => '公告',
	34 => '皇宫-个人宣言',
	35 => '每日任务',
	36 => '成就',
	37 => '签到',
	38 => '公用兑换码',
	39 => '主线任务',
	40 => '联盟个人信息',
	41 => '联盟个人信息-贡献兑换商店',
	42 => '联盟-跨服帮会战门客列表',
	50 => '酒楼-个人宴会信息',
	51 => '酒楼-兑换商店',
	52 => '酒楼-消息-我的历史宴会',
	53 => '酒楼-消息-仇人信息',
	54 => '酒楼-兑换商店元宝刷新',
	55 => '宴会-赴会次数',
	58 => '翰林院-个人信息',
	60 => '衙门战--个人信息',
	61 => '衙门战--战斗信息',
	62 => '衙门战--防守信息',
	63 => '衙门战--仇人列表',
	65 => '神迹福利--还没做',
	66 => '首充福利--还没做',
	67 => 'vip福利--还没做',
	68 => '月卡/年卡活动--还没做',
	69 => '加微信加QQ',
	70 => '充值-充值档次',
	71 => 'vip经验',
	72 => '充值翻倍',
	73 => '获取称号',
	74 => '用户场景',
	81 => '商城-单品限购',
	82 => '商城-特惠礼包',
	83 => '双十一单品限购',
	84 => '双十一特惠礼包',
	85 => '双十一-累计充值',
	90 => '主线的宠幸第一次生小孩',
	91 => '势力达到某一值新增红颜',
	92 => '卷轴升级伪概率',
	93 => '发放邮件',
	94 => '第一次生小孩',
	95 => '第一次科举',
	96 => '通用兑换码',
	97 => '聊天-加入黑名单',
	98 => '聊天-禁言内容',
	99 => '数据记录',
	100 => '新官上任-商城',
	101 => '新官上任-兑换',
	102 => '新官上任-仓库',
	103 => '新官上任-积分',
	104 => '惩戒来福-商城',
	105 => '惩戒来福-兑换',
	106 => '惩戒来福-仓库',
	107 => '惩戒来福-积分',
	110 => '狩猎',
	111 => '讨伐',
	112 => '通商-关卡',
	113 => '通商-一键通商开启',
	114 => '国庆活动-商城',
	115 => '国庆活动-兑换',
	116 => '国庆活动-仓库',
	117 => '国庆活动-积分',
	118 => '重阳节活动-商城',
	119 => '重阳节活动-兑换',
	120 => '重阳节活动-仓库',
	121 => '重阳节活动-积分',
	122 => '重阳节活动-累计充值',
	123 => '感恩节活动-商城',
	124 => '感恩节活动-兑换',
	125 => '感恩节活动-仓库',
	126 => '感恩节活动-积分',
	127 => '感恩节活动-累计充值',
	130 => '好友系统',
	131 => '申请好友列表',
	132 => '申请好友列表限制',
	133 => '亲家列表',
	134 => '亲家亲密度',
	135 => '私聊列表',
	199 => '活动生效列表版本',
	200 => '活动生效列表',
	201 => '限时奖励-元宝消耗',
	202 => '限时奖励-士兵消耗',
	203 => '限时奖励-银两消耗',
	204 => '限时奖励-强化卷轴消耗',
	205 => '限时奖励-亲密度涨幅',
	206 => '限时奖励-势力涨幅',
	207 => '限时奖励-处理政务次数',
	208 => '限时奖励-累计登录天数',
	209 => '限时奖励-衙门分数涨幅',
	210 => '限时奖励-联姻次数',
	211 => '限时奖励-书院学习',
	212 => '限时奖励-经营商产次数',
	213 => '限时奖励-经营农产次数',
	214 => '限时奖励-招募士兵次数',
	215 => '限时奖励-击杀葛尔丹次数',
	216 => '限时奖励-挑战书消耗',
	217 => '限时奖励-惩戒犯人次数',
	218 => '限时奖励-赈灾次数',
	219 => '限时奖励-体力丹消耗',
	220 => '限时奖励-活力丹消耗',
	221 => '限时奖励-魅力值涨幅',
	222 => '限时奖励-赴宴次数',
	223 => '限时奖励-联盟副本伤害',
	224 => '限时奖励-联盟副本击杀（累计击杀僵尸）',
	225 => '限时奖励-酒楼积分涨幅',
	250 => '联盟冲榜',
	251 => '关卡冲榜',
	252 => '势力冲榜',
	253 => '亲密冲榜',
	254 => '衙门冲榜',
	255 => '银两冲榜',
	256 => '酒楼冲榜',
	257 => '士兵冲榜',
	260 => '充值活动-每日充值',
	261 => '充值活动-累计充值',
	262 => '充值活动-累天充值',
	270 => '四大奸臣',
	271 => '巾帼五虎',
	280 => '新官上任',
	281 => '重阳节活动',
	282 => '惩戒来福',
	283 => '国庆活动',
	284 => '感恩节活动',
	285 => '双十一活动',
	300 => '跨服-衙门战',
	301 => '衙门战-战斗信息',
	302 => '跨服衙门战出战',
	303 => '跨服衙门战复仇',
	304 => '跨服衙门-防守信息',
	305 => '跨服衙门-仇人信息',
	306 => '跨服衙门-预选赛门票',
	307 => '跨服衙门-领取全服奖励',
	400 => '兑换码',
);


//$db = Common::getDbName();
$config = Common::getConfig(GAME_MARK."/AllServerMemConfig");

//本服配置
$config_info = $config[$SevidCfg['sevid']];
print_r($config_info);

//连接数据库
$db = Common::getDbBySevId($SevidCfg['sevid']);
//连接缓存
$cache = Common::getCacheBySevId($SevidCfg['sevid']);

//遍历所有表
for ($i = 0 ; $i < $config_info['weight'] ; $i++)
{
	$table = 'user_'.computeTableId($i); //用户表
	$sql = "select uid from {$table}";
	$data = $db->fetchArray($sql);

	//预制表名
	$table_hero = 'hero_'.computeTableId($i); //门客表
	$table_wife = 'wife_'.computeTableId($i); //
	$table_son = 'son_'.computeTableId($i); //红颜表
	$table_item = 'item_'.computeTableId($i); //子嗣表
	$table_act = 'act_'.computeTableId($i); //活动表

	//遍历所有用户
	foreach ($data as $user){
		$uid = $user['uid'];

		//用户表user_
		// $key = $uid.'_user';
		// $u_date = $cache->get($key);//查询用户缓存
		// if (!empty($u_date)){
		// 	//如果缓存存在 更新数据库
		// 	$sql = "update `{$table}` set
		// 	`name` = '{$u_date['name']}',
		// 	`job` = '{$u_date['job']}',
		// 	`sex` = '{$u_date['sex']}',
		// 	`level` = '{$u_date['level']}',
		// 	`vip` = '{$u_date['vip']}',
		// 	`step` = '{$u_date['step']}',
		// 	`bmap` = '{$u_date['bmap']}',
		// 	`smap` = '{$u_date['smap']}',
		// 	`mkill` = '{$u_date['mkill']}',
		// 	`baby_num` = '{$u_date['baby_num']}',
		// 	`cb_time` = '{$u_date['cb_time']}',
		// 	`clubid` = '{$u_date['clubid']}',
		// 	`mw_num` = '{$u_date['mw_num']}',
		// 	`mw_day` = '{$u_date['mw_day']}',
		// 	`voice` = '{$u_date['voice']}',
		// 	`music` = '{$u_date['music']}',
		// 	`loginday` = '{$u_date['loginday']}',
		// 	`lastlogin` = '{$u_date['lastlogin']}',
		// 	`platform` = '{$u_date['platform']}',
		// 	`channel_id` = '{$u_date['channel_id']}',
		// 	`ip` = '{$u_date['ip']}',
		// 	`xuanyan` = '{$u_date['xuanyan']}',
		// 	`exp` = '{$u_date['exp']}',
		// 	`coin` = '{$u_date['coin']}',
		// 	`food` = '{$u_date['food']}',
		// 	`army` = '{$u_date['army']}',
		// 	`cash_sys` = '{$u_date['cash_sys']}',
		// 	`cash_buy` = '{$u_date['cash_buy']}',
		// 	`cash_use` = '{$u_date['cash_use']}'
		// 	where `uid`='{$uid}'";
        //
		// 	echo $sql;
		// 	echo "\n";
		// 	exit;
		// 	//$db->query($sql);
		// }

		/*
		//门客表hero_
		$key = $uid.'_hero';
		$h_date = $cache->get($key);//查询门客缓存
		if (!empty($h_date)){
			//如果缓存存在 更新数据库
			foreach ($h_date as $vh){
				$sql = "update {$table_hero} set
				`uid`     = '{$u_date['uid']}',
				`heroid`  = '{$u_date['heroid']}',
				`level`   = '{$u_date['level']}',
				`exp`     = '{$u_date['exp']}',
				`zzexp`   = '{$u_date['zzexp']}',
				`pkexp`   = '{$u_date['pkexp']}',
				`senior`  = '{$u_date['senior']}',
				`epskill` = '{$u_date['epskill']}',
				`pkskill` = '{$u_date['pkskill']}',
				`ghskill` = '{$u_date['ghskill']}',
				`e1`      = '{$u_date['e1']}',
				`e2`      = '{$u_date['e2']}',
				`e3`      = '{$u_date['e3']}',
				`e4`      = '{$u_date['e4']}'
				where uid = '{$uid}' and heroid = {$vh['hero id']}"; //----伪代码
				echo $sql;
				echo "\n";
				//$db->query($sql);
			}
		}



		//后宫表wife_
        $key = $uid.'_wife';
		$h_date = $cache->get($key);//查询妻妾缓存
		if (!empty($h_date)){
			//如果缓存存在 更新数据库
			foreach ($h_date as $vh){
				$sql = "update {$table_hero} set
				`uid`    = '{$u_date['uid']}'   ,
				`wifeid` = '{$u_date['wifeid']}',
				`love`   = '{$u_date['love']}'  ,
				`flower` = '{$u_date['flower']}',
				`exp`    = '{$u_date['exp']}'   ,
				`skill`  = '{$u_date['skill']}' ,
				`state`  = '{$u_date['state']}' ,
				`ptime`  = '{$u_date['ptime']}' ,
				`count`  = '{$u_date['count']}' ,
				where uid = {$uid} and heroid = {$vh['hero id']}"; //----伪代码
				//$db->query($sql);
			}
		}

		//子嗣表son_
        $key = $uid.'_son';
		$h_date = $cache->get($key);//查询子嗣缓存
		if (!empty($h_date)){
			//如果缓存存在 更新数据库
			foreach ($h_date as $vh){
				$sql = "update {$table_hero} set

				`uid`      = '{$u_date['uid']}',
				`sonuid`   = '{$u_date['sonuid']}',
				`name`     = '{$u_date['name']}',
				`sex`      = '{$u_date['sex']}',
				`mom`      = '{$u_date['mom']}',
				`state`    = '{$u_date['state']}',
				`e1`       = '{$u_date['e1']}',
				`e2`       = '{$u_date['e2']}',
				`e3`       = '{$u_date['e3']}',
				`e4`       = '{$u_date['e4']}',

				`talent`   = '{$u_date['talent']}',
				`cpoto`    = '{$u_date['cpoto']}',
				`level`    = '{$u_date['level']}',
				`exp`      = '{$u_date['exp']}',
				`power`    = '{$u_date['power']}',
				`ptime`    = '{$u_date['ptime']}',

				`honor`    = '{$u_date['honor']}',

				`tquid`    = '{$u_date['tquid']}',
				`tqitem`   = '{$u_date['tqitem']}',
				`spuid`    = '{$u_date['spuid']}',
				`spsonuid` = '{$u_date['spsonuid']}',
				`sptime`   = '{$u_date['sptime']}',
				where uid = {$uid} and heroid = {$vh['hero id']}"; //----伪代码
				//$db->query($sql);
			}
		}


		//道具表item_
		$key = $uid.'_item';
		$h_date = $cache->get($key);//查询道具缓存
		if (!empty($h_date)){
			//如果缓存存在 更新数据库
			foreach ($h_date as $vh){
				$sql = "update {$table_hero} set
				`uid`    = '{$u_date['uid']}'   ,
				`itemid`  = '{$u_date['itemid']}' ,
				`count`  = '{$u_date['count']}' ,
				where uid = {$uid} and heroid = {$vh['hero id']}"; //----伪代码
				//$db->query($sql);
			}
		}

		//活动表act_
		$key = $uid.'_act';
		$h_date = $cache->get($key);//查询道具缓存
		if (!empty($h_date)){
			//如果缓存存在 更新数据库
			foreach ($h_date as $vh){
				$sql = "update {$table_hero} set
				`uid`    = '{$u_date['uid']}'   ,
				`actid`  = '{$u_date['actid']}' ,
				`tjson`  = '{$u_date['tjson']}' ,
				where uid = {$uid} and heroid = {$vh['hero id']}"; //----伪代码
				//$db->query($sql);
			}
		}

		//邮件表mail_
		$key = $uid.'_mail';
		$h_date = $cache->get($key);//查询道具缓存
		if (!empty($h_date)){
			//如果缓存存在 更新数据库
			foreach ($h_date as $vh){
				$sql = "update {$table_hero} set
				`mid`      = '{$_date['mid']}',
				`uid`      = '{$_date['uid']}',
				`mtitle`   = '{$_date['mtitle']}',
				`mcontent` = '{$_date['mcontent']}',
				`items`    = '{$_date['items']}',
				`mtype`    = '{$_date['mtype']}',
				`fts`      = '{$_date['fts']}',
				`rts`      = '{$_date['rts']}',
				`isdel`    = '{$_date['isdel']}',
				where uid = {$uid} and heroid = {$vh['hero id']}"; //----伪代码
				//$db->query($sql);
			}


		/*

		//活动数据表act_
		foreach($act_cfg as $actid => $actv){
			$key = $uid.'_act_'.$actid;
			$a_date = $cache->get($key);//查询活动缓存
			if (!empty($a_date)){
				$tjson = json_encode($a_date,JSON_UNESCAPED_UNICODE);
				$sql = "update `{$table}` set `tjson`	='{$tjson}' where `uid` ='{$uid}' and `actid` = '{$actid}' limit 1";
				//$db->query($sql);
			}
		}
		*/
	}
}





function computeTableId($uid)
{
	return str_pad( $uid % 100 , 2 , '0' , STR_PAD_LEFT );
}


echo PHP_EOL, '----------------end----------------------', PHP_EOL;
exit();
