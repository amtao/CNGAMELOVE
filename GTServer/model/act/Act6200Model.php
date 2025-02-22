<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6200
 */
class Act6200Model extends ActHDBaseModel
{
    public $atype = 6200;//活动编号
    public $comment = "国力庆典-每日总排行";
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//子类配置
    public $hd_id = 'huodong_6200';//活动配置文件关键字
    /*
	 * 初始化结构体
	 */
    public $_init =  array(

    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add_day($type,$num){
        if( self::get_state() == 1 ){
            $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
            $this->info[$riqi][$type] = $num;
            $this->save();
            $total = array_sum($this->info[$riqi]);
            //保存到排行榜中
            $Redis6200Model = Master::getRedis6200($this->_get_day_redis_id());
            $paihang = intval($Redis6200Model->zScore($this->uid));
            if ($total > $paihang){
                $diff = $total - $paihang;
                $Redis6200Model->zAdd($this->uid, $total);
                $Act6187Model = Master::getAct6187($this->uid);
                $Act6187Model -> add_total($type,$diff);
            }
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
