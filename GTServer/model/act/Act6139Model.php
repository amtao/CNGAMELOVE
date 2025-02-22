<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6139
 */
class Act6139Model extends ActHDBaseModel
{
	public $atype = 6139;//活动编号
	public $comment = "限时奖励-单次充值档次";
    public $b_mol = "czhuodong";
	public $b_ctrl = "onceRecharge";//子类配置
	public $hd_id = 'huodong_6139';//活动配置文件关键字
    protected $_rank_id = 6139;

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            if(empty($this->info['cons']) || $this->info['cons'] < $num){
                $this->info['cons'] = $num;
                $this->save();
            }
        }
    }

}
