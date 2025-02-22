<?php
/*
 * 世界BOSS 葛二蛋
 */
require_once "SevListBaseModel.php";
class Sev6013Model extends SevListBaseModel
{
	public $comment = "本服聊天滞留";

    public $b_mol = "chat";//返回信息 所在模块
    public $b_ctrl = "laba";//返回信息 所在控制器
    public $act = 6013;//活动标签
    protected $_use_lock = false;//是否加锁

    const MAX_CHAT_NUM = 1;//最大内部保存数量
    protected $_max_chat_num = 1;///最大内部保存数量
    protected $_delete_cache_when_save = false;
    public $chat_info_num = 1;//初始/自动 发送条数
    public $chat_history_num = 1;//每次历史滚动条数

    public $_init = array(//初始化数据
        /*
         * array(
         *  'uid' => 10086,
         *  'type' => 1,//类型1 普通 2 红字 3 系统通告
         *  'msg' => ''//内容
         *  'time ' => now
         * ),
         */
    );

    /*
     * 添加一条信息
     */
    public function add_msg($uid,$msg){
        $isGM = 0;  //默认不是
        //判断是不是官方
        $sev35Model = Master::getSev35();
        if (!empty($sev35Model->info) && in_array($uid, $sev35Model->info)){
            $isGM = 1;
        }

        $data = array(
            'uid' => $uid,
            'msg' => $msg,
            'time' => Game::get_now(),
            'isGM' => $isGM,
        );
        parent::list_push($data);
    }

    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
        $data = array(
            'msg' => $v_info['msg'],
            'time' => $v_info['time'],
            'isGM' => $v_info['isGM']?$v_info['isGM']:0,
        );
        if (isset($v_info['user'])){
            $data['user'] = $v_info['user'];
        } else {
            $data['user'] = Master::fuidInfo($v_info['uid']);
        }
        return $data;
    }
}
