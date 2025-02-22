<?php
/*
 * 排行榜合并
 * */
class redisCom{

    protected $key = array(//需要遍历的redis_key
        'shili',      //势力排行
        'guanka',     //关卡排行
        'love',       //亲密排行
        'fbscore',    //副本积分排行
        'yamen',      //衙门积分排行
        'club',       //公会排行
        'taofa',      //乱党
        'trade',      //丝绸之路
        'jiulou',     //酒楼-宴会排行榜
        'yhLaiBin',   //酒楼-消息-来宾统计
        'trea_6110',  //珍宝阁宝物积分排行
        'trea_6111',  //珍宝阁整理积分排行
        'flower_6190',//御花园世界树排行
    );
    protected $postfix = '_redis';
    /*
     * 遍历
     * */
    public function bianli($SevCfg){
        if($SevCfg['sevid'] != $SevCfg['he']){//遍历的服务器不是合服id
            if(empty($this->key)) return;
            $my_redis = Common::getRedisBySevId($SevCfg['sevid']);
            $he_redis = Common::getDftRedis();
            $he_cache = Common::getDftMem();
            foreach ($this->key as $v){//数据合并
                if ($v=='trea_6111'){
                    $v.='_'.date('ymd',$_SERVER['REQUEST_TIME']);
                }
                $rdata  = $my_redis->zRevRange($v.$this->postfix, 0, -1,true);  //获取排行数据
                if(empty($rdata)){
                   continue;
                }
                foreach($rdata as $uid => $score){
                    $he_redis->zAdd($v.$this->postfix,$score,$uid);
                    unset($uid,$score);
                    $he_cache->delete($v.$this->postfix.'_msg');
                }
                echo $v.'转移完成',PHP_EOL;
                unset($rdata,$v);
            }
        }
    }
}