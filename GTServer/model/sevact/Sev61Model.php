<?php
/*
 * 跨服大理寺战分组
 */
require_once "SevListBaseModel.php";
class Sev61Model extends SevListBaseModel
{
	public $comment = "跨服大理寺分组";
	public $act = 61;//活动标签

	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "group";//返回信息 所在控制器
	protected $_use_lock = false;//是否加锁
	public $hd_id = "huodong_300";

	public $_init = array(//初始化数据
		/*
		 * id => 1,
		 * list => array(array(1,2,3,4,5))
		 */
	);
	/*
	 * 分组
	 *
	 * */
	public function grouping($act_id,$max_rank=1,$limit=1,$server,$recover=0){

		//正式
		if(empty($this->info) || $this->info['id'] != $act_id){
			$list = array();
			if(empty($recover) && !empty($this->info['id'])){//获取上一次的排名
				$Redis304Model = Master::getRedis304($this->info['id']);
				$before_list = $Redis304Model->out_redis();
				if(!empty($before_list)){
					foreach ($before_list as $v){
						if(!empty($server)){
							foreach ($server as $k => $val){
								if($v['sid'] >= $val['mi'] && $v['sid'] <= $val['ma']){
									$SevCfgObj = Common::getSevCfgObj($v['sid']);
									$he = $SevCfgObj->getHE();
									if(!in_array($he,$list[$k])){
										$list[$k][] = $he;
									}
									break;
								}
							}
						}
					}
				}
			}
			$count = 0;
			if($max_rank > $count) {
				//获取服务器列表
				Common::loadModel('ServerModel');
				$serverList = ServerModel::getServList();
				for($i = $count+1;$i<=$max_rank;$i++){
					if(empty($serverList[$i])){
						continue;
					}
					if(!empty($server)){
						foreach ($server as $k => $val){
							if($i >= $val['mi'] && $i <= $val['ma']){
								$SevCfgObj = Common::getSevCfgObj($i);
								$he = $SevCfgObj->getHE();
								if(!in_array($he,$list[$k])){
									$list[$k][] = $he;
								}
								break;
							}
						}
					}
				}
			}
			if(!empty($list)){
				foreach ($list as $ks => $sids){
					foreach ($sids as $k => $sid){
						$num = intval($k/$limit);
						$result[$ks][$num][] = $sid;
					}
				}
				$res = array();
				foreach ($result as $ks => $val){
					foreach ($val as $sids){
						sort($sids,SORT_NUMERIC);
						$res[] = $sids;
					}
				}
				$this->info  = array(
					'id' => $act_id,
					'list' => $res
				);

				$this->save();
				//初始化
				$Redis305Model = Master::getRedis305($act_id);
				$SevCfg = Common::getSevidCfg();
				foreach ($res as $v){
					if(in_array($SevCfg['he'],$v)){
						foreach ($v as $sid){
							$Redis305Model->zAdd($sid, 0);
						}
					}
				}
			}
		}
	}
}
