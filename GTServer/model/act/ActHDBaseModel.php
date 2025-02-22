<?php
require_once "ActBaseModel.php";
/*
 * 活动基类
 * 活动类型:   0:不下发给客户端
 * 1:普通活动		2:限时活动		3:冲榜活动 		4:充值活动 		5:奸臣 	6:巾帼 	7:新官上任 
 * 8：狩猎		9:跨服活动		10:双11活动	11:转盘双12   12:女将    13:双旦活动  14:寻宝大冒险
 * 15: 招财进宝   16:发红包       17:挖宝活动   18:情人节活动 19:新年活动
 */
class ActHDBaseModel extends ActBaseModel
{
	public $comment = "活动基类";
	public $b_mol = "xshuodong";//返回信息 所在模块
	
	public $b_ctrl = "";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = '' ;//活动配置文件关键字
    protected $_rank_id = null;
	
	/*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
	public $_init =  array(
		'cons' => 0,  //已消耗(完成)量
		'rwd' => 0,  //已领取的档次
	);
	
	/**
	 * @param unknown_type $uid   玩家id
	 * @param unknown_type $id    活动id
	 */
	public function __construct($uid)
	{
		
		//获取活动配置
		$this->uid = intval($uid);
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($this->hd_cfg['info']['id'])){
			parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
		}
	}
	
	/**
	 * 获得奖励
	 */
	public function get_rwd($id = 0){
		if( self::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}
		$this->info['rwd'] += 1;
		//奖励信息
		$rinfo = $this->hd_cfg['brwd'][$this->info['rwd']];
		if(empty($rinfo)){
			Master::error(ACTHD_NO_REWARD);
		}
		if($this->info['cons'] < $rinfo['need']){
			Master::error(ACTHD_NO_RECEIVE);
		}
		//领取奖励
		Master::add_item3($rinfo['items']);
		$this->save();
	}
	
	/**
	 * 资源消耗
	 * @param $num
	 */
	public function add($num){
		if( self::get_state() == 1 ){
			$this->info['cons'] += $num;
			$this->save();
            //保存到排行榜中
            if (!empty($this->_rank_id)) {
                $RedisModel = Master::getRedis($this->_rank_id, $this->hd_cfg['info']['id']);
                $RedisModel->zAdd($this->uid, $this->info['cons']);
            }
		}
	}
	
	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
		$news = 0; //不可领取
		if( self::get_state() == 0){
			$news = 0;
		}else{
			//奖励信息
			$rinfo = $this->hd_cfg['brwd'][$this->info['rwd']+1];
			if(!empty($rinfo) && $this->info['cons'] >= $rinfo['need']){
				$news = 1; //可以领取
			}
		}
		return $news;
	}
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//构造输出
		$this->outf = array();
		if( self::get_state() == 0 ){
            return;
		}
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['brwd']);
		unset($hd_cfg['info']['no']);
		$this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝
		$this->outf['cons'] = $this->info['cons'];  //活动期间花费多少元宝
		$this->outf['rwd'] = $this->info['rwd'];  //领取到的档次
	}
	
	/**
	 * 活动活动状态  
	 * 返回:
	 * 0: 活动未开启  
	 * 1: 活动中
	 * 2: 活动结束,展示中
	 */
	public function get_state(){
		$state = 0;  //活动未开启  
		if(!empty($this->hd_cfg) ){

			if(Game::dis_over($this->hd_cfg['info']['showTime'])){
				$state = 2;  //活动结束,展示中
			}
			if(Game::dis_over($this->hd_cfg['info']['eTime'])){
				$state = 1;  //活动中
			}
		}
		return $state;
	}
	
	/*
	 * 返回活动信息--保存时不返回信息 
	 * 只返回当前活动在生效列表中对应的部分
	 */
	public function back_data(){
		$outf = array();
		Common::loadModel('HoutaiModel');
		$outf = HoutaiModel::get_huodong_list($this->uid,$this->hd_id);
		Master::back_data($this->uid,'huodonglist','all',$outf,true);
	}
	
	/*
	 * 返回活动详细信息
	 */
	public function back_data_hd(){
		if( empty($this->outf) ){
			$this->outf = array();			
		}
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}

	public function back_rank_data()
    {
        //保存到排行榜中
        if (!empty($this->_rank_id)) {
            $RedisModel = Master::getRedis($this->_rank_id, $this->hd_cfg['info']['id']);
            $outf = $RedisModel->back_xs_rank($this->_rank_id, $this->uid);
            if (empty($outf)) {
                Master::error(ACT_HD_RANK_NO_EXISTS);
            }
            Master::back_data($this->uid,$this->b_mol,"xsRank",$outf);
        } else {
            Master::error(ACT_HD_RANK_NO_EXISTS);
        }
    }

	/**
	 * 标记当前活动id
	 */
	public function signHdID(){
		if( self::get_state() == 1 ){
			$this->info['id'] = $this->hd_cfg['info']['id'];
			$this->save();
		}
	}

    /**
     * 每日排行key
     */
    protected function _get_day_redis_id(){
        return $this->hd_cfg['info']['id'].'_'.Game::get_today_long_id();
    }

    /*
	 * 商品购买
	 * id 商品列表档次 id
     * num
	 * */
    public function shop_buy($id,$num = 1){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        //判断id是否可以兑换
        $shop_cfg = $this->hd_cfg['shop'];
        if(empty($shop_cfg)){
            Master::error(HD_TYPE8_DONT_SHOPING);
        }

        foreach ($shop_cfg as $item){
            $shop[$item['id']] = $item;
        }
        if(empty($shop[$id]) || empty($shop[$id]['need'])){
            Master::error(HD_TYPE8_SHOP_NO_FUND);
        }
        if (isset($this->info['shop'][$id]) && !is_array($this->info['shop'][$id])){
            $count = $this->info['shop'][$id];
            $this->info['shop'][$id] = array();
            $this->info['shop'][$id][$ymd] = $count;
        }
        if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $this->info['shop'][$id][$ymd]){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        //扣除
        Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']*$num);
        //购买
        if($shop[$id]['is_limit'] == 1){
            $this->shop_add($id,$num);
        }
        $items = $shop[$id]['items'];
        if(empty($items['kind'])){
            $items['kind'] = 1;
        }
        Master::add_item($this->uid,$items['kind'],$items['id'],$items['count']*$num);
        $shop = $this->back_data_shop();
        Master::back_data($this->uid,$this->b_mol,'shop',$shop);
    }

    /*
         * 添加
         *
         * */
    public function shop_add($id,$num = 1)
    {
        if(!is_int($num)){
            Master::error(ACT_HD_ADD_SCORE_NO_INT);
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $this->info['shop'][$id][$ymd] +=$num;
        $this->save();
    }

    /*
     * 商城列表
     * */
    public function back_data_shop() {
        $outof = array();
        if(!empty($this->hd_cfg)){
            $init = $this->hd_cfg['shop'];
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        if(!empty($init)){
            foreach ($init as $v){
                $value['id'] = $v['id'];
                $value['need'] = $v['need'];
                $value['items'] = array(
                    'kind' => $v['items']['kind'] ? $v['items']['kind'] : 1,
                    'id' => $v['items']['id'],
                    'count' => $v['items']['count']
                );
                $value['is_limit'] = $v['is_limit'];
                //是否限购
                if($v['is_limit'] == 1){
                    //每天重置旧数据处理
                    if (isset($this->info['shop'][$v['id']]) && !is_array($this->info['shop'][$v['id']])){
                        $count = $this->info['shop'][$v['id']];
                        $this->info['shop'][$v['id']] = array();
                        $this->info['shop'][$v['id']][$ymd] = $count;
                    }
                    if (empty($this->info['shop'][$v['id']][$ymd])){
                        $value['limit'] = $v['limit'];
                    }else{
                        $Surplus = $v['limit'] - $this->info['shop'][$v['id']][$ymd];
                        $value['limit'] = $Surplus<=0?0:$Surplus;
                    }
                }else{
                    $value['limit'] = 0;
                }
                $outof[] = $value;
            }
        }
        //默认输出直接等于内部存储数据
        return $outof;
    }

    /**
     * 兑换
     * $id
     */
    public function exchange($id = 0){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        // $buy_count = floor($id / 10000);
        // if ($buy_count <= 0)return;
        // $id = $id % 10000;
        // if ($buy_count == 0)Master::error();
        $buy_count = 1;
        foreach($this->hd_cfg['exchange'] as $rwd){
            if ($rwd['id'] == $id){
                $c = empty($this->info['exchange'][$id])?0:$this->info['exchange'][$id];
                if ($c + $buy_count > $rwd['count'] && $rwd['count'] != 0){
                    Master::error();
                }
                $item = $rwd['items'][0];
                // $ItemModel = Master::getItem($this->uid);
                // $ItemModel->sub_item($item['id'], $item['count'] * $buy_count);
                Master::sub_item($this->uid,KIND_ITEM,$item['id'],$item['count']);
                $this->info['exchange'][$id] = $c + $buy_count;
                $item = $rwd['items'][1];
                $item['count'] = $item['count'] * $buy_count;
                Master::add_item2($item);
                $this->save();
                break;
            }
        }
        $exchange = $this->back_data_exchange();
        Master::back_data($this->uid,$this->b_mol,'exchange',$exchange);
    }

    /*
     * 兑换列表
     * */
    public function back_data_exchange() {
        //构造输出
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $hd_cfg = $this->hd_cfg;
        $info = $this->info['exchange'];
        $rwds = array();
        foreach($hd_cfg['exchange'] as $rwd){
            $rwd['buy'] = empty($info[$rwd['id']])?0:$info[$rwd['id']];
            $rwds[] = $rwd;
        }
        return $rwds;
    }

    /**
     * 直购礼包回调
     * @return [type] [description]
     */
    public function exchangeOrderBack(){

        if (!empty($this->info["itemInfo"])) {

            $itemInfo = $this->info["itemInfo"];
            Master::add_item($this->uid, $itemInfo['kind'], $itemInfo['id'], $itemInfo['count']);

            $this->info["itemInfo"] = array();
            $this->save();
        }
    }

    public function exchangeItem($id, $zc_item){

        $c = empty($this->info['exchange'][$id])?0:$this->info['exchange'][$id];
        $this->info['exchange'][$id] = $c + 1;
        $this->info["itemInfo"] = $zc_item['items'][0];
        $this->save();

        $exchange = $this->back_data_exchange();
        Master::back_data($this->uid,$this->b_mol,'exchange',$exchange);
    }

}
