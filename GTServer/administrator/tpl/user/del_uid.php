<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<form name="form1" id="form1" method="post" action="">
Uid:<input type="text" name="uid" value="<?php echo $uid;?>"  /><input type="submit" value="查看" name="show" />
<?php if(!empty($userInfo)){ ?>
</form>

	<table style="width: 450px;margin-top:10px;line-height:30px;font-size: 13px;">
			<tr>
				<td style="text-align:center;width: 200px;">用户id </td>
				<td >  <?php echo $userInfo['uid']?> </td>
			</tr>
			<tr>
				<td style="text-align:center;width: 200px;">用户名称 </td>
				<td >  <?php echo $userInfo['name']?> </td>
			</tr>
			<tr>
				<td style="text-align:center;width: 200px;">用户身份 </td>
				<td >  <?php echo $userInfo['level']?> </td>
			</tr>
			<tr>
				<td style="text-align:center;width: 200px;">vip等级 </td>
				<td >  <?php echo $userInfo['vip']?> </td>
			</tr>
			<tr>
				<td style="text-align:center;width: 200px;">状态 </td>
				<td >  <?php if($userInfo['is_del'] == 1){ echo '角色已删除'; }else{ echo '角色正常';}?> </td>
			</tr>
	</table>
<?php
   if($userInfo['is_del'] == 0){
?>
<hr class="hr" />
<form name="form2" id="form2" method="post" action="">
<input type="hidden" value="<?php echo $uid; ?>" name="del_uid">
<input type="submit" value="角色删除" name="del" />
</form>
<?php }else{?>
   <hr class="hr" />
   <form name="form3" id="form3" method="post" action="">
	   <input type="hidden" value="<?php echo $uid; ?>" name="recover_uid">
	   <input type="submit" value="角色恢复" name="recover" />
   </form>
<?php }?>

<?php } else{?>
	</form>
<?php }?>

