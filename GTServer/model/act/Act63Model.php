<?php
require_once "ActBaseModel.php";
/*
 * 衙门-仇人信息
 */
class Act63Model extends ActBaseModel
{
	public $atype = 63;//活动编号
	
	public $comment = "衙门-仇人信息";
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "enymsg";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'uid' = >array(
		 *      array(1565687,15698656),
		 * )
		 */ 
	);
	
	/*
	 * 添加仇人
	 */
	public function add($fuid){
	    //数据初始化
        if(!is_array($this->info[$fuid])){
            $data = array();
        }else{
            $data = $this->info[$fuid];
        }
        //兼容旧版本
	    if(isset($this->info[$fuid]['time'])){
            $data = array();
	        array_push($data,$this->info[$fuid]['time']);
        }
        //插入数据
        array_push($data,Game::get_now());
	    $this->info[$fuid] = $data;
		$this->save();
	}
	
	/*
	 * 删除仇人
	 * 删除失败 返回false
	 */
	public function del($fuid,$time = 0){
		if (isset($this->info[$fuid])){//验证是否有数据
		    if(isset($this->info[$fuid]['time'])){//验证是否为旧版本
		        unset($this->info[$fuid]['time']);
            }else{
                if(is_array($this->info[$fuid])){
                    if(empty($time)){
                        array_shift($this->info[$fuid]);
                    }else{
                        foreach ($this->info[$fuid] as $k => $v){
                            if($v == $time){
                                unset($this->info[$fuid][$k]);
                            }
                        }
                    }
                }
            }
			$this->save();
			return false;
		}else{
		    return true;
		}
	}


	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		$out = array();
		$Redis6Model = Master::getRedis6();
		$fuid = array();
		foreach($this->info as $k_uid => $v){
			if(!is_array($v)){continue;}//foreach安全验证
			//输出这个人的衙门分数
            if(isset($v['time'])){//兼容旧版本
                //超过30天过滤
                if($v['time'] < Game::get_over(-2592000)){
                    continue;
                }
                $u_data = array(
                    'id' => $k_uid,
                    'fuser' => Master::fuidData($k_uid),
                    'score' => $Redis6Model->zScore($k_uid),
                    'time' => $v['time'],
                );
                $out[] = $u_data;
            }else{
                foreach ($v as $key => $value){
                    //超过30天过滤
                    if($value < Game::get_over(-2592000)){
                        continue;
                    }
                    if(!isset($fuid[$k_uid])){
                        $fuid[$k_uid] = Master::fuidData($k_uid);
                    }
                    $u_data = array(
                        'id' => $k_uid,
                        'fuser' => $fuid[$k_uid],
                        'score' => $Redis6Model->zScore($k_uid),
                        'time' => $value,
                    );
                    $out[] = $u_data;
                }
            }
		}
		//
		$arr1 = array_map(create_function('$n','return $n["time"];'),$out);
        array_multisort($arr1,SORT_DESC,$out);
		$this->outf = $out;
	}
}
