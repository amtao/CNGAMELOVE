<?php
/*
 * 聊天 - 敏感过滤
 */
require_once "SevComBaseModel.php";
class Sev28Model extends SevComBaseModel
{
	public $comment = "聊天-敏感词汇过滤";
	public $act = 28;//活动标签
	protected $_use_lock = false;//是否加锁
	
	public $_init = array(//初始化数据
	    /*
         *   'id' => '词汇'   //'id' => 词汇
	     */
	);

    /**
     * 添加 词汇
     * @param $uid  玩家uid
     */
    public function add($msg){
        $this->info[] = $msg;
        $this->save();
    }

    /**
     * 删除词汇
     * @param 词汇 id
     */
    public function remove($id){
        unset($this->info[$id]);
        $this->save();
    }
    /*
     * 是否是敏感词汇
     * */
    public function isSensitify($msg){
        if(!empty($this->info)){
            foreach ($this->info as $v){
                if(!(strpos($msg,$v) === FALSE))
                    return true;
            }
        }
        return false;
    }
}





