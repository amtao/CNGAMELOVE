<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<form  method="post" action="">
<table style="width: 1000px;margin-top:10px;line-height:30px;font-size: 13px;">
		<tr><th colspan="2">升级</th></tr>
    	<tr><td style="text-align:center;">玩家UID</td><td><input style="width:90%;" value="<?php echo $uidInfo;?>" name="uid" id="uid" /></td></tr>
    	<tr>
			<td style="text-align:center;">
				级别
			</td>
    		<td style="text-align:center;">
    		  <select name="level">
                 <option value="" >请选择级别</option>
        	     <option value="1" <?php if ($_POST['level'] == 1){echo "selected=selected"; };?>>初级</option>
                 <option value="2" <?php if ($_POST['level'] == 2){echo "selected=selected"; };?>>中级</option>
                 <option value="3" <?php if ($_POST['level'] == 3){echo "selected=selected"; };?>>高级</option>
	           </select>	
	         </td>
    	</tr>
		<tr><td style="text-align:center;">
				操作
			</td><td style="text-align:center;" ><input type="submit" value="升级"/></td></tr>
</table>
</form>

<?php include(TPL_DIR.'footer.php');?>
<script>
	$(document).ready(function () {
		$(':input[type="submit"]').on('click',function () {
			$(this).hide();
		});
	});
</script>