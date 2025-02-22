<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">删除沙盒充值记录</th></tr>
        <tr>
            <th>玩家账号*</th>
            <th colspan="6">
                <textarea name="uids" cols="100" rows="8" style="width: 600px;" id="uids"><?php echo isset($_POST['uids']) ? $_POST['uids'] : '';?></textarea>多个用半角逗号（,）隔开
            </th>
        </tr>

        <tr>
            <th>成功区服*</th>
            <th colspan="6">
                <?php echo $sevIds;?>
            </th>
        </tr>

        <tr><th colspan="6"><input type="submit" value="删除" /></th></tr>
    </table>
</form>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
