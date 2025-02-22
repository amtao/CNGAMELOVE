<?php
require_once "RedisBaseModel.php";
/*
 * 宫斗积分排行
 */
class Redis6Model extends RedisBaseModel
{
	public $comment = "宫斗积分排行";
	public $act = 'yamen';//活动标签
	
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "rank";//返回信息 所在控制器
	
//	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
//	public $out_num = 100;//常规输出范围 要获取几个
//	public $out_time = 60;//输出缓存过期时间
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	);
	
	/*
	 * 检查加入排行列表
	 */
	public function join($uid){
		//判断是否已经开启了宫斗战
		$rank_id = $this->get_rank_id($uid);
		if(empty($rank_id)){
			//如果没有 执行加入
			$this->zIncrBy($uid,0);
			//返回宫斗开放信息
			$this->back_data_my($uid);
		}
	}
	
	
	/**
	 * 已经存在元素member，则该元素的score增加increment；否则向集合中添加该元素，其score的值为increment
	 * @param $member   成员
	 * @param $increment   增加值
	 * 
	 */
	public function zIncrBy($member,$increment)
	{
		$redis = Common::getDftRedis();
		$redis->zIncrBy($this->key, $increment, $member );
		
		//在榜单内  更新缓存
		if(self::get_rank_id($member) < $this->out_num ){
			$cache = Common::getDftMem();
			$cache->delete($this->keyMsg);
		}
		//限时活动 - 宫斗积分 涨幅
		$HuodongModel = Master::getHuodong($member);
		$HuodongModel->xianshi_huodong('huodong209',$increment);
		
		//宫斗冲榜
		$HuodongModel = Master::getHuodong($member);
	   	$HuodongModel->chongbang_huodong('huodong254',$member,$increment);

        //宫殿宫斗冲榜 - 宫殿宫斗积分 涨幅
        $HuodongModel->chongbang_huodong('huodong315',$member,$increment);
	}
	
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		
		//玩家个人信息
		$this->_init = $fuidInfo;
		//玩家排名
		$fuidInfo['rid'] = $rid;
		$fuidInfo['num'] = $this->zScore($member);
		
		return $fuidInfo;
	}
	
	/*
	 * 返回额外信息
	 * 返回我的排名和 我的积分
	 */
	public function back_data_my($uid){
		//返回我的积分 //返回我的排名
		$data = array(
			'score' => $this->zScore($uid),
			'rank' => $this->get_rank_id($uid),
		);
		Master::back_data($uid,$this->b_mol,'myrank',$data);
	}
	
	/*
	 * 随机一个对战玩家
	 */
	public function rand_f_uid($myuid){
		//获取我的名次
		$my_rank = $this->get_rank_id($myuid);
		if (empty($my_rank)){
			Master::error('rand_f_uid_my_rankerr');
		}
		
		//获取下50名次
		$r_start = max(1,$my_rank - 50);
		
		//获取我的上下50名次
		//获取 uid => 分值列表
		$uis_scores = $this->azRange($r_start,$r_start+101);
		
		// 查找和删掉自己
		unset($uis_scores[array_search($myuid,$uis_scores)]);
		
		if(empty($uis_scores)){
			return 0;
		}
		return $uis_scores[array_rand($uis_scores,1)];
	}
    
}


