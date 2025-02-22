<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 10;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
     <tr><th colspan="6">伙伴出游问候查询</th></tr>
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
            <th>出游总消耗</th>
            <th>问候总消耗</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataList as $key => $value){
            echo '<td style="text-align:center;">'.$value['heroid'].'</td>';
            echo '<td style="text-align:center;">'.$value['chuyou'].'</td>';
            echo '<td style="text-align:center;">'.$value['wenhou'].'</td>';
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