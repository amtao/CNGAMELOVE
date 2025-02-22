<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 10;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
     <tr><th colspan="6">消耗道具查询</th></tr>
     <tr>
        <th style="text-align:right;width: 150px;">区服：</th>
        <td style="text-align:left;">
            <select name="serverid" id="serverid">
                <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
                 <?php foreach ($serverList as $key => $value):?>
                     <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
                 <?php endforeach;?>
            </select>   
        </td>
        <th style="text-align:right;width: 50px;">VIP等级：</th>
        <td style="text-align:left;" colspan="3">
            <select name="vipLevel" id="vipLevel">
                 <?php foreach ($vipList as $key => $value):?>
                     <option value="<?php echo $key; ?>" <?php if($_POST['vipLevel'] == $key) echo "selected";?>><?php echo $value['vip']; ?></option>
                 <?php endforeach;?>
            </select>   
        </td>
     </tr>
     <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<BR>
<?php if($itemList):?>

    <table style="width: 100%" id="tableId">
        <thead>
        <tr>
            <th>道具ID</th>
            <th>道具名称</th>
            <th>总道具数量</th>
            <th>vip消耗道具数量</th>
            <th>vip占比</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($itemList as $key => $value){
            echo '<tr style="background-color:#f6f9f3;">';
            echo '<td style="text-align:center;">'.$value['itemid'].'</td>';
            echo '<td style="text-align:center;">'.$value['name'].'</td>';
            echo '<td style="text-align:center;">'.$value['count'].'</td>';
            echo '<td style="text-align:center;">'.$value['vipcount'].'</td>';
            echo '<td style="text-align:center;">'.number_format($value['vipcount'] / $value['count'] * 100, 2)."%".'</td>';
        } ?>
        </tbody>
    </table>
    <BR>

<?php else:?>
    <table>
        <tr>
            <td>暂无消耗记录</td>
        </tr>
    </table>
    <BR>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>