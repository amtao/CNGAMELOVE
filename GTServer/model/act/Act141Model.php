<?php
require_once "ActBaseModel.php";
/*
 * 新年活动 - 仓库
 */
class Act141Model extends ActBaseModel
{
    public $atype = 141;//活动编号
    public $b_mol = "newyear";//返回信息 所在模块
    public $b_ctrl = "bag";//返回信息 所在控制器
    public $comment = "新年活动 - 仓库";
    public $hd_id = "huodong_298";
    public $item_type = "hd298";
    public $hd_cfg;

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * 'id' => num
         * */
    );
    /*
     * 扣除仓库道具
     * */
    public function sub($id,$num=1) {
        if($this->info[$id]<$num){
            Master::error(USER_ITEMS_SHORT);
        }
        $this->info[$id] -= $num;
        $this->save();
        Game::cmd_flow(6,$id,-$num,$this->info[$id]);
    }

    /*
     * 添加仓库道具
     * */
    public function add($id,$num=1) {
        if(empty($num)){
            Master::error(USER_ITEMS_NUM_ERROR);
        }
        if(empty($this->info[$id])){
            $this->info[$id] = 0;
        }
        $this->info[$id] += $num;
        $this->save();
        Game::cmd_flow(6,$id,$num,$this->info[$id]);
    }

    public function make_out(){
        if(empty($this->info)){
            $outof = array();
        }else{
            foreach($this->info as $id => $num){
                if($num >0){
                    $outof[] = array('id'=>$id,'count'=> $num,'kind'=>11);
                }
            }
        }
        //默认输出直接等于内部存储数据
        $this->outf = empty($outof) ? array() : $outof;
    }
}
