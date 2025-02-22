<?php
require_once "ActBaseModel.php";
/*
 * 御膳房类
 */
class Act6101Model extends ActBaseModel
{
	public $atype = 6101;//活动编号
	
	public $comment = "御膳房煮饭";
	public $b_mol = "kitchen";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//

	);
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		$outf = array();
		$in_wifes = array(); //正在学习中的英雄ID列表
		foreach ($this->info as $id => $dmsg){
			$h_msg = array(
				'id' => $id,
				'wid' => $dmsg['wid'],
				'itemId' => $dmsg['itemId'],
				'cd' => array(
					'next' => Game::dis_over($dmsg['over']),
					'label' => 'kitchen',
				),
			);
			$in_wifes[$dmsg['wid']] = 1;
			$outf[] = $h_msg;
		}
		$this->outf = $outf;
		$this->wids = $in_wifes;//开始学习判定有没有在学习中用
	}
	
	/*
	 * 开始学习
	 */
	public function start($id,$wid,$itemId){
        $WifeMode = Master::getWife($this->uid);
        //门客ID合法
        $wifeInfo = $WifeMode->check_info($wid);
		//这个红颜 是不是正在煮饭中
		if ($this->wids[$wid]){
			Master::error(KITCHEN_WIFE_FOOD);
		}
		//这个火炉 有没有人
		if (isset($this->info[$id])
		&& $this->info[$id]['wid'] > 0){
			Master::error(KITCHEN_STOVE_IS_TAKEN);
		}

		$kit_wife = Game::getcfg_info("kitchen_wife", $wid);
		if (!in_array($itemId, $kit_wife['kitchenid']))return;

		$kit = Game::getcfg_info("kitchen", $itemId);

        if ($wifeInfo['love'] < $kit['intmin'] || $wifeInfo['flower'] < $kit['intmax']){
            Master::error(KIT_LIMIT_ERROR);
        }

        $ItemModel = Master::getItem($this->uid);
        $UserModel = Master::getUser($this->uid);
		foreach ($kit['fooditemid'] as $foodid){
            if ($ItemModel->sub_item($foodid, 1,true)){
                Master::sub_item($this->uid, KIND_ITEM, $foodid, 1);
            }else {
                $shop = Game::getcfg("kitchen_shop");
                foreach($shop as $item){
                    if ($item['itemid'] == $foodid){
                        if ($UserModel->info['level'] < $shop['level']){
                            Master::sub_item($this->uid, KIND_ITEM, $foodid, 1);
                            return;
                        }
                        Master::sub_item($this->uid, KIND_ITEM, 3, $item['cost']);
                        break;
                    }
                }
            }
        }
		
		//书桌ID 超上限
		$Act6100Model = Master::getAct6100($this->uid);
		$Act6100Model->click_id($id);

        //存数据
        $Act6100Model = Master::getAct6100($this->uid);
        $Act6100Model->addOver();

		//开始学习
		$this->info[$id] = array(
			'wid' => $wid,	//门客ID 0 没人
			'itemId' => $itemId, //消耗的物品id
			'over' => Game::get_over($kit['time']),//下课时间
		);

        $this->save();

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(50,1);
        $Act39Model->task_refresh(50);

		//限时-御膳房烹饪次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6179',1);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(10, 1);
	}
    /*
     * 一键开始学习
     */
    public function allstart($arr){
        $WifeMode = Master::getWife($this->uid);
        $num = 0;
        foreach ($arr as $k=>$v){
            $id = $v['id'];
            $wid = $v['wid'];
            $itemId = $v['itemId'];
            //门客ID合法
            $wifeInfo = $WifeMode->check_info($wid);
            //这个红颜 是不是正在煮饭中
            if ($this->wids[$wid]){
                Master::error(KITCHEN_WIFE_FOOD);
            }
            //这个火炉 有没有人
            if (isset($this->info[$id])
                && $this->info[$id]['wid'] > 0){
                Master::error(KITCHEN_STOVE_IS_TAKEN);
            }
            //书桌ID 超上限
            $Act6100Model = Master::getAct6100($this->uid);
            $Act6100Model->click_id($id);
            $kit_wife = Game::getcfg_info("kitchen_wife", $wid);
            if (!in_array($itemId, $kit_wife['kitchenid']))Master::error(KIT_LIMIT_ERROR);

            $kit = Game::getcfg_info("kitchen", $itemId);

            if ($wifeInfo['love'] < $kit['intmin'] || $wifeInfo['flower'] < $kit['intmax']){
                Master::error(KIT_LIMIT_ERROR);
            }

            $ItemModel = Master::getItem($this->uid);
            $UserModel = Master::getUser($this->uid);
            foreach ($kit['fooditemid'] as $foodid){
                if ($ItemModel->sub_item($foodid, 1,true)){
                    Master::sub_item($this->uid, KIND_ITEM, $foodid, 1);
                }else {
                    $shop = Game::getcfg("kitchen_shop");
                    foreach($shop as $item){
                        if ($item['itemid'] == $foodid){
                            if ($UserModel->info['level'] < $shop['level']){
                                Master::sub_item($this->uid, KIND_ITEM, $foodid, 1);
                                return;
                            }
                            Master::sub_item($this->uid, KIND_ITEM, 3, $item['cost']);
                            break;
                        }
                    }
                }
                $num++;
                //开始学习
                $this->info[$id] = array(
                    'wid' => $wid,	//门客ID 0 没人
                    'itemId' => $itemId, //消耗的物品id
                    'over' => Game::get_over($kit['time']),//下课时间
                );
            }
        }
        //存数据
        $Act6100Model = Master::getAct6100($this->uid);
        $Act6100Model->addOver($num);

        $this->save();
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(50,$num);
        $Act39Model->task_refresh(50);

        //限时-御膳房烹饪次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6179',$num);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(10, $num);
        return $num;
    }
	/*
	 * 完成学习
	 */
	public function over($id){
		//这个火炉 有没有人
		if (empty($this->info[$id]['wid'])){
			Master::error(KITCHEN_STOVE_UN_TAKEN);
		}
		
		//时间完成
		if (!Game::is_over($this->info[$id]['over'])){
			Master::error(KITCHEN_NO_TIME_YET);
		}

		//红颜完成煮饭操作
		$this->_over_study($id);
		
		//下课
		$this->info[$id] = array(
			'wid' => 0,	//红颜ID 0 没人
			'itemId' => 0, //消耗的物品id
			'over' => 0,//下课时间
		);
		$this->save();


        //舞狮大会 - 完成烹饪次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(17,1);
	}
	
	/*
	 * 一键完成煮饭
	 */
	public function allover(){
		$over_num = 0;//完成煮饭的红颜数量
		//遍历火炉
		foreach ($this->info as $id => $dsk){
			if ($dsk['wid'] > 0	&& Game::is_over($dsk['over'])){
				$this->_over_study($id);
				$over_num ++;
				
				//下课
				$this->info[$id] = array(
					'wid' => 0,	//红颜ID 0 没人
					'itemId' => 0, //消耗的物品id
					'over' => 0,//下课时间
				);
			}
		}
		$this->save();
		if (empty($over_num)){
			Master::error(KITCHEN_ALL_WIFE_NO_TIME_YET);
		}


	}

	/*
	 * 一个英雄
	 * 完成学习
	 */
	private function _over_study($id){

	    $dsk = $this->info[$id];
	    if (empty($dsk))return;

        $kit = Game::getcfg_info("kitchen", $dsk['itemId']);
        $exp = Game::getcfg_param("kitchen_exp");
        $exp = floor($kit['time'] / 1800) * $exp;
        $Act6104Model = Master::getAct6104($this->uid);
        $Act6104Model->addExp($exp);

		//记录做过的菜
	    $Act6103Model = Master::getAct6103($this->uid);
		$Act6103Model->addFood($dsk['itemId']);

        $lvSys = Game::getcfg_info('school_level', $Act6104Model->info['level']);
        $bei = 1;
        if ($lvSys['crit'] > rand(0, 10000)){
            $bei = 2;
        }

	    $item = array('id' => $kit['itemid'], 'count' => $bei);
	    Master::add_item2($item);
	}


}
