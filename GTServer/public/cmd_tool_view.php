<style type="text/css">
    *{ font-size:12px;}
    .header a{ margin-right:7px; border:#06F 1px solid;  line-height:25px; padding:3px;}
    a{text-decoration:none;}
    a:link {color: #666}
    a:visited {color: #666}
    a:hover { color:#F00;}
    a:active {color: #F00}
    table{width:70%;border-collapse:collapse; line-height:20px;}
    table th { background-color:#B5B5FF;border:#06F solid 1px;}
    table td {  border:#06F solid 1px; padding-left:5px;}
</style>
<form action="" method="post" name="cmd_tool">
<table>
<tr>
<th>标题</th><th>表单</th>
</tr>
<tr>
<td>UID：</td><td><input type="text" name="uid" value="<?php echo $_POST["uid"];?>"  /></td>
</tr>
<tr>
<td>协议：</td><td>
<select name="msgName" id="msgName">
	<option value="">请选择</option>
	<?php foreach($csMsgNames as $csType => $csName): ?>
	<option value="<?php echo $csName;?>" <?php echo $_POST['msgName'] == $csName ? "selected" : "";?>><?php echo $csType .'-'. $csName;?></option>
	<?php endforeach;?>
</select>
</td>
</tr>
<tr>
<td>参数：</td><td>
<textarea rows="20" cols="60" name="csArgs"><?php echo $_POST['csArgs'];?></textarea>
</td>
</tr>
<tr>
    <td>执行次数：</td><td>
        <input name="num" value="1" />
    </td>
</tr>
<tr>
<td></td><td>
<input type="submit" value="提交"></input>
</td>
</tr>
</table>
</form>