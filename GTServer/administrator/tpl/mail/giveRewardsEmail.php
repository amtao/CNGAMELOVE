<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
<table>
<tr><th colSpan="2">发放邮件</th></tr>
<tr><th>邮件标题*:</th><td><input type="text" name="title" style="width: 600px;" size="120" id="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '';?>"></td></tr>
<tr><th>邮件内容:</th><td><textarea cols="100" style="width: 600px;" rows="10" name ="message" ><?php echo isset($_POST['message']) ? $_POST['message'] : '';?></textarea></td></tr>
<tr><th>玩家账号*</th><td>
<textarea name="uids" cols="100" rows="10" style="width: 600px;" id="uids"><?php echo isset($_POST['uids']) ? $_POST['uids'] : '';?></textarea>多个用半角逗号（,）隔开
</td></tr>
<tr><th>物品列表</th>
    <td>
        <?php var_dump($item);?>
    </td>
</tr>
<tr><th></th><td><input type="submit" value="发送" /></td></tr>
</table>
</form>

<script type="text/javascript">
    $(function(){
	    $("input[type=submit]").click(function(){
		if (!$("#title").val()) {
			alert("邮件标题不为空");
			return false;
		}
		if (!$("#uids").val()) {
			alert("账号不为空");
			return false;
		}
		var uids = $("#uids").val();
		if(contains(uids, '，',0)){
            alert("uid包含中文字符逗号 ，");
            //return false;
        }
		return true;
	});
});

</script>
<?php include(TPL_DIR.'footer.php');?>