<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6240
 */
class Act6240Model extends ActHDBaseModel
{
    public $atype = 6240;//活动编号
    public $comment = "跨服兑换商城";
    public $b_mol = "kuacbhuodong";//返回信息 所在模块
    public $b_ctrl = "fengxiandian";//子类配置
    public $hd_id = 'huodong_6240';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'exchange' => array(),
    );

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        $info = $this->info;
        $ItemModel = Master::getItem($this->uid);
        foreach($this->hd_cfg['exchange'] as $rwd){
            //验证道具是否充足
            $id = $rwd['id'];
            $need = $rwd['items'][0];
            if ($info['duihuan'][$id] < $rwd['count'] && $ItemModel->sub_item($need['id'],$rwd['count'],true)){
                return 1;
            }
        }
        return $news;
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            return;
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);

        $info = $this->info['exchange'];
        foreach($hd_cfg['exchange'] as $k => $rwd){
            $hd_cfg['exchange'][$k]['buy'] = empty($info[$rwd['id']])?0:$info[$rwd['id']];
        }
        $this->outf = $hd_cfg;
    }

    /**
     * 兑换
     * $id
     */
    public function exchange($id = 0){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $buy_count = floor($id / 10000);
        if ($buy_count <= 0)return;
        $id = $id % 10000;
        if ($buy_count == 0)Master::error();
        foreach($this->hd_cfg['exchange'] as $rwd){
            if ($rwd['id'] == $id){
                $c = empty($this->info['exchange'][$id])?0:$this->info['exchange'][$id];
                if ($c + $buy_count > $rwd['count'] && $rwd['count'] != 0){
                    Master::error();
                }
                $item = $rwd['items'][0];
                $ItemModel = Master::getItem($this->uid);
                $ItemModel->sub_item($item['id'], $item['count'] * $buy_count);
                $this->info['exchange'][$id] = $c + $buy_count;
                $item = $rwd['items'][1];
                $item['count'] = $item['count'] * $buy_count;
                Master::add_item2($item);
                $this->save();
                break;
            }
        }
    }

    /*
	 * 返回活动详细信息
	 */
    public function back_data_hd(){
        if( empty($this->outf) ){
            $this->outf = array();
        }
        $hd_state = $this->get_state();
        //活动状态
        if( $hd_state == 0){
            return;
        }
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

}

