<?php
//后台配置
class HoutaiModel
{
	
	/**
	 * 只供后台使用  --- 单服活动写入
	 * @param $key
	 * @param $value
	 */
	static public function write_base_hd($key,$value)
	{
		$SevidCfg = Common::getSevidCfg();
		if (!is_dir(CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/')){
			mkdir(CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/',0777,true);
		}
		$dir_file = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/'. $key . '.php';//需要包含的文件
		$file = fopen($dir_file,'w+');
		fwrite($file, $value);
		@chmod($dir_file,0777);
		fclose($file);
        
		return true;
	}
	
	/**
	 * 只供后台使用  --- 单服活动读取
	 */
	static public function read_base_hd($onefile = '')
	{
		$SevidCfg = Common::getSevidCfg();
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/';    //文件路径
		if(is_dir($dir)){
			$file=scandir($dir);   //获取所有文件名称
		}
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 只供后台使用  --- 删除 单服活动
	 * @param $key  :文件名字
	 */
	static public function del_base_hd($key)
	{
		$SevidCfg = Common::getSevidCfg();
		$dir_file = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/' . $key . '.php';//需要包含的文件
		if(unlink($dir_file)){
			return true;
		}
		return false;
	}
	
	
	
	//*********************************************************************//
	/**
	 * 只供后台使用  --- 通服活动写入
	 * @param $key
	 * @param $value
	 */
	static public function write_all_hd($key,$value)
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/hdall/' . $key . '.php';//需要包含的文件
		$file = fopen($dir_file,'w');
		fwrite($file, $value);
		@chmod($dir_file,0777);
		fclose($file);

		return true;
	}
	
	//*********************************************************************//
	/**
	 * 只供后台使用  --- 新服活动
	 * @param $key
	 * @param $value
	 */
	static public function write_new_hd($key,$value)
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/hdnew/' . $key . '.php';//需要包含的文件
		$file = fopen($dir_file,'w');
		fwrite($file, $value);
		@chmod($dir_file,0777);
		fclose($file);

		return true;
	}
	
	/**
	 * 只供后台使用  --- 通服活动读取
	 */
	static public function read_all_hd($onefile = '')
	{
		
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/hdall/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/hdall/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			
			$dir_file = CONFIG_DIR . '/houtaicfg/hdall/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 只供后台使用  --- 通服活动读取
	 */
	static public function read_new_hd($onefile = '')
	{
		
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/hdnew/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/hdnew/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			
			$dir_file = CONFIG_DIR . '/houtaicfg/hdnew/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 只供后台使用  --- 删除 通服活动
	 * @param $key  :文件名字
	 */
	static public function del_all_hd($key)
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/hdall/' . $key . '.php';//需要包含的文件
		if(unlink($dir_file)){
			return true;
		}
		return false;
	}
	
	
	
	//*********************************************************************//
	/**
	 * 只供后台使用  --- 基础配置写入
	 * @param $key
	 * @param $value
	 */
	static public function write_base_peizhi($key,$value)
	{
		$SevidCfg = Common::getSevidCfg();
		if (!is_dir(CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/')){
			mkdir(CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/',0777,true);
		}
		$dir_file = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/' . $key . '.php';//需要包含的文件
		$file = fopen($dir_file,'w');
		fwrite($file, $value);
		@chmod($dir_file,0777);
		fclose($file);

		return true;
	}
	
	/**
	 * 只供后台使用  --- 基础配置读取
	 */
	static public function read_base_peizhi($onefile = '')
	{
		$SevidCfg = Common::getSevidCfg();
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/';    //文件路径
		if(is_dir($dir)){
			$file=scandir($dir);   //获取所有文件名称
		}
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 只供后台使用  --- 删除 基础配置
	 * @param $key  :文件名字
	 */
	static public function del_base_peizhi($key)
	{
		$SevidCfg = Common::getSevidCfg();
		$dir_file = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/' . $key . '.php';//需要包含的文件
		if(unlink($dir_file)){
			return true;
		}
		return false;
	}
	
	
	
	
	//*********************************************************************//
	/**
	 * 只供后台使用  --- 通服基础配置写入
	 * @param $key
	 * @param $value
	 */
	static public function write_all_peizhi($key,$value)
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $key . '.php';//需要包含的文件
		$file = fopen($dir_file,'w');
		fwrite($file, $value);
		@chmod($dir_file,0777);
		fclose($file);

		return true;
	}
	
	
	/**
	 * 只供后台使用  --- 通服基础配置读取
	 */
	static public function read_all_peizhi($onefile = '')
	{
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			return $value;
		}
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/pzall/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}

	
	/**
	 * 只供后台使用  --- 删除 通服基础配置
	 * @param $key  :文件名字
	 */
	static public function del_all_peizhi($key)
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $key . '.php';//需要包含的文件
		if(unlink($dir_file)){
			return true;
		}
		return false;
	}
	
//*********************************************************************//
	/**
	 * 服务器写入
	 * @param $key
	 * @param $value
	 */
	static public function write_servers($value)
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/servers/serverList.php';//需要包含的文件
		$file = fopen($dir_file,'w');
		fwrite($file, "<?php\nreturn " . var_export($value,1) . ';');
		@chmod($dir_file,0777);
		fclose($file);

		return true;
	}
	
	
	/**
	 * 服务器读取
	 */
	static public function read_servers()
	{
		$dir_file = CONFIG_DIR . '/houtaicfg/servers/serverList.php';//需要包含的文件
		//过滤不能读取
		if (!file_exists($dir_file)){
	        return array();
	    }
		//获取value
		$value = include($dir_file);  //读取新配置
		return $value;
		
	}
	
	
	
	
	
	//********************************************************************
	/**
	 * 单服活动读取 -- 读取是数组
	 */
	static public function get_base_hd($onefile = '')
	{
		$SevidCfg = Common::getSevidCfg();
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			$value = @eval('return ' . $value . ';');
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/';    //文件路径
		if(is_dir($dir)){
			$file=scandir($dir);   //获取所有文件名称
		}
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/hdbase/'.$SevidCfg['sevid'].'/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$value = @eval('return ' . $value . ';');
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 通服活动读取-- 读取是数组
	 */
	static public function get_all_hd($onefile = '')
	{
		 
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/hdall/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			$value = @eval('return ' . $value . ';');
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/hdall/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/hdall/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			
			//保存活动
			$value = @eval('return ' . $value . ';');
			
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 新服活动读取-- 读取是数组
	 */
	static public function get_new_hd($onefile = '')
	{
		 
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/hdnew/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			$value = @eval('return ' . $value . ';');
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/hdnew/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/hdnew/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			
			//保存活动
			$value = @eval('return ' . $value . ';');
			
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	/**
	 * 单服基础配置读取 -- 读取是数组
	 */
	static public function get_base_pz($onefile = '')
	{
		//return array();
		$SevidCfg = Common::getSevidCfg();
		//读取单个配置文件
		if(!empty($onefile)){
			
			$dir_file = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			$value = @eval('return ' . $value . ';');
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/';    //文件路径
		if(is_dir($dir)){
			$file=scandir($dir);   //获取所有文件名称
		}
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/pzbase/'.$SevidCfg['sevid'].'/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$value = @eval('return ' . $value . ';');
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	
	/**
	 * 通服基础配置读取-- 读取是数组
	 */
	static public function get_all_pz($onefile = '')
	{
		
		//读取单个配置文件
		if(!empty($onefile)){
			$dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $onefile.'.php';//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        return array();
		    }
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			$value = @eval('return ' . $value . ';');
			return $value;
		}
		
		
		$base_hd = array();
		
		$dir = CONFIG_DIR . '/houtaicfg/pzall/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $base_hd;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			$dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
		    //获取key
			$key = str_replace('.php','',$name);
			//获取value
			$value = file_get_contents($dir_file);  //读取新配置
			//保存活动
			$value = @eval('return ' . $value . ';');
			$base_hd[$key] = $value;
		}
		return $base_hd;
		
	}
	
	
	/**
	 * *************************************************************************
	 * 获取个人生效活动列表      1.先获取本服  2.在转化为个人
	 */
	static public function get_huodong_list($uid,$key='')
	{
		$SevidCfg = Common::getSevidCfg();
		$cache 	= Common::getDftMem();
		//存放活动列表
		$huodong_list = $cache->get(HoutaiModel::get_huodong_list_key($SevidCfg['he']));
		$all_list = array();  //存放个人活动信息列表
		if(empty($huodong_list)){
			return $all_list;
		}
		//转化成个人的活动生效列表
		$HuodongModel = Master::getHuodong($uid);
		if(!empty($key)){
			//单个活动种类更新
			$huodong_list[$key]['news'] = $HuodongModel->huodong_news($huodong_list[$key]['id']);
	   		$all_list[] = $huodong_list[$key];
		}else{
			//所有活动种类更新
			foreach($huodong_list as $key => $hlist){
		   		$hlist['news'] = $HuodongModel->huodong_news($hlist['id']);
		   		$all_list[] = $hlist;
			}
		}
		return $all_list;
	}
	
	static public function get_huodong_info($key)
	{
		static $sxList = array(); //静态生效活动列表
		
		//服务器ID
		$SevidCfg = Common::getSevidCfg();
		
		if( empty($sxList[$SevidCfg['he']]) ){
			$cache 	= Common::getDftMem();
			//生效列表
			$base_list = HoutaiModel::get_hd_base_list_key($SevidCfg['he']);
			$sxList[$SevidCfg['he']] = $cache->get($base_list);
		}
		if(empty($sxList[$SevidCfg['he']][$key])){
			return array();
		}
		
		static $xxList = array(); //静态生效详细活动
		if( empty($xxList[$SevidCfg['he']][$key]) ){
            $cache 	= Common::getDftMem();
			//该活动详细信息
            $key_base = HoutaiModel::get_benfu_key($SevidCfg['he'], $key);
			$xxList[$SevidCfg['he']][$key] = $cache->get($key_base);
		}
		
		$hdinfo = array();
		if( !empty($xxList[$SevidCfg['he']][$key])){
			$hdinfo = $xxList[$SevidCfg['he']][$key];
		}
		
		//返回该活动详细信息
		return $hdinfo;
	}
	
	
	/**
	 * 脚本 --- 单服活动写入
	 * @param $key
	 * @param $value
	 */
	static public function write_huodong_run($key,$id)
	{
		$db = Common::getMyDb();
        $value = array('id' => $id);
		$value = json_encode($value);
        $sql = "update `run` set `vjson`='{$value}' where `key`='{$key}'";
    	if($db->query($sql)){
    		return true;
    	}
    	return false;    
	}
	
	/**
	 * 脚本  --- 单服活动读取
	 */
	static public function read_huodong_run($key)
	{
		$db = Common::getMyDb();
        $sql = "select * from `run` where `key`= '{$key}'";
        
        $data = $db->fetchRow($sql);
        if(empty($data)){
        	$value = array('id' => 1);
        	$value = json_encode($value);
        	$ins_sql = "insert into `run` values('{$key}','{$value}')";
            $db->query($ins_sql);
            
            $data = $db->fetchRow($sql);
        }
        
		$vjson = json_decode($data['vjson'],1);
		if(empty($vjson['id'])){
			return 0;
		}
		return $vjson;
	}
	
	/*
	 * 插入登录日志数据
	 * */
	public static function insertData($uid,$platform) {
	    if(!empty($uid)){
	        $openid = Common::getOpenid($uid);
	        if(!empty($openid)){
	            $SevidCfg = Common::getSevidCfg();
	            $servid = $SevidCfg['sevid'];
        	    Common::loadModel('ServerModel');
        	    $serverid = ServerModel::getDefaultServerId();
        	    $today = intval(strtotime(date('Ymd',time())));
        	    $cx_sql = "select * from `login_log` where `openid`='{$openid}' and `login_time`='{$today}' and `servid`='{$servid}'";
        	    $db = Common::getDbBySevId($serverid);
        	    $res = $db->fetchRow($cx_sql);
        	    if(empty($res)){
        	        $sql = "insert into `login_log` values('{$openid}','{$today}','{$platform}','{$servid}')";
        	        $result = $db->query($sql);
        	    }
	        }
	    }
	}
	
	/*
	 * 查询登录日志数据
	 * */
	public static function findData($data){
	    Common::loadModel('ServerModel');
	    $serverid = ServerModel::getDefaultServerId();
	    $sql = 'select * from `login_log` where `openid`=\''.$data['openid'].'\'';
	    $db = Common::getDbBySevId($serverid);
	    $result = $db->fetchRow($sql);
	    return $result;
	}

	public static function queryData($servid) {
		Common::loadModel('ServerModel');
		$serverid = ServerModel::getDefaultServerId();
		$today = intval(strtotime(date('Ymd',time())));
		$startTime = $today - 86400 * 7;
		$sql = "select DISTINCT(`openid`) from `login_log` where login_time > {$startTime} and `servid`={$servid} limit 500";
		$db = Common::getDbBySevId($serverid);
		$result = $db->fetchArray($sql);
		return $result;
	}
	

	/*
	 * 插入注册日志数据
	 * */
	public static function insertRegData($param) {
	    if(!empty($param['uid']) && !empty($param['platform'])){
            $openid = Common::getOpenid($param['uid']);
	        if(!empty($openid)){
	            $SevidCfg = Common::getSevidCfg();
	            $servid = $SevidCfg['sevid'];
    	        Common::loadModel('ServerModel');
    	        $serverid = ServerModel::getDefaultServerId();
    	        $today = time();
    	        $cx_sql = "select * from `register` where `openid`='{$openid}'";
    	        $db = Common::getDbBySevId($serverid);
    	        $res = $db->fetchRow($cx_sql);
    	        if(empty($res)){
    	            $sql = "insert into `register` values('{$openid}',{$today},'{$param['platform']}',{$servid},'{$param['uid']}','')";
    	            $db->query($sql);
                    //分时间段新增统计
                    Game::flow_php_record($param['uid'], 10,date('H',$_SERVER['REQUEST_TIME']) , 0, '',0,date("Y-m-d",$today));
    	        }else{
    	            $data = json_decode($res['data'],true);
    	            $data[$servid] = array('uid'=>$param['uid'],'reg_time'=>$today);
    	            $data = json_encode($data);
    	            $sql = "update `register` set `data`='{$data}' where `openid`='{$openid}'";
    	            $db->query($sql);
                    //分时间段新增统计-滚服
                    Game::flow_php_record($param['uid'], 10,date('H',$_SERVER['REQUEST_TIME']) , 0, '',1,date("Y-m-d",$today));
    	        }
	        }
	    }
	}
	
	
	/*
	 * 获取官群QQ
	 * */
	public static function getQQ($platform){
	    $base_cfg = Game::get_peizhi('groupno_'.$platform);//群号
        if (empty($base_cfg)) {
            $base_cfg = Game::get_peizhi('groupno');//群号
        }
	    if(empty($base_cfg)){
	        return '123456789';
	    }else{
	        return strval($base_cfg[0]);
	    }
	}
	
	public static function get_hd_base_list_key($sevid)
    {
        Common::loadModel('GameActViewModel');
        return GameActViewModel::useNewTime() ? 'hd_base_list_new_time_'.$sevid : 'hd_base_list_'.$sevid;
    }
    public static function get_huodong_list_key($sevid)
    {
        Common::loadModel('GameActViewModel');
        return GameActViewModel::useNewTime() ? 'huodong_list_new_time_'.$sevid : 'huodong_list_'.$sevid;
    }
    public static function get_benfu_key($sevid, $key)
    {
        Common::loadModel('GameActViewModel');
        return GameActViewModel::useNewTime() ? 'benfu_new_time_'.$sevid.'_'.$key : 'benfu_'.$sevid.'_'.$key;
    }
}





