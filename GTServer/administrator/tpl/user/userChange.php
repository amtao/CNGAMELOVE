<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<?php if($uid && empty($auth['ban']['user']['userChange'])){?>
	<div style="width: 600px;display: inline-block">

	<hr class="hr" />
    <table style="width: 600px;margin-top:10px;line-height:30px;font-size: 13px;">
        <tr>
            <td style="text-align:center;width: 350px;background-color:#c6e4fe; font-weight: bold;">气势</td>
            <td style="text-align:center;width: 350px;background-color:#c6e4fe; font-weight: bold;">智谋</td>
            <td style="text-align:center;width: 350px;background-color:#c6e4fe; font-weight: bold;">政略</td>
            <td style="text-align:center;width: 400px;background-color:#c6e4fe; font-weight: bold;">魅力</td>
        </tr>
        <tr>
            <td style="text-align:center;width: 100px;"><?php echo $ep[1];?></td>
            <td style="text-align:center;width: 100px;"><?php echo $ep[2];?></td>
            <td style="text-align:center;width: 100px;"><?php echo $ep[3];?></td>
            <td style="text-align:center;width: 100px;"><?php echo $ep[4];?></td>
        </tr>

    </table>
	<table style="width: 600px;margin-top:10px;line-height:30px;font-size: 13px;">
        <tr>
            <td style="text-align:center;width: 350px;background-color:#c6d4f2; font-weight: bold;">总势力</td>
            <td style="text-align:center;width: 350px;background-color:#c6d4f2; font-weight: bold;">ep</td>
            <td style="text-align:center;width: 350px;background-color:#c6d4f2; font-weight: bold;"><?php echo $shili;?></td>
            <td style="text-align:center;width: 400px;background-color:#c6d4f2; font-weight: bold;">修改类型</td>
            <td style="text-align:center;width: 350px;background-color:#c6d4f2; font-weight: bold;">数据</td>
            <td style="text-align:center;width: 350px;background-color:#c6d4f2; font-weight: bold;">操作</td>
        </tr>
		<?php if(!empty($info)){ foreach ($info as $k => $v){?>
            <form method="post" action="">
                <input type="hidden" value="<?php echo $uid;?>" name="uid" id="uid" />
                <input type="hidden" value="<?php echo $uid;?>" name="add_change" id="add_change" />
                <input type="hidden" value="<?php echo $k;?>" name="info" id="add_change" />
		<tr>
			<td style="text-align:center;width: 350px;background-color:#c6d4f2; font-weight: bold;"> <?php
			switch ($k){
			    case 'uid':
			        echo '用户uid  ';
			        break;
			    case 'name':
			        echo '用户名称  ';
			        break;
			    case 'job':
			        echo '头像ID  ';
			        break;
			    case 'sex' :
			        echo '性别  ';
                    break;
                case 'level' :
                    echo '身份  ';
                    break;
                case 'exp' :
                    echo '威望  ';
                    break;
                case 'dresscoin' :
                    echo '装扮货币  ';
                    break;
                case 'vip' :
                    echo 'VIP等级  ';
                    break;
                case 'step' :
                    echo '账号进度  ';
                    break;
                case 'guide' :
                    echo '新手引导步骤  ';
                    break;
                case 'cash_sys' :
                    echo '系统钻石  ';
                    break;
                case 'cash_buy' :
                    echo '充值钻石  ';
                    break;
                case 'cash_use' :
                    echo '消耗钻石  ';
                    break;
                case 'coin' :
                    echo '阅历  ';
                    break;
                case 'food' :
                    echo '银两  ';
                    break;
                case 'army' :
                    echo '名声  ';
                    break;
                case 'bmap' :
                    echo '地图大关ID  ';
                    break;
                case 'smap' :
                    echo '地图小关ID  ';
                    break;
                case 'mkill' :
                    echo '已杀小兵数量/已伤的BOSS血量  ';
                    break;
                case 'baby_num' :
                    echo '徒弟席位  ';
                    break;
                case 'cb_time' :
                    echo '朝拜时间  ';
                    break;
                case 'clubid' :
                    echo '宫殿ID  ';
                    break;
                case 'mw_num' :
                    echo '名望数值  ';
                    break;
                case 'mw_day' :
                    echo '名望每日产出  ';
                    break;
                case 'xuanyan' :
                    echo '宣言  ';
                    break;
                case 'voice' :
                    echo '声音开关  ';
                    break;
                case 'music' :
                    echo '音乐开关  ';
                    break;
                case 'regtime' :
                    echo '注册时间  ';
                    break;
                case 'lastlogin' :
                    echo '最后一次登陆时间  ';
                    break;
                case 'loginday' :
                    echo '累计登陆天数  ';
                    break;
                case 'platform' :
                    echo '渠道标识  ';
                    break;
                case 'channel_id' :
                    echo '渠道  ';
                    break;
                case 'ip' :
                    echo '注册Ip  ';
                    break;
                case 'cash' :
                    echo '钻石总结  ';
                    break;
			}
			?></td>
            <td style="text-align:center;width: 100px;"><?php echo $k;?></td>
            <td style="text-align:center;width: 100px;"><?php if ($k == "regtime" || $k == "lastlogin"){ echo date("Y-m-d H:i:s", $v); }else{ echo $v;}?></td>
            <td style="text-align:center;width: 100px;"> <?php
                switch ($k){
                    case 'uid':
                        echo '';
                        break;
                    case 'name':
                        echo '=';
                        break;
                    case 'job':
                        echo '=';
                        break;
                    case 'sex':
                        echo '=';
                        break;
                    case 'level':
                        echo '=';
                        break;
                    case 'exp':
                        echo '+';
                        break;
                    case 'dresscoin':
                        echo '+';
                        break;
                    case 'vip':
                        echo '=';
                        break;
                    case 'step':
                        echo '=';
                        break;
                    case 'guide':
                        echo '=';
                        break;
                    case 'cash_sys':
                        echo '+';
                        break;
                    case 'cash_buy':
                        echo '+';
                        break;
                    case 'cash_use':
                        echo '+';
                        break;
                    case 'coin':
                        echo '+';
                        break;
                    case 'food':
                        echo '+';
                        break;
                    case 'army':
                        echo '+';
                        break;
                    case 'bmap':
                        echo '=';
                        break;
                    case 'smap':
                        echo '=';
                        break;
                    case 'mkill':
                        echo '=';
                        break;
                    case 'baby_num':
                        echo '=';
                        break;
                    case 'cb_time':
                        echo '=';
                        break;
                    case 'clubid':
                        echo '=';
                        break;
                    case 'mw_num':
                        echo '=';
                        break;
                    case 'mw_day':
                        echo '=';
                        break;
                    case 'xuanyan':
                        echo '=';
                        break;
                    case 'voice':
                        echo '=';
                        break;
                    case 'music':
                        echo '=';
                        break;
                    case 'regtime':
                        echo '=';
                        break;
                    case 'lastlogin':
                        echo '=';
                        break;
                    case 'loginday':
                        echo '=';
                        break;
                    case 'platform':
                        echo '=';
                        break;
                    case 'channel_id':
                        echo '=';
                        break;
                    case 'ip':
                        echo '=';
                        break;
                    case 'cash':
                        echo '=';
                        break;
                }
                ?></td>
            <td><?php if($k != 'uid' && $k != 'cash' && $k != 'ip' && $k != 'guide' && $k != 'regtime' && $k != 'lastlogin'):?><input type="text" value="" name="<?php echo $k;?>"><?php endif;?></td>
            <td><?php if($k != 'uid' && $k != 'cash' && $k != 'ip' && $k != 'guide' && $k != 'regtime' && $k != 'lastlogin' && empty($auth['ban']['user']['post'])):?><input type="submit" value="修改" /><?php endif;?></td>
		</tr>
            </form>
		<?php } }?>
		</tr>
	</table>

	</div>
<?php }?>
<?php if($info){?>
	<div style="width: 200px;float: right;padding-right: 50%;font-size: 14px;line-height: 20px;">
<pre>
<?php var_export($info);?>
</pre>
	</div>
<?php }?>

<?php
//形象地显示时间 
function ptf_time($time){
	$d_time = $_SERVER['REQUEST_TIME'] - $time;
	if ($d_time < 60){
		return $d_time.'秒前 ('.date('Y-m-d H:i:s',$time).')';
	}else if ($d_time < 3600){
		return floor($d_time/60).'分钟前 ('.date('Y-m-d H:i:s',$time).')';
	}else if ($d_time < 864000){
		return round($d_time/86400,1).'天前 ('.date('Y-m-d H:i:s',$time).')';
	}
}
?>
<?php include(TPL_DIR.'footer.php');?>