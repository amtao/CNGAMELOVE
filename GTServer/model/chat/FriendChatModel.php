<?php
require_once "BaseChatModel.php";
class FriendChatModel extends BaseChatModel
{
    protected $_b_mol = 'friends';//TODO 聊天信息model
    protected $_b_ctrl = 'fllist';//TODO 聊天ctrl
    protected $_notice_mol = 'friends';//TODO 红点model
    protected $_notice_ctrl = 'sltip';//TODO 红点ctrl
    protected $_type = 5;//聊天类型

    /**
     * 红点列表，登录时初始化调用
     * @param $uid
     */
    public function noticeList($uid, $fuid)
    {
        $out_put = array();//TODO 构造输出体
        $info = $this->_getRedis()->zRevRange($this->_user_notice_key($uid),0,-1);
        if(!empty($info)){
	        foreach($info as $k => $v){
	        	$out_put[] = array('fuid' => intval($v));
	        }
        }
        Master::back_data($uid, $this->_notice_mol, $this->_notice_ctrl, $out_put);
        Master::back_data($fuid, $this->_notice_mol, $this->_notice_ctrl, $out_put);
    }

    /**
     * 与$friendUID聊天信息复位
     * @param $uid，自己UID
     * @param $friendUID，好友UID
     */
    public function listReset($uid, $friendUID)
    {
        $this->_key_suffix = "{$uid}_{$friendUID}";
        parent::listReset();
    }

    /**
     * 心跳获取聊天信息
     * @param $uid，自己UID
     * @param $friendUID，好友UID
     */
    public function listCheck($uid, $friendUID, $signRemoveUID = null)
    {
    	if ($signRemoveUID === null || ($signRemoveUID !== null && $friendUID == $signRemoveUID)) {
        	//移除通知
        	$this->_getRedis()->zDelete($this->_user_notice_key($uid), $friendUID);
    	}
        $this->noticeList($uid, $friendUID);

        $this->_key_suffix = "{$uid}_{$friendUID}";
        parent::listCheck($uid,$friendUID);
    }
    
    /**
     * 输出历史列表
     * @param $uid
     * @param $friendUID，好友UID
     * @param $id
     */
    public function listHistory($uid, $friendUID, $id)
    {
        $this->_key_suffix = "{$uid}_{$friendUID}";
        parent::listHistory($uid,$friendUID, $id);
    }

    /**
     * 添加聊天信息
     * @param $fromUID，发送者UID
     * @param $msg，聊天信息
     * @param $toUID，接收者UID
     */
    public function addMsg($fromUID, $msg, $toUID)
    {
        //发送加聊天记录
        $this->_key_suffix = "{$fromUID}_{$toUID}";
        parent::addMsg($fromUID, $msg);

        //接收方加聊天记录
        $this->_key_suffix = "{$toUID}_{$fromUID}";
        parent::addMsg($fromUID, $msg);

        //添加通知
        $this->_addNotice($toUID, $fromUID);
        //私聊聊天流水
        Game::cmd_chat_flow(5, $fromUID, '', '', '', $msg, time(), $toUID);
    }

    /**
     * 格式化聊天信息
     * @param $uid
     * @param $msg
     * @return array
     */
    protected function _formatData($uid, $msg)
    {
        $id = $this->_getMaxID() + 1;
        return array(
            'id' => $id,
            'uid' => $uid,
            'msg' => $msg,
            'time' => Game::get_now(),
        );
    }
    /**
     * 添加红点
     * @param $toUID
     * @param $fromUID
     */
    protected function _addNotice($toUID, $fromUID)
    {
        $this->_getRedis()->zIncrBy(
            $this->_user_notice_key($toUID),
            1,
            $fromUID
        );
        $this->noticeList($toUID, $fromUID);
    }
    protected function _user_notice_key($str)
    {
        return 'Chat_notice_'.$this->_server_type.'_'.$str;
    }
}
