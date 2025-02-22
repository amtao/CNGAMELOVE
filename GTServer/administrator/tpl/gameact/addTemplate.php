<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>

<script>
    function checkTempArg(){
        var act_key = $("#act_key").val();
        var title = $("#title").val();
        var contents = $("#contents").val();
        if (act_key == null || act_key == "") {
            alert("活动类型编号不能为空");return;
        }
        if (title == null || title == "") {
            alert("活动名称不能为空");return;
        }
        $.ajax({
            type:"POST",
            url:'?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=checkTempAjax',
            dataType:"json",
            async:false,
            data:"r="+Math.random()+"&contents="+contents,
            success:function(msg){
                if(msg["ok"]){
                    $("#formid").submit();
                }else{
                    alert(msg["result"]);
                }
            }
        });
    }
</script>

<?php if (isset($_REQUEST['flag'])):?>
<div style="color:red"><?php echo $msg;?></div>
<?php endif;?>

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">添加模板</th></tr>
        <tr>
            <td style='text-align: right;'>活动编号：</td>
            <td><input type='text' size='40' id='act_key' name='act_key' value='<?php echo $_POST['act_key'];?>' /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>活动名称：</td>
            <td>
                <input type='text' size='40' id='title' name='title' value='<?php echo $_POST['title'];?>' />
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>详细信息：</td>
            <td>
                <textarea rows="40" cols="150" id="contents" name="contents"><?php echo $_POST['contents'];?></textarea>
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td style='text-align: center;'>
                <a href="javascript:checkTempArg()" style="color: green;display: inline-block;">【--添加--】</a>
            </td>
        </tr>
    </table>
</form>
<?php include(TPL_DIR.'footer.php');?>
