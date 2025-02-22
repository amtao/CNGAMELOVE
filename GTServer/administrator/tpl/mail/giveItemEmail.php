<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
<table>
<tr><th colSpan="2">发放邮件</th></tr>
<tr><th>邮件标题*:</th><td><input type="text" name="title" style="width: 600px;" size="120" id="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '';?>"></td></tr>
<tr><th>邮件内容:</th><td><textarea cols="100" style="width: 600px;" rows="8" name ="message" ><?php echo isset($_POST['message']) ? $_POST['message'] : '';?></textarea></td></tr>
<tr><th>玩家账号*</th><td>
<textarea name="uids" cols="100" rows="8" style="width: 600px;" id="uids"><?php echo isset($_POST['uids']) ? $_POST['uids'] : '';?></textarea>多个用半角逗号（,）隔开
</td></tr>
<tr><th>道具列表</th>
    <td>
        <select class="input" name="item">
        <?php foreach ($items as $key => $value):?>
            <option value="<?php echo $key.'-'.$value['name']; ?>"><?php echo $value['id'].' - '.$value['name']; ?></option>
        <?php endforeach;?>
        </select>
        <input name="num" class="input" value="1" />
        <input name="add" class="input" type="button" value="添加" />
    </td>
</tr>
<tr><th>发送的道具信息</th>
    <td id="item">
    </td>
</tr>
    <tr><th>备注信息:</th><td><textarea cols="100" style="width: 600px;" rows="3" name ="remarks" ><?php echo isset($_POST['remarks']) ? $_POST['remarks'] : '';?></textarea></td></tr>
<tr><th></th><td><input type="submit" class="input" value="发送" /></td></tr>
</table>
</form>

<div style="width: 100%;clear: both;">
    <hr class="hr" />
    <p><b>所有道具信息 :</b></p>
    <?php if($allItem){?>
        <pre><?php echo $allItem;?></pre>
    <?php }?>
</div>

<script type="text/javascript">
    $(function () {
        $(':input[name="add"]').click(function () {
            var item = $('[name="item"]').val();
            var num = $('[name="num"]').val();
            var item = item + '-' + num;
            var arr = item.split('-');
            var rand = Math.random()*Math.random();
            str = '<p data-number="'+rand+'"><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>' + arr[1] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><input class="input" onclick="del('+rand+')" type="button" value="删除" / ></p>';
            $('#item').append(str);
        });
    });
    function del(rand){
        if (confirm('确认删除?')){
            $('[data-number="'+rand+'"]').remove();
        }else {
            return false;
        }
    }
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
        if (!$("#item").html()) {
            alert("道具不为空");
            return false;
        }
		return true;
	});
});
</script>
<?php include(TPL_DIR.'footer.php');?>