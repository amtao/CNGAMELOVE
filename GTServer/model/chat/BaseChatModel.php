<?php
require_once MOD_DIR . '/BModel.php';
class BaseChatModel extends BModel
{
    protected $_b_mol = null;
    protected $_b_ctrl = null;
    protected $_type = null;//聊天类型
    protected $_max_chat_num = 100;
    protected $_page_max_num = 20;
    protected $_history_max_num = 10;
    protected $_key_suffix = "";
    protected $new_put = array();
    
	public function __construct($serverID = null)
	{
	    if (is_null($this->_type)) {
            Master::error(get_class($this).'_type_null');
        }
        parent::__construct($serverID);
	}
    /**
     * 重置列表输出
     * @param $uid
     */
	public function listReset()
    {
        $size = $this->_getMaxID() - $this->_page_max_num;
        $size = $size <= 0 ? 0 : $size;
        $this->_set_user_list_last($size);
    }
    /**
     * 输出聊天记录，只取20个
     * @param $uid
     */
	public function listCheck($uid,$friendUID)
    {
        $last = $this->_get_user_list_last();
        $start = $last - $this->_getIDStep();
        $list = $this->_getRedis()->zRange(
            $this->_getKey(),
            $start, ($start + $this->_page_max_num),
            true
        );
        $out_put = array();
        foreach ($list as $k => $v) {
            $out_put[] = json_decode($k, true);
        }
        if (!empty($out_put)) {
            $this->_set_user_list_last(($last + count($list)));
        }
        
        $this->new_put[] = array(
        	'sllist'  => $out_put,
        	'id'    => $friendUID,
        );
    }
    
    /**
     * 输出聊天记录，只取20个
     * @param $uid
     */
	public function back_data_au($uid,$flag = false){
		Master::back_data($uid, $this->_b_mol, $this->_b_ctrl, $this->new_put, $flag);
		
	}

    /**
     * ID 序号和最大条数据的偏移量
     * @return int
     */
	protected function _getIDStep()
    {
        $last = $this->_getRedis()->zRevRange($this->_getKey(), 0, 0);
        $step = 0;
        if (!empty($last[0])) {
            $last = json_decode($last[0], true);
            $step = $last['id'] - $this->_max_chat_num;//偏移量
        }
        return $step > 0 ? $step : 0;
    }

    protected function _getMaxID()
    {
        $last = $this->_getRedis()->zRevRange($this->_getKey(), 0, 0);
        if (!empty($last[0])) {
            $last = json_decode($last[0], true);
            return $last['id'];
        }
        return 0;
    }
    
    /**
     * 输出历史列表
     * @param $uid
     * @param $id
     */
    public function listHistory($uid,$friendUID, $id)
    {
        $id -= $this->_getIDStep();
    	$start = $id - $this->_history_max_num;
        $id -= 1;
        if ($id <= 0) {
            Master::back_data($uid, $this->_b_mol, $this->_b_ctrl, array(), true);
            return;
        }
        $start = $start < 0 ? 0 : $start;
        
        $list = $this->_getRedis()->zRange(
            $this->_getKey(),
            $start, $id,
            true
        );
        
        $out_put = array();
        foreach ($list as $k => $v) {
            $out_put[] = json_decode($k, true);
        }
        
        $this->new_put[] = array(
        	'sllist'  => $out_put,
        	'id'    => $friendUID,
        );
        $this->back_data_au($uid,true);
    }
    /**
     * 添加聊天记录
     * @param $uid
     * @param $msg
     */
	public function addMsg($uid, $msg)
    {
        if($this->_getRedis()->zSize($this->_getKey()) > $this->_max_chat_num ) {
            //删除多余的
            $this->_getRedis()->zDeleteRangeByRank($this->_getKey(), 0, 0);
        }
        //以时间做排序
        $this->_getRedis()->zAdd(
            $this->_getKey(),
            microtime(true) * 10000,
            json_encode($this->_formatData($uid, $msg), JSON_UNESCAPED_UNICODE)
        );
    }
    protected function _formatData($uid, $msg)
    {
        $last = $this->_getRedis()->zRevRange($this->_getKey(), 0, 0);
        $id = 1;
        if (!empty($last[0])) {
            $last = json_decode($last[0], true);
            $id = $last['id'] + 1;
        }
        return array(
            'id' => $id,
            'user' => Master::fuidInfo($uid),
            'uid' => $uid,
            'type' => $this->_type,
            'msg' => $msg,
            'time' => Game::get_now(),
        );
    }
    protected function _getKey()
    {
        return 'Chat_'.$this->_server_type.'_'.$this->_key_suffix;
    }
    protected function _get_user_list_last()
    {
        $data = $this->_getCache()->get($this->_user_list_key());
        if (empty($data)) {
            return 0;
        }
        return $data['last'];
    }
    protected function _set_user_list_last($last = 0)
    {
        $data = array(
            'last' => $last,
            'time' => Game::get_now(),
        );
        $this->_getCache()->set($this->_user_list_key(), $data);
    }
    protected function _user_list_key()
    {
        return 'Chat_last_'.$this->_server_type.'_'.$this->_key_suffix;
    }
}
