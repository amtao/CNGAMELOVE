<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<br />
<div class="mytable">
    <a href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=serviceChat'>返回客服中心系统</a>
</div>
<br />
<br />

<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">用户详情</th></tr>
        <tr><th style="width: 40%">用户ID*</th><td><?php echo $userInfo['uid'];?></td></tr>
        <tr><th style="width: 40%">用户昵称*</th><td><?php echo $userInfo['name'];?></td></tr>
        <tr><th style="width: 40%">用户VIP*</th><td><?php echo $userInfo['vip'];?></td></tr>
        <tr><th style="width: 40%">用户身份*</th><td><?php echo $userInfo['level'];?></td></tr>
        <tr><th colspan="2"><input type="submit" class="input" value="刷新聊天内容"  name="upd" /></th></tr>
    </table>
    <input type="hidden" value="2" name="step"></input>
</form>

<hr class="hr" />
<form name="form2" id="form2" method="post" action="">
        <table style='width:100%;' class="mytable">
            <tr>
                <td style='text-align: right;'>回复内容：</td>
                <td><textarea type='text' rows="10" cols="140"  id='content' name='content' ></textarea></td>
            </tr>
            <tr>
                <td colspan='2' align='center'>
                    <input type='hidden' id='reply' name='reply' value='reply' />
                    <input type='submit' value='回复' />
                </td>
            </tr>

        </table>
    </form>

<hr class="hr" />
<table style="width: 100%" class="mytable">
    <tr>
        <th>ID</th>
        <th>身份</th>
        <th>内容</th>
        <th>时间</th>
    </tr>
    <?php foreach($list as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;"><?php if ($val['is_service'] == 0) {echo '用户';} else {echo '客服-'.$userAccount[$val["from"]]["name"];} ?></td>
            <td style="text-align:center;"><?php echo $val['content']; ?></td>
            <td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$val["send_time"]);?></td>
        </tr>
    <?php } ?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>