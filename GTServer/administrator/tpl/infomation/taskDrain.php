<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form method="post">
    <table style='width: 100%;'>
        <tbody>
        <tr>
            <th style="text-align: right;">区服:</th>
            <td style="text-align: left;">
                <select name="server">
<?php foreach($serverlist as $k => $v){?>
                    <option <?php if ($_POST['server'] != "all" && $sevid == $v['id']) { echo 'selected="selected"'; } ?> value="<?php echo $v['id'];?>"><?php echo $v['name']['zh'];?></option>
<?php } ?>
                    <option value="all" <?php if ($_POST['server'] == "all") { echo 'selected="selected"'; } ?>>全部</option>
                </select>
            </td>
        </tr>
        <tr>
            <th colspan="2"><input type="submit" value="确定查询"></th>
        </tr>
        </tbody>
    </table>
</form>
<hr class="hr"/>
<?php

if($task){
    ?>
    <table style="width: 100%;">
        <tr>
            <th>主线任务</th>
            <th>流失人数</th>
            <th>比例</th>
        </tr>
        <?php foreach($task as $k => $v){?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $k.'.'.$task_cfg[$k]['name']; ?></td>
                <td style="text-align:center;"><?php echo $v;  ?></td>
                <td style="text-align:center;"><?php echo number_format($v*100/$total,2).'%';  ?></td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>