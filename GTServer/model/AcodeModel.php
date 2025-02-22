<?php
//兑换码表
require_once "AModel.php";
class AcodeModel extends AModel{
    public $_key = "_acode";
    public $info;
    public $all_cfg;
    public $base_cfg;
    public $cfg;
    public $outf;
    public $b_mol = "recode";//返回信息 所在模块
    public $b_ctrl = "exchange";//返回信息 所在控制器
    protected  $updateSetKey = array(
        'acode','uid','utime'
    );
    
    protected  $updateAddKey = array(
        'acode','act_key','type','serid','ctime'
    );
    
    public function __construct($uid) {
        parent::__construct($uid);
    }
    /*
     * 兑换
     * */
    public function exchange($key){
        //指定cdkey的兑换码
        $gift_cfg = Game::get_peizhi('public_gift');
        if(!empty($gift_cfg)){
            $Act38Model = Master::getAct38($this->uid);
            foreach ($gift_cfg as $gift){
                if($gift['key'] == $key && Game::is_over(strtotime($gift['stime'])) 
                    && Game::dis_over(strtotime($gift['etime'])) && empty($Act38Model->info[$gift['id']])){
                    $this->cfg['items'] = $gift['items'];
                    $Act38Model->save_acode($gift['id'],$key);
                    break;
                }
            }
        }
        if(!empty($this->cfg)){
            foreach ($this->cfg['items'] as $val){
                Master::add_item($this->uid,$val['kind'],$val['id'],$val['count']);
            }
            $this->make_out();
        }else{//其他
            self::redeemCode($key);
        }
    }
    
    /*
     * 普通兑换码兑换
     * */
    public function redeemCode($key){
        //查询key的记录是否存在
        Common::loadModel('ServerModel');
        $servcfg = Common::getSevidCfg();
        $my_servid = $servcfg['sevid'];//当前服务器id
        $serverid = ServerModel::getDefaultServerId();//默认服务器id
        $db = Common::getDbBySevId($serverid);
        $sql = "select * from `acode` where `acode`='{$key}'";
        $info = $db->fetchRow($sql);
        if(empty($info)){
            Master::error(ACODE_HAS_THE_FAILURE);
        }

        Common::loadModel('AcodeTypeModel');
        $AcodeTypeModel = new AcodeTypeModel();
        $cfg = $AcodeTypeModel->getvalue($info['act_key']);
        $this->cfg = $cfg;
        if(empty($this->cfg)){
            Master::error(ACODE_HAS_THE_FAILURE);
        }
        if(Game::dis_over($this->cfg['sTime'])){
            Master::error(ACODE_UN_START);
        }
        if(Game::is_over($this->cfg['eTime'])){
            Master::error(ACODE_OVERDUE);
        }
        if($this->cfg['isdel'] == 1){
            Master::error(ACODE_HAS_THE_FAILURE);
        }
        $this->SevJudge($my_servid);//判读服务器是否正常
        if(!empty($info['uid'])){
            Master::error(ACODE_HAS_LINGQU);
        }
        switch ($this->cfg['type']){
            case 1://普通兑换码
            case 2://单服 普通兑换码 现在已弃用
            case 4:// 多用兑换码 同一个act_key下的兑换码用户可以多次使用

                //查询当前用户是否领取过这类型的
                $info_sql = 'select acode from `acode` where `act_key`=\''.$info['act_key'].'\' and `uid`='.$this->uid;
                $userinfo = $db->fetchRow($info_sql);
                
                if(!empty($userinfo) && $this->cfg['type']!=4){
                    Master::error(ACODE_HAVE_RECEIVE);
                }
                //更新操作
                $update_sql = 'update `acode` set `uid`='.$this->uid.',`utime`='.$_SERVER['REQUEST_TIME'].' where id='.$info['id'].' and acode=\''.$info['acode'].'\'';
                if($db->query($update_sql) === false){
                    Master::error(ACODE_EXCHANGE_FAILURE);
                }
                break;
            case 3://通用兑换码 同一个key每个用户都可以使用
                $Act96Model = Master::getAct96($this->uid);
                $Act96Model->save_acode($info['act_key'],$key);
                break;
            case 5://周礼包
                $Act400Model = Master::getAct400($this->uid);
                $Act400Model->save_acode($this->cfg['type'],$info['act_key'], $key);
                //更新操作
                $update_sql = 'update `acode` set `uid`='.$this->uid.',`utime`='.$_SERVER['REQUEST_TIME'].' where id='.$info['id'].' and acode=\''.$info['acode'].'\'';
                if($db->query($update_sql) === false){
                    Master::error(ACODE_EXCHANGE_FAILURE);
                }
                break;
        }
        //兑换成功获得礼品
        foreach ($this->cfg['items'] as $val){
            Master::add_item($this->uid,$val['kind'],$val['id'],$val['count']);
        }
        $this->make_out();
    }
    /*
     * 服务器判断
     * 
     * */
    public function SevJudge($my_servid){
        if($this->cfg['sever']!='all'){//不是全服的进行判断
            //切割数组
            $sev = $this->StrTransSev($this->cfg['sever']);
            if($sev[1] == 0 && $sev[0] !=$my_servid){//当为单服
                Master::error(ACODE_HAS_THE_FAILURE);
            }else if($sev[1] != 0 && ($sev[0]>$my_servid || $sev[1]<$my_servid)){
                Master::error(ACODE_HAS_THE_FAILURE);
            }
        }
    }
    /*
     * 服务器切割
     * @return array(0=>1,1=>999)   第一个数值为起始服务器  第二个数值为结束服务器
     * */
    public function StrTransSev($str){
        $sev[] = intval(substr($str,4,3));
        $sev[] = intval(substr($str,1,3));
        return $sev;
    }
    /*
     * 构造输出函数
     * */
    public function make_out() {//兑换成功或者失败
        $out = array();
        if($this->cfg){
            foreach ($this->cfg['items'] as $val){
                if($val['kind'] == 1){//道具
                    $out['items'][] = $val;
                }else{
                    $out['newnpc'][] = $val;
                }
            }
        }
        $this->outf = $out;
    }
    
    /*
     * 返回活动信息
     */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }
}