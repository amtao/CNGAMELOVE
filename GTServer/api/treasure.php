<?php
//学院模块
class TreasureMod extends Base
{
	/*
	 * 购买火炉
	 */
	public function clear($params){
		//御膳房类
		$Act6110Model = Master::getAct6110($this->uid);
		//加火炉
		$Act6110Model->clear();
	}

	public function reward($params){
		//参数 奖励id
		$id = Game::intval($params,'id');
		
		//御膳房类
		$Act6110Model = Master::getAct6110($this->uid);
		$Act6110Model->getReward($id);		
	}

	public function treasure($params){
		//参数 奖励id
		$id = Game::intval($params,'id');
		
		//御膳房类
		$Act6110Model = Master::getAct6110($this->uid);
		$Act6110Model->addItem($id);		
	}

	public function clipTrea($params){
        //参数 奖励id
        $id = Game::intval($params,'id');

        //御膳房类
        $Act6110Model = Master::getAct6110($this->uid);
        $Act6110Model->addItem($id, true);
    }

    public function rank(){
        $Redis6110Model = Master::getRedis6110();
        $Redis6110Model->back_data();
        $Redis6110Model->back_data_my($this->uid);//我的排名
    }

    public function info(){
        $Act6111Model = Master::getAct6111($this->uid);
        $Act6111Model->flush();
    }

    public function win(){
        $Act6111Model = Master::getAct6111($this->uid);
        $Act6111Model->win();
    }

    public function reset(){
        $Act6111Model = Master::getAct6111($this->uid);
        $Act6111Model->reset();
    }

    public function addCount(){
        $Act6111Model = Master::getAct6111($this->uid);
        $Act6111Model->addCount();
    }

    public function tidyRank(){
        $Redis6111Model = Master::getRedis6111();
        $Redis6111Model->back_data();
        $Redis6111Model->back_data_my($this->uid);//我的排名
    }

    public function trun($params){
        $index1 = Game::intval($params,'index1');
        $index2 = Game::intval($params,'index2');
        $Act6111Model = Master::getAct6111($this->uid);
        $Act6111Model->trun($index1, $index2);
    }


}
