<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6191Model extends ActBaseModel
{
	public $atype = 6191;//活动编号
	
	public $comment = "花圃";
    public $b_mol = "flower";//返回信息 所在模块
    public $b_ctrl = "feild";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'feilds' => array(
            /*
             * 'id'
             * 'pid'
             * 'sTime'
             */
        ),
        'openid' => array(),
    );


    public function plant($fid, $pid){
        $Act6192Model = Master::getAct6192($this->uid);
        $fSys = Game::getcfg_info("flowerFeild", $fid);
        if ($fSys == null || ($Act6192Model->info['lv'] < $fSys['lv'] && !in_array($fid, $this->info['openid']))){
            Master::error(FLOWER_FLOWER_UNLOCK);
        }

        $feilds = array();
        $isFind = false;
        $pSys = Game::getcfg_info("flowerCore", $pid);
        if ($Act6192Model->info['lv'] < $pSys['lv']){
            Master::error(FLOWER_PLANT_UNLOCK);
        }
        $Act6192Model->addChenlu(-$pSys['dew']);
        foreach ($this->info['feilds'] as $f){
            if ($f['id'] == $fid && !$isFind){
                if ($f['pid'] != 0){
                    Master::error(FLOWER_FLOWER_PLANT);
                }
                $f['pid'] = $pid;
                $f['sTime'] = Game::get_now();
                $isFind = true;
            }
            $feilds[] = $f;
        }
        if (!$isFind){
            $f = array();
            $f['id'] = $fid;
            $f['pid'] = $pid;
            $f['sTime'] = Game::get_now();
            $feilds[] = $f;
        }
        $this->info['feilds'] = $feilds;
        $this->save();
        //活动消耗 - 御花园偷取晨露
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong6216',$this->uid,$pSys['dew']);
    }

    public function yjplant($arr){
        $Act6192Model = Master::getAct6192($this->uid);
        $allDew = 0;//消耗的总晨露
        foreach ($arr as $v){
            $fid = $v['id'];
            $pid = $v['uid'];
            //
            $fSys = Game::getcfg_info("flowerFeild", $fid);
            if ($fSys == null || ($Act6192Model->info['lv'] < $fSys['lv'] && !in_array($fid, $this->info['openid']))){
                Master::error(FLOWER_FLOWER_UNLOCK);
            }
            $pSys = Game::getcfg_info("flowerCore", $pid);
            if ($Act6192Model->info['lv'] < $pSys['lv']){
                Master::error(FLOWER_PLANT_UNLOCK);
            }
            $allDew += $pSys['dew'];
            foreach ($this->info['feilds'] as $k=>$f){
                if ($f['id'] == $fid){
                    if ($f['pid'] != 0){
                        Master::error(FLOWER_FLOWER_PLANT);
                    }
                    $this->info['feilds'][$k]['pid'] = $pid;
                    $this->info['feilds'][$k]['sTime'] = Game::get_now();
                }
            }
        }
        $Act6192Model->addChenlu(-$allDew);
        $this->save();
        //活动消耗 - 御花园偷取晨露
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong6216',$this->uid,$allDew);
    }

    public function shou($fid,$oneKey = false){
        $feilds = array();
        $Act6192Model = Master::getAct6192($this->uid);
        $lvSys = Game::getcfg_info("flowerLv", $Act6192Model->info['lv']);
        $bei = 1;
        if ($lvSys['chance'] > rand(0, 10000)){
            $bei = 2;
        }
        $isUp = false;
        foreach ($this->info['feilds'] as $f){
            if ($f['id'] == $fid || $oneKey == true){
                if ($f['pid'] == 0){
                    if ($oneKey){
                        $feilds[] = $f;
                        continue;
                    }
                    Master::error(FLOWER_FLOWER_UNPLANT);
                }
                $pSys = Game::getcfg_info("flowerCore", $f['pid']);
                if ($f['sTime'] + $pSys['time'] > Game::get_now()){
                    if ($oneKey){
                        $feilds[] = $f;
                        continue;
                    }
                    Master::error(FLOWER_FLOWER_OVER);
                }
                $isUp = true;
                $f['pid'] = 0;
                $f['sTime'] = 0;
                $Act6192Model->addExp($pSys['exp']);
                Master::add_item($this->uid, KIND_ITEM, $pSys['itemid'], $bei);
            }
            $feilds[] = $f;
        }
        if (!$isUp){
            Master::error(FLOWER_FLOWER_OVER);
        }
        $this->info['feilds'] = $feilds;
        $this->save();
    }

    public function kai($fid){
        $Act6192Model = Master::getAct6192($this->uid);
        $fSys = Game::getcfg_info("flowerFeild", $fid);
        $fSys1 = Game::getcfg_info("flowerFeild", $fid-1);
        if (in_array($fid, $this->info['openid'])){
            Master::error(FLOWER_FLOWER_OPEN_ERROR);
        }

        if ($fSys == null || ($Act6192Model->info['lv'] < $fSys1['lv'] && !in_array($fid-1, $this->info['openid']))){
            Master::error(FLOWER_FLOWER_OPEN_ERROR);
        }

        if ($fSys == null || $Act6192Model->info['lv'] > $fSys['lv']){
            Master::error(FLOWER_FLOWER_OPEN_ERROR);
        }

        if ($fSys['yb'] != 0){
            Master::sub_item($this->uid, KIND_ITEM, 1, $fSys['yb']);
        }

        $this->info['openid'][] = $fid;
        $this->info['feilds'][] = array(
            'id' => $fid,
            'pid' => 0,
            'sTime' => 0,
        );
        $this->save();
    }

    public function isNews(){
        $time_n = Game::get_now();
        foreach ($this->info['feilds'] as $f) {
            if ($f['pid'] != 0){
                $pSys = Game::getcfg_info("flowerCore", $f['pid']);
                if ($f['sTime'] + $pSys['time'] <= $time_n){
                    return 1;
                }
            }
        }
        return 0;
    }
}














