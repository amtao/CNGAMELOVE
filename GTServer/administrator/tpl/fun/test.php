<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<div id='addDiv'>
	<form id='addForm' method='post' action=''>
		<table style='width: 100%;'>
			<caption>信息</caption>
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> key：</th>
				<td>
					<input type='text' id='name' name='key' value='' />
					<label style='color:red'></label>
				</td>
			</tr>
            <tr>
                <th style='text-align: right;'><font color='red'>*</font> value：</th>
                <td>
                    <input type='text' id='name' name='value' value='' />
                    <label style='color:red'></label>
                </td>
            </tr>
            <tr>
                <th style='text-align: right;'><font color='red'>*</font> time：</th>
                <td>
                    <input type='text' id='name' name='time' value='' />
                    <label style='color:red'></label>
                </td>
            </tr>
			<tr><th></th><td><input type="submit" class="input" value="提交" /></td></tr>
		</table>
	</form>
	<hr />
    <table style='width: 100%;'>
        <caption>信息</caption>
        <tr>
            <th style='text-align: right;'><font color='red'>*</font> key：</th>
            <th>
                <font color='red'>*</font> value：
            </th>
            <th>
                <font color='red'>*</font> 过期时间：
            </th>
            <th>
                <font color='red'>*</font> 添加时间：
            </th>
            <th>
                <font color='red'>*</font> 过期时间：
            </th>
            <th>
                <font color='red'>*</font> 是否过期：
            </th>
        </tr>
        <?php if (!empty($data)):?>
        <?php foreach ($data as $key => $value):?>
        <tr>
            <td style="text-align: center;"><?php echo $key; ?></td>
            <td><?php echo $value['value']; ?>
            </td>
            <td style="text-align: center;"><?php echo $value['time']; ?></td>
            <td style="text-align: center;"><?php echo date("Y-m-d H:i:s", $value['createTime']); ?></td>
            <td style="text-align: center;"><?php echo date("Y-m-d H:i:s", ($value['createTime']+$value['time'])); ?></td>
            <td style="text-align: center;"><?php if ($cache->get($key)){ echo '未过期'; }else{ echo '<span style="color: red;">过期</span>';} ?></td>
        </tr>
        <?php endforeach;?>
        <?php endif;?>
    </table>
</div>
<?php include(TPL_DIR.'footer.php');?>
