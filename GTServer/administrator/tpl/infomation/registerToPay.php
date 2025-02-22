<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <hr class="hr"/>
    <form method="post">
        <table>
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
<?php
if($dataInfo){
    ?>
    <table>
        <tr>
            <th>玩家注册距离付费天数</th>
            <th>总数</th>
            <th>比例</th>
        </tr>
        <?php foreach($dataInfo as $k => $v){?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $k; ?></td>
                <td style="text-align:center;"><?php echo $v;  ?></td>
                <td style="text-align:center;"><?php echo number_format($v*100/$total, 2).'%'; ?></td>
            </tr>
        <?php } ?>
    </table>
    <!--<table>
        <tr>
            <th>玩家uid</th>
            <th>注册日期</th>
            <th>付费日期</th>
        </tr>
        <?php /*foreach($data as $k => $v){*/?>
            <tr>
                <td style="text-align:center;"><?php /*echo $k; */?></td>
                <td style="text-align:center;"><?php /*echo date("Y-m-d H:i:s",$v['rtime']);  */?></td>
                <td style="text-align:center;"><?php /*echo date("Y-m-d H:i:s",$v['otime']); */?></td>
            </tr>
        <?php /*} */?>
    </table>-->

<?php  }?>