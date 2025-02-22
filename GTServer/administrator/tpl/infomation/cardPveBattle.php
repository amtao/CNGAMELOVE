<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
    <tr>
            <th>日期范围*:</th>
            <td>
            <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                   value='<?php echo (empty($startTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $startTime);?>'
                   onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    maxDate:'#F{$dp.$D(\'endTime\')}'})" />
            <font> 至 </font>
            <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                   value='<?php echo (empty($endTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $endTime);?>'
                   onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    minDate:'#F{$dp.$D(\'startTime\')}'})" />
            </td>
        </tr>
    <tr>
        <th style="text-align:right;width: 50px;">区服：</th>
        <td style="text-align:left;" colspan="3">
            <input style="margin-top:3px;" type="button" value="全选" data-btn="all" />
            <input style="margin-top:3px;" type="button" value="全不选" data-btn="delAll" />
            <input style="margin-top:3px;" type="button" value="奇选" data-btn="ji" />
            <input style="margin-top:3px;" type="button" value="偶选" data-btn="ou" />
            </br><hr class="hr"/>
            <?php foreach ($serverList as $key => $value):?>
            <?php if ($value['id']==999){continue;}?>
            <input type="checkbox" data-btn="check" name="server[]" <?php if (!empty($_POST['server']) && in_array($value['id'], $_POST['server'])){ echo 'checked="checked"';}; ?> value="<?php echo $value['id']; ?>">  <?php echo $value['id']; ?> 服<span style="color: #97c6ff"> | </span><?php if ($value['id']%20 == 0){echo '<br/>';}; ?>
            <?php endforeach;?>
        </td>
    </tr>
     <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<script>
    $(document).ready(function () {
        $('[data-btn="all"]').click(function () {
            $('[data-btn="check"]').prop('checked','checked');
        });
        $('[data-btn="delAll"]').click(function () {
            $('[data-btn="check"]').prop('checked','');
        });
        $('[data-btn="ji"]').click(function () {
            $('[data-btn="check"]').each(function () {
                var v = $(this).val();
                if (v%2 != 0){
                    $(this).prop('checked','checked');
                }else {
                    $(this).prop('checked','');
                }
            });
        });
        $('[data-btn="ou"]').click(function () {
            $('[data-btn="check"]').each(function () {
                var v = $(this).val();
                if (v%2 == 0){
                    $(this).prop('checked','checked');
                }else {
                    $(this).prop('checked','');
                }
            });
        });
    });
</script>

<BR>
<?php if($pveList):?>

    <table style="width: 100%" id="tableId">
        <thead>
        <tr>
            <th>区服ID</th>
            <th>购买次数</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pveList as $key => $value){
            echo '<tr style="background-color:#f6f9f3;">';
            echo '<td style="text-align:center;">'.$value['sevid'].'</td>';
            echo '<td style="text-align:center;">'.$value['count'].'</td>';
        } ?>
        </tbody>
    </table>
    <BR>

<?php else:?>
    <table>
        <tr>
            <td>暂无充值记录</td>
        </tr>
    </table>
    <BR>
<?php endif;?>