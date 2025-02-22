<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php  if ($info['lock'] != 1):?>
<form method="post">

    <table style='width: 100%;'>
        <tbody>
        <tr>
            <th style="text-align: right;">选择日期:</th>
            <td style="text-align:left;">
                <input class='Wdate' type='text' size='40' id='time' name='time'
                       value='<?php echo (empty($time)) ? date('Y-m-d 00:00:00') : $time;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                   })" />
            </td>
        </tr>
        <tr>
            <th style="text-align: right;">是否加锁</th>
            <td style="text-align:left;">
                <select name="lock">
                    <option value="0">不加锁</option>
                    <option value="1">加锁</option>
                </select>
            </td>
        </tr>
        <tr class="lock" style="display: none">
            <th style="text-align: right;">加锁密码</th>
            <td style="text-align:left;">
                <input name="password" value="" />
            </td>
        </tr>
        <tr class="lock" style="display: none">
            <th style="text-align: right;">加锁说明</th>
            <td style="text-align:left;">
                <input name="explain" value="" />
            </td>
        </tr>
        <tr>
            <th colspan="2"><input type="submit" value="提交"></th>
        </tr>
        </tbody>
    </table>
</form>
<?php  endif;?>
<?php  if ($info['lock'] == 1):?>
    <form method="post">
        <table style='width: 100%;'>
            <tbody>
            <tr>
                <th style="text-align: right;">加锁人</th>
                <td style="text-align:left;">
                    <?php  echo $info['admin']; if ($_SESSION['CURRENT_USER'] == $info['admin']){ echo '密码:'.$info['password'];} ?>
                </td>
            </tr>
            <tr>
                <th style="text-align: right;">说明</th>
                <td style="text-align:left;">
                    <?php  echo $info['explain']; ?>
                </td>
            </tr>
            <tr>
                <th style="text-align: right;">解锁密码</th>
                <td style="text-align:left;">
                    <input name="deblocking" value="" />
                </td>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="提交"></th>
            </tr>
            </tbody>
        </table>
    </form>
<?php  endif;?>
<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function () {
        $('[name="lock"]').on('change',function () {
            var status = $(this).val();
            if (status == 0){
                $('.lock').hide();
            }else {
                $('.lock').show();
            }
        });
    });
</script>
