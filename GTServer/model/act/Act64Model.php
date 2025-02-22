<?php
require_once "ActBaseModel.php";
/*
 * 衙门-衙门日志列表挑战记录
 */
class Act64Model extends ActBaseModel
{
	public $atype = 64;//活动编号
    	public $b_mol = "yamen";//返回信息 所在模块
    	public $b_ctrl = "hastz";//返回信息 所在控制器
	public $comment = "衙门日志列表挑战记录";
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * id
		 */ 
	);
	
	/*
	 * 添加仇人
	 */
	public function add($id){
	    array_push($this->info,$id);
	    $this->save();
	}
	/*
	 * 检查是否打过
	 */
	public function check($id){
	    return in_array($id,$this->info);
    }

    /*
     * 返回活动信息
     */
    public function back_data(){
        $outf = array();
        if(!empty($this->info)){
            foreach ( $this->info as $value) {
                $outf[]['id'] = $value;
            }
        }
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$outf);
    }
}
