<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">充值退款</th></tr>
        <tr><th style="width: 40%">角色ID*</th><td><input type="text" name="uid" style="width: 300px;" size="120" id="uid" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" value="<?php echo isset($_POST['uid']) ? $_POST['uid'] : '';?>">&nbsp;例如:10086</td></tr>
        <tr><th>道具类型*</th>
            <td>
                <select class="input" name="item">
                    <?php foreach ($item as $key => $value):?>
                        <option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
                    <?php endforeach;?>
                </select>
            </td>
        </tr>
        <tr><th colspan="2"><input type="submit" name="czjj" class="input" value="成长基金退款" /><input type="submit" name="grl" class="input" value="贵人令退款" /><input type="submit" name="ngrl" class="input" value="新贵人令退款" /><input type="submit" name="zctk" class="input" value="直充退款" /></th></tr>
    </table>
</form>

<script type="text/javascript">

    function auditPass(step) {

        if (!$("#uid").val()) {
            alert("账号不为空");
            return false;
        }

        uid = $("#uid").val();
        if ( window.confirm('确认退款?') ) {
            location.href = '?sevid=<?php echo $_GET['sevid'];?>&mod=fun&act=czjijin&step=' + step + '&uid=' + uid;
        }
    }
</script>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>