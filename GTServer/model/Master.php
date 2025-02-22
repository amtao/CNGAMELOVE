<?php
/**
 * 框架模块管理器
 * 全局变量管理器
 */

//定义几个枚举类型
define( 'KIND_ITEM' , 1 );//道具
define( 'KIND_OTHER' , 2 );//枚举
define( 'KIND_LOVE' , 3 );//红颜亲密
define( 'KIND_FLOWER' , 4 );//红颜魅力
define( 'KIND_BOOKEXP' , 5 );//门客书籍经验
define( 'KIND_SKILLEXP' , 6 );//门客技能经验
define( 'KIND_HERO' , 7 );//门客
define( 'KIND_WIFE' , 8 );//红颜
define( 'KIND_LIKE' , 9 );//红颜好感
define( 'KIND_CHENGHAO' , 10 );//称号
define( 'KIND_HUODONG' , 11 );//活动
define( 'KIND_WIFEEXP' , 12 );//红颜经验
define( 'KIND_CARLJ' , 13 );//车零件
define( 'KIND_CAR' , 14 );//车
define( 'KIND_HERO_SKIN' , 15 );//门客皮肤
define( 'KIND_WIFE_SKIN' , 16 );//红颜皮肤

define( 'KIND_HERO_SW', 90);//伙伴名声
define( 'KIND_ROLE_SW', 91);//自己名声
define( 'KIND_HERO_JB', 92);//伙伴羁绊
define( 'KIND_WIFE_JB', 93);//知己羁绊
define( 'KIND_HEAD_BLANK', 94);//头像框
define( 'KIND_CARD', 99);//卡牌
define( 'KIND_CARD_STONE', 106);//卡牌碎片
class Master
{
	private static $uid = 0;//我的UID , 用来判定消息是不是发给自己的
	
	//public static $uid;
	//各种用户类 容器
	/**
	 * @var [AModel]
	 */
	private static $models = array(
		//'User' => null,//用户类
		//'Act' => null,//活动类
		//'Item' => null,//道具类
		//'Hero' => null,//门客类
		//'Wife' => null,//红颜类
		//'Son' => null,//子嗣类
		//'Mail' => null,//邮件类
	);
	
	//活动类
	private static $act_models = array(
		//1 ://经营活动
		//2: 政务处理
		//15: 学院
		//政务处理 => null,//经营活动
	);
	
	private static $team; //阵法类 较特殊 无需写入
	private static $club; //公会类 直接写入了
    /**
     * @var array()
     */
    private static $_friend_chat = array();
	
	//排行类 公共类 不分UID
	private static $redis_models = array(
		//1' => null,//势力榜
		//2' => null,//关卡榜
		//3' => null,//亲密榜
	);
	//公共信息类 
	private static $sev_models = array(
		//1 => 子嗣全服提亲表
		//2 世界BOSS葛二蛋 
	);
	
	/*
	 * 需要更新的英雄ID列表
	 */
	private static $u_heros = array();
	//需要更新对应羁绊英雄的红颜ID 列表 
	public static $u_wifes = array();
	//更新信息标记
	public static $u_types = array(
		//'alllove' => false,//总亲密更新标记
	);
	
	//返回客户端信息 结构体
	public static $bak_data = array(
		's' => 1,
		'a' => array(),//逻辑返回数据
		'u' => array(),//更新返回数据
	);
	
	//设置我的UID
	public static function set_uid($uid){
		self::$uid = $uid;
	}
	
	//锁列表
	private static $_locks = array(
		//lockkey => lock_obj,
	);
	/**
	 * 获取特殊锁
	 * @param $mod
	 * @param $ctrl
	 * @param string $id
	 * @param bool $pass
	 */
	public static function get_lock_special($mod,$ctrl,$id='',$pass = true){
		$lock = Game::getBaseCfg('lock');
		if(!empty($id) && !empty($lock[$mod][$ctrl]['key_arg'])){
			$msg = empty($lock[$mod][$ctrl]['msg']) ? SERVER_IS_BUSY : $lock[$mod][$ctrl]['msg'];
			Master::get_lock($lock[$mod][$ctrl]['type'], $lock[$mod][$ctrl]['key_arg'].'_'.$id,$pass,$msg);
		}
	}
	
	/**
	 * 获取锁
	 * @param $type   int 锁类型 1:本服,2和服,3跨服,4全服
	 * @param $key   string 锁的key
	 * @param $is_pass bool 加锁失败是否退出
	 * 用户锁 user_uid
	 * 功能锁 wordboss_1_1
	 */
	public static function get_lock($type,$key,$is_pass = true,$passmsg=SERVER_BUSY){
		//如果已存在
		if (isset(self::$_locks[$key])){
			return true;
		}
		//锁类型
		switch($type){
			case 1://本服锁
				Common::loadLockModel("MyLockModel");
				$LockModel = new MyLockModel($key);
				break;
			case 2://合服锁
				Common::loadLockModel("DftLockModel");
				$LockModel = new DftLockModel($key);
				break;
			case 3://跨服锁
				Common::loadLockModel("KuaLockModel");
				$LockModel = new KuaLockModel($key);
				break;
			case 4://全服锁
				Common::loadLockModel("ComLockModel");
				$LockModel = new ComLockModel($key);
				break;
		}
		
		//尝试加锁
		if ($LockModel->getLock(1)){
			self::$_locks[$key] = $LockModel;
			return true;//加锁成功
		}
		//加锁失败
		if ($is_pass){
			self::error($passmsg.$key);
		}
		return false;
	}
	/*
	 * 获取其他玩家交互锁
	 */
	public static function get_fuser_lock($uid){
		//如果是他人 则获取他人的锁
		if ($uid != self::$uid && self::$uid > 0){
			return self::get_lock(1,'user_'.$uid,true,"对方忙碌中");
		}
		return true;
	}
	
	/*
	 * 释放所有锁
	 */
	public static function free_all_lock(){
		foreach (self::$_locks as $lk => $lock){
			$lock->releaseLock();
			unset(self::$_locks[$lk]);
		}
	}
	
	/*
	 * 检查道具类型
	 * 返回
	 * array(
	 * 	'type' => 道具类型 1,user道具 2,itemModel道具 3,其他?
	 * 	'itemid' => 道具ID
	 * 	'count' => 道具数量
	 * );
	 */
	public static function chick_item($itemid,$num){
		$type = 2;//类型
		$tiem_id = $itemid;//ID
		$count = $num;//数量
		$user_item = array(
			1 => 'cash',//元宝
			2 => 'coin',//银两
			3 => 'food',//粮食
			4 => 'army',//军队
			5 => 'exp',//政绩
			10 => 'dresscoin',//装扮货币--伙伴商城购买
		);
		
		//如果是用户道具类型
		if (isset($user_item[$itemid])){
			$type = 1;
			$tiem_id = $user_item[$itemid];
		}else{
			//否则就是背包道具类型
		}
		
		return array(
			'type' => $type,
			'itemid' => $tiem_id,
			'count' => $num,
		);
	}
	
	/*
	 * 添加道具
	 */
	public static function add_item3($item_arr,$winModel="msgwin",$winName="items"){
	    //非空判断
	    if(empty($item_arr)){
	        return false;
        }
		foreach ($item_arr as $v){
			self::add_item2($v,$winModel,$winName);
		}
	}
	
	/*
	 * 添加道具
	 */
	public static function add_item2($item,$winModel="msgwin",$winName="items"){
		$item_id = isset($item['id'])?$item['id']:$item['itemid'];
		$item['kind'] = empty($item['kind'])?1:$item['kind'];
		self::add_item(
			self::$uid,
			$item['kind'],
			$item_id,
			$item['count'],
			$winModel,
			$winName);
	}

	public static function add_rwd_singe($uid, $rwd, $key='prob_10000', $count = 1, $total = 0){
	    if ($total == 0){
            $total = 0;
            foreach ($rwd as $k => $v) {
                $total += $v[$key];
            }
        }
        $rwds = array();
        for ($i = 0; $i < $count; $i++){
            $roll = rand(1, $total);
            foreach ($rwd as $k => $v) {
                if ($roll <= $v[$key]){
                    if (!empty($v['type'])){
                        $v['count'] = Game::type_to_count($v['type'], Master::get_all_prop($uid));
                    }
                    $rwds[] = $v;
                    break;
                }
                else {
                    $roll -= $v[$key];
                }
            }
        }
        Master::add_item3($rwds);
    }

    public static function get_all_prop($uid){
        $team = Master::get_team($uid);
        $act6003Model = Master::getAct6003($uid);
        $allep = $team['allep'];
        $allep[2] += $act6003Model->getAddEp(2);
        $allep[3] += $act6003Model->getAddEp(3);
        $allep[4] += $act6003Model->getAddEp(4);
        return $allep;
    }
	
	/*
	 * 添加道具
	 * 道具ID , 数量
	 * 弹窗所在模块
	 * 弹窗名字
	 * 1	道具 普通道具 + 角色数据道具
		2	枚举道具  <-角色数据道具 
		3	红颜亲密
		4	红颜魅力
		5	门客书籍经验
		6	门客技能经验
		7	门客
		8	红颜
		9	红颜好感度
		11  活动道具
		99 卡牌
		106 卡牌碎片
	 */
public static function add_item($uid,$kind,$itemid,$num = 1,$winModel="msgwin",$winName="items",$params=array()){
		$max = pow(2,32)-1;
		if($num <= 0 || $num >= $max){
			return;
		}
		if (empty($kind)){
			//未定义种类 默认为道具类型
			$kind = KIND_ITEM;
		}
		//是不是用户类型的道具
		switch ($kind){
            case 1://用户数据 和 普通道具
            case 13:
            case 14:
            case 15:
            case 16:
            case 100:
            case 101:
            case 103:
            case 104:
			case 105:
			case 106://卡牌碎片
			case 201:
                $kind = 1;
                $tiem_data = self::chick_item($itemid,$num);
				if ($tiem_data['type'] == 1){
					$UserModel= Master::getUser($uid);
					$UserModel->add_sth($tiem_data['itemid'],$tiem_data['count']);
					//如果是用户数据 输出类型改为 枚举类型
					if ($winModel=="msgwin"){
						$kind = 2;
					}
				}else{
					$ItemModel = Master::getItem($uid);
					$ItemModel->add_item($itemid,$num);
				}
				break;
			case 2://枚举道具 这个较乱 不在这里加
				self::add_meiju($uid,$itemid,$num);
				break;
			case 3://红颜亲密
				$WifeModel = Master::getWife($uid);
				$WifeModel->update(array(
					'wifeid' => $itemid,
					'love' => $num,
				));
                $TeamModel = Master::getTeam($uid);
                $TeamModel->reset(2);
				break;
			case 4://红颜魅力
				$WifeModel = Master::getWife($uid);
				$WifeModel->update(array(
					'wifeid' => $itemid,
					'flower' => $num,
				));
				$huodomgModel = Master::getHuodong($uid);
				$huodomgModel->xianshi_huodong('huodong221',$num);

                //魅力冲榜
                $HuodongModel = Master::getHuodong($uid);
                $HuodongModel->chongbang_huodong('huodong258',$uid,$num);

				break;
			case 5://门客书籍经验
				$HeroModel = Master::getHero($uid);
				$HeroModel->update(array(
					'heroid' => $itemid,
					'zzexp' => $num,
				));
				break;
			case 6://门客技能经验
				$HeroModel = Master::getHero($uid);
				$HeroModel->update(array(
					'heroid' => $itemid,
					'pkexp' => $num,
				));
				break;
			case 7://门客
				$HeroModel = Master::getHero($uid);
				$info = $HeroModel->check_info($itemid,true);
				if(!empty($info)){
					return;
				}
				$HeroModel->add_hero($itemid);
				$num = 1;

				//加门客-跑马灯
                $UserInfo = Master::fuidInfo($uid);
                $Sev91Model = Master::getSev91();
                $Sev91Model->add_msg(array(106,Game::filter_char($UserInfo['name']),$itemid));

				break;
			case 8://红颜
				$WifeModel = Master::getWife($uid);
				$WifeModel->add_wife($itemid);
				$num = 1;

                //加红颜-跑马灯
                $UserInfo = Master::fuidInfo($uid);
                $Sev91Model = Master::getSev91();
                $Sev91Model->add_msg(array(105,Game::filter_char($UserInfo['name']),$itemid));

				break;
			case 9://红颜好感度
				break;
			case 10://称号
				$Act25Model = Master::getAct25($uid);
				$Act25Model->add_chenghao($itemid,$params['num1']);
				break;
			case 11://活动道具
				self::hd_item($uid,$itemid,$num);
				break;
			case 12://红颜经验
				$WifeModel = Master::getWife($uid);
				$w_update = array(
					'wifeid' => $itemid,
					'exp' => $num,
				);
				$WifeModel->update($w_update);
				break;
            case 90:
                $Act6001Model = Master::getAct6001($uid);
                $Act6001Model -> addHeroSW($itemid, $num);
                break;
            case 91:
                $Act6001Model = Master::getAct6001($uid);
                $Act6001Model -> addRoleSW($num);
                break;
            case 92://门客羁绊
                $Act6001Model = Master::getAct6001($uid);
                $Act6001Model -> addHeroJB($itemid, $num);
				break;
            case 93://门客羁绊
                $Act6001Model = Master::getAct6001($uid);
                $Act6001Model -> addWifeJB($itemid, $num);
                break;
            case 94://头像框
                $Act6150Model = Master::getAct6150($uid);
                $Act6150Model -> addBlank($itemid);
                break;
            case 95:
                $Act6140Model = Master::getAct6140($uid);
                $Act6140Model ->addSpClothe($itemid);
                break;
            case 96:
                $Act6005Model = Master::getAct6005($uid);
                $Act6005Model ->addItem($itemid, $num);
				break;
			case 98:
                //$Act6005Model = Master::getAct6005($uid);
				//$Act6005Model ->addItem($itemid, $num);
				$UserModel = Master::getUser($uid);
				$UserModel->addface($itemid);
				break;
			
			case 99://添加卡牌
				$CardModel = Master::getCard($uid);
				$isCard = $CardModel->add_card($itemid);
				if(!$isCard){
					$kind = 106;
				}
				break;
			case 200:
				$Act2001Model = Master::getAct2001($uid);
				$Act2001Model->addMailToken($itemid,$num);
				break;
			case 202:
				$BaowuModel = Master::getBaowu($uid);
				$isBaowu = $BaowuModel->add_baowu($itemid);
				if(!$isBaowu){
					$kind = 201;
				}
				break;
            case 111:
                $Act6143Model = Master::getAct6143($uid);
                $Act6143Model ->addSpClothe($itemid);
				break;
			case 112:
				$Act6144Model = Master::getAct6144($uid);
				$Act6144Model ->addBlanks($itemid);
				break;
			case 113:
				$Act6145Model = Master::getAct6145($uid);
				$Act6145Model ->addEmojis($itemid);
				break;
			case 114:	// 宫殿贡献
				$Act40Model = self::getActModel(40,$uid);
				$Act40Model->add_gx($num);
				break;
			case 115:	// 宫殿经验
				$Act40Model = self::getActModel(40,$uid);
				$cid = $Act40Model->info['cid'];
				if(empty($cid)){
					self::error(CLUB_IS_NULL);
				}
				$ClubModel = self::getClub($cid);
				$ClubModel->add_exp($uid,$num);
				break;
			case 116:	// 宫殿资金
				$Act40Model = self::getActModel(40,$uid);
				$cid = $Act40Model->info['cid'];
				if(empty($cid)){
					self::error(CLUB_IS_NULL);
				}
				$ClubModel = self::getClub($cid);
				$ClubModel->add_fund($uid,$num);
				break;
			case 118:
				$Act40Model = self::getActModel(40,$uid);
				$cid = $Act40Model->info['cid'];
				if(empty($cid)){
					self::error(CLUB_IS_NULL);
				}
				$Sev17Model = Master::getSev17($cid);
				$Sev17Model->addResource($num);
				break;
            case 999:
                $HeroModel = Master::getHero($uid);
                $rid = rand(1,4);
                $epstr = 'e'.$rid;
                //分类嗑药
                $h_update = array(
                    'heroid' => $itemid,
                    $epstr => $num,
                );
                $itemid = $rid * 10000 + $itemid;
                $HeroModel->update($h_update);
                break;
			default:
				self::error("add_item_kind_err_".$kind);
				break;
		}
		
		//如果是门客或者红颜  使用专用弹窗
		//if (in_array($kind,array(6,7))){
		if (0){
			Master::$bak_data['a']["msgwin"]["newnpc"][] = array(
				'kind' => $kind,
				'id' => $itemid,
				'count' => 1
			);
		}else{
			//弹窗信息
			if ($winModel == "msgwin"){
			    if($winName == "fight"){
			        Master::$bak_data['a']["msgwin"][$winName]['items'][] = array('kind' => $kind,'id' => $itemid,'count' => $num);
			    }elseif($winName == "other"){//他人
					$outf['items'][] = array('kind' => $kind,'id' => $itemid,'count' => $num);
                    Master::back_data($uid,$winModel,$winName,$outf);
				}else{
    				//通用弹窗信息
    				Master::$bak_data['a']["msgwin"][$winName][] = array('kind' => $kind,'id' => $itemid,'count' => $num);
			    }
			}elseif (!empty($winModel)){
				//模块弹窗信息
				Master::$bak_data['a'][$winModel]['win'][$winName]['items'][] = array('kind' => $kind,'id' => $itemid,'count' => $num);
			}
		}

		Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterPropDot($uid, $itemid, '添加道具种类_'.$kind, $count, 0, '', 1);
	}
	
	
	
	/**
	 * 活动道具分类
	 * @param $itemid   活动道具id
	 * @param $num      活动道具数据
	 */
	public static function hd_item($uid,$itemid,$num){
		//道具配置
		$itemcfg_info = Game::getcfg_info('item',$itemid);
		$type = $itemcfg_info['type'][0];
		switch($type){
			case 'hd280':
				$Act102Model = Master::getAct102($uid);
	    		$Act102Model->add($itemid,$num);
				break;
			case 'hd281':
			    $Act120Model = Master::getAct120($uid);
			    $Act120Model->add($itemid,$num);
			    break;
			case 'hd282':
			    $Act106Model = Master::getAct106($uid);
			    $Act106Model->add($itemid,$num);
			    break;
		    case 'hd283':
		        $Act116Model = Master::getAct116($uid);
		        $Act116Model->add($itemid,$num);
		        break;
			case 'hd284':
				$Act125Model = Master::getAct125($uid);
				$Act125Model->add($itemid,$num);
				break;
			case 'hd286':
				$Act138Model = Master::getAct138($uid);
				$Act138Model->add($itemid,$num);
				break;
            case 'hd292':
                $Act292Model = Master::getAct292($uid);
                $Act292Model->add($num);
                break;
            case 'hd293':
                $Act293Model = Master::getAct293($uid);
                $Act293Model->add($num);
                break;
            case 'hd296':
                $Act296Model = Master::getAct296($uid);
                $Act296Model->add($num);
                break;
            case 'hd297':
                $Act297Model = Master::getAct297($uid);
                $Act297Model->add($num);
                break;
			case 'hd298':
				break;
            case 'hd6136':
                $Act6162Model = Master::getAct6162($uid);
                $Act6162Model->add($itemid,$num);
                break;
            case 'hd6123':
                $Act6123Model = Master::getAct6123($uid);
                $Act6123Model->add($itemid,$num);
                break;
            case 'hd6223':
                $Act6223Model = Master::getAct6223($uid);
                $Act6223Model->add($itemid,$num);
                break;
			default:
				Master::error(ITEMS_ERROR);
		}
	}
	
	/*
	 * 添加枚举类型道具
	 */
	public static function add_meiju($uid,$itemid,$num = 1){
		switch ($itemid){
			case 6://衙门精力
				$Act61Model = Master::getAct61($uid);
				$Act61Model->add_money($num);
				break;
			case 7://日常任务活跃点
				$Act35Model = self::getAct35($uid);
				$Act35Model->add_score($num);
				break;
			case 8://宴会积分
				$Act51Model = Master::getAct51($uid);
				$Act51Model->add_score($num);
				break;
			case 9://衙门积分
				$Redis6Model = Master::getRedis6();
				$Redis6Model->zIncrBy($uid,$num);
				$Redis6Model->back_data_my($uid);
				break;
			case 15:	//红颜经验
			case 16:	//魅力值
			case 17:	//书籍经验
			case 18:	//技能经验
			case 19:	//运势
			case 20:	//武力属性
			case 21:	//智力属性
			case 22:	//政治属性
			case 23:	//魅力属性
			case 24:	//门客技能经验
			case 25:	//门客书籍经验
			case 26:	//武力资质
			case 27:	//智力资质
			case 28:	//政治资质
			case 29:	//魅力资质
			case 30:	//联盟经验
			case 31:	//联盟财富
			case 32:	//个人贡献
			case 33:
			case 34:
			case 35:
			    break;
			case 36://讨伐分数
			    $Redis111Model = Master::getRedis111();
			    $Redis111Model->zIncrBy($uid,$num);
			    break;
		    case 37://丝绸之路分数
		        $Redis114Model = Master::getRedis114();
		        $Redis114Model->zIncrBy($uid,$num);
		        break;
	        case 38://跨服衙门分数
	            $Act300Model = Master::getAct300($uid);
	            $Act300Model->add_score($num);
	            break;
			case 39://亲家好感度
	            break;
			case 40://子嗣声望
//				
				break;
			case 41://福气值
				$Act144Model = Master::getAct144($uid);
				$Act144Model->add_score($num);
				break;
            case 50://新版宴会积分
                $Act171Model = Master::getAct171($uid);
                $Act171Model->add_score($num);
                break;
            case 100://寻访体力
                $Act26Model = Master::getAct26($uid);
                $Act26Model->add_num_task($num);
                break;
            case 10001://晨露
                $Act6192Model = Master::getAct6192($uid);
                $Act6192Model->addChenlu($num);
                break;
			default:
				//无效的枚举类型道具
				self::error('add_meiju_err_'.$itemid);
				break;
		}
	}
	/*
	 * 扣除枚举类型道具
	 */
	public static function sub_meiju($uid,$itemid,$num = 1){
		switch ($itemid){
			case 6://衙门精力
				$Act61Model = Master::getAct61($uid);
				$Act61Model->sub_money($num);
				break;
            case 10001://晨露
                $Act6192Model = Master::getAct6192($uid);
                $Act6192Model->addChenlu(-$num);
                break;
			//case 8://宴会积分
				//$Act51Model = Master::getAct51($uid);
				//$Act51Model->add_score($num);
				break;
//			case 9://衙门积分
//				$Redis6Model = Master::getRedis6();
//				$Redis6Model->zIncrBy($uid,$num);
//				$Redis6Model->back_data_my($uid);
				break;
			default:
				//无效的枚举类型道具
				self::error('sub_meiju_err_'.$itemid);
				break;
		}
	}	
	
	/*
	 * 扣除道具调用
	 */
	public static function sub_item2($item,$winModel=false,$winName=false){
		$item_id = isset($item['id'])?$item['id']:$item['itemid'];
		//$uid,$kind,$itemid,$num,$winModel=false,$winName=false
		self::sub_item(
			self::$uid,
			$item['kind'],
			$item_id,
			$item['count'],
			$winModel,
			$winName);
	}
	
	/*
	 * 扣除道具道具
	 * 道具ID , 数量
	 */
	public static function sub_item($uid,$kind,$itemid,$num,$winModel=false,$winName=false,$is_off = 0){
		if($num <= 0){
			return;
		}
		//弹窗信息
		if ($winModel == true){
			//通用弹窗信息
			Master::$bak_data['a']["msgwin"][$winName][] = array('id' => $itemid,'count' => -$num);
		}elseif (!empty($winModel)){
			//模块弹窗信息
			Master::$bak_data['a'][$winModel]['win'][$winName]['items'][] = array('id' => $itemid,'count' => -$num);
		}
		
		if (empty($kind)){
			//未定义种类 默认为道具类型
			$kind = KIND_ITEM;
		}
		
		//是不是用户类型的道具
		switch ($kind){
			case 1://用户数据 和 普通道具
			case 106://卡牌碎片	
			case 201:
				$tiem_data = self::chick_item($itemid,$num);
				//是不是用户类型的道具
				if ($tiem_data['type'] == 1){
					$UserModel= Master::getUser($uid);
					return $UserModel->sub_sth($tiem_data['itemid'],$tiem_data['count'],$is_click = false,$is_off);
				}else{
					$ItemModel = Master::getItem($uid);
					return $ItemModel->sub_item($itemid,$num);
				}
				break;
			case 2://枚举道具
				self::sub_meiju($uid,$itemid,$num);
				break;
            case 11://枚举道具
                self::sub_hd_item($uid,$itemid,$num);
				break;
			case 114:	// 宫殿贡献
				$Act40Model = self::getActModel(40,$uid);
				$cid = $Act40Model->info['cid'];
				if(empty($cid)){
					self::error(CLUB_IS_NULL);
				}
				$Act40Model->sub_leftgx($num);
				break;
			case 115:	// 宫殿经验
				$Act40Model = self::getActModel(40,$uid);
				$cid = $Act40Model->info['cid'];
				if(empty($cid)){
					self::error(CLUB_IS_NULL);
				}
				$ClubModel = self::getClub($cid);
				$ClubModel->sub_exp($uid,$num);
				break;
			case 116:	// 宫殿资金
				$Act40Model = self::getActModel(40,$uid);
				$cid = $Act40Model->info['cid'];
				if(empty($cid)){
					self::error(CLUB_IS_NULL);
				}
				$ClubModel = self::getClub($cid);
				$ClubModel->sub_fund($uid,$num);
				break;
			default:
				self::error("sub_item_kind_err_".$kind);
				break;
		}

        Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterPropDot($uid, $itemid, '扣除道具种类_'.$kind, $count, 0, '', 2);
		
	}

    /**
     * 活动道具分类
     * @param $itemid   活动道具id
     * @param $num      活动道具数据
     */
    public static function sub_hd_item($uid,$itemid,$num){
        //道具配置
        $itemcfg_info = Game::getcfg_info('item',$itemid);
        $type = $itemcfg_info['type'][0];
        switch($type){
            case 'hd292':
                $Act292Model = Master::getAct292($uid);
                $Act292Model->sub($num);
                break;
            case 'hd293':
                $Act293Model = Master::getAct293($uid);
                $Act293Model->sub($num);
                break;
            case 'hd296':
                $Act296Model = Master::getAct296($uid);
                $Act296Model->sub($num);
                break;
            case 'hd297':
                $Act297Model = Master::getAct297($uid);
                $Act297Model->sub($num);
                break;
			case 'hd298':
				break;
            default:
                Master::error(ITEMS_ERROR);
        }
    }

	
	/**
	 * 其他弹窗
	 * @param $type
	 * @param $count
	 */
	public static function win_other($uid,$type,$count){
		
		switch($type){
			case 'e1': $type = 20; break;  //武力属性
			case 'e2': $type = 21; break;  //智力属性
			case 'e3': $type = 22; break;  //政治属性
			case 'e4': $type = 23; break;  //魅力属性
			case 'pkexp': $type = 24; break;  //技能经验
			case 'zzexp': $type = 25; break;  //书籍经验
			case 'zz1': $type = 26; break;  //武力资质
			case 'zz2': $type = 27; break;  //智力资质
			case 'zz3': $type = 28; break;  //政治资质
			case 'zz4': $type = 29; break;  //魅力资质
		}
		Master::add_item($uid,KIND_OTHER,$type,$count);
	}
	
	/**
	 * 检查UID合法
	 * 跨服UID检查?
	 */
	public static function click_uid($uid){
		$uid = intval($uid);
		//如何检查UID是否合法?
		$UserModel = Master::getUser($uid);
		if (empty($UserModel->info['uid'])) {
			Master::error(USER_ID_ERROR);
		}
	}
	
	/**
	 * 设置系统时间
	 */
	public static function setTime(){
		//设置系统时间
		self::$bak_data['a']['system']['sys']['time'] = $_SERVER['REQUEST_TIME'];
	}
	
	/*
	 * 获取某个MODEL的方法
	 */
	private static function getModel($mtype,$uid){
		if (!self::$models[$uid][$mtype]){
			//载入类
			$modeName= $mtype."Model";
			Common::loadModel($modeName);
			self::$models[$uid][$mtype] = new $modeName($uid);
		}
		return self::$models[$uid][$mtype];
	}

    /*
     * 获取Act MODEL的方法
     */
    private static function getModelAct($mtype,$uid,$actType){
        $mtype = $mtype.$actType;
        if (!self::$models[$uid][$mtype]){
            //载入类
            Common::loadModel('ActModel');
            self::$models[$uid][$mtype] = new ActModel($uid, $actType);
        }
        return self::$models[$uid][$mtype];
    }

	/*
	 * 获取某个 活动 Model 的方法
	 */
	private static function getActModel($aid,$uid,$hid = 1){
		if (!self::$act_models[$uid][$aid]){
			//载入类
			$modeName= 'Act'.$aid."Model";
			Common::loadActModel($modeName);
			self::$act_models[$uid][$aid] = new $modeName($uid,$hid);
		}
		return self::$act_models[$uid][$aid];
	}

    /**
     * @param $aid
     * @param $key
     * @return RedisBaseModel
     */
	public static function getRedis($aid,$key="")
    {
        return self::getRedisModel($aid,$key);
    }

	/*
	 * 获取某个  排行  Model 的方法
	 */
	private static function getRedisModel($aid,$key = ''){
		//加上服务器编号，防止跨服读取覆盖
        $SevidCfg = Common::getSevidCfg();
        $id_key = sprintf("%s_%s_%s", $SevidCfg['sevid'], $aid,$key);
		if (!self::$redis_models[$id_key]){
			//载入类
			$modeName= 'Redis'.$aid."Model";
			Common::loadRedisModel($modeName);
			self::$redis_models[$id_key] = new $modeName($key);
		}
		return self::$redis_models[$id_key];
	}
	/*
	 * 获取某个  服务器公共信息类  Model 的方法
	 */
	private static function getSevModel($sid = 1,$did = 1,$cid = 1,$serverID=null)
    {
        //加上服务器编号，防止跨服读取覆盖
        $SevidCfg = Common::getSevidCfg();
		$key = sprintf("%s_%s_%s_%s", $SevidCfg['sevid'], $sid, $did, $cid);
        if ($sid == 5 && $serverID !== null) {
            $key = sprintf("%s_%s_%s_%s_%s", $SevidCfg['sevid'], $sid, $did, $cid, $serverID);
        }
		if (!self::$sev_models[$key]){
			//载入类
			$modeName= 'Sev'.$sid."Model";
			Common::loadSevModel($modeName);
			self::$sev_models[$key] = new $modeName($did,$cid,$serverID);
		}
		return self::$sev_models[$key];
	}
	//
	
	/**
	 * 获取用户Model
	 * @param $uid
	 * @return UserModel
	 */
	public static function getUser($uid){
		return self::getModel("User",$uid);
	}
	/**
	 * 活动个人数据Model
	 * @param $uid
	 * @param $aType
	 * @return ActModel
	 */
	public static function getAct($uid, $aType){
		return self::getModelAct("Act",$uid, $aType);
	}
	/**
	 * 获取道具Model
	 * @param $uid
	 * @return ItemModel
	 */
	public static function getItem($uid){
		return self::getModel("Item",$uid);
	}
	
	/**
	 * 获取门客Model
	 * @param $uid
	 * @return HeroModel
	 */
	public static function getHero($uid){
		return self::getModel("Hero",$uid);
	}
	/**
	 * 获取红颜Model
	 * @param $uid
	 * @return WifeModel
	 */
	public static function getWife($uid){
		return self::getModel("Wife",$uid);
	}
	/**
	 * 获取子嗣Model
	 * @param $uid
	 * @return SonModel
	 */
	public static function getSon($uid){
		return self::getModel("Son",$uid);
	}
	/**
	 * 获取邮件Model
	 * @param $uid
	 * @return MailModel
	 */
	public static function getMail($uid){
		return self::getModel("Mail",$uid);
	}
	/**
	 * 获取兑换码Model
	 * @param $uid
	 * @return AcodeModel
	 */
	public static function getCode($uid){
	    return self::getModel("Acode",$uid);
	}
	
	/**
	 * 获取门客Model
	 * @param $uid
	 * @return CardModel
	 */
	public static function getCard($uid){
		return self::getModel("Card",$uid);
	}

	/**
	 * 获取宝物Model
	 * @param $uid
	 * @return CardModel
	 */
	public static function getBaowu($uid){
		return self::getModel("Baowu",$uid);
	}

	/**
	 * 获取活动Model
	 * @param $uid
	 * @return HuodongModel
	 */
	public static function getHuodong($uid){
		return self::getModel("Huodong",$uid);
	}
	
	/**
	 * 获取帮会Model
	 * @param $cid
	 * @return ClubModel
	 */
	public static function getClub($cid){
		if (!self::$club[$cid]){
			//载入类
			Common::loadModel("ClubModel");
			self::$club[$cid] = new ClubModel($cid);
		}
		return self::$club[$cid];
	}
    /**
	 * 获取社交Model
     * @param $uid
     * @param $serverID
     * @return FriendChatModel
     */
	public static function getFriendChat($uid, $serverID = null)
    {
        $key = "{$uid}";
        if (!self::$_friend_chat[$key]){
            //载入类
            Common::loadChatModel('FriendChatModel');
            self::$_friend_chat[$key] = new FriendChatModel($serverID);
        }
        return self::$_friend_chat[$key];
	}
	
	/**
	 * 获取好友Model
	 * @param $uid
	 * @return FriendModel
	 */
	public static function getFriend($uid){
		return self::getModel("Friend",$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act1Model
	 */
	public static function getActZhiGou($aid, $uid){
		return self::getActModel($aid,$uid);
	}
	/**
	 * @param $uid
	 * @return Act1Model
	 */
	public static function getAct1($uid){
		return self::getActModel(1,$uid);
	}
	/**
	 * @param $uid
	 * @return Act2Model
	 */
	public static function getAct2($uid){
		return self::getActModel(2,$uid);
	}
	/**
	 * @param $uid
	 * @return Act3Model
	 */
	public static function getAct3($uid){
		return self::getActModel(3,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act4Model
	 */
	public static function getAct4($uid){
		return self::getActModel(4,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act5Model
	 */
	public static function getAct5($uid){
		return self::getActModel(5,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act6Model
	 * 每天早上八点半刷新
	 */
	public static function getAct6($uid){
		return self::getActModel(6,$uid,Game::get_today_id_9());
	}
	/**
	 * @param $uid
	 * @return Act7Model
	 */
	public static function getAct7($uid){
		return self::getActModel(7,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act8Model
	 */
	public static function getAct8($uid){
		return self::getActModel(8,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act9Model
	 */
	public static function getAct9($uid){
		return self::getActModel(9,$uid);
	}
	/**
	 * @param $uid
	 * @return Act10Model
	 */
	public static function getAct10($uid){
		return self::getActModel(10,$uid);
	}
	/**
	 * @param $uid
	 * @return Act11Model
	 */
	public static function getAct11($uid){
		return self::getActModel(11,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act12Model
	 */
	public static function getAct12($uid){
		return self::getActModel(12,$uid);
	}
	/**
	 * @param $uid
	 * @return Act13Model
	 */
	public static function getAct13($uid){
		return self::getActModel(13,$uid);
	}
	/**
	 * @param $uid
	 * @return Act14Model
	 */
	public static function getAct14($uid){
		return self::getActModel(14,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act15Model
	 */
	public static function getAct15($uid){
		return self::getActModel(15,$uid);
	}
	/**
	 * @param $uid
	 * @return Act16Model
	 */
	public static function getAct16($uid){
		return self::getActModel(16,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act17Model
	 */
	public static function getAct17($uid){
		return self::getActModel(17,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act18Model
	 */
	public static function getAct18($uid){
		return self::getActModel(18,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act19Model
	 */
	public static function getAct19($uid){
		return self::getActModel(19,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act20Model
	 */
	public static function getAct20($uid){
		return self::getActModel(20,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act21Model
	 */
	public static function getAct21($uid){
		return self::getActModel(21,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act22Model
	 */
	public static function getAct22($uid){
		return self::getActModel(22,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act23Model
	 */
	public static function getAct23($uid){
		return self::getActModel(23,$uid);
	}

    /**
	 * @param $uid
     * @return Act24Model
     */
    public static function getAct24($uid){
        return self::getActModel(24,$uid);
    }

	/**
	 * @param $uid
	 * @return Act25Model
	 */
	public static function getAct25($uid){
		return self::getActModel(25,$uid);
	}
	/**
	 * @param $uid
	 * @return Act26Model
	 */
	public static function getAct26($uid){
		return self::getActModel(26,$uid);
	}
	/**
	 * @param $uid
	 * @return Act27Model
	 */
	public static function getAct27($uid){
		return self::getActModel(27,$uid);
	}
	/**
	 * @param $uid
	 * @return Act28Model
	 */
	public static function getAct28($uid){
		return self::getActModel(28,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act29Model
	 */
	public static function getAct29($uid){
		return self::getActModel(29,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act30Model
	 */
	public static function getAct30($uid){
		return self::getActModel(30,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act31Model
	 */
	public static function getAct31($uid){
		return self::getActModel(31,$uid);
	}
	/**
	 * @param $uid
	 * @return Act32Model
	 */
	public static function getAct32($uid){
		return self::getActModel(32,$uid);
	}
	/**
	 * @param $uid
	 * @return Act33Model
	 */
	public static function getAct33($uid){
	    return self::getActModel(33,$uid);
	}
	/**
	 * @param $uid
	 * @return Act34Model
	 */
	public static function getAct34($uid){
	    return self::getActModel(34,$uid);
	}
	/**
	 * @param $uid
	 * @return Act35Model
	 */
	public static function getAct35($uid){
		return self::getActModel(35,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act36Model
	 */
	public static function getAct36($uid){
		return self::getActModel(36,$uid);
	}
    /**
	 * @param $uid
     * @return Act37Model
     */
    public static function getAct37($uid){
        return self::getActModel(37,$uid);
    }
    /**
	 * @param $uid
     * @return Act38Model
     */
    public static function getAct38($uid){
        return self::getActModel(38,$uid);
    }
    /**
	 * @param $uid
     * @return Act39Model
     */
    public static function getAct39($uid){
        return self::getActModel(39,$uid);
    }
	/**
	 * @param $uid
	 * @return Act40Model
	 */
	public static function getAct40($uid){
		return self::getActModel(40,$uid);
	}
	/**
	 * @param $uid
	 * @return Act41Model
	 */
	public static function getAct41($uid){
		return self::getActModel(41,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act42Model
	 */
	public static function getAct42($uid){
		return self::getActModel(42,$uid,Game::get_month_id());
	}
	/**
	 * @param $uid
	 * @return Act43Model
	 */
	public static function getAct43($uid){
		return self::getActModel(43,$uid);
	}
	/**
	 * @param $uid
	 * @return Act44Model
	 */
	public static function getAct44($uid){
		return self::getActModel(44,$uid);
	}
	/**
	 * @param $uid
	 * @return Act45Model
	 */
	public static function getAct45($uid){
		return self::getActModel(45,$uid);
	}
    /**
	 * @param $uid
     * @return Act46Model
     */
    public static function getAct46($uid){
        return self::getActModel(46,$uid);
    }

	/**
	 * @param $uid
	 * @return Act47Model
	 */
	public static function getAct47($uid){
		return self::getActModel(47,$uid);
	}

    /**
	 * @param $uid
     * @return Act48Model
     */
    public static function getAct48($uid){
        return self::getActModel(48,$uid);
    }

	/**
	 * @param $uid
	 * @return Act49Model
	 */
	public static function getAct49($uid){
		return self::getActModel(49,$uid);
	}

	/**
	 * @param $uid
	 * @return Act50Model
	 */
	public static function getAct50($uid){
		return self::getActModel(50,$uid);
	}
	/**
	 * @param $uid
	 * @return Act51Model
	 */
	public static function getAct51($uid){
		return self::getActModel(51,$uid);
	}
	/**
	 * @param $uid
	 * @return Act52Model
	 */
	public static function getAct52($uid){
		return self::getActModel(52,$uid);
	}
	/**
	 * @param $uid
	 * @return Act53Model
	 */
	public static function getAct53($uid){
		return self::getActModel(53,$uid);
	}
	/**
	 * @param $uid
	 * @return Act54Model
	 */
	public static function getAct54($uid){
		return self::getActModel(54,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act55Model
	 */
	public static function getAct55($uid){
		return self::getActModel(55,$uid,Game::get_today_id());
	}
	/**
	 * @return Act58Model
	 */
	public static function getAct58($uid){
		return self::getActModel(58,$uid);
	}
	/**
	 * @return Act60Model
	 */
	public static function getAct60($uid){
		return self::getActModel(60,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act61Model
	 */
	public static function getAct61($uid){
		return self::getActModel(61,$uid);
	}
	/**
	 * @param $uid
	 * @return Act62Model
	 */
	public static function getAct62($uid){
		return self::getActModel(62,$uid);
	}
	/**
	 * @param $uid
	 * @return Act63Model
	 */
	public static function getAct63($uid){
		return self::getActModel(63,$uid);
	}

    /**
	 * @param $uid
     * @return Act64Model
     */
    public static function getAct64($uid){
        return self::getActModel(64,$uid,Game::get_today_id());
    }
	
	/**
	 * @param $uid
	 * @return Act65Model
	 */
	public static function getAct65($uid){
		return self::getActModel(65,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act66Model
	 */
	public static function getAct66($uid){
		return self::getActModel(66,$uid);
	}

	
	/**
	 * @param $uid
	 * @return Act67Model
	 */
	public static function getAct67($uid){
		return self::getActModel(67,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act68Model
	 */
	public static function getAct68($uid){
		return self::getActModel(68,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act69Model
	 */
	public static function getAct69($uid){
		return self::getActModel(69,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act70Model
	 */
	public static function getAct70($uid){
		return self::getActModel(70,$uid);
	}

	/**
	 * @param $uid
	 * @return Act71Model
	 */
	public static function getAct71($uid){
		return self::getActModel(71,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act72Model
	 */
	public static function getAct72($uid){
		return self::getActModel(72,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act73Model
	 */
	public static function getAct73($uid){
		return self::getActModel(73,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act74Model
	 */
	public static function getAct74($uid){
		return self::getActModel(74,$uid);
	}

	/**
	 * @param $uid
	 * @return Act75Model
	 */
	public static function getAct75($uid){
		return self::getActModel(75,$uid);
	}

	/**
	 * @param $uid
	 * @return Act76Model
	 */
	public static function getAct76($uid){
		return self::getActModel(76,$uid);
	}

	/**
	 * @param $uid
	 * @return Act77Model
	 */
	public static function getAct77($uid){
		return self::getActModel(77,$uid);
	}

	/**
	 * @param $uid
	 * @return Act78Model
	 */
	public static function getAct78($uid){
		return self::getActModel(78,$uid);
	}

	/**
	 * @param $uid
	 * @return Act79Model
	 */
	public static function getAct79($uid){
		return self::getActModel(79,$uid);
	}

	/**
	 * @param $uid
	 * @return Act80Model
	 */
	public static function getAct80($uid){
		return self::getActModel(80,$uid);
	}

	/**
	 * @param $uid
	 * @return Act81Model
	 */
	public static function getAct81($uid){
		return self::getActModel(81,$uid,Game::get_today_id());
	}

	/**
	 * @param $uid
	 * @return Act82Model
	 */
	public static function getAct82($uid){
		return self::getActModel(82,$uid);
	}

    /**
	 * @param $uid
     * @return Act83Model
     */
    public static function getAct83($uid){
        return self::getActModel(83,$uid);
    }

    /**
	 * @param $uid
     * @return Act84Model
     */
    public static function getAct84($uid){
        return self::getActModel(84,$uid);
    }

    /**
	 * @param $uid
     * @return Act85Model
     */
    public static function getAct85($uid){
        return self::getActModel(85,$uid);
    }
    /**
	 * @param $uid
     * @return Act86Model
     */
    public static function getAct86($uid){
        return self::getActModel(86,$uid);
    }

    /**
	 * @param $uid
     * @return Act87Model
     */
    public static function getAct87($uid){
        return self::getActModel(87,$uid,Game::get_today_id());
    }

	/**
	 * @param $uid
	 * @return Act88Model
	 */
	public static function getAct88($uid){
		return self::getActModel(88,$uid);
	}

	/**
	 * @param $uid
	 * @return Act89Model
	 */
	public static function getAct89($uid){
		return self::getActModel(89,$uid);
	}

	/**
	 * @param $uid
	 * @return Act90Model
	 */
	public static function getAct90($uid){
		return self::getActModel(90,$uid);
	}
	/**
	 * @param $uid
	 * @return Act91Model
	 */
	public static function getAct91($uid){
	    return self::getActModel(91,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act92Model
	 */
	public static function getAct92($uid){
	    return self::getActModel(92,$uid);
	}
	/**
	 * @param $uid
	 * @return Act93Model
	 */
	public static function getAct93($uid){
	    return self::getActModel(93,$uid);
	}
	/**
	 * @param $uid
	 * @return Act94Model
	 */
	public static function getAct94($uid){
	    return self::getActModel(94,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act95Model
	 */
	public static function getAct95($uid){
	    return self::getActModel(95,$uid);
	}
	/**
	 * @param $uid
	 * @return Act96Model
	 */
	public static function getAct96($uid){
	    return self::getActModel(96,$uid);
	}
	/**
	 * @param $uid
	 * @return Act97Model
	 */
	public static function getAct97($uid){
	    return self::getActModel(97,$uid);
	}
	/**
	 * @param $uid
	 * @return Act98Model
	 */
	public static function getAct98($uid){
	    return self::getActModel(98,$uid);
	}
	/**
	 * @param $uid
	 * @return Act99Model
	 */
	public static function getAct99($uid){
	    return self::getActModel(99,$uid);
	}
	/**
	 * @param $uid
	 * @return Act100Model
	 */
	public static function getAct100($uid){
	    return self::getActModel(100,$uid);
	}
	/**
	 * @param $uid
	 * @return Act101Model
	 */
	public static function getAct101($uid){
	    return self::getActModel(101,$uid);
	}
	/**
	 * @param $uid
	 * @return Act102Model
	 */
	public static function getAct102($uid){
	    return self::getActModel(102,$uid);
	}
	/**
	 * @param $uid
	 * @return Act103Model
	 */
	public static function getAct103($uid){
	    return self::getActModel(103,$uid);
	}
	/**
	 * @param $uid
	 * @return Act104Model
	 */
	public static function getAct104($uid){
	    return self::getActModel(104,$uid);
	}
	/**
	 * @param $uid
	 * @return Act105Model
	 */
	public static function getAct105($uid){
	    return self::getActModel(105,$uid);
	}
	/**
	 * @param $uid
	 * @return Act106Model
	 */
	public static function getAct106($uid){
	    return self::getActModel(106,$uid);
	}
	/**
	 * @param $uid
	 * @return Act107Model
	 */
	public static function getAct107($uid){
	    return self::getActModel(107,$uid);
	}
	/**
	 * @param $uid
	 * @return Act110Model
	 */
	public static function getAct110($uid){
	    return self::getActModel(110,$uid);
	}
	/**
	 * @param $uid
	 * @return Act111Model
	 */
	public static function getAct111($uid){
	    return self::getActModel(111,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act112Model
	 */
	public static function getAct112($uid){
	    return self::getActModel(112,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act113Model
	 */
	public static function getAct113($uid){
	    return self::getActModel(113,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act114Model
	 */
	public static function getAct114($uid){
	    return self::getActModel(114,$uid);
	}
	/**
	 * @param $uid
	 * @return Act115Model
	 */
	public static function getAct115($uid){
	    return self::getActModel(115,$uid);
	}
	/**
	 * @param $uid
	 * @return Act116Model
	 */
	public static function getAct116($uid){
	    return self::getActModel(116,$uid);
	}
	/**
	 * @param $uid
	 * @return Act117Model
	 */
	public static function getAct117($uid){
	    return self::getActModel(117,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act118Model
	 */
	public static function getAct118($uid){
	    return self::getActModel(118,$uid);
	}
	/**
	 * @param $uid
	 * @return Act119Model
	 */
	public static function getAct119($uid){
	    return self::getActModel(119,$uid);
	}
	/**
	 * @param $uid
	 * @return Act120Model
	 */
	public static function getAct120($uid){
	    return self::getActModel(120,$uid);
	}
	/**
	 * @param $uid
	 * @return Act121Model
	 */
	public static function getAct121($uid){
	    return self::getActModel(121,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act122Model
	 */
	public static function getAct122($uid){
	    return self::getActModel(122,$uid);
	}


	/**
	 * @param $uid
	 * @return Act123Model
	 */
	public static function getAct123($uid){
		return self::getActModel(123,$uid);
	}
	/**
	 * @param $uid
	 * @return Act124Model
	 */
	public static function getAct124($uid){
		return self::getActModel(124,$uid);
	}
	/**
	 * @param $uid
	 * @return Act125Model
	 */
	public static function getAct125($uid){
		return self::getActModel(125,$uid);
	}
	/**
	 * @param $uid
	 * @return Act126Model
	 */
	public static function getAct126($uid){
		return self::getActModel(126,$uid);
	}

	/**
	 * @param $uid
	 * @return Act127Model
	 */
	public static function getAct127($uid){
		return self::getActModel(127,$uid);
	}

	/**
	 * @param $uid
	 * @return Act128Model
	 */
	public static function getAct128($uid){
		return self::getActModel(128,$uid);
	}

	/**
	 * @param $uid
	 * @return Act129Model
	 */
	public static function getAct129($uid){
		return self::getActModel(129,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act130Model
	 */
	public static function getAct130($uid){
	    return self::getActModel(130,$uid);
	}
	/**
	 * @param $uid
	 * @return Act131Model
	 */
	public static function getAct131($uid){
	    return self::getActModel(131,$uid);
	}	
	/**
	 * @param $uid
	 * @return Act132Model
	 */
	public static function getAct132($uid){
	    return self::getActModel(132,$uid);
	}
	/**
	 * @param $uid
	 * @return Act133Model
	 */
	public static function getAct133($uid){
	    return self::getActModel(133,$uid);
	}
	/**
	 * @param $uid
	 * @return Act134Model
	 */
	public static function getAct134($uid){
	    return self::getActModel(134,$uid);
	}
	/**
	 * @param $uid
	 * @return Act135Model
	 */
	public static function getAct135($uid){
	    return self::getActModel(135,$uid);
	}

	/**
	 * @param $uid
	 * @return Act136Model
	 */
	public static function getAct136($uid){
		return self::getActModel(136,$uid);
	}
	/**
	 * @param $uid
	 * @return Act137Model
	 */
	public static function getAct137($uid){
		return self::getActModel(137,$uid);
	}
	/**
	 * @param $uid
	 * @return Act138Model
	 */
	public static function getAct138($uid){
		return self::getActModel(138,$uid);
	}
	/**
	 * @param $uid
	 * @return Act139Model
	 */
	public static function getAct139($uid){
		return self::getActModel(139,$uid);
	}

	/**
	 * @param $uid
	 * @return Act140Model
	 */
	public static function getAct140($uid){
		return self::getActModel(140,$uid);
	}
	/**
	 * @param $uid
	 * @return Act141Model
	 */
	public static function getAct141($uid){
		return self::getActModel(141,$uid);
	}
	/**
	 * @param $uid
	 * @return Act142Model
	 */
	public static function getAct142($uid){
		return self::getActModel(142,$uid);
	}
	/**
	 * @param $uid
	 * @return Act143Model
	 */
	public static function getAct143($uid){
		return self::getActModel(143,$uid);
	}
	/**
	 * @param $uid
	 * @return Act144Model
	 */
	public static function getAct144($uid){
		return self::getActModel(144,$uid);
	}

    /**
	 * @param $uid
     * @return Act151Model
     */
    public static function getAct151($uid){
        return self::getActModel(151,$uid);
    }

    /**
	 * @param $uid
     * @return Act152Model
     */
    public static function getAct152($uid)
    {
        return self::getActModel(152, $uid);
    }

	/**
	 * @param $uid
	 * @return Act170Model
	 */
	public static function getAct170($uid){
		return self::getActModel(170,$uid);
	}
	/**
	 * @param $uid
	 * @return Act171Model
	 */
	public static function getAct171($uid){
		return self::getActModel(171,$uid);
	}
	/**
	 * @param $uid
	 * @return Act172Model
	 */
	public static function getAct172($uid){
		return self::getActModel(172,$uid);
	}

	/**
	 * @param $uid
	 * @return Act199Model
	 */
	public static function getAct199($uid){
		return self::getActModel(199,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act200Model
	 */
	public static function getAct200($uid){
		return self::getActModel(200,$uid);
	}
	/**
	 * @param $uid
	 * @return Act201Model
	 */
	public static function getAct201($uid){
		return self::getActModel(201,$uid);
	}
	/**
	 * @param $uid
	 * @return Act202Model
	 */
	public static function getAct202($uid){
		return self::getActModel(202,$uid);
	}
	/**
	 * @param $uid
	 * @return Act203Model
	 */
	public static function getAct203($uid){
		return self::getActModel(203,$uid);
	}
	/**
	 * @param $uid
	 * @return Act204Model
	 */
	public static function getAct204($uid){
		return self::getActModel(204,$uid);
	}
	/**
	 * @param $uid
	 * @return Act205Model
	 */
	public static function getAct205($uid){
		return self::getActModel(205,$uid);
	}
	/**
	 * @param $uid
	 * @return Act206Model
	 */
	public static function getAct206($uid){
		return self::getActModel(206,$uid);
	}
	/**
	 * @param $uid
	 * @return Act207Model
	 */
	public static function getAct207($uid){
		return self::getActModel(207,$uid);
	}
	/**
	 * @param $uid
	 * @return Act208Model
	 */
	public static function getAct208($uid){
		return self::getActModel(208,$uid);
	}
	/**
	 * @param $uid
	 * @return Act209Model
	 */
	public static function getAct209($uid){
		return self::getActModel(209,$uid);
	}
	/**
	 * @param $uid
	 * @return Act210Model
	 */
	public static function getAct210($uid){
		return self::getActModel(210,$uid);
	}
	/**
	 * @param $uid
	 * @return Act211Model
	 */
	public static function getAct211($uid){
		return self::getActModel(211,$uid);
	}
	/**
	 * @param $uid
	 * @return Act212Model
	 */
	public static function getAct212($uid){
		return self::getActModel(212,$uid);
	}
	/**
	 * @param $uid
	 * @return Act213Model
	 */
	public static function getAct213($uid){
		return self::getActModel(213,$uid);
	}
	/**
	 * @param $uid
	 * @return Act214Model
	 */
	public static function getAct214($uid){
		return self::getActModel(214,$uid);
	}
	/**
	 * @param $uid
	 * @return Act215Model
	 */
	public static function getAct215($uid){
		return self::getActModel(215,$uid);
	}
	/**
	 * @param $uid
	 * @return Act216Model
	 */
	public static function getAct216($uid){
		return self::getActModel(216,$uid);
	}
	/**
	 * @param $uid
	 * @return Act217Model
	 */
	public static function getAct217($uid){
		return self::getActModel(217,$uid);
	}
	/**
	 * @param $uid
	 * @return Act218Model
	 */
	public static function getAct218($uid){
		return self::getActModel(218,$uid);
	}
	/**
	 * @param $uid
	 * @return Act219Model
	 */
	public static function getAct219($uid){
		return self::getActModel(219,$uid);
	}
	/**
	 * @param $uid
	 * @return Act220Model
	 */
	public static function getAct220($uid){
		return self::getActModel(220,$uid);
	}
	/**
	 * @param $uid
	 * @return Act221Model
	 */
	public static function getAct221($uid){
		return self::getActModel(221,$uid);
	}
	/**
	 * @param $uid
	 * @return Act222Model
	 */
	public static function getAct222($uid){
		return self::getActModel(222,$uid);
	}
	/**
	 * @param $uid
	 * @return Act223Model
	 */
	public static function getAct223($uid){
		return self::getActModel(223,$uid);
	}
	/**
	 * @param $uid
	 * @return Act224Model
	 */
	public static function getAct224($uid){
		return self::getActModel(224,$uid);
	}
	/**
	 * @param $uid
	 * @return Act225Model
	 */
	public static function getAct225($uid){
		return self::getActModel(225,$uid);
	}
    /**
	 * @param $uid
     * @return Act226Model
     */
    public static function getAct226($uid){
        return self::getActModel(226,$uid);
    }
	/**
	 * @param $uid
	 * @return Act250Model
	 */
	public static function getAct250($uid){
		return self::getActModel(250,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act251Model
	 */
	public static function getAct251($uid){
		return self::getActModel(251,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act252Model
	 */
	public static function getAct252($uid){
		return self::getActModel(252,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act253Model
	 */
	public static function getAct253($uid){
		return self::getActModel(253,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act254Model
	 */
	public static function getAct254($uid){
		return self::getActModel(254,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act255Model
	 */
	public static function getAct255($uid){
		return self::getActModel(255,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act256Model
	 */
	public static function getAct256($uid){
		return self::getActModel(256,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act257Model
	 */
	public static function getAct257($uid){
		return self::getActModel(257,$uid);
	}

    /**
	 * @param $uid
     * @return Act258Model
     */
    public static function getAct258($uid){
        return self::getActModel(258,$uid);
    }

    /**
	 * @param $uid
     * @return Act259Model
     */
    public static function getAct259($uid){
        return self::getActModel(259,$uid);
    }

	/**
	 * @param $uid
	 * @return Act260Model
	 */
	public static function getAct260($uid){
		return self::getActModel(260,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act261Model
	 */
	public static function getAct261($uid){
		return self::getActModel(261,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act262Model
	 */
	public static function getAct262($uid){
		return self::getActModel(262,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act270Model
	 */
	public static function getAct270($uid){
		return self::getActModel(270,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act271Model
	 */
	public static function getAct271($uid){
		return self::getActModel(271,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act272Model
	 */
	public static function getAct272($uid){
		return self::getActModel(272,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act280Model
	 */
	public static function getAct280($uid){
	    return self::getActModel(280,$uid);
	}
	/**
	 * @param $uid
	 * @return Act281Model
	 */
	public static function getAct281($uid){
	    return self::getActModel(281,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act282Model
	 */
	public static function getAct282($uid){
	    return self::getActModel(282,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act283Model
	 */
	public static function getAct283($uid){
	    return self::getActModel(283,$uid);
	}

	/**
	 * @param $uid
	 * @return Act284Model
	 */
	public static function getAct284($uid){
		return self::getActModel(284,$uid);
	}

    /**
	 * @param $uid
     * @return Act285Model
     */
    public static function getAct285($uid){
        return self::getActModel(285,$uid);
    }

	/**
	 * @param $uid
	 * @return Act286Model
	 */
	public static function getAct286($uid){
		return self::getActModel(286,$uid);
	}
    /**
	 * @param $uid
     * @return Act287Model
     */
    public static function getAct287($uid){
        return self::getActModel(287,$uid);
    }
    /**
    * @param $uid
     * @return Act290Model
     */
    public static function getAct290($uid){
        return self::getActModel(290,$uid);
    }
    /**
	 * @param $uid
     * @return Act291Model
     */
    public static function getAct291($uid){
        return self::getActModel(291,$uid);
    }
    /**
	 * @param $uid
     * @return Act292Model
     */
    public static function getAct292($uid){
        return self::getActModel(292,$uid);
    }
    /**
	 * @param $uid
     * @return Act293Model
     */
    public static function getAct293($uid){
        return self::getActModel(293,$uid);
    }

    /**
	 * @param $uid
     * @return Act294Model
     */
    public static function getAct294($uid){
        return self::getActModel(294,$uid);
    }

	/**
	 * @param $uid
	 * @return Act295Model
	 */
	public static function getAct295($uid){
		return self::getActModel(295,$uid);
	}

    /**
	 * @param $uid
     * @return Act296Model
     */
    public static function getAct296($uid){
        return self::getActModel(296,$uid);
    }

    /**
	 * @param $uid
     * @return Act297Model
     */
    public static function getAct297($uid){
        return self::getActModel(297,$uid);
    }

	/**
	 * @param $uid
	 * @return Act298Model
	 */
	public static function getAct298($uid){
		return self::getActModel(298,$uid);
	}
	
	/**
	 * @param $uid
	 * @return Act300Model
	 */
	public static function getAct300($uid){
	    return self::getActModel(300,$uid);
	}
	/**
	 * @param $uid
	 * @return Act301Model
	 */
	public static function getAct301($uid){
	    return self::getActModel(301,$uid);
	}
	/**
	 * @param $uid
	 * @return Act302Model
	 */
	public static function getAct302($uid){
	    return self::getActModel(302,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act303Model
	 */
	public static function getAct303($uid){
	    return self::getActModel(303,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act305Model
	 */
	public static function getAct304($uid){
	    return self::getActModel(304,$uid,Game::get_today_id());
	}
	/**
	 * @param $uid
	 * @return Act305Model
	 */
	public static function getAct305($uid){
	    return self::getActModel(305,$uid,Game::get_today_id());
	}
	
	/**
	 * @param $uid
	 * @return Act306Model
	 */
	public static function getAct306($uid){
	    return self::getActModel(306,$uid);
	}
	/**
	 * @param $uid
	 * @return Act307Model
	 */
	public static function getAct307($uid){
	    return self::getActModel(307,$uid);
	}
    /**
	 * @param $uid
     * @return Act308Model
     */
    public static function getAct308($uid){
        return self::getActModel(308,$uid);
    }

    /**
	 * @param $uid
     * @return Act310Model
     */
    public static function getAct310($uid){
        return self::getActModel(310,$uid);
    }

	/**
	 * @param $uid
	 * @return Act311Model
	 */
	public static function getAct311($uid){
		return self::getActModel(311,$uid);
	}

    /**
     * @param $uid
     * @return Act312Model
     */
    public static function getAct312($uid){
        return self::getActModel(312,$uid);
    }

    /**
     * @param $uid
     * @return Act313Model
     */
    public static function getAct313($uid){
        return self::getActModel(313,$uid);
    }

    /**
     * @param $uid
     * @return Act314Model
     */
    public static function getAct314($uid){
        return self::getActModel(314,$uid);
    }

    /**
	 * @param $uid
     * @return Act315Model
     */
    public static function getAct315($uid){
        return self::getActModel(315,$uid);
	}

	/**
	 * @param $uid
	 * @return Act316Model
	 */
	public static function getAct316($uid){
		return self::getActModel(316,$uid);
	}

	/**抽卡
	 * @param $uid
	 * @return Act317Model
	 */
	public static function getAct317($uid){
		return self::getActModel(317,$uid);
	}

	/**卡牌剧情
	 * @param $uid
	 * @return Act318Model
	 */
	public static function getAct318($uid){
		return self::getActModel(318,$uid);
	}

		/**四海奇珍
	 * @param $uid
	 * @return Act319Model
	 */
	public static function getAct319($uid){
		return self::getActModel(319,$uid);
	}

	/**四海奇珍剧情
	 * @param $uid
	 * @return Act320Model
	 */
	public static function getAct320($uid){
		return self::getActModel(320,$uid);
	}
	
	
    /**
	 * @return Act400Model
	 */
	public static function getAct400($uid){
	    return self::getActModel(400,$uid);
	}

    /**
     * @return Act500Model
     */
    public static function getAct500($uid){
        return self::getActModel(500,$uid);
	}
	
	/**
     * @return Act700Model
     */
    public static function getAct700($uid){
        return self::getActModel(700,$uid);
    }

	/**
     * @return Act701Model
     */
    public static function getAct701($uid){
        return self::getActModel(701,$uid);
	}
		
	/**
     * @return Act702Model
     */
    public static function getAct702($uid){
        return self::getActModel(702,$uid);
    }

    /**
     * @return Act703Model
     */
    public static function getAct703($uid){
        return self::getActModel(703,$uid,Game::get_today_id());
	}
		
	/**
     * @return Act704Model
     */
    public static function getAct704($uid){
        return self::getActModel(704,$uid);
    }

    /**
     * @return Act705Model
     */
    public static function getAct705($uid){
        return self::getActModel(705,$uid,Game::get_today_id());
	}
	
	/**
     * @return Act706Model
     */
    public static function getAct706($uid){
        return self::getActModel(706,$uid);
	}
	
	/**
     * @return Act707Model
     */
    public static function getAct707($uid){
        return self::getActModel(707,$uid);
	}
	
	/**
     * @return Act708Model
     */
    public static function getAct708($uid){
        return self::getActModel(708,$uid);
	}
	
	/**
     * @return Act709Model
     */
    public static function getAct709($uid){
        return self::getActModel(709,$uid);
	}
	
	/**
     * @return Act710Model
     */
    public static function getAct710($uid){
        return self::getActModel(710,$uid,Game::get_today_id());
	}
	
		/**
     * @return Act711Model
     */
    public static function getAct711($uid){
        return self::getActModel(711,$uid);
	}
	
		/**
     * @return Act712Model
     */
    public static function getAct712($uid){
        return self::getActModel(712,$uid);
	}

			/**
     * @return Act713Model
     */
    public static function getAct713($uid){
        return self::getActModel(713,$uid);
    }
	
		/**
     * @return Act714Model
     */
    public static function getAct714($uid){
        return self::getActModel(714,$uid);
	}
	
		/**
     * @return Act715Model
     */
    public static function getAct715($uid){
        return self::getActModel(715,$uid,Game::get_today_id());
	}

	public static function getAct716($uid){
        return self::getActModel(716,$uid);
	}


	/**
     * @return Act720Model
     */
    public static function getAct720($uid){
        return self::getActModel(720,$uid,Game::get_today_id());
	}

	/**
     * @return Act721Model
     */
    public static function getAct721($uid){
        return self::getActModel(721,$uid);
	}

	/**
     * @return Act722Model
     */
    public static function getAct722($uid){
        return self::getActModel(722,$uid);
	}

			/**
     * @return Act725Model
     */
    public static function getAct725($uid){
        return self::getActModel(725,$uid,Game::get_today_id());
	}

	/**
     * @return Act726Model
     */
    public static function getAct726($uid){
        return self::getActModel(726,$uid);
	}

	/**
     * @return Act730Model
     */
    public static function getAct730($uid){
        return self::getActModel(730,$uid);
	}

	/**
     * @return Act731Model
     */
    public static function getAct731($uid){
        return self::getActModel(731,$uid,Game::get_today_id());
	}

	/**
     * @return Act732Model
     */
    public static function getAct732($uid){
        return self::getActModel(732,$uid);
	}

	/**
     * @return Act733Model
     */
    public static function getAct733($uid){
        return self::getActModel(733,$uid);
	}

	/**
     * @return Act734Model
     */
    public static function getAct734($uid){
        return self::getActModel(734,$uid);
	}

	/**
     * @return Act735Model
     */
    public static function getAct735($uid){
        return self::getActModel(735,$uid);
	}

	/**
     * @return Act736Model
     */
    public static function getAct736($uid){
        return self::getActModel(736,$uid,Game::get_today_id());
	}

	/**
     * @return Act737Model
     */
    public static function getAct737($uid){
        return self::getActModel(737,$uid);
	}

	
	/**
     * @return Act738Model
     */
    public static function getAct738($uid){
        return self::getActModel(738,$uid);
	}

		/**
     * @return Act740Model
     */
    public static function getAct740($uid){
        return self::getActModel(740,$uid);
	}

	/**
     * @return Act741Model
     */
    public static function getAct741($uid){
        return self::getActModel(741,$uid,Game::get_today_id());
	}

	/**
     * @return Act742Model
     */
    public static function getAct742($uid){
        return self::getActModel(742,$uid,Game::get_week_id_new());
	}

	/**
     * @return Act743Model
     */
    public static function getAct743($uid){
        return self::getActModel(743,$uid);
	}

	/**
     * @return Act744Model
     */
    public static function getAct744($uid){
        return self::getActModel(744,$uid);
	}

	/**
     * @return Act745Model
     */
    public static function getAct745($uid){
        return self::getActModel(745,$uid);
	}

	/**
     * @return Act750Model
     */
    public static function getAct750($uid){
        return self::getActModel(750,$uid);
	}

		/**
     * @return Act751Model
     */
    public static function getAct751($uid){
        return self::getActModel(751,$uid,Game::get_today_id());
	}

	/**
     * @return Act755Model
     */
    public static function getAct755($uid){
        return self::getActModel(755,$uid);
	}
	
	/**
     * @return Act756Model
     */
    public static function getAct756($uid){
        return self::getActModel(756,$uid);
	}

	/**
     * @return Act757Model
     */
    public static function getAct757($uid){
        return self::getActModel(757,$uid);
	}

	/**
     * @return Act758Model
     */
    public static function getAct758($uid){
        return self::getActModel(758,$uid);
	}

	/**
     * @return Act759Model
     */
    public static function getAct759($uid){
        return self::getActModel(759,$uid);
	}

		/**
     * @return Act760Model
     */
    public static function getAct760($uid){
        return self::getActModel(760,$uid,Game::get_today_id());
	}

	/**
     * @return Act761Model
     */
    public static function getAct761($uid){
        return self::getActModel(761,$uid,Game::get_today_id());
	}

	/**
     * @return Act762Model
     */
    public static function getAct762($uid){
        return self::getActModel(762,$uid);
	}

	
	/**
     * @return Act763Model
     */
    public static function getAct763($uid){
        return self::getActModel(763,$uid);
	}

		
	/**
     * @return Act764Model
     */
    public static function getAct764($uid){
        return self::getActModel(764,$uid);
	}

	/**
     * @return Act765Model
     */
    public static function getAct765($uid){
        return self::getActModel(765,$uid);
	}

	
	/**
     * @return Act766Model
     */
    public static function getAct766($uid){
        return self::getActModel(766,$uid,Game::get_today_id());
	}

	/**
     * @return Act767Model
     */
    public static function getAct767($uid){
        return self::getActModel(767,$uid,Game::get_today_id());
	}

	/**
     * @return Act768Model
     */
    public static function getAct768($uid){
        return self::getActModel(768,$uid,Game::get_today_id());
	}

	/**
     * @return Act769Model
     */
    public static function getAct769($uid){
        return self::getActModel(769,$uid,Game::get_today_id());
	}

	/**
     * @return Act770Model
     */
    public static function getAct770($uid){
        return self::getActModel(770,$uid,Game::get_today_id());
	}

	/**
     * @return Act771Model
     */
    public static function getAct771($uid){
        return self::getActModel(771,$uid);
	}

	/**
     * @return Act772Model
     */
    public static function getAct772($uid){
        return self::getActModel(772,$uid,Game::get_today_id());
	}

    /**
     * @param $uid
     * @return Act1000Model
     */
    public static function getAct1000($uid){
        return self::getActModel(1000,$uid);
	}
	
	/***
     * @param $uid
     * @return Act2000Model
     */
    public static function getAct2000($uid){
        return self::getActModel(2000,$uid);
	}
	
	/***
     * @param $uid
     * @return Act2001Model
     */
    public static function getAct2001($uid){
        return self::getActModel(2001,$uid);
	}
	
	/***
     * @param $uid
     * @return Act2002Model
     */
    public static function getAct2002($uid){
        return self::getActModel(2002,$uid);
	}
	
	/***
     * @param $uid
     * @return Act2003Model
     */
    public static function getAct2003($uid){
        return self::getActModel(2003,$uid);
	}
	
	/***
     * @param $uid
     * @return Act2004Model
     */
    public static function getAct2004($uid){
        return self::getActModel(2004,$uid);
	}

	/***
     * @param $uid
     * @return Act2005Model
     */
    public static function getAct2005($uid){
        return self::getActModel(2005,$uid);
	}


    /**
     * @return Act6000Model
     */
    public static function getAct6000($uid){
        return self::getActModel(6000,$uid);
    }

    /**
     * @return Act6001Model
     */
    public static function getAct6001($uid){
        return self::getActModel(6001,$uid);
    }

    /**
     * @return Act6002Model
     */
    public static function getAct6002($uid){
        return self::getActModel(6002,$uid);
    }

    /**
     * @return Act6003Model
     */
    public static function getAct6003($uid){
        return self::getActModel(6003,$uid);
    }

    /**
     * @return Act6004Model
     */
    public static function getAct6004($uid){
        return self::getActModel(6004,$uid);
    }

    /**
     * @return Act6005Model
     */
    public static function getAct6005($uid){
        return self::getActModel(6005,$uid);
	}
	
	 /**
     * @return Act6006Model
     */
	public static function getAct6006($uid){
        return self::getActModel(6006,$uid);
    }

    /**
     * @return Act6010Model
     */
    public static function getAct6010($uid){
        return self::getActModel(6010,$uid);
    }

    /**
     * @return Act6011Model
     */
    public static function getAct6011($uid){
        return self::getActModel(6011,$uid);
    }

    /**
     * @return Act6012Model
     */
    public static function getAct6012($uid){
        return self::getActModel(6012,$uid);
    }

    /**
     * @return Act6014Model
     */
    public static function getAct6014($uid){
        return self::getActModel(6014,$uid);
    }

    /**
     * @return Act6015Model
     */
    public static function getAct6015($uid){
        return self::getActModel(6015,$uid);
    }

    /**
     * @return Act6100Model
     */
    public static function getAct6100($uid){
        return self::getActModel(6100,$uid);
    }

    /**
     * @return Act6101Model
     */
    public static function getAct6101($uid){
        return self::getActModel(6101,$uid);
    }

	/**
     * @return Act6102Model
     */
    public static function getAct6102($uid){
        return self::getActModel(6102,$uid);
    }

    /**
     * @return Act6103Model
     */
    public static function getAct6103($uid){
        return self::getActModel(6103,$uid);
    }

    /**
     * @return Act6104Model
     */
    public static function getAct6104($uid){
        return self::getActModel(6104,$uid);
    }

    /**
     * @return Act6105Model
     */
    public static function getAct6105($uid){
        return self::getActModel(6105,$uid);
    }

    /**
     * @return Act6106Model
     */
    public static function getAct6106($uid){
        return self::getActModel(6106,$uid);
    }

    /**
     * @return Act6107Model
     */
    public static function getAct6107($uid){
        return self::getActModel(6107,$uid);
    }

    /**
     * @return Act6110Model
     */
    public static function getAct6110($uid){
        return self::getActModel(6110,$uid);
    }

    /**
     * @return Act6111Model
     */
    public static function getAct6111($uid){
        return self::getActModel(6111,$uid);
    }

    /**
     * @return Act6120Model
     */
    public static function getAct6120($uid){
        return self::getActModel(6120,$uid);
    }

    /**
     * @return Act6121Model
     */
    public static function getAct6121($uid){
        return self::getActModel(6121, $uid);
    }

    /**
     * @return Act6122Model
     */
    public static function getAct6122($uid){
        return self::getActModel(6122, $uid);
    }

    /**
     * @return Act6123Model
     */
    public static function getAct6123($uid){
        return self::getActModel(6123, $uid);
    }

    /**
     * @return Act6130Model
     */
    public static function getAct6130($uid){
        return self::getActModel(6130,$uid);
    }

    /**
     * @return Act6131Model
     */
    public static function getAct6131($uid){
        return self::getActModel(6131,$uid);
    }

    /**
     * @return Act6132Model
     */
    public static function getAct6132($uid){
        return self::getActModel(6132,$uid);
    }

    /**
     * @return Act6133Model
     */
    public static function getAct6133($uid){
        return self::getActModel(6133,$uid);
    }

    /**
     * @return Act6134Model
     */
    public static function getAct6134($uid){
        return self::getActModel(6134,$uid);
    }

    /**
     * @return Act6135Model
     */
    public static function getAct6135($uid){
        return self::getActModel(6135,$uid);
    }

    /**
     * @return Act6136Model
     */
    public static function getAct6136($uid){
        return self::getActModel(6136,$uid);
    }

    /**
     * @return Act6137Model
     */
    public static function getAct6137($uid){
        return self::getActModel(6137,$uid);
    }

    /**
     * @return Act6138Model
     */
    public static function getAct6138($uid){
        return self::getActModel(6138,$uid);
    }

    /**
     * @return Act6139Model
     */
    public static function getAct6139($uid){
        return self::getActModel(6139,$uid);
    }
	
	/**
     * @return Act6140Model
     */
    public static function getAct6140($uid){
        return self::getActModel(6140,$uid);
    }

    /**
     * @return Act6150Model
     */
    public static function getAct6141($uid){
        return self::getActModel(6141,$uid);
    }

    /**
     * @return Act6142Model
     */
    public static function getAct6142($uid){
        return self::getActModel(6142, $uid);
    }

    /**
     * @return Act6143Model
     */
    public static function getAct6143($uid){
        return self::getActModel(6143, $uid);
	}
	
	   /**
     * @return Act6144Model
     */
    public static function getAct6144($uid){
        return self::getActModel(6144, $uid);
	}
	
	   /**
     * @return Act6145Model
     */
    public static function getAct6145($uid){
        return self::getActModel(6145, $uid);
    }

    /**
     * @return Act6150Model
     */
    public static function getAct6150($uid){
        return self::getActModel(6150,$uid);
    }

	/**
     * @return Act6150Model
     */
    public static function getAct6151($uid){
        return self::getActModel(6151,$uid);
    }

    /**
     * @return Act6152Model
     */
    public static function getAct6152($uid){
        return self::getActModel(6152,$uid);
    }

    /**
     * @return Act6153Model
     */
    public static function getAct6153($uid){
        return self::getActModel(6153,$uid);
    }

    /**
     * @return Act6154Model
     */
    public static function getAct6154($uid){
        return self::getActModel(6154,$uid);
    }

    /**
     * @return Act6160Model
     */
    public static function getAct6160($uid){
        return self::getActModel(6160,$uid);
    }

    /**
     * @return Act6161Model
     */
    public static function getAct6161($uid){
        return self::getActModel(6161,$uid);
    }

    /**
     * @return Act6162Model
     */
    public static function getAct6162($uid){
        return self::getActModel(6162,$uid);
    }

    /**
     * @return Act6163Model
     */
    public static function getAct6163($uid){
        return self::getActModel(6163,$uid);
    }

    /**
     * @return Act6164Model
     */
    public static function getAct6164($uid){
        return self::getActModel(6164,$uid);
    }

    /**
     * @return Act6165Model
     */
    public static function getAct6165($uid){
        return self::getActModel(6165,$uid);
    }

    /**
     * @return Act6166Model
     */
    public static function getAct6166($uid){
        return self::getActModel(6166,$uid);
    }

    /**
     * @return Act6167Model
     */
    public static function getAct6167($uid){
        return self::getActModel(6167,$uid);
    }

    /**
     * @return Act6168Model
     */
    public static function getAct6168($uid){
        return self::getActModel(6168,$uid);
    }

    /**
     * @return Act6169Model
     */
    public static function getAct6169($uid){
        return self::getActModel(6169,$uid);
    }

    /**
     * @return Act6170Model
     */
    public static function getAct6170($uid){
        return self::getActModel(6170,$uid);
    }

    /**
     * @return Act6171Model
     */
    public static function getAct6171($uid){
        return self::getActModel(6171,$uid);
    }

    /**
     * @return Act6172Model
     */
    public static function getAct6172($uid){
        return self::getActModel(6172,$uid);
    }

    /**
     * @return Act6173Model
     */
    public static function getAct6173($uid){
        return self::getActModel(6173,$uid);
    }

    /**
     * @return Act6174Model
     */
    public static function getAct6174($uid){
        return self::getActModel(6174,$uid);
    }

    /**
     * @return Act6175Model
     */
    public static function getAct6175($uid){
        return self::getActModel(6175,$uid);
    }

    /**
     * @return Act6176Model
     */
    public static function getAct6176($uid){
        return self::getActModel(6176,$uid);
    }

    /**
     * @return Act6177Model
     */
    public static function getAct6177($uid){
        return self::getActModel(6177,$uid);
    }

    /**
     * @return Act6178Model
     */
    public static function getAct6178($uid){
        return self::getActModel(6178,$uid);
    }

    /**
     * @return Act6179Model
     */
    public static function getAct6179($uid){
        return self::getActModel(6179,$uid);
    }

    /**
     * @return Act6180Model
     */
    public static function getAct6180($uid){
        return self::getActModel(6180,$uid);
    }

    /**
     * @return Act6181Model
     */
    public static function getAct6181($uid){
        return self::getActModel(6181,$uid);
    }

    /**
     * @return Act6182Model
     */
    public static function getAct6182($uid){
        return self::getActModel(6182,$uid);
    }

    /**
     * @return Act6183Model
     */
    public static function getAct6183($uid){
        return self::getActModel(6183,$uid);
    }

    /**
     * @return Act6184Model
     */
    public static function getAct6184($uid){
        return self::getActModel(6184,$uid);
    }

    /**
     * @return Act6185Model
     */
    public static function getAct6185($uid){
        return self::getActModel(6185,$uid);
    }

    /**
 * @return Act6186Model
 */
    public static function getAct6186($uid){
        return self::getActModel(6186,$uid);
    }

    /**
     * @return Act6187Model
     */
    public static function getAct6187($uid){
        return self::getActModel(6187,$uid);
    }

    /**
     * @return Act6188Model
     */
    public static function getAct6188($uid){
        return self::getActModel(6188,$uid);
    }

    /**
     * @return Act6189Model
     */
    public static function getAct6189($uid){
        return self::getActModel(6189,$uid);
    }

    /**
     * @return Act6190Model
     */
    public static function getAct6190($uid){
        return self::getActModel(6190,$uid);
    }

    /**
     * @return Act6191Model
     */
    public static function getAct6191($uid){
        return self::getActModel(6191,$uid);
    }

    /**
     * @return Act6192Model
     */
    public static function getAct6192($uid){
        return self::getActModel(6192,$uid);
    }

    /**
     * @return Act6193Model
     */
    public static function getAct6193($uid){
        return self::getActModel(6193,$uid);
    }

    /**
     * @return Act6194Model
     */
    public static function getAct6194($uid){
        return self::getActModel(6194,$uid);
    }

    /**
     * @return Act6200Model
     */
    public static function getAct6200($uid){
        return self::getActModel(6200,$uid);
    }

    /**
     * @return Act6201Model
     */
    public static function getAct6201($uid){
        return self::getActModel(6201,$uid);
    }

    /**
     * @return Act6202Model
     */
    public static function getAct6202($uid){
        return self::getActModel(6202,$uid);
    }

    /**
     * @return Act6203Model
     */
    public static function getAct6203($uid){
        return self::getActModel(6203,$uid);
    }

    /**
     * @return Act6204Model
     */
    public static function getAct6204($uid){
        return self::getActModel(6204,$uid);
    }

    /**
     * @return Act6205Model
     */
    public static function getAct6205($uid){
        return self::getActModel(6205,$uid);
    }

    /**
     * @return Act6206Model
     */
    public static function getAct6206($uid){
        return self::getActModel(6206,$uid);
    }

    /**
     * @return Act6207Model
     */
    public static function getAct6207($uid){
        return self::getActModel(6207,$uid);
    }

    /**
     * @return Act6208Model
     */
    public static function getAct6208($uid){
        return self::getActModel(6208,$uid);
    }

    /**
     * @return Act6210Model
     */
    public static function getAct6210($uid){
        return self::getActModel(6210,$uid);
    }

    /**
     * @return Act6211Model
     */
    public static function getAct6211($uid){
        return self::getActModel(6211,$uid);
    }

    /**
     * @return Act6212Model
     */
    public static function getAct6212($uid){
        return self::getActModel(6212,$uid);
    }

    /**
     * @return Act6213Model
     */
    public static function getAct6213($uid){
        return self::getActModel(6213,$uid);
    }

    /**
     * @return Act6214Model
     */
    public static function getAct6214($uid){
        return self::getActModel(6214,$uid);
    }

    /**
     * @return Act6215Model
     */
    public static function getAct6215($uid){
        return self::getActModel(6215,$uid);
    }

    /**
     * @return Act6216Model
     */
    public static function getAct6216($uid){
        return self::getActModel(6216,$uid);
    }

    /**
     * @return Act6217Model
     */
    public static function getAct6217($uid){
        return self::getActModel(6217,$uid);
    }

    /**
     * @return Act6218Model
     */
    public static function getAct6218($uid){
        return self::getActModel(6218,$uid);
    }

    /**
     * @return Act6219Model
     */
    public static function getAct6219($uid){
        return self::getActModel(6219,$uid);
    }

    /**
     * @return Act6220Model
     */
    public static function getAct6220($uid){
        return self::getActModel(6220,$uid);
    }

    /**
     * @return Act6221Model
     */
    public static function getAct6221($uid){
        return self::getActModel(6221,$uid);
    }

    /**
     * @return Act6222Model
     */
    public static function getAct6222($uid){
        return self::getActModel(6222,$uid);
    }

    /**
     * @return Act6223Model
     */
    public static function getAct6223($uid){
        return self::getActModel(6223,$uid);
    }

    /**
     * @return Act6224Model
     */
    public static function getAct6224($uid){
        return self::getActModel(6224,$uid);
    }

    /**
     * @return Act6225Model
     */
    public static function getAct6225($uid){
        return self::getActModel(6225,$uid);
    }

    /**
     * @return Act6226Model
     */
    public static function getAct6226($uid){
        return self::getActModel(6226,$uid);
    }

    /**
     * @return Act6227Model
     */
    public static function getAct6227($uid){
        return self::getActModel(6227,$uid);
    }

    /**
     * @return Act6228Model
     */
    public static function getAct6228($uid){
        return self::getActModel(6228,$uid);
    }

    /**
     * @return Act6229Model
     */
    public static function getAct6229($uid){
        return self::getActModel(6229,$uid);
    }

    /**
     * @return Act6230Model
     */
    public static function getAct6230($uid){
        return self::getActModel(6230,$uid);
    }

    /**
     * @return Act6231Model
     */
    public static function getAct6231($uid){
        return self::getActModel(6231,$uid);
    }
    /**
     * @return Act6232Model
     */
    public static function getAct6232($uid){
        return self::getActModel(6232,$uid);
	}
	
	/**
    * @return Act7010Model
    */
    public static function getAct7010($uid){
        return self::getActModel(7010,$uid);
	}
	
    /**
     * @return Act8002Model
     */
    public static function getAct8002($uid){
        return self::getActModel(8002,$uid);
    }
    /**
     * @return Act6233Model
     */
    public static function getAct6233($uid){
        return self::getActModel(6233,$uid);
    }

    /***
     * @param $uid
     * @return Act6234Model
     */
    public static function getAct6234($uid)
    {
        return self::getActModel(6234,$uid);
    }

    /***
     * @param $uid
     * @return Act6240Model
     */
    public static function getAct6240($uid)
    {
        return self::getActModel(6240,$uid);
    }

    /***
     * @param $uid
     * @return Act6241Model
     */
    public static function getAct6241($uid)
    {
        return self::getActModel(6241,$uid);
	}
	   /***
     * @param $uid
     * @return Act6241Model
     */
    public static function getAct6242($uid)
    {
        return self::getActModel(6242,$uid);
    }


    /***
     * @param $uid
     * @return Act6500Model
     */
    public static function getAct6500($uid)
    {
        return self::getActModel(6500,$uid);
    }

    /***
     * @param $uid
     * @return Act8003Model
     */
    public static function getAct8003($uid)
    {
        return self::getActModel(8003,$uid);
    }

    /***
     * @param $uid
     * @return Act8004Model
     */
    public static function getAct8004($uid)
    {
        return self::getActModel(8004,$uid);
    }

    /***
     * @param $uid
     * @return Act8005Model
     */
    public static function getAct8005($uid)
    {
        return self::getActModel(8005,$uid);
    }

    /***
     * @param $uid
     * @return Act8006Model
     */
    public static function getAct8006($uid)
    {
        return self::getActModel(8006,$uid);
    }

    /***
     * @param $uid
     * @return Act8007Model
     */
    public static function getAct8007($uid)
    {
        return self::getActModel(8007,$uid);
    }

    /***
     * @param $uid
     * @return Act8008Model
     */
    public static function getAct8008($uid)
    {
        return self::getActModel(8008,$uid);
    }

    /***
     * @param $uid
     * @return Act8009Model
     */
    public static function getAct8009($uid)
    {
        return self::getActModel(8009,$uid);
	}

	/***
     * @param $uid
     * @return Act8011Model
     */
    public static function getAct8011($uid)
    {
        return self::getActModel(8011,$uid);
	}

	    /**
     * @return Act8012Model
     */
    public static function getAct8012($uid){
        return self::getActModel(8012,$uid);
    }
	
		/***
     * @param $uid
     * @return Act8016Model
     */
    public static function getAct8016($uid)
    {
        return self::getActModel(8016,$uid);
    }
	
	/***
     * @param $uid
     * @return Act8018Model
     */
    public static function getAct8018($uid)
    {
        return self::getActModel(8018,$uid);
	}

	 /***
     * @param $uid
     * @return Act8020Model
     */
    public static function getAct8020($uid)
    {
        return self::getActModel(8020,$uid);
    }

	    /***
     * @param $uid
     * @return Act8022Model
     */
    public static function getAct8022($uid)
    {
        return self::getActModel(8022,$uid);
	}
	
	   /**
	 * @param $uid
	 * @return Act8023Model
	 */
	public static function getAct8023($uid){
		return self::getActModel(8023,$uid,Game::get_today_id());
	}

	    /***
     * @param $uid
     * @return Act8026Model
     */
    public static function getAct8026($uid)
    {
        return self::getActModel(8026,$uid);
    }

	public static function getAct9000($uid){
		return self::getActModel(9000,$uid);
	}

	   /***
     * @param $uid
     * @return Act8029Model
     */
    public static function getAct8029($uid)
    {
        return self::getActModel(8029,$uid);
    }

    /***
     * @param $uid
     * @return Act8500Model
     */
    public static function getAct8500($uid)
    {
        return self::getActModel(8500,$uid);
    }

    /**
	 * @return Redis1Model
	 */
	public static function getRedis1(){
		return self::getRedisModel(1);
	}
	/**
	 * @return Redis2Model
	 */
	public static function getRedis2(){
		return self::getRedisModel(2);
	}
	/**
	 * @return Redis3Model
	 */
	public static function getRedis3(){
		return self::getRedisModel(3);
	}
	/**
	 * @return Redis4Model
	 */
	public static function getRedis4(){
		return self::getRedisModel(4);
	}
	/**
	 * @return Redis5Model
	 */
	public static function getRedis5(){
		return self::getRedisModel(5,Game::get_today_id());
	}
	/**
	 * @return Redis6Model
	 */
	public static function getRedis6(){
		return self::getRedisModel(6);
	}
	/**
	 * @return Redis9Model
	 */
	public static function getRedis9(){
		return self::getRedisModel(9);
	}
	/**
	 * @return Redis10Model
	 */
	public static function getRedis10(){
		return self::getRedisModel(10);
	}
	
	/**
	 * @return Redis11Model
	 */
	public static function getRedis11(){
		return self::getRedisModel(11);
	}

	/**
	 * @return Redis18Model
	 */
	public static function getRedis18($key){
		return self::getRedisModel(18,$key);
	}

	/**
	 * @return Redis19Model
	 */
	public static function getRedis19($key){
		return self::getRedisModel(19,$key);
	}
	
	/**
	 * @return Redis20Model
	 */
	public static function getRedis20(){
		return self::getRedisModel(20);
	}
	/**
	 * @return Redis21Model
	 */
	public static function getRedis21($uid){
		return self::getRedisModel(21,$uid.'_'.Game::get_month_id());
	}

	/**
	 * @return Redis30Model
	 */
	public static function getRedis30($hid){
		return self::getRedisModel(30,$hid);

	}

	/**
	 * @return Redis31Model
	 */
	public static function getRedis31($hid){
		return self::getRedisModel(31,$hid);
	}

	/**
	 * @return Redis32Model
	 */
	public static function getRedis32(){
		return self::getRedisModel(32);
	}

	/**
	 * @return Redis33Model
	 */
	public static function getRedis33(){
		return self::getRedisModel(33);
	}
	
	/**
	 * @return Redis101Model
	 */
	public static function getRedis101($hid){
		return self::getRedisModel(101,$hid);
	}
	
	/**
	 * @return Redis102Model
	 */
	public static function getRedis102($hid){
		return self::getRedisModel(102,$hid);
	}
	
	/**
	 * @return Redis103Model
	 */
	public static function getRedis103($hid){
		return self::getRedisModel(103,$hid);
	}
	
	/**
	 * @return Redis104Model
	 */
	public static function getRedis104($hid){
		return self::getRedisModel(104,$hid);
	}
	
	/**
	 * @return Redis105Model
	 */
	public static function getRedis105($hid){
		return self::getRedisModel(105,$hid);
	}
	
	
	/**
	 * @return Redis106Model
	 */
	public static function getRedis106($hid){
	    return self::getRedisModel(106,$hid);
	}
	
	/**
	 * @return Redis107Model
	 */
	public static function getRedis107($hid){
	    return self::getRedisModel(107,$hid);
	}
	
	/**
	 * @return Redis108Model
	 */
	public static function getRedis108($hid){
	    return self::getRedisModel(108,$hid);
	}
	/**
	 * @return Redis109Model
	 */
	public static function getRedis109($hid){
	    return self::getRedisModel(109,$hid);
	}
	
	/**
	 * @return Redis110Model
	 */
	public static function getRedis110($hid){
	    return self::getRedisModel(110,$hid);
	}
	
	/**
	 * @return Redis111Model
	 */
	public static function getRedis111(){
	    return self::getRedisModel(111);
	}
	
	/**
	 * @return Redis112Model
	 */
	public static function getRedis112($hid){
	    return self::getRedisModel(112,$hid);
	}
	
	/**
	 * @return Redis113Model
	 */
	public static function getRedis113($hid){
	    return self::getRedisModel(113,$hid);
	}
	
	/**
	 * @return Redis114Model
	 */
	public static function getRedis114(){
	    return self::getRedisModel(114);
	}
	
	/**
	 * @return Redis115Model
	 */
	public static function getRedis115($hid){
	    return self::getRedisModel(115,$hid);
	}
	
	/**
	 * @return Redis116Model
	 */
	public static function getRedis116($hid){
	    return self::getRedisModel(116,$hid);
	}
	
	/**
	 * @return Redis117Model
	 */
	public static function getRedis117($hid){
	    return self::getRedisModel(117,$hid);
	}
	
	/**
	 * @return Redis118Model
	 */
	public static function getRedis118($hid){
	    return self::getRedisModel(118,$hid);
	}

	/**
	 * @return Redis119Model
	 */
	public static function getRedis119($hid){
		return self::getRedisModel(119,$hid);
	}

	/**
	 * @return Redis120Model
	 */
	public static function getRedis120($hid){
		return self::getRedisModel(120,$hid);
	}

	/**
	 * @return Redis123Model
	 */
	public static function getRedis123($hid){
		return self::getRedisModel(123,$hid);
	}

	/**
	 * @return Redis124Model
	 */
	public static function getRedis124($hid){
		return self::getRedisModel(124,$hid);
	}

	/**
	 * @return Redis125Model
	 */
	public static function getRedis125($hid){
		return self::getRedisModel(125,$hid);
	}

	/**
	 * @return Redis126Model
	 */
	public static function getRedis126($hid){
		return self::getRedisModel(126,$hid);
	}


	/**
	 * @return Redis127Model
	 */
	public static function getRedis127($hid){
		return self::getRedisModel(127,$hid,Game::get_today_id());
	}

	/**
	 * @return Redis128Model
	 */
	public static function getRedis128($hid){
		return self::getRedisModel(128,$hid);
	}

    /**
     * @return Redis131Model
     */
    public static function getRedis131($hid){
        return self::getRedisModel(131,$hid);
    }

    /**
     * @return Redis132Model
     */
    public static function getRedis132($hid){
        return self::getRedisModel(132,$hid);
    }

    /**
     * @return Redis133Model
     */
    public static function getRedis133(){
        return self::getRedisModel(133);
    }

    /**
     * @return Redis134Model
     */
    public static function getRedis134($hid){
        return self::getRedisModel(134,$hid);
    }

    /**
     * @return Redis137Model
     */
    public static function getRedis137($hid){
        return self::getRedisModel(137,$hid);
    }

    /**
     * @return Redis138Model
     */
    public static function getRedis138($hid){
        return self::getRedisModel(138,$hid);
    }

    /**
     * @return Redis139Model
     */
    public static function getRedis139(){
        return self::getRedisModel(139);
    }

    /**
     * @return Redis140Model
     */
    public static function getRedis140($hid){
        return self::getRedisModel(140,$hid);
    }

	/**
	 * @return Redis257Model
	 */
	public static function getRedis257($hid){
	    return self::getRedisModel(257,$hid);
	}

    /**
     * @return Redis258Model
     */
    public static function getRedis258($hid){
        return self::getRedisModel(258,$hid);
    }

    /**
     * @return Redis259Model
     */
    public static function getRedis259($hid){
        return self::getRedisModel(259,$hid);
    }
	
    /**
     * @return Redis301Model
     */
    public static function getRedis301(){
        return self::getRedisModel(301);
    }

    /**
     * @return Redis302Model
     */
    public static function getRedis302(){
        return self::getRedisModel(302);
    }
    
    /**
     * @return Redis303Model
     */
    public static function getRedis303($hid){
        return self::getRedisModel(303,$hid);
    }
    
    /**
     * @return Redis304Model
     */
    public static function getRedis304($hid){
        return self::getRedisModel(304,$hid);
    }
    
    /**
     * @return Redis305Model
     */
    public static function getRedis305($hid){
        return self::getRedisModel(305,$hid);
    }
    /**
     * @return Redis306Model
     */
    public static function getRedis306($hid){
        return self::getRedisModel(306,$hid);
    }
    /**
     * @return Redis307Model
     */
    public static function getRedis307($hid){
        return self::getRedisModel(307,$hid);
    }

    /**
     * @return Redis310Model
     */
    public static function getRedis310($hid){
        return self::getRedisModel(310,$hid);
    }

	/**
	 * @return Redis311Model
	 */
	public static function getRedis311($hid){
		return self::getRedisModel(311,$hid);
	}

    /**
     * @return Redis312Model
     */
    public static function getRedis312($hid){
        return self::getRedisModel(312,$hid);
    }

    /**
     * @return Redis315Model
     */
    public static function getRedis315($hid){
        return self::getRedisModel(315,$hid);
    }
    /**
     * @return Redis6010Model
     */
    public static function getRedis6010($hid){
        return self::getRedisModel(6010, $hid);
    }

    /**
     * @return Redis6015Model
     */
    public static function getRedis6015($hid){
        return self::getRedisModel(6015, $hid);
    }

    /**
     * @return Redis6104Model
     */
    public static function getRedis6104(){
        return self::getRedisModel(6104);
    }

    /**
     * @return Redis6105Model
     */
    public static function getRedis6105(){
        return self::getRedisModel(6105);
    }

    /**
     * @return Redis311Model
     */
    public static function getRedis6110(){
        return self::getRedisModel(6110);
    }

    /**
     * @return Redis6111Model
     */
    public static function getRedis6111(){
        return self::getRedisModel(6111, Game::get_today_id());
    }

    /**
     * @return Redis6112Model
     */
    public static function getRedis6112($hid){
        return self::getRedisModel(6112,$hid);
    }

    /**
     * @return Redis6113Model
     */
    public static function getRedis6113($hid){
        return self::getRedisModel(6113,$hid);
    }

    /**
     * @return Redis6114Model
     */
    public static function getRedis6114($hid){
        return self::getRedisModel(6114,$hid);
    }

    /**
     * @return Redis6142Model
     */
    public static function getRedis6123($hid){
        return self::getRedisModel(6123,$hid);
    }

    /**
     * @return Redis6135Model
     */
    public static function getRedis6135($hid){
        return self::getRedisModel(6135,$hid);
    }
	
	/**
     * @return Redis6140Model
     */
    public static function getRedis6140(){
        return self::getRedisModel(6140);
    }

    /**
     * @return Redis6142Model
     */
    public static function getRedis6142($hid){
        return self::getRedisModel(6142,$hid);
    }

    /**
     * @return Redis1000Model
     */
    public static function getRedis1000(){
        return self::getRedisModel(1000);
    }


    /**
     * @return Redis6166Model
     */
    public static function getRedis6166($hid){
        return self::getRedisModel(6166,$hid);
    }

    /**
     * @return Redis6167Model
     */
    public static function getRedis6167($hid){
        return self::getRedisModel(6167,$hid);
    }

    /**
     * @return Redis6200Model
     */
    public static function getRedis6185($hid){
        return self::getRedisModel(6185,$hid);
    }

    /**
     * @return Redis6187Model
     */
    public static function getRedis6187($hid){
        return self::getRedisModel(6187,$hid);
    }

    /**
     * @return Redis6190Model
     */
    public static function getRedis6190(){
        return self::getRedisModel(6190);
    }


    /**
     * @return Redis6192Model
     */
    public static function getRedis6192(){
        return self::getRedisModel(6192);
    }

    /**

     * @return Redis6200Model
     */
    public static function getRedis6200($hid){
        return self::getRedisModel(6200,$hid);
    }

    /**
     * @return Redis6201Model
     */
    public static function getRedis6201($hid){
        return self::getRedisModel(6201,$hid);
    }

    /**
     * @return Redis6202Model
     */
    public static function getRedis6202($hid){
        return self::getRedisModel(6202,$hid);
    }

    /**
     * @return Redis6203Model
     */
    public static function getRedis6203($hid){
        return self::getRedisModel(6203,$hid);
    }

    /**
     * @return Redis6204Model
     */
    public static function getRedis6204($hid){
        return self::getRedisModel(6204,$hid);
    }

    /**
     * @return Redis6205Model
     */
    public static function getRedis6205($hid){
        return self::getRedisModel(6205,$hid);
    }

    /**
     * @return Redis6206Model
     */
    public static function getRedis6206($hid){
        return self::getRedisModel(6206,$hid);
    }

    /**
     * @return Redis6207Model
     */
    public static function getRedis6207($hid){
        return self::getRedisModel(6207,$hid);
    }

    /**
     * @return Redis6208Model
     */
    public static function getRedis6208($hid){
        return self::getRedisModel(6208,$hid);
    }

    /**
     * @return Redis6215Model
     */
    public static function getRedis6215($hid){
        return self::getRedisModel(6215,$hid);
    }

    /**
     * @return Redis6216Model
     */
    public static function getRedis6216($hid){
        return self::getRedisModel(6216,$hid);
    }

    /**
     * @return Redis6217Model
     */
    public static function getRedis6217($hid){
        return self::getRedisModel(6217,$hid);
    }

    /**
     * @return Redis6218Model
     */
    public static function getRedis6218($hid){
        return self::getRedisModel(6218,$hid);
    }

    /**
     * @return Redis6219Model
     */
    public static function getRedis6219($hid){
        return self::getRedisModel(6219,$hid);
    }

    /**
     * @return Redis6220Model
     */
    public static function getRedis6220($hid){
        return self::getRedisModel(6220,$hid);
    }

    /**
     * @return Redis6221Model
     */
    public static function getRedis6221($hid){
        return self::getRedisModel(6221,$hid);
    }

    /**
     * @return Redis6222Model
     */
    public static function getRedis6222($hid){
        return self::getRedisModel(6222,$hid);
    }

    /**
     * @return Redis6223Model
     */
    public static function getRedis6223($hid){
        return self::getRedisModel(6223,$hid);
    }

    /**
     * @return Redis6224Model
     */
    public static function getRedis6224($hid){
        return self::getRedisModel(6224,$hid);
    }

    /**
     * @return Redis6226Model
     */
    public static function getRedis6226($hid){
        return self::getRedisModel(6226,$hid);
    }

    /**
     * @return Redis6227Model
     */
    public static function getRedis6227($hid){
        return self::getRedisModel(6227,$hid);
    }

    /**
     * @return Redis6229Model
     */
    public static function getRedis6229($hid){
        return self::getRedisModel(6229,$hid);
    }

    /**
     * @return Redis6230Model
     */
    public static function getRedis6230($hid){
        return self::getRedisModel(6230,$hid);
    }

    /**
     * @return Redis6231Model
     */
    public static function getRedis6231($hid){
        return self::getRedisModel(6231,$hid);
    }

    /**
     * @return Redis6232Model
     */
    public static function getRedis6232($hid){
        return self::getRedisModel(6232,$hid);
    }

    /**
     * @return Redis8002Model
     */
    public static function getRedis8002($hid){
        return self::getRedisModel(8002,$hid);
    }

    /***
     * @return Redis6234Model
     */
    public static function getRedis6234($hid){
        return self::getRedisModel(6234,$hid);
    }

    /***
     * @return Redis6241Model
     */
    public static function getRedis6241($hid){
        return self::getRedisModel(6241,$hid);
    }

    /***
     * @return Redis8003Model
     */
    public static function getRedis8003($hid){
        return self::getRedisModel(8003,$hid);
    }

    /***
     * @return Redis6183Model
     */
    public static function getRedis6183($hid){
        return self::getRedisModel(6183,$hid);
    }

    /***
     * @return Redis8005Model
     */
    public static function getRedis8005($hid){
        return self::getRedisModel(8005,$hid);
    }

    /***
     * @return Redis8006Model
     */
    public static function getRedis8006($hid){
        return self::getRedisModel(8006,$hid);
    }

    /***
     * @return Redis8008Model
     */
    public static function getRedis8008($hid){
        return self::getRedisModel(8008,$hid);
    }

    /***
     * @return Redis8009Model
     */
    public static function getRedis8009($hid){
        return self::getRedisModel(8009,$hid);
	}

	  /***
     * @return Redis8011Model
     */
    public static function getRedis8011($hid){
        return self::getRedisModel(8011,$hid);
	}
	
	/***
     * @return Redis8016Model
     */
    public static function getRedis8016($hid){
        return self::getRedisModel(8016,$hid);
    }
	
	/***
     * @return Redis8018Model
     */
    public static function getRedis8018($hid){
        return self::getRedisModel(8018,$hid);
	}

	
    /***
     * @return Redis8022Model
     */
    public static function getRedis8022($hid){
        return self::getRedisModel(8022,$hid);
    }
	
	    /***
     * @return Redis8026Model
     */
    public static function getRedis8026($hid){
        return self::getRedisModel(8026,$hid);
    }

    /***
     * @return Redis8029Model
     */
    public static function getRedis8029($hid){
        return self::getRedisModel(8029,$hid);
    }

    /**
	 * @return Sev1Model
	 */
	public static function getSev1(){
		return self::getSevModel(1);
	}
	/**
	 * @return Sev2Model
	 */
	public static function getSev2(){
		return self::getSevModel(2);
	}
	/**
	 * @return Sev3Model
	 */
	public static function getSev3(){
		return self::getSevModel(3,Game::get_today_id());
	}
    /**
     * @param int $did
     * @param int $cid
     * @param null $serverID
     * @return Sev5Model
     */
    public static function getSev5($did = 1,$cid = 1,$serverID=null){
        return self::getSevModel(5,$did,$cid,$serverID);
    }
	/**
	 * @return Sev6Model
	 */
	public static function getSev6(){
		return self::getSevModel(6);
	}
	/**
	 * @return Sev8Model
	 */
	public static function getSev8(){
		return self::getSevModel(8);
	}
	/**
	 * @return Sev9Model
	 */
	public static function getSev9($id){
		return self::getSevModel(9,1,$id);
	}
	/**
	 * @return Sev4Model
	 */
	public static function getSev4(){
		return self::getSevModel(4);
	}

	/**
	 * @return Sev10Model
	 */
	public static function getSev10($cid){
		return self::getSevModel(10,Game::get_today_id(),$cid);
	}
	
	/**
	 * @return Sev11Model
	 */
	public static function getSev11(){
		return self::getSevModel(11);
	}
	
	/**
	 * @return Sev12Model
	 */
	public static function getSev12($cid){
		return self::getSevModel(12,Game::get_today_id_9(),$cid);
	}
	
	/**
	 * @return Sev13Model
	 */
	public static function getSev13($cid){
		return self::getSevModel(13,Game::get_today_id_9(),$cid);
	}
	
	/**
	 * @return Sev14Model
	 */
	public static function getSev14($cid){
		return self::getSevModel(14,1,$cid);
	}
	
	/**
	 * @return Sev15Model
	 */
	public static function getSev15($cid){
		return self::getSevModel(15,Game::get_today_id(),$cid);
	}
	/**
	 * @return Sev16Model
	 */
	public static function getSev16($cid){
		return self::getSevModel(16,Game::get_today_id(),$cid);
	}

	/**
	 * @return Sev17Model
	 */
	public static function getSev17($cid){
		return self::getSevModel(17,1,$cid);
	}

	/**
	 * @return Sev18Model
	 */
	public static function getSev18($cid){
		return self::getSevModel(18,Game::get_today_id(),$cid);
	}
	
	
	/**
	 * @return Sev19Model
	 */
	public static function getSev19(){
		return self::getSevModel(19);
	}

	/**
	 * @return Sev20Model
	 */
	public static function getSev20($clubid){
		return self::getSevModel(20,1,$clubid);
	}
	/**
	 * @return Sev21Model
	 */
	public static function getSev21(){
		return self::getSevModel(21);
	}
	
	/**
	 * @return Sev22Model
	 */
	public static function getSev22(){
		return self::getSevModel(22);
	}
    /**
     * @return Sev23Model
     */
    public static function getSev23(){
        return self::getSevModel(23);
    }
	/**
	 * @return Sev24Model
	 */
	public static function getSev24($clubid){
		return self::getSevModel(24,1,$clubid);
	}
    /**
     * @return Sev25Model
     */
    public static function getSev25(){
        return self::getSevModel(25);
    }
    /**
     * @return Sev26Model
     */
    public static function getSev26(){
        return self::getSevModel(26);
    }
    /**
     * @return Sev27Model
     */
    public static function getSev27(){
        return self::getSevModel(27);
    }
    /**
     * @return Sev28Model
     */
    public static function getSev28(){
        return self::getSevModel(28);
    }
    
	/**
	 * @return Sev29Model
	 */
	public static function getSev29(){
		return self::getSevModel(29);
	}
	
    /**
     * @return Sev30Model
     */
    public static function getSev30($hdid){
        return self::getSevModel(30,$hdid);
    }
    
    /**
     * @return Sev31Model
     */
    public static function getSev31(){
        return self::getSevModel(31);
    }

    /**
     * @return Sev32Model
     */
    public static function getSev32($hdid){
        return self::getSevModel(32,Game::get_today_id(),$hdid);
    }
    /**
     * @return Sev33Model
     */
    public static function getSev33(){
        return self::getSevModel(33);
    }
    /**
     * @return Sev34Model
     */
    public static function getSev34(){
        return self::getSevModel(34);
    }
    /**
     * @return Sev35Model
     */
    public static function getSev35(){
        return self::getSevModel(35);
    }
    /**
     * @return Sev36Model
     */
    public static function getSev36($hdid){
        return self::getSevModel(36,Game::get_today_id(),$hdid);
    }
    /**
     * @return Sev37Model
     */
    public static function getSev37($hdid){
        return self::getSevModel(37,$hdid);
    }
    /**
     * @return Sev38Model
     */
    public static function getSev38(){
        return self::getSevModel(38);
    }
    /**
     * @return Sev39Model
     */
    public static function getSev39(){
        return self::getSevModel(39);
    }
    /**
     * @return Sev40Model
     */
    public static function getSev40($hdid){
        return self::getSevModel(40,$hdid);
    }
    /**
     * @return Sev41Model
     */
    public static function getSev41($hdid){
        return self::getSevModel(41,$hdid);
    }
    /**
     * @return Sev42Model
     */
    public static function getSev42(){
        return self::getSevModel(42);
    }
    
    /**
     * @return Sev43Model
     */
    public static function getSev43($hdid){
        return self::getSevModel(43,Game::get_today_id(),$hdid);
    }
    /**
     * @return Sev44Model
     */
    public static function getSev44($hdid){
        return self::getSevModel(44,$hdid);
    }
    
    /**
     * @return Sev45Model
     */
    public static function getSev45($hdid){
        return self::getSevModel(45,Game::get_today_id(),$hdid);
    }
    /**
     * @return Sev46Model
     */
    public static function getSev46($hdid){
        return self::getSevModel(46,$hdid);
    }
    /**
     * @return Sev47Model
     */
    public static function getSev47(){
        return self::getSevModel(47);
    }
    /**
     * @return Sev48Model
     */
    public static function getSev48(){
        return self::getSevModel(48,Game::get_today_id());
    }
    /**
     * @return Sev50Model
     */
    public static function getSev50($ksid = null){
        return self::getSevModel(50,Game::club_pk_id(),1,$ksid);
    }
    /**
     * @return Sev51Model
     */
    public static function getSev51($cid,$ksid = null){
        return self::getSevModel(51,Game::club_pk_id(),$cid,$ksid);
    }
    /**
     * @return Sev52Model
     */
    public static function getSev52($ksid = null){
        return self::getSevModel(52,Game::club_pk_id(),1,$ksid);
    }
    /**
     * @return Sev53Model
     */
    public static function getSev53($cid,$ksid = null){
        return self::getSevModel(53,Game::club_pk_id1(),$cid,$ksid);
    }
    
    /**
     * @return Sev54Model
     */
    public static function getSev54($cid,$ksid = null){
        return self::getSevModel(54,1,$cid,$ksid);
    }
    /**
     * @return Sev55Model
     */
    public static function getSev55($cid,$ksid = null){
        return self::getSevModel(55,Game::club_pk_id1(),$cid,$ksid);
    }
    /**
     * @return Sev56Model
     */
    public static function getSev56($ksid = null){
        return self::getSevModel(56,Game::club_pk_id1(),1,$ksid);
    }
    /**
     * @return Sev57Model
     */
    public static function getSev57($cid,$ksid = null){
        return self::getSevModel(57,Game::club_pk_id1(),$cid,$ksid);
    }
    
    /**
     * @return Sev60Model
     */
    public static function getSev60($hdid){
        return self::getSevModel(60,$hdid);
    }
    /**
     * @return Sev61Model
     */
    public static function getSev61(){
        return self::getSevModel(61);
    }
    /**
     * @return Sev62Model
     */
    public static function getSev62(){
        return self::getSevModel(62);
    }

	/**
	 * @return Sev70Model
	 */
	public static function getSev70($hdid){
		return self::getSevModel(70,Game::get_today_id(),$hdid);
	}
	/**
	 * @return Sev71Model
	 */
	public static function getSev71($hdid){
		return self::getSevModel(71,$hdid);
	}

	/**
	 * @return Sev72Model
	 */
	public static function getSev72($hdid){
		return self::getSevModel(72,Game::get_today_id(),$hdid);
	}
	/**
	 * @return Sev73Model
	 */
	public static function getSev73($hdid){
		return self::getSevModel(73,$hdid);
	}
	
	/**
	 * @return Sev80Model
	 */
	public static function getSev80($hdid){
		return self::getSevModel(80,$hdid);
	}
	
	/**
	 * @return Sev81Model
	 */
	public static function getSev81($hdid){
		return self::getSevModel(81,$hdid);
	}
    /**
     * @return Sev82Model
     */
    public static function getSev82($hdid){
        return self::getSevModel(82,$hdid);
    }

    /**
     * @return Sev80Model
     */
    public static function getSev83($hdid){
        return self::getSevModel(83,$hdid);
    }

    /**
     * @return Sev81Model
     */
    public static function getSev84($hdid){
        return self::getSevModel(84,$hdid);
    }

    /**
     * @return Sev90Model
     */
    public static function getSev90(){
        return self::getSevModel(90,Game::get_today_id());
    }

	/**
	 * @return Sev91Model
	 */
	public static function getSev91(){
		return self::getSevModel(91);
	}

    /**
     * @return Sev92Model
     */
    public static function getSev92(){
        return self::getSevModel(92);
    }

    /**
     * @return Sev93Model
     */
    public static function getSev93(){
        return self::getSevModel(93);
	}

	    /**
     * @return Sev100Model
     */
    public static function getSev100($cid){
        return self::getSevModel(100,1,$cid);
    }

	/**
	 * @return Sev200Model
	 */
	public static function getSev200($hid){
		return self::getSevModel(200,$hid);
	}

	/**
	 * @return Sev201Model
	 */
	public static function getSev201($hid){
		return self::getSevModel(201,$hid);
	}

    /**
     * @return Sev313Model
     */
    public static function getSev313($hid, $servid){
        return self::getSevModel(313,$hid, 1, $servid);
    }

    /**
     * @return Sev314Model
     */
    public static function getSev314($hid, $servid){
        return self::getSevModel(314,$hid, 1, $servid);
    }

    /**
     * @return SevModel
     */
    public static function getSev($cid, $did, $hdid){
        return self::getSevModel($cid, $did, $hdid);
    }


    /**
     * @return Sev6010Model
     */
    public static function getSev6010(){
        return self::getSevModel(6010);
    }

    /**
     * @return Sev6012Model
     */
    public static function getSev6012(){
        return self::getSevModel(6012);
    }

    /**
     * @return Sev6013Model
     */
    public static function getSev6013(){
        return self::getSevModel(6013);
    }

    /**
     * @return Sev6015Model
     */
    public static function getSev6015($hid){
        return self::getSevModel(6015, $hid);
    }

    /**
     * @return Sev6136Model
     */
    public static function getSev6123($hid){
        return self::getSevModel(6123,$hid);
    }

    /**
     * @return Sev6136Model
     */
    public static function getSev6136($hid){
        return self::getSevModel(6136,$hid);
    }

    /**
     * @return Sev6137Model
     */
    public static function getSev6137($hid){
        return self::getSevModel(6137,$hid);
    }

    /**
     * @return Sev6168Model
     */
    public static function getSev6168($hid){
        return self::getSevModel(6168,$hid);
    }

    /**
     * @return Sev6183Model
     */
    public static function getSev6183($hid){
        return self::getSevModel(6183,$hid);
    }

    /**
     * @return Sev6188Model
     */
    public static function getSev6188($hid){
        return self::getSevModel(6188,$hid);
    }

    /**
     * @return Sev6189Model
     */
    public static function getSev6189($hid){
        return self::getSevModel(6189,$hid);
    }

    /**
     * @return Sev6190Model
     */
    public static function getSev6190(){
        return self::getSevModel(6190);
    }

    /**
     * @return Sev6220Model
     */
    public static function getSev6220($hid){
        return self::getSevModel(6220,$hid);
    }

    /**
     * @return Sev6221Model
     */
    public static function getSev6221($hid){
        return self::getSevModel(6221,$hid);
    }

    /**
     * @return Sev6222Model
     */
    public static function getSev6222($hid){
        return self::getSevModel(6222,$hid);
    }

    /**
     * @return Sev6227Model
     */
    public static function getSev6227($hid){
        return self::getSevModel(6227,$hid);
    }

    /**
     * @return Sev6228Model
     */
    public static function getSev6228($hid){
        return self::getSevModel(6228,$hid);
    }

    /**
     * @return Sev6229Model
     */
    public static function getSev6229($hid){
        return self::getSevModel(6229,$hid);
    }

    /**
     * @return Sev6230Model
     */
    public static function getSev6230($hid){
        return self::getSevModel(6230,$hid);
    }

    /**
     * @return Sev6231Model
     */
    public static function getSev6231($hid){
        return self::getSevModel(6231,$hid);
    }
    /**
     * @return Sev6232Model
     */
    public static function getSev6232($hid){
        return self::getSevModel(6232,$hid);
    }

    /**
     * @return Sev8002Model
     */
    public static function getSev8002($hid){
        return self::getSevModel(8002,$hid);
    }

    /***
     * @return Sev6234Model
     */
    public static function getSev6234($hid)
    {
        return self::getSevModel(6234, $hid);
    }

    /**
     * @return Sev8003Model
     */
    public static function getSev8003($hid){
        return self::getSevModel(8003,$hid);
    }

    /**
     * @return Sev8003Model
     */
    public static function getSev94($hid, $servid){
        return self::getSevModel(94,$hid,1, $servid);
	}
	
		/**
	 * @return Sev707Model
	 */
	public static function getSev707(){
		return self::getSevModel(707,Game::get_today_id());
	}

    /***
     * @param $uid
     * @return Act7001Model
     */
    public static function getAct7001($uid)
    {
        return self::getActModel(7001,$uid);
    }

	/**
	 * @return TeamModel
	 */
	public static function getTeam($uid){
		if (!self::$team[$uid]){
			//载入类
			Common::loadModel("TeamModel");
			self::$team[$uid] = new TeamModel($uid);
		}
		return self::$team[$uid];
	}
	
	/**
	 * 获取阵法数据
	 */
	public static function get_team($uid){
		$TeamModel = self::getTeam($uid);
		return $TeamModel->info;
	}
	
	/*
	 * 常用功能打包
	 * 获取当前VIP档次配置
	 */
	public static function get_vip_cfg_info($uid){
		$UserModel = self::getUser($uid);
		return Game::getcfg_info('vip',$UserModel->info['vip']);
	}

    /*
     * VIP等级限制
     */
    public static function vip_limit($uid,$lv=1,$error='PARAMS_ERROR'){
        $UserModel = self::getUser($uid);
        if ($UserModel->info['vip'] < $lv){
            self::error($error);
        }
    }
	
	
	/*
	 * 检查活动类 如果有更新 则刷新活动信息
	 */
	public static function click_act_udata(){
		if (empty(self::$act_models)){
			return;
		}
		foreach (self::$act_models as $user_model){
			foreach ($user_model as $k_actmodel => $v_actmodel){
				if (!empty($v_actmodel) && $v_actmodel->_update){
					$v_actmodel->back_data_u();
				}
			}
		}
	}
	
	/**
	 * 检查所有类是否更新  写入
	 */
	public static function click_destroy(){
		foreach (self::$models as $user_model){
			foreach ($user_model as $k_model => $v_model){
				//如果存在 并且有更新
				if (!empty($v_model) && $v_model->_update){
					$v_model->destroy();
				}
			}
		}
	}
	
	/*
	 * 添加一个英雄更新标记
	 */
	public static function add_hero_rst($hid){
		self::$u_heros[$hid] = 1;
	}
	/*
	 * 添加一个羁绊红颜更新标记
	 */
	public static function add_wife_rst($wid){
		self::$u_wifes[$wid] = 1;
	}
	/*
	 * 添加一个需要输出的更新信息标记
	 */
	public static function add_u_type($type){
		self::$u_types[$type] = true;
		
	}
	
	/*
	 * 更新标记的英雄信息
	 */
	public static function back_hero_rst($uid){
		if (empty(self::$u_heros)
		&& empty(self::$u_types)){
			return;
		}
		$TeamModel = self::getTeam($uid);
		$TeamModel->reset(1);
		
		//返回更新的英雄信息
		if (!empty(self::$u_heros)){
			$TeamModel->back_hero_info(self::$u_heros);
		}
		//返回更新的其他各字段信息
		if (!empty(self::$u_types)){
			foreach (self::$u_types as $k => $v){
				switch ($k){
					case 'alllove':
						Master::back_data($uid,'wife','base',array('allLove'=>$TeamModel->info['alllove']),true);
					break;
				}
			}
		}
	}
	
	/*
	 * 添加通用弹窗返回信息
	 */
	public static function back_basewin_item($itemid,$count,$kind = 1){
		if (!isset(self::$bak_data['a']['msgwin']['items'])){
			self::$bak_data['a']['msgwin']['items'] = array();
		}
		self::$bak_data['a']['msgwin']['items'][] = array('kind'=>$kind,'id'=>$itemid,'count'=>$count);
	}
	
	/*
	 * 添加弹窗返回信息
	 * 弹窗里面的道具列表
	 */
	public static function back_win_item($mol,$wname,$itemid,$count,$kind = 1){
		self::$bak_data['a'][$mol]['win'][$wname]['items'][] = array('kind'=>$kind,'id'=>$itemid,'count'=>$count);
	}
	/*
	 * 添加弹窗返回信息
	 */
	public static function back_win($mol,$wname,$key,$date){
		self::$bak_data['a'][$mol]['win'][$wname][$key] = $date;
	}
	/*
	 * 添加自定义弹窗返回信息
	 */
	public static function back_custom_data($mol,$ctrl,$data){
	    if (empty($data)){
	        $data = array();
        }
		self::$bak_data['a'][$mol][$ctrl] = $data;
	}
	/*
	 * 累加数组型 弹窗返回信息
	 */
	public static function back_win_array($mol,$wname,$date){
		if(!isset(self::$bak_data['a'][$mol]['win'][$wname])){
			self::$bak_data['a'][$mol]['win'][$wname] = array();
		}
		self::$bak_data['a'][$mol]['win'][$wname][] = $date;
	}
	
	/*
	 * 添加返回数据
	 */
	public static function back_data($uid,$mol,$ctrl,$data,$is_u = false){
		if ($uid == self::$uid || $uid == 0){
			//本用户信息更新
			if (!is_array($data)){
				Master::error('back_data_err:'.$mol.'_'.$ctrl);
			}
			$bag_key = 'a';
			if ($is_u){//如果是要更新
				$bag_key = 'u';
			}elseif(empty($data)){
				//如果是空数组 则清空本项
				self::$bak_data[$bag_key][$mol][$ctrl] = array();
			}
			foreach($data as $k => $v){
				//如果是数字下标 则累加 否则 按KEY覆盖
				if (is_int($k)){
					self::$bak_data[$bag_key][$mol][$ctrl][] = $v;
				}else{
					self::$bak_data[$bag_key][$mol][$ctrl][$k] = $v;
				}
			}
		}else{
			//对其他用户的更新信息
			//插入信息待发队列
			Common::loadModel("OtherMsgModel");
			OtherMsgModel::back_data($uid,$mol,$ctrl,$data,$is_u);
		}
		
		
	}
	
	/*
	 * 改变S的值
	 */
	public static function back_s($value){
		self::$bak_data['s'] = $value;
	}
	
	/*
	 * 清空输出信息
	 */
	public static function clear_bak(){
		self::$bak_data = array(
			's' => 1,
			'a' => array(),
			'u' => array(),
		);
	}

	public static function checkParam(&$param)
    {
        if (!isset($param['rsn'])) {//强制验证
            Master::error(PARAMS_ERROR.'param'.__LINE__);
        }

        $rsn = $param['rsn'];
        //解密
        $deCodeRand = substr($rsn, 0, 1);
        $arr = array(
            array('3','1','5','8','9','7','4','2','0','6'),
            array('0','5','3','2','1','7','9','4','8','6'),
            array('1','0','6','7','3','8','2','5','4','9'),
            array('6','1','5','4','2','9','0','3','8','7'),
            array('7','6','0','2','5','8','1','4','9','3'),
            array('6','5','3','4','0','2','8','1','7','9'),
            array('9','6','1','4','0','5','3','2','8','7'),
            array('8','9','3','1','5','7','0','6','4','2'),
            array('6','2','4','9','1','5','3','8','0','7'),
        );
        $newTime = str_replace(
            $arr[$deCodeRand - 1],
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
            substr($rsn, 1)
        );
        $time = substr($newTime, 1) >> substr($newTime, 0, 1);
        $newTime = intval(substr(Game::get_now(), 0, 2) . $time);
        if (Game::is_over($newTime + 60)) {
            Master::error(PARAMS_ERROR.'param'.__LINE__);
        }
        unset($param['rsn']);
    }
	
	/*
	 * 报错退出
	 */
	public static function error($msg = '',$type = 0){
		//释放所有锁
		self::free_all_lock();
		//输出返回
		self::$bak_data['s'] = 0;
		self::$bak_data['u'] = array();
		self::$bak_data['a'] = array(
				'system' => array(
					'errror' => array(
						'type' => empty($type)?0:$type+10000,
						'msg' => $msg,
					),
				),
			);
		self::setTime();
		if(defined('SP_DECODE') && SP_DECODE){
			$data = array();
			$out_data = self::multimerge($data,self::$bak_data);
			require_once LIB_DIR . '/aes.php';
            $aes = new AES();
            $rtn_data = json_encode($out_data);
			$rtn_data = $aes->encrypt($rtn_data, $aes->getSecretKey());
			echo $rtn_data;
		}else{
			echo json_encode(self::$bak_data);
		}
		exit;
	}
	/*
	 * 报错不退出
	 */
	public static function error_msg($msg = '',$type = 0){
		//输出返回
		self::$bak_data['a']['system']['errror'] = array(
				'type' => $type,
				'msg' => $msg,
		);
	}

	/**
	 * 不返回报错信息但是有返回状态
	 * @param $s
	 */
	public static function back_s_end($s){
		//释放所有锁
		self::free_all_lock();
		//输出返回
		self::$bak_data['s'] = $s;
		self::setTime();
		echo json_encode(self::$bak_data);
		exit;
	}
	
	/*
	 * 输出返回数据
	 */
	public static function output($uid,$endode = 0){
		$data = array();
		if (!empty($uid)){
			//检查异步返回信息
			Common::loadModel("OtherMsgModel");
			$data = OtherMsgModel::output_data($uid);
		}
		//结合两个数组
		$out_data = self::multimerge($data,self::$bak_data);
		//数组递归结合

        //数据监测
        $cs_sc_uid = Game::get_peizhi('cs_sc_uid');
        if(!empty($cs_sc_uid) && in_array($uid, $cs_sc_uid)){
            $url_this = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $trace_data = array(
                'url'=>$url_this,
                'time'=>date('Y-m-d H:i:s'),
                'usTime'=>microtime(true) - $GLOBALS['microtime'],
                'cs'=>file_get_contents("php://input"),
                'sc'=>json_encode($out_data),
            );
            $file_name = '/tmp/cs_sc_uid_log_'.date("Ymd");
            if( defined('FILE_PATH') && FILE_PATH ){
                $file_name = FILE_PATH . 'cs_sc_uid_log_'.date("Ymd");
            }
            Common::logMsg($file_name, var_export($trace_data, true).PHP_EOL, FILE_APPEND);
		}
		//检查数据结构
		$rtn_data = json_encode($out_data);
		if($endode == 1)
		{
			require_once LIB_DIR . '/aes.php';
			$aes = new AES();
			$rtn_data = $aes->encrypt($rtn_data,$aes->getSecretKey());
		}
		echo $rtn_data;
	}
	
	/*
	 * 数组联合
	 * 以参数2 为最终标准
	 */
	public static function multimerge($ar1,$ar2){
		foreach ($ar1 as $key => $value){
			if (is_array($value)){
				$ar2[$key] = self::multimerge($value,$ar2[$key]);
			}else{
				if (!isset($ar2[$key])){
					$ar2[$key] = $value;
				}
			}
		}
		return $ar2;
	}

	/**
	 * 获取玩家信息--完整简化
	 * @param $uid
	 * @return array
	 */
	public static function getUserInfo($uid){
		//优化加速?

		static $user_info;
		if ( empty( $user_info[$uid] ) ) {
			//玩家信息
			$fUserModel = self::getUser($uid);
			$fUser_info = $fUserModel->info;
			$fUser_info['name'] = Game::filter_char($fUser_info['name']);
			//获取玩家称号
			$Act25Model = Master::getAct25($uid);
			$chenghao = $Act25Model->outf['setid'];

			//获取玩家势力
			$Act99Model = Master::getAct99($uid);
			$shili = array_sum($Act99Model->info['ep']);

			//获取头像框
            $Act6151Model = Master::getAct6151($uid);
            $headavatar = $Act6151Model->info;

            //获取头像框
            $Act6141Model = Master::getAct6141($uid);
            $userClothe = $Act6141Model->info;

			//玩家个人信息
			$user_info[$uid] = array(
				'id'=> $fUser_info['uid'],	//玩家UID
				'name'=> $fUser_info['name'],  //名字
				'job'=> empty($fUser_info['job'])?1:intval($fUser_info['job']),  //头像
				'sex'=> intval($fUser_info['sex']),  //性别
				'level'=> intval($fUser_info['level']),  //官阶
				'vip'=> intval($fUser_info['vip']),  //VIP
				'chenghao'=> intval($chenghao),  //称号
                'clothe' => $userClothe,
				'headavatar'=>$headavatar,
				'shili'=> intval($shili),  //势力
			);
		}
		return $user_info[$uid];

	}
	
	/**
	 * 获取玩家信息--简略信息
	 * @param $fuid 玩家fuid
	 */
	public static function fuidInfo($fuid){
		//优化加速?	
		
		static $fuser_data;
		if ( empty( $fuser_data[$fuid] ) ) {
			//玩家信息
			$fUserModel = self::getUser($fuid);
			$fUser_info = $fUserModel->info;
			$fUser_info['name'] = Game::filter_char($fUser_info['name']);
			//获取玩家称号
			$Act25Model = Master::getAct25($fuid);
			$chenghao = $Act25Model->outf['setid'];

            //获取头像框
            $Act6151Model = Master::getAct6151($fuid);
            $headavatar = $Act6151Model->info;

            //获取头像框
            $Act6141Model = Master::getAct6141($fuid);
            $userClothe = $Act6141Model->info;

            $Redis6Model = Master::getRedis6();
			$yamenScore = $Redis6Model->zScore($fuid);

			$TeamModel = Master::getTeam($fuid);
			$fetterLv = $TeamModel->info['fetterlv'];

			$Act759Model = Master::getAct759($fuid);
			$specialId = $Act759Model->info['sepcial'][$userClothe['body']];

			//玩家个人信息
			$fuser_data[$fuid] = array(
				'uid'=> $fUser_info['uid'],	//玩家UID
				'name'=> $fUser_info['name'],  //名字
				'job'=> empty($fUser_info['job'])?1:intval($fUser_info['job']),  //头像
				'sex'=> intval($fUser_info['sex']),  //性别
				'level'=> intval($fUser_info['level']),  //官阶
				'vip'=> intval($fUser_info['vip']),  //VIP
                'clothe' => $userClothe,
                'headavatar'=>$headavatar,
				'chenghao'=> intval($chenghao),  //称号
				'yamenScore' => intval($yamenScore),
				'fetterlv' => $fetterLv,
				'clotheSpecial' => intval($specialId),
			);
		}
		return $fuser_data[$fuid];
		
	}

	public static function getFriendInfo($fuid) {
		$fUserModel = Master::getUser($fuid);
		$fUser_info = $fUserModel->info;
		$fTeamModel = Master::getTeam($fuid); //阵法
		$fTeam_info = $fTeamModel->info;
		$Act6151Model = Master::getAct6151($fuid);
		$headavatar = $Act6151Model->info;
		//获取玩家称号
		$Act25Model = Master::getAct25($fuid);
		$chenghao = $Act25Model->outf['setid'];
		$fuid_Data = array(
			'uid'=> $fUser_info['uid'],	//玩家UID
			'name'=> $fUser_info['name'],  //名字
			'shili' => array_sum($fTeam_info['allep']),
			'vip'=> $fUser_info['vip'],  //VIP
			'lastlogin' => $fUser_info['lastlogin'], // 最后登陆时间
			'job'=> empty($fUser_info['job'])?1:intval($fUser_info['job']),
			'headavatar'=>$headavatar,//头像
			'chenghao'=>$chenghao,//头像
		);
		unset($fUserModel, $fUser_info, $fTeamModel, $fTeam_info);
		return $fuid_Data;
	}
	
	/**
	 * 获取玩家完整信息
	 * @param int $fuid
	 */
	public static function fuidData($fuid){
		//优化加速?
		
		//玩家信息
		$fUserModel = Master::getUser($fuid);
		$fUser_info = $fUserModel->info;
		
		$fUser_info['name'] = Game::filter_char($fUser_info['name']);
		
		//阵法
		$fTeamModel = Master::getTeam($fuid);
		$fTeam_info = $fTeamModel->info;
		
		//中地图ID加入返回信息里面
		//$fUser_info['mmap'] = ceil(($fUser_info['smap']+1)/8);
        $smap_cfg = Game::getcfg_info('pve_smap',intval($fUser_info['smap']) + 1);
        $fUser_info['mmap'] = intval($smap_cfg['mmap']);


		//获取玩家称号
		$Act25Model = Master::getAct25($fuid);
		$chenghao = $Act25Model->outf['setid'];
		
		//玩家称号列表
		$Act73Model = Master::getAct73($fuid);
		$chlist = $Act73Model->get_chlist($fuid);

        //获取头像框
        $Act6151Model = Master::getAct6151($fuid);
        $headavatar = $Act6151Model->info;

        //获取头像框
        $Act6141Model = Master::getAct6141($fuid);
        $userClothe = $Act6141Model->info;
		
		//公会名字
		$clubname = '';
		$Act40Model = Master::getAct40($fuid);
		$cid = $Act40Model->info['cid'];
		if(!empty($cid)){
			$ClubModel = Master::getClub($cid);
			$clubname = $ClubModel->info['name'];
		}

		
		$Act759Model = Master::getAct759($fuid);
		$specialId = $Act759Model->info['sepcial'][$userClothe['body']];

		$fuid_Data = array(
			//玩家个人信息
			'id'=> $fUser_info['uid'],	//玩家UID
			'name'=> $fUser_info['name'],  //名字
			'level'=> $fUser_info['level'],  //官阶
			'sex'=> $fUser_info['sex'],  //性别1男2女
			'job'=> empty($fUser_info['job'])?1:$fUser_info['job'],  //头像编号
			'exp'=> $fUser_info['exp'],  //政绩
			'vip'=> $fUser_info['vip'],  //VIP
            'clothe' => $userClothe,
            'headavatar'=>$headavatar,
			'bmap'=> $fUser_info['bmap'],  //地图大关ID
			'mmap'=> $fUser_info['mmap'],  //地图中关ID
			'smap'=> intval($fUser_info['smap']),  //地图小关ID
			
			//玩家属性信息
			'ep' => array(
				'e1'=> $fTeam_info['allep'][1],  //武力
				'e2'=> $fTeam_info['allep'][2],  //智力
				'e3'=> $fTeam_info['allep'][3],  //政治
				'e4'=> $fTeam_info['allep'][4],  //魅力
			),
			'shili'=> $fTeam_info['shili'],  //总势力
			'love'=> $fTeam_info['alllove'], //总亲密度
			//公会信息
			'clubid'=> $fUser_info['clubid'],
			'clubname'=> $clubname,  //工会名字
			//称号
			'chenghao'=> $chenghao,  //称号
			//宣言
			'xuanyan'=> $fUser_info['xuanyan'],  //宣言
			'chlist'=> $chlist,  //玩家称号列表
			'clotheSpecial' => intval($specialId),
		);

        unset(
            $fUserModel, $fUser_info, $fTeamModel, $fTeam_info,
			$Act25Model, $Act73Model, $Act40Model, $ClubModel,
			$Act759Model
        );
		return $fuid_Data;
	}
	
	/*
	 * 获取婚姻亲家信息  不含子嗣
	 */
	public static function getMarryDate_onlyuser($fuid){
		//玩家信息
		$fUserModel = Master::getUser($fuid);
		
		return array(
			'fuid' => $fuid,
			'fname' => $fUserModel->info['name'],
		);
	}
	
	/*
	 * 玩家ID , 子嗣ID
	 */
	public static function getMarryDate($fuid,$fsonid,$honor = 0){
		//玩家信息
		$fUserModel = Master::getUser($fuid);
		//子嗣信息
		$fSonModel = Master::getSon($fuid);
		$fson_info = $fSonModel->check_info($fsonid);
		
		return array(
			'fuid' => $fuid,
			'fname' => $fUserModel->info['name'],
			'sname' => $fson_info['name'],
			'sonuid' => $fsonid,
            'talent' => $fson_info['talent'],
			'honor' => empty($honor)?$fson_info['honor']:$honor,
			'sex' => $fson_info['sex'],
			'ep' => array(
				'e1' => (int)$fson_info['e1'],
				'e2' => (int)$fson_info['e2'],
				'e3' => (int)$fson_info['e3'],
				'e4' => (int)$fson_info['e4'],
			),
		);
	}
	
	/**
	 * 发送邮件
	 * @param $uid  玩家id
	 * @param $title 标题
	 * @param $content 内容
	 * @param $mtype   0:无道具列表  1:有道具列表 2:其他
	 * @param $daoju  道具列表
	 */
	public static function sendMail($uid,$title,$content,$mtype = 0,$daoju = 0,$link = ''){
		$MailModel = Master::getMail($uid);
		$MailModel->sendMail($uid,$title,$content,$mtype,$daoju,$link);
	}

	//获取商店
	public static function getOrderShopCfg($sid = 1)
    {
        if(defined('OVERSEAS')  && OVERSEAS){
            if(defined('OVERSEAS_DEF') && OVERSEAS_DEF){
                return Game::getcfg_info('order_shop_'.OVERSEAS_DEF,$sid);
            }
        }
        return Game::getcfg_info('order_shop_k',$sid);
	}
	
	//检测克制关系
	public function checkRestraint($mine,$enemy){
		$restraintArr = array("2"=>3,"3"=>4,"4"=>2);
		if($restraintArr[$mine] == $enemy){
			return true;
		}else{
			return false;
		}
	}
	/*
     * 美元转换
     *
     * */
    public static function returnDoller($money){
        $dc = array(
            // '6' => 0.99, '28' => 4.99, '30' => 4.99, '68' => 9.99, '198' => 29.99, '288' => 49.99, '328' => 49.99, '648' => 99.99,
            '5' => 0.99, '6' => 2.99, '18' => 2.99, '28' => 6.99, '30' => 4.99, '35' => 2.99, '36' => 19.99, '68' => 9.99, '198' => 29.99, '288' => 69.99, '328' => 49.99, '648' => 99.99, '25' => 7.99, '26' => 2.99, '27' => 4.99, '29' => 9.99, '31' => 19.99, '32' => 29.99, '33' => 49.99, '34' => 99.99,
        );
        $shopcfg = Master::getOrderShopCfg();
        foreach($shopcfg as $v){
            if($v['rmb'] == $money){
                return $v['dollar'];
            }
        }

        // $newMoney = $money * 100;
        // $newMoney2 = intval($money) * 100;

        // if(empty($dc[intval($money)]) || $newMoney != $newMoney2){
        //     return $money;
        // }else{
        //     return $dc[intval($money)];
        // }
    }

    /*
     * 数数数据上报
     *
     * */
    public static function taTrack($uid, $event_name, $properties, $userInfo = array(), $ip = "", $time = ""){
        if(defined('LOGBUS_OPEN')  && LOGBUS_OPEN){
            $dir = '/data/logs/logBus';
            if(defined('LOGBUS_DIR')  && LOGBUS_DIR){
                $dir = LOGBUS_DIR;
            }
            $ta = new ThinkingDataAnalytics(new FileConsumer($dir));

            $sevid = Game::get_sevid($uid);
            $uids = Common::getSharding($uid);
            $accname = $uids['ustr'];

            if (empty($userInfo)) {

                $UserModel = Master::getUser($uid);
                $userInfo = $UserModel->info;
            }

            if ($ip == "") {
                $ip = Common::GetIP();
            }

            if ($time == "") {
                $time = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
            }

            $properties["createtime"] = $time;
            $properties["#ip"] = $ip;
            $properties["language"] = OVERSEAS_DEF;
            $properties["country"] = OVERSEAS;
            $properties["channel_id"] = SNS;
            $properties["server_id"] = $sevid;
            $properties["role_id"] = $uid;
            $properties["platform"] = $userInfo["platform"];
            $properties["account_regtime"] = date('Y-m-d H:i:s',$userInfo['regtime']);
            $properties["role_name"] = $userInfo["name"];
            $properties["role_vip"] = $userInfo["vip"];
            $properties["role_level"] = $userInfo["level"];
            $properties["diamond"] = $userInfo["cash"];

            $ta->track($accname,$uid,$event_name,$properties);
            $ta->flush();
            $ta->close();
        }
    }
}
