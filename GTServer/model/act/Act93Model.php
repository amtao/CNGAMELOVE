<?php
require_once "ActBaseModel.php";
/*
 * 邮件发放
 */
class Act93Model extends ActBaseModel
{
	public $atype = 93;//活动编号
	
	public $comment = "邮件发放";
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'key' => 1, 1已领取
		*/
	);
	
	/*
	 * 不返回
	 */
	public function back_data(){
	}
	

	/*
	 * 发放邮件
	 * */
	public function sendMail()
	{
        $mail_key = 'mai_send_content';
	    $Sev31Model = Master::getSev31();
	    if(empty($Sev31Model->info)){
	        return true;
	    }
	    $cache = Common::getDftMem ();
	    $maildata = $cache->get($mail_key);
	    $UserModel = Master::getUser($this->uid);
	    $status = 0;//是否需要更新
	    foreach ($Sev31Model->info as $key => $val){
            if (!empty($val['serverEnd'])){
                $sevid = Common::getSevidCfg();
                if ($sevid['sevid']<$val['serverStart'] || $sevid['sevid'] > $val['serverEnd'] ){
                    continue;
                }
            }
            if (!empty($val['channels'])){
                if (!in_array($UserModel->info['platform'], $val['channels'])){
                    continue;
                }
            }
            if (!empty($val['startTime'])){
                if (!empty($val['vipType'])){
                    if ($val['vipType'] == 1 && !empty($val['vipData'])){
                        if ($UserModel->info['vip'] < $val['vipData']){
                            continue;
                        }
                    }elseif ($val['vipType'] == 2 && is_array($val['vipData'])){
                        if ($UserModel->info['vip'] < $val['vipData'][0] || $UserModel->info['vip'] > $val['vipData'][1]){
                            continue;
                        }
                    }elseif ($val['vipType'] == 3 && is_array($val['vipData'])){
                        if (!in_array($UserModel->info['vip'], $val['vipData'])){
                            continue;
                        }
                    }
                }
                if (strtotime($val['startTime'])>time() || time()>strtotime($val['endTime'])){
                    continue;
                }
                if ($UserModel->info['level']<intval($val['level'])){
                    continue;
                }
                if (!empty($val['registerTime'])){
                    if ($UserModel->info['regtime'] > strtotime($val['startTime'])){
                        continue;
                    }
                }
            }
	        if(!empty($this->info[$key])){
	            continue;
			}
			$link = isset($val['link']) ? $val['link'] : '';
	        if(!empty($val['items'])){
	            Master::sendMail($this->uid, $val['title'], $maildata[$key],1,$val['items'],$link);
	        }else{
	            Master::sendMail($this->uid, $val['title'], $maildata[$key],0,0,$link);
	        }
	        $this->info[$key] = 1;
	        $status = 1;
	    }
	    if($status == 1){
	        $this->save();
	    }
	    return true;
	}
}
