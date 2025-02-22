<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<!--<?php include(TPL_DIR.'user/playmsg_head.php');?>-->
<form name="zhichong" method="POST" action="" onsubmit="return checkForm();">
	<table style="width:100%;">
		<caption>玩家角色数据转移</caption>
		<tbody><tr>
			<th style="text-align:right;"><font color="red">*</font>  新UID:</th>
			<td style="text-align:left;">
				<input id="uid1" name="uid1" value="" onkeyup="value=value.replace(/[^\d]/g,'');" type="text">
				请输入用户登录用的游戏ID，例如：1000001 <label style="color:red">【请注意区分大小写】</label>
			</td>
	     </tr>
	 	 <tr>
			<th style="text-align:right;"><font color="red">*</font> 老UID:</th>
			<td style="text-align:left;">
				<input id="uid2" name="uid2" value="" onkeyup="value=value.replace(/[^\d]/g,'');" type="text">
				请输入用户需要的显示游戏ID，例如：1000002 <label style="color:red">【请注意区分大小写】</label>
			</td>
	     </tr>
	     <tr><th colspan="2" style="color: red;">* 功能实现：两个角色ID对应的平台账户互换</th></tr>
		 <tr>
		 	<th colspan="2">
				<input name="step" value="1" type="hidden">
		 		<input value="下一步" type="submit">
		 	</th>
		 </tr>
	</tbody></table>
</form>

<hr class="hr" />
<table style="width: 100%" class="mytable">
    <tr>
        <th>ID</th>
        <th>新UID</th>
        <th>老UID</th>
    </tr>
    <?php foreach($list as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;"><?php echo $val['oldUID']; ?></td>
            <td style="text-align:center;"><?php echo $val['newUID']; ?></td>
        </tr>
    <?php } ?>
</table>

<script>
<!--
function checkForm() {
	uid1 = $.trim($('#uid1').val());
	if ( 0 >= uid1.length ) {
		alert('请输入旧角色ID。');
		$('#uid1').focus();
		return false;
	}
	uid2 = $.trim($('#uid2').val());
	if ( 0 >= uid2.length ) {
		alert('请输入新角色ID。');
		$('#uid2').focus();
		return false;
	}
	if ( uid1 == uid2 ) {
		alert('旧角色ID和新角色ID不能相同。');
		$('#uid2').focus();
		return false;
	}
		
	return true;
}
//-->
</script>
<?php include(TPL_DIR.'footer.php');?>