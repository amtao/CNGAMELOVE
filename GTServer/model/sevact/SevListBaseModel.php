<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 2017/6/30
 * Time: 13:50
 */
require_once "SevBaseModel.php";
class SevListBaseModel extends SevBaseModel{
    //所在数据位置
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//返回信息 所在控制器

    const MAX_CHAT_NUM = 100;//最大内部保存数量
    protected $_max_chat_num = 100;///最大内部保存数量
    protected $_delete_cache_when_save = false;
    public $chat_info_num = 20;//初始/自动 发送条数
    public $chat_history_num = 10;//每次历史滚动条数
    public $_init = array(
    );//初始化数据

    public function __construct($hid,$cid, $servid)
    {
        parent::__construct($hid,$cid, $servid);

    }

    /*
     * 构造输出
     */
    public function mk_outf(){
        $out_data = array();
        foreach ($this->info as $k => $v) {
        	$_data = $this->list_mk_outf($v);
        	$_data['id'] = $k+1;
            $out_data[$k+1] = $_data;
        }
        return $out_data;
    }

    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
        return $v_info;
    }

    /*
     * 添加一条信息
     */
    public function list_push($data, $infoKey = null){
        if (empty($infoKey)) {
            $this->info[] = $data;
        } else {
            $this->info[$infoKey] = $data;
        }

        //截取最大数量
        //截取数据表
        if ($this->_max_chat_num > 0 && count($this->info) > $this->_max_chat_num){
            $this->info = array_slice($this->info,-$this->_max_chat_num,$this->_max_chat_num,1);
        }
        $this->save();
    }

    /**
     * 列表类 用户滚动信息
     * 初始化用户信息
     * @param $uid
     */
    public function list_init($uid){
        $this->set_user_list_id($uid, 0);

    }

    /**
     * 列表类 用户滚动信息
     * 检查更新输出
     * @param $uid
     * @param $model
     * @param $act
     * @return array
     */
    public function list_click($uid){
        //所有信息
        $data = $this->get_outf();
        if (empty($data)){
            return array();
        }

		$out_put = array();
		end($data);
        $now_id = key($data);//把指针指向最后一个key

        //验证now_id值
        $this->_check_now_id($now_id);

        //用户当前key
        $u_lid = $this->get_user_list_id($uid);
        //最多刷新 self::chat_info_num 条信息 从下向上取
     	
        for($i=0; $i < $this->chat_info_num; $i++){
            if ($u_lid >= $now_id - $i){
                break;
            }
            $out_put[] = $data[$now_id - $i];
        }
        //如果有输出 则改变用户序列ID
        if (!empty($out_put)){
            //写入ID
            $this->set_user_list_id($uid,$now_id);
        }
        Master::back_data($uid,$this->b_mol,$this->b_ctrl,$out_put,true);
    }
    
    /**
     * 列表类 获取历史消息
     */
    public function list_history($uid,$id){
        
    	//所有信息
        $data = $this->get_outf();
        
        $out_put = array();
        for ($i = 0 ; $i < $this->chat_history_num ; $i++){
        	if(empty($data[$id - $this->chat_history_num + $i])){
        		continue;
        	}else{
        		$out_put[] = $data[$id - $this->chat_history_num + $i];
        	}
        }
    	Master::back_data($uid,$this->b_mol,$this->b_ctrl,$out_put,true);
    }
    
    /**
     * 功能特殊操作
     * 聊天类 删除个人聊天信息
     */
    public function delete_msg($uid){
        foreach ($this->info as &$v){
            if ($v['uid'] == $uid){
                $v['msg'] = "****";
            }
        }
        $this->save();
    }
    

    /**
     * 获取用户列表ID
     *
     */
    protected function get_user_list_id($uid){
        $cache = Common::getDftMem();
        $data = $cache->get($this->user_list_key($uid));
        if (empty($data)){
            return 1;
        }
        return $data['id'];
    }

    /**
     * 设置用户列表ID
     * @param $uid
     * @param $id
     * @return mixed
     */
    protected function set_user_list_id($uid, $id){
        $cache = Common::getDftMem();
        $data = array(
            'id' => $id,
            'time' => Game::get_now(),
        );
        $cache->set($this->user_list_key($uid), $data);
        return $id;
    }

    /**
     * 获取用户列表缓存KEY
     * $act   活动标签
     * $hid   活动重置id
     * $cid   活动分组id
     */
    private function user_list_key($uid){
        return 'actlist_'.$this->act.'_'.$this->cid.'_'.$this->hid.'_'.$uid;
    }

    /**
     * 检查缓存里的now_id是否和实际的now_id是否一致
     * @param $now_id
     */
    protected function _check_now_id($now_id)
    {
    }
}