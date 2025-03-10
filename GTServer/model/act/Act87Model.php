<?php
require_once "ActBaseModel.php";
/*
 * 双旦-寻宝大冒险 - 任务每日重置
 */
class Act87Model extends ActBaseModel
{
	public $atype = 87;//活动编号
	public $comment = "双旦-寻宝大冒险 - 任务每日重置";
	public $b_mol = "sdhuodong";//返回信息 所在模块
	public $b_ctrl = "task";//子类配置
	public $hd_id = 'huodong_293';//活动配置文件关键字

	/*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
	public $_init =  array(
        'task' => array(),
        'get' => array(),
	);


    /**
     * @param $id : 任务类型id
     * 1:每日登陆
     * 2:处理政务次数
     * 3:寻访次数
     * 4:开启演武场次数
     * 5:充值金额
     * 6:赴宴
     * 7:衙门出战
     * 8:帮会建设
     * @param $num : 这次完成的进度
     */
    public function add_task($id,$num = 0){
        if(empty($this->info['task'][$id])){
            $this->info['task'][$id] == 0;
        }
        $this->info['task'][$id] += $num;
        $this->save();
    }

    /**
     * 记录任务完成
     * @param $id : 任务id
     */
    public function add_get($id){

        $this->info['get'][] = $id;
        $this->save();
    }

    /**
     * 检测任务是否完成
     * @param $id 任务id
     * @param $type 任务类型id
     * @param $max 任务满足条件
     * @return int
     */
    public function check($id,$type,$max){
        //已经领取
        if(!empty($this->info['get']) && in_array($id,$this->info['get'])){
            return 2;
        }
        //可领取
        if(!empty($this->info['task'][$type]) && $this->info['task'][$type] >= $max){
            return 1;
        }
        //不能领取
        return 0;
    }

    /**
     * 返回当前完成的进度
     * @param $id  任务类型id
     * @return int
     */
    public function get_num($id){
        if(!empty($this->info['task'][$id])){
            return $this->info['task'][$id];
        }
        return 0;
    }

    /*
     * 返回活动信息
     */
    public function back_data(){

    }

    /*
     * 累计每日登陆兼容
     */
    public function do_check(){
        //每日登陆做兼容,登陆至少要有一次
        if( empty($this->info['task'][1])){
            $this->info['task'][1] = 1;
            $this->save();
        }
    }

}







