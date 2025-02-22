<?php

require_once "ActBaseModel.php";
/**
 * 行商
 */
class Act711Model extends ActBaseModel{
    public $atype = 711;

    public $comment = "行商-次数及恢复时间";
    public $b_mol = "business";//返回信息 所在模块
    public $b_ctrl = "startinfo";//返回信息 所在控制器

    public $_init = array(
        'startCount' => 0,
    );

    public function make_out(){
        $this->outf = $this->info;
    }
}

