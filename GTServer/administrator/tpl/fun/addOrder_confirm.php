<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">玩家直充</th></tr>
        <tr><th style="width: 40%">角色ID*</th><td><?php echo $data['roleid'];?></td></tr>
        <tr><th style="width: 40%">角色姓名*</th><td><?php echo $info['name'];?></td></tr>
        <tr><th style="width: 40%">渠道*</th><td><?php echo $platformInfo[$info['platform']]['name'];?></td></tr>
        <tr><th style="width: 40%">角色IP*</th><td><?php echo $info['ip'];?></td></tr>
        <tr><th>道具类型*</th>
            <td>
                <?php echo $data['money'];?>元
            </td>
        </tr>
        <tr><th colspan="2"><input type="submit" class="input" value="充值" /></th></tr>
    </table>
    <input type="hidden" value="2" name="step" />
    <input type="hidden" value="<?php echo $data['roleid'];?>" name="uid" />
    <input type="hidden" value="<?php echo $id;?>" name="item" />
    <input type="hidden" value="<?php echo $sdk;?>" name="sdk" />
</form>


<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>