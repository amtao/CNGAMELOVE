<?php
//排行榜
class huodongMod extends Base
{
	public function check($k_ctrl)
	{
		$lock_cfg = Game::getBaseCfg('lock');
		$k_mod = 'huodong';
		if (!empty($lock_cfg[$k_mod][$k_ctrl]['key_arg'])) {
			switch ($k_ctrl){
				case 'hd295sendHb':
				case 'hd295getHb':
					$Act40Model = Master::getAct40($this->uid);
					$cid = $Act40Model->info['cid'];
					if (!empty($cid)) {
						Master::get_lock($lock_cfg[$k_mod][$k_ctrl]['type'], $lock_cfg[$k_mod][$k_ctrl]['key_arg'].'_'.$cid);
					}
					break;
				case 'hd298play'://打年兽
					Master::get_lock($lock_cfg[$k_mod][$k_ctrl]['type'], $lock_cfg[$k_mod][$k_ctrl]['key_arg']);
					break;
				default:
					break;
			}
		}
		return parent::check($k_ctrl);
	}

	/**
	 *所有生效活动列表
	 */
	public function hdList($params){
		$Act200Model = Master::getAct200($this->uid);
		$Act200Model->back_data();
	}

	/**
	 *限时奖励-元宝消耗 - 信息
	 */
	public function hd201Info($params){
		$Act201Model = Master::getAct201($this->uid);
		$Act201Model->back_data_hd();
	}

	/**
	 * 限时奖励-元宝消耗 - 领取奖励
	 * @param unknown_type $params
	 */
	public function hd201Rwd($params){
		$Act201Model = Master::getAct201($this->uid);
		$Act201Model->get_rwd();
		$Act201Model->back_data_hd();
	}

	/**
	 *限时奖励-士兵消耗 - 信息
	 */
	public function hd202Info($params){
		$Act202Model = Master::getAct202($this->uid);
		$Act202Model->back_data_hd();
	}

	/**
	 * 限时奖励-士兵消耗 - 领取奖励
	 * @param unknown_type $params
	 */
	public function hd202Rwd($params){
		$Act202Model = Master::getAct202($this->uid);
		$Act202Model->get_rwd();
		$Act202Model->back_data_hd();
	}

	/**
	 *限时奖励-银两消耗- 信息
	 */
	public function hd203Info($params){
		$Act203Model = Master::getAct203($this->uid);
		$Act203Model->back_data_hd();
	}

	/**
	 * 限时奖励-银两消耗- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd203Rwd($params){
		$Act203Model = Master::getAct203($this->uid);
		$Act203Model->get_rwd();
		$Act203Model->back_data_hd();
	}


	/**
	 *限时奖励-强化卷轴消耗- 信息
	 */
	public function hd204Info($params){
		$Act204Model = Master::getAct204($this->uid);
		$Act204Model->back_data_hd();
	}

	/**
	 * 限时奖励-强化卷轴消耗- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd204Rwd($params){
		$Act204Model = Master::getAct204($this->uid);
		$Act204Model->get_rwd();
		$Act204Model->back_data_hd();
	}

	/**
	 *限时奖励-亲密度涨幅- 信息
	 */
	public function hd205Info($params){
		$Act205Model = Master::getAct205($this->uid);
		$Act205Model->back_data_hd();
	}

	/**
	 * 限时奖励-亲密度涨幅- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd205Rwd($params){
		$Act205Model = Master::getAct205($this->uid);
		$Act205Model->get_rwd();
		$Act205Model->back_data_hd();
	}


	/**
	 *限时奖励-势力涨幅 - 信息
	 */
	public function hd206Info($params){
		$Act206Model = Master::getAct206($this->uid);
		$Act206Model->back_data_hd();
	}

	/**
	 * 限时奖励-势力涨幅- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd206Rwd($params){
		$Act206Model = Master::getAct206($this->uid);
		$Act206Model->get_rwd();
		$Act206Model->back_data_hd();
	}

	/**
	 *限时奖励-处理政务次数 - 信息
	 */
	public function hd207Info($params){
		$Act207Model = Master::getAct207($this->uid);
		$Act207Model->back_data_hd();
	}

	/**
	 * 限时奖励-处理政务次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd207Rwd($params){
		$Act207Model = Master::getAct207($this->uid);
		$Act207Model->get_rwd();
		$Act207Model->back_data_hd();
	}

	/**
	 *限时奖励-累计登录天数 - 信息
	 */
	public function hd208Info($params){
		$Act208Model = Master::getAct208($this->uid);
        $Act208Model->do_check();
		$Act208Model->back_data_hd();
	}

	/**
	 * 限时奖励-累计登录天数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd208Rwd($params){
		$Act208Model = Master::getAct208($this->uid);
		$Act208Model->get_rwd();
		$Act208Model->back_data_hd();
	}

	/**
	 *限时奖励-衙门分数涨幅 - 信息
	 */
	public function hd209Info($params){
		$Act209Model = Master::getAct209($this->uid);
		$Act209Model->back_data_hd();
	}

	/**
	 * 限时奖励-衙门分数涨幅- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd209Rwd($params){
		$Act209Model = Master::getAct209($this->uid);
		$Act209Model->get_rwd();
		$Act209Model->back_data_hd();
	}


	/**
	 *限时奖励-联姻次数 - 信息
	 */
	public function hd210Info($params){
		$Act210Model = Master::getAct210($this->uid);
		$Act210Model->back_data_hd();
	}

	/**
	 * 限时奖励-联姻次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd210Rwd($params){
		$Act210Model = Master::getAct210($this->uid);
		$Act210Model->get_rwd();
		$Act210Model->back_data_hd();
	}

	/**
	 *限时奖励-书院学习- 信息
	 */
	public function hd211Info($params){
		$Act211Model = Master::getAct211($this->uid);
		$Act211Model->back_data_hd();
	}

	/**
	 * 限时奖励-书院学习- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd211Rwd($params){
		$Act211Model = Master::getAct211($this->uid);
		$Act211Model->get_rwd();
		$Act211Model->back_data_hd();
	}


	/**
	 *限时奖励-经营商产次数- 信息
	 */
	public function hd212Info($params){
		$Act212Model = Master::getAct212($this->uid);
		$Act212Model->back_data_hd();
	}

	/**
	 * 限时奖励-经营商产次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd212Rwd($params){
		$Act212Model = Master::getAct212($this->uid);
		$Act212Model->get_rwd();
		$Act212Model->back_data_hd();
	}

	/**
	 *限时奖励-经营农产次数- 信息
	 */
	public function hd213Info($params){
		$Act213Model = Master::getAct213($this->uid);
		$Act213Model->back_data_hd();
	}

	/**
	 * 限时奖励-经营农产次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd213Rwd($params){
		$Act213Model = Master::getAct213($this->uid);
		$Act213Model->get_rwd();
		$Act213Model->back_data_hd();
	}


	/**
	 *限时奖励-招募士兵次数- 信息
	 */
	public function hd214Info($params){
		$Act214Model = Master::getAct214($this->uid);
		$Act214Model->back_data_hd();
	}

	/**
	 * 限时奖励-招募士兵次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd214Rwd($params){
		$Act214Model = Master::getAct214($this->uid);
		$Act214Model->get_rwd();
		$Act214Model->back_data_hd();
	}


	/**
	 *限时奖励-击杀葛尔丹次数- 信息
	 */
	public function hd215Info($params){
		$Act215Model = Master::getAct215($this->uid);
		$Act215Model->back_data_hd();
	}

	/**
	 * 限时奖励-击杀葛尔丹次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd215Rwd($params){
		$Act215Model = Master::getAct215($this->uid);
		$Act215Model->get_rwd();
		$Act215Model->back_data_hd();
	}


	/**
	 *限时奖励-挑战书消耗- 信息
	 */
	public function hd216Info($params){
		$Act216Model = Master::getAct216($this->uid);
		$Act216Model->back_data_hd();
	}

	/**
	 * 限时奖励-挑战书消耗- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd216Rwd($params){
		$Act216Model = Master::getAct216($this->uid);
		$Act216Model->get_rwd();
		$Act216Model->back_data_hd();
	}

	/**
	 *限时奖励-惩戒犯人次数- 信息
	 */
	public function hd217Info($params){
		$Act217Model = Master::getAct217($this->uid);
		$Act217Model->back_data_hd();
	}

	/**
	 * 限时奖励-惩戒犯人次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd217Rwd($params){
		$Act217Model = Master::getAct217($this->uid);
		$Act217Model->get_rwd();
		$Act217Model->back_data_hd();
	}


	/**
	 *限时奖励-赈灾次数- 信息
	 */
	public function hd218Info($params){
		$Act218Model = Master::getAct218($this->uid);
		$Act218Model->back_data_hd();
	}

	/**
	 * 限时奖励-赈灾次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd218Rwd($params){
		$Act218Model = Master::getAct218($this->uid);
		$Act218Model->get_rwd();
		$Act218Model->back_data_hd();
	}


	/**
	 *限时奖励-体力丹消耗- 信息
	 */
	public function hd219Info($params){
		$Act219Model = Master::getAct219($this->uid);
		$Act219Model->back_data_hd();
	}

	/**
	 * 限时奖励-体力丹消耗- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd219Rwd($params){
		$Act219Model = Master::getAct219($this->uid);
		$Act219Model->get_rwd();
		$Act219Model->back_data_hd();
	}


	/**
	 *限时奖励-活力丹消耗- 信息
	 */
	public function hd220Info($params){
		$Act220Model = Master::getAct220($this->uid);
		$Act220Model->back_data_hd();
	}

	/**
	 * 限时奖励-活力丹消耗- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd220Rwd($params){
		$Act220Model = Master::getAct220($this->uid);
		$Act220Model->get_rwd();
		$Act220Model->back_data_hd();
	}


	/**
	 *限时奖励-魅力值涨幅- 信息
	 */
	public function hd221Info($params){
		$Act221Model = Master::getAct221($this->uid);
		$Act221Model->back_data_hd();
	}

	/**
	 * 限时奖励-魅力值涨幅- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd221Rwd($params){
		$Act221Model = Master::getAct221($this->uid);
		$Act221Model->get_rwd();
		$Act221Model->back_data_hd();
	}


	/**
	 *限时奖励-赴宴次数- 信息
	 */
	public function hd222Info($params){
		$Act222Model = Master::getAct222($this->uid);
		$Act222Model->back_data_hd();
	}

	/**
	 * 限时奖励-赴宴次数- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd222Rwd($params){
		$Act222Model = Master::getAct222($this->uid);
		$Act222Model->get_rwd();
		$Act222Model->back_data_hd();
	}

	/**
	 *限时奖励-联盟副本伤害- 信息
	 */
	public function hd223Info($params){
		$Act223Model = Master::getAct223($this->uid);
		$Act223Model->back_data_hd();
	}

	/**
	 * 限时奖励-联盟副本伤害- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd223Rwd($params){
		$Act223Model = Master::getAct223($this->uid);
		$Act223Model->get_rwd();
		$Act223Model->back_data_hd();
	}

	/**
	 *限时奖励-联盟副本击杀（累计击杀僵尸）- 信息
	 */
	public function hd224Info($params){
		$Act224Model = Master::getAct224($this->uid);
		$Act224Model->back_data_hd();
	}

	/**
	 * 限时奖励-联盟副本击杀（累计击杀僵尸）- 领取奖励
	 * @param unknown_type $params
	 */
	public function hd224Rwd($params){
		$Act224Model = Master::getAct224($this->uid);
		$Act224Model->get_rwd();
		$Act224Model->back_data_hd();
	}

	/**
	 *限时奖励-酒楼积分涨幅 -信息
	 */
	public function hd225Info($params){
		$Act225Model = Master::getAct225($this->uid);
		$Act225Model->back_data_hd();
	}

    /**
     *限时奖励-粮食消耗- 信息
     */
    public function hd226Info($params){
        $Act226Model = Master::getAct226($this->uid);
        $Act226Model->back_data_hd();
    }

    /**
     * 限时奖励-粮食消耗- 领取奖励
     * @param unknown_type $params
     */
    public function hd226Rwd($params){
        $Act226Model = Master::getAct226($this->uid);
        $Act226Model->get_rwd();
        $Act226Model->back_data_hd();
    }

	/**
	 * 限时奖励-酒楼积分涨幅 - 领取奖励
	 * @param unknown_type $params
	 */
	public function hd225Rwd($params){
		$Act225Model = Master::getAct225($this->uid);
		$Act225Model->get_rwd();
		$Act225Model->back_data_hd();
	}

    /**
     *限时奖励-珍宝阁累计整理关卡次数- 信息
     */
    public function hd6170Info($params){
        $Act6170Model = Master::getAct6170($this->uid);
        $Act6170Model->back_data_hd();
    }

    /**
     * 限时奖励-珍宝阁累计整理关卡次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6170Rwd($params){
        $Act6170Model = Master::getAct6170($this->uid);
        $Act6170Model->get_rwd();
        $Act6170Model->back_data_hd();
    }

    /**
     *限时奖励-祈福次数- 信息
     */
    public function hd6171Info($params){
        $Act6171Model = Master::getAct6171($this->uid);
        $Act6171Model->back_data_hd();
    }

    /**
     * 限时奖励-祈福次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6171Rwd($params){
        $Act6171Model = Master::getAct6171($this->uid);
        $Act6171Model->get_rwd();
        $Act6171Model->back_data_hd();
    }

    /**
     *限时奖励-精力丹消耗- 信息
     */
    public function hd6172Info($params){
        $Act6172Model = Master::getAct6172($this->uid);
        $Act6172Model->back_data_hd();
    }

    /**
     * 限时奖励-精力丹消耗- 领取奖励
     * @param unknown_type $params
     */
    public function hd6172Rwd($params){
        $Act6172Model = Master::getAct6172($this->uid);
        $Act6172Model->get_rwd();
        $Act6172Model->back_data_hd();
    }

    /**
     *限时奖励-知己出游次数- 信息
     */
    public function hd6173Info($params){
        $Act6173Model = Master::getAct6173($this->uid);
        $Act6173Model->back_data_hd();
    }

    /**
     * 限时奖励-知己出游次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6173Rwd($params){
        $Act6173Model = Master::getAct6173($this->uid);
        $Act6173Model->get_rwd();
        $Act6173Model->back_data_hd();
    }

    /**
     *限时奖励-问候知己次数- 信息
     */
    public function hd6174Info($params){
        $Act6174Model = Master::getAct6174($this->uid);
        $Act6174Model->back_data_hd();
    }

    /**
     * 限时奖励-问候知己次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6174Rwd($params){
        $Act6174Model = Master::getAct6174($this->uid);
        $Act6174Model->get_rwd();
        $Act6174Model->back_data_hd();
    }

    /**
     *限时奖励-郊祀献礼次数- 信息
     */
    public function hd6175Info($params){
        $Act6175Model = Master::getAct6175($this->uid);
        $Act6175Model->back_data_hd();
    }

    /**
     * 限时奖励-郊祀献礼次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6175Rwd($params){
        $Act6175Model = Master::getAct6175($this->uid);
        $Act6175Model->get_rwd();
        $Act6175Model->back_data_hd();
    }

    /**
     *限时奖励-皇子应援次数- 信息
     */
    public function hd6176Info($params){
        $Act6176Model = Master::getAct6176($this->uid);
        $Act6176Model->back_data_hd();
    }

    /**
     * 限时奖励-皇子应援次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6176Rwd($params){
        $Act6176Model = Master::getAct6176($this->uid);
        $Act6176Model->get_rwd();
        $Act6176Model->back_data_hd();
    }

    /**
     *限时奖励-出城寻访次数- 信息
     */
    public function hd6177Info($params){
        $Act6177Model = Master::getAct6177($this->uid);
        $Act6177Model->back_data_hd();
    }

    /**
     * 限时奖励-出城寻访次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6177Rwd($params){
        $Act6177Model = Master::getAct6177($this->uid);
        $Act6177Model->get_rwd();
        $Act6177Model->back_data_hd();
    }

    /**
     *限时奖励-徒弟历练次数- 信息
     */
    public function hd6178Info($params){
        $Act6178Model = Master::getAct6178($this->uid);
        $Act6178Model->back_data_hd();
    }

    /**
     * 限时奖励-徒弟历练次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6178Rwd($params){
        $Act6178Model = Master::getAct6178($this->uid);
        $Act6178Model->get_rwd();
        $Act6178Model->back_data_hd();
    }

    /**
     *限时奖励-御膳房烹饪次数- 信息
     */
    public function hd6179Info($params){
        $Act6179Model = Master::getAct6179($this->uid);
        $Act6179Model->back_data_hd();
    }

    /**
     * 限时奖励-御膳房烹饪次数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6179Rwd($params){
        $Act6179Model = Master::getAct6179($this->uid);
        $Act6179Model->get_rwd();
        $Act6179Model->back_data_hd();
    }

    /**
     * 直冲礼包- 信息
     */
    public function hd6180Info($params){
        $Act6180Model = Master::getAct6180($this->uid);
        $Act6180Model->info['isClick'] = 1;
        $Act6180Model->_save();
        $Act6180Model->back_data();
    }

    /**
     * 直冲礼包- 购买
     */
    public function hd6180buy($params){
        $id = Game::intval($params,'id');
        $Act6180Model = Master::getAct6180($this->uid);
        $Act6180Model -> setTempBuy($id);
    }

    /**
     *皇子累充解锁- 信息
     */
    public function hd6181Info($params){
        $Act6181Model = Master::getAct6181($this->uid);
        $Act6181Model->back_data_hd();
    }

    /**
     * 皇子累充解锁- 领取奖励
     * @param unknown_type $params
     */
    public function hd6181Rwd($params){
        $id = Game::intval($params,'id');
        $Act6181Model = Master::getAct6181($this->uid);
        $Act6181Model->get_rwd($id);
        $Act6181Model->back_data_hd();
    }

    /**
     *身份大礼- 信息
     */
    public function hd6182Info($params){
        $Act6182Model = Master::getAct6182($this->uid);
        $Act6182Model->back_data_hd();
    }

    /**
     * 身份大礼- 领取奖励
     * @param unknown_type $params
     */
    public function hd6182Rwd($params){
        $lv = Game::intval($params,'lv');
        $Act6182Model = Master::getAct6182($this->uid);
        $Act6182Model->get_rwd($lv);
        $Act6182Model->back_data_hd();
    }

    /**
     * 身份大礼- 付费领取奖励
     * @param unknown_type $params
     */
    public function hd6182RwdCharge($params){
        $lv = Game::intval($params,'lv');
        $Act6182Model = Master::getAct6182($this->uid);
        $Act6182Model->get_rwd_charge($lv);
        $Act6182Model->back_data_hd();
    }

    /**
     *堆雪人- 信息
     */
    public function hd6183Info($params){
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->back_data_hd();
        $Act6183Model->back_data();
        Master::back_data($this->uid,$Act6183Model->b_mol,'shop',$Act6183Model->back_data_shop());
        Master::back_data($this->uid,$Act6183Model->b_mol,'exchange',$Act6183Model->back_data_exchange());
    }
    /**
     *堆雪人
     */
    public function hd6183Paly($params){
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->play();
        $Act6183Model->back_data_hd();
    }
    /**
     *堆雪人十次
     */
    public function hd6183PalyTen($params){
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->play(10);
        $Act6183Model->back_data_hd();
    }

    /**
     * 堆雪人- 领取奖励
     * @param unknown_type $params
     */
    public function hd6183Rwd($params){
        $lv = Game::intval($params,'lv');
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->get_rwd($lv);
        $Act6183Model->back_data_hd();
    }

    /***
     * 堆雪人.排行
     */
    public function hd6183paihang() {
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->paihang();
    }

    /**
     * 堆雪人 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6183buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->shop_buy($id,$num);
        $Act6183Model->back_data();
    }

    /**
     * 堆雪人 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6183exchange($params){
        $id = Game::intval($params,'id');
        $Act6183Model = Master::getAct6183($this->uid);
        $Act6183Model->exchange($id);
    }

    /**
     *限时奖励-累计登录天数 - 信息
     */
    public function hd6186Info($params){
        $Act6186Model = Master::getAct6186($this->uid);
        $Act6186Model->do_check();
        $Act6186Model->back_data_hd();
    }

    /**
     * 限时奖励-累计登录天数- 领取奖励
     * @param unknown_type $params
     */
    public function hd6186Rwd($params){
        $Act6186Model = Master::getAct6186($this->uid);
        $Act6186Model->get_rwd();
        $Act6186Model->back_data_hd();
    }

    /**
     *限时奖励-御花园偷取晨露 - 信息
     */
    public function hd6212Info(){
        $Act6212Model = Master::getAct6212($this->uid);
        $Act6212Model->back_data_hd();
    }

    /**
     * 限时奖励-御花园偷取晨露- 领取奖励
     * @param unknown_type $params
     */
    public function hd6212Rwd(){
        $Act6212Model = Master::getAct6212($this->uid);
        $Act6212Model->get_rwd();
        $Act6212Model->back_data_hd();
    }

    /**
     *限时奖励-御花园种植 - 信息
     */
    public function hd6213Info(){
        $Act6213Model = Master::getAct6213($this->uid);
        $Act6213Model->back_data_hd();
    }

    /**
     * 限时奖励-御花园种植- 领取奖励
     * @param unknown_type $params
     */
    public function hd6213Rwd(){
        $Act6213Model = Master::getAct6213($this->uid);
        $Act6213Model->get_rwd();
        $Act6213Model->back_data_hd();
    }

    public function hd6010Info($params){
        $Act6010Model = Master::getAct6010($this->uid);
        $Act6010Model->back_data_hd();

        $Act6011Model = Master::getAct6011($this->uid);
        $Act6011Model->back_data();
    }

    public function hd6010Rank($params){
        $Act6010Model = Master::getAct6010($this->uid);
        $this->hd_cfg['info']['id'].'_'.Game::get_today_long_id();
        $Redis6010Model = Master::getRedis6010($Act6010Model->hd_cfg['info']['id'].'_'.Game::get_today_long_id());
        $Redis6010Model->back_data();
        $Redis6010Model->back_data_my($this->uid);
    }

    public function hd6010Fight($params){
        $hid= Game::intval($params,'id');
        $type = Game::intval($params, 'type');
        $Act6010Model = Master::getAct6010($this->uid);
        $Act6010Model->hit($hid, $type);
    }

    public function hd6010Add($params){
        //需要复活的门客ID
        $hero_id = Game::intval($params,'id');
        $Act6010Model = Master::getAct6010($this->uid);
        $Act6010Model -> comeback($hero_id);
    }

	/**
	 *冲榜活动250信息--联盟冲榜
	 */
	public function hd250Info($params){
		$Act250Model = Master::getAct250($this->uid);
		$Act250Model->back_data_hd();
	}

	/**
	 *冲榜活动251信息--关卡冲榜
	 */
	public function hd251Info($params){
		$Act251Model = Master::getAct251($this->uid);
        $Act251Model->back_data_hd();
        Master::back_data($this->uid,$Act251Model->b_mol,$Act251Model->b_ctrl.'exchange',$Act251Model->back_data_exchange());
    }
    
     /**
	 *冲榜活动251信息--关卡冲榜礼包购买
	 */
	public function hd251Buy($params){
        $id = Game::intval($params,'id');
		$Act251Model = Master::getAct251($this->uid);
        $Act251Model->exchange($id);
	}

	/**
	 *冲榜活动252信息--势力冲榜
	 */
	public function hd252Info($params){
		$Act252Model = Master::getAct252($this->uid);
        $Act252Model->back_data_hd();
        Master::back_data($this->uid,$Act252Model->b_mol,$Act252Model->b_ctrl.'exchange',$Act252Model->back_data_exchange());
    }
    
    /**
	 *冲榜活动252信息--势力冲榜礼包购买
	 */
	public function hd252Buy($params){
        $id = Game::intval($params,'id');
		$Act252Model = Master::getAct252($this->uid);
        $Act252Model->exchange($id);
	}

	/**
	 *冲榜活动253信息--亲密冲榜
	 */
	public function hd253Info($params){
		$Act253Model = Master::getAct253($this->uid);
		$Act253Model->back_data_hd();
	}

	/**
	 *冲榜活动254信息--衙门冲榜
	 */
	public function hd254Info($params){
		$Act254Model = Master::getAct254($this->uid);
		$Act254Model->back_data_hd();
	}

	/**
	 *冲榜活动255信息--银两冲榜
	 */
	public function hd255Info($params){
		$Act255Model = Master::getAct255($this->uid);
		$Act255Model->back_data_hd();
	}

	/**
	 *冲榜活动256信息--酒楼冲榜
	 */
	public function hd256Info($params){
		$Act256Model = Master::getAct256($this->uid);
		$Act256Model->back_data_hd();
	}

	/**
	 *冲榜活动257信息--士兵冲榜
	 */
	public function hd257Info($params){
		$Act257Model = Master::getAct257($this->uid);
		$Act257Model->back_data_hd();
	}

    /**
     *冲榜活动258信息--魅力冲榜
     */
    public function hd258Info($params){
        $Act258Model = Master::getAct258($this->uid);
        $Act258Model->back_data_hd();
    }

    /**
     *冲榜活动259信息--粮食冲榜
     */
    public function hd259Info($params){
        $Act259Model = Master::getAct259($this->uid);
        $Act259Model->back_data_hd();
    }

    /**
     *冲榜活动6135信息--珍宝阁积分冲榜
     */
    public function hd6135Info($params){
        $Act6135Model = Master::getAct6135($this->uid);
        $Act6135Model->back_data_hd();
    }

    /**
     *冲榜活动6166信息--伙伴羁绊冲榜
     */
    public function hd6166Info($params){
        $Act6166Model = Master::getAct6166($this->uid);
        $Act6166Model->back_data_hd();
    }

    /**
     *冲榜活动6167信息--伙伴资质冲榜
     */
    public function hd6167Info($params){
        $Act6167Model = Master::getAct6167($this->uid);
        $Act6167Model->back_data_hd();
    }

    /**
     *冲榜活动6215信息--偷取晨露次数冲榜
     */
    public function hd6215Info($params){
        $Act6215Model = Master::getAct6215($this->uid);
        $Act6215Model->back_data_hd();
    }

    /**
     *冲榜活动6216信息--御花园种植次数冲榜
     */
    public function hd6216Info($params){
        $Act6216Model = Master::getAct6216($this->uid);
        $Act6216Model->back_data_hd();
    }

    /**
     *冲榜活动6217信息--知己技能经验涨幅冲榜
     */
    public function hd6217Info($params){
        $Act6217Model = Master::getAct6217($this->uid);
        $Act6217Model->back_data_hd();
    }

    /**
     *冲榜活动6218信息--徒弟势力冲榜
     */
    public function hd6218Info($params){
        $Act6218Model = Master::getAct6218($this->uid);
        $Act6218Model->back_data_hd();
        Master::back_data($this->uid,$Act6218Model->b_mol,$Act6218Model->b_ctrl.'exchange',$Act6218Model->back_data_exchange());
    }

    /**
	 *冲榜活动6218信息--势力冲榜礼包购买
	 */
	public function hd6218Buy($params){
        $id = Game::intval($params,'id');
		$Act6218Model = Master::getAct6218($this->uid);
        $Act6218Model->exchange($id);
	}

	/**
	 *充值活动  -- 每日充值信息
	 */
	public function hd260Info($params){
		$Act260Model = Master::getAct260($this->uid);
		$Act260Model->back_data_hd();
	}

	/**
	 * 充值活动  -- 每日充值领取奖励
	 */
	public function hd260Rwd($params){
		$Act260Model = Master::getAct260($this->uid);
		$Act260Model->get_rwd();
		$Act260Model->back_data_hd();
	}


	/**
	 *充值活动  -- 累计充值信息
	 */
	public function hd261Info($params){
		$Act261Model = Master::getAct261($this->uid);
		$Act261Model->back_data_hd();
	}

	/**
	 * 充值活动  -- 累计充值领取奖励
	 */
	public function hd261Rwd($params){
		$Act261Model = Master::getAct261($this->uid);
		$Act261Model->get_rwd();
		$Act261Model->back_data_hd();
	}

	/**
	 *充值活动  -- 累天充值信息
	 */
	public function hd262Info($params){
		$Act262Model = Master::getAct262($this->uid);
		$Act262Model->back_data_hd();
	}

	/**
	 * 充值活动  -- 累计充值领取奖励
	 */
	public function hd262Rwd($params){
		$Act262Model = Master::getAct262($this->uid);
		$Act262Model->get_rwd();
		$Act262Model->back_data_hd();
	}

    /**
     *充值活动  -- 单笔充值信息
     */
    public function hd6139Info($params){
        $Act6139Model = Master::getAct6139($this->uid);
        $Act6139Model->back_data_hd();
    }

    /**
     * 充值活动  -- 单笔充值领取奖励
     */
    public function hd6139Rwd($params){
        $Act6139Model = Master::getAct6139($this->uid);
        $Act6139Model->get_rwd();
        $Act6139Model->back_data_hd();
    }

    /**
     *充值活动  -- 天天充值信息
     */
    public function hd6168Info($params){
        $Act6168Model = Master::getAct6168($this->uid);
        $Act6168Model->back_data_hd();
        //活动信息
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6168');
        $Sev6168Model = Master::getSev6168($hd_cfg['info']['id']);
        $Sev6168Model->back_data();
    }

    /**
     * 充值活动  -- 天天充值领取奖励
     */
    public function hd6168Rwd($params){

        $Act6168Model = Master::getAct6168($this->uid);
        $Act6168Model->get_rwd();
        $Act6168Model->back_data_hd();
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6168');
        $Sev6168Model = Master::getSev6168($hd_cfg['info']['id']);
        $Sev6168Model->back_data();
    }

    /**
     * 充值活动  -- 连续天天充值领取奖励
     */
    public function hd6168TotalRwd($params){

        $Act6168Model = Master::getAct6168($this->uid);
        $Act6168Model->get_totalrwd();
        $Act6168Model->back_data_hd();
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6168');
        $Sev6168Model = Master::getSev6168($hd_cfg['info']['id']);
        $Sev6168Model->back_data();
    }


    /**
     *充值活动  -- 连续充值信息
     */
    public function hd6184Info($params){
        $Act6184Model = Master::getAct6184($this->uid);
        $Act6184Model->back_data_hd();
    }

    /**
     * 充值活动  -- 连续充值每日领取奖励
     */
    public function hd6184Rwd($params){
        $id = Game::intval($params,'id');
        $Act6184Model = Master::getAct6184($this->uid);
        $Act6184Model->get_rwd($id);
        $Act6184Model->back_data_hd();
    }

    /**
     * 充值活动  -- 连续充值累计领取奖励
     */
    public function hd6184TotalRwd($params){
        $id = Game::intval($params,'id');
        $Act6184Model = Master::getAct6184($this->uid);
        $Act6184Model->get_totalrwd($id);
        $Act6184Model->back_data_hd();
    }

	/**
	 *四大奸臣 --- 信息
	 */
	public function hd270Info($params){
		$Act270Model = Master::getAct270($this->uid);
		$Act270Model->back_data_hd();
	}

	/**
	 * 四大奸臣   --- 领取奖励
	 * $params['id'] :  门客id
	 */
	public function hd270Rwd($params){
		$id = Game::intval($params,'id');
		$Act270Model = Master::getAct270($this->uid);
		$Act270Model->get_rwd($id);
		$Act270Model->back_data_hd();
		$Act200Model = Master::getAct200($this->uid);
		$Act200Model->back_data();
	}


	/**
	 *巾帼五虎 --- 信息
	 */
	public function hd271Info($params){
		$Act271Model = Master::getAct271($this->uid);
		$Act271Model->back_data_hd();
	}

	/**
	 * 巾帼五虎   --- 领取奖励
	 * $params['id'] :  门客id
	 */
	public function hd271Rwd($params){
		$id = Game::intval($params,'id');
		$Act271Model = Master::getAct271($this->uid);
		$Act271Model->get_rwd($id);
		$Act271Model->back_data_hd();
		$Act200Model = Master::getAct200($this->uid);
		$Act200Model->back_data();
	}

	/**
	 *巾帼女将 --- 信息
	 */
	public function hd272Info($params){
		$Act272Model = Master::getAct272($this->uid);
		$Act272Model->back_data_hd();
	}

	/**
	 * 巾帼女将   --- 领取奖励
	 * $params['id'] :  门客id
	 */
	public function hd272Rwd($params){
		$id = Game::intval($params,'id');
		$Act272Model = Master::getAct272($this->uid);
		$Act272Model->get_rwd($id);
		$Act272Model->back_data_hd();
		$Act200Model = Master::getAct200($this->uid);
		$Act200Model->back_data();
	}

	/**
	 * 新官上任
	 */
	public function hd280Info($params){
	    $Act280Model = Master::getAct280($this->uid);
	    $Act280Model->back_data_hd();
	}

	/**
	 * 新官上任 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd280buy($params){
	    $id = Game::intval($params,'id');
	    $Act280Model = Master::getAct280($this->uid);
	    $Act280Model->buyone($id);
// 	    $Act280Model->back_data_hd();
	}

	/**
	 * 新官上任 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd280exchange($params){
	    $id = Game::intval($params,'id');
	    $Act280Model = Master::getAct280($this->uid);
	    $Act280Model->exchange($id);
// 	    $Act280Model->back_data_hd();
	}

	/**
	 * 新官上任 - 打
	 * $params['id'] :  道具id
	 */
	public function hd280play($params){
	    $id = Game::intval($params,'id');
	    $Act280Model = Master::getAct280($this->uid);
	    $Act280Model->play($id);
// 	    $Act280Model->back_data_hd();
	}
	/*
	 * 新官上任 - 排行 奖励
	 * */
	public function hd280paihang() {
	    $Act280Model = Master::getAct280($this->uid);
	    $Act280Model->paihang();
	}

	/*
	 * 新官上任 - 领取击杀boss奖励
	 * */
	public function hd280Rwd(){
	    $Act280Model = Master::getAct280($this->uid);
	    $Act280Model->KillRwd();
	}


	/**
	 * 惩戒来福
	 */
	public function hd282Info($params){
	    $Act282Model = Master::getAct282($this->uid);
	    $Act282Model->back_data_hd();
	}

	/**
	 * 惩戒来福 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd282buy($params){
	    $id = Game::intval($params,'id');
	    $Act282Model = Master::getAct282($this->uid);
	    $Act282Model->buyone($id);
	}

	/**
	 * 惩戒来福 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd282exchange($params){
	    $id = Game::intval($params,'id');
	    $Act282Model = Master::getAct282($this->uid);
	    $Act282Model->exchange($id);
	}

	/**
	 * 惩戒来福 - 打
	 * $params['id'] :  道具id
	 */
	public function hd282play($params){
	    $id = Game::intval($params,'id');
	    $Act282Model = Master::getAct282($this->uid);
	    $Act282Model->play($id);
	}
	/*
	 * 惩戒来福 - 排行 奖励
	 * */
	public function hd282paihang() {
	    $Act282Model = Master::getAct282($this->uid);
	    $Act282Model->paihang();
	}

	/*
	 * 惩戒来福 - 领取击杀boss奖励
	 * */
	public function hd282Rwd(){
	    $Act282Model = Master::getAct282($this->uid);
	    $Act282Model->KillRwd();
	}


	/**
	 * 国庆活动
	 */
	public function hd283Info($params){
	    $Act283Model = Master::getAct283($this->uid);
	    $Act283Model->back_data_hd();
	}

	/**
	 * 国庆活动 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd283buy($params){
	    $id = Game::intval($params,'id');
	    $Act283Model = Master::getAct283($this->uid);
	    $Act283Model->buyone($id);
	}

	/**
	 * 国庆活动 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd283exchange($params){
	    $id = Game::intval($params,'id');
	    $Act283Model = Master::getAct283($this->uid);
	    $Act283Model->exchange($id);
	}

	/**
	 * 国庆活动 - 打
	 * $params['id'] :  道具id
	 */
	public function hd283play($params){
	    $id = Game::intval($params,'id');
	    $Act283Model = Master::getAct283($this->uid);
	    $Act283Model->play($id);
	}
	/*
	 * 国庆活动 - 排行 奖励
	 * */
	public function hd283paihang() {
	    $Act283Model = Master::getAct283($this->uid);
	    $Act283Model->paihang();
	}

	/*
	 * 国庆活动 - 领取击杀boss奖励
	 * */
	public function hd283Rwd(){
	    $Act283Model = Master::getAct283($this->uid);
	    $Act283Model->KillRwd();
	}

	//------------------- 重阳节活动 -----------------------
	/**
	 * 重阳节活动
	 */
	public function hd281Info($params){
	    $Act281Model = Master::getAct281($this->uid);
	    $Act281Model->back_data_hd();
	}

	/**
	 * 重阳节活动 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd281buy($params){
	    $id = Game::intval($params,'id');
	    $Act281Model = Master::getAct281($this->uid);
	    $Act281Model->buyone($id);
	}

	/**
	 * 重阳节活动 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd281exchange($params){
	    $id = Game::intval($params,'id');
	    $Act281Model = Master::getAct281($this->uid);
	    $Act281Model->exchange($id);
	}

	/**
	 * 重阳节活动 - 打
	 * $params['id'] :  道具id
	 */
	public function hd281play($params){
	    $id = Game::intval($params,'id');
	    $Act281Model = Master::getAct281($this->uid);
	    $Act281Model->play($id);
	}
	/*
	 * 重阳节活动 - 排行 奖励
	 * */
	public function hd281paihang() {
	    $Act281Model = Master::getAct281($this->uid);
	    $Act281Model->paihang();
	}

	/*
	 * 重阳节活动- 领取击杀boss奖励
	 * */
	public function hd281Rwd(){
	    $Act281Model = Master::getAct281($this->uid);
	    $Act281Model->KillRwd();
	}

	/*
	 * 重阳节活动- 累计充值领取
	 * */
	public function hd281getRwd($params){
	    $id = Game::intval($params, 'id');
	    $Act122Model = Master::getAct122($this->uid);
	    $Act122Model->getRwd($id);
	}

	//--------------------双十一活动--------------
    /*
     * 双十一活动-配置信息
     */
    public function hd285Info(){
        $Act285Model = Master::getAct285($this->uid);
        $Act285Model->back_data_hd();
    }
    /*
     * 双十一活动-商城单品购买
     */
	public function hd285buy($params){
        $id = Game::intval($params,'id');
        $Act83Model = Master::getAct83($this->uid);
        $Act83Model->shopLimit($id);
        $Act83Model->back_data();
    }
    /*
     * 双十一活动-商城礼包购买
     */
    public function hd285buyGift($params){
        $id = Game::intval($params,'id');
        $Act84Model = Master::getAct84($this->uid);
        $Act84Model->shopGift($id);
        $Act84Model->back_data_hd();
    }
    //领取充值奖励
    public function hd285getRwd($params){
        $id = Game::intval($params,'id');
        $Act85Model = Master::getAct85($this->uid);
        $Act85Model->getRwd($id);
        $Act285Model = Master::getAct285($this->uid);
        $Act285Model->back_data_hd();
    }


	//------------------- 感恩节活动 -----------------------
	/**
	 * 感恩节活动
	 */
	public function hd284Info(){
		$Act284Model = Master::getAct284($this->uid);
		$Act284Model->back_data_hd();
	}

	/**
	 * 感恩节活动 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd284buy($params){
		$id = Game::intval($params,'id');
		$Act284Model = Master::getAct284($this->uid);
		$Act284Model->buyone($id);
	}

	/**
	 * 感恩节活动 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd284exchange($params){
		$id = Game::intval($params,'id');
		$Act284Model = Master::getAct284($this->uid);
		$Act284Model->exchange($id);
	}

	/**
	 * 感恩节活动 - 打
	 * $params['id'] :  道具id
	 */
	public function hd284play($params){
		$id = Game::intval($params,'id');
		$Act284Model = Master::getAct284($this->uid);
		$Act284Model->play($id);
	}
	/*
	 * 感恩节活动 - 排行 奖励
	 * */
	public function hd284paihang() {
		$Act284Model = Master::getAct284($this->uid);
		$Act284Model->paihang();
	}

	/*
	 * 感恩节活动- 领取击杀boss奖励
	 * */
	public function hd284Rwd(){
		$Act284Model = Master::getAct284($this->uid);
		$Act284Model->KillRwd();
	}

	/*
	 * 感恩节活动- 累计充值领取
	 * */
	public function hd284getRwd($params){
		$id = Game::intval($params, 'id');
		$Act127Model = Master::getAct127($this->uid);
		$Act127Model->getRwd($id);
	}

    //------------------- 兑换活动 -----------------------
    /**
     * 兑换活动
     */
    public function hd6152Info(){
        $Act6152Model = Master::getAct6152($this->uid);
        $Act6152Model->back_data_hd();
    }

    public function hd6152Rwd($params){
        $id = Game::intval($params,'id');
        $Act6152Model = Master::getAct6152($this->uid);
        $Act6152Model->get_rwd($id);
    }

    //------------------- 天天秒杀 -----------------------
    /**
     * 兑换活动
     */
    public function hd6121Info(){
        $Act6121Model = Master::getAct6121($this->uid);
        $Act6121Model->back_data_hd();
    }

    public function hd6121Rwd($params){
        $id = Game::intval($params,'id');
        $Act6121Model = Master::getAct6121($this->uid);
        $Act6121Model->get_rwd($id);
    }

    //------------------- 天天秒杀 -----------------------
    /**
     * 兑换活动
     */
    public function hd6122Info(){
        $Act6122Model = Master::getAct6122($this->uid);
        $Act6122Model->back_data_hd();
    }

    public function hd6122Rwd($params){
        $id = Game::intval($params,'id');
        $Act6122Model = Master::getAct6122($this->uid);
        $Act6122Model->get_rwd($id);
    }

    /**
     * 抢汤圆
     */
    public function hd6015Info(){
        $Act6015Model = Master::getAct6015($this->uid);
        $Act6015Model->back_data_hd(true);
        Master::back_data($this->uid,$Act6015Model->b_mol,'shop',$Act6015Model->back_data_shop());
        Master::back_data($this->uid,$Act6015Model->b_mol,'exchange',$Act6015Model->back_data_exchange());
    }

    public function hd6015Rwd($params){
        $id = Game::intval($params,'id');
        $Act6015Model = Master::getAct6015($this->uid);
        $Act6015Model->get_rwd($id);

    }

    public function hd6015Rank(){
        $Act6015Model = Master::getAct6015($this->uid);
        $Redis6015Model = Master::getRedis6015($Act6015Model->hd_cfg['info']['id']);
        $Redis6015Model->back_data();
        $Redis6015Model->back_data_my($this->uid);//我的排名
    }

    /**
     * 抢汤圆 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6015buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6015Model = Master::getAct6015($this->uid);
        $Act6015Model->shop_buy($id,$num);
    }

    /**
     * 抢汤圆 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6015exchange($params){
        $id = Game::intval($params,'id');
        $Act6015Model = Master::getAct6015($this->uid);
        $Act6015Model->exchange($id);
    }


    //------------------- 盛装出席 -----------------------
    /**
     * 兑换活动
     */
    public function hd6123Info(){
        $Act6123Model = Master::getAct6123($this->uid);
        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);

        $Act6123Model->back_data_hd(true);
        $Sev6123Model->list_init($this->uid);
        $Sev6123Model->list_click($this->uid);
    }

    public function hd6123Rwd($params){
        $id = Game::intval($params,'id');
        $Act6123Model = Master::getAct6123($this->uid);
        $Act6123Model->get_rwd($id);

        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->list_click($this->uid);
    }

    public function hd6123Rank(){
        $Act6123Model = Master::getAct6123($this->uid);
        $Redis6123Model = Master::getRedis6123($Act6123Model->hd_cfg['info']['id']);
        $Redis6123Model->back_data();
        $Redis6123Model->back_data_my($this->uid);//我的排名

        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->list_click($this->uid);
    }

    public function hd6123Fight($params){
        $id = Game::intval($params,'id');
        $head = Game::intval($params,"head");
        $body = Game::intval($params,"body");
        $ear = Game::intval($params,"ear");
        $bg = Game::intval($params,"background");
        $eff = Game::intval($params,"effect");
        $ani = Game::intval($params,"animal");
        $Act6123Model = Master::getAct6123($this->uid);
        $Act6123Model-> fight($head, $body, $ear, $bg, $eff, $ani, $id);

        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->list_click($this->uid);
    }

    public function hd6123Clear($params){
        $Act6123Model = Master::getAct6123($this->uid);
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6123Model->clear($id, $num);

        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->list_click($this->uid);
    }

    public function hd6123Add(){
        $Act6123Model = Master::getAct6123($this->uid);
        $Act6123Model->addCount();

        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->list_click($this->uid);
    }

    public function hd6123Referr($params){
        $Act6123Model = Master::getAct6123($this->uid);
        $id = Game::intval($params,'id');
        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->get_referr($this->uid, $id);

        $Sev6123Model = Master::getSev6123($Act6123Model->hd_cfg['info']['id']);
        $Sev6123Model->list_click($this->uid);
    }

    //------------------- 盛装出席 -----------------------
    /**
     *
     */
    public function hd6142Info(){
        $Act6142Model = Master::getAct6142($this->uid);
        $Act6142Model->back_data_hd(true);
    }

    public function hd6142Rwd(){
        $Act6142Model = Master::getAct6142($this->uid);
        $Act6142Model->get_rwd();
    }

    public function hd6142Rank(){
        $Act6142Model = Master::getAct6142($this->uid);
        $Redis6142Model = Master::getRedis6142($Act6142Model->hd_cfg['info']['id']);
        $Redis6142Model->back_data();
        $Redis6142Model->back_data_my($this->uid);//我的排名
    }

    public function hd6142Zan($params){
        $id = Game::intval($params,'id');
        $Act6142Model = Master::getAct6142($this->uid);
        $Act6142Model->zan($id);
    }

    public function hd6142Fight($params){
        $head = Game::intval($params,"head");
        $body = Game::intval($params,"body");
        $ear = Game::intval($params,"ear");
        $bg = Game::intval($params,"background");
        $eff = Game::intval($params,"effect");
        $ani = Game::intval($params,"animal");
        $Act6142Model = Master::getAct6142($this->uid);
        $Act6142Model->saveClothe($head, $body, $ear, $bg, $eff, $ani);
    }

    public function hd6142Math(){
        $Act6142Model = Master::getAct6142($this->uid);
        $Act6142Model->getMath();
    }

    //------------------- 皇子应援活动 -----------------------
    /**
     * 皇子应援活动
     */
    public function hd6136Info(){
        $Act6136Model = Master::getAct6136($this->uid);
        $Act6136Model->back_data_hd();
    }

    /**
     * 皇子应援活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6136buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6136Model = Master::getAct6136($this->uid);
        $Act6136Model->buyone($id,$num);
    }

    /**
     * 皇子应援活动 - 积分兑换
     * $params['id'] :  商品id
     */
    public function hd6136exchange($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6136Model = Master::getAct6136($this->uid);
        $Act6136Model->exchangea($id,$num);
    }

    /**
     * 皇子应援活动 - 应援
     * $params['id'] :  道具id
     */
    public function hd6136play($params){
        $id = Game::intval($params,'id');
        $pkID = Game::intval($params,'pkID');
        $Act6136Model = Master::getAct6136($this->uid);
        $Act6136Model->play($id,$pkID);
		//限时-皇子应援次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6176',1);
    }
    /*
     * 皇子应援活动 - 排行奖励
     * */
    public function hd6136paihang() {
        $Act6136Model = Master::getAct6136($this->uid);
        $Act6136Model->paihang();
    }

    /*
     * 皇子应援活动- 领取活动奖励
     * */
    public function hd6136Rewards(){
        $Act6136Model = Master::getAct6136($this->uid);
        $Act6136Model->rewards();
    }

    /*
     * 皇子应援活动- 累计充值领取
     * */
    public function hd6136getRwd($params){
        $id = Game::intval($params, 'id');
        $Act6165Model = Master::getAct6165($this->uid);
        $Act6165Model->getRwd($id);
    }

    /*
     * 皇子应援活动- 应援日志
     * */
    public function hd6136Journal($params){
        $cfgId = Game::intval($params, 'id');
        //日志
        $Sev6136Model = Master::getSev6136($cfgId);
        $Sev6136Model->bake_data();
    }

    /**
     * 语音包活动 --- 信息
     */
    public function hd6137Info($params){
        $Act6137Model = Master::getAct6137($this->uid);
        $Act6137Model->back_data();
    }

    /**
     * 语音包活动   --- 领取奖励
     * $params['id'] :  门客id
     */
    public function hd6137Rwd($params){
        $id = Game::intval($params,'id');
        $Act6137Model = Master::getAct6137($this->uid);
        $Act6137Model->buy($id);
    }


	//------------------- 双12转盘 -----------------------
	/**
	 * 双12转盘 -- 基本信息
	 */
	public function hd290Info($params){

		//活动配置
		$Act290Model = Master::getAct290($this->uid);
		$Act290Model->back_data_hd();

		//双12-剩余积分不删档
		$Act86Model = Master::getAct86($this->uid);
		$Act86Model->back_data();

		//双12转盘  -- 日志
		$Act290Model->out_log($this->uid,1);

		//跑马灯
		$Act290Model->out_pmd($this->uid,1);

	}

	/**
	 * 双12转盘  -- 摇奖
	 * "type":[0,"1:外圈  2:内圈"],
	 * "num":[0,"次数 1 次 或者 10次"]
	 */
	public function hd290Yao($params){

		$type = Game::intval($params,'type');
		$num = Game::intval($params,'num');
		//验证参数
		if( !in_array($type,array(1,2)) || !in_array($num,array(1,10)) ){
			Master::error(PARAMS_ERROR.$type.$num);  //参数错误
		}

		$Act290Model = Master::getAct290($this->uid);
		if($Act290Model->get_state() == 2){
			Master::error(ACTHD_OVERDUE);
		}

		$is_free = false;
		if($num == 1 ){
			$is_free = $Act290Model->check_free($type);
		}
		//如果不是免费
		if(!$is_free){
			//扣除花费元宝
			$pay = $Act290Model->pay($type,$num);
			Master::sub_item($this->uid,KIND_ITEM,1,$pay['pay']);

			//加入个人不删档分数
			$Act86Model = Master::getAct86($this->uid);
			$Act86Model->add($pay['jifen']);
			//计入本次活动总积分
			$Act290Model->add($pay['jifen']);

		}else{
			$Act290Model->back_free($type);
		}

		//摇奖
		$UserModel = Master::getUser($this->uid);
		$name = Game::filter_char($UserModel->info['name']);    //玩家名字

		$wlist = array(); //前端弹窗
		$zmdlist = array(); //统计走马灯
		for($i = 1 ; $i <= $num ; $i ++){

			//摇奖获得的配置
			$rwd = $Act290Model->yao($type);
			//加道具
			Master::add_item2($rwd['items']);

			//统计跑马灯
			$zmdlist[$rwd['dc']] = empty($zmdlist[$rwd['dc']])?1:$zmdlist[$rwd['dc']]+1;

			//列入获奖情况日志
			if( !empty($rwd['tip']) ){
				$loglist = array();
				$loglist['name'] = $name;
				$loglist['type'] = $type;  //"type":[0,"1:外圈  2:内圈"],
				$loglist['dc'] = $rwd['dc'];
				$loglist['time'] = $_SERVER['REQUEST_TIME'];
				$Act290Model->add_log($loglist);
			}

			//列入前端弹窗列表
			$wlist[]['id'] = $rwd['dc'];
		}


		//列入跑马灯
		$zlist = array();
		$zlist['name'] = $name;
		$zlist['type'] = $type;  //"type":[0,"1:外圈  2:内圈"],
		foreach($zmdlist as $k => $v){
			$zlist['list'][] = array(
				'id' => $k,
				'count' => $v,
			);
		}
		$Act290Model->add_pmd($zlist);
		//下发给客户端
		$Act290Model->back_data_cd_u();
		Master::$bak_data['a']['zphuodong']['win'] = $wlist;

		//双12转盘  -- 日志
		$Act290Model->out_log($this->uid);

		//跑马灯
		$Act290Model->out_pmd($this->uid);

	}

	 /**
	 * 双12转盘  -- 日志
	 * $params['id'] : 位置标识
	 */
	public function hd290log($params){

		$id = Game::intval($params,'id');

		$Act290Model = Master::getAct290($this->uid);
		$Act290Model->out_log_history($this->uid,$id);

	}

	 /**
	 * 双12转盘  -- 兑换
	 * $params['id'] : 档次dc
	 */
	public function hd290exchange($params){

		$id = Game::intval($params,'id');

		//获取配置-兑换商店
		$Act290Model = Master::getAct290($this->uid);
		$shop = $Act290Model->get_shop($id);

		//扣除个人不删档分数
		$Act86Model = Master::getAct86($this->uid);
		$Act86Model->sub($shop[$id]['need']);

		//获得道具
		Master::add_item2($shop[$id]['items']);

		$Act290Model->back_data_shop_u();

	}

    /**
     * 获取排行榜列表
     * @param $params
     */
	public function hdGetXSRank($params)
    {
        $type = Game::intval($params, 'type');
        $act = "getAct{$type}";
        if (!method_exists('Master', $act)) {
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        $Act280Model = Master::$act($this->uid);
        $Act280Model->back_rank_data();
    }

    //------------------- 转盘-命盘 -----------------------
    /**
     * 命盘活动 -- 基本信息
     */
    public function hd6169Info($params){

        //活动配置
        $Act6169Model = Master::getAct6169($this->uid);
        $Act6169Model->back_data_hd();

    }

    /**
     * 命盘活动  -- 摇奖
     * "num":[0,"次数 1 次 或者 10次"]
     */
    public function hd6169Yao($params){

        $num = Game::intval($params,'num');
        //验证参数
        if( !in_array($num,array(1,10)) ){
            Master::error(PARAMS_ERROR.$num);  //参数错误
        }
        $Act6169Model = Master::getAct6169($this->uid);
        $Act6169Model->yao($num);
    }

    //------------------- 幸运命盘-----------------------
    /**
     * 幸运命盘 -- 基本信息
     */
    public function hd6227Info(){

        //活动配置
        $Act6227Model = Master::getAct6227($this->uid);
        $Act6227Model->back_data_hd();
        $Sev6227Model = Master::getSev6227($Act6227Model->hd_cfg['info']['id']);
        $Sev6227Model->bake_data();
        Master::back_data($this->uid,$Act6227Model->b_mol,'shop',$Act6227Model->back_data_shop());
        Master::back_data($this->uid,$Act6227Model->b_mol,'scoreExchange',$Act6227Model->back_data_exchange());

    }

    /**
     * 幸运命盘  -- 摇奖
     * "num":[0,"次数 1 次 或者 10次"]
     */
    public function hd6227Yao($params){

        $num = Game::intval($params,'num');
        //验证参数
        if( !in_array($num,array(1,10)) ){
            Master::error(PARAMS_ERROR.$num);  //参数错误
        }
        $Act6227Model = Master::getAct6227($this->uid);
        $Act6227Model->yao($num);
        $Act6227Model->back_data_hd();
        $Act6227Model->back_data();
    }

    /**
     * 幸运命盘 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6227buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6227Model = Master::getAct6227($this->uid);
        $Act6227Model->shop_buy($id,$num);
        $Act6227Model->back_data();
        $Act6227Model->back_data_hd();
        Master::back_data($this->uid,$Act6227Model->b_mol,'shop',$Act6227Model->back_data_shop());
    }

    /**
     * 幸运命盘  -- 排行榜
     * "type":["1:每日排行 2:累计排行"]
     */
    public function hd6227Paihang($params){
        $type = Game::intval($params,'type');
        //验证参数
        if( !in_array($type,array(1,2)) ){
            Master::error(PARAMS_ERROR.$type);  //参数错误
        }
        $Act6227Model = Master::getAct6227($this->uid);
        $Act6227Model->paihang($type);
    }

    /**
     * 幸运命盘 -- 兑换
     * ['id'] : 档次
     */
    public function hd6227duihuan($params){

        $id = Game::intval($params,'id');

        $Act6227Model = Master::getAct6227($this->uid);
        $Act6227Model->exchangea_cons($id);
        $Act6227Model->back_data();
        $Act6227Model->back_data_hd();
        Master::back_data($this->uid,$Act6227Model->b_mol,'scoreExchange',$Act6227Model->back_data_exchange());

    }

    //------------------- 双旦 -----------------------
    /**
     * 双旦 -- 基本信息  下发 291 292 生效活动信息
     */
    public function hd291Info($params){

        //活动配置
        $Act291Model = Master::getAct291($this->uid);
        $Act291Model->back_data_hd();

        //活动配置
        $Act292Model = Master::getAct292($this->uid);
        $Act292Model->back_data_hd();

        //跑马灯
        $Act291Model->out_pmd($this->uid,1);

    }
    /**
     * 双旦 -- 砸蛋
     */
    public function hd291Zadan($params){

        $num = Game::intval($params,'num');
        //验证参数
        if( !in_array($num,array(1,10)) ){
            Master::error(PARAMS_ERROR.$num);  //参数错误
        }

        $Act291Model = Master::getAct291($this->uid);
        $Act291Model->zadan($num);
        //跑马灯
        $Act291Model->out_pmd($this->uid);

    }

    /**
     * 双旦 -- 单抽消费提示
     * ['type'] : 单抽是否消费提示0:不提示 1:提示
     */
    public function hd291Set($params){

        $type = Game::intval($params, 'type');

        $Act291Model = Master::getAct291($this->uid);
        $Act291Model->set_cons($type);

    }

    /**
     * 双旦 -- 兑换
     * ['id'] : 档次
     */
    public function hd292exchange($params){

        $id = Game::intval($params,'id');

        $Act292Model = Master::getAct292($this->uid);
        $Act292Model->buy($id);
        $Act292Model->back_data_hd();

    }

    /**
     * 双旦 -- 基本信息  下生效活动信息
     */
    public function hd293Info($params){

        //活动配置
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->do_check();
        $Act293Model->back_data_hd();

    }
    /**
     * 双旦-寻宝大冒险 - 领取圈数奖励
     * ['id'] : 档次id
     */
    public function hd293Rwd($params){

        $id = Game::intval($params,'id');

        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_quan_rwd($id);
        $Act293Model->back_data_rwd_u();

    }
    /**
     * 双旦-寻宝大冒险 - 领取每日重置任务
     * ['id'] : 任务id
     */
    public function hd293Task($params){

        $id = Game::intval($params,'id');
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_rwd($id);
        $Act293Model->back_data_touzi_u();
    }
    /**
     * 双旦-寻宝大冒险 - 要骰子转圈圈
     */
    public function hd293Run($params){

        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->run();
        $Act293Model->back_data_gezi_u();
    }





    //------------------- 招财进宝 -----------------------
    /**
     * 招财进宝 -- 基本信息
     */
    public function hd294Info($params){

        //活动配置
        $Act294Model = Master::getAct294($this->uid);
        $Act294Model->back_data_hd();

        //招财进宝  -- 日志
        //$Act294Model->out_log($this->uid,1);

        //招财进宝 -- 跑马灯
        $Act294Model->out_pmd($this->uid,1);

    }

    /**
     * 招财进宝  -- 摇奖
     * "num":[0,"次数 1 次 或者 10次"]
     */
    public function hd294Zao($params){

        $num = Game::intval($params,'num');
        //验证参数
        if( !in_array($num,array(1,10)) ){
            Master::error(PARAMS_ERROR.$num);  //参数错误
        }

        $Act294Model = Master::getAct294($this->uid);
        if($Act294Model->get_state() != 1){
            Master::error(ACTHD_OVERDUE);
        }

        $is_free = false;
        if($num == 1 ){
            $is_free = $Act294Model->check_free();
        }

        //如果不是免费
        if(!$is_free){
            //扣除花费元宝
            $pay = $Act294Model->pay($num);
            Master::sub_item($this->uid,KIND_ITEM,1,$pay['pay']);

            //加入个人不删档分数
            $Act294Model->add($pay['jifen']);

        }else{
            $Act294Model->back_free();
        }

        //摇奖
        $UserModel = Master::getUser($this->uid);
        $name = Game::filter_char($UserModel->info['name']);    //玩家名字

        $wlist = array(); //前端弹窗
        $zmdlist = array(); //统计走马灯
        for($i = 1 ; $i <= $num ; $i ++){

            //摇奖获得的配置
            $rwd = $Act294Model->zao();
            //加道具
            Master::add_item2($rwd['items']);

            //统计跑马灯
            $zmdlist[$rwd['id']] = empty($zmdlist[$rwd['id']])?1:$zmdlist[$rwd['id']]+1;

            //列入获奖情况日志
            /*
            if( !empty($rwd['tip']) ){
                $loglist = array();
                $loglist['name'] = $name;
                $loglist['dc'] = $rwd['id'];
                $loglist['time'] = $_SERVER['REQUEST_TIME'];
                $Act294Model->add_log($loglist);
            }
            */
            //列入前端弹窗列表
            $wlist[]['id'] = $rwd['id'];
        }


        //列入跑马灯
        $zlist = array();
        $zlist['name'] = $name;
        foreach($zmdlist as $k => $v){
            $zlist['list'][] = array(
                'id' => $k,
                'count' => $v,
            );
        }
        $Act294Model->add_pmd($zlist);
        //下发给客户端
        $Act294Model->back_data_cd_u();
        Master::$bak_data['a']['zchuodong']['win'] = $wlist;

        //招财进宝  -- 日志
        //$Act294Model->out_log($this->uid);

        //招财进宝  -- 跑马灯
        $Act294Model->out_pmd($this->uid);

		Common::loadModel('HoutaiModel');
		$outf = HoutaiModel::get_huodong_list($this->uid,$Act294Model->hd_id);
		Master::back_data($this->uid,'huodonglist','all',$outf,true);

    }

    /**
     * 招财进宝  -- 日志
     * $params['id'] : 位置标识
     */
    public function hd294log($params){

        $id = Game::intval($params,'id');

        $Act294Model = Master::getAct294($this->uid);
        $Act294Model->out_log_history($this->uid,$id);

    }

    /**
     * 招财进宝  -- 领取
     * $params['id'] : 档次dc
     */
    public function hd294Get($params){

        $id = Game::intval($params,'id');
        $Act294Model = Master::getAct294($this->uid);
        $Act294Model->get_jifen_rwd($id);
        $Act294Model->back_data_jifen_u();

		Common::loadModel('HoutaiModel');
		$outf = HoutaiModel::get_huodong_list($this->uid,$Act294Model->hd_id);
		Master::back_data($this->uid,'huodonglist','all',$outf,true);
    }

    /**
     * 单抽消费提示
     * ['type'] : 单抽是否消费提示0:不提示 1:提示
     */
    public function hd294Set($params){

        $type = Game::intval($params, 'type');

        $Act294Model = Master::getAct294($this->uid);
        $Act294Model->set($type);

    }

    /*
     * 招财活动 - 排行 奖励
     * */
    public function hd294paihang() {
        $Act294Model = Master::getAct294($this->uid);
        $Act294Model->paihang();
    }


	//------------------- 腊八节活动 -----------------------
	/**
	 * 腊八节活动
	 */
	public function hd286Info(){
		$Act286Model = Master::getAct286($this->uid);
		$Act286Model->back_data_hd();
	}

	/**
	 * 腊八节活动 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd286buy($params){
		$id = Game::intval($params,'id');
		$Act286Model = Master::getAct286($this->uid);
		$Act286Model->buyone($id);
	}

	/**
	 * 腊八节活动 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd286exchange($params){
		$id = Game::intval($params,'id');
		$Act286Model = Master::getAct286($this->uid);
		$Act286Model->exchange($id);
	}

	/**
	 * 腊八节活动 - 打
	 * $params['id'] :  道具id
	 */
	public function hd286play($params){
		$id = Game::intval($params,'id');
		$Act286Model = Master::getAct286($this->uid);
		$Act286Model->play($id);
	}
	/*
	 * 腊八节活动 - 排行 奖励
	 * */
	public function hd286paihang() {
		$Act286Model = Master::getAct286($this->uid);
		$Act286Model->paihang();
	}

	/*
	 * 腊八节活动- 领取击杀boss奖励
	 * */
	public function hd286Rwd(){
		$Act286Model = Master::getAct286($this->uid);
		$Act286Model->KillRwd();
	}

	/*
	 * 腊八节活动- 累天充值领取
	 * */
	public function hd286getRwd($params){
		$id = Game::intval($params, 'id');
		$Act140Model = Master::getAct140($this->uid);
		$Act140Model->getRwd($id);
	}

	//-------------------------发红包活动-------------------------------


	/**
	 * 发红包-活动详情
	 */
	public function hd295Info(){
		$Act295Model = Master::getAct295($this->uid);
		$Act295Model->back_data_hd();
	}

	/**
	 * 发红包-发红包
	 */
	public function hd295sendHb($params){
		$id = Game::intval($params, 'id');
		$type = Game::intval($params, 'type');
		$Act295Model = Master::getAct295($this->uid);
		$Act295Model->sendHb($id,$type);
	}

	/**
	 * 发红包-领红包
	 */
	public function hd295getHb($params){
		$fuid = Game::intval($params, 'fuid');
		$id = Game::intval($params, 'id');
		$Act295Model = Master::getAct295($this->uid);
		$Act295Model->getHb($fuid,$id);
	}

	/**
	 * 发红包-查看红包详情
	 */
	public function hd295getHbInfo($params){
		$fuid = Game::intval($params, 'fuid');
		$id = Game::intval($params, 'id');
		$Act295Model = Master::getAct295($this->uid);
		$Act295Model->getHbInfo($fuid,$id);
	}

	public function hd295Paihang(){
		$Act295Model = Master::getAct295($this->uid);
		$Act295Model->getPaihang();
	}

	public function hd295mobai(){
		$Act49Model = Master::getAct49($this->uid);
		$Act49Model->add();
	}

	//------------------联盟冲榜-------------------

    /**
     *冲榜活动310信息--联盟冲榜
     */
    public function hd310Info(){
        $Act310Model = Master::getAct310($this->uid);
        $Act310Model->back_data_hd();
    }

    //------------------跨服势力冲榜-------------------

    /**
     *冲榜活动313信息--跨服势力冲榜
     */
    public function hd313Info(){
        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->back_data_hd();
        $Act313Model->list_init();
        $Act313Model->list_click();
    }

    /**
     *冲榜活动313信息--跨服势力冲榜 - 领取区服奖励
     */
    public function hd313Get(){
        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->do_get();
        $Act313Model->back_data_get_u();
        $Act313Model->back_data();
    }

    /**
     *冲榜活动313信息--跨服势力冲榜 - 预选榜单
     */
    public function hd313YXRank(){
        $Act252Model = Master::getAct252($this->uid);
        $Act252Model->back_data_hd313();
    }

    /**
     *冲榜活动313信息--跨服势力冲榜 - 正式个人榜单
     */
    public function hd313UserRank(){
        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->back_data_UserRank();
    }

    /**
     *冲榜活动313信息--跨服势力冲榜 - 正式区服榜单
     */
    public function hd313QuRank(){
        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->back_data_QuRank();
    }

    /*
     * 跨服聊天
     */
    public function hd313Chat($params){

        //聊天信息
        $msg = Game::strval($params,"msg");

        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->add_msg($msg);
        $Act313Model->list_click();
    }

    /*
     * 聊天检测
     */
    public function hd313Check(){

        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->list_click();
    }

    /*
     * 跨服频道 : 历史消息
     */
    public function hd313Log($params){
        //聊天信息
        $id = Game::intval($params,"id");
        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->list_history($id);
    }


    //------------------跨服好感冲榜-------------------

    /**
     *冲榜活动314信息--跨服好感冲榜
     */
    public function hd314Info(){
        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->back_data_hd();
        $Act314Model->list_init();
        $Act314Model->list_click();
    }

    /**
     *冲榜活动314信息--跨服好感冲榜 - 领取区服奖励
     */
    public function hd314Get(){
        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->do_get();
        $Act314Model->back_data_get_u();
        $Act314Model->back_data();
    }

    /**
     *冲榜活动314信息--跨服好感冲榜 - 预选榜单
     */
    public function hd314YXRank(){
        $Act253Model = Master::getAct253($this->uid);
        $Act253Model->back_data_hd314();
    }

    /**
     *冲榜活动314信息--跨服好感冲榜 - 正式个人榜单
     */
    public function hd314UserRank(){
        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->back_data_UserRank();
        $Act314Model->back_data_QuRank();
    }

    /**
     *冲榜活动314信息--跨服好感冲榜 - 正式区服榜单
     */
    public function hd314QuRank(){
        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->back_data_UserRank();
        $Act314Model->back_data_QuRank();
    }

    /*
     * 跨服聊天
     */
    public function hd314Chat($params){

        //聊天信息
        $msg = Game::strval($params,"msg");

        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->add_msg($msg);
        $Act314Model->list_click();
    }

    /*
     * 聊天检测
     */
    public function hd314Check(){

        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->list_click();
    }

    /*
     * 跨服频道 : 历史消息
     */
    public function hd314Log($params){
        //聊天信息
        $id = Game::intval($params,"id");
        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->list_history($id);
    }


    //------------------- 挖宝活动 -----------------------
    /**
     * 挖宝 -- 基本信息  下生效活动信息
     */
    public function hd296Info($params){
        //活动配置
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->do_check();
        $Act296Model->back_data_hd();
    }

    /**
     * 挖宝
     */
    public function hd296Wa($params){

        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->chucao();
        $Act296Model->back_data_rwd_u();

    }

    /**
     * 挖宝 - 领取宝箱
     * ['id'] : 任务id
     */
    public function hd296Rwd($params){

        $id = Game::intval($params,'id');
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->baoxiang($id);
        $Act296Model->back_data_rwd_u();
    }

    /**
     * 挖宝 - 领取每日重置任务
     * ['id'] : 任务id
     */
    public function hd296Task($params){

        $id = Game::intval($params,'id');
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_rwd($id);
        $Act296Model->back_data_task_u();
    }

	//-----------------子嗣势力冲榜-------------------

	/**
	 *冲榜活动311信息--子嗣势力冲榜
	 */
	public function hd311Info($params){
		$Act311Model = Master::getAct311($this->uid);
		$Act311Model->back_data_hd();
	}

	/**
	 *冲榜活动315信息--宫殿宫斗冲榜
	 */
	public function hd315Info($params){
		$Act315Model = Master::getAct315($this->uid);
		$Act315Model->get_info($params);
	}
	/**
	 *冲榜活动315排行榜--宫殿宫斗冲榜
	 */
	public function hd315Rank($params){
		$Act315Model = Master::getAct315($this->uid);
		$Act315Model->get_rank($params);
	}



    //------------------- 情人节 -----------------------
    /**
     * 情人节 -- 基本信息
     */
    public function hd297Info($params){

        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->back_data_hd();

    }

    /**
     * 情人节  -- 抽奖
     * "num":[0,"次数 1 次 或者 10次"]
     * "tip":[0,"0:不在提示,1:提示"]
     */
    public function hd297Yao($params){

        $num = Game::intval($params,'num');
        $tip = Game::intval($params,'tip');
        if(!in_array($num, array(1,10))){
            Master::error(PARAMS_ERROR);
        }
        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->yao($num,$tip);
        $Act297Model->back_data_cons_u();
    }

    /**
     * 情人节--玩家信息
     * $params['fuid'] :玩家id
     */
    public function hd297Guid($params){

        $fuid = Game::intval($params,'fuid');

        //是否合服范围内
        Game::isHeServerUid($fuid);

        $Act297Model = Master::getAct297($fuid);
        $user_info =  Master::fuidInfo($fuid);  //亲家信息
        $user_info['num'] = $Act297Model->get_score(); //收到的玫瑰花数
        Master::back_data($this->uid,'lovehuodong','fUInfo',$user_info);
    }

    /**
     * 情人节--送花
     * $params['fuid'] :玩家id
     * $params['num'] :赠送数量
     */
    public function hd297Send($params){

        $fuid = Game::intval($params,'fuid');
        $num = Game::intval($params,'num');

        //是否合服范围内
        Game::isHeServerUid($fuid);

        if($fuid == $this->uid){
            Master::error(ACT_297_MYSELF);
        }

        if( $num <= 0 ){
            Master::error(PARAMS_ERROR);
        }

        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->send_save($fuid,$num);

        $Act151Model = Master::getAct151($fuid);
        $Act151Model->add_log($fuid,$num);

        $Act297Model->back_data_send_u();

        //一次性赠送超过999朵花则出现全服跑马灯
        $fUserInfo = Master::fuidInfo($fuid);
        $UserInfo = Master::fuidInfo($this->uid);
        $Sev91Model = Master::getSev91();
        $Sev91Model->add_msg(array(109,Game::filter_char($UserInfo['name']),Game::filter_char($fUserInfo['name']),$num ));

    }

    /**
     * 情人节--获赠日志
     */
    public function hd297Log($params){

        $Act151Model = Master::getAct151($this->uid);
        $Act151Model->back_data_a();

    }


    /**
     * 情人节  -- 目标奖励--送花   领取
     * "id":[0,"档次id"]
     */
    public function hd297Sget($params){

        $id = Game::intval($params,'id');
        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->send_rwd($id);
        $Act297Model->back_data_send_u();

    }

    /**
     * 情人节  -- 目标奖励--收花   领取
     * "id":[0,"档次id"]
     */
    public function hd297Gget($params){

        $id = Game::intval($params,'id');
        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->get_rwd($id);
        $Act297Model->back_data_get_u();

    }

    /**
     * 情人节--玩家排行信息 - 收花榜
     */
    public function hd297GRank($params){

        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->back_get_rank_data();
    }

    /**
     * 情人节--玩家排行信息 - 送花榜
     */
    public function hd297SRank($params){

        $Act297Model = Master::getAct297($this->uid);
        $Act297Model->back_send_rank_data();
    }


	//-----------------------新年活动--------------------------
	/**
	 * 新年活动
	 */
	public function hd298Info(){
		$Act298Model = Master::getAct298($this->uid);
		$Act298Model->back_data_hd();
	}

	/**
	 * 新年活动 - 商品购买
	 * $params['id'] :  商品id
	 */
	public function hd298buy($params){
		$id = Game::intval($params,'id');
		$Act298Model = Master::getAct298($this->uid);
		$Act298Model->buyone($id);
	}

	/**
	 * 新年活动 - 积分兑换
	 * $params['id'] :  商品id
	 */
	public function hd298exchange($params){
		$id = Game::intval($params,'id');
		$Act298Model = Master::getAct298($this->uid);
		$Act298Model->exchange($id);
	}

	/**
	 * 新年活动 - 打
	 * $params['id'] :  道具id
	 */
	public function hd298play($params){
		$id = Game::intval($params,'id');
		$Act298Model = Master::getAct298($this->uid);
		$Act298Model->play($id);
	}
	/*
	 * 新年活动 - 排行 奖励
	 * */
	public function hd298paihang($params) {
		$type = Game::intval($params,'type');
		$Act298Model = Master::getAct298($this->uid);
		$Act298Model->paihang($type);
	}

    /*
     * 七天签到活动
     */
    public function hd287Info($params){
        $Act287Model = Master::getAct287($this->uid);
        $Act287Model->back_data_hd();
    }
    /*
     * 七天签到活动---领取奖励
     */
    public function hd287Get($params){
        $id = Game::intval($params,'id');
        $Act287Model = Master::getAct287($this->uid);
        $Act287Model->rwd($id);
        $Act287Model->back_data_hd();
    }

    /*
     * 四十五天签到活动
     */
    public function hd6500Info($params){
        $Act6500Model = Master::getAct6500($this->uid);
        $Act6500Model->back_data_hd();
    }
    /*
     * 四十五天签到活动---领取奖励
     */
    public function hd6500Get($params){
        $id = Game::intval($params,'id');
        $Act6500Model = Master::getAct6500($this->uid);
        $Act6500Model->rwd($id);
        $Act6500Model->back_data_hd();
    }

    /*
     * 国力庆典活动 - 总览
     */
    public function hd6187Info($params){
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->data_out();
    }

    /*
     * 国力庆典 - 领取当如
     */
    public function hd6187Rwd($params){
        $id = Game::intval($params,'id');
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->get_rwd($id);
        $Act6187Model->data_out();
    }

    /*
     * 国力庆典---排行榜
     */
    public function hd6187Paihang($params){
        $type = Game::intval($params,'type');
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->paihang($type);
    }

    /*
     * 国力庆典活动 - 国力庆典每日排行榜总览
     */
    public function hd6187dayPaihang($params){
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->Paihang_day();
    }

    /**
     * 国力庆典活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6187buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->shop_buy($id,$num);
        $Act6187Model->back_data();
    }

    /**
     * 国力庆典活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6187exchange($params){
        $id = Game::intval($params,'id');
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->exchange($id);
    }

    /**
     * 国力庆典活动 - 排行刷新
     * $params['id'] :  商品id
     */
    public function hd6187flush($params){
        $type = Game::intval($params,'type');
        $Act6187Model = Master::getAct6187($this->uid);
        $Act6187Model->flush($type);
    }

    /*
     * 充值翻牌活动 - 活动信息
     */
    public function hd6188Info($params){
        $Act6188Model = Master::getAct6188($this->uid);
        $Act6188Model->back_data_hd();
    }

    /*
     * 充值翻牌活动 - 翻牌
     */
    public function hd6188Rwd($params){
        $id = Game::intval($params,'id');
        $Act6188Model = Master::getAct6188($this->uid);
        $Act6188Model->get_rwd($id);
        $Act6188Model->back_data_hd();
    }

    /*
     * 充值翻牌活动 - 活动信息
     * */
    public function hd6188Journal($params){
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6188');
        //日志
        $Sev6188Model = Master::getSev6188($hd_cfg['info']['id']);
        $Sev6188Model->bake_data();
    }

    /*
     * 点灯笼活动 - 活动信息
     */
    public function hd6189Info($params){
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6189');
        $Act6189Model = Master::getAct6189($this->uid);
        $Act6189Model->back_data_hd();
        $Sev6189Model = Master::getSev6189($hd_cfg['info']['id']);
        $Sev6189Model->bake_data();
    }

    /*
     * 点灯笼活动 - 点灯笼
     */
    public function hd6189Rwd($params){
        $id = Game::intval($params,'id');
        $Act6189Model = Master::getAct6189($this->uid);
        $Act6189Model->get_rwd($id);
        $Act6189Model->back_data_hd();
    }

    /*
     * 二十四节气 - 活动信息
     */
    public function hd6211Info(){
        $Act6211Model = Master::getAct6211($this->uid);
        $Act6211Model->back_data_hd();
    }

    /*
     * 二十四节气 - 免费领取
     */
    public function hd6211free($params){
        $id = Game::intval($params,'id');
        $Act6211Model = Master::getAct6211($this->uid);
        $Act6211Model->free($id);
        $Act6211Model->back_data_hd();
    }

    /*
     * 二十四节气 - 元宝购买
     */
    public function hd6211cash($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6211Model = Master::getAct6211($this->uid);
        $Act6211Model->cash($id,$num);
        $Act6211Model->back_data_hd();
    }

    /*
     * 福星锦鲤活动 - 活动信息
     */
    public function hd6214Info(){
        $Act6214Model = Master::getAct6214($this->uid);
        $Act6214Model->back_data_hd();
    }

    /*
     * 女生节活动 - 活动信息
     */
    public function hd6220Info(){
        $Act6220Model = Master::getAct6220($this->uid);
        $Act6220Model->back_data_hd();
        $Act6220Model->back_data();
        Master::back_data($this->uid,$Act6220Model->b_mol,'shop',$Act6220Model->back_data_shop());
        Master::back_data($this->uid,$Act6220Model->b_mol,'exchange',$Act6220Model->back_data_exchange());
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6220');
        $Sev6220Model = Master::getSev6220($hd_cfg['info']['id']);
        $Sev6220Model->back_data();
    }

    /**
     * 女生节活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6220exchange($params){
        $id = Game::intval($params,'id');
        $Act6220Model = Master::getAct6220($this->uid);
        $Act6220Model->exchange($id);
    }

    /**
     * 新春活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6220buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6220Model = Master::getAct6220($this->uid);
        $Act6220Model->shop_buy($id,$num);
        $Act6220Model->back_data();
    }

    /*
     * 女生节活动 - 抽奖
     */
    public function hd6220Rwd($params){
        $num = Game::intval($params,'num');
        $Act6220Model = Master::getAct6220($this->uid);
        $Act6220Model->play($num);
        $Act6220Model->back_data();
    }

      /**
     * 植树节活动 - 信息
     */
    public function hd6221Info(){
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->back_data();
        $Act6221Model->back_data_hd();
        $Act6221Model->back_data_allhd();
        Master::back_data($this->uid,$Act6221Model->b_mol,'shop',$Act6221Model->back_data_shop());
        Master::back_data($this->uid,$Act6221Model->b_mol,'exchange',$Act6221Model->back_data_exchange());
    }

    /**
     * 植树节活动 - 种树
     * $params['id'] :    道具id
     * $params['pkID'] :  阵营id
     */
    public function hd6221play($params){
        $id = Game::intval($params,'id');
        $pkID = Game::intval($params,'pkID');
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->play($id,$pkID);
        $Act6221Model->back_data_hd();
    }

    /**
     * 植树节活动 - 领取奖励
     * $params['id'] :    档次id
     */
    public function hd6221Rwd($params){
        $id = Game::intval($params,'id');
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->get_rwd($id);
        $Act6221Model->back_data_hd();
    }

    /*
     * 植树节活动 - 排行奖励
     * */
    public function hd6221paihang($params) {
    	$type = Game::intval($params,'type');
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->paihang($type);
    }

    /*
     * 植树节活动 - 选择阵营
     * */
    public function hd6221Select($params) {
        $id = Game::intval($params,'id');
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->Select($id);
        $Act6221Model->back_data_hd();
    }

    /**
     * 植树节活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6221buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->shop_buy($id,$num);
    }

    /**
     * 植树节活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6221exchange($params){
        $id = Game::intval($params,'id');
        $Act6221Model = Master::getAct6221($this->uid);
        $Act6221Model->exchange($id);
    }

     /**
     * 清明踏青
     */
    public function hd6222Info($params){
        $Act6222Model = Master::getAct6222($this->uid);
        $Act6222Model->back_data_hd();
        Master::back_data($this->uid,$Act6222Model->b_mol,'shop',$Act6222Model->back_data_shop());
        Master::back_data($this->uid,$Act6222Model->b_mol,'exchange',$Act6222Model->back_data_exchange());
    }

    /**
     * 清明踏青 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6222buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6222Model = Master::getAct6222($this->uid);
        $Act6222Model->buyone($id,$num);
        $Act6222Model->back_data();
    }

    /**
     * 清明踏青 - 积分兑换
     * $params['id'] :  商品id
     */
    public function hd6222exchange($params){
        $id = Game::intval($params,'id');
        $Act6222Model = Master::getAct6222($this->uid);
        $Act6222Model->exchange($id);
        $Act6222Model->back_data_hd();
    }

    /**
     * 清明踏青 - 踏青
     * $params['id'] :  道具id
     */
    public function hd6222play($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6222Model = Master::getAct6222($this->uid);
        $Act6222Model->play($id,$num);
    }
    /*
     * 清明踏青 - 排行 奖励
     * */
    public function hd6222paihang() {
        $Act6222Model = Master::getAct6222($this->uid);
        $Act6222Model->paihang();
    }

    /**
     * 拼图活动 - 信息
     */
    public function hd6223Info($params){
        $Act6223Model = Master::getAct6223($this->uid);
        $Act6223Model->back_data_hd();
        Master::back_data($this->uid,'jigsaw','rwdLog',$Act6223Model->mk_outf());
    }

    /**
     * 拼图活动 - 领奖
     */
    public function hd6223Rwd($params){
        $Act6223Model = Master::getAct6223($this->uid);
        $Act6223Model->get_rwds();
        $Act6223Model->back_data_hd();
    }

    /**
     * 拼图活动 - 赠送
     */
    public function hd6223give($params){
        $fuid = Game::intval($params,'fuid');
        $itemid = Game::intval($params,'itemId');
        $itemInfo = Game::getcfg_info('item',$itemid);
        $item = array('id'=>$itemInfo['id'],'kind'=>$itemInfo['kind'],'count'=>1);

        //是否合服范围内
        Game::isHeServerUid($fuid);
        $user_info =  Master::fuidInfo($fuid);  //玩家信息
        if(empty($user_info['level'])){
            Master::error(SPELL_PLEASE_INPUT_ID);
        }

        //记录赠送
        $Act6223ModelMe = Master::getAct6223($this->uid);
        $Act6223ModelMe->addlog($fuid,$item,0);
        $Act6223ModelMe->back_data_hd();
        Master::back_data($this->uid,'jigsaw','rwdLog',$Act6223ModelMe->mk_outf());
        //对方记录获赠
        $Act6223Model = Master::getAct6223($fuid);
        $Act6223Model->add($item['id']);
        $Act6223Model->addlog($this->uid,$item,1);
        Master::back_data($fuid,'jigsaw','rwdLog',$Act6223Model->mk_outf());
    }

    /**
     * 舞狮大会 - 信息
     */
    public function hd6224Info(){
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->back_data_hd();
    }

    /**
     * 舞狮大会 - 解锁
     */
    public function hd6224buy(){
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->buyone();
        $Act6224Model->back_data_hd();
    }


    /**
     * 舞狮大会 - 更换任务
     */
    public function hd6224change(){
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->rand_task(true);
        $Act6224Model->back_data_hd();
    }

    /**
     * 舞狮大会 - 领活动奖
     */
    public function hd6224Rwd($params){
        $id = Game::intval($params,'id');
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->get_rwds($id);
        $Act6224Model->back_data_hd();
    }

    /**
     * 舞狮大会 - 领任务奖
     */
    public function hd6224task($params){
        $id = Game::intval($params,'id');
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->get_task_rwds($id);
        $Act6224Model->back_data_hd();
    }

    /**
     *充值活动  -- 新累天充值信息
     */
    public function hd6225Info($params){
        $Act6225Model = Master::getAct6225($this->uid);
        $Act6225Model->back_data_hd();
    }

    /**
     * 充值活动  -- 新累天充值每日领取奖励
     */
    public function hd6225Rwd($params){
        $id = Game::intval($params,'id');
        $Act6225Model = Master::getAct6225($this->uid);
        $Act6225Model->get_rwd($id);
        $Act6225Model->back_data_hd();
    }

    /**
     * 充值活动  -- 新累天充值累计领取奖励
     */
    public function hd6225TotalRwd($params){
        $id = Game::intval($params,'id');
        $Act6225Model = Master::getAct6225($this->uid);
        $Act6225Model->get_totalrwd($id);
        $Act6225Model->back_data_hd();
    }

    /*
     * 单笔连续充值活动 - 活动信息
     */
    public function hd6226Info(){
        $Act6226Model = Master::getAct6226($this->uid);
        $Act6226Model->back_data_hd();
    }

    /*
     * 单笔连续充值活动 - 领奖
     * id : 波数
     * dc : 档次
     */
    public function hd6226Rwd($params){
        $id = Game::intval($params,'id');
        $dc = Game::intval($params,'dc');
        $Act6226Model = Master::getAct6226($this->uid);
        $Act6226Model->get_wave_rwd($id,$dc);
        $Act6226Model->back_data();
        $Act6226Model->back_data_hd();
    }

    /*
     * 读书节活动 - 活动信息
     */
    public function hd6228Info(){
        $Act6228Model = Master::getAct6228($this->uid);
        $Act6228Model->back_data_hd();
        $Act6228Model->back_data();
        $Sev6228Model = Master::getSev6228($Act6228Model->hd_cfg['info']['id']);
        $Sev6228Model->back_data();
        $shop = $Act6228Model->back_data_shop();
        Master::back_data($this->uid,$Act6228Model->b_mol,'shop',$shop);
    }

    /**
     * 读书节活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6228buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6222Model = Master::getAct6228($this->uid);
        $Act6222Model->shop_buy($id,$num);
        $Act6222Model->back_data();
    }

    /*
     * 读书节活动 - 抽奖
     */
    public function hd6228Rwd($params){
        $num = Game::intval($params,'num');
        $Act6228Model = Master::getAct6228($this->uid);
        $Act6228Model->play($num);
        $Act6228Model->back_data();
    }

    /**
     * 劳动节活动 - 信息
     */
    public function hd6229Info(){
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->back_data();
        $Act6229Model->back_data_hd();
        $Act6229Model->data_out();

    }

    /**
     * 劳动节活动 - 种菜
     * $params['id'] :    道具id
     * $params['pkID'] :  阵营id
     */
    public function hd6229play($params){
        $id = Game::intval($params,'id');
        $type = Game::intval($params,'type');
        $num = Game::intval($params,'num');
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->play($id,$type,$num);
        $Act6229Model->back_data_hd();
    }

    /**
     * 劳动节活动 - 领取奖励
     * $params['id'] :    档次id
     */
    public function hd6229Rwd($params){
        $id = Game::intval($params,'id');
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->get_rwd($id);
        $Act6229Model->back_data_hd();
    }

    /*
     * 劳动节活动 - 排行奖励
     * */
    public function hd6229paihang() {
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->paihang();
    }

    /*
     * 劳动节活动 - 选择阵营
     * */
    public function hd6229Select($params) {
        $id = Game::intval($params,'id');
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->select($id);
        $Act6229Model->back_data_hd();
    }

    /**
     * 劳动节活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6229buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->shop_buy($id,$num);
        $Act6229Model->back_data();
    }

    /**
     * 劳动节活动 - 积分兑换
     * $params['id'] :  商品id
     */
    public function hd6229exchange($params){
        $id = Game::intval($params,'id');
        $Act6229Model = Master::getAct6229($this->uid);
        $Act6229Model->exchange($id);
    }

    /**
     * 端午节活动 - 信息
     */
    public function hd6230Info(){
        $Act6230Model = Master::getAct6230($this->uid);
        $Act6230Model->back_data_allhd();
        $Act6230Model->back_data();

    }

    /**
     * 端午节活动 - 划龙舟
     * $params['num'] :   道具数量
     */
    public function hd6230play($params){
        $num = Game::intval($params,'num');
        $Act6230Model = Master::getAct6230($this->uid);
        $Act6230Model->play($num);
        $Act6230Model->back_data();
    }

    /*
     * 端午节活动 - 排行奖励
     * */
    public function hd6230paihang() {
        $Act6230Model = Master::getAct6230($this->uid);
        $Act6230Model->paihang();
    }

    /**
     * 端午节活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6230buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6230Model = Master::getAct6230($this->uid);
        $Act6230Model->shop_buy($id,$num);
        $Act6230Model->back_data();
    }

    /**
     * 端午节活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6230exchange($params){
        $id = Game::intval($params,'id');
        $Act6230Model = Master::getAct6230($this->uid);
        $Act6230Model->exchange($id);
    }

   /**
     * 抢糕点
     */
    public function hd6231Info(){
        $Act6231Model = Master::getAct6231($this->uid);
        $Act6231Model->back_data_hd();
        Master::back_data($this->uid,$Act6231Model->b_mol,'shop',$Act6231Model->back_data_shop());
        Master::back_data($this->uid,$Act6231Model->b_mol,'exchange',$Act6231Model->back_data_exchange());
    }

    public function hd6231Rwd($params){
        $id = Game::intval($params,'id');
        $Act6231Model = Master::getAct6231($this->uid);
        $Act6231Model->get_rwd($id);

    }

    public function hd6231Rank(){
        $Act6231Model = Master::getAct6231($this->uid);
        $Act6231Model->paihang();
    }

    /**
     * 抢糕点 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6231buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6231Model = Master::getAct6231($this->uid);
        $Act6231Model->shop_buy($id,$num);
    }

    /**
     * 抢糕点 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6231exchange($params){
        $id = Game::intval($params,'id');
        $Act6231Model = Master::getAct6231($this->uid);
        $Act6231Model->exchange($id);
    }

    /**
     * 热气球 - 信息
     */
    public function hd6232Info(){
        $Act6232Model = Master::getAct6232($this->uid);
        $Act6232Model->back_data_allhd();
        $Act6232Model->back_data();
    }

    /**
     * 热气球 - 行驶
     * $params['num'] :   道具数量
     */
    public function hd6232play($params){
        $num = Game::intval($params,'num');
        $Act6232Model = Master::getAct6232($this->uid);
        $Act6232Model->play($num);
        $Act6232Model->back_data();
    }

    /*
     * 热气球 - 排行奖励
     * */
    public function hd6232paihang() {
        $Act6232Model = Master::getAct6232($this->uid);
        $Act6232Model->paihang();
    }

    /**
     * 热气球 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6232buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6232Model = Master::getAct6232($this->uid);
        $Act6232Model->shop_buy($id,$num);
        $Act6232Model->back_data();
    }

    /**
     * 热气球 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6232exchange($params){
        $id = Game::intval($params,'id');
        $Act6232Model = Master::getAct6232($this->uid);
        $Act6232Model->exchange($id);
    }

    /**
     * 热气球 - 信息
     */
    public function hd8002Info(){
        $Act8002Model = Master::getAct8002($this->uid);
        $Act8002Model->back_data_allhd();
        $Act8002Model->back_data();
    }

    /**
     * 热气球 - 行驶
     * $params['num'] :   道具数量
     */
    public function hd8002play($params){
        $num = Game::intval($params,'num');
        $Act8002Model = Master::getAct8002($this->uid);
        $Act8002Model->play($num);
        $Act8002Model->back_data();
    }

    /*
     * 热气球 - 排行奖励
     * */
    public function hd8002paihang() {
        $Act8002Model = Master::getAct8002($this->uid);
        $Act8002Model->paihang();
    }

    /**
     * 热气球 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8002buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8002Model = Master::getAct8002($this->uid);
        $Act8002Model->shop_buy($id,$num);
        $Act8002Model->back_data();
    }

    /**
     * 热气球 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8002exchange($params){
        $id = Game::intval($params,'id');
        $Act8002Model = Master::getAct8002($this->uid);
        $Act8002Model->exchange($id);
    }

    /**
     * 四大藩王
     */
    public function hd6233Info(){
        $Act6233Model = Master::getAct6233($this->uid);
        $Act6233Model->back_data_hd();
    }
    /***
     * 四大藩王
     * @param $params
     */
    public function hd6233Rwd($params)
    {
        $id = Game::intval($params, 'id');
        $Act6233Model = Master::getAct6233($this->uid);
        $Act6233Model->get_rwd($id);
    }

    /**
     *荷灯- 信息
     */
    public function hd6234Info($params){
        $Act6234Model = Master::getAct6234($this->uid);
        $Act6234Model->back_data_hd();
        $Act6234Model->back_data();
    }
    /**
     *荷灯
     */
    public function hd6234Paly($params){
        $Act6234Model = Master::getAct6234($this->uid);
        $Act6234Model->play();
        $Act6234Model->back_data_hd();
    }
    /**
     *荷灯十次
     */
    public function hd6234PalyTen($params){
        $Act6234Model = Master::getAct6234($this->uid);
        $Act6234Model->play(10);
        $Act6234Model->back_data_hd();
    }

    /**
     * 荷灯- 领取奖励
     * @param int $params
     */
    public function hd6234Rwd($params){
        $lv = Game::intval($params,'lv');
        $Act6234Model = Master::getAct6234($this->uid);
        $Act6234Model->get_rwd($lv);
        $Act6234Model->back_data_hd();
    }

    /***
     * 荷灯.排行
     */
    public function hd6234paihang() {
        $Act6232Model = Master::getAct6234($this->uid);
        $Act6232Model->paihang();
    }

    /**
     * 荷灯 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6234buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6234Model = Master::getAct6234($this->uid);
        $Act6234Model->shop_buy($id,$num);
        $Act6234Model->back_data();
    }

    /**
     * 荷灯 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6234exchange($params){
        $id = Game::intval($params,'id');
        $Act6234Model = Master::getAct6234($this->uid);
        $Act6234Model->exchange($id);
    }

    /**
     *跨服通用兑换- 信息
     */
    public function hd6240Info($params){
        $Act6240Model = Master::getAct6240($this->uid);
        $Act6240Model->back_data_hd();
    }

    /**
     * 跨服通用兑换 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6240exchange($params){
        $id = Game::intval($params,'id');
        $Act6240Model = Master::getAct6240($this->uid);
        $Act6240Model->exchange($id);
        $Act6240Model->back_data_hd();
        $Act6240Model->back_data();
    }

    /**
     *七夕活动- 信息
     */
    public function hd6241Info($params){
        $Act6241Model = Master::getAct6241($this->uid);
        $Act6241Model->back_data_hd();
        $Act6241Model->back_data();
        Master::back_data($this->uid,$Act6241Model->b_mol,'shop',$Act6241Model->back_data_shop());
        Master::back_data($this->uid,$Act6241Model->b_mol,'exchange',$Act6241Model->back_data_exchange());
    }
    /**
     *七夕活动
     */
    public function hd6241Paly($params){
        $num = Game::intval($params,'num');
        if (!in_array($num,array(1,10))){

        }
        $Act6241Model = Master::getAct6241($this->uid);
        $Act6241Model->play($num);
        $Act6241Model->back_data_hd();
    }

    /**
     * 七夕活动- 领取奖励
     * @param int $params
     */
    public function hd6241Rwd($params){
        $id = Game::intval($params,'id');
        $hid = Game::intval($params,'hid');
        $Act6241Model = Master::getAct6241($this->uid);
        $Act6241Model->get_hrwd($id,$hid);
        $Act6241Model->back_data_hd();
    }

    /**
     * 七夕活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd6241exchange($params){
        $id = Game::intval($params,'id');
        $Act6241Model = Master::getAct6241($this->uid);
        $Act6241Model->exchange($id);
    }

    /***
     * 七夕活动.排行
     */
    public function hd6241paihang() {
        $Act6241Model = Master::getAct6241($this->uid);
        $Act6241Model->paihang();
    }

    /**
     * 七夕活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd6241buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act6241Model = Master::getAct6241($this->uid);
        $Act6241Model->shop_buy($id,$num);
        $Act6241Model->back_data();
    }

    /**
     * kv展示
     */
    public function hd7001List($params){
        $Act7001Model = Master::getAct7001($this->uid);
        $Act7001Model->make_out();
    }

    /**
     *新人团购- 信息
     */
    public function hd7010Info(){
        $Act7010Model = Master::getAct7010($this->uid);
        $Act7010Model->back_data_hd();
    }

    /**
     *新人团购- 领奖
     */
    public function hd7010Rwd($params){

        $id = Game::intval($params,'id');
        $Act7010Model = Master::getAct7010($this->uid);
        $Act7010Model->pickRwd($id);
        $Act7010Model->back_data_hd();
        $Act7010Model->back_data();
    }

    /**
     *许愿池- 信息
     */
    public function hd8003Info($params){
        $Act8003Model = Master::getAct8003($this->uid);
        $Act8003Model->back_data_hd();
        $Act8003Model->back_data();
		$Act8003Model->out_log_history($this->uid);
        Master::back_data($this->uid,$Act8003Model->b_mol,'shop',$Act8003Model->back_data_shop());
        Master::back_data($this->uid,$Act8003Model->b_mol,'exchange',$Act8003Model->back_data_exchange());
    }

    /**
     *许愿池
     */
    public function hd8003Play($params){
        $num = Game::intval($params,'num');
        if (!in_array($num,array(1,10))){

        }
        $Act8003Model = Master::getAct8003($this->uid);
        $Act8003Model->play($num);
        $Act8003Model->back_data_hd();
    }

    /***
     * 许愿池.排行
     */
    public function hd8003paihang() {
        $Act8003Model = Master::getAct8003($this->uid);
        $Act8003Model->paihang();
    }

    /**
     * 许愿池 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8003exchange($params){
        $id = Game::intval($params,'id');
        $Act8003Model = Master::getAct8003($this->uid);
        $Act8003Model->exchange($id);
    }

    /**
     * 许愿池- 领取奖励
     * @param int $params
     */
    public function hd8003Rwd($params){
        $id = Game::intval($params,'id');
        $Act8003Model = Master::getAct8003($this->uid);
        $Act8003Model->get_hrwd($id);
        $Act8003Model->back_data_hd();
    }

    /**
     * 许愿池 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8003buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8003Model = Master::getAct8003($this->uid);
        $Act8003Model->shop_buy($id,$num);
        $Act8003Model->back_data();
    }

    /**
	 * 许愿池  -- 日志
	 * $params['id'] : 位置标识
	 */
	public function hd8003log($params){

		$id = Game::intval($params,'id');

		$Act8003Model = Master::getAct8003($this->uid);
		$Act8003Model->out_log_history($this->uid,$id);

	}

	/**
     *购物狂欢- 信息
     */
    public function hd8004Info($params){
        $Act8004Model = Master::getAct8004($this->uid);
        $Act8004Model->back_data_hd();
        $Act8004Model->back_data();
        Master::back_data($this->uid,$Act8004Model->b_mol,'exchange',$Act8004Model->back_data_exchange());
    }

    /**
     * 购物狂欢 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8004exchange($params){
        $id = Game::intval($params,'id');
        $Act8004Model = Master::getAct8004($this->uid);
        $Act8004Model->exchange($id);
    }

    /**
     * 购物狂欢- 领取奖励
     * @param int $params
     */
    public function hd8004Rwd($params){
        $id = Game::intval($params,'id');
        $Act8004Model = Master::getAct8004($this->uid);
        $Act8004Model->get_hrwd($id);
        $Act8004Model->back_data_hd();
    }

    /**
     *圣诞节活动- 信息
     */
    public function hd8005Info($params){
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->back_data_hd();
        $Act8005Model->back_data();
        Master::back_data($this->uid,$Act8005Model->b_mol,'shop',$Act8005Model->back_data_shop());
        Master::back_data($this->uid,$Act8005Model->b_mol,'exchange',$Act8005Model->back_data_exchange());
    }
    /**
     *圣诞节活动
     */
    public function hd8005Paly($params){
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->play();
        $Act8005Model->back_data_hd();
    }
    /**
     *圣诞节活动十次
     */
    public function hd8005PalyTen($params){
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->play(10);
        $Act8005Model->back_data_hd();
    }

    /**
     * 圣诞节活动- 领取奖励
     * @param unknown_type $params
     */
    public function hd8005Rwd($params){
        $cons = Game::intval($params,'cons');
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->get_rwd($cons);
        $Act8005Model->back_data_hd();
    }

    /***
     * 圣诞节活动.排行
     */
    public function hd8005paihang() {
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->paihang();
    }

    /**
     * 圣诞节活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8005buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->shop_buy($id,$num);
        $Act8005Model->back_data();
    }

    /**
     * 圣诞节活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8005exchange($params){
        $id = Game::intval($params,'id');
        $Act8005Model = Master::getAct8005($this->uid);
        $Act8005Model->exchange($id);
    }

    /**
     *厨艺大赛- 信息
     */
    public function hd8006Info($params){
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->back_data_hd();
        $Act8006Model->back_data();
        Master::back_data($this->uid,$Act8006Model->b_mol,'shop',$Act8006Model->back_data_shop());
        Master::back_data($this->uid,$Act8006Model->b_mol,'exchange',$Act8006Model->back_data_exchange());
    }
    /**
     *厨艺大赛
     */
    public function hd8006Play($params){
    	$score = Game::intval($params,'score');
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->play(1, $score);
        $Act8006Model->back_data_hd();
    }
    /**
     *厨艺大赛十次
     */
    public function hd8006PlayTen($params){
    	$score = Game::intval($params,'score');
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->play(10, $score);
        $Act8006Model->back_data_hd();
    }

    /**
     * 厨艺大赛- 领取奖励
     * @param unknown_type $params
     */
    public function hd8006Rwd($params){
        $id = Game::intval($params,'id');
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->get_rwd($id);
        $Act8006Model->back_data_hd();
    }

    /***
     * 厨艺大赛.排行
     */
    public function hd8006paihang($params) {
        $type = Game::intval($params,'type');
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->paihang($type);
    }

    /**
     * 厨艺大赛 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8006buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->shop_buy($id,$num);
        $Act8006Model->back_data();
    }

    /**
     * 厨艺大赛 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8006exchange($params){
        $id = Game::intval($params,'id');
        $Act8006Model = Master::getAct8006($this->uid);
        $Act8006Model->exchange($id);
    }

    /**
     *珍绣坊- 信息
     */
    public function hd8007Info($params){
        $Act8007Model = Master::getAct8007($this->uid);
        $Act8007Model->back_data_hd();
        $Act8007Model->back_data();
    }

    /**
     *珍绣坊- 刷新
     */
    public function hd8007Refresh($params){
        $Act8007Model = Master::getAct8007($this->uid);
        $Act8007Model->back_data_hd(true);
    }

    /**
     *珍绣坊- 兑换
     */
    public function hd8007exchange($params){
    	$id = Game::arrayval($params,'ids');
        $Act8007Model = Master::getAct8007($this->uid);
        $Act8007Model->exchange($id);
    }

     /**
     *天赐抽卡数据
     */
    public function hd6242Info($params){
        $Act6242Model = Master::getAct6242($this->uid);
        $Act6242Model->back_data_hd();
        $Act6242Model->back_data();
    }

    /**
     *新春活动- 信息
     */
    public function hd8008Info($params){
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->back_data_hd();
        $Act8008Model->back_data();
        Master::back_data($this->uid,$Act8008Model->b_mol,'shop',$Act8008Model->back_data_shop());
        Master::back_data($this->uid,$Act8008Model->b_mol,'exchange',$Act8008Model->back_data_exchange());
    }

    /**
     *新春活动
     */
    public function hd8008Play($params){
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->play();
        $Act8008Model->back_data_hd();
    }

    /**
     *新春活动
     */
    public function hd8008Move($params){
    	$qizi = Game::strval($params,'qizi');
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->move($qizi);
        $Act8008Model->back_data_hd();
    }

    /**
     * 新春活动- 领取奖励
     * @param unknown_type $params
     */
    public function hd8008Rwd($params){
        $id = Game::intval($params,'id');
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->get_rwd($id);
        $Act8008Model->back_data_hd();
    }

    /***
     * 新春活动.排行
     */
    public function hd8008paihang($params) {
        $type = Game::intval($params,'type');
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->paihang($type);
    }

    /**
     * 新春活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8008buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->shop_buy($id,$num);
        $Act8008Model->back_data();
    }

    /**
     * 新春活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8008exchange($params){
        $id = Game::intval($params,'id');
        $Act8008Model = Master::getAct8008($this->uid);
        $Act8008Model->exchange($id);
        $Act8008Model->back_data_hd();
    }

    /**
     *情人节活动- 信息
     */
    public function hd8009Info($params){
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->back_data_hd();
        $Act8009Model->back_data();
        Master::back_data($this->uid,$Act8009Model->b_mol,'shop',$Act8009Model->back_data_shop());
        Master::back_data($this->uid,$Act8009Model->b_mol,'exchange',$Act8009Model->back_data_exchange());
    }
    /**
     *情人节活动
     */
    public function hd8009Play($params){
    	$score = Game::intval($params,'score');
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->play(1, $score);
        $Act8009Model->back_data_hd();
    }
    /**
     *情人节活动十次
     */
    public function hd8009PlayTen($params){
    	$score = Game::intval($params,'score');
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->play(10, $score);
        $Act8009Model->back_data_hd();
    }

    /**
     * 情人节活动- 领取奖励
     * @param unknown_type $params
     */
    public function hd8009Rwd($params){
        $id = Game::intval($params,'id');
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->get_rwd($id);
        $Act8009Model->back_data_hd();
    }

    /***
     * 情人节活动.排行
     */
    public function hd8009paihang($params) {
        $type = Game::intval($params,'type');
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->paihang($type);
    }

    /**
     * 情人节活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8009buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->shop_buy($id,$num);
        $Act8009Model->back_data();
    }

    /**
     * 情人节活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8009exchange($params){
        $id = Game::intval($params,'id');
        $Act8009Model = Master::getAct8009($this->uid);
        $Act8009Model->exchange($id);
    }

      /**
     *贵人令- 信息
     */
    public function hd8011Info($params){
        $Act8011Model = Master::getAct8011($this->uid);
        $Act8011Model->back_data_hd();
    }

    /**
     * 贵人令- 领取奖励
     * @param unknown_type $params
     */
    public function hd8011Rwd($params){
        $id = Game::intval($params,'id');
        $Act8011Model = Master::getAct8011($this->uid);
        $Act8011Model->get_rwd($id);
        $Act8011Model->back_data_hd();
    }

    /**
     * 贵人令- 购买等级
     * @param unknown_type $params
     */
    public function hd8011UpLevel($params){
        $num = Game::intval($params,'num');
        $Act8011Model = Master::getAct8011($this->uid);
        $Act8011Model->upLevel($num);
        $Act8011Model->back_data_hd();
    }

    /***
     * 贵人令.排行
     */
    public function hd8011paihang() {
        $Act8011Model = Master::getAct8011($this->uid);
        $Act8011Model->paihang();
    }

    
    /**
     *新贵人令- 信息
     */
    public function hd8016Info($params){
        $Act8016Model = Master::getAct8016($this->uid);
        $Act8016Model->back_data_hd();
    }

    /**
     * 新贵人令- 领取奖励
     * @param unknown_type $params
     */
    public function hd8016Rwd($params){
        $id = Game::intval($params,'id');
        $Act8016Model = Master::getAct8016($this->uid);
        $Act8016Model->get_rwd($id);
        $Act8016Model->back_data_hd();
    }

    /**
     * 新贵人令- 购买等级
     * @param unknown_type $params
     */
    public function hd8016UpLevel($params){
        $num = Game::intval($params,'num');
        $Act8016Model = Master::getAct8016($this->uid);
        $Act8016Model->upLevel($num);
        $Act8016Model->back_data_hd();
    }

    /***
     * 新贵人令.排行
     */
    public function hd8016paihang() {
        $Act8016Model = Master::getAct8016($this->uid);
        $Act8016Model->paihang();
    }

        /**
     * 三消活动- 信息
     */
    public function hd8018Info($params){
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->back_data_hd();
        $Act8018Model->back_data();
        Master::back_data($this->uid,$Act8018Model->b_mol,'shop',$Act8018Model->back_data_shop());
        Master::back_data($this->uid,$Act8018Model->b_mol,'exchange',$Act8018Model->back_data_exchange());
    }

    /**
     * 三消活动-使用体力药水
     */
    public function hd8018Recovery($params){
        $num = Game::intval($params,'num');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->recovery($num);
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动-消除
     */
    public function hd8018Play($params){
        $list = Game::intval($params,'list');
        $combo = Game::intval($params,'combo');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->play($list, $combo);
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动-开启下一关卡
     */
    public function hd8018Next($params){
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->startNext();
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动-失败
     */
    public function hd8018Fail($params){
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->pveFail();
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动-重置关卡
     */
    public function hd8018Reset($params){
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->resetChess();
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动-保存棋盘
     */
    public function hd8018Save($params){
        $chess = Game::arrayval($params,'chess');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->saveChess($chess);
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动- 领取积分奖励
     * @param unknown_type $params
     */
    public function hd8018Rwd($params){
        $id = Game::intval($params,'id');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->get_rwd($id);
        $Act8018Model->back_data_hd();
    }

    /**
     * 三消活动- 关卡排行榜
     * @param unknown_type $params
     */
    public function hd8018PvePaihang($params){
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->pvePaihang();
    }

    /***
     * 三消活动.排行
     */
    public function hd8018paihang($params) {
        $type = Game::intval($params,'type');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->paihang($type);
    }

    /**
     * 三消活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8018buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->shop_buy($id,$num);
        $Act8018Model->back_data();
    }

    /**
     * 三消活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8018exchange($params){
        $id = Game::intval($params,'id');
        $count = Game::intval($params,'count');
        $Act8018Model = Master::getAct8018($this->uid);
        $Act8018Model->exchange($id,$count);
        $Act8018Model->back_data_hd();
    }

     /**
     * 豆腐女孩活动- 信息
     */
    public function hd8022Info($params){
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->back_data_hd();
        $Act8022Model->back_data();
        Master::back_data($this->uid,$Act8022Model->b_mol,'shop',$Act8022Model->back_data_shop());
        Master::back_data($this->uid,$Act8022Model->b_mol,'exchange',$Act8022Model->back_data_exchange());
    }

    /**
     * 豆腐女孩活动-跳
     */
    public function hd8022Play($params){
        $jump = Game::intval($params,'jump');
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->play($jump);
        $Act8022Model->back_data_hd();
    }

    /**
     * 豆腐女孩活动- 领取积分奖励
     * @param unknown_type $params
     */
    public function hd8022Rwd($params){
        $id = Game::intval($params,'id');
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->get_rwd($id);
        $Act8022Model->back_data_hd();
    }

    /***
     * 豆腐女孩活动.排行
     */
    public function hd8022paihang($params) {
        $type = Game::intval($params,'type');
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->paihang($type);
    }

    /**
     * 豆腐女孩活动 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8022buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->shop_buy($id,$num);
        $Act8022Model->back_data();
    }

    /**
     * 豆腐女孩活动-购买次数
     */
    public function hd8022Recovery($params){
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->recovery();
        $Act8022Model->back_data_hd();
    }

    /**
     * 豆腐女孩活动 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8022exchange($params){
        $id = Game::intval($params,'id');
        $count = Game::intval($params,'count');
        $Act8022Model = Master::getAct8022($this->uid);
        $Act8022Model->exchange($id,$count);
        $Act8022Model->back_data_hd();
    }

    /**
     *海滩夺宝- 信息
     */
    public function hd8026Info($params){
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->back_data_hd();
        $Act8026Model->back_data();
        Master::back_data($this->uid,$Act8026Model->b_mol,'shop',$Act8026Model->back_data_shop());
        Master::back_data($this->uid,$Act8026Model->b_mol,'exchange',$Act8026Model->back_data_exchange());
    }

    /*
     * 海滩夺宝 - 更换宠物
     * */
    public function hd8026Select($params) {
        $id = Game::intval($params,'id');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->selectPid($id);
        $Act8026Model->back_data_hd();
    }

    /**
     *海滩夺宝-夺宝
     */
    public function hd8026Play($params){
    	$type = Game::intval($params,'type');
    	$score = Game::intval($params,'score');
    	$isSkill = Game::intval($params,'isSkill');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->play($type, $score, $isSkill);
        $Act8026Model->back_data_hd();
    }

    /**
     * 海滩夺宝- 领取日常奖励
     * @param unknown_type $params
     */
    public function hd8026Rwd($params){
        $id = Game::intval($params,'id');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->get_rwd($id);
        $Act8026Model->back_data_hd();
    }

    /**
     * 海滩夺宝- 领取任务奖励
     * @param unknown_type $params
     */
    public function hd8026TaskRwd($params){
        $id = Game::intval($params,'id');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->get_task_rwd($id);
        $Act8026Model->back_data_hd();
    }

    /***
     * 海滩夺宝.排行
     */
    public function hd8026paihang($params) {
        $type = Game::intval($params,'type');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->paihang($type);
    }

    /**
     * 海滩夺宝 - 商品购买
     * $params['id'] :  商品id
     */
    public function hd8026buy($params){
        $id = Game::intval($params,'id');
        $num = Game::intval($params,'num');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->shop_buy($id,$num);
        $Act8026Model->back_data();
    }

    /**
     * 海滩夺宝 - 兑换
     * $params['id'] :  商品id
     */
    public function hd8026exchange($params){
        $id = Game::intval($params,'id');
        $count = Game::intval($params,'count');
        $Act8026Model = Master::getAct8026($this->uid);
        $Act8026Model->exchange($id,$count);
    }

    /**
    *打月亮- 信息
    */
   public function hd8029Info($params){
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->getMoonInfo();
       $Act8029Model->back_data_hd();
       $Act8029Model->back_data();
       Master::back_data($this->uid,$Act8029Model->b_mol,'shop',$Act8029Model->back_data_shop());
       Master::back_data($this->uid,$Act8029Model->b_mol,'exchange',$Act8029Model->back_data_exchange());
   }

   /**
    *打月亮- 信息
    */
   public function hd8029GetShell($params){
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->getFreeShell();
       $Act8029Model->back_data_hd();
   }

   /*
    * 打月亮 - 开启月亮
    * */
   public function hd8029OpenMoon($params) {
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->openMoon();
       $Act8029Model->back_data_hd();
   }

   /**
    *打月亮-战斗
    */
   public function hd8029Play($params){
       $hit = Game::intval($params,'hit');
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->play($hit);
       $Act8029Model->back_data_hd();
   }

   /**
    *打月亮-十倍战斗
    */
   public function hd8029PlayTen($params){
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->playTen();
       $Act8029Model->back_data_hd();
   }

   /**
    * 打月亮- 领取日常奖励
    * @param unknown_type $params
    */
   public function hd8029Rwd($params){
       $id = Game::intval($params,'id');
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->get_rwd($id);
       $Act8029Model->back_data_hd();
   }

   /**
    * 打月亮- 领取好友赠送的炮弹
    * @param unknown_type $params
    */
   public function hd8029ShellRwd($params){
       $pos = Game::intval($params,'pos');
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->get_shell_rwd($pos);
       $Act8029Model->back_data_hd();
   }

   /***
    * 打月亮.排行
    */
   public function hd8029paihang($params) {
       $type = Game::intval($params,'type');
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->paihang($type);
   }

   /**
    * 打月亮- 总排行榜
    * @param unknown_type $params
    */
   public function hd8029AllPaihang($params){
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->allPaihang();
   }

   /**
    * 打月亮 - 商品购买
    * $params['id'] :  商品id
    */
   public function hd8029buy($params){
       $id = Game::intval($params,'id');
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->buyShells($id);
       $Act8029Model->back_data_hd();
   }

   /**
    * 打月亮 - 兑换
    * $params['id'] :  商品id
    */
   public function hd8029exchange($params){
       $id = Game::intval($params,'id');
       $count = Game::intval($params,'count');
       $Act8029Model = Master::getAct8029($this->uid);
       $Act8029Model->exchange($id,$count);
   }

}
