<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 10;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
     <tr><th colspan="6">奇珍查询</th></tr>
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
        <th style="text-align:right;width: 150px;">统计类型：</th>
        <td style="text-align:left;" colspan="3">
            <input type="radio" name="stype" value="1"<?php if(empty($_POST['stype']) || $_POST['stype'] == 1) echo "checked";?>/> 奇珍
            <input type="radio" name="stype" value="2"<?php if($_POST['stype'] == 2) echo "checked";?>/> 星级
            <input type="radio" name="stype" value="3"<?php if($_POST['stype'] == 3) echo "checked";?>/> 等级
            <input type="radio" name="stype" value="4"<?php if($_POST['stype'] == 4) echo "checked";?>/> 道具
        </td>
    </tr>
     <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<BR>
<?php if($dataInfo):?>

    <table style="width: 100%" id="tableId">
        <thead>
        <tr>
            <th onclick="sortAble(this,'tableId',1, 2, 'int')" style="cursor:pointer"><?php if($_POST['stype'] == 1){echo 'ID';}elseif ($_POST['stype'] == 2){echo '星级';}elseif ($_POST['stype'] == 3){echo '等级';}else{echo '道具';} ?></th>
            <th>数量</th>
            <th>占比</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataInfo as $key => $value){
            echo '<tr style="background-color:#f6f9f3;">';
            echo '<td style="text-align:center;">'.$key.'</td>';
            echo '<td style="text-align:center;">'.$value.'</td>';
            echo '<td style="text-align:center;">'.number_format($value*100/$total, 2).'%</td>';
            echo '</tr>';
        } ?>
        </tbody>
        <th style="text-align:center;">总计</th>
        <td style="text-align:center;"><?php echo $total; ?></td>
        <td style="text-align:center;"><?php echo 100; ?>%</td>
    </table>
    <BR>

<?php else:?>
    <table>
        <tr>
            <td>暂无奇珍记录</td>
        </tr>
    </table>
    <BR>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>