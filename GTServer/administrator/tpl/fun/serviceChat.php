<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">客服中心系统</th></tr>
        <tr><th style="width: 40%">角色ID*</th><td><input type="text" name="uid" style="width: 300px;" size="120" id="uid" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" value="<?php echo isset($_POST['uid']) ? $_POST['uid'] : '';?>">&nbsp;例如:10086</td></tr>
        <tr><th colspan="2"><input type="submit"  class="input" value="查询" /></th></tr>
    </table>
    <input type="hidden" value="1" name="step"></input>
</form>

<hr class="hr" />
<table style="width: 100%" class="mytable">
    <tr>
        <th>ID</th>
        <th>玩家UID</th>
        <th>昵称</th>
        <th>VIP等级</th>
        <th>身份</th>
        <th>内容</th>
        <th>时间</th>
        <th>回复人</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($noList as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;width: 80px;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['uid']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['name']; ?></td>
            <td style="text-align:center;width: 40px;"><?php echo $val['vip']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['level']; ?></td>
            <td style="text-align:center;width: 900px;"><?php echo $val['content']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo date('Y-m-d H:i:s',$val["send_time"]);?></td>
            <td style="text-align:center;width: 80px;"><?php if ($val['is_service'] == 0) {echo '';} else {echo '客服-'.$userAccount[$val["from"]]["name"];} ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['status']; ?></td>
            <td style="text-align:center;width: 80px;">
                <?php
                    echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=serviceChatReply&uid='.$val['uid'].'">回复 </a>  <a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=serviceChat&type=close&uid='.$val['uid'].'">关闭 </a>';
                ?>
            </td>
        </tr>
    <?php } ?>
</table>

<hr class="hr" />
<table style="width: 100%" class="mytable">
    <tr>
        <th>ID</th>
        <th>玩家UID</th>
        <th>昵称</th>
        <th>VIP等级</th>
        <th>身份</th>
        <th>内容</th>
        <th>时间</th>
        <th>回复人</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($okList as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;width: 80px;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['uid']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['name']; ?></td>
            <td style="text-align:center;width: 40px;"><?php echo $val['vip']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['level']; ?></td>
            <td style="text-align:center;width: 900px;"><?php echo $val['content']; ?></td>
            <td style="text-align:center;width: 80px;"><?php echo date('Y-m-d H:i:s',$val["send_time"]);?></td>
            <td style="text-align:center;width: 80px;"><?php if ($val['is_service'] == 0) {echo '';} else {echo '客服-'.$userAccount[$val["from"]]["name"];} ?></td>
            <td style="text-align:center;width: 80px;"><?php echo $val['status']; ?></td>
            <td style="text-align:center;width: 80px;">
                <?php
                    echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=serviceChatReply&uid='.$val['uid'].'">回复 </a>';
                ?>
            </td>
        </tr>
    <?php } ?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>