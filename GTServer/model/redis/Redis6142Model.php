<?php
require_once "RedisBaseModel.php";
/*
 * 盛装出行排行榜
 */
class Redis6142Model extends RedisBaseModel
{
    public $comment = "盛装出行排行榜";
    public $act = 'huodong_6142';//活动标签
    public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
    public $out_num = 20;//常规输出范围 要获取几个
    public $b_mol = "clothepvp";//返回信息 所在模块
    public $b_ctrl = "ranklist";//返回信息 所在控制器
    protected $_with_decimal_sort = true;//加小数排序
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1506528000;
    }

    /*
     * 初始化结构体
     */
    public $_init = array(
        /*
        'id' => 0,  //玩家UID
        'name' => 0,  //名字
        'pkID' => 0,  //伙伴ID
        'rid'	=> 0, //排名
    */
    );

    //获取个人信息
    public function getMember($member,$rid){
        //玩家信息
        $fuidInfo = Master::fuidInfo($member);
        if ($rid < 4){
            $Act6142Model = Master::getAct6142($member);
            $v_info = $Act6142Model->info;
            $fuidInfo['clothe']['head'] = $v_info['head'];
            $fuidInfo['clothe']['body'] = $v_info['body'];
            $fuidInfo['clothe']['ear'] = $v_info['ear'];
            $fuidInfo['clothe']['background'] = $v_info['background'];
            $fuidInfo['clothe']['effect'] = $v_info['effect'];
            $fuidInfo['clothe']['animal'] = $v_info['animal'];
        }
        //玩家排名
        $fuidInfo['rid'] = $rid;
        //获取分值
        $fuidInfo['num'] = intval($this->zScore($member));

        return $fuidInfo;
    }

    /*
     * 返回我的积分信息
     */
    public function back_data_my($uid){
        //返回我的总伤害 //返回我的排名
        $UserModel = Master::getUser($uid);
        $data = array(
            'name' => $UserModel->info['name'],
            'score' => intval($this->zScore($uid)),
            'rid' => $this->get_rank_id($uid),
        );
        Master::back_data(0,$this->b_mol,'myScore',$data);
    }

    /*
	 * 随机一个对战玩家
	 */
    public function rand_f_uid($myuid){
        //获取我的名次
        $my_rank = $this->get_rank_id($myuid);
        if (empty($my_rank)){
            return;
        }

        $size = $this->sSize();
        $r_start = rand(1, $size);
        if ($r_start == $my_rank){
            $r_start = rand(1,$size);
        }

        $uid = $this->get_member($r_start);
        //获取 uid => 分值列表
        $uis_scores = $this->azRange($r_start-50 < 0?1:$r_start-50,$r_start+50 > $size?$size:$r_start+50);

        // 查找和删掉自己
        unset($uis_scores[array_search($myuid,$uis_scores)]);
        unset($uis_scores[array_search($uid,$uis_scores)]);

        if(empty($uis_scores)){
            return array('uid1'=>$uid,'uid2'=>$myuid);
        }
        $uid1 = $uis_scores[array_rand($uis_scores,1)];

        return array('uid1'=>$uid,'uid2'=>$uid1);
    }

}