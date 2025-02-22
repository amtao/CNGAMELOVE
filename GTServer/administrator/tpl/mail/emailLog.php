<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>
<div style="width: 100%;display: inline-block;">
<p>邮件日志:</p>
    <hr/>
<table style="width:100%;">
    <caption>用户信息</caption>
    <tbody>
    <tr>
        <th style="width: 100px;">账号</th>
        <th>邮件名称</th>
        <th>道具</th>
        <th>收件人(uids)</th>
        <th style="width: 200px;">发送时间</th>
    </tr>
    <?php
    if(!empty($data)){
        foreach ($data as $k => $v) {
            echo '<tr>';
            echo '<td>' . $v['user'] . '</td>';
            echo '<td>' . $v['title'] . '</td>';
            echo '<td>' . $v['items'] . '</td>';
            echo '<td>' . $v['uids'] . '</td>';
            echo '<td>' . date("Y-m-d H:i:s",$v['time']) . '</td>';
        }
    }
    ?>
    </tbody>
</table>
<?php include(TPL_DIR.'footer.php');?>