<?php
require_once "RedisBaseModel.php";
/**
 * 跨服亲密冲榜--区间 排行榜  (区服为单位)   =>   下次匹配
 */
class Redis139Model extends RedisBaseModel
{
	public $comment = "跨服亲密冲榜";
	public $act = 'huodong_314_all';//活动标签

    protected $_server_type = 6;

    public $b_mol = "kuacbhuodong";//返回信息 所在模块
    public $b_ctrl = "allqufulovelist";//返回信息 所在控制器

    public $hd_id = 'huodong_314';//活动配置文件关键字
    private $kua_allzhufu ;//活动配置

    /**
     * @param $act  活动标签
     * @param $out_start  常规输出范围 从第几个开始 下标从1开始
     * @param $out_num  常规输出范围 要获取几个
     */
    public function __construct($key = '')
    {

        Common::loadModel('ServerModel');
        $id = ServerModel::getDefaultServerId();
        parent::__construct($key,$id);

        if($key != ''){
            $this->act .= '_'.$key;
        }
        //活动 - 榜单key
        $this->key = $this->getkey();
        //活动 - 榜单key - 缓存
        $this->keyMsg = $this->getkeyMsg();

    }

}

