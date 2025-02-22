<?php
require_once "ActBaseModel.php";
/*
 * 活动200
 */
class Act200Model extends ActBaseModel
{
	public $atype = 200;//活动编号
	public $comment = "活动生效列表";
	public $b_mol = "huodonglist";//返回信息 所在模块
	public $b_ctrl = "all";//返回信息 所在控制器
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	
	);
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//默认输出直接等于内部存储数据
		Common::loadModel('HoutaiModel');
		$outf = HoutaiModel::get_huodong_list($this->uid);
		if(!empty($outf)){
			foreach($outf as $k => $v){
				if($v['type'] == 0){
					unset($outf[$k]);
				}
			}
		}
		$this->outf = $outf;
	}

	public function flushZero(){
        $this->make_out();
        foreach($this->outf as $k => $v){
            switch ($v['id']){
                case 6121:
                case 6168:
                case 6184:
                case 260:
                case 6123:
                case 6189:
                case 6211:
                case 6225:
//                case 287:
                    $modeName= 'getAct'.$v['id'];
                    $ActModel = Master::$modeName($this->uid);
                    $ActModel->back_data_hd();
                    break;
            }
        }
        $this->back_data();
    }

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		if(!empty($this->outf)){
			Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
		}
	}
}
