<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6204
 */
class Act6204Model extends ActHDBaseModel
{
    public $atype = 6204;//活动编号
    public $comment = "国力庆典-每日名声消耗排行";
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//子类配置
    public $hd_id = 'huodong_6204';//活动配置文件关键字

    /*
	 * 初始化结构体
	 */
    public $_init =  array(

    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
            if (empty($this->info[$riqi])){
                $this->info[$riqi] = $num;
            }else{
                $this->info[$riqi] += $num;
            }
            $this->save();
            $rule = $this->hd_cfg['rule'];
            $hdscore = intval($this->info[$riqi]/$rule['cons'])*$rule['add'];
            $Redis6204Model = Master::getRedis6204($this->_get_day_redis_id());
            $paihang = $Redis6204Model->zScore($this->uid);

            if ($hdscore > $paihang){
                $Redis6204Model->zAdd($this->uid, $hdscore);
                $Act6200Model = Master::getAct6200($this->uid);
                $Act6200Model -> add_day($rule['id'],$hdscore);
            }
        }
    }

    public function back_hdcfg(){
        if (!empty($this->hd_cfg['rwd'])){
            Master::back_data($this->uid,'glqdhuodong','cbrwd',$this->hd_cfg['rwd']);
        }
    }

    /*
     * 返回活动信息
     * 使这个函数 无效
     */
    public function back_data(){
        return;
    }

    public function get_news(){

        $news = 0; //不可领取
        return $news;
    }

}
