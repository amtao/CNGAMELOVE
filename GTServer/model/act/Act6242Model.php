<?php
require_once "ActHDBaseModel.php";

/*
 * 天赐抽卡
 */
class Act6242Model extends ActHDBaseModel
{
    public $atype = 6242;//活动编号
    public $comment = "天赐抽卡";
    public $b_mol = "card";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_id = 'huodong_6242';//活动配置文件关键字-编号

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'drawtimes'      => 0, //抽取次数
        'rwdnum'      => 0, //领奖次数
    );
    public function back_data() {
	    Master::back_data($this->uid,$this->b_mol,'act',$this->info);
    }
    public function back_data_hd() {
	    Master::back_data($this->uid,$this->b_mol,'cfg',$this->hd_cfg);
	}
    

    public function draw_check($poolid){
         //活动已结束
        if(empty($this->hd_cfg['poolid']) ){
            Game::other_debug("act 6242".json_encode($this->hd_cfg));
            Master::error(ACTHD_OVERDUE."1");
        }
        //奖池是否符合
        if ($this->hd_cfg['poolid'] !=$poolid){
            Master::error(ACTHD_OVERDUE."2");
        }
         //活动已结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE."3");
        }
         
    }
    /*
     * 十次十连特殊奖励
     * poolid  奖池id
     * 返回 奖励卡牌id,0为没有特殊奖励
     * */
    public function mult_rwd($poolid){
       
        //活动已结束
        if( $this->get_state() == 0){
            //Master::error(ACTHD_OVERDUE);
            return 0;
        }

        //次数限制
        if(!empty($this->hd_cfg['surelimit'])){
            if ($this->info['rwdnum'] >=$this->hd_cfg['surelimit']){
                //Master::error(REWARD_IS_GET);
                return 0;
            }
        }

        //奖池是否符合

        if ($this->hd_cfg['poolid'] !=$poolid){
            return 0;
        }

        //是否满足条件
        $this->info['drawtimes']+=1;
        
        //echo json_encode($this->hd_cfg);
        //echo "req".$this->hd_cfg['surereq'];
        if ($this->info['drawtimes']< $this->hd_cfg['surereq']){
            $this->back_data();
            //Master::back_data($this->uid,$this->b_mol,'act',$this->info);
            $this->save();
            return 0;
        }
     
        $this->info['drawtimes']-=$this->hd_cfg['surereq'];
        $this->info['rwdnum']+=1;
        $this->back_data();
        //Master::back_data($this->uid,$this->b_mol,'act',$this->info);
        $this->save();
        //数据返回
        //Master::back_data($this->uid,$this->b_mol,'act',$this->info);
        //$this->back_data();
        return $this->hd_cfg['surecard'];
        
    }


}
