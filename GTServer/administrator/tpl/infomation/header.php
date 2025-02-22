<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<table style="text-align: center;">
    <tr>
        <th>性别</th>
        <th>总数</th>
        <th>比例</th>
    </tr>
    <tr style="background-color:#f6f9f3;">
        <td>男性</td>
        <td><?php echo $sex['1']; ?></td>
        <td><?php echo number_format($sex['1']*100/$total, 2); ?>%</td>
    </tr>
    <tr>
        <th>头像</th>
        <th>总数</th>
        <th>比例</th>
    </tr>
    <?php 
    foreach ($job[1] as $k => $v):?>
        <tr style="background-color:#f6f9f3;">
            <td><?php echo $k; ?></td>
            <td><?php echo $v; ?></td>
            <td><?php echo number_format($v*100/$total, 2); ?>%</td>
        </tr>
    <?php endforeach;?>
    <tr>
        <th>性别</th>
        <th>总数</th>
        <th>比例</th>
    </tr>
    <tr style="background-color:#f6f9f3;">
        <td>女性</td>
        <td><?php echo $sex['2']; ?></td>
        <td><?php echo number_format($sex['2']*100/$total, 2); ?>%</td>
    </tr>
    <tr>
        <th>头像</th>
        <th>总数</th>
        <th>比例</th>
    </tr>
    <?php foreach ($job[2] as $k => $v):?>
        <tr style="background-color:#f6f9f3;">
            <td><?php echo $k; ?></td>
            <td><?php echo $v; ?></td>
            <td><?php echo number_format($v*100/$total, 2); ?>%</td>
        </tr>
    <?php endforeach;?>
</table>