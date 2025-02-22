<?php
//排行榜
class huanggongMod extends Base
{
	/*
	 * 皇宫请安
	 */
	public function qingAn($params){
        $uid = Game::intval($params,'uid');
        $type = Game::intval($params, 'type');
		//请安
		$Act18Model = Master::getAct18($this->uid);
		$Act18Model->qingAn($uid,$type);
	}

	/*
	 * 进入奉先殿
	 * */
	public function getInfo(){

        $Act313Model = Master::getAct313($this->uid);
        $Act313Model->back_data_RankFirst();

        $Act314Model = Master::getAct314($this->uid);
        $Act314Model->back_data_RankFirst();

        Common::loadApiModel("kuayamen");
        $KuayamenModel = new KuayamenMod($this->uid);
        $KuayamenModel->back_data_RankFirst();

        $Act6240Model = Master::getAct6240($this->uid);
        $Act6240Model->back_data_hd();
	}

    /**
     * 跨服兑换 - 兑换
     * $params['id'] :  商品id
     */
    public function exchangeRwd($params){
        $id = Game::intval($params,'id');
        $Act6240Model = Master::getAct6240($this->uid);
        $Act6240Model->exchange($id);
    }

}
