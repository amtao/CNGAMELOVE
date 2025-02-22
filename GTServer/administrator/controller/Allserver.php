<?php
class AllServer
{

	/**
	 * 全服数据基础界面
	 */
	public function index() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	/**
	 * 充值统计
	 */
	public function paySearch() {
        $where = ' where';
        if ($_POST['startTime']){
            $where .= ' `ctime`>'.strtotime($_POST['startTime']);
        }
        if ($_POST['endTime']){
            $where .= ' and `ctime`<'.strtotime($_POST['endTime']);
        }
        if ($_POST['platForms']){
            $where .= ' and `platform`='.$_POST['platform'];
        }
        $table = '`t_order`';
        if ($where!=' where'){
            $sql = 'SELECT * FROM '.$table.$where;
        }else{
            $sql = 'SELECT * FROM '.$table;
        }
        var_dump($sql);
        $db = Common::getMyDb();
        $searchRecords = $db->fetchArray($sql);
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	/**
	 * 充值查询
	 */
	public function orderCX() {
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
}
