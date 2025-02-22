<?php
//日常任务
class DailyMod extends Base
{
	/**
	 * 获取任务完成奖励
	 */
	public function gettask($params){
		//任务档次ID
		$id = Game::intval($params,'id');
		
		//日常任务类
		$Act35Model = Master::getAct35($this->uid);
		$Act35Model->task_rwd($id);
		
		//咸鱼日志
        Common::loadModel('XianYuLogModel');
        $UserModel = Master::getUser($this->uid);
        XianYuLogModel::daily($UserModel->info['platform'],$this->uid,$id,1);
	}
	
	/**
	 * 获取活跃值档次奖励
	 */
	public function getrwd($params){
		//活跃值奖励档次ID
		$id = Game::intval($params,'id');
		
		//日常任务类
		$Act35Model = Master::getAct35($this->uid);
		$Act35Model->act_rwd($id);
        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        $UserModel = Master::getUser($this->uid);
        XianYuLogModel::daily($UserModel->info['platform'],$this->uid,$id,2);
	}

	public function answer($params){
        $id = Game::intval($params,'id');

        $Act6106Model = Master::getAct6106($this->uid);
        $Act6106Model -> answer($id);
    }
}









