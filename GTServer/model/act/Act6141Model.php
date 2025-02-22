<?php
require_once "ActBaseModel.php";
/*
 * 主角换装
 */
class Act6141Model extends ActBaseModel
{
	public $atype = 6141;//活动编号
	
	public $comment = "主角换装";
    public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "userClothe";//返回信息 所在控制器

    public $head_part = 1;
    public $body_part = 2;
    public $ear_part = 3;
    public $bg_part = 4;
    public $eff_part = 5;
    public $animal_part = 6;
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'body' => 0,
        'head' => 0,
        'ear' => 0,
        'background' => 0,
        'effect' => 0,
        'animal' => 0,
    );

    private function isParts($id, $part){
        if ($id == 0)return true;
        $sys = Game::getcfg_info("use_clothe", $id);
        return $sys['part'] == $part;
    }

    public function changeClothe($head, $body, $ear, $bg, $eff, $ani){
        $info = $this->info;
        if ($body == 0){
            $info["body"] = $info['head'] = 0;
            $this->info = $info;
            $this->save();
            return;
        }
        $Act6140Model = Master::getAct6140($this->uid);

        if ($Act6140Model->isUnlock($head) && $this->isParts($head, $this->head_part)){
            $info['head'] = $head;
        }

        if ($Act6140Model->isUnlock($body) && $this->isParts($body, $this->body_part)){
            $info['body'] = $body;
        }

        if ($Act6140Model->isUnlock($ear) && $this->isParts($ear, $this->ear_part)){
            $info['ear'] = $ear;
        }

        if ($Act6140Model->isUnlock($bg) && $this->isParts($bg, $this->bg_part)){
            $info['background'] = $bg;
        }

        if ($Act6140Model->isUnlock($eff) && $this->isParts($eff, $this->eff_part)){
            $info['effect'] = $eff;
        }

        if ($Act6140Model->isUnlock($ani) && $this->isParts($ani, $this->animal_part)){
            $info['animal'] = $ani;
        }

        $this->info = $info;
        $this->save();
    }

}














