<?php
require_once "ActBaseModel.php";
/*
 * 红颜转世
 */
class Act47Model extends ActBaseModel
{
	public $atype = 47;//活动编号
	
	public $comment = "红颜-转世";
    public $b_mol = "wife";//返回信息 所在模块
    public $b_ctrl = "transferList";//返回信息 所在控制器

    public $_init = array();

    public function trans($wid){
        //判断是否拥有该红颜
        $WifeModel = Master::getWife($this->uid);
        if(empty($WifeModel->info[$wid])){
            Master::error(WIFE_NOT_HAVE_THIS);
        }
        if(!empty($this->info[$wid])){
            Master::sub_item($this->uid,KIND_ITEM,1,1);
        }
        $this->info[$wid] = empty($this->info[$wid]) ? 1 : $this->info[$wid] += 1;
        $this->save();
        $h_info = $WifeModel->getBase_buyid($wid);
        Master::back_data($this->uid,'wife','wifeList',array($h_info),true);
    }

    public function make_out()
    {
        $outf = array();
        if(!empty($this->info)){
            foreach ($this->info as $wid => $num){
                $outf[] = array(
                    'id' => $wid,
                    'trans' => $num % 2 == 0 ? 0 : 1,//是否转换了
                );
            }
        }
        $this->outf = $outf;
    }

    public function is_trans($wid){
        if(empty($this->info[$wid])){
            return 0;
        }
        return $this->info[$wid] % 2 == 0 ? 0 : 1;
    }
}














