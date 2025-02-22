<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">月卡年卡管理</th></tr>
        <tr><th style="width: 40%">角色ID*</th><td><?php echo $data['roleid'];?></td></tr>
        <tr><th style="width: 40%">开始时间(月卡)*</th><td><?php echo $info["tjson"]["data"]["1"]["retime"];?></td></tr>
        <tr><th style="width: 40%">结束时间(月卡)*</th><td><?php echo $info["tjson"]["data"]["1"]["daytime"];?></td></tr>
        <tr><th style="width: 40%">上次领取时间(月卡)*</th><td><?php echo $info["tjson"]["data"]["1"]["rwdtime"];?></td></tr>
        <tr><th style="width: 40%">开始时间(年卡)*</th><td><?php echo $info["tjson"]["data"]["2"]["retime"];?></td></tr>
        <tr><th style="width: 40%">结束时间(年卡)*</th><td><?php echo $info["tjson"]["data"]["2"]["daytime"];?></td></tr>
        <tr><th style="width: 40%">上次领取时间(年卡)*</th><td><?php echo $info["tjson"]["data"]["2"]["rwdtime"];?></td></tr>
        <tr><th style="width: 40%">开始时间(周卡)*</th><td><?php echo $info["tjson"]["data"]["4"]["retime"];?></td></tr>
        <tr><th style="width: 40%">结束时间(周卡)*</th><td><?php echo $info["tjson"]["data"]["4"]["daytime"];?></td></tr>
        <tr><th style="width: 40%">上次领取时间(周卡)*</th><td><?php echo $info["tjson"]["data"]["4"]["rwdtime"];?></td></tr>
        <tr><th colspan="2"><input type="submit" class="input" value="关闭月卡"  name="month" /><input type="submit" class="input" value="关闭年卡"  name="year" /><input type="submit" class="input" value="关闭周卡"  name="week" /></th></tr>
    </table>
    <input type="hidden" value="2" name="step"></input>
    <input type="hidden" value="<?php echo $data['roleid'];?>" name="uid"></input>
</form>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>