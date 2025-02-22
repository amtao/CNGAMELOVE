<?php
require_once "ActBaseModel.php";
/*
 * 新版酒楼-已参与门客
 */
class Act172Model extends ActBaseModel
{
	public $atype = 172;//活动编号
	
	public $comment = "新版酒楼-已参与门客";
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "heroList";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 *  hid => array(
		 *    'uid' => $uid,
		 *    'time' => $time
		 * )
		 * */
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outf = array();

        $HeroModel = Master::getHero($this->uid);
        $heros = $HeroModel->get_all_heros();
        foreach ($heros as $x){
            $count = 0;
            if(!empty($this->info[$x]) && $this->info[$x]['uid'] != 0){
                continue;
            }else{

                if (!empty($this->info[$x]['count'])){
                    $count = $this->info[$x]['count'];
                }
                $outf[]=array('hid'=>$x,'count'=>$count);
            }

        }

		$this->outf = $outf;
	}

	public function setOver($uid){
        foreach ($this->info as $hid => $v){
            if ($this->info[$hid]['uid'] != 0 && $this->info[$hid]['uid'] == $uid){
                $this->info[$hid]['uid'] = 0;
            }
        }
        $this->save();
    }

	public function checkOver(){
        foreach ($this->info as $hid => $v){
            if ($this->info[$hid]['time'] < Game::day_0()){
                $this->info[$hid]['count'] = 0;
            }
            if ($this->info[$hid]['uid'] != 0){
                $Act170Model = Master::getAct170($v['uid']);
                if ($Act170Model->is_over()){
                    $this->info[$hid]['uid'] = 0;
                }
            }
        }

        $this->save();
    }

	public function remove($hid){
	    if (!isset($this->info[$hid])){
	        return;
        }
        $this->info[$hid]['uid'] = 0;
	    $this->save();
    }

	public function add($fuid,$hid){
		if(isset($this->info[$hid]) && !empty($this->info[$hid]['uid'])){
            Master::error('BOITE_HERO_YANHUI_ENTER');
		}
		if ($this->info[$hid]['count'] > 0){

		    if ($this->info[$hid]['count'] == 1){
		        $money = 50;
            }else{
		        $num = $this->info[$hid]['count']-1;
                $money =floor(pow(1.2,$num)*50);
            }

            Master::sub_item($this->uid,KIND_ITEM,1,$money);

        }
		$this->info[$hid] = array(
			'uid' => $fuid,
			'count' => $this->info[$hid]?$this->info[$hid]['count']+1:1,
			'time' => Game::get_now()
		);
        $this->save();
        
	}
}
















