<?php include(TPL_DIR . 'header.php');?>

<br />
	<div class="mytable">
<a class="mytable" href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=slist'>返回服务器列表</a>
	</div>
<br />
<br />


<form name="form2" id="form2" method="post" action="">
<table style='width:100%;' class="mytable">

	<tr>
		<td style='text-align: right;'>服务器区号：</td>
		<td><input type='text' size='40' readonly="readonly" id='id' name='id' value='<?php echo $data['id'];?>' /></td>
	</tr>
	<tr>
		<td style='text-align: right;'>服务器域名：</td>
		<td>
			<input type='text' size='40' id='url' name='url' value='<?php echo $data['url'];?>' />
		</td>
	</tr>
	<tr>
		<td style='text-align: right;'>服务器名称：</td>
		<td><input type='text' size='40' id='zhname' name='zhname' value='<?php echo $data['name']['zh']?$data['name']['zh']:'新服';?>' /></td>
	</tr>
    <tr>
        <td style='text-align: right;'>服务器皮肤：</td>
        <td><input type='text' size='40' id='skin' name='skin' value='<?php echo isset($data['skin'])?$data['skin']:2;?>' /></td>
    </tr>
	<tr>
			<td style='text-align: right;'>服务器状态：</td>
			<td>
			<?php 
				$radioHtml = '';
				foreach ($statusCfg as $k => $v) {
					$isCheck = ($k == $data['status']) ? "checked='checked'" : '';
					$radioHtml .= <<<RADIO
					<input type='radio' name='status' value='{$k}' {$isCheck} />{$v}				
RADIO;

				}
				echo $radioHtml;
			?>
			</td>
		</tr>

		<tr>
			<td style='text-align: right;'>选区图标开放时间：</td>
			<td>
				<input class='Wdate' type='text' size='40' id='showtime' name='showtime' 
					value='<?php echo (empty($data['showtime'])) ? date('Y-m-d H', strtotime('now +3 years')) : date('Y-m-d H', $data['showtime']);?>' 
					onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH',isShowClear:false,readOnly:true})" />
			</td>
		</tr>

		<tr>
			<td colspan='2' align='center'>
				<input type='hidden' id='save' name='save' value='save' />
				<input type='submit' value='保存' />
			</td>
		</tr>

</table>
</form>


<script>
</script>
<?php include(TPL_DIR.'footer.php');?>