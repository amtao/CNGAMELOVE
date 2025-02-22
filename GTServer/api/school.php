<?php
//学院模块
class SchoolMod extends Base
{
	/*
	 * 购买学位
	 */
	public function buydesk($params){
		//书院座位类
		$Act15Model = Master::getAct15($this->uid);
		//加座位
		$Act15Model->add_desk();
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_refresh(36);
	}
	
	/*
	 * 开始学习
	 * hid 门客ID
	 * id 座位ID
	 */
	public function start($params){
		//参数 门客ID
		$hid = Game::intval($params,'hid');
		//参数 座位ID
		$id = Game::intval($params,'id');
		
		$HeroModel = Master::getHero($this->uid);
		//门客ID合法
		$HeroModel->check_info($hid);
		
		//书院学习类
		$Act16Model = Master::getAct16($this->uid);
		$Act16Model->start($id,$hid);

        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(11,1);
	
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(30,1);
		
		//活动消耗 - 限时书院学习
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong211',1);

        //舞狮大会 - 书院学习
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(11,1);
	}

    /*
     * 一键学习
	 * hid 门客ID
	 * id 座位ID
     */
    public function yjStart($params){
        Master::vip_limit($this->uid,5,'LOOK_FOR_VIP_LEVEL_SHORT');
        $arr = Game::arrayval($params,'arr');
        if (empty($arr)){
            Master::error(PARAMS_ERROR);
        }
        $Act16Model = Master::getAct16($this->uid);
        $num = $Act16Model->allstart($arr);
        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(11,$num);

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(30,$num);

        //活动消耗 - 限时书院学习
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong211',$num);

        //舞狮大会 - 书院学习
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(11,$num);
    }
	
	/*
	 * 完成学习
	 * id 座位ID
	 */
	public function over($params){
		//参数 座位ID
		$id = Game::intval($params,'id');
		
		//书院学习类
		$Act16Model = Master::getAct16($this->uid);
		$Act16Model->over($id);
	}
	
	/*
	 * 完成学习
	 * 一键完成
	 */
	public function allover(){
        Master::vip_limit($this->uid,4,'LOOK_FOR_VIP_LEVEL_SHORT');
		//书院学习类
		$Act16Model = Master::getAct16($this->uid);
		$Act16Model->allover();
	}

	/*
	 * 一键学习
	 * 设置
	 */

	public function set($params){
        $userModel = Master::getUser($this->uid);
        if($userModel->info['vip'] < 3){
            Master::error(LOOK_FOR_VIP_LEVEL_SHORT);//提示VIP等级不足
        }
        //书院学习类
        $Act46Model = Master::getAct46($this->uid);
        $Act46Model->set($params['hids']);
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
        //书院学习类
        $Act46Model = Master::getAct46($this->uid);
        $Act46Model->back_data();
    }

    /*
     * 开始学习
     * 一键开始
     */
    public function allstart($params){
        $userModel = Master::getUser($this->uid);
        if($userModel->info['vip'] < 3){
            Master::error(LOOK_FOR_VIP_LEVEL_SHORT);//提示VIP等级不足
        }
        //书院学习类
        $Act16Model = Master::getAct16($this->uid);
        $num = $Act16Model->allstart();
        if($num > 0){
            //主线任务
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(30,$num);

            //活动消耗 - 限时书院学习
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->xianshi_huodong('huodong211',$num);
        }
    }

    //书院加速完成cd
    public function speedFinish($params){
        $id = Game::intval($params,"id");
        $Act16Model = Master::getAct16($this->uid);
        $Act16Model->speedFinish($id);
    }
	
}
