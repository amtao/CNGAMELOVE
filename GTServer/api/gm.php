<?php
class GmMod extends Base
{
    public function gm($params) {
        $data = Game::intval($params,'data');
        $data = explode('/',$data);
        if ($data[0] == 'addItem') {
            $itemId = $data[1];
            $count = $data[2];
            $itemInfo = Game::getcfg_info('item',$itemId);
            if ($itemInfo) {
                Master::add_item($this->uid,$itemInfo['kind'],$itemId,$count);
            } else {
                Master::error(ITEMS_NOT_EXIST);
            }
        } elseif ($data[0] == 'addVip') {
            $UserModel = Master::getUser($this->uid);
            $vip = $data[1];
            if($vip >= 12) {
                $vip = 12;
            }
            $u_update = array(
                'vip'	=> $vip,
            );
            $UserModel->update($u_update);
        } elseif($data[0] == 'addHero') {
            $heroId = $data[1];
            $HeroModel = Master::getHero($this->uid);
            $HeroModel->add_hero($heroId);
        } elseif ($data[0] == 'addHeroJb') {
            $heroId = $data[1];
            $num = $data[2];
            $act6001Model = Master::getAct6001($this->uid);
            $act6001Model -> addHeroJB($heroId,$num);
        } elseif ($data[0] == 'sendEmail') {
            $itemIds = json_decode($data[1],true);
            $count = $data[2];
            $daoju = array();
            foreach ($itemIds as $itemId) {
                $itemInfo = Game::getcfg_info('item',$itemId);
                $daoju[] = array(
                    'id' => $itemId,
                    'count' => $count,
                    'kind' => $itemInfo['kind'],
                );
            }

            Master::sendMail($this->uid,'这是标题啊!!!','这是内容!!!',1,$daoju);
        }
    }
}