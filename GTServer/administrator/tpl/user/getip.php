<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>


    <div style="width: 100%;display: inline-block;">
    <p>当前玩家IP:<?php echo $ip ?></p>
<?php if (!empty($list)):?>
    <table style="margin-bottom:10px">
        <tr>
            <td style="width: 100px;">用户id</td>
            <td>用户设备号</td>
            <td>用户名称</td>
            <td>用户等级</td>
            <td>vip等级</td>
        </tr>
    <?php foreach($list as $k=>$v):?>
            <tr>
                <td>
                    <?php echo $k;?>
                </td>
                <td>
                    <?php echo $v['openid'];?>
                </td>
                <td>
                    <?php echo $v['name'];?>
                </td>
                <td>
                    <?php echo $v['level'];?>
                </td>
                <td>
                    <?php echo $v['vip'];?>
                </td>
            </tr>
    <?php endforeach;?>
    </table>
<?php endif;?>
<div class="add_uid">
    <form action="" method="post">
        <input type="text" name="fuid" value="" />
        <input type="submit" value="新增">
    </form>
</div>
<?php include(TPL_DIR.'footer.php');?>