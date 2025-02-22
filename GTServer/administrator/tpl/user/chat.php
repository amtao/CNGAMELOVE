<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<?php
if($chatData){
    ?>
    <table>
        <tr>
            <th>玩家UID</th>
            <th>玩家昵称</th>
            <th>vip等级</th>
            <th>身份</th>
            <th>聊天频道</th>
            <th>内容</th>
            <th>时间</th>
        </tr>
        <?php foreach($chatData as $k => $val){?>
            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php echo $val['uid']; ?></td>
                <td style="text-align:center;"><?php echo $val['user']['name']; ?></td>
                <td style="text-align:center;"><?php echo $val['user']['vip']; ?></td>
                <td style="text-align:center;"><?php echo $guan[$val['user']['level']]['name']; ?></td>
                <td style="text-align:center;">
                    <?php echo $val['type'];?>
                </td>
                <td style="text-align:center;">
                    <?php echo $val['msg']; ?>
                </td>
                <td style="text-align:center;">
                    <?php echo  date("Y-m-d H:i:s",$val['time']); ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>