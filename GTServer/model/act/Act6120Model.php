<?php
require_once "ActBaseModel.php";
/*
 * 选择门客
 */
class Act6120Model extends ActBaseModel
{
	public $atype = 6120;//活动编号
	
	public $comment = "选择门客";
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "heroShow";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'id' => 1,
    );

    /*
     * 改变展示伙伴
     */
    public function changeHero($id){
        /*if ($id >= 200){
            $id1 = $id % 200;
            $WifeModel = Master::getWife($this->uid);
            $WifeModel->check_info($id1);
        }
        else {
            $HeroModel = Master::getHero($this->uid);
            $HeroModel->check_info($id);
        }*/
        if($id < 1 || $id > 6){
            return;
        }
        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($id);
        $this->info['id'] = $id;
        $this->save();
    }

}














