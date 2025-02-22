<?php
/*
 * 翰林院 总列表信息
 */
require_once "SevListBaseModel.php";
class Sev8Model extends SevBaseModel
{
	public $comment = "翰林院";
	public $act = 8;//活动标签
	
	public $b_mol = "hanlin";//返回信息 所在模块
    public $b_ctrl = "ting";//返回信息 所在控制器
	
	public $_init = array(//翰林院大厅
		/*
		10086 => fuser
		),
		*/
	);
	
	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$data = array();
		$over = 0;//放学的数量
		foreach ($this->info as $k => $v){
			unset($v['debug']);
			//是否放学了
			if (Game::is_over($v['num'])){
				unset($this->info[$k]);
				$over ++;
			}else{
				$data[] = $v;
			}
		}
		//如果有人放学了 就保存一下
		if ($over > 0){
			$this->save();
		}
		return $data;
	}
	
	/*
	 * 添加一个座位
	 */
	public function add($fUser){
		$fUser['num2'] = 0;//人数
		$this->info[$fUser['uid']] = $fUser;
		$this->save();
	}
	
	//更新一个座位人数信息
	public function update($uid,$num,$new_uid = 0){
		$this->info[$uid]['num2'] = $num;
		$this->info[$uid]['debug'][] = $new_uid;
		//$new_uid
		$this->save();
	}
	
	/*
	 * 返回协议信息
	 * 返回房间弹窗信息
	 */
	public function back_data(){
		$outf = $this->get_outf();
		Master::back_data(0,'hanlin','ting',$outf);
	}
	
	/*
	 * 获取输出数据
	 * 缓存每分钟更新一次
	 */
	public function get_outf(){
        return $this->mk_outf();
	}
    
}
