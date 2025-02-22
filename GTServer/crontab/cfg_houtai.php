<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

if (defined("SWITCH_GAME_ACT_FROM_DB") && SWITCH_GAME_ACT_FROM_DB) {
	Common::loadModel('GameActModel');
	$GameActModel = new GameActModel();
	$gameActList = $GameActModel->getAllInfo(GameActModel::AUDIT_PASS);
	ksort($gameActList);
}

if ( is_array($serverList) ) {

	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

		echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
			//echo PHP_EOL, '>>>跳过', PHP_EOL;
			//continue;
		}
		if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}

		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}

		$open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
		//过滤未开服的
		if($open_day <= 0){
			// continue;
		}
		echo '生效时间'.$open_day."\n";

		if (defined("SWITCH_GAME_ACT_FROM_DB") && SWITCH_GAME_ACT_FROM_DB) {
			//数据库
			do_hd_list_from_db($gameActList, $Sev_Cfg, $open_day);
		}
		else {
			//文件
			$list = array(); //新服活动 + 通服活动的生效列表 'huodong_1' => 1,//
			$list = do_hd_new($list);   //新服简单的生效列表 'huodong_1' => 3,
			$list = do_hd_all($list);   //新服+通服简单的生效列表
			$dlist = do_hd_base($list); //本服简单的生效列表

			do_hd_list($dlist);  //本服生效详细信息
		}

	}
}

if (defined("SWITCH_GAME_ACT_FROM_DB") && SWITCH_GAME_ACT_FROM_DB) {
	GameActModel::setLastChangeVer(time());

	//预览计算
	if ( is_array($serverList) ) {
		//把时间改为预览时间
		Common::loadModel('GameActViewModel');
		$_SERVER['REQUEST_TIME'] = GameActViewModel::getNewTime();

		reset($serverList);
		foreach ($serverList as $k => $v) {
			if ( empty($v) ) {continue;}
			$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

			echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

			if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
				echo PHP_EOL, '>>>跳过', PHP_EOL;
				continue;
			}
			if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
				echo PHP_EOL, '>>>跳过', PHP_EOL;
				continue;
			}
			if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
				&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
				echo PHP_EOL, '>>>从服跳过', PHP_EOL;
				continue;
			}
			$open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
			//过滤未开服的
			if($open_day <= 0){ continue; }
			echo '生效时间'.$open_day."\n";
			reset($gameActList);
			do_hd_list_from_db($gameActList, $Sev_Cfg, $open_day, true);
		}
	}
}

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();


/**
 * 新服简单的生效列表
 * @param $list
 */
function do_hd_new($list){
	global $Sev_Cfg;
	global $open_day;

	//获取【新服活动】【所有活动】【所有档次】配置
	$data = HoutaiModel::get_new_hd();
	if(empty($data)){
		return $list;
	}

	//遍历【所有活动】
	foreach($data as $key => $value){
		//遍历【单个活动】的【所有档次】获取【生效档次】
		$info = check_huodong($value);
		//不是生效活动过滤
		if(empty($info)){
			continue;
		}
		//存储通服生效列表信息
		$list[$key] = 3;
	}

	return $list;
}


/**
 * 新服+通服简单的生效列表
 * @param $list
 */
function do_hd_all($list){

	global $Sev_Cfg;
	global $open_day;

	//获取 【通服】【所有活动】【所有档次】配置
	$data = HoutaiModel::get_all_hd();
	if(empty($data)){
		return $list;
	}

	//遍历【所有活动】
	foreach($data as $key => $value){
		//如果这个活动 在【新服生效列表】里面没有
		if( !empty($list[$key]) && $list[$key] == 3 ){
			continue;
		}
		//取出 【档次列表生效的档次】
		$info = check_huodong($value);
		//如果没有生效列表 过滤
		if(empty($info)){
			continue;
		}
		//存储通服生效列表信息
		$list[$key] = 2;
	}

	return $list;
}


/**
 * 新服+通服 + 本服 简单的生效列表
 * @param $list
 */
function do_hd_base($list){
	global $Sev_Cfg;
	global $open_day;

	//获取 【本服】【所有活动】【所有档次】列表
	$data = HoutaiModel::get_base_hd();
	if(empty($data)){
		return $list;
	}
	//遍历【所有活动】
	foreach($data as $key => $value){
		//获取【生效档次】
		$info = check_huodong($value);
		//如果没有生效列表 过滤
		if(empty($info)){
			continue;
		}
		//存储本服生效列表信息
		$list[$key] = 1;
	}
	return $list;

}

/**
 * 获取活动生效详细信息
 */
function do_hd_list($dlist){
	global $Sev_Cfg;
	global $open_day;
	$cache 	= Common::getCacheBySevId($Sev_Cfg['sevid']);

	//$type  1:单区   2:通服   3:新服
	foreach($dlist as $key => $type){
		switch($type){
			case 1:  //本服获取
				$info = HoutaiModel::get_base_hd($key);//获取单个活动的 所有档次
				$info = check_huodong($info);//获取生效档次
				break;
			case 2:  //通服获取
				$info = HoutaiModel::get_all_hd($key);
				$info = check_huodong($info);
				break;
			case 3:  //新服获取
				$info = HoutaiModel::get_new_hd($key);
				$info = check_huodong($info);
				break;
		}
		//生成后端方便读取格式
		$data = create_cfg($key,$info);

		//处理info各种时间
		$dataInfo = $data['info'];
        $dataInfo['hid'] = $dataInfo['id'];
		$dataInfo['id'] = $dataInfo['no'];
		$dataInfo['cd'] = array(  //倒计时
			'next' => $dataInfo['eTime'],
			'label' => $key.'_ltime',
		);
		unset($dataInfo['startDay']);
		unset($dataInfo['endDay']);
		unset($dataInfo['startTime']);
		unset($dataInfo['endTime']);
		unset($dataInfo['no']);
		$huodong_list[$key] = $dataInfo;

		//比较两个字符串
		$hd_key = 'benfu_'.$Sev_Cfg['sevid'].'_'.$key;
		$get_info 	= $cache->get($hd_key);
		if(!empty($get_info) && json_encode($data) == json_encode($get_info) ){
			continue;
		}
		huodong_ver();
		$cache->set($hd_key,$data);
	}

	//存储单服生效列表
	$base_list = 'hd_base_list_'.$Sev_Cfg['sevid'];
	$blist 	= $cache->get($base_list);
	if(json_encode($dlist) != json_encode($blist)){
		//添加一个版本缓存id
		huodong_ver();
		$cache->set($base_list,$dlist);
	}

	//存放本服活动生效简单列表
	if($huodong_list != $cache->get('huodong_list_'.$Sev_Cfg['sevid'])){
		$cache->set('huodong_list_'.$Sev_Cfg['sevid'],$huodong_list);
		huodong_ver();
	}
}

/*
 * 更新活动版本
 */
function huodong_ver(){
	global $Sev_Cfg;

	$cache 	= Common::getCacheBySevId($Sev_Cfg['sevid']);
	//添加一个版本缓存id
	$base_ver = 'hd_base_ver_'.$Sev_Cfg['sevid'];
	$ver 	= $cache->get($base_ver);
	if(empty($ver['ver']) || $ver['ver'] > 1000000){
		$ver['ver'] = 1;
	}
	$ver['ver'] = $ver['ver']+1;
	$cache->set($base_ver,$ver);

	echo $base_ver.'版本:   '.$ver['ver']."\n";
}

/**
 * 构造一个函数
 * 单个活动输入
 * 如果活动不生效  return 0
 * 否则 返回生效的档次构造信息
 */
function check_huodong($hdInfo, $gameActID = 1){
	global $Sev_Cfg;
	global $open_day;
	
	if(empty($hdInfo)){
		return 0;
	}

	//将字符串转数组
	foreach($hdInfo as $info){
	    //自动轮回设置
        $autoDay = isset($info['info']['autoDay']) ? intval($info['info']['autoDay']) : 0;
        if ($autoDay > 0) {
            $autoNum = isset($info['info']['autoNum']) ? intval($info['info']['autoNum']) : 999;
            for ($i=0; $i<=$autoNum; $i++) {
                $autoReal = $autoDay * $i;
                $inDay = !empty($info['info']['startDay']) && !empty($info['info']['endDay'])
                    && $open_day >= ($info['info']['startDay'] + $autoReal)
                    && $open_day <= ($info['info']['endDay'] + $autoReal);
                if ($inDay) {
                    if($info['info']['id'] != 'day'){
                        $info['info']['id'] += $autoReal;
                    }
                    $info['info']['startDay'] += $autoReal;
                    $info['info']['endDay'] += $autoReal;
                    break;
                }
                $inTime = !empty($info['info']['startTime']) && !empty($info['info']['endTime'])
                    && $_SERVER['REQUEST_TIME'] >= (strtotime($info['info']['startTime']) + $autoReal * 86400)
                    && $_SERVER['REQUEST_TIME'] <= (strtotime($info['info']['endTime']) + $autoReal * 86400);
                if ($inTime) {
                    if($info['info']['id'] != 'day') {
                        $info['info']['id'] += $autoReal;
                    }
                    $info['info']['startTime'] = date("Y-m-d H:i:s", (strtotime($info['info']['startTime']) + $autoReal * 86400));
                    $info['info']['endTime'] = date("Y-m-d H:i:s", (strtotime($info['info']['endTime']) + $autoReal * 86400));
                    break;
                }
            }
        }
        //自动轮回设置end


		//每天重置活动
		if($info['info']['id'] == 'day') {
            $info['info']['id'] = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        }

	    $showDay = !empty($info['info']['showDay'])?$info['info']['showDay']:$info['info']['endDay'];
		//开服时间
		if(  !empty($info['info']['startDay']) && !empty($info['info']['endDay'])
			&& $open_day >= $info['info']['startDay'] && $open_day <= $showDay ){

			$todayt = Game::day_0();  //今天0点的时间戳
			//活动开始时间戳
			$info['info']['sTime'] = $todayt - ($open_day-$info['info']['startDay']) * 86400;
			//活动结束时间戳
			$info['info']['eTime'] = $todayt + ($info['info']['endDay']-$open_day+1) * 86400-1;
			//展示结束时间
			$info['info']['showTime'] = $todayt + ($showDay-$open_day+1) * 86400-1;
			switch ($info['info']['type']){
                case 3:
                case 7:
                case 8:
                case 15:
                case 18:
                case 6123:
                case 6142:
                case 6221:
                case 6222:
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay']-$open_day+1) * 86400 - 7200;
                    break;
                case 16:
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay']-$open_day) * 86400;
                    break;
                case 19:
                case 22:
                case 6227:
                case 6229:
                case 6230:
                case 6232:
                case 6234:
                case 6241:
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay']-$open_day) * 86400 - 7200;
                    break;
                case 11:
                case 13:
                    $info['info']['eTime'] = $info['info']['eTime'] - 24 * 60 * 60 ;
                    break;
                case 6187:
                case 6200:
                case 6201:
                case 6202:
                case 6203:
                case 6204:
                case 6205:
                case 6206:
                case 6207:
                case 6208:
                    $info['info']['eTime'] = $todayt + ($info['info']['endDay']-$open_day+1) * 86400 - 86400;
                    break;
            }

			if($info['info']['type'] == 9){//9:跨服活动
				static $max_sevid = array();
				if(!isset($max_sevid[$gameActID])){
					$max_sevid[$gameActID] = 0;
					$serverList9 = ServerModel::getServList();
					$sevid_arr = array();//记录下每个合服的最大sevid
					foreach ($serverList9 as $k9 => $v9) {
						if ( empty($v9) ) {
							continue;
						}
						if($v9['id'] == 999) continue;
						if(!empty($info['server'])){
							foreach ($info['server'] as $val){
								$state = 0;
								if($v9['id'] >= $val['mi'] && $v9['id'] <= $val['ma']){
									$state = 1;
									break;
								}
							}
							if(empty($state)) continue;
						}

						$open_day9 = ServerModel::isOpen($v9['id']);
						if($open_day9 >= $info['day']){
							$SevCfgObj = Common::getSevCfgObj($v9['id']);//子服ID
							$he = $SevCfgObj->getHE();
							if(empty($sevid_arr[$he]) || $sevid_arr[$he] < $v9['id']){
								$sevid_arr[$he] = $v9['id'];
							}
						}
					}
					$max_sevid[$gameActID] = max($sevid_arr);
				}
				if($Sev_Cfg['sevid'] > $max_sevid[$gameActID] ){
					return 0;
				}
				if(empty($info['yu_day'])) $info['yu_day'] = 2;
				$info['info']['max_rank'] = $max_sevid[$gameActID];
				$info['info']['yueTime'] = $info['info']['sTime'] + $info['yu_day']*24*3600-7200;//预选赛持续46小时
				$info['info']['yushowTime'] = $info['info']['yueTime']+7200;
				$info['info']['eTime'] = $todayt + ($info['info']['endDay']-$open_day9+1) * 86400 - 1 - 26*3600;
				$info['info']['showTime'] = $todayt + ($info['info']['endDay']-$open_day9+1) * 86400-1;
			}

			unset($info['info']['startDay']);
			unset($info['info']['endDay']);
            unset($info['info']['showDay']);
			unset($info['info']['startTime']);
			unset($info['info']['endTime']);
            unset($info['info']['shTime']);
            unset($info['info']['autoDay'], $info['info']['autoNum']);


			return $info;
		}

		$showTime = !empty($info['info']['shTime'])? strtotime($info['info']['shTime']):strtotime($info['info']['endTime']);

		$isOldServer = true;
		if (!empty($info['info']['oldDay']) && $open_day < $info['info']['oldDay']) {
			$isOldServer = false;
		}

		//固定日期开始时间内
		if(  !empty($info['info']['startTime']) && !empty($info['info']['endTime'])
			&& $_SERVER['REQUEST_TIME'] >= strtotime($info['info']['startTime'])
			&& $_SERVER['REQUEST_TIME'] <= $showTime && $isOldServer ){

			//活动开始时间戳
			$info['info']['sTime'] = strtotime($info['info']['startTime']);
			//活动结束时间戳
			$info['info']['eTime'] = strtotime($info['info']['endTime'])-1;
            //展示结束时间
            $info['info']['showTime'] = $showTime-1;
            switch ($info['info']['type']){
                case 3:
                case 7:
                case 8:
                case 15:
                case 18:
                case 6123:
                case 6142:
                case 6221:
                case 6222:
                    $info['info']['eTime'] = strtotime($info['info']['endTime'])-7200+1;
                    break;
                case 16:
                    $info['info']['eTime'] = strtotime($info['info']['endTime'])-86400+1;
                    break;
                case 19:
                case 22:
                case 6227:
                case 6229:
                case 6230:
                case 6232:
                case 6234:
                case 6241:
                    $info['info']['eTime'] = strtotime($info['info']['endTime'])-86400-7200+1;
                    break;
                case 11:
                case 13:
                    $info['info']['eTime'] = $info['info']['eTime'] - 24 * 60 * 60 ;
                    break;
                case 6187:
                case 6200:
                case 6201:
                case 6202:
                case 6203:
                case 6204:
                case 6205:
                case 6206:
                case 6207:
                case 6208:
                    $info['info']['eTime'] = strtotime($info['info']['endTime'])-86400+1;
                    break;
            }
			
			if($info['info']['type'] == 9){//9:跨服活动
				static $max_sevid = array();
				if(!isset($max_sevid[$gameActID])){
					$max_sevid[$gameActID] = 0;
					$serverList9 = ServerModel::getServList();
					$sevid_arr = array();//记录下每个合服的最大sevid
					foreach ($serverList9 as $k9 => $v9) {
						if ( empty($v9) ) {
							continue;
						}
						if($v9['id'] == 999) continue;
						if(!empty($info['server'])){
							foreach ($info['server'] as $val){
								$state = 0;
								if($v9['id'] >= $val['mi'] && $v9['id'] <= $val['ma']){
									$state = 1;
									break;
								}
							}
							if(empty($state)) continue;
						}

						$open_day9 = ServerModel::isOpen($v9['id']);
						if($open_day9 >= $info['day']){
							$SevCfgObj = Common::getSevCfgObj($v9['id']);//子服ID
							$he = $SevCfgObj->getHE();
							if(empty($sevid_arr[$he]) || $sevid_arr[$he] < $v9['id']){
								$sevid_arr[$he] = $v9['id'];
							}
						}
					}
					$max_sevid[$gameActID] = max($sevid_arr);
				}
				if($Sev_Cfg['sevid'] > $max_sevid[$gameActID] ){
					return 0;
				}
				$info['info']['max_rank'] = $max_sevid[$gameActID];//可匹配的最高区服
				if(empty($info['yu_day'])) $info['yu_day'] = 2;

				//正式
				$info['info']['yueTime'] = strtotime($info['info']['startTime'])+$info['yu_day']*24*3600-7200;
				$info['info']['yushowTime'] = $info['info']['yueTime']+7200;
				$info['info']['eTime'] = strtotime($info['info']['endTime'])-26*3600+1;
				$info['info']['showTime'] = strtotime($info['info']['endTime'])+1;
			}
			unset($info['info']['startDay']);
			unset($info['info']['endDay']);
            unset($info['info']['showDay']);
			unset($info['info']['startTime']);
			unset($info['info']['endTime']);
            unset($info['info']['shTime']);
            unset($info['info']['autoDay'], $info['info']['autoNum']);


			return $info;
		}

	}

	return 0;
}

/**
 * 生成后端方便可用的配置
 * @param $key 活动key
 * @param $info  活动配置信息
 */
function create_cfg($key,$info){
	$str = explode('huodong_',$key);
	$info['info']['no'] = intval($str[1]);
	switch($key){
		case 'huodong_201':
		case 'huodong_202':
		case 'huodong_203':
		case 'huodong_204':
		case 'huodong_205':
		case 'huodong_206':
		case 'huodong_207':
		case 'huodong_208':
		case 'huodong_209':
		case 'huodong_210':
		case 'huodong_211':
		case 'huodong_212':
		case 'huodong_213':
		case 'huodong_214':
		case 'huodong_215':
		case 'huodong_216':
		case 'huodong_217':
		case 'huodong_218':
		case 'huodong_219':
		case 'huodong_220':
		case 'huodong_221':
		case 'huodong_222':
		case 'huodong_223':
		case 'huodong_224':
		case 'huodong_225':
        case 'huodong_226':

		case 'huodong_260':
		case 'huodong_261':
		case 'huodong_262':
        case 'huodong_6139':
        case 'huodong_6170':
        case 'huodong_6171':
        case 'huodong_6172':
        case 'huodong_6173':
        case 'huodong_6174':
        case 'huodong_6175':
        case 'huodong_6176':
        case 'huodong_6177':
        case 'huodong_6178':
        case 'huodong_6179':
        case 'huodong_6186':
        case 'huodong_6212':
        case 'huodong_6213':
        case 'huodong_6215':
        case 'huodong_6216':
        case 'huodong_6217':

			$info['brwd'] = Game::get_key2id($info['rwd'],'id');
			break;
	}
	return $info;
}

/**
 * 从数据库里读取活动配置
 * @param $gameActList
 * @param $Sev_Cfg
 * @param $open_day
 */
function do_hd_list_from_db($gameActList, $Sev_Cfg, $open_day, $newTime = false) {
	$actKeyList = array();
	//同服走一轮
	foreach ($gameActList as $v) {
		$server = $v['server'];
		if (empty($server) || $server != 'all') {
			continue;
		}
		$info = check_huodong($v['contentsArr'], $v['id']);
		if (empty($info)) {
			continue;
		}
		$actKey = $v['act_key'];
		$actKeyList[$actKey] = array('id'=>$v['id'], 's'=>$server);
	}

	//非通服走一轮
	foreach ($gameActList as $v) {
		$server = $v['server'];
		if (empty($server) || $server == 'all') {
			continue;
		}
		$serverList = Game::serves_str_arr($server);
		if (!in_array($Sev_Cfg['sevid'], $serverList)) {
			continue;
		}
		$info = check_huodong($v['contentsArr'], $v['id']);
		if (empty($info)) {
			continue;
		}
		$actKey = $v['act_key'];
		$actKeyList[$actKey] = array('id'=>$v['id'], 's'=>$server);
	}
	//获取详细信息
	$actInfoList = array();
	$actKeyInfoList = array();
	foreach ($actKeyList as $actKey => $actKeyList_v) {
		if (!isset($gameActList[$actKeyList_v['id']])) {
			continue;
		}
		$v = $gameActList[$actKeyList_v['id']];
		$info = check_huodong($v['contentsArr'], $v['id']);
		if (empty($info)) {
			continue;
		}
		$data = create_cfg($actKey, $info);
		//处理info各种时间
		$dataInfo = $data['info'];
        $dataInfo['hid'] = $dataInfo['id'];
		$dataInfo['id'] = $dataInfo['no'];
		$dataInfo['cd'] = array(//倒计时
			'next' => $dataInfo['eTime'],
			'label' => $actKey.'_ltime',
		);
		unset($dataInfo['startDay']);
		unset($dataInfo['endDay']);
		unset($dataInfo['startTime']);
		unset($dataInfo['endTime']);
		unset($dataInfo['no']);
		$actInfoList[$actKey] = $dataInfo;
		$actKeyInfoList[$actKey] = $data;
	}
	//存储到本服
	$cache 	= Common::getCacheBySevId($Sev_Cfg['he']);
	if ($newTime) {
		$cache->set('hd_base_list_new_time_'.$Sev_Cfg['sevid'], $actKeyList);
		$cache->set('huodong_list_new_time_'.$Sev_Cfg['sevid'], $actInfoList);
		foreach ($actKeyInfoList as $actKey => $data) {
			$cache->set('benfu_new_time_'.$Sev_Cfg['sevid'].'_'.$actKey, $data);
		}
	} else {
		$cache->set('hd_base_list_'.$Sev_Cfg['sevid'], $actKeyList);
		$cache->set('huodong_list_'.$Sev_Cfg['sevid'], $actInfoList);
		foreach ($actKeyInfoList as $actKey => $data) {
			$cache->set('benfu_'.$Sev_Cfg['sevid'].'_'.$actKey, $data);
		}
	}
}