<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>
<form name="form1" method="POST" action="">
	<table style="width: 90%;" class="mytable">
		<tr><th colspan="6">区服活动</th></tr>
		<tr>
			<th style="text-align:right;">预览时间：</th>
			<td style="text-align:left;">
				<input class='Wdate' id='newTime' type='text' name='newTime' value='<?php echo $value['newTime']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true, minDate:'2016-01-01 00:00:00'})" />
			</td>
		</tr>
		<!--
		<tr>
			<td style='text-align: right;'>预览IP：</td>
			<td>
				<textarea rows="8" cols="40" id="ip" name="ip"><?php echo $value['ip'];?></textarea>
				多个用逗号“,”隔开
			</td>
		</tr>
		-->
		<tr>
			<th style="text-align:right;">操作：</th>
			<td style="text-align:left;">
				<input type="submit" value="修改" />
			</td>
		</tr>
	</table>
</form>

<?php include(TPL_DIR.'footer.php');?>