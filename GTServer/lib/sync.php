<?php
class Sync
{
	private static $key1='sync_updateseed';//有多少笔
	private static $key2='sync_updatetime';//这个时间点
	private static $key3='sync_time'; //     更新时间戳 

	public static function toBeSync($id,$type='')//10087_Universe  1_Universe
	{
		$cache = Common::getDftMem();
		$updateseed = $cache->increment(Sync::$key1.'_'.$type,1); // 计数sync_updateseed_1_user
		$updatetime = $cache->get(Sync::$key2.'_'.$type);// 以分钟为间隔//上/这次更新时间?
		
		if(!$updateseed || !$updatetime)//初始化计数器
		{
			$updatetime = date('Hi',time());//分钟
			$cache->set(Sync::$key1.'_'.$type,1);
			$cache->set(Sync::$key2.'_'.$type, $updatetime);
			$cache->set(Sync::$key3.'_'.$type, time());
		}
		
		$key = "{$updatetime}_{$updateseed}_{$type}";//1521_1_1_user  /时间点_计数器_类 =>类的KEY
		$cache->set($key,$id,300);
	}

	public static function doSync($type, $crontabName = 'doSync')
	{
		$start = time();
		echo '>>>start:' . $start . ', ';
		Game::crontab_debug('>>>start:' . $start . ', ', $crontabName);

		$cache = Common::getDftMem();
		$newUpdatetime = date('Hi',time());//分钟
		$updateseed = intval($cache->get(Sync::$key1.'_'.$type));//这个类有几笔
		$updatetime = $cache->get(Sync::$key2.'_'.$type);//上次更新时间点
		$oldtime    = $cache->get(Sync::$key3.'_'.$type);//这次更新时间点

		echo 'key:' . Sync::$key1 . '_' . $type . ', ';
		Game::crontab_debug('key:' . Sync::$key1 . '_' . $type . ', ', $crontabName);
		
		$cache->set(Sync::$key1.'_'.$type, 0);//计数清O
		$cache->set(Sync::$key2.'_'.$type, $newUpdatetime);//时间点更新为这次
		$cache->set(Sync::$key3.'_'.$type, $start);//时间戳
		
		echo 'count:' . $updateseed . ', ';
		Game::crontab_debug('count:' . $updateseed . ', ', $crontabName);

		$info = array();
		for($i=1 ; $i<=$updateseed ; $i++)
		{
			$key = "{$updatetime}_{$i}_{$type}";//类的KEY
			$skey = $cache->get($key);//数量
			if ($skey === false) continue;
			list($uid, $datatype, $t) = explode('_', $skey);//这里的 $datatype 会是同一个
			$info[$skey] = empty($t) ? 1 : $t;//去重复
		}
		
		if($info)//[10089_user] => 1，活动是[10089_act_1] => 1
		{
            $models = array(
                'user' => 'UserModel',
                'item' => 'ItemModel',
                'hero' => 'HeroModel',
                'son'  => 'SonModel',
                'wife' => 'WifeModel',
                'act'  => 'ActModel',
				'mail' => 'MailModel',
				'card' => 'CardModel',
				'baowu' => 'BaowuModel',
            );
			$model = $models[$datatype];//根据KEY得出类名
			if (empty($model)) {
				echo 'LIB_SYNC_ERROR:' . $datatype . ', ';
				Game::crontab_debug('LIB_SYNC_ERROR:' . $datatype . ', ', $crontabName);
				return false;
			}
			
			$i = $j = 0;
			Common::loadModel($model);
			
			//锁类型
			Common::loadLockModel("MyLockModel");
			foreach($info as $k=>$v)//$k 就是 每个mod 的key  如 10086_user
			{
				list($uid, $datatype) = explode('_', $k);
				//加锁 uid 用户 锁 
				/*
				 * "user_".$uid
				 */
				$LockModel = new MyLockModel("user_".$uid);
				$uid_Lock = $LockModel->getLock();
				if (empty($uid_Lock)) {
					//跳过这个用户 (写日志?)
					echo "busy_".$uid."\n";
					Game::crontab_debug("busy_".$uid."\n", $crontabName);
					continue;
				}
				
				$Model = $datatype == 'act' ? new $model($uid, $v) : new $model($uid);
				switch ($model)
				{
                    case 'UserModel':
                    case 'ItemModel':
                    case 'HeroModel':
                    case 'SonModel':
                    case 'WifeModel':
                    case 'ActModel':
					case 'MailModel':
					case 'CardModel':
					case 'BaowuModel':
						if($Model->info){
							$j += $Model->sync();
							$cache->set($Model->getKey(),$Model->info);
							$i++;
						}
						break;
					default:
						break;
				}
				//解UID锁
				if( null != $uid_Lock ){
					$LockModel->releaseLock();
				}
			}
			echo "{$model}: {$i} {$j}, ";
			Game::crontab_debug("{$model}: {$i} {$j}, ", $crontabName);
		}
		echo 'time:' . (time()-$start) . PHP_EOL;
		Game::crontab_debug('time:' . (time()-$start), $crontabName);
		return $updateseed;
	}
}