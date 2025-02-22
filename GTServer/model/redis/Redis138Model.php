<?php
require_once "RedisBaseModel.php";
/**
 * 跨服亲密冲榜--区间 pk区服 排行榜  (区服为单位)   =>   整个区奖励
 */
class Redis138Model extends RedisBaseModel
{
	public $comment = "跨服亲密冲榜-区服";
	public $act = 'huodong_314_serv';//活动标签
    protected $_server_type = 6;

    public $b_mol = "kuacbhuodong";//返回信息 所在模块
    public $b_ctrl = "qufulovelist";//返回信息 所在控制器

    public $hd_id = 'huodong_314';//活动配置文件关键字
    private $kua_zhufu ;//活动配置
    protected $_with_decimal_sort = true;//加小数排序
    protected $hd_cfg;
    /**
     * @param $act  活动标签
     * @param $out_start  常规输出范围 从第几个开始 下标从1开始
     * @param $out_num  常规输出范围 要获取几个
     */
    public function __construct($key = '')
    {

        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        $this->kua_zhufu = Game::kua_lovezhufu($this->hd_cfg['need']['serv']);

        if(!empty($this->kua_zhufu['zhufu'])){
            parent::__construct($key,$this->kua_zhufu['zhufu']);
        }
        $this->_with_decimal_denominator = Game::get_now() - $this->hd_cfg['info']['id'];

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


    /**
     * 获取单个势力的信息
     * @param $member  势力id
     * @param $rid   排名id
     */
    public function getMember($member,$rid){

        $Sev_Cfg = Common::getSevidCfg($member);//子服ID
        //获取公共基础信息
        $cinfo = array(
            'uid' => $Sev_Cfg['he'],
            'serv' => $Sev_Cfg['he'],
            'rid' => $rid,
            'score' => intval(parent::zScore($Sev_Cfg['he'])),
        );
        return $cinfo;
    }

    /*
     * 返回我的排行信息
     */
    public function back_data_my($member){

        $rid = 100001;
        $score = 0;
        $serv = Game::get_sevid($member);
        $Sev_Cfg = Common::getSevidCfg($serv);//子服ID
        if(!empty($member)){
            $rid = parent::get_rank_id($Sev_Cfg['he']);
            $score = intval(parent::zScore($Sev_Cfg['he']));
        }

        Master::back_data(0,$this->b_mol,"mykuaquloveRid",array(
            'uid' => $member,
            'serv' => $Sev_Cfg['he'],
            'rid' => $rid,
            'score' => $score,
        ));
    }

}

