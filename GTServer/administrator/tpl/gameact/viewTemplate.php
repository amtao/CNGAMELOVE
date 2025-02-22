<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>
<script>
    $(function(){
        $("#header").hide();
        $(".header").hide();
        $(".mytable").hide();
        $("hr").hide();
    });
</script>

<table style='width:100%;'>
    <tr><th colspan="6">查看模板</th></tr>
    <tr>
        <td style='text-align: right;'>序号：</td>
        <td><?php echo $info['id'];?></td>
    </tr>
    <tr>
        <td style='text-align: right;'>活动编号：</td>
        <td><?php echo $info['act_key'];?></td>
    </tr>
    <tr>
        <td style='text-align: right;'>活动名称：</td>
        <td><?php echo $info['title'];?></td>
    </tr>
    <tr>
        <td style='text-align: right;'>详细信息：</td>
        <td>
            <textarea rows="40" cols="150" id="contents" name="contents" readonly="readonly" ><?php echo $info['contents'];?></textarea>
        </td>
    </tr>
</table>
<?php include(TPL_DIR.'footer.php');?>