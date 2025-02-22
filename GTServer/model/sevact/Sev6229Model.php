<?php
/*
 * 植树节
 */
require_once "SevBaseModel.php";
class Sev6229Model extends SevBaseModel
{
    public $comment = "劳动节-公用数据";
    public $act = 6229;
    public $b_mol = 'laborDay';
    public $hd_id = 'huodong_6229';//活动配置文件关键字
    public $hd_cfg;
    public $pkIDs = array();       //阵营id

    //初始化数据
    public $_init = array(

    );

    /**
     * 赛况
     */
    public function outs()
    {
        $arr = array(
            array(0,1),
            array(1,0),
        );
        $h = date('H',$_SERVER['REQUEST_TIME']);
        if (empty($this->info['date']) || $this->info['date']!=$h){
            $this->info['date'] = $h;
            $this->info['set'] = $arr[rand(0,1)];
            $this->info['log'][] = array('h'=>$h,'s'=>$this->info['set']);
            $this->save();
        }
        return $this->info['set'];
    }

    /**
     * 设置胜利门客编号
     * @return bool
     */
    public function setWinID()
    {
        $state = $this->get_state();
        $this->get_heroIds();
        if ($state == 2 && empty($this->info['winID'])){
            //阵营1总积分
            $Redis6219Model = Master::getRedis6223($this->hd_cfg['info']['id']);
            $camp1 = (int)$Redis6219Model->zSum();
            //阵营2总积分
            $Redis6220Model = Master::getRedis6224($this->hd_cfg['info']['id']);
            $camp2= (int)$Redis6220Model->zSum();

            if ($camp1 > $camp2){//id小的为胜方
                $this->info['index'] = 1;
                $this->info['winID'] = min($this->pkIDs);

            }elseif ($camp1 < $camp2){//id大的为胜方
                $this->info['index'] = 2;
                $this->info['winID'] = max($this->pkIDs);
            }else{//如果平局 id小的为胜方 随机增加3-10
                $this->info['index'] = 1;
                $this->info['add'] = rand(3,10);
                $this->info['winID'] = min($this->pkIDs);
            }
            $this->save();
        }
    }

    /**
     * 获取对决的门客id存在heroIds里
     */
    public function get_heroIds()
    {

        foreach($this->hd_cfg['set'] as $val) {
            array_push($this->pkIDs,$val['pkID']);
        }
    }

    /**
     * 活动活动状态
     * 返回:
     * 0: 活动未开启
     * 1: 活动中
     * 2: 活动结束,展示中
     */
    public function get_state(){
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        $state = 0;  //活动未开启
        if(!empty($this->hd_cfg) ){

            if(Game::dis_over($this->hd_cfg['info']['showTime'])){
                $state = 2;  //活动结束,展示中
            }
            if(Game::dis_over($this->hd_cfg['info']['eTime'])){
                $state = 1;  //活动中
            }
        }
        return $state;
    }

    /*
     * 返回协议信息
     */
    public function bake_data(){

    }

}