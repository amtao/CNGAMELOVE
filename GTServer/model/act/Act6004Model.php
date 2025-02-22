<?php
require_once "ActBaseModel.php";
/*
 *  邮件
 */
class Act6004Model extends ActBaseModel
{
	public $atype = 6004;//活动编号

	public $comment = "邮件";
	public $b_mol = "feige";//返回信息 所在模块
	public $b_ctrl = "feige";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);

	public function addReward($id){
        $email_list = Game::getcfg("emailItem");
        $group = null;
        if (stripos($group, "e") != 0) {
            Master::error(STORY_DATA_EMAIL_FIND);
        }
        foreach ($email_list as $v){
            if ($v['award1'] == $id || $v['award2'] == $id){
                $group = Game::getcfg_info("emailGroup", $v['group']);
                break;
            }
        }
        if (empty($group)){
            Master::error(STORY_DATA_EMAIL_FIND);
        }

        if (!$this->isOpen($group)){
            Master::error(STORY_DATA_EMAIL_NOT_OPEN);
        }

        $g = $this->info[$group['id']];
        if (empty($g)){
            $g = array('id'=> $group['id'], 'select' => array());
        }

        if (!empty($g['select']) && count($g['select']) > 0 && in_array($id, $g['select'])){
            Master::error(STORY_DATA_EMAIL_SELECT);
        }

        $g['select'][] = $id;

        $this->info[$group['id']] = $g;
        $this->save();
    }

    private function isOpen($group){
	    switch ($group["type"]){
            case 1:
                $userModel = Master::getUser($this->uid);
                return $userModel->info['level'] >= $group['param'];
            case 2:
                $Act6001Model = Master::getAct6001($this->uid);
                if ($group['fromtype'] == 1){
                    return $Act6001Model -> getHeroJB($group['param']);
                }
                else if ($group['fromtype'] == 2){
                    return $Act6001Model -> getWifeJB($group['param']);
                }
                break;
            case 3:
                $UserModel = Master::getUser($this->uid);
                $smap_cfg = Game::getcfg_info('pve_smap',intval($UserModel->info['smap']) + 1);
                return intval($smap_cfg['mmap']) > $group['param'];
            case 4:
                $Act6000Model = Master::getAct6000($this->uid);
                return $Act6000Model->isOver($group['param']);
            case 5:
                $Act39Model = Master::getAct39($this->uid);
                return $Act39Model->info['id'] > $group['param'];
        }
        return true;
    }


}
