<?php
require_once "ActBaseModel.php";
/*
 * 公会邀请
 */
class Act8020Model extends ActBaseModel
{
	public $atype = 8020;//活动编号
	public $comment = "公会邀请";
	public $b_mol = "club";//返回信息 所在模块
	public $b_ctrl = "clubInvitation";//返回信息 所在控制器
	public $outf_u;//活动更新输出数据

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'list' => array()
	);
    public function __construct($uid,$hid = 1)
    {
		parent::__construct($uid,$hid);
    }

    /**
     * 添加邀请
     * @param unknown_type $fuid  玩家id
     * @param unknown_type $cid  公会
     */
    public function add_invitation($uid, $uname, $cid, $cname){

        foreach ($this->info["list"] as $k => $v) {

            if ($v["cid"] == $cid) {
                Master::error(CLUB_INVITATION);
            }
        }

        $this->info["list"][] = array("uid" => $uid, "uname" => $uname, "cid" => $cid, "cname" => $cname, "time" => $_SERVER['REQUEST_TIME']);
        $this->save();
        $this->ht_destroy();

        $news = 0;
        if (count($list) > 0) {
            $news = 1;
        }
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,array("list" => $this->info["list"], "news" => 1));
    }

    /**
     * 删除邀请
     * @param $cid  公会id   指定公会  0:标识删除该玩家的全部申请信息 ;  >0 标识删除指定玩家指定公会信息
     */
    public function del_invitation_user($cid){

        $list = array();
        foreach ($this->info["list"] as $k => $v) {

            if ($v["cid"] == $cid) {
                continue;
            }
            $list[] = $v;
        }

        $news = 0;
        if (count($list) > 0) {
            $news = 1;
        }
        $this->info["list"] = $list;
        $this->save();

        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,array("list" => $list, "news" => $news));
    }

    /**
     * 获取某个公会的邀请列表,邀请列表
     * @param unknown_type $cid
     */
    public function invitation_list(){

        $list = array();
        $flag = 0;  //是否进行保存操作   1:保存   0:不保存
        if(!empty($this->info)){

            foreach($this->info["list"] as $k => $v ){
                //三天没了申请操作  就清除申请记录
                if($_SERVER['REQUEST_TIME'] - $v["time"] > 86400*3 ){
                    $flag = 1;
                    continue;
                }

                $list[] = $v;
            }
        }

        //保存
        if($flag == 1){
            $this->info["list"] = $list;
            $this->save();
        }

        $news = 0;
        if (count($list) > 0) {
            $news = 1;
        }
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,array("list" => $list, "news" => $news));
    }
}