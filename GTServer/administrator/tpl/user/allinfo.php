<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />

<form name="form1" id="form1" method="post" action="">
Uid:<input type="text" name="uid" value="<?php echo $uid;?>"  />&nbsp;
<input type="submit" value="查看" name="show" />

</form>
<?php if(!empty($info)){?>

<table style="width: 450px;margin-top:10px;line-height:30px;font-size: 13px;">
	<?php if(!empty($info)){ foreach ($info as $k => $v){?>
	<tr>
		<td style="text-align:center;width: 200px;"> <?php 
		switch ($k){
		    case 'uid':
		        echo '用户id';
		        break;
		    case 'name':
		        echo '用户名称';
		        break;
		    case 'job':
		        echo '头像ID';
		        break;
		    case 'sex':
		        echo '性别';
                    break;
                case 'level':
                    echo '身份';
                    break;
                case 'exp':
                    echo '威望';
                    break;
                case 'vip':
                    echo 'VIP等级';
                    break;
                case 'step':
                    echo '账号进度';
                    break;
                case 'guide':
                    echo '新手引导步骤';
                    break;
                case 'cash_sys':
                    echo '系统钻石';
                    break;
                case 'cash_buy':
                    echo '充值钻石';
                    break;
                case 'cash_use':
                    echo '消耗钻石';
                    break;
                case 'coin':
                    echo '阅历';
                    break;
                case 'food':
                    echo '银两';
                    break;
                case 'army':
                    echo '名声';
                    break;
                case 'bmap':
                    echo '地图大关ID';
                    break;
                case 'smap':
                    echo '地图小关ID';
                    break;
                case 'mkill':
                    echo '已经杀掉的小兵数量/已伤的BOSS血量';
                    break;
                case 'baby_num':
                    echo '徒弟席位';
                    break;
                case 'cb_time':
                    echo '朝拜时间';
                    break;
                case 'clubid':
                    echo '宫殿ID';
                    break;
                case 'mw_num':
                    echo '名望数值';
                    break;
                case 'mw_day':
                    echo '名望每日产出';
                    break;
                case 'xuanyan':
                    echo '宣言';
                    break;
                case 'voice':
                    echo '声音开关';
                    break;
                case 'music':
                    echo '音乐开关';
                    break;
                case 'regtime':
                    echo '注册时间';
                    break;
                case 'lastlogin':
                    echo '最后一次登陆时间';
                    break;
                case 'loginday':
                    echo '累计登陆天数';
                    break;
                case 'platform':
                    echo '渠道标识';
                    break;
                case 'channel_id':
                    echo '渠道';
                    break;
                case 'ip':
                    echo '注册Ip';
                    break;
                case 'cash':
                    echo '钻石总结';
                    break;
                default:
                	echo $k;
		}
		?></td>
		<td >  <?php echo $v?> </td>
	</tr>
	<?php } }?>
</table>

<?php }?>
<?php include(TPL_DIR.'footer.php');?>
