<?php
require_once "ActBaseModel.php";
/*
 * 跨服衙门-仇人信息
 */
class Act305Model extends ActBaseModel
{
	public $atype = 305;//活动编号
	
	public $comment = "跨服衙门-仇人信息";
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "enymsg";//返回信息 所在控制器
	public $hd_cfg;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'uid' = >array(
		 * 	'time' => 1,
		 * )
		 */ 
	);
	public function __construct($uid){
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info('huodong_300');
	    if(!empty($this->hd_cfg)){
	        parent::__construct($uid,$this->hd_cfg['info']['id']);
	    }
	}

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
    public function del($fuid){
        if (isset($this->info[$fuid])){//验证是否有数据
            if(isset($this->info[$fuid]['time'])){//验证是否为旧版本
                unset($this->info[$fuid]['time']);
            }else{
                if(is_array($this->info[$fuid])) array_shift($this->info[$fuid]);
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
        $Redis306Model = Master::getRedis306($this->hd_cfg['info']['id']);

        foreach($this->info as $k_uid => $v){
            $fUser = Master::fuidData($k_uid);
            if(!is_array($v)){continue;}//foreach安全验证
            //输出这个人的衙门分数
            if(isset($v['time'])){//兼容旧版本
                $u_data = array(
                    'id' => $k_uid,
                    'fuser' => $fUser,
                    'score' => intval($Redis306Model->zScore($k_uid)),
                    'time' => $v['time'],
                );
                $out[] = $u_data;
            }else{
                foreach ($v as $key => $value){
                    $u_data = array(
                        'id' => $k_uid,
                        'fuser' => $fUser,
                        'score' => intval($Redis306Model->zScore($k_uid)),
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
