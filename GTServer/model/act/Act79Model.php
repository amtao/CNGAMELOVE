<?php
require_once "ActBaseModel.php";
/*
 * 国子监-送礼
 */
class Act79Model extends ActBaseModel
{
    public $atype = 79;//活动编号

    public $comment = "国子监-送礼";
    public $b_mol = "gzj";//返回信息 所在模块
    public $b_ctrl = "gifts";//返回信息 所在控制器

    /*
     * 初始化结构体
     */
//    public $_init =  array(//
//        'sid' => time
//    );

    /**
     * 构造输出函数
     */
    public function make_out()
    {
        $outf = array();
        if(!empty($this->info)){
            foreach ($this->info as $id => $time){
                switch ($id){
                    case 1:
                        $outf['primary'] = array(
                            'next' => Game::dis_over($time),
                            'label' => 'gzj_primary',
                        );
                        break;
                    case 2:
                        $outf['middle'] = array(
                            'next' => Game::dis_over($time),
                            'label' => 'gzj_middle',
                        );
                        break;
                }
            }
        }
        $this->outf = $outf;
    }

    /**
     * 送礼
     * @param $id
     */
    public function sendGift($id){
        $bool = false;
        if(empty($this->info[$id]) || Game::is_over($this->info[$id])){
            switch ($id){
                case 1:
                    $this->info[$id] = Game::get_now()+24*3600;
                    $bool = true;
                    break;
                case 2:
                    $this->info[$id] = Game::get_now()+48*3600;
                    $bool = true;
                    break;
                case 3:
                    $bool = false;
                    break;
                default:
                    Master::error(GZJ_THE_GIFT_HAVE_PROBLEM);
                    break;
            }
        }

        $cfg = Game::getcfg_info('gzj_bribery',$id,GZJ_THE_GIFT_HAVE_PROBLEM);
        if($bool == true){
            $this->save();
        }else{
            //需要付出的代价
            $itemid = $cfg['need']['id'];
            $count = $cfg['need']['count'];
            $kind = empty($cfg['need']['kind']) ? 1 : $cfg['need']['kind'];
            Master::sub_item($this->uid,$kind,$itemid,$count);
        }
        //获得的奖励 行贿只获得人气？
        $num = rand(1,100);
        $pop = 0;
        foreach ($cfg['popular'] as $val){
            if($val['rand'][0] <= $num && $val['rand'][1]>=$num){
                $pop=$val['pop'];
                break;
            }
        }
        return $pop;
    }

}
