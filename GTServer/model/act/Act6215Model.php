<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6215
 */
class Act6215Model extends ActHDBaseModel
{
    public $atype = 6215;//活动编号
    public $comment = "偷取晨露次数冲榜";
    public $b_mol = "cbhuodong";//返回信息 所在模块
    public $b_ctrl = "stealcl";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6215';//活动配置文件关键字



    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        return $news;
    }

    /**
     * 资质分数排行保存
     * @param $num  资质涨幅
     */
    public function do_save($num){
        //在活动中
        if( parent::get_state() == 1){
            if ($num > 0){
                //保存到排行榜中
                $Redis6215Model = Master::getRedis6215($this->hd_cfg['info']['id']);
                $Redis6215Model->zIncrBy($this->uid,$num);
            }

        }
    }


    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $this->outf['cfg'] = $hd_cfg;
    }

    /*
     * 返回活动信息
     */
    public function back_data_hd(){
        //配置信息
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
        //排行信息
        $Redis6215Model = Master::getRedis6215($this->hd_cfg['info']['id']);
        $Redis6215Model->back_data();
        $Redis6215Model->back_data_my($this->uid);

    }

}
