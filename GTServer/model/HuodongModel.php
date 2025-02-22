<?php
//门客
require_once "AModel.php";
class HuodongModel extends AModel
{
	public function __construct($uid)
	{
		parent::__construct($uid);
	}
	
	/**
	 * 活动期间-充值的钻石
	 * @param $num  充值获得的钻石
	 */
	public function order_diamond($money,$diamond){
		
		//充值活动 - 每日充值
		$Act260Model = Master::getAct260($this->uid);
		$Act260Model->add($money);
		
		//充值活动 - 累计充值
		$Act261Model = Master::getAct261($this->uid);
		$Act261Model->add($money);
		
		//充值活动 - 累天充值
		$Act262Model = Master::getAct262($this->uid);
		$Act262Model->add($money);
		
		//重阳节活动 - 累天充值
		$Act122Model = Master::getAct122($this->uid);
		$Act122Model->add_recode($money);

		//双十一活动 - 累计充值
        $Act85Model = Master::getAct85($this->uid);
        $Act85Model->add_recode($money);

		//感恩节活动 - 累天充值
		$Act127Model = Master::getAct127($this->uid);
		$Act127Model->add_recode($money);

        //活动293 - 获得骰子-充值
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(5,$money);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(5,$money);

		//腊八节活动 - 累天充值
		$Act140Model = Master::getAct140($this->uid);
		$Act140Model->add_recode($money);

        //皇子应援活动 - 累天充值
        $Act6135Model = Master::getAct6165($this->uid);
        $Act6135Model->add_recode($money);

		//红包活动
		$Act295Model = Master::getAct295($this->uid);
		$Act295Model->addRedTicket($money);

        //限时奖励 - 单次充值档次
        $Act6139Model = Master::getAct6139($this->uid);
        $Act6139Model->add($money);

        //充值活动 - 天天充值
        $Act6168Model = Master::getAct6168($this->uid);
        $Act6168Model->add($money);

        //皇子累充解锁
        $Act6181Model = Master::getAct6181($this->uid);
        $Act6181Model->add($money);

        //充值活动 - 连续充值
        $Act6184Model = Master::getAct6184($this->uid);
        $Act6184Model->add($money);

        //充值活动 - 充值翻牌
        $Act6188Model = Master::getAct6188($this->uid);
        $Act6188Model->add($money);

        //春节活动 - 点灯笼
        $Act6189Model = Master::getAct6189($this->uid);
        $Act6189Model->add($money);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(3, 1);
        // $Act6190Model->addType(1, $money);

        //充值活动 - 新累天充值
        $Act6225Model = Master::getAct6225($this->uid);
        $Act6225Model->add($money);

        //充值活动 - 单笔连续充值
        $Act6226Model = Master::getAct6226($this->uid);
        $Act6226Model->add($money);
	}
	
	
	/**
	 * 冲榜活动
	 * @param $key  活动key
	 * @param $member  排行榜key
	 * @param $score   排行榜值
	   $HuodongModel = Master::getHuodong($this->uid);
	   $HuodongModel->chongbang_huodong('huodong251',$this->info['cid'],$num);
	 */
	public function chongbang_huodong($key,$member = 0,$score = 0){
		$redisIndex = 0;
		$actId = 0;
		switch ($key){
			case 'huodong250':  //联盟冲榜
				$Act250Model = Master::getAct250($this->uid);
				$Act250Model->do_save($member,$score);
				$redisIndex = 101;
				$actId = $Act250Model->hd_cfg['info']['id'];
				break;
			case 'huodong251':  //关卡冲榜
				$Act251Model = Master::getAct251($this->uid);
				$Act251Model->do_save($member,$score);
				$redisIndex = 102;
				$actId = $Act251Model->hd_cfg['info']['id'];
				break;
			case 'huodong252':  //势力冲榜
				$Act252Model = Master::getAct252($this->uid);
				$Act252Model->do_save($score);
				$redisIndex = 103;
				$actId = $Act252Model->hd_cfg['info']['id'];
				break;
			case 'huodong253':  //好感冲榜
				$Act253Model = Master::getAct253($this->uid);
				$Act253Model->do_save($score);
				$redisIndex = 104;
				$actId = $Act253Model->hd_cfg['info']['id'];
				break;
			case 'huodong254':  //宫斗冲榜
				$Act254Model = Master::getAct254($this->uid);
				$Act254Model->do_save($score);
				$redisIndex = 105;
				$actId = $Act254Model->hd_cfg['info']['id'];
				break;
			case 'huodong255':  //银两冲榜
				$Act255Model = Master::getAct255($this->uid);
				$Act255Model->do_save($score);
				$redisIndex = 109;
				$actId = $Act255Model->hd_cfg['info']['id'];
				break;
			case 'huodong256':  //酒楼冲榜
				$Act256Model = Master::getAct256($this->uid);
				$Act256Model->do_save($score);
				$redisIndex = 110;
				$actId = $Act256Model->hd_cfg['info']['id'];
				break;
			case 'huodong257':  //士兵冲榜
				$Act257Model = Master::getAct257($this->uid);
				$Act257Model->do_save($score);
				$redisIndex = 257;
				$actId = $Act257Model->hd_cfg['info']['id'];
				break;
            case 'huodong258':  //魅力冲榜
                $Act258Model = Master::getAct258($this->uid);
				$Act258Model->do_save($score);
				$redisIndex = 258;
				$actId = $Act258Model->hd_cfg['info']['id'];
                break;
            case 'huodong259':  //粮食冲榜
                $Act259Model = Master::getAct259($this->uid);
				$Act259Model->do_save($score);
				$redisIndex = 259;
				$actId = $Act259Model->hd_cfg['info']['id'];
                break;
            case 'huodong310':  //联盟势力涨幅冲榜
                $Act310Model = Master::getAct310($this->uid);
				$Act310Model->do_save($score);
				$redisIndex = 310;
				$actId = $Act310Model->hd_cfg['info']['id'];
                break;
			case 'huodong311':  //子嗣势力冲榜
				$Act311Model = Master::getAct311($this->uid);
				$Act311Model->do_save($member,$score);
				$redisIndex = 311;
				$actId = $Act311Model->hd_cfg['info']['id'];
				break;
            case 'huodong313':  //跨服势力冲榜
                $Act313Model = Master::getAct313($this->uid);
				$Act313Model->do_save($score);
				$redisIndex = 132;
				$actId = $Act313Model->hd_cfg['info']['id'];
                break;
            case 'huodong314':  //跨服好感冲榜
                $Act314Model = Master::getAct314($this->uid);
				$Act314Model->do_save($score);
				$redisIndex = 138;
				$actId = $Act314Model->hd_cfg['info']['id'];
                break;
            case 'huodong315':  //宫殿宫斗冲榜
                $Act315Model = Master::getAct315($this->uid);
				$Act315Model->do_save($score);
				$redisIndex = 315;
				$actId = $Act315Model->hd_cfg['info']['id'];
                break;
            case 'huodong6135':  //珍宝阁积分冲榜
                $Act6135Model = Master::getAct6135($this->uid);
				$Act6135Model->do_save($score);
				$redisIndex = 6135;
				$actId = $Act6135Model->hd_cfg['info']['id'];
                break;
            case 'huodong6166':  //伙伴羁绊冲榜
                $Act6166Model = Master::getAct6166($this->uid);
				$Act6166Model->do_save($score);
				$redisIndex = 6166;
				$actId = $Act6166Model->hd_cfg['info']['id'];
                break;
            case 'huodong6167':  //伙伴资质冲榜
                $Act6167Model = Master::getAct6167($this->uid);
				$Act6167Model->do_save($score);
				$redisIndex = 6167;
				$actId = $Act6167Model->hd_cfg['info']['id'];
                break;
            case 'huodong6215':  //偷取晨露次数冲榜
                $Act6215Model = Master::getAct6215($this->uid);
				$Act6215Model->do_save($score);
				$redisIndex = 6215;
				$actId = $Act6215Model->hd_cfg['info']['id'];
                break;
            case 'huodong6216':  //御花园种植次数冲榜
                $Act6216Model = Master::getAct6216($this->uid);
				$Act6216Model->do_save($score);
				$redisIndex = 6216;
				$actId = $Act6216Model->hd_cfg['info']['id'];
                break;
            case 'huodong6217':  //知己技能经验涨幅冲榜
                $Act6217Model = Master::getAct6217($this->uid);
				$Act6217Model->do_save($score);
				$redisIndex = 6217;
				$actId = $Act6217Model->hd_cfg['info']['id'];
                break;
            case 'huodong6218':  //知己技能经验涨幅冲榜
                $Act6218Model = Master::getAct6218($this->uid);
				$Act6218Model->do_save($score);
				$redisIndex = 6218;
				$actId = $Act6218Model->hd_cfg['info']['id'];
                break;

		}
		if($redisIndex > 0 && $actId > 0){
			$redisModel = Master::getRedis($redisIndex,$actId);
			$rank = $redisModel -> get_rank_id($member);
			//主线任务
			$Act39Model=Master::getAct39($this->uid);
			$Act39Model->task_add(116,$rank);
		}	
	}
	
	
	
	/**
	 * 用于统计消耗活动的数据函数
	 * @param $key  道具id 或者对应活动的key  ID
	 * @param $num  道具数量
	 */
	public function xianshi_huodong($key,$num){
		
		switch ($key){
			case 'cash':  //消耗元宝 201
				$Act201Model = Master::getAct201($this->uid);
				$Act201Model->add($num);
				break;
			case 'army':  //消耗士兵 202
				$Act202Model = Master::getAct202($this->uid);
				$Act202Model->add($num);
				break;
			case 'coin':  //消耗银两 203
				$Act203Model = Master::getAct203($this->uid);
				$Act203Model->add($num);
				break;
			case 61:  //武力强化卷轴消耗  204
			case 62:  //战力强化卷轴消耗  204
			case 63:  //政治强化卷轴消耗  204
			case 64:  //魅力强化卷轴消耗  204
				$Act204Model = Master::getAct204($this->uid);
				$Act204Model->add($num);
            //舞狮大会 - 强化书卷使用次数
            $Act6224Model = Master::getAct6224($this->uid);
            $Act6224Model->task_add(23,$num);
				break;
			case 'huodong205':  //临时好感度涨幅205
				$Act205Model = Master::getAct205($this->uid);
				$Act205Model->do_save($num);
				break;
			case 'huodong206':  //临时势力涨幅206
				$Act206Model = Master::getAct206($this->uid);
				$Act206Model->do_save($num);
				break;	
			case 'huodong207':  //限时处理政务次数207
				$Act207Model = Master::getAct207($this->uid);
				$Act207Model->add($num);
				break;
			case 'huodong208':  //累计登录天数208
				$Act208Model = Master::getAct208($this->uid);
				$Act208Model->add($num);
				break;
			case 'huodong209':  //限时宫斗分数涨幅209
				$Act209Model = Master::getAct209($this->uid);
				$Act209Model->add($num);
				break;
			case 'huodong210':  //限时联姻次数210
				$Act210Model = Master::getAct210($this->uid);
				$Act210Model->add($num);
				break;
			case 'huodong211':  //限时书院学习211
				$Act211Model = Master::getAct211($this->uid);
				$Act211Model->add($num);
				break;
			case 'huodong212':  //限时经营商产次数212
				$Act212Model = Master::getAct212($this->uid);
				$Act212Model->add($num);
				break;
			case 'huodong213':  //限时经营农产次数213
				$Act213Model = Master::getAct213($this->uid);
				$Act213Model->add($num);
				break;
			case 'huodong214':  //限时招募士兵次数214
				$Act214Model = Master::getAct214($this->uid);
				$Act214Model->add($num);
				break;
			case 'huodong215':  //限时击杀葛尔丹次数215
				$Act215Model = Master::getAct215($this->uid);
				$Act215Model->add($num);
				break;
			case 125:  //限时挑战书消耗216
				$Act216Model = Master::getAct216($this->uid);
				$Act216Model->add($num);
				break;
			case 'huodong217':  //限时惩戒犯人次数217
				$Act217Model = Master::getAct217($this->uid);
				$Act217Model->add($num);
				break;
			case 'huodong218':  //限时赈灾次数218
				$Act218Model = Master::getAct218($this->uid);
				$Act218Model->add($num);
				break;
			case 72:  //限时体力丹消耗219
				$Act219Model = Master::getAct219($this->uid);
				$Act219Model->add($num);
                $Act6224Model = Master::getAct6224($this->uid);
                $Act6224Model->task_add(26,$num);
				break;
			case 73:  //限时活力丹消耗220
				$Act220Model = Master::getAct220($this->uid);
				$Act220Model->add($num);
                //舞狮大会 - 体力丹消耗数量
                $Act6224Model = Master::getAct6224($this->uid);
                $Act6224Model->task_add(27,$num);
				break;
			case 'huodong221':  //限时奖励-魅力值涨幅221
			    $Act221Model = Master::getAct221($this->uid);
			    $Act221Model->add($num);
			    break;
			case 'huodong222':  //限时赴宴次数222
				$Act222Model = Master::getAct222($this->uid);
				$Act222Model->add($num);
				break;
			case 'huodong223':  //限时联盟副本伤害（累计副本伤害）223
				$Act223Model = Master::getAct223($this->uid);
				$Act223Model->add($num);
				break;
			case 'huodong224':  //限时联盟副本击杀（累计击杀僵尸）224
				$Act224Model = Master::getAct224($this->uid);
				$Act224Model->add($num);
				break;
			case 'huodong225':  //限时酒楼积分涨幅 225
				$Act225Model = Master::getAct225($this->uid);
				$Act225Model->add($num);
				break;
            case 'huodong226':  //限时粮食消耗 226
                $Act226Model = Master::getAct226($this->uid);
                $Act226Model->add($num);
                break;
            case 'huodong6170':  //限时奖励-珍宝阁累计整理关卡次数 6170
                $Act6170Model = Master::getAct6170($this->uid);
                $Act6170Model->add($num);
                break;
            case 'huodong6171':  //限时奖励-祈福次数 6171
                $Act6171Model = Master::getAct6171($this->uid);
                $Act6171Model->add($num);
                break;
            case 'huodong6172':  //限时奖励-精力丹消耗 6172
                $Act6172Model = Master::getAct6172($this->uid);
                $Act6172Model->add($num);
                break;
            case 'huodong6173':  //限时奖励-知己出游次数 6173
                $Act6173Model = Master::getAct6173($this->uid);
                $Act6173Model->add($num);
                break;
            case 'huodong6174':  //限时奖励-问候知己次数 6174
                $Act6174Model = Master::getAct6174($this->uid);
                $Act6174Model->add($num);
                break;
            case 'huodong6175':  //限时奖励-郊祀献礼次数 6175
                $Act6175Model = Master::getAct6175($this->uid);
                $Act6175Model->add($num);
                break;
            case 'huodong6176':  //限时奖励-皇子应援次数 6176
                $Act6176Model = Master::getAct6176($this->uid);
                $Act6176Model->add($num);
                break;
            case 'huodong6177':  //限时奖励-出城寻访次数 6177
                $Act6177Model = Master::getAct6177($this->uid);
                $Act6177Model->add($num);
                break;
            case 'huodong6178':  //限时奖励-徒弟历练次数 6178
                $Act6178Model = Master::getAct6178($this->uid);
                $Act6178Model->add($num);
                break;
            case 'huodong6179':  //限时奖励-御膳房烹饪次数 6179
                $Act6179Model = Master::getAct6179($this->uid);
                $Act6179Model->add($num);
                break;
            case 'huodong6186':  //冬至累计登录天数6186
                $Act6186Model = Master::getAct6186($this->uid);
                $Act6186Model->add($num);
                break;
            case 'huodong6212':  //偷取晨露次数6212
                $Act6212Model = Master::getAct6212($this->uid);
                $Act6212Model->add($num);
                break;
            case 'huodong6213':  //御花园种植次数6213
                $Act6213Model = Master::getAct6213($this->uid);
                $Act6213Model->add($num);
                break;
		}
		
	}
	
	
	/**
	 * 用于显示是否有红点
	 * @param $no  活动唯一标识
	 */
	public function huodong_news($no){
        $modeName= 'getAct'.$no;
		$ActModel = Master::$modeName($this->uid);
		$news = $ActModel->get_news();
		return $news;
	}
	
	/*
	 * 
	 */
	public function sync()
	{
		
	}

	/**
	 * 活动直购礼包回调
	 */
	public function huodong_order_back(){

        foreach ($this->_huodongOderBackHid as $hid => $hcontent) {

        	$ActModel = Master::getActZhiGou($hid, $this->uid);
			$ActModel->exchangeOrderBack();
        }
	}

	protected $_huodongOderBackHid = array(
		6183 => '直购礼包',
        8003 => '许愿池',
        8004 => '购物狂欢',
        8005 => '圣诞节活动',
        8006 => '厨艺大赛',
        8007 => '珍绣坊',
        6220 => '明镜阁',
		8009 => '情人节活动',
		251 => '势力冲榜',
		252 => '关卡冲榜',
		6218 => '徒弟势力冲榜',
    );
}
