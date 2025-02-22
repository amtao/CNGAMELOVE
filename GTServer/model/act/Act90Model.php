<?php
require_once "ActBaseModel.php";
/*
 *   主线的宠幸 第一次 生小孩
 */
class Act90Model extends ActBaseModel
{
	public $atype = 90;//活动编号

	public $comment = "主线的宠幸第一次生小孩";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'id' => 0,  //  1:主线的宠幸第一次生小孩
	);
	
	public function do_save(){
		if(empty($this->info['id'])){
			$this->info['id'] = 1;
			$this->save();
			return true;
		}
		return false;
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
		
	}

	/*
	 * 新加亲家闪光
	 * $fuid : 亲家uid
	 */
    public function qjTip($fuid){
        $this->info['qjlist'][$fuid] = $fuid;
        $this->save();
    }

    /*
     * 清除  新加亲家闪光
     * $fuid : 亲家uid
     * $open : 1 :下发(默认)  0:不下发
     */
    public function clearQjTip($fuid,$open = 1){
        if(empty($this->info['qjlist'][$fuid])){
            return false;
        }
        unset($this->info['qjlist'][$fuid]);
        $this->save();
        if($open){
            $Act133Model = Master::getAct133($this->uid);
            $Act133Model->back_data();
        }

    }


	
}
