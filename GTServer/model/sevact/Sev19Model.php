<?php
/*
 * 聊天 - 敏感词封号
 */
require_once "SevComBaseModel.php";
class Sev19Model extends SevComBaseModel
{
	public $comment = "聊天-敏感词封号";
	public $act = 19;//活动标签
	protected $_use_lock = false;//是否加锁
	
    public $_init = array(//初始化数据
        'words'         => array(),  //敏感词
        'time'          => 86400,//封禁时间
	    /*
         *   'id' => {msg:'词汇',p:0}   //'id' => 词汇
	     */
	);

    /**
     * 添加 词汇
     * @param $uid  玩家uid
     */
    public function add($msg,$p){

        $this->info['words'][$msg] = $p;
        
        
        $this->save();
    }

    /**
     * 删除词汇
     * @param 词汇 id
     */
    public function remove($id){
    //    array_splice($this->info,$id,1);
        unset($this->info['words'][$id]);
        $this->save();
    }
    /*
     * 是否是敏感词汇,返回权重
     * */
    public function isSensitify($msg){
        if(!empty($this->info['words'])){
            foreach ($this->info['words'] as $k=> $v){
                if(!(strpos($msg,$k) === FALSE))
                    return $v;
            }
        }
        return 0;
    }
}





