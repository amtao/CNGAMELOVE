<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 10;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
     <tr><th colspan="6">伙伴信息查询</th></tr>
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
            <input type="radio" name="stype" value="1"<?php if(empty($_POST['stype']) || $_POST['stype'] == 1) echo "checked";?>/> 伙伴时装
            <input type="radio" name="stype" value="2"<?php if($_POST['stype'] == 2) echo "checked";?>/> 伙伴星级
            <input type="radio" name="stype" value="3"<?php if($_POST['stype'] == 3) echo "checked";?>/> 伙伴羁绊等级
        </td>
    </tr>
     <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<BR>
<?php if($dataList):?>

    <table style="width: 100%" id="tableId">
        <thead>
        <tr>
            <th>伙伴id</th>
            <th>对应id</th>
            <th>人数</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataList as $key => $value){
            echo '<td style="text-align:center;">'.$value['heroid'].'</td>';
            echo '<td style="text-align:center;">'.$value['id'].'</td>';
            echo '<td style="text-align:center;">'.$value['count'].'</td>';
            echo '</tr>';
        } ?>
        </tbody>
    </table>
    <BR>

<?php else:?>
    <table>
        <tr>
            <td>暂无兑换记录</td>
        </tr>
    </table>
    <BR>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>