<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <hr class="hr"/>
 <div style="display: inline-block;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=jinyan&cur=transUser" <?php if ($_GET['act'] == 'jinyan') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">禁言列表</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=fenghao&cur=transUser" <?php if ($_GET['act'] == 'fenghao') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">封号列表</a></div>
        <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=fengsb&cur=transUser" <?php if ($_GET['act'] == 'fengsb') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">封设备列表</a></div>
    <div style="display: inline-block;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=jinyankua&cur=transUser" <?php if ($_GET['act'] == 'jinyankua') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">跨服禁言列表</a></div>
    <hr/>
<table style="width:100%;" class="mytable">
            <caption>封设备列表</caption>
            <tbody>
            <tr>
                <th>openid</th>
                <th>操作</th>
            </tr>
            <?php
            if(!empty($result)){
                foreach ($result as $v) {
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td style="text-align:center;">' . $v . '</td>';
                    echo '<td style="text-align:center;"><a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=fengsb&type=jiefeng&sbOpen='.$v.'">解封 </a></td>';
                    echo '</tr>';
                }
            }else{
                echo '<tr>';
                echo '<td colspan="2" style="text-align:center;">暂无封设备列表</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>



<br/>
<?php include(TPL_DIR.'footer.php');?>