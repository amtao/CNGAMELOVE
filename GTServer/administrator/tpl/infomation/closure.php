<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <form name="closure" method="POST" action="">
        <table style="width: 100%">
            <tr><th colspan="6">封号</th></tr>
            <tr>
                <th style="text-align:right;">UID：</th>
                <td style="text-align:left;">
                    <input  style="width: 80%" type="text" id="uid" name="uids"  value=""/>
                </td>
            </tr>

            <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
        </table>
    </form>
    <BR>
    <form name="closure" method="POST" action="">
        <table style="width: 100%">
            <tr><th colspan="6">禁言</th></tr>
            <tr>
                <th style="text-align:right;">UID：</th>
                <td style="text-align:left;">
                    <input  style="width: 80%" type="text" id="uid" name="banUid"  value=""/>
                </td>
            </tr>

            <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
        </table>
    </form>
    <BR>
    <form name="closure" method="POST" action="">
        <table style="width: 100%">
            <tr><th colspan="6">封设备</th></tr>
            <tr>
                <th style="text-align:right;">UID：</th>
                <td style="text-align:left;">
                    <input  style="width: 80%" type="text" id="uid" name="sbUid"  value=""/>
                </td>
            </tr>
            <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
        </table>
    </form>
    <BR>
<?php
if($sev26Model->info){
    ?>
    <table style="width: 100%" class="mytable">
        <tr>
            <th colspan="6">封号列表</th>
        </tr>
        <tr>
            <th>玩家UID</th>
            <th>玩家昵称</th>
            <th>vip等级</th>
            <th>身份</th>
            <th>时间</th>
            <th>封号操作</th>
        </tr>
        <?php foreach($sev26Model->info as $k => $val){ ?>

            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php $userInfo = new UserModel($k); if (empty($userInfo)){ continue;} echo $k; ?></td>
                <td style="text-align:center;"><?php echo $userInfo->info['name']; ?></td>
                <td style="text-align:center;"><?php echo $userInfo->info['vip']; ?></td>
                <td style="text-align:center;"><?php echo $guan[$userInfo->info['level']]['name']; ?></td>
                <td style="text-align:center;"><?php echo date("Y-m-d H:i:s", $val);?></td>
                <td style="text-align:center;">
                    <?php
                    echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=closure&closureUid='.$k.'">解封 </a>';
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>

<?php
if($sev23Model->info){
    ?>
    <BR>
    <table style="width: 100%">
        <tr>
            <th colspan="6">禁言列表</th>
        </tr>
        <tr>
            <th>玩家UID</th>
            <th>玩家昵称</th>
            <th>vip等级</th>
            <th>身份</th>
            <th>时间</th>
            <th>解禁言操作</th>
        </tr>
        <?php foreach($sev23Model->info as $k => $val){ ?>

            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php $userInfo = new UserModel($k); if (empty($userInfo)){ continue;} echo $k; ?></td>
                <td style="text-align:center;"><?php echo $userInfo->info['name']; ?></td>
                <td style="text-align:center;"><?php echo $userInfo->info['vip']; ?></td>
                <td style="text-align:center;"><?php echo $guan[$userInfo->info['level']]['name']; ?></td>
                <td style="text-align:center;"><?php echo date("Y-m-d H:i:s", $val);?></td>
                <td style="text-align:center;">
                    <?php
                    echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=closure&jieUid='.$k.'">解禁 </a>';
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>
<?php
if(!empty($sb_list)){
    ?>
    <BR>
    <table style="width: 100%">
        <tr>
            <th colspan="2">封设备列表</th>
        </tr>
        <tr>
            <th>设备</th>
            <th>封设备操作</th>
        </tr>
        <?php foreach($sb_list as $val){ ?>

            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php echo $val; ?></td>
                <td style="text-align:center;">
                    <?php
                    echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=closure&type=jiefeng&sbOpen='.$val.'">解封 </a>';
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>

