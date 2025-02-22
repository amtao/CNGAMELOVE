<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );
class MemcachedClass
{
	protected $link;

	protected $expire = 864000;
    public $_mem_config = array();

	public function __construct( $config )
	{
		if (empty($config)){
			Master::error('MemcachedClass_cfg_err');
		}
		$this->_mem_config = $config;

		$this->link = new Memcached();
		//设置分布算法
		$this->link->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        if(isset($config['is_binary_protocol']) && $config['is_binary_protocol']){
            $this->link->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
            //重要，php memcached有个bug，当get的值不存在，有固定40ms延迟，开启这个参数，可以避免这个bug
            $this->link->setOption(Memcached::OPT_TCP_NODELAY, true);
        }
		//连接服务器
        if (isset($config['host'])) {
            //设置前缀
            $this->link->setOption(Memcached::OPT_PREFIX_KEY, $config['prekey'].'_');
            $this->link->addServers(array(array($config['host'],$config['port'],$config['weight'])));
        } else {//分布式
            //设置前缀
            $this->link->setOption(Memcached::OPT_PREFIX_KEY, $config[0]['prekey'].'_');
            $servers = array();
            foreach ($config as $config_v) {
                $servers[] = array($config_v['host'],$config_v['port'],$config_v['weight']);
            }
            $this->link->addServers($servers);
        }
        if(!empty($config['username']) && !empty($config['pass'])){
            $this->link->setSaslAuthData($config['username'], $config['pass']);
        }
    }

	public function set( $key , $value , $expire = 0 )
	{
		$microtime = microtime(true);
		
		$expire = ( $expire > 0 ) ? $expire : $this->expire;
		if ( defined('MEMCACHED_PREFIX_KEY') && MEMCACHED_PREFIX_KEY ) {
			$res = $this->link->set( $key , $value , $expire );
		} else {
			$res = $this->link->set( $key , $value ,  time()+$expire );
		}
		if( !$res ){
			$url_this = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$trace_data = array('mem_key' => $key,'mem_data' => $value,'url'=>$url_this,'time'=>date('Y-m-d H:i:s'));
		
	        $file_name = '/tmp/memcache_error_log_'.date("Ymd");
			if( defined('FILE_PATH') && FILE_PATH ){
				$file_name = FILE_PATH . 'memcache_error_log_'.date("Ymd");
			}
			if( !file_exists($file_name)){
				$com = "touch $file_name";
				@exec($com);
				$com = "chmod 777 $file_name";
				@exec($com);
			}
			file_put_contents($file_name,var_export($trace_data,true) . PHP_EOL,FILE_APPEND);
		}else{
			/*
			$time = microtime(true) - $microtime;
	        if ( $time >= 1 ) {
		        $fileName = '/tmp/mem_tome_long2';
				file_put_contents($fileName, PHP_EOL . date('Y-m-d H:i:s') . 
					' Request: ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . PHP_EOL . 
					' key:' .$key.PHP_EOL.'val:'. var_export($value, true)  . PHP_EOL, FILE_APPEND);
				
				$com = "chmod 777 $fileName";
				@exec($com);
			}
			*/
		}
		return $res;
	}

	public function add( $key , $value , $expire = 0 )
	{
		$expire = ( $expire > 0 ) ? $expire : $this->expire;
		if ( defined('MEMCACHED_PREFIX_KEY') && MEMCACHED_PREFIX_KEY ) {
			return $this->link->add( $key , $value , $expire );
		} else {
			return $this->link->add( $key , $value , time()+$expire );
		}
	}

	public function replace( $key , $value , $expire = 0 )
	{
		$expire = ( $expire > 0 ) ? $expire : $this->expire;
		if ( defined('MEMCACHED_PREFIX_KEY') && MEMCACHED_PREFIX_KEY ) {
			return $this->link->replace( $key , $value , $expire );
		} else {
			return $this->link->replace( $key , $value , time()+$expire );
		}
	}

	public function get( $key )
	{
        $memory_get_usage = memory_get_usage();
        $array = array(
            'bytes'=> $memory_get_usage,
            'MB' => $memory_get_usage/1024/1024,
        );
        if($array['MB']>=400){
            //内存使用过大记录
            $file_name = '/tmp/memory_get_usage_big400_log_'.date("Ymd");
            if( defined('FILE_PATH') && FILE_PATH ){
                $file_name = FILE_PATH . 'memory_get_usage_big400_log_'.date("Ymd");
            }
            $array['key'] = $key;
            $array['url'] = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $array['request'] = isset($GLOBALS['param']) ? $GLOBALS['param'] : array();
            //$array['debug'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            file_put_contents($file_name, date("Y-m-d H:i:s").PHP_EOL.var_export($array,true).PHP_EOL, FILE_APPEND);
            unset($file_name);
        }
        unset($array);
		return $this->link->get( $key );
	}

	public function increment( $key , $value)
	{
		return $this->link->increment( $key , $value );
	}

	public function delete( $key , $time_out = 0 )
	{
		return $this->link->delete( $key , $time_out );
	}
	public function quitLink()
    {
        return $this->link->quit();
    }
}
