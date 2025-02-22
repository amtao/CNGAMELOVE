<?php
class RedisClass extends Redis
{
	/**
	 * 引用结束，关闭连接
	 */
	public function __destruct()
	{
		$this->close();
	}
}