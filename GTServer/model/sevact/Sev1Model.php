<?php
/*
 * 子嗣全服提亲列表
 */
require_once "SevBaseModel.php";
class Sev1Model extends SevBaseModel
{
	public $comment = "子嗣全服提亲列表";
	//重写 建表保存
	/*
	 * UID
	 * 子嗣ID
	 * 性别
	 * 秀才
	 */
	public $act = 1;//活动标签
	public $_init = array(
	);
	/*
	 * //和服设想
	 * 所有全服招亲的子嗣 全部超时回去
	 * 
	 * 
	 * $sqls[] = "CREATE TABLE `son_marry` (
	`id` INT NOT NULL AUTO_INCREMENT COMMENT '流水号',
	`uid` BIGINT(64) NOT NULL COMMENT '玩家uid',
	`sonuid` INT(11) NOT NULL DEFAULT '0' COMMENT '子嗣ID',
	`sex` TINYINT(4) NULL DEFAULT NULL COMMENT '性别',
	`honor` TINYINT(4) NULL DEFAULT NULL COMMENT '秀才',
	`otime` INT(12) NULL DEFAULT NULL COMMENT '过期时间',
	PRIMARY KEY (`uid`, `sonuid`),
	INDEX `honor` (`honor`),
	INDEX `sex` (`sex`),
	INDEX `id` (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT";
	 */
	
	/*
	 * 重写构造函数  什么都没做
	 */
	public function __construct()
	{
		$this->_server_id = $this->_getServerID();
		return true;
	}
	
	/*
	 * 提亲 往提亲表插入数据
	 * uid
	 * 子嗣ID
	 * 性别
	 * 秀才
	 * 返回流水ID
	 */
	public function request($uid,$sid,$sex,$honor,$ishonor){
		$otime = $_SERVER['REQUEST_TIME'] + 3600*72;//过期时间 3天
		//插入 (或更新)数据表 数据
		$sql=<<<SQL
replace INTO `son_marry` set 
`uid` = {$uid},
`sonuid` = {$sid},
`sex` = {$sex},
`honor` = {$honor},
`otime` = {$otime},
`ishonor` = {$ishonor}
SQL;
    	$db = $this->_getDB();
        if (empty($db)){
			Master::error('sev1_dberr_request');
			return false;
		}
		if (!$db->query($sql)){
			Master::error('sev1_err_mysql_'.$sql);
		}
		$id = $db->insertId();
		return $id;
	}
	
	/*
	 * 结婚 或撤销 全服提亲
	 */
	public function delete($uid,$sid){
		//插入 (或更新)数据表 数据
		$sql = "DELETE FROM `son_marry` WHERE `uid`={$uid} AND `sonuid`={$sid} LIMIT 1;";
    	$db = $this->_getDB();
		if (empty($db)){
			Master::error('sev1_dberr_delete');
		}
		if (!$db->query($sql)){
			Master::error('sev1_mysql_'.$sql);
		}
		return true;
	}
	
	/*
	 * 结婚 或撤销 全服提亲 按照ID检索
	 */
	public function delete_byid($id){
		//插入 (或更新)数据表 数据
		$sql = "DELETE FROM `son_marry` WHERE `id`={$id} LIMIT 1;";
    	$db = $this->_getDB();
		if (empty($db)){
			Master::error('sev1_dberr_delete');
		}
		if (!$db->query($sql)){
			Master::error('sev1_mysql_'.$sql);
		}
		return true;
	}
	
	/*
	 * 招亲 往提亲随机获取数据
	 * 带来原有的ID列表
	 */
	public function zhaoqin($uid,$sex,$honor,$idarr = array()){
		$db = $this->_getDB();
		if (empty($db)){
			Master::error('sev1_dberr_zhaoqin');
			return false;
		}
		if (!empty($idarr)){
			//先查询 原本的几条信息 还在不在
			//$idstr = implode(',',$idarr);
			if(!empty($idstr)){
				$usida = array();
				foreach ($idstr as $_v){
					$usida[] = "(uid = {$_v['uid']} and sonuid={$_v['sonuid']})";
				}
				$where = empty($usida) ? '' : implode(' or ',$usida);
				$sql = "select * from son_marry where {$where})";
				//查询数据
				$data = $db->fetchArray($sql);
				$idarr = array();
				if (count($data) >= 3){
					return $data;
				}
			}
			
		}
		
		//查询出N个用户 随机出3个
		$sql = "select * from `son_marry`
			where sex <> {$sex} and `uid` <> {$uid}
			and `otime` > {$_SERVER['REQUEST_TIME']} limit 100";

		//查询数据
		$data = $db->fetchArray($sql);
        //如果提亲方设置了身份匹配选项 就再筛选一次数据

        foreach ($data as $f=>$a){
            if (empty($a['ishonor']) && $a['honor'] > $honor ){
                unset($data[$f]);
            }else{
                continue;
            }
        }

		if (!empty($data)){
			$need = 3 - count($idarr);//还差几条数据
            //随机取出其中几条信息
			$data = Game::array_rand($data,$need);
			
			/*
			foreach ($sj as $v){
				$idarr[] = $v['id'];
			}
			*/
		}
  
		return $data;
	}
	
	/*
	 * 进行结婚
	 */
	public function jiehun($uid,$sid){
		//检查这个子嗣 是不是还在招亲中
		$sql = "select * from `son_marry` where 
			`uid` = {$uid},
			and `sonuid` = {$sid},
			and `otime` > {$_SERVER['REQUEST_TIME']}";
    	$db = $this->_getDB();
		if (empty($db)){
			Master::error('sev1_dberr_request');
			return false;
		}
		$row = $db->fetchRow($sql);
		if(!empty($row)) {
			return false;//已经不在全服表里
		}

		//删除这个人的招亲信息 并且返回成功
		$this->delete($uid,$sid);
		return true;
	}
	
	/*
	 * 跑脚本 删除过期提亲请求
	 */
	
}
