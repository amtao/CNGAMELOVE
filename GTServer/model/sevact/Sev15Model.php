<?php
/*
 * 联盟日志
 */
require_once "SevBaseModel.php";
class Sev15Model extends SevBaseModel
{
	public $comment = "联盟日志";
	public $act = 15;//活动标签
	public $_init = array();
	protected $_save_msg_cd = 3600;//结果集保存CD时长，单位秒数

	public function __construct($hid,$cid){
		parent::__construct($hid,$cid);
	}

	/**
	 * 添加公会日志
	 * @param $type 1:建设;2:更改公告;3:击杀;4:开启副本;5:职位变更;6:联盟名字更改;7:逐出联盟;8:联盟升级;9:加入联盟;10:主动退出帮会,
	 * 11:随机加入帮会,12:进贡,13:建筑升级,14:捐献,15:权贵友好,16:对方拉拢失败,17:对方拉拢成功,18:我方拉拢成功,19:我方拉拢失败,20:宗人府攻击
	 * 21:宴会购买特效
	 * @param $uid  当事人
	 * @param $fuid  执行人
	 * @param $num1  数据1
	 * @param $num2  数据2
	 * @param $items  道具列表
	 */
	public function add_log($type,$uid,$fuid = 0,$num1 = 0,$num2 = 0,$num3 = 0,$items = array(),$items2 = array(), $num4 = 0){

		$this->info[] = array(
			'type' => $type,
			'uid' => $uid,
			'fuid' => $fuid,
			'num1' => $num1,
			'num2' => $num2,
			'num3' => $num3,
			'num4' => $num4,
			'items' => $items,
			'items2' => $items2,
			'time' => $_SERVER['REQUEST_TIME'],
		);
		$this->save();

		$data = $this->get_outf();
		foreach ($data as $k => $v) {
			 if ($v["type"] == 1) {
            	$data[$k]["type"] = 14;
            }
		}
		Master::back_data(0,'club','clubLog',$data);
	}

	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$this->outof = array();
		$info = $this->info;
		rsort($info);

		$list = array();
		$flag = 0;  //是否进行保存操作   1:保存   0:不保存
		foreach($info as $v){

			if($_SERVER['REQUEST_TIME'] - $v["time"] > 86400 * 3 ){
                $flag = 1;
                continue;
            }

            if ( ($v["type"] == 1 && $v["num3"] == 0) || ($v["type"] == 12 && $v["num3"] == 0) ) {
            	$flag = 1;
                continue;
            }

            $type = $v['type'];
            if ($type == 1) {
            	$type = 14;
            }

			$name = '';
			if(!empty($v['uid'])){
				$UserModel = Master::getUser($v['uid']);
				$name = $UserModel->info['name'];
			}
			$fname = '';
			if(!empty($v['fuid'])){
				$fUserModel = Master::getUser($v['fuid']);
				$fname = $fUserModel->info['name'];
			}

			$list[] = array(
				'type' => $type,
				'name' => $name,
				'fname' => $fname,
				'uid' => $v['uid'],
				'fuid' => $v['fuid'],
				'num1' => $v['num1'],
				'num2' => $v['num2'],
				'num3' => $v['num3'],
				'num4' => $v['num4'],
				'items' => $v['items'],
				'items2' => $v['items2'],
				'time' => $v['time'],
			);
		}

        //保存
        if($flag == 1){
            $this->info = $list;
            $this->save();
        }

        $this->outof = $list;
		return $this->outof;
	}

	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		$data = $this->get_outf();
		foreach ($data as $k => $v) {
			 if ($v["type"] == 1) {
            	$data[$k]["type"] = 14;
            }
		}

		Master::back_data(0,'club','clubLog',$data);
	}
}





