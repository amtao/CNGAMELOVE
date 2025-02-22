<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6227
 */
class Act6227Model extends ActHDBaseModel
{
	public $atype = 6227;//活动编号
	public $comment = "幸运转盘";
	public $b_mol = "luckydraw";//返回信息 所在模块
	public $b_ctrl = "turntable";//子类配置
	public $hd_id = 'huodong_6227';//活动配置文件关键字
    public $hd_dc = array();

    /*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
    public $_init =  array(
        'cons' => 0,  //已消耗(完成)量
        'shop'=>array(),    //商城信息
        'exchange'=>array(),//兑换信息
    );


    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN);
        }

        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);
        $rwd = array();
        foreach($hd_cfg['list'] as $k=>$v){
            $rwd[$k] = array('dc'=>$v['dc'],'id'=>$v['items']['id'],'count'=>$v['items']['count'],'kind'=>$v['items']['kind']) ;
        }
        $hd_cfg['list'] = $rwd;
        $this->outf = $hd_cfg;
        $this->outf['prize'] = $this->hd_dc;
        $this->outf['cons'] = $this->info['cons'];
    }

    /**
     * 摇奖
     * $num 次数 1 或 10次
     */
    public function yao($num){

        if( parent::get_state() != 1 ){
            Master::error(ACTHD_OVERDUE.__LINE__);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        //扣道具
        Master::sub_item($this->uid,KIND_ITEM,$hd_cfg['need'],$num);
        //奖励
        $list = Game::get_key2id($this->hd_cfg['list'],'dc');
        $score = 0;
        //摇奖循环
        $Sev6227Model = Master::getSev6227($this->hd_cfg['info']['id']);
        for($i = 1 ; $i<= $num ; $i ++){
            //随机奖励
            $key =  Game::get_rand_key(10000,$list,'prob_10000');
            $this->hd_dc[] = array('dc'=>$key);
            $item = $list[$key]['items'];
            Master::add_item2($item);
            //抽到特奖 记录日志
            if ($key == 1){
                $score += $this->hd_cfg['surprised'];
                $Sev6227Model->add($this->uid,$item);
            }else{
                $score += $this->hd_cfg['base'];
            }
        }
        $Sev6227Model->bake_data();
        //增加缘分值
        $this->info['cons'] += $score;
        $this->save();
        //每日排行
        $Redis6226Model = Master::getRedis6226($this->_get_day_redis_id());
        $Redis6226Model->zIncrBy($this->uid,$score);
        //总排行
        $Redis6227Model = Master::getRedis6227($this->hd_cfg['info']['id']);
        $Redis6227Model->zIncrBy($this->uid,$score);
        $Redis6227Model->back_data_my($this->uid);
    }

    /*
     * 积分兑换
     * 兑换列表档次id
     * */
    public function exchangea_cons($id){

        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //判断id是否可以兑换
        if(empty($this->hd_cfg['exchange']) ){
            Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
        }
        //转换数据
        $exchange = Game::get_key2id($this->hd_cfg['exchange'],'id');
        //判断信息
        if(empty($exchange[$id]) ){
            Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
        }
        //判断能否购买
        $hd_info = $exchange[$id];
        if($hd_info['is_limit'] == 1 && $hd_info['limit'] <= $this->info['exchange'][$id]){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        //积分不足
        $need = $hd_info['need'];
        if ($need > $this->info['cons']){
            Master::error(BOITE_EXCHANGE_SCORE_SHORT);
        }
        //扣除积分
        $this->sub($need);
        //增加道具
        $items = $hd_info['item'];
        if(empty($items['kind'])){
            $items['kind'] = 1;
        }
        Master::add_item($this->uid,$items['kind'],$items['id'],1);
        $this->info['exchange'][$id] += 1;
        $this->save();

    }

    /*
     * 兑换列表
     * */
    public function back_data_exchange() {
        //构造输出
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $outof = array();

        if(!empty($this->hd_cfg['exchange'])){
            $init = $this->hd_cfg['exchange'];
            foreach ($init as $v){
                $value['id'] = $v['id'];
                $value['need'] = $v['need'];
                $value['items'] = array(
                    'kind' => $v['item']['kind'] ? $v['item']['kind'] : 1,
                    'id' => $v['item']['id'],
                    'count' => $v['item']['count']
                );
                $value['is_limit'] = $v['is_limit'];
                //是否限购
                if($v['is_limit'] == 1){
                    if (empty($this->info['exchange'][$v['id']])){
                        $value['limit'] = $v['limit'];
                    }else{
                        $value['limit'] = $v['limit'] - $this->info['exchange'][$v['id']];
                    }
                }else{
                    $value['limit'] = 0;
                }
                $outof[] = $value;
            }
        }
        return $outof;
    }

    /**
     * 扣除积分
     */
    private function sub($num){
        if (is_int($num)){
            $this->info['cons'] -= $num;
        }
    }

    /*
     * 排行榜
     * */
    public function paihang($type){
        if ($type == 1){
            //每日排行榜
            $Redis6226Model = Master::getRedis6226($this->_get_day_redis_id());
            $Redis6226Model->back_data();
            $Redis6226Model->back_data_my($this->uid);
        }else{
            //总排行榜
            $Redis6227Model = Master::getRedis6227($this->hd_cfg['info']['id']);
            $Redis6227Model->back_data();
            $Redis6227Model->back_data_my($this->uid);
        }
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        //活动消耗道具
        $hd_need = $this->hd_cfg['need'];
        $hd_exchange = $this->hd_cfg['exchange'];
        $ItemModel = Master::getItem($this->uid);
        if (isset($ItemModel->info[$hd_need]) && $ItemModel->info[$hd_need]['count'] > 0){
            $news = 1;
        }
        if ($news == 0){
            foreach ($hd_exchange as $k => $v){
                if ($this->info['cons'] >= $v['need']){
                    return 1;
                }
            }
        }
        return $news;
    }
}
