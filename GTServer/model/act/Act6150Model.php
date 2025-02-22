<?php
require_once "ActBaseModel.php";
/*
 * 主角头像
 */
class Act6150Model extends ActBaseModel
{
    public $atype = 6150;//活动编号
	
	public $comment = "主角头像";
    public $b_mol = "userhead";//返回信息 所在模块
    public $b_ctrl = "blanks";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'blanks' => array(1),
        'limittime'=> array(),
    );

    /*
	 * 返回活动信息
	 */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol, $this->b_ctrl, $this->info['blanks']);

        $time = array();
        if (!empty($this->info['limittime'])) {
            foreach ($this->info['limittime'] as $k => $t) {
                $time[] = array("id" => $k, 'time' => $t);
            }
        }
        Master::back_data($this->uid, $this->b_mol, 'blanktime', $time);
    }

    /*
     * 改变展示伙伴
     */
    public function addBlank($id){
        if ($this->isUnlock($id))return;
        $blanks = $this->info['blanks'];
        $blanks[] = $id;
        $this->info['blanks'] = $blanks;
        $this->save();
    }

    /*
     * 删除
     */
    public function delBlank($id){
        $blanks = $this->info['blanks'];

        $newBlanks = array();
        foreach ($variable as $key => $value) {

            if ($value == $id) {
                continue;
            }
            $newBlanks[] = $value;
        }

        $Act6151Model = Master::getAct6151($this->uid);
        $actInfo = $Act6151Model->info;
        if ($id == $actInfo["blank"]) {
            $actInfo["blank"] = 1;
            $Act6151Model->changeHead($actInfo["head"], $actInfo["blank"]);
        }

        $this->info['blanks'] = $newBlanks;
        $this->save();
    }

    public function isUnlock($id){
        if (!in_array($id, $this->info['blanks']))return false;
        $time = !empty($this->info['limittime']) && !empty($this->info['limittime'][$id])?$this->info['limittime'][$id]:0;
        if ($time != 0 && $time < Game::get_now())return false;
        return true;
    }
}

