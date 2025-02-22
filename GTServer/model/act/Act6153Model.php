<?php
require_once "ActBaseModel.php";
/*
 * 主角头像
 */
class Act6153Model extends ActBaseModel
{
	public $atype = 6153;//活动编号
	
	public $comment = "分享系统";
    public $b_mol = "fuli";//返回信息 所在模块
    public $b_ctrl = "share";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'shizhuang' => 0,
        'lastTime' => 0,
        'heroids' => array(),
        'wifeids' => array(),
    );

    public function share($id)
    {
        $info = $this->info;
        if ($info['lastTime'] < Game::get_week_0()){
            $info['shizhuang'] = 0;
            $info['lastTime'] = Game::get_now();
        }
        if ($id > 2000){
            $wifeModel = Master::getWife($this->uid);
            $wifeModel->check_info($id - 2000);
            Master::add_item($this->uid,KIND_ITEM, 1, Game::getcfg_param("share_gold"));
            $info['heroids'][] = $id - 2000;
        }
        else if ($id > 1000){
            $heroModel = Master::getHero($this->uid);
            $heroModel->check_info($id - 1000);
            Master::add_item($this->uid,KIND_ITEM, 1, Game::getcfg_param("share_gold"));
            $info['heroids'][] = $id - 1000;
        }
        else {
            switch ($id){
                case 1:
                    if ($this->info["shizhuang"] == 1){
                        return;
                    }
                    Master::add_item($this->uid,KIND_ITEM, 1, Game::getcfg_param("share_gold"));
                    $this->info['shizhuang'] = 1;
                    break;
            }
        }
        $this->info = $info;
        $this->save();
    }

}

