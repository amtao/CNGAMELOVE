<?php
require_once "RedisBaseModel.php";
/**
 * 跨服势力冲榜--区间 pk区服 单人排行榜 (单人为一个单位)  =>个人奖励
 */
class Redis131Model extends RedisBaseModel
{
	public $comment = "跨服势力冲榜-单人";
	public $act = 'huodong_313_user';//活动标签
    protected $_server_type = 6;

    public $b_mol = "kuacbhuodong";//返回信息 所在模块
    public $b_ctrl = "userlist";//返回信息 所在控制器

    public $hd_id = 'huodong_313';//活动配置文件关键字
    private $kua_zhufu ;//活动配置

    /**
     * @param $act  活动标签
     * @param $out_start  常规输出范围 从第几个开始 下标从1开始
     * @param $out_num  常规输出范围 要获取几个
     */
    public function __construct($key = '')
    {

        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        $this->kua_zhufu = Game::kua_zhufu($this->hd_cfg['need']['serv']);
        if(!empty($this->kua_zhufu['zhufu'])){
            parent::__construct($key,$this->kua_zhufu['zhufu']);
        }
        $this->_with_decimal_denominator = time() - 1505232000;


        //活动 - 榜单key
        $this->key = $this->getkey();
        //活动 - 榜单key - 缓存
        $this->keyMsg = $this->getkeyMsg();
    }

    /*
         * 初始化结构体
         */
    public $_init = array(
        /*
            uid => 势力数
        */
    );

    /*
     * 返回第一名信息
     * */
    public function back_data_first(){
        $fuid = $this->get_member(1);
        if(empty($fuid))return array();
        $Sev_Cfg = Common::getSevidCfg(Game::get_sevid($fuid));//子服ID
        Common::loadModel('ServerModel');
        $serverInfo = ServerModel::getServInfo($Sev_Cfg['he']);
        $serverZh = $serverInfo['name']['zh'];
        $serverName = explode('|',$serverZh);
        //获取公共基础信息
        $cinfo = Master::fuidData($fuid);
        $cinfo['sevname'] = $serverName[1];
        return $cinfo;
    }

    /**
     * 获取单个势力的信息
     * @param $member  势力id
     * @param $rid   排名id
     */
    public function getMember($member,$rid){

        $UserModel = Master::getUser($member);

        $Sev_Cfg = Common::getSevidCfg(Game::get_sevid($member));//子服ID

        //获取公共基础信息
        $cinfo = array(
            'uid' => $member,
            'serv' => $Sev_Cfg['he'],
            'name' => $UserModel->info['name'],
            'rid' => $rid,
            'score' => intval(parent::zScore($member)),
        );
        return $cinfo;
    }

    /*
     * 返回我的排行信息
     */
    public function back_data_my($member){
        $name = RANK_NO_NAME;
        $rid = 100001;
        $score = 0;

        if(!empty($member)){
            $rid = parent::get_rank_id($member);
            $score = intval(parent::zScore($member));
            $UserModel = Master::getUser($member);
            $name = $UserModel->info['name'];
        }
        $Sev_Cfg = Common::getSevidCfg(Game::get_sevid($member));//子服ID
        Master::back_data(0,$this->b_mol,"mykuashiliRid",array(
            "rid"=>$rid,
            'uid' => $member,
            'serv' => $Sev_Cfg['he'],
            'score' => $score,
            'name' => $name,
        ));
    }

}

