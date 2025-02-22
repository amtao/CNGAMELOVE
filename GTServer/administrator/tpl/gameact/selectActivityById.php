<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>

<script>
    function nextOpt(){
        var templateID = $("#template_id").val();
        if (templateID == null || templateID == "" || templateID == 0)
        {
            alert("请选择活动模板");
            return;
        }
        var cateID = $('#category_id option:selected').val();
        var url = '?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=addGameAct&template_id=' + templateID;
        window.location.href = url;
    }
</script>

<?php if (isset($_REQUEST['flag'])):?>
<div style="color:red"><?php echo $msg;?></div>
<?php endif;?>

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">添加活动通过模板id</th></tr>
        <tr>
            <td style='text-align: right;'>活动编号：</td>
            <td><input type='text' size='40' id='template_id' name='template_id' value='<?php echo $_POST['template_id'];?>' /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td style='text-align: center;'>
                <a href="javascript:nextOpt()" style="color: green;display: inline-block;">【--下一步--】</a>
            </td>
        </tr>
    </table>
</form>

<?php include(TPL_DIR.'footer.php');?>
