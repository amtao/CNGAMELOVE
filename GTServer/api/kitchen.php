<?php
//学院模块
class KitchenMod extends Base
{
	/*
	 * 购买火炉
	 */
	public function buyStove($params){
		//御膳房类
		$Act6100Model = Master::getAct6100($this->uid);
		//加火炉
		$Act6100Model->add_stove();

	}
	
	/*
	 * 开始学习
	 * wid 门客ID
	 * id 座位ID
	 */
	public function food($params){
		//参数 红颜ID
		$wid = Game::intval($params,'wid');
		//参数 座位ID
		$id = Game::intval($params,'id');
		//参数 物品ID
		$itemId = Game::intval($params, 'itemId');
		
		//御膳房类
		$Act6101Model = Master::getAct6101($this->uid);
		$Act6101Model->start($id,$wid,$itemId);
	}
	
	/*
	 * 完成煮饭
	 * id 座位ID
	 */
	public function over($params){
		//参数 座位ID
		$id = Game::intval($params,'id');
		
		//书院学习类
		$Act6101Model = Master::getAct6101($this->uid);
		$Act6101Model->over($id);
	}
	
	/*
	 * 完成煮饭
	 * 一键完成
	 */
	public function allover($params){
        Master::vip_limit($this->uid,4,'LOOK_FOR_VIP_LEVEL_SHORT');
		//书院学习类
		$Act6101Model = Master::getAct6101($this->uid);
		$Act6101Model->allover();
	}

	/*
	 * 完成煮饭
	 * 设置
	 */

	public function set($params){
        $userModel = Master::getUser($this->uid);
        if($userModel->info['vip'] < 3){
            Master::error(LOOK_FOR_VIP_LEVEL_SHORT);//提示VIP等级不足
        }
        //书院学习类
        $Act6102Model = Master::getAct6102($this->uid);
        $Act6102Model->set($params['wids']);
    }

    /*
	 * 一键学习
	 * 信息
	 */

    public function setinfo($params){
        $userModel = Master::getUser($this->uid);
        if($userModel->info['vip'] < 3){
            Master::error(LOOK_FOR_VIP_LEVEL_SHORT);//提示VIP等级不足
        }
        //御膳房学习类
        $Act6102Model = Master::getAct6102($this->uid);
        $Act6102Model->back_data();
    }

    /*
     * 开始学习
     * 一键开始
     */
    public function allstart($params){
        Master::vip_limit($this->uid,5,'LOOK_FOR_VIP_LEVEL_SHORT');
        $arr = Game::arrayval($params,'arr');
        if (empty($arr)){
            Master::error(PARAMS_ERROR);
        }
        //御膳房类
        $Act6101Model = Master::getAct6101($this->uid);
        $Act6101Model->allstart($arr);
        //$userModel = Master::getUser($this->uid);
        //if($userModel->info['vip'] < 3){
        //    Master::error(LOOK_FOR_VIP_LEVEL_SHORT);//提示VIP等级不足
        //}
        ////书院学习类
        //$Act6101Model = Master::getAct6101($this->uid);
        //$num = $Act6101Model->allstart();
        //if($num > 0){
            // //成就更新
            // $Act36Model = Master::getAct36($this->uid);
            // $Act36Model->add(12,$num);

            //主线任务
            // $Act39Model = Master::getAct39($this->uid);
            // $Act39Model->task_add(30,$num);

            // //活动消耗 - 限时书院学习
            // $HuodongModel = Master::getHuodong($this->uid);
            // $HuodongModel->xianshi_huodong('huodong211',$num);
//        }
    }

    /*
     * 开始学习
     * 一键开始
     */
    public function buyFood($params){
        //参数 座位ID
        $id = Game::intval($params,'id');
        $count = Game::intval($params,'count');
        if ($count == 0 || $id == 0)return;

        $shop = Game::getcfg_info("kitchen_shop", $id);
        $UserModel = Master::getUser($this->uid);
        if ($UserModel->info['level'] < $shop['level'])return;

        $zy_count = $shop['cost'] * $count;

        //如果粮草不够
        if ($UserModel->info['food'] < $zy_count){
            $count = floor($zy_count/$shop['cost']);
            $zy_count = $count * $shop['cost'];
        }

        //减去粮草
        Master::sub_item($this->uid,KIND_ITEM,3, $zy_count);

        Master::add_item($this->uid,KIND_ITEM, $shop['itemid'], $count);
    }
}
