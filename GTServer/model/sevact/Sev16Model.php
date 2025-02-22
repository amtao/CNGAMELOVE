<?php
/*
 * 联盟-红包列表
 */
require_once "SevBaseModel.php";
class Sev16Model extends SevBaseModel
{
	public $comment = "联盟-红包列表";
	public $act = 16;//活动标签
	
	public $_init = array(//初始化数据
		
	);

	/**
	 * 添加红包
	 * @param $uid
	 * @param $id
	 */
	public function add($uid,$id){
		if(!empty($this->info['list']) && in_array($uid.'_'.$id,$this->info['list'])){
    		Master::error(HBHD_USED_VOUCHER);
		}
		$this->info['list'][] = $uid.'_'.$id;
		$this->save();
	}


	/**
	 * 领取红包
	 * @param $uid
	 * @param $fuid
	 * @param $id
	 * @return array
	 */
	public function getHb($uid,$fuid,$id){
		//判断是否存在红包
		if(empty($this->info['list']) || !in_array($fuid.'_'.$id,$this->info['list'])){
			Master::error(HBHD_RED_PACK_NO_FIND);
		}
		if($this->info['detail'][$fuid.'_'.$id][$uid]){
			Master::error(HBHD_RED_PACK_RECEIVED);
		}
		$Act44Model = Master::getAct44($fuid);
		$max = $Act44Model->info[$id]['num'];//该红包总个数
		$num = empty($this->info['detail'][$fuid.'_'.$id]) ? 0 : count($this->info['detail'][$fuid.'_'.$id]);
		if($max <= $num){
			Master::error_msg(HBHD_RED_PACK_PICKED_UP);
			Master::back_s(2);
		}else{
			$money = $Act44Model->getMoney($uid,$id);
			$this->info['detail'][$fuid.'_'.$id][$uid] = $money;
			$this->save();

			$Act45Model = Master::getAct45($uid);
			$Act45Model->add($fuid,$id);

			Master::add_item($uid,KIND_ITEM,1,$money);
			Game::cmd_flow(49,$fuid.'_'.$id,$money,$money);
			Game::cmd_other_flow($this->cid , 'hbhuodong', 'getHb', array($uid => $fuid.'_'.$id), 50, 1, $money,$money);
		}
	}



	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$outof = array();
		if(!empty($this->info['list'])){
			foreach ($this->info['list'] as $val){
				$member = explode('_',$val);
				$fuid = $member[0];
				$id = $member[1];
				$Act44Model = Master::getAct44($fuid);
				$UserModel = Master::getUser($fuid);
				$lq_list = array();
				if(!empty($this->info['detail'][$val])){
					$max_uid = array_search(max($this->info['detail'][$val]),$this->info['detail'][$val]);
					foreach ($this->info['detail'][$val] as $e_uid => $money){
						$e_UserModel = Master::getUser($e_uid);
						if(empty($e_UserModel->info['name'])){
							$name = '未知';
						}else{
							$name = $e_UserModel->info['name'];
						}
						$lucky = 0;
						$exite = empty($this->info['detail'][$val]) ? 0 : count($this->info['detail'][$val]);
						if($Act44Model->info[$id]['num'] <= $exite && $max_uid == $e_uid){
							$lucky = 1;
						}
						$lq_list[] = array(
							'uid' => $e_uid,
							'name' => $name,
							'money' => $money,
							'lucky' => $lucky
						);
					}
				}
				$outof[] = array(
					'uid' => $fuid,
					'id' => $id,
					'name' => $UserModel->info['name'],
					'sex' => $UserModel->info['sex'],
					'job' => $UserModel->info['job'],
					'total' =>  $Act44Model->info[$id]['num'],
					'exite' =>  empty($this->info['detail'][$val]) ? 0 : count($this->info['detail'][$val]),
					'msg' => $Act44Model->info[$id]['msg'],
					'stime' => $Act44Model->info[$id]['stime'],
					'lq_list' => $lq_list
				);
			}
		}
		$this->outof = $outof;
		return $this->outof;
	}

	/**
	 * 移除红包信息
	 * @param $uid
	 */
	public function delHb($uid){
		if(!empty($this->info['list'])){
			$bool = false;
			foreach ($this->info['list'] as $k => $val){
				$member = explode('_',$val);
				if($uid == $member[0]){
					unset($this->info['list'][$k],$this->info['detail'][$val]);
					Game::cmd_other_flow($this->cid , 'hbhuodong', 'removeHb', array($uid => $k), 50, 1, 1,0);
					$bool = true;
				}
			}
			if($bool){
				$this->save();
			}
		}
	}

	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		$data = $this->get_outf();

		Master::back_data(0,'hbhuodong','hblist',$data);
	}

	/**
	 * 获取最后一个红包的信息
	 * @return array
	 */
	public function getLastHb(){
		$data = array();
		if(!empty($this->info['list'])){
			$key = count($this->info['list'])-1;
			$member = explode('_',$this->info['list'][$key]);
			$data = array(
				'uid' => $member[0],
				'id' => $member[1]
			);
		}
		return $data;
	}

	/**
	 * 是否有可以领取的红包
	 * @param $uid
	 */
	public function isGetHb($uid){
		$is_get = 0;
		if(!empty($this->info['list'])){
			foreach ($this->info['list'] as $val){
				if(empty($this->info['detail'][$val]) || !isset($this->info['detail'][$val][$uid])){
					$is_get = 1;
					break;
				}
			}
		}
		return $is_get;
	}
}





