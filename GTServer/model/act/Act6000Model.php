<?php
require_once "ActBaseModel.php";
/*
 *  特殊关卡
 */
class Act6000Model extends ActBaseModel
{
	public $atype = 6000;//活动编号

	public $comment = "门客特殊关卡信息保存";
	public $b_mol = "scpoint";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'scpoint' => array(),
	);

	public function isOver($id){
		$item = Game::getcfg_info('hero_pve',$id);
		if ($item == null){
			return false;
		}
		$list = $this->info['scpoint'];		
		foreach($list as $v){
			if ($v['id'] == $id && $v['type'] == $item['type'] && $v['roleid'] == $item['roleid']){
				return true;
			}
		}
		return false;
	}

    public function do_save($id, $type, $roleid){
    	$list = $this->info['scpoint'];
    	$isFind = false;
    	for ($i = 0; $i < count($list); $i++){
			if (stripos($id, "jiban") === 0) {
				if ($list[$i]['type'] == $type && $list[$i]['roleid'] == $roleid){
					if (empty($list[$i]['jbs'])){
						$list[$i]['jbs'] = array($id);
					}
					else if (!in_array($id, $list[$i]['jbs'])){
						$list[$i]['jbs'][]= $id;
					}
					$isFind = true;
				}
			}else {
				if($list[$i]['id'] == $id){
					$isFind = true;
				}
			}
    	}
    	if ($isFind == false){
            if (stripos($id, "jiban") === 0) {
                $list[] = array('id' => 0, 'type'=>$type, 'roleid'=>$roleid, 'jbs'=>array($id));
            }else {
				// $Act35Model = Master::getAct35($this->uid);
				// $Act35Model->do_act(17,1);
                $list[] = array('id'=>$id, 'type'=>$type, 'roleid'=>$roleid, 'jbs'=>array());
            }
    	}
		$this->info['scpoint'] = $list;
		
		//主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(120, count($this->info['scpoint']));

        $this->save();
	}

    public function make_out(){
        $this->outf = $this->info['scpoint'];
    }
	
}
