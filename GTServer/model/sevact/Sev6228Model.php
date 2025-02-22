<?php
/*
 * 读书节
 */
require_once "SevBaseModel.php";
class Sev6228Model extends SevBaseModel
{
    public $comment = "读书节";
    public $act = 6228;
    public $b_mol = "studyday";//返回信息 所在模块
    public $b_ctrl = "mirror";//子类配置
    public $hd_id = 'huodong_6228';//活动配置文件关键字
    public $hd_cfg;

    public function __construct($hid,$cid)
    {
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if (empty($this->hd_cfg)){
            return ;
        }
        parent::__construct($hid,$cid);

    }
    
    /*
	 * 初始化数据
	 */
    public $_init = array(

    );

    /*
	 * 构造业务输出数据
	 */
    public function mk_outf(){
        $log = array();
        $temparra = $this->info['log'];//倒序输出
        if (!empty($temparra)){
            foreach($temparra as $k => $v){

                $UserModel = Master::getUser($v['name']);
                $name = $UserModel->info['name'];

                $fuidInfo['name'] = $name;
                $fuidInfo['itemid'] = $v['itemid'];

                $log[] = $fuidInfo;
            }
        }
        return $log;
    }

    /*
     * 添加一条投票信息
     */
    public function add($uid,$itemid){

        $this->info['log'][] = array(
            'name' => $uid,
            'itemid' => $itemid,
        );
        //截取数据表
        $max_num = 8;
        if (count($this->info['log']) > $max_num){
            $this->info['log'] = array_slice($this->info['log'],-$max_num);
        }
        $this->save();
    }


    /*
     * 返回协议信息
     */
    public function back_data(){
        $data = $this->mk_outf();
        if($data){
            Master::back_data(0,$this->b_mol,'records',$data);
        }
    }

}