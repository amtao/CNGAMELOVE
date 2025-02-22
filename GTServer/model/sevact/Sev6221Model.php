<?php
/*
 * 植树节
 */
require_once "SevBaseModel.php";
class Sev6221Model extends SevBaseModel
{
    public $comment = "植树节- 胜负";
    public $act = 6221;
    public $b_mol = 'arborday';
    public $hd_id = 'huodong_6221';//活动配置文件关键字
    public $hd_cfg;
    public $pkIDs = array();       //阵营id

    //初始化数据
    public $_init = array(
        'winID'=>0,
    );

    /*
	 * 构造业务输出数据
	 */
    public function mk_outf(){
        $this->get_hdcfg();
        $state = 0;  //活动未开启
        if(!empty($this->hd_cfg) ){
            if(Game::dis_over($this->hd_cfg['info']['showTime'])){
                $state = 2;  //活动结束,展示中
            }
            if(Game::dis_over($this->hd_cfg['info']['eTime'])){
                $state = 1;  //活动中
            }
        }
        if ($state==2 && empty($this->info['winID'])){
            $this->setWinID();
        }
        if($this->info['winID'] != 0){
            $outf['winID'] = $this->info['winID'];
        }else{
            $outf['winID'] = 0;
        }
        return $outf;
    }

    /**
     * 设置胜利门客编号
     * @return bool
     */
    public function setWinID()
    {
        $this->get_hdcfg();
        $this->get_heroIds();
        if (empty($this->info['winID'])){
            //阵营1总积分
            $Redis6219Model = Master::getRedis6219($this->hd_cfg['info']['id']);
            $camp1 = (int)$Redis6219Model->zSum();
            //阵营2总积分
            $Redis6220Model = Master::getRedis6220($this->hd_cfg['info']['id']);
            $camp2= (int)$Redis6220Model->zSum();

            if ($camp1 > $camp2){//id小的为胜方
                $this->info['index'] = 1;
                $this->info['winID'] = min($this->pkIDs);

            }else{
                $this->info['index'] = 2;//id大的为胜方
                $this->info['winID'] = max($this->pkIDs);
            }
            if ($camp1 == $camp2){//如果平局 id小的为胜方 随机增加1-100
                $this->info['index'] = 1;
                $this->info['add'] = rand(3,10);
                $this->info['winID'] = min($this->pkIDs);
            }
            $this->save();
        }
    }

    /*
     * 返回协议信息
     */
    public function get_hdcfg()
    {
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
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

    /*
     * 返回协议信息
     */
    public function bake_data(){
        $data = self::mk_outf();
        Master::back_data(0,'arborday','outcome',$data);
    }

}