<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>
<tr>
<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='beginDate' value='<?php echo $_POST['beginDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endDate' value='<?php echo $_POST['endDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
<input type="submit" value="查询">
</td>
</tr>
<tr>
<th rowspan='2' style='text-align: right;'>平台渠道</th>
<td colspan="2" style='text-align:left;'>
<input type='button' id='allSelect' value='选中全部'>&nbsp;
<input type='button' id='cancelAll' value='取消全选'>&nbsp;
<input type='button' id='selectEven' value='选中奇数'>&nbsp;
<input type='button' id='selectOdd' value='选中偶数'>&nbsp;
<input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
</td>
</tr>
<tr>
<td colspan="2" style='text-align:left;'>
<?php
$tmp = 0;
$checkboxHtml = '';
if(!empty($platformList)){
    foreach ($platformList as $k => $v) {
        $tmp++;
        $brSting = ($tmp%5) ? '' : '<br/>';
        if(empty($channels)){
            $channels = array();
        }
        $isChecked = in_array($k, $channels) ? 'checked' : '';
        $checkboxHtml .= sprintf("<input type='checkbox' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $brSting);
    }
}
echo $checkboxHtml;
?>
            </td>
        </tr>
    </table>
</form>
<hr class="hr"/>
    <form name='datalist'>
        <table style="width:100%;">
            <caption>ltv信息总览</caption>
            <tbody>
            <tr>
                <th style="text-align:center;">日期</th>
                <th style="text-align:center;">新建角色数</th>
                <th style="text-align:center;">1日</th>
                <th style="text-align:center;">2日</th>
                <th style="text-align:center;">3日</th>
                <th style="text-align:center;">4日</th>
                <th style="text-align:center;">5日</th>
                <th style="text-align:center;">6日</th>
                <th style="text-align:center;">7日</th>
            </tr>
            <?php
            if(!empty($data)){
                foreach ($data as $k => $v) {
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td style="text-align:center;">' . $k . '</td>';
                    echo '<td style="text-align:center;">' . $v['register'] . '</td>';

                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])){
                        foreach ($v['money'] as $mk => $mv){
                            if (strtotime($k) == strtotime($k)){
                                if (!empty($v['register'])){
                                    echo number_format($mv/$v['register'], 2);
                                }
                            }
                        }
                    }

                    echo '</td>';
                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])) {
                        foreach ($v['money'] as $mk => $mv) {
                            if (strtotime($k) == strtotime('+1 day', strtotime($k))) {
                                if (!empty($v['register'])) {
                                    echo number_format($mv / $v['register'], 2);
                                }
                            }
                        }
                    }
                    echo '</td>';
                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])) {
                        foreach ($v['money'] as $mk => $mv) {
                            if (strtotime($k) == strtotime('+2 day', strtotime($k))) {
                                if (!empty($v['register'])) {
                                    echo number_format($mv / $v['register'], 2);
                                }
                            }
                        }
                    }
                    echo '</td>';
                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])) {
                        foreach ($v['money'] as $mk => $mv) {
                            if (strtotime($k) == strtotime('+3 day', strtotime($k))) {
                                if (!empty($v['register'])) {
                                    echo number_format($mv / $v['register'], 2);
                                }
                            }
                        }
                    }
                    echo '</td>';
                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])) {
                        foreach ($v['money'] as $mk => $mv) {
                            if (strtotime($k) == strtotime('+4 day', strtotime($k))) {
                                if (!empty($v['register'])) {
                                    echo number_format($mv / $v['register'], 2);
                                }
                            }
                        }
                    }
                    echo '</td>';
                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])) {
                        foreach ($v['money'] as $mk => $mv) {
                            if (strtotime($k) == strtotime('+5 day', strtotime($k))) {
                                if (!empty($v['register'])) {
                                    echo number_format($mv / $v['register'], 2);
                                }
                            }
                        }
                    }
                    echo '</td>';
                    echo '<td style="text-align:center;">';
                    if (!empty($v['money'])) {
                        foreach ($v['money'] as $mk => $mv) {
                            if (strtotime($k) == strtotime('+6 day', strtotime($k))) {
                                if (!empty($v['register'])) {
                                    echo number_format($mv / $v['register'], 2);
                                }
                            }
                        }
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>

    </form>

    <script>
        $(document).ready(function () {
            $("#excel-submit").click(function () {
                $("#excel-input").val(1);
                $("#form-search").submit();
            });

            // 全选
            $('#allSelect').click(function(){
                $("input[name='channels[]']").attr('checked', 'true');
            });

            // 全不选
            $('#cancelAll').click(function(){
                $("input[name='channels[]']").removeAttr('checked');
            });

            //选中所有奇数
            $('#selectEven').click(function(){
                $("input[name='channels[]']:even").attr('checked', 'true');
            });

            //选中所有偶数
            $('#selectOdd').click(function(){
                $("input[name='channels[]']:odd").attr('checked', 'true');
            });

            // 反选
            $('#reverseSelect').click(function(){
                $("input[name='channels[]']").each(function(){
                    if($(this).attr('checked')){
                        $(this).removeAttr('checked');
                    }else{
                        $(this).attr('checked', 'true');
                    }
                });
            });
        });
    </script>
