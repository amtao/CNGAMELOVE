<?php
require_once "RedisBaseModel.php";
/**
 * 跨服势力冲榜--区间 排行榜  (区服为单位)   =>   本次匹配期间
 */
class Redis134Model extends RedisBaseModel
{
	public $comment = "跨服势力冲榜";
	public $act = 'huodong_313_pk';//活动标签

    protected $_server_type = 6;

    public $b_mol = "kuacbhuodong";//返回信息 所在模块
    public $b_ctrl = "pkqufulist";//返回信息 所在控制器

    public $hd_id = 'huodong_313';//活动配置文件关键字
    private $kua_allzhufu ;//活动配置


    /**
     * @param $act  活动标签
     * @param $out_start  常规输出范围 从第几个开始 下标从1开始
     * @param $out_num  常规输出范围 要获取几个
     */
    public function __construct($key = '')  //$key : 服务器id
    {

        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        $this->kua_allzhufu = Game::kua_all_zhufu($this->hd_cfg['need']['serv'],$key);

        if(!empty($this->kua_allzhufu)){
            parent::__construct($this->hd_cfg['info']['id'],$this->kua_allzhufu);

            if($key != ''){
                $this->act .= '_'.$this->kua_allzhufu;  //  huodong_313_pk_活动id_合服id
            }
            //活动 - 榜单key
            $this->key = $this->getkey();
            //活动 - 榜单key - 缓存
            $this->keyMsg = $this->getkeyMsg();

        }
    }

    /**
     * @param $inservid  : 合服id
     * 跨服势力冲榜 服务区资格
     */
    public function comein_pk($inservid){

        if(!empty($this->kua_allzhufu)){
            $Redis133Model = Master::getRedis133();
            $score = $Redis133Model->zScore($inservid);
            $score = empty($score) ? 999 - $inservid : $score;  //作用: 第一次参加 靠近附近的区
            parent::zAdd($inservid,$score);
        }
    }


}

