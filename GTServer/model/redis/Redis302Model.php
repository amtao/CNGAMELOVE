<?php
require_once "RedisBaseModel.php";
/**
 * 跨服联盟排行榜
 */
class Redis302Model extends RedisBaseModel
{
	public $comment = "跨服联盟排行榜";
	public $act = 'huodong_302';//活动标签
    public $out_time = 600;//输出缓存过期时间
    protected $_server_type = 3;//1：合服，2：跨服，3：全服
    protected $_with_decimal_sort = true;//加小数排序

    public $b_mol = "ranking";//返回信息 所在模块
    public $b_ctrl = "clubKua";//返回信息 所在控制器
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1505232000;
    }
    /**
     * 获取单个联盟的信息
     * @param $member  联盟id
     * @param $rid   排名id
     */
    public function getMember($member,$rid){

        $ClubModel = Master::getClub($member);
        //获取公共基础信息
        $cinfo = $ClubModel->getSimple();

        $cinfo['rid'] = $rid; //联盟排名
        $cinfo['allShiLi'] = 0; //总势力
        foreach($cinfo['members'] as &$info){
            $cinfo['allShiLi'] += $info['shili'];
            $info = array(
                'id'=>$info['id'],
                'name'=>$info['name'],
                'post'=>$info['post'],
            );
        }
        return $cinfo;
    }
}