<?php
require_once "ActHDBaseModel.php";

/*
 * 活动294 招财进宝
 */
class Act294Model extends ActHDBaseModel
{
	public $atype = 294;//活动编号
	public $comment = "招财进宝";
	public $b_mol = "zchuodong";//返回信息 所在模块
	public $b_ctrl = "zhaocai";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_294';//活动配置文件关键字
    protected $_rank_id = 121;
    protected $_club_rank_id = 122;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'cons' => 0,  //当前积分
		'rwd' => array(),  //领取的档次
        'set' => 1, //消费提示 1:提示 0:不提示
		
		'time' => 0,	   //每天刷新免费次数
		'free' => array( 'num' => 0,'next' => 0 ),
	);
	
	
	/**
	 * @param unknown_type $uid   玩家id
	 * @param unknown_type $id    活动id
	 */
	public function __construct($uid)
	{
		$this->uid = intval($uid);
		//获取活动配置
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($this->hd_cfg['info']['id'])){
			parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
		}
	}
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//构造输出
		$this->outf = array();
		if( parent::get_state() == 0 ){
			Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
		}
		
		//更新免费次数
		if(!Game::is_today($this->info['time'])){
			$this->info['time'] = $_SERVER['REQUEST_TIME'];
			$this->info['free']['num'] = 0;
            $this->info['set'] = 1;
		}
		
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['info']['no']);
		$this->outf['cfg']['info'] = $hd_cfg['info'];  //活动时间配置

        $this->outf['cfg']['rwd'] = $hd_cfg['rwd'];  //招财树档次配置
		
		//免费次数
		$time = $this->info['free']['next'] + $this->hd_cfg['rwd']['freeT'] * 60 ;
		$this->outf['cfg']['rwd']['cd'] = array(
			'next' => self::set_time($time),
			'num' => $this->hd_cfg['rwd']['free'] - $this->info['free']['num'],
			'label' => 'huodong_294_refresh',
		);
		
		//兑换商品
		$this->outf['cfg']['jifen'] = array();
		
		foreach($hd_cfg['jifen'] as $v){
		    $isGet = 1;//1:不能兑换 0:可兑换  2:已兑换
            if($v['need'] <= $this->info['cons']){
                $isGet = 0;
            }
            if( in_array($v['id'],$this->info['rwd'])){
                $isGet = 2;
            }
            $this->outf['cfg']['jifen'][] = array(
				'id' => $v['id'],
				'need' => $v['need'],
				'items' => $v['items'],
				'isGet' => $isGet,  //1:不能兑换 0:可兑换  2:已兑换
			);
		}
		$this->outf['cons'] = $this->info['cons'];  //活动期间获得的积分
        $this->outf['set'] = $this->info['set'];  //消费提示 1:提示 0:不提示

        $this->outf['cfg']['rank'] = $hd_cfg['rank'];//排行奖励

	}


    /**
     * 单次招财
     */
    public function zao(){

        //获取配置
        $cfg  = $this->hd_cfg['rwd'];
        static $cfg_rwd = array();
        //获得档次配置
        if(empty($cfg_rwd)){
            $cfg_rwd = array();
            foreach($cfg['list'] as $v){
                $cfg_rwd[$v['id']] = $v;
            }
        }
        $rid =  Game::get_rand_key(10000,$cfg_rwd,'prob_10000');
        //配置是否正确
        if(empty($cfg_rwd[$rid])){
            Master::error(ACT_14_CONFIGWRONG);
        }
        return  $cfg_rwd[$rid];
    }
	
	
	/*
	 * 返回活动信息--保存时不返回信息
	 */
	public function back_data(){
		
	}
	
	/*
	 * 返回活动--倒计时/积分
	 */
	public function back_data_cd_u(){
		
		$data = array();
		$data['cfg']['rwd']['cd'] = $this->outf['cfg']['rwd']['cd'];
		$data['cons'] = $this->info['cons'];  //活动期间获得的积分
        $data['cfg']['jifen'] = $this->outf['cfg']['jifen'];
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
	}
	
	/*
	 * 返回活动--积分兑换
	 */
	public function back_data_jifen_u(){
        $this->make_out();
		$data = array();
		$data['cfg']['jifen'] = $this->outf['cfg']['jifen'];
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
	}


    /**
     * 返回要扣除的元宝/获得的积分
     * $num  1次    10 次
     */
    public function pay($num){

        //获取花费对应元宝
        $need = empty($this->outf['cfg']['rwd']['need'])?0:$this->outf['cfg']['rwd']['need'];
        $needTen = empty($this->outf['cfg']['rwd']['needTen'])?0:$this->outf['cfg']['rwd']['needTen'];
        $jifen = empty($this->outf['cfg']['rwd']['jifen'])?0:$this->outf['cfg']['rwd']['jifen'];
        if( $num == 10){
            $need = $needTen;
            $jifen = $num * $jifen;
        }
        return array(
            'pay' => $need,    //总消耗
            'jifen' => $jifen,  //总获得的积分
        );
    }

    /**
     * 单抽消费提示
     * $type : 单抽是否消费提示0:不提示 1:提示
     */
    public function set($type){

        if( parent::get_state() != 1 ){
            Master::error(ACTHD_OVERDUE);
        }

        $this->info['set'] = $type;
        $this->save();

        $data = array();
        $data['set'] = $this->info['set'];  //消费提示 1:提示 0:不提示
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }

    /*
     *检查免费次数
     */
    public function check_free(){
        if($this->outf['cfg']['rwd']['cd']['num'] > 0 && $this->outf['cfg']['rwd']['cd']['next'] <= 0 ){
            return true;
        }
        return false;
    }

    /*
     * 刷新免费次数
     */
    public function back_free(){
        if($this->outf['cfg']['rwd']['cd']['num'] <= 0){
            Master::error(CLUB_NO_DATA);  //参数错误
        }

        $this->info['free']['num'] += 1;
        $this->info['free']['next'] = $_SERVER['REQUEST_TIME'];
        $this->save();

    }

    /*
     * 领取积分奖励
     * $id :档次
     */
    public function get_jifen_rwd($id){

        foreach ( $this->outf['cfg']['jifen'] as $value) {
            //过滤不是领取档位
            if($id != $value['id']){
                continue;
            }
            //积分不足
            if($this->info['cons'] < $value['need']){
                Master::error(ACT_HD_TOTAL_SCORE_IS_SHORT);
            }
            //已经领取
            if(in_array($id,$this->info['rwd'])){
                Master::error(DAILY_IS_RECEIVE);
            }
            //记录已领取
            $this->info['rwd'][] = $id;
            //加道具
            Master::add_item3($value['items']);
            //保存
            $this->save();
            return true;
        }
        Master::error(PARAMS_ERROR.$id);
    }

	
	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
		$news = 0; //不可领取
        if(parent::get_state() == 1 && !empty($this->outf)){
            if($this->outf['cfg']['rwd']['cd']['num'] > 0 && $this->outf['cfg']['rwd']['cd']['next'] == 0){
                $news = 1;
            }else if(!empty($this->outf['cfg']['jifen'])){
                foreach ($this->outf['cfg']['jifen'] as $val){
                    if($val['isGet'] == 0){
                        $news = 1;
                        break;
                    }
                }
            }
        }

        if(parent::get_state() == 2 && !empty($this->outf)){
            if(!empty($this->outf['cfg']['jifen'])){
                foreach ($this->outf['cfg']['jifen'] as $val){
                    if($val['isGet'] == 0){
                        $news = 1;
                        break;
                    }
                }
            }
        }
		return $news;
	}
	
	
	/**
	 * 列入跑马灯
	 * $data  : 信息
	 */
	public function add_pmd($data){
		
		$Sev84Model = Master::getSev84($this->hd_cfg['info']['id']);
        $Sev84Model->add_msg($data);
	}
	
	/**
	 * 输出跑马灯
	 * $uid : 玩家id
	 */
	public function out_pmd($uid,$init = 0){
        $Sev84Model = Master::getSev84($this->hd_cfg['info']['id']);
		//初始化
		if($init){
            $Sev84Model->list_init($uid);
		}
        $Sev84Model->list_click($uid);
	}
	
	/**
	 * 列入获奖情况
	 * $data  : 信息
	 */
	public function add_log($data){
		$Sev83Model = Master::getSev83($this->hd_cfg['info']['id']);
        $Sev83Model->add_msg($data);
	}
	
	/**
	 * 输出获奖情况
	 * $uid : 玩家id
	 */
	public function out_log($uid,$init = 0){
        $Sev83Model = Master::getSev83($this->hd_cfg['info']['id']);
		//初始化
		if($init){
            $Sev83Model->list_init($uid);
		}
        $Sev83Model->list_click($uid);
	}
	
	/**
	 * 输出获奖情况-历史消息
	 * $uid : 玩家id
	 * $id : 第几个
	 */
	public function out_log_history($uid,$id){
        $Sev83Model = Master::getSev83($this->hd_cfg['info']['id']);
        $Sev83Model->list_history($uid,$id);
	}
	
	/**
	 * 没超过当前时间,时间设为0
	 * $time
	 */
	public function set_time($time){
		if(Game::is_over($time)){
			return 0;
		}
		return $time;
	}


    /*
     * 排行榜 和奖励
     * */
    public function paihang(){
        //个人排行榜
        $RedisModel = Master::getRedis($this->_rank_id, $this->hd_cfg['info']['id']);
        $RedisModel->back_data();
        $RedisModel->back_data_my($this->uid);

        //联盟排行榜
        $ClubRedisModel = Master::getRedis($this->_club_rank_id, $this->hd_cfg['info']['id']);
        $ClubRedisModel->back_data();
        $Act40Model = Master::getAct40($this->uid);
        $cid = empty($Act40Model->info['cid'])?0:$Act40Model->info['cid'];
        $ClubRedisModel->back_data_my($cid);
    }

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['cons'] += $num;
            $this->save();
            //保存个人数据
            $RedisModel = Master::getRedis($this->_rank_id, $this->hd_cfg['info']['id']);
            $RedisModel->zAdd($this->uid, $this->info['cons']);

            $Act40Model = Master::getAct40($this->uid);
            $cid = $Act40Model->info['cid'];
            if(!empty($cid)){
                $RedisModel = Master::getRedis($this->_club_rank_id, $this->hd_cfg['info']['id']);
                $RedisModel->zIncrBy($cid, $num);
                Game::cmd_other_flow($cid, 'club', 'huodong_294_'.$this->hd_cfg['info']['id'], array($this->uid), 41, 1, $num, $RedisModel->zScore($cid));
            }
        }
    }

    /**
     * 资源消耗 -- 解散帮会
     * @param $cid
     */
    public function del_club_rank($cid){
        if( self::get_state() == 1 ){
            $RedisModel = Master::getRedis($this->_club_rank_id, $this->hd_cfg['info']['id']);
            $RedisModel->del_member($cid);
        }
    }

    /**
     * 资源消耗 -- 退出帮会
     * @param $cid
     * @param $num
     */
    public function out_club_rank($cid,$num){
        if( self::get_state() == 1 ){
            $RedisModel = Master::getRedis($this->_club_rank_id, $this->hd_cfg['info']['id']);
            $RedisModel->zIncrBy($cid,-$num);
            Game::cmd_other_flow($cid, 'club', 'huodong_294_'.$this->hd_cfg['info']['id'], array($this->uid), 41, 1, $num, $RedisModel->zScore($cid));
        }
    }

    /**
     * 资源消耗 -- 加入帮会
     * @param $cid
     * @param $num
     */
    public function in_club_rank($cid,$num){
        if( self::get_state() == 1 ){
            $RedisModel = Master::getRedis($this->_club_rank_id, $this->hd_cfg['info']['id']);
            $RedisModel->zIncrBy($cid,$num);
            Game::cmd_other_flow($cid, 'club', 'huodong_294_'.$this->hd_cfg['info']['id'], array($this->uid), 41, 1, $num, $RedisModel->zScore($cid));
        }
    }

}




