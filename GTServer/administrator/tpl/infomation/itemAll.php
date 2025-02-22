<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
     <tr><th colspan="6">道具查询</th></tr>
     <tr>
        <th style="text-align:right;width: 50px;">道具：</th>
        <td style="text-align:left;">
            <select name="itemId" id="itemId">
                <option value="0" <?php if($_POST['itemId'] == 0) echo "selected";?>>全部</option>
                 <?php foreach ($items as $key => $value):?>
                     <option value="<?php echo $key; ?>" <?php if($_POST['itemId'] == $key) echo "selected";?>><?php echo $value['id'].'-'.$value['name_cn']; ?></option>
                 <?php endforeach;?>
            </select>   
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
<?php if($itemList):?>

    <table style="width: 100%" id="tableId">
        <thead>
        <tr>
            <th>道具ID</th>
            <th>道具名称</th>
            <th>道具数量</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($itemList as $key => $value){
            echo '<tr style="background-color:#f6f9f3;">';
            echo '<td style="text-align:center;">'.$value['itemid'].'</td>';
            echo '<td style="text-align:center;">'.$value['name'].'</td>';
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