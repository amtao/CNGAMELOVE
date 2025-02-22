<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">用户步骤流失率</th></tr>
        <tr>
            <th style="text-align:right;width: 50px;">区服：</th>
            <td style="text-align:left;">
                <input type="text" name="serverid" id="serverid" value="<?php echo $_POST['serverid']; ?>">
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
    </table>
</form>

<hr class="hr" />
<?php
?>
<table>
    <tr>
        <th>步骤</th>
        <th>流失人数</th>
        <th>比例</th>
    </tr>
    <?php foreach ($allList as $k => $v): ?>
    <tr>
        <td style="text-align:center;"><?php echo $v["step_id"]; ?></td>
        <td style="text-align:center;"><?php echo $v["total"];  ?></td>
        <td style="text-align:center;"><?php echo number_format($v["total"]*100/$total,2).'%';  ?></td>
    </tr>
    <?php endforeach;?>
</table>

<script type="text/javascript">
    $(function(){
        $("input[type=submit]").click(function(){
            if (!$("#serverid").val()) {
                alert("区服不为空");
                return false;
            }
            return true;
        });
    });
</script>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
