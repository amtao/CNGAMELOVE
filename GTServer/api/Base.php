<?php
class Base{
	//public $config;
	public $uid;
	/**
	 * @var UserModel
	 */
	public $test;
	/*
	 * @param int $uid
	 * @param UserModel &$UserModel
	 */
    public function __construct($uid)
    {
    	//$this->config = Common::getConfig();
		$this->uid = $uid;
    }
    
    public function error($msg)
    {
        
    }
    
    public function display($data)
    {
       
    }

    public function check($k_ctrl)
    {
        if (empty($this->uid)) {
            return true;
        }
        $UM = Master::getUser($this->uid);
        $obj = get_class($this);
        //没有取名限制（引导接口和心跳接口不限制）
        if ($obj == 'userMod' && $k_ctrl == 'adok') {
            return true;
        }
        if ($obj == 'userMod' && ($k_ctrl == 'recordSteps' || $k_ctrl == 'recordNewBie') ) {
            return true;
        }
        if ($obj != 'GuideMod' && $UM->info['step'] <= 0) {

            Game::crontab_debug($obj." === ".json_encode($UM->info)." === ".$k_ctrl, "check");

            Master::error(PARAMS_ERROR.'_'.$k_ctrl.'_'.__LINE__);
        }
        return true;
    }
}