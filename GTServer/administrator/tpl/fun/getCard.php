<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">月卡年卡管理</th></tr>
        <tr><th style="width: 40%">角色ID*</th><td><input type="text" name="uid" style="width: 300px;" size="120" id="uid" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" value="<?php echo isset($_POST['uid']) ? $_POST['uid'] : '';?>">&nbsp;例如:10086</td></tr>
        <tr><th colspan="2"><input type="submit"  class="input" value="查询" /></th></tr>
    </table>
    <input type="hidden" value="1" name="step"></input>
</form>
<script type="text/javascript">
    $(function(){
        $("input[type=submit]").click(function(){
            if (!$("#uid").val()) {
                alert("账号不为空");
                return false;
            }
            return true;
        });
    });
</script>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>