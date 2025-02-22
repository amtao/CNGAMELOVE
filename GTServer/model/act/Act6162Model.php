<?php
require_once "ActBaseModel.php";
/*
 * 感恩节活动 - 仓库
 */
class Act6162Model extends ActBaseModel
{
    public $atype = 6162;//活动编号
    public $b_mol = "yyhuodong";//返回信息 所在模块
    public $b_ctrl = "bag";//返回信息 所在控制器
    public $comment = "皇子应援活动 - 仓库";
    public $hd_id = "huodong_6136";
    public $item_type = "hd6136";
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

    /*
     * 使用活动道具
     * */
    public function sub_hdItems($id,$num=1) {
        $itemcfg_info = Game::getcfg_info('item',$id);
        $type = $itemcfg_info['type'][0];
        if($type != $this->item_type){
            Master::error(ACT_HD_NO_ACT_ITEM);
        }
        self::sub($id,$num);

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
