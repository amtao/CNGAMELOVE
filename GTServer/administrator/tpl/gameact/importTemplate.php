<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>

<script>
    function checkTempArg(){
        var contents = $("#contents").val();
        if (contents == null || contents == "") {
            alert("导入内容不为空");return;
        }
        $("#formid").submit();
    }
</script>

<?php if (isset($_REQUEST['flag'])):?>
<div style="color:red"><?php echo $msg;?></div>
<?php endif;?>

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">导入模板</th></tr>
        <tr>
            <td style='text-align: right;'>导入信息：</td>
            <td>
                <textarea rows="40" cols="150" id="contents" name="contents"><?php echo $_POST['contents'];?></textarea>
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td style='text-align: center;'>
                <a href="javascript:checkTempArg()" style="color: green;display: inline-block;">【--导入--】</a>
            </td>
        </tr>
    </table>
</form>
<?php include(TPL_DIR.'footer.php');?>
