<?php
require_once "ActBaseModel.php";
/**
 * 数据保存
 */
class Act99Model extends ActBaseModel
{
	public $atype = 99;//活动编号
	
	public $comment = "数据记录";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器

	public $_init =  array(
        'ep' => array(
            'e1' => 0,  //属性
            'e2' => 0,  //属性
            'e3' => 0,  //属性
            'e4' => 0,  //属性
        ),
        'alllove' => 0,  //总亲密
	);

    public function setep($eps,$info = ''){
        //之前总势力
        $old = array_sum($this->info['ep']);
        //现在总势力
        $new = array_sum($eps);
        //改变值
        $add = $new - $old;
		
        //是否改变
        if ($add == 0){
            return;
        }
        
        //---------业务需求加代码处-----------
        //增幅排行榜
        //$Act202Model = Master::getAct202($this->uid);
       // $Act202Model->add($add);

        //其他业务需求
		
        /*
        //势力改变通用弹窗
        $win = array(
            'old' => $old,
            'new' => $new,
        );
        Master::$bak_data['a']["msgwin"]["shili"] = $win;
        */
        //---------业务需求加代码处-----------
       
        
        //势力流水
        Game::cmd_flow(7,1,$add,$new);
        $this->info['ep'] = $eps;
        $this->save();
    }

    public function love($love){
        $this->info['alllove'] = $love;
        $this->save();
    }


    /*
     * 返回活动信息
     * 使这个函数 无效
     */
    public function back_data(){
        return;
    }
}
















