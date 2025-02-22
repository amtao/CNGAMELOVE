<?php
require_once MOD_DIR . '/BModel.php';
/*
 * 1:	势力排行
 * 2:	关卡排行
 * 3:	亲密排行
 * 4:	副本积分排行
 * 5:	葛二蛋伤害排行
 * 6:	衙门积分排行
 * 8:	人气排行
 * 9:	封设备排行
 * 10:  公会排行
 * 11:  公会盟战积分
 * 12:  封号排行
 * 13:  名胜建造排行
 * 14:  军机处 事件派遣排行
 * 15:  门客皮肤排行
 * 16:  红颜皮肤排行
 * 17:  本服门客排行榜
 *
 *
 * 20:  宴会排行
 * 21:  酒楼来宾统计
 *
 * 30:  百服开服充值不断,福利礼包不停 (弃用)
 * 31:  帮会红包-排行榜
 * 
 * 101 : 联盟冲榜排行
 * 102:  活动关卡冲榜
 * 103:  活动势力冲榜
 * 104:  亲密冲榜排行
 * 105:  衙门冲榜排行
 * 106:  新官上任 - 积分排行
 * 107:  新官上任 - 联盟排行
 * 108:  狩猎 - 积分排行
 * 109:  银两冲榜排行
 * 110:  酒楼冲榜排行
 * 111:  讨伐分数排行
 * 112:  惩戒来福 - 积分排行
 * 113:  惩戒来福 - 联盟积分排行
 * 114:  丝绸之路 - 积分排行
 * 115:  国庆活动 - 积分排行
 * 116:  国庆活动 - 联盟积分排行
 * 117:  重阳节活动 - 积分排行
 * 118:  重阳节活动 - 联盟积分排行
 * 119:  感恩节活动 - 积分排行
 * 120:  感恩节活动 - 联盟积分排行
 * 121:  元旦-招财活动 - 积分排行
 * 122:  元旦-招财活动 - 联盟积分排行
 * 123:  腊八节活动 - 积分排行
 * 124:  腊八节活动 - 联盟积分排行
 * 125:  情人节-送花排行榜
 * 126:  情人节-收花排行榜
 * 127:  新年活动 - 每日排行
 * 128:  新年活动 - 总排行
 * 129:  植树节活动 - 积分排行
 * 130:  植树节活动 - 联盟积分排行
 * 135:  愚人节活动 - 积分排行
 * 136:  愚人节活动 - 联盟积分排行
 * 137:  跨服亲密冲榜--区间 pk区服 单人排行榜 (单人为一个单位)  =>个人奖励
 * 138:  跨服亲密冲榜--区间 pk区服 排行榜  (区服为单位)   =>   整个区奖励
 * 139:  跨服亲密冲榜--区间 排行榜  (区服为单位)   =>   下次匹配
 * 140:  跨服亲密冲榜--区间 排行榜  (区服为单位)   =>   本次匹配期间
 * 141:  太平天国-个人榜单
 * 142:  太平天国-帮会榜单
 * 143： 太平天国-伤害排行
 * 144： 周年秘宝-每日排行
 * 145： 周年秘宝-总排行
 * 146:  摸金校尉-个人榜单
 * 147:  摸金校尉-帮会榜单
 * 148： 摸金校尉-伤害排行
 * 149:  七夕活动-每日排行
 * 150:  七夕活动-总排行
 * 151： 植树造林 - 积分排行
 * 152： 植树造林 - 联盟排行
 * 153： 百戏活动 - 积分排行
 * 154： 百戏活动 - 联盟排行
 * 158:  国庆祭天大典-每日排行
 * 159:  国庆祭天大典-总排行
 * 201:  元宝消耗排行榜
 * 202:  士兵消耗排行榜
 * 203:  银两消耗排行榜
 * 204:  强化卷轴消耗排行榜
 * 205:  临时亲密度涨幅排行榜
 * 206:  临时势力涨幅排行榜
 * 207:  审理政务次数排行榜
 * 208:  累计登录天数排行榜
 * 209:  衙门分数涨幅排行榜
 * 210:  联姻次数排行榜
 * 211:  书院学习排行榜
 * 212:  经营商产次数排行榜
 * 213:  经营农产次数排行榜
 * 214:  招募士兵次数排行榜
 * 215:  击杀葛尔丹次数排行榜
 * 216:  挑战书消耗排行榜
 * 217:  惩戒犯人次数排行榜
 * 218:  赈灾次数排行榜
 * 219:  体力丹消耗排行榜
 * 220:  活力丹消耗排行榜
 * 221:  魅力值涨幅排行榜
 * 222:  赴宴次数排行榜
 * 223:  联盟副本伤害排行榜
 * 224:  联盟副本击杀排行榜
 * 225:  限时酒楼积分涨幅排行榜
 * 226:  限时粮食消耗排行榜
 * 227:  精力丹消耗排行榜
 *
 *
 * 257:  士兵冲榜排行
 * 258:  魅力冲榜排行
 * 259:  粮食冲榜排行
 * 271:  八旗阅阵每日排行
 * 272:  八旗阅阵总排行
 * 273:  重阳节活动每日排行
 * 274:  重阳节活动总排行
 * 276:  元旦节活动总排行榜
 * 290:  转盘双12排行榜
 *
 * 299:  线下活动（限时元宝消耗）
 *
 * 301:  跨服势力排行榜
 * 302:  跨服联盟排行榜
 * 303:  跨服单门客排行
 * 304:  跨服衙门 - 各个服务器积分排行
 * 305:  跨服衙门 - 各个服务器积分排行
 * 306:  跨服衙门 - 小分区内积分排名
 * 307:  跨服衙门 - 当前区的排名
 * 308:  跨服衙门--区间 排行榜  (区服为单位)   =>   下次匹配
 * 309:  跨服全门客排行榜
 * 310:  联盟势力涨幅冲榜排行
 * 311:  子嗣势力涨幅冲榜排行
 * 312:  帮会亲密涨幅冲榜排行
 * 315:  帮会衙门冲榜
 * 316:  绝地衙门-帮会榜
 * 317:  绝地衙门-帮会内部榜
 * 318:	 绝地衙门-帮会关系表
 * 319:	 绝地衙门-每轮个人榜
 * 320:  绝地衙门-总淘汰榜单
 * 321:  绝地衙门-每日个人榜
 *
 *
 * 330:  元宵活动公会积分
 * 331:  限时元宝消耗活动(返利活动)
 * 332:  清明踏青活动
 * 333:  西郊踏青活动
 * 334:  限时招募活动
 *
 *
 * 999:  封号
 * 1000:  角色删除
 * 1001:  角色重置
 *
 */
/*
 * http://www.cnblogs.com/weafer/archive/2011/09/21/2184059.html
 * 排序方法类
 */
class RedisBaseModel extends BModel
{
	public $act;//活动标签
	public $out_type = 1;//排序   1: 从大到小   2: 从小到大
	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
	public $out_num = 100;//常规输出范围 要获取几个

	public $out_time = 60;//输出缓存过期时间
    protected $_with_decimal_sort = false;//加小数排序
	protected $_with_decimal_denominator = null;//分母
	protected $_with_decimal_number = 1000000000;//总值
	protected $_add_expire_time = 0;//redis过期时间
	public $info;//活动数据
	public $comment = "榜单名称";

    /**
     * RedisBaseModel constructor.
     * @param string $key
     * @param null $serverID
     */
	public function __construct($key = '', $serverID = null)
	{
	    parent::__construct($serverID);

		if($key != ''){
			$this->act .= '_'.$key;
		}
		//活动 - 榜单key
		$this->key = $this->getkey();
		//活动 - 榜单key - 缓存
		$this->keyMsg = $this->getkeyMsg(); 
	}

	/**
	 * 插入榜单数据
	 * @param mixed $member
	 * @param int $score
	 */
	public function zAdd($member,$score)
	{
		$redis = $this->_getRedis();
        if ($score > 0 && $this->_with_decimal_sort && $this->_with_decimal_denominator > 0) {
            //负数不加小数
			// $score = intval($score) + 1 / $this->_with_decimal_denominator;
			if ($this->_with_decimal_number > 0) {
				$score = intval($score) + (1 - $this->_with_decimal_denominator / $this->_with_decimal_number);
			}else{
				$score = intval($score) + 1 / $this->_with_decimal_denominator;
			}
        }
		$redis->zAdd($this->key, $score, $member );
		if($this->_add_expire_time){
			$redis->expire($this->key,$this->_add_expire_time);
		}
	}
	
	/**
	 * 已经存在元素member，则该元素的score增加increment；否则向集合中添加该元素，其score的值为increment
	 * @param mixed $member   成员
	 * @param int $increment   增加值
	 */
	public function zIncrBy($member,$increment)
	{
		$redis = $this->_getRedis();
        if ($this->_with_decimal_sort && $this->_with_decimal_denominator > 0) {
            /*
             * 带小数时会有数值溢出问题并被四舍五入导致数值异常
             * 详细见https://blog.csdn.net/xlxxcc/article/details/52293978
             */
            $oldScore = $redis->zScore($this->key, $member);
            $oldScore = empty($oldScore) ? 0: $oldScore;
            $newScore = intval($oldScore) + $increment;
            if ($newScore > 0) {
				// $newScore += 1 - $this->_with_decimal_denominator / 1000000000;
				if ($this->_with_decimal_number > 0) {
            		$newScore += 1 - $this->_with_decimal_denominator / $this->_with_decimal_number;
            	}else{
            		$newScore += 1 / $this->_with_decimal_denominator;
            	}
            }
            $redis->zAdd($this->key, $newScore, $member );
        }
        else {
            $redis->zIncrBy($this->key, $increment, $member );
        }
		if($this->_add_expire_time){
			$redis->expire($this->key,$this->_add_expire_time);
		}
		/*
		//在榜单内  更新缓存
		if(self::get_rank_id($member) < $this->out_num ){
			$cache = Common::getDftMem();
			$cache->delete($this->key);
			$cache->delete($this->keyMsg);
		}
		*/
	}
	
	/**
	 * 获取 对象 值 分数值
	 * @param $member
	 */
	public function zScore($member)
	{
		$redis = $this->_getRedis();
		$score = $redis->zScore($this->key, $member);
		if (empty($score)){
			$score = 0;
		}
		return $score;
	}
	
	/**
	 * 获取对象名次id
	 * @param $member
	 */
	public function get_rank_id($member)
	{
		$redis = $this->_getRedis();
		if($this->out_type == 1){
			//从大到小
			$rid = $redis->zRevRank( $this->key, $member );  //下标从0开始
		}else{
			//从小到大
			$rid = $redis->zRank( $this->key, $member );  //下标从0开始
		}
		if(!is_numeric($rid)){
			//未进榜
			return 0;
		}
		if(strpos($this->act,'huodong')===0){
			$Act39model = Master::getAct39($member);
			$Act39model->task_add(116,$rid+1);
		}
		return $rid+1;
	}
	
	/**
	 * 根据排名id获取成员
	 * @param int $rid  排名id
	 */
	public function get_member($rid)
	{
		$rid -= 1;  //redis下标从0开始
		$redis = $this->_getRedis();
		if($this->out_type == 1){
			//从大到小
			$member = $redis->zRevRange( $this->key, $rid, $rid);  //下标从0开始
		}else{
			//从小到大
			$member = $redis->zRange($this->key, $rid, $rid );  //下标从0开始
		}
		if(empty($member)){
			return 0;
		}
		return $member[0];
	}
	
	
	
	/**
	 * 获取key总个数
	 */
	public function sSize()
	{
		$redis = $this->_getRedis();
		$size = $redis->zCard($this->key);
		if (empty($size)){
			$size = 0;
		}
		return $size;
	}
	
	/**
	 * 删除key中单个成员的值
	 * @param $member
	 */
	public function del_member($member){
		$redis = $this->_getRedis();
		return $redis->zDelete($this->key,$member);
	}
	
	/**
	 * 删除整个key值
	 */
	public function del_key(){
		$redis = $this->_getRedis();
		return $redis->delete($this->key);
	}

	/**
	 * 删除整个缓存key值
	 */
	public function del_msg_key(){
		$redis = $this->_getCache();
		return $redis->delete($this->keyMsg);
	}
	
	/**
	获取redis key
	*/
	public function getkey(){
		return $this->act.'_redis';
	}
	
	/**
	获取redis key  缓存
	*/
	public function getkeyMsg(){
		return $this->key.'_msg';
	}
	/**
	 * 外部类要重写 覆盖的函数
	 * @param mixed $member
     * @param int $rid
	 */
	public function getMember($member,$rid){
		return $member;
	}
	
	/**
	 * 获取名次范围
	 */
	public function azRange($start,$end){
		$redis = $this->_getRedis();
		if($this->out_type == 1){
			//从大到小
			return $redis->zRevRange($this->key,$start-1,$end-1);
		}else{
			//从小到大
			return $redis->zRange($this->key,$start-1,$end-1);
		}
	}

    /**
     * 获取范围内的个数
     * @param $min
     * @param $max
     * @return mixed
     */
	public function azCount($min,$max){
	    $redis = $this->_getRedis();
	    return $redis->zCount($this->key, $min, $max);
    }
	
	/*
	* 获取排行输出
	*/
	public function out_redis(){
		$cache = $this->_getCache();
		$keyMsg = $cache->get($this->keyMsg);
		
		//超时时间
		$dt = 0;
		if (!empty($keyMsg)){
			$dt = $_SERVER["REQUEST_TIME"] - $keyMsg['time'];
		}
		
		//判断缓存是否已过期
		if ( (  empty($keyMsg)  ||  $dt > $this->out_time )  
		&& rand(1,10000) < pow(($dt - $this->out_time),4)){
			//获取排行信息
			$redis_info = $this->azRange($this->out_start,$this->out_num);
			//存储排行成员
			$members = array(); 
			//构造
			$rid = 0;
			if(!empty($redis_info)){
				foreach($redis_info as $member){
					$rid ++;
					$members[] = $this->getMember($member,$rid);
				}
			}
			//写入缓存
			$keyMsg = array(
				'time' => $_SERVER["REQUEST_TIME"],
				'data' => $members,
			);
			$cache->set($this->keyMsg,$keyMsg);
		}
		return $keyMsg['data'];
	}
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//默认输出直接等于内部存储数据
		$this->outf = $this->out_redis();
	}
	
	/*
	 * 返回排行信息
	 */
	public function back_data(){
		$this->make_out();
		Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
	}

    /**
     * @param mixed $type
     * @param mixed $member
     * @return array
     */
	public function back_xs_rank($type, $member)
    {
        return array();
    }

	/**
	 * 返回排行榜所有数据
	 * @param bool $withscore
	 * @return mixed
	 */
	public function zRevRange($withscore = false){
		$redis = $this->_getRedis();
		return $redis->zRevRange($this->key,0,-1,$withscore);
	}

	/**
	 * 删除数据
	 * @param $uid
	 */
	public function zDelete($uid){
		$redis = $this->_getRedis();
		return $redis->zDelete($this->key,$uid);
	}

	/**
	 * 删除指定区间的数据
	 * @param $min
	 * @param $max
	 */
	public function zRemRangeByScore($min,$max){
		$redis = $this->_getRedis();
		return $redis->zRemRangeByScore($this->key,$min,$max);
	}

	/**
	 * 判断指定的key是否存在
	 * @param $key
	 * @return mixed
	 */
	public function exists($key){
		$redis = $this->_getRedis();
		return $redis->exists($key);
	}

    /**
     * 获取属性总和
     * @return int|number
     */
    public function zSum(){
        $redis = $this->_getRedis();
        $all = $redis->zRange($this->key,0,-1,true);
        return empty($all) ? 0 : array_sum($all);
    }
    public function setWithDecimalDenominator($v)
    {
        $this->_with_decimal_denominator = Game::get_now() - $v;
    }

    /*
     * 刷新排行信息
     */
    public function back_data_flush(){
        $redis_info = $this->azRange($this->out_start,$this->out_num);
        //存储排行成员
        $members = array();
        //构造
        $rid = 0;
        if(!empty($redis_info)){
            foreach($redis_info as $member){
                $rid ++;
                $members[] = $this->getMember($member,$rid);
            }
        }
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$members);
    }
}




