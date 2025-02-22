<?php
class Db
{
	protected $db;
	public $_db_config = array();
	public function connect($config){
		$this->_db_config = $config;
		//$NEW_LINK = FALSE;
		//if( defined("NEW_LINK") && NEW_LINK ){
			$NEW_LINK = true;
		//}
		if( $this->db = mysql_connect( "{$config['host']}:{$config['port']}" , $config['user'] , $config['passwd'],$NEW_LINK ) )
		{
			mysql_query( "SET NAMES 'utf8'" );
			return mysql_select_db( $config['name'], $this->db );
		}
		else
		{
            $SevidCfg = Common::getSevidCfg();
			//连接数据库错误
            $fileName = "mysql_error_log_".date("Ymd");
            $content = PHP_EOL . date('Y-m-d H:i:s') .$SevidCfg['sevid'].':'.$SevidCfg['sevid']."\r\n" .
                'wen_jian: '.$_SERVER['argv'][0]."\r\n" .
                ' Request: ' . 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . PHP_EOL .
                ' Error-Connect. Config:' . var_export($config, true) . PHP_EOL .
                " Error-Msg:" . mysql_error() . PHP_EOL;
            Common::log($fileName, $content);
            die( 'Error mysql_connect' );
            return false;
		}
	}
	public function __construct($config)
	{
		return $this->connect($config);
	}
	function reconnect(){
		$nfileName = "mysql_reconnect_log_".date("Ymd");
		try{
			if (!mysql_ping ($this->db)) {
				//here is the major trick, you have to close the connection (even though its not currently working) for it to recreate properly.
				$this->close($this->db);
				$re = $this->connect($this->_db_config);

				$content = "";
				if($re)
				{
					$content = PHP_EOL ."reconnect ok".PHP_EOL;
				}else{
					$content = PHP_EOL ."reconnect faild".PHP_EOL;
				}
				Common::log($nfileName, $content);
			}else{
				$content = PHP_EOL ."reconnect ping ok".PHP_EOL;
				Common::log($nfileName, $content);
			}
		}catch (Exception $e){
			$this->close($this->db);
				$re = $this->connect($this->_db_config);
				$nfileName = "mysql_reconnect_ex_log_".date("Ymd");
				$content = "";
				if($re)
				{
					$content = PHP_EOL ."reconnect ok".PHP_EOL;
				}else{
					$content = PHP_EOL ."reconnect faild".PHP_EOL;
				}
				Common::log($nfileName, $content);
		}

	}
	public function query($sql)
	{
		$microtime = microtime(true);
		//$this->reconnect();
		$query = mysql_query($sql,$this->db);// or die($sql."Invalid query: " . mysql_error());
		$errno = mysql_errno();
		if($errno==2003||$errno==2006)
		{
			$this->reconnect();
			$query = mysql_query($sql,$this->db);
			$errno = mysql_errno();
		}
		if ( $errno ) {

			$SevidCfg = Common::getSevidCfg();
			$fileName = "mysql_error_log_".date("Ymd");
			$array = array(
				'sql'=>$sql,
				'request'=>isset($GLOBALS['param']) ? $GLOBALS['param'] : array(),
			);
			$content = "\r\n" . date('Y-m-d H:i:s') .  $SevidCfg['sevid'].':'.$SevidCfg['sevid']."\r\n".'wen_jian: '.$_SERVER['argv'][0]."\r\n" .
				' Request: ' . 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI']."\r\n".' Error-Query. Sql:' . var_export($array, true)  . "\r\n" .
				" Error-Msg:" . mysql_error() . "\r\n".
				" Error-No:" . $errno . "\r\n"
				;
            Common::log($fileName, $content);
		} else {
			$time = microtime(true) - $microtime;
	        if ( $time >= 1 ) {
				$SevidCfg = Common::getSevidCfg();
		        $fileName = 'mysql_slow_log_'.date("Ymd");
				$array = array(
					'time'=>$time,
					'sql'=>$sql,
					'request'=>isset($GLOBALS['param']) ? $GLOBALS['param'] : array(),
				);
                $content = PHP_EOL . date('Y-m-d H:i:s').$SevidCfg['sevid'].':'.$SevidCfg['sevid']."\r\n".'wen_jian:'.$_SERVER['argv'][0]."\r\n" .
					' Request: '.'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].PHP_EOL.'Query-Slow. Sql:'.var_export($array, true).PHP_EOL;
                Common::log($fileName, $content);
			}
		}
		return $query;
	}

	public function fetchArray($sql)
	{
		$result = $this->query($sql);
        if (empty($result)){
            return array();
        }
		return $this->res2Assoc($result);
	}

	public function fetchAssoc($result)
	{
		return mysql_fetch_assoc($result);
	}

	public function fetchRow($query)
	{
		$result = $this->query($query);
		return $this->fetchAssoc($result);
	}

	public function fetchObject($result)
	{
		return mysql_fetch_object($result);
	}

	public function affectedRows()
	{
		return mysql_affected_rows($this->db);
	}

	public function insertId()
	{
		return mysql_insert_id($this->db);
	}

	public function getCount($tables, $condition = "")
	{
		$r = $this->fetchRow("select count(*) as count from $tables " . ( $condition ? " where $condition" : ""));
		return $r['count'];
	}

	public function & res2Assoc(& $res)
	{
		$rows = array();
		while($row = $this->fetchAssoc($res))
		{
			$rows[] = $row;
		}
		return $rows;
	}

	public function startTransaction()
	{
		mysql_query( "SET AUTOCOMMIT=0" , $this->db );
		mysql_query( "START TRANSACTION" , $this->db );
	}

	public function commitTransaction()
	{
		mysql_query( "COMMIT" );
		mysql_query( "SET AUTOCOMMIT=1" , $this->db );
	}

	public function rollbackTransaction()
	{
		mysql_query( "ROLLBACK" );
	}

	public function close()
	{
	    if (!empty($this->db)) {
            mysql_close($this->db);
        }
	}

	/**
	 * 引用结束，关闭数据库
	 */
	public function __destruct()
	{
		//关闭数据库
		$this->close();
	}
}