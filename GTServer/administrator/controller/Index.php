<?php
class Index
{
	public function run()
	{
		$db = Common::getMyDb();
		//$sql = "select count(0) from `gm_session`";
		//$row = $db->fetchRow($sql);
		$_SESSION["CURRENT_USER"] = $_GET['user'];


        include TPL_DIR . 'index.php';
	}
	
	/**
	 * 清楚文件缓存数据
	 */
	public function cleanData()
	{
	    $key = $_GET['key'];
        $path = '/tmp/robber'.SNS.$this->config['memcache']['data'][0]['port'].'/';
        unlink($path.$key);
        header('Location: admin.php');
	}
	
	/**
	 * 清楚内存中的数据
	public function cleanMC()
	{
	    $key = $_GET['key'];
	    $cache = Common::getCache();
	    $cache->delete($key);
        header('Location: admin.php');
	}
	 */
}
