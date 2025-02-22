<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
ini_set('memory_limit','1500M');
require_once dirname( __FILE__ ) . '/../public/common.inc.php';

//全局开始时间
$_GLOBALS_MICRO_TIME = microtime(true);
//分析开始
XHProfTool::start($_GLOBALS_MICRO_TIME, true);
XHProfTool::setKey('XHProfTool_clubKua_pk');

//按指定大区跑
$areaArr = array();
if (isset($_SERVER['argv'][1]) && isset($_SERVER['argv'][2])) {
    $areaArr = array(
        's'=>intval($_SERVER['argv'][1]),
        'e'=>intval($_SERVER['argv'][2]),
    );
}

Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
Common::loadModel("Master");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;


if ( is_array($serverList) ) {
    $areaNow = 1;

	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID
		
		echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
		
		//如果不是主服务器  跳过
		if($Sev_Cfg['sevid'] != Game::club_pk_serv($Sev_Cfg['sevid'])){
			echo PHP_EOL, '>>>不是主服务器-跳过', PHP_EOL;
			continue;
		}
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}

		//按指定大区跑
		if (!empty($areaArr)) {
		    if ($areaNow < $areaArr['s'] || $areaNow > $areaArr['e']) {
                echo PHP_EOL, '>>>'.$areaNow.' 不在指定大区跳过', PHP_EOL;
                $areaNow++;
                continue;
            }
        }

		//PK
		do_club_pk();
		$areaNow++;
	}
}


Master::click_destroy();


echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;

XHProfTool::end('clubkua_pk', 'clubkua_pk',1);

exit();



/**
 * 匹配
 */
function do_club_pk(){
	
	global $Sev_Cfg;
	
	//加入匹配列表
	$Sev50Model = Master::getSev50();
	if(empty($Sev50Model->info)){
		echo PHP_EOL, '>>>无匹配数据', PHP_EOL;
		return 0;
	}
	$pk_members = array();

    print_r($Sev50Model->info);

	//pk
	foreach($Sev50Model->info as $cid => $fcinfo ){
		
		if(empty($fcinfo['fcid'])){
			
			echo $cid." =======轮空\n";

			//伤害排行
			$Sev51Model = Master::getSev51($cid,Game::get_sevid_club($cid));
			$myclub = $Sev51Model->outf_pk();
			$members = $myclub['list'];
			$myhit = reset_hit($members);
			$Sev57Model = Master::getSev57($cid,Game::get_sevid_club($cid));
			$Sev57Model->add(1,$myhit,0);
			//重置奖励
			$Sev55Model = Master::getSev55($cid,Game::get_sevid_club($cid));
			$Sev55Model->reset(1,0,$myclub['clevel']);
			//加积分
            $redis11Model = Master::getRedis11();
            $redis11Model->zIncrBy($cid,$myclub['clevel']);
			continue;
		}
		
		if(!empty($pk_members) && in_array($cid,$pk_members)){
            //echo $cid." 过滤\n";
			continue;
		}
		$pk_members[] = $fcinfo['fcid'];
		
		
		echo $cid.' VS '.$fcinfo['fcid'] ." 开始\n";
		
		$Sev51Model = Master::getSev51($cid,Game::get_sevid_club($cid));
		$myclub = $Sev51Model->outf_pk();
		
		$fSev51Model = Master::getSev51( $fcinfo['fcid'],Game::get_sevid_club($fcinfo['fcid']));
		$fclub = $fSev51Model->outf_pk();
		$fclub['clevel'] = empty($fclub['clevel'])?$myclub['clevel']:$fclub['clevel'];
		$myclub['clevel'] = empty($myclub['clevel'])?$fclub['clevel']:$myclub['clevel'];
		//我方职位buff
		$mypbuff = array(
			1 => $myclub['post']['mz'] * $myclub['clevel'],
			2 => $myclub['post']['fmz'] * $myclub['clevel'],
			3 => $myclub['post']['jy'] * $myclub['clevel'],
			4 => $myclub['post']['cy'] * $myclub['clevel'],
		);
		//敌方职位buff
		$fpbuff = array(
			1 => $fclub['post']['mz'] * $fclub['clevel'],
			2 => $fclub['post']['fmz'] * $fclub['clevel'],
			3 => $fclub['post']['jy'] * $fclub['clevel'],
			4 => $fclub['post']['cy'] * $fclub['clevel'],
		);
		$members = $myclub['list'];
		$fmembers = $fclub['list'];
		
		$myhit = reset_hit($members);
		$fhit = reset_hit($fmembers);
		//pk
		$log = pk($members,$mypbuff,$fmembers,$fpbuff,array(),0,array(),0,array(),array(),0,$myhit,$fhit);
		
		
		$my_win = $log['my_win'];
		$f_win = $log['f_win'];
		if($my_win == $f_win){
			$my_win = 1;
			$f_win = 0;
		}
		
		//加入我方战斗日志
		$Sev53Model = Master::getSev53($cid,Game::get_sevid_club($cid));
		$Sev53Model->add($log['mylog'],$cid,$fcinfo['fcid']);
		//重置奖励
		$Sev55Model = Master::getSev55($cid,Game::get_sevid_club($cid));
		$Sev55Model->reset($my_win,$fcinfo['fcid'],$fclub['clevel']);
		//加入敌方战斗日志
		$fSev53Model = Master::getSev53($fcinfo['fcid'],Game::get_sevid_club($fcinfo['fcid']));
		$fSev53Model->add($log['flog'],$fcinfo['fcid'],$cid);
		//重置奖励
		$fSev55Model = Master::getSev55($fcinfo['fcid'],Game::get_sevid_club($fcinfo['fcid']));
		$fSev55Model->reset($f_win,$cid,$myclub['clevel']);
		
		//积分排名
		$redis11Model = Master::getRedis11();
		$myjifen = $my_win == 1 ? $fclub['clevel']:(-1)*$fclub['clevel'];
		$myjifen = $my_win == $f_win ? 0:$myjifen;
		$redis11Model->zIncrBy($cid,$myjifen);
		$fjifen = $f_win == 1 ? $myclub['clevel']:(-1)*$myclub['clevel'];
		$fjifen = $my_win == $f_win ? 0:$fjifen;
		$redis11Model->zIncrBy($fcinfo['fcid'],$fjifen);
		
		//帮会战-查看更多日志
		$Sev56Model = Master::getSev56();
		
		$servcid = $cid;
		$servid = Game::get_sevid_club($cid);
		$name = $Sev51Model->info['cname'];
		$power = $Sev51Model->info['allshili'];
		$fservcid = $fcinfo['fcid'];
		$fservid = Game::get_sevid_club($fcinfo['fcid']);
		$fname = $fSev51Model->info['cname'];
		$fpower = $fSev51Model->info['allshili'];
		if($f_win > 0){
			$servcid = $fcinfo['fcid'];
			$servid = Game::get_sevid_club($fcinfo['fcid']);
			$name = $fSev51Model->info['cname'];
			$power = $fSev51Model->info['allshili'];
			$fpower = $Sev51Model->info['allshili'];
			$fservcid = $cid;
			$fservid = Game::get_sevid_club($cid);
			$fname = $Sev51Model->info['cname'];
		}
		$data = array(
			'cid'   => $servcid,
			'servid' => $servid,
			'name' => $name,
			'power' => $power,
			'fpower' => $fpower,
			'fcid' => $fservcid,
			'fservid' => $fservid,
			'fname' => $fname,
			'win'	=> 1
		);
		//echo $my_win.'==='.$f_win.'==='.$servcid."\n";
		$Sev56Model->add_msg($data);
		
		//伤害排行
		$myhit = $log['myhit'];
		$fhit = $log['fhit'];
		$Sev57Model = Master::getSev57($cid,Game::get_sevid_club($cid));
		$Sev57Model->add($my_win,$myhit,$fcinfo['fcid']);
		
		$fSev57Model = Master::getSev57($fcinfo['fcid'],Game::get_sevid_club($fcinfo['fcid']));
		$fSev57Model->add($f_win,$fhit,$cid);


        unset($mypbuff,$fpbuff,$members,$fmembers,$log,$Sev51Model,$fSev51Model,$Sev53Model,$Sev55Model,$fSev53Model,$fSev55Model,$Sev56Model,$Sev57Model,$fSev57Model);

		echo PHP_EOL, '>>>一个公会PK完', PHP_EOL;
	}
}

function pk($members,$mypbuff,$fmembers,$fpbuff,$myinfo,$myleft,$finfo,$fleft,$mylog,$flog,$lun,$myhit,$fhit){
	
	$my_win = 0; //我方 0:失败 1:获胜
	$f_win = 0;  //敌方 0:失败 1:获胜
	if( !$myleft ){
		//我方获取1个门客
		$myinfo = get_fmember($members,$mypbuff);
		$myleft = $myinfo['left'];
		if($myleft){ //是否有新的门客
			$members = $myinfo['m'];
			$mylog[$lun]['in']['my'] = array(
				'uid' => $myinfo['i']['uid'],  //uid
				'name' => $myinfo['i']['name'], //名字
				'post' => $myinfo['i']['post'], //职位
				'padd' => $mypbuff[$myinfo['i']['post']], //职位加成
				'hid' => $myinfo['i']['heroid'], //门客id
				'use' => $myinfo['i']['jnuse'], //使用锦囊
				'dx' => $myinfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $myinfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $myinfo['i']['hpower'], //战力
				'is_win' => $my_win, // 0:失败   1: 获胜 
				'huihe' => $myinfo['i']['hh'], //连胜回合数
				'out' => 1, // 0:退场  1:继续  
			);
			$flog[$lun]['in']['f'] = array(
				'uid' => $myinfo['i']['uid'],  //uid
				'name' => $myinfo['i']['name'], //名字
				'post' => $myinfo['i']['post'], //职位
				'padd' => $mypbuff[$myinfo['i']['post']], //职位加成
				'hid' => $myinfo['i']['heroid'], //门客id
				'use' => $myinfo['i']['jnuse'], //使用锦囊
				'dx' => $myinfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $myinfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $myinfo['i']['hpower'], //战力
				'is_win' => $my_win, // 0:失败   1: 获胜 
				'huihe' => $myinfo['i']['hh'], //连胜回合数
				'out' => 1, // 0:退场  1:继续  
			);
		}

	}
	
	if( !$fleft ){
		//敌方获取1个门客
		$finfo = get_fmember($fmembers,$fpbuff);
		$fleft = $finfo['left'];  //是否有新的门客
		if($fleft){
			$fmembers = $finfo['m'];
			$mylog[$lun]['in']['f'] = array(
				'uid' => $finfo['i']['uid'],  //uid
				'name' => $finfo['i']['name'], //名字
				'post' => $finfo['i']['post'], //职位
				'padd' => $fpbuff[$finfo['i']['post']], //职位加成
				'hid' => $finfo['i']['heroid'], //门客id
				'use' => $finfo['i']['jnuse'], //使用锦囊
				'dx' => $finfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $finfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $finfo['i']['hpower'], //战力
				'is_win' => $f_win, // 0:失败   1: 获胜 
				'huihe' => $finfo['i']['hh'], //连胜回合数
				'out' => 1, // 0:退场  1:继续  
			);
			$flog[$lun]['in']['my'] = array(
				'uid' => $finfo['i']['uid'],  //uid
				'name' => $finfo['i']['name'], //名字
				'post' => $finfo['i']['post'], //职位
				'padd' => $finfo[$finfo['i']['post']], //职位加成
				'hid' => $finfo['i']['heroid'], //门客id
				'use' => $finfo['i']['jnuse'], //使用锦囊
				'dx' => $finfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $finfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $finfo['i']['hpower'], //战力
				'is_win' => $f_win, // 0:失败   1: 获胜 
				'huihe' => $finfo['i']['hh'], //连胜回合数
				'out' => 1, // 0:退场  1:继续  
			);
		}
		
	}
	
	//如果两边有一边获取不到门客  战斗结束
	if( !$myleft || !$fleft ){
		
		
		//echo $myleft . " =====  ". $fleft."\n";
		if($myleft == $fleft){
			$my_win = empty($mylog[$lun-1]['pk']['my']['is_win'])?0:$mylog[$lun-1]['pk']['my']['is_win'];
			$f_win = empty($mylog[$lun-1]['pk']['f']['is_win'])?0:$mylog[$lun-1]['pk']['f']['is_win'];
		}
		
		
		unset($mylog[$lun]);
		unset($flog[$lun]);
		return array(
			'mylog' => $mylog,
			'flog' => $flog,
			'my_win' => !$myleft ? $my_win: 1,
			'f_win' => !$fleft ? $f_win: 1,
			'myhit' =>  $myhit,
			'fhit' =>  $fhit,
		);
	}
	
	//给对方技能
	if($finfo['jnfunc']['to'] == 1){
		$myinfo['i']['hpower'] = sub_hpower($myinfo['i']['hpower'],$myinfo['i']['ypower'],$finfo['jnfunc']['add']);
	}
	if($myinfo['jnfunc']['to'] == 1){
		$finfo['i']['hpower'] = sub_hpower($finfo['i']['hpower'],$finfo['i']['ypower'],$myinfo['jnfunc']['add']);
	}
	
	//echo $myinfo['i']['uid'].' PK '.$finfo['i']['uid'] ."\n";
	
	
	//我方战力
	$mypwer = $myinfo['i']['hpower'];
	//敌方战力
	$fpwer = $finfo['i']['hpower'];
	
	//判断哪一方离场
	if($mypwer > $fpwer){   //我方胜利
		$fleft = 0;//对方离场
		$finfo['i']['hpower'] = 0;  //敌方血量为0
		$myinfo['i']['hpower'] -= $fpwer;
		$my_win = 1;  //我方获胜
		$myinfo['i']['hh'] += 1; //获胜回合数+1
		$myhit[$myinfo['i']['uid']]['hh'] += 1;
		if($myinfo['i']['hh'] >= $myinfo['i']['jnfunc']['huihe']){ //超过一定回合数离场
			//echo "===退场==";
			$myleft = 0;
			$fhit[$finfo['i']['uid']]['hh'] += 1;
		}
		
		//echo "我方胜利\n";
	}elseif($mypwer < $fpwer){   //敌方胜利
		$myleft = 0;   //我方离场
		$myinfo['i']['hpower'] = 0;   //我方血量为0
		$finfo['i']['hpower'] -= $mypwer;
		$f_win = 1;   //敌方获胜
		$finfo['i']['hh'] += 1;  //敌方胜利回合数+1
		//伤害
		$fhit[$finfo['i']['uid']]['hh'] += 1;
		if($finfo['i']['hh'] >= $finfo['i']['jnfunc']['huihe']){   //超过回合数胜利离场
			//echo "===退场==";
			$fleft = 0;
			//伤害
			$myhit[$myinfo['i']['uid']]['hh'] += 1;
		}
		//echo "敌方胜利\n";
		
	}else{   //平手
		$myinfo['i']['hpower'] = 0;    //我方血量为0
		$finfo['i']['hpower'] = 0;  //敌方血量为0
		$myleft = 0;  //我方离场
		$fleft = 0;   //敌方离场
		//伤害
		$myhit[$myinfo['i']['uid']]['hh'] += 1;
		$fhit[$finfo['i']['uid']]['hh'] += 1;
		//echo "平局\n";
		
	}
	
	
	//伤害
	//if($finfo['i']['jnuse'] != 280){  //过滤击杀的是伏兵
		$myhit[$myinfo['i']['uid']]['hit'] += min($mypwer,$fpwer);
	//}
	//if($myinfo['i']['jnuse'] != 280){
		$fhit[$finfo['i']['uid']]['hit'] += min($mypwer,$fpwer);
	//}
	
	$mylog[$lun]['pk'] = array(
			'my' => array(
				'uid' => $myinfo['i']['uid'],  //uid
				'name' => $myinfo['i']['name'], //名字
				'post' => $myinfo['i']['post'], //职位
				'padd' => $mypbuff[$myinfo['i']['post']], //职位加成
				'hid' => $myinfo['i']['heroid'], //门客id
				'use' => $myinfo['i']['jnuse'], //使用锦囊
				'dx' => $myinfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $myinfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $myinfo['i']['hpower'], //战力
				'is_win' => $my_win, // 0:失败   1: 获胜 
				'huihe' => $myinfo['i']['hh'], //连胜回合数
				'out' => $myleft, // 0:退场  1:继续  
			),
			'f' => array(
				'uid' => $finfo['i']['uid'],  //uid
				'name' => $finfo['i']['name'], //名字
				'post' => $finfo['i']['post'], //职位
				'padd' => $fpbuff[$finfo['i']['post']], //职位加成
				'hid' => $finfo['i']['heroid'], //门客id
				'use' => $finfo['i']['jnuse'], //使用锦囊
				'dx' => $finfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $finfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $finfo['i']['hpower'], //战力
				'is_win' => $f_win, // 0:失败   1: 获胜 
				'huihe' => $finfo['i']['hh'], //连胜回合数
				'out' => $fleft, // 0:退场  1:继续  
			),
	);
	
	$flog[$lun]['pk'] = array(
			'my' => array(
				'uid' => $finfo['i']['uid'],  //uid
				'name' => $finfo['i']['name'], //名字
				'post' => $finfo['i']['post'], //职位
				'padd' => $fpbuff[$finfo['i']['post']], //职位加成
				'hid' => $finfo['i']['heroid'], //门客id
				'use' => $finfo['i']['jnuse'], //使用锦囊
				'dx' => $finfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $finfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $finfo['i']['hpower'], //战力
				'is_win' => $f_win, // 0:失败   1: 获胜 
				'huihe' => $finfo['i']['hh'], //连胜回合数
				'out' => $fleft, // 0:退场  1:继续  
			),
			'f' => array(
				'uid' => $myinfo['i']['uid'],  //uid
				'name' => $myinfo['i']['name'], //名字
				'post' => $myinfo['i']['post'], //职位
				'padd' => $mypbuff[$myinfo['i']['post']], //职位加成
				'hid' => $myinfo['i']['heroid'], //门客id
				'use' => $myinfo['i']['jnuse'], //使用锦囊
				'dx' => $myinfo['i']['jnfunc']['to'], //使用对象   0:自己   1对手
				'add' => $myinfo['i']['jnfunc']['add'], //使用加成  对手减
				'power' => $myinfo['i']['hpower'], //战力
				'is_win' => $my_win, // 0:失败   1: 获胜 
				'huihe' => $myinfo['i']['hh'], //连胜回合数
				'out' => $myleft, // 0:退场  1:继续  
			),
	);
	
	$lun++;
	$ok = pk($members,$mypbuff,$fmembers,$fpbuff,$myinfo,$myleft,$finfo,$fleft,$mylog,$flog,$lun,$myhit,$fhit);
	if($ok){
		return array(
			'mylog' => $ok['mylog'],
			'my_win' =>  $ok['my_win'],
			'myhit' =>  $ok['myhit'],
			'flog' => $ok['flog'],
			'f_win' =>  $ok['f_win'],
			'fhit' =>  $ok['fhit'],
		);
	}
	
}

/**
 * 获取敌方一个玩家门客数据
 * @param $fmembers
 */
function get_fmember($fmembers,$pbuff){
	
	$info = array();
	$left = 0;
	foreach($fmembers as $k => $v){
		
		//还没有pk
		if( $v['ispk'] == 0 ){
			$fmembers[$k]['ispk'] = 1;
			$info = $fmembers[$k];
			$left = 1;
			if($v['jnuse'] == 280){  //是否触发伏兵锦囊  默认未触发
				$info['jnuse'] = 0;
			}
			break;
		}
		//使用伏兵
		if( $v['ispk'] == 1 && $v['jnuse'] == 280 ){
			$fmembers[$k]['ispk'] = 2;
			$info = $fmembers[$k];
			$left = 1;
			
			$info['jnuse'] = $v['jnuse'];
			$info['heroid'] = $info['jnfunc']['heroid'];
			$info['hpower'] = $info['jnfunc']['hpower'];
			$info['hh'] = 0;
			break;
		}
		unset($fmembers[$k]);
	}
	
	
	
	//我方战力
	$hpower = $info['hpower'];  //是不是伏兵
	$add = empty($info['jnfunc']['to'])?$info['jnfunc']['add']:0;  
	$pwer = add_allpower($hpower,$pbuff[$info['post']],$add);
	$info['ypower'] = $hpower;
	$info['hpower'] = empty($pwer)?$info['hpower']:$pwer;
	$info['hit'] = 0;
	return array(
		'm' => $fmembers,   //公会数据
		'i'	=> $info,  //公会成员玩家key
		'left' => $left,
	);
}

/**
 * 获取pk门客总战力
 * @param $hpower   门客战力
 * @param $padd    职位加成
 * @param $add     锦囊加成
 * @param $sub     对手锦囊减成
 */
function add_allpower($hpower,$padd,$add){
	$hpower += intval($hpower * ($padd + $add)/100);
	return $hpower;
}

/**
 * 计算势力
 * @param $allpower  总势力
 * @param $hpower    基础势力
 * @param $sub       减的倍数
 */
function sub_hpower($allpower,$hpower,$sub){
	
	$allpower -= intval($hpower * $sub/100);
	
	$allpower = max($allpower,1);
	
	return $allpower;
}

/**
 * 伤害列表初始化
 */
function reset_hit($members){
	$hit = array();
	foreach($members as $k => $v){
		$hit[$v['uid']] = array(
			'hh' => 0,
			'name' => $v['name'],
			'hit' => 0,
		);
	}
	return $hit;
}










