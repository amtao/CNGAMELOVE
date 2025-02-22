<?php
//排行榜
class fengxiandianMod extends Base
{

    /*
     * 奉先殿详情
     */
	public function getInfo(){
		$Sev5Model = Master::getSev5();
		$Sev5Model->back_data();
	}

    /*
     * 奉先殿请安
     */
    public function qingAn($params){
        $type = Game::intval($params, 'type');
        //请安
        $Act18Model = Master::getAct18($this->uid);
        $Act18Model->qingAn($type);
    }
}
