<?php
//签到
class TaskMod extends Base
{
	/**
	 * 做主线任务
	 * @param $params
	 * $params['id'] : 主线任务id
	 */
	public function taskdo($params){
		$id = Game::intval($params,'id');

        $guanq = Game::get_peizhi('gq_status');

		if( !empty($guanq['mainTask']) && $id > $guanq['mainTask']){
            Master::error(PARAMS_ERROR.$id);
        }

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_do($id);
	}
	
	

	
	
}









