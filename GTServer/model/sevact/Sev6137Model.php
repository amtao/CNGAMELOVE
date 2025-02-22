<?php
/*
 * 门客对决
 */
require_once "SevBaseModel.php";
class Sev6137Model extends SevBaseModel
{
    public $comment = "皇子应援活动- 胜负";
    public $act = 6137;
    public $b_mol = 'yyhuodong';
    public $hd_id = 'huodong_6136';//活动配置文件关键字
    public $hd_cfg;

    //初始化数据
    public $_init = array(
        /**
         * 'pk'=>array(
         * 'small'=>0,
         * 'big'=>1
         * ),
         * 'winID'=>0,   //默认为0，还没结算，其他数字就是获胜的门客id
         */
        'WinRank_contribution'=>0,
        'LostRank_contribution'=>0,
        'winID'=>0,
    );

    /*
	 * 构造业务输出数据
	 */
    public function mk_outf(){
        $outf = array(
            'WinRank_contribution'=>0,
            'LostRank_contribution'=>0,
            'winID'=>0,
        );
        if (!empty($this->info['winID'])){
            return $this->info;
        }else{
            return $outf;
        }

    }

    public function getWinID()
    {
        return !empty($this->info['winID']) ? $this->info['winID'] : 0;
    }
    /**
     * 设置胜利门客编号
     * @return bool
     */
    public function setWinID()
    {
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);

        if (empty($this->info['winID'])){
            $pkIDs = $this->getPKIDs();

            $Redis6113Model = Master::getRedis6113($this->hd_cfg['info']['id']);
            $smallTotal = (int)$Redis6113Model->zSum();
            $Redis6114Model = Master::getRedis6114($this->hd_cfg['info']['id']);
            $bigTotal= (int)$Redis6114Model->zSum();

            if ($smallTotal > $bigTotal){
                $this->info['winID']=min($pkIDs);
                $this->info['WinRank_contribution']=$smallTotal;
                $this->info['LostRank_contribution']=$bigTotal;

            }else{
                $this->info['winID']=max($pkIDs);
                $this->info['WinRank_contribution']=$bigTotal;
                $this->info['LostRank_contribution']=$smallTotal;
            }

            $this->save();
        }


    }
    public function getPKIDs()
    {
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        $pkIDs = array();
        foreach ($this->hd_cfg['set']['pk'] as $val) {
            array_push($pkIDs,$val['pkID']);
        }
        return $pkIDs;
    }

    /*
     * 返回协议信息
     */
    public function bake_data(){
        $data = self::mk_outf();
        Master::back_data(0,'yyhuodong','VictoryOrDefeat',$data);
    }

}