<?php
require_once "ActBaseModel.php";
/*
 * 排行膜拜
 */
class Act17Model extends ActBaseModel
{
    public $atype = 17;//活动编号

    public $comment = "排行膜拜";
    public $b_mol = "ranking";//返回信息 所在模块
    public $b_ctrl = "mobai";//返回信息 所在控制器

    /*
     * 初始化结构体
     */
    public $_init =  array(//排行膜拜信息
        1 => 0,   //势力榜
        2 => 0,  //关卡榜
        3 => 0,	//好感榜
        4 => 0, //跨服势力榜
        5 => 0, //跨服联盟榜
    );

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = array(
            'shili' => $this->info[1],
            'guanka' => $this->info[2],
            'love' => $this->info[3],
            'shiliKua' => isset($this->info[4]) ? $this->info[4] : 0,
            'clubKua' => isset($this->info[5]) ? $this->info[5] : 0,
        );
    }

    /**
     * @param $type 1:势力榜 2:关卡榜 3:好感榜
     */
    public function mobai($type){
        if (!isset($this->_init[$type])) {
            Master::error(REANK_WORSHIP_COMPLETE);
        }
        if(isset($this->info[$type]) && $this->info[$type] != 0){
            Master::error(REANK_WORSHIP_COMPLETE);
        }

        if (in_array($type, array(4, 5))) {
            //加政绩
            Master::add_item($this->uid,KIND_ITEM,5,rand(1, 100));
        } else {
            $UserModel = Master::getUser($this->uid);
            $guan_cfg = Game::getcfg_info('guan',$UserModel->info['level']);
            //加元宝
            Master::add_item($this->uid,KIND_ITEM,1,$guan_cfg['qingAn']);
            //神迹
//
//            $Act65Model = Master::getAct65($this->uid);
//            if ($Act65Model->rand(5)){
//                //触发神迹: 5.天赐元宝
//                Master::add_item($this->uid,KIND_ITEM,1,100);
//            }
        }

        $this->info[$type] = 1;
        $this->save();
    }

}
