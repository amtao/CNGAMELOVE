<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
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
        <td style='text-align: right;'>KEY：</td>
        <td><?php echo $info['config_key'];?></td>
    </tr>
    <tr>
        <td style='text-align: right;'>生效区服：</td>
        <td><?php echo $info['server'];?></td>
    </tr>
    <tr>
        <td style='text-align: right;'>详细信息：</td>
        <td>
            <textarea rows="40" cols="150" id="contents" name="contents" readonly="readonly" ><?php echo $info['contents'];?></textarea>
        </td>
    </tr>
</table>
<?php include(TPL_DIR.'footer.php');?>