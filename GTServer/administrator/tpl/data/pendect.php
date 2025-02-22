<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>

<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='beginDate' value='<?php echo $_POST['beginDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endDate' value='<?php echo $_POST['endDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
<input type="submit" value="查询">
</td>
<td><input name="excel-all" id="excel-input-all" type="hidden" value="0"><input id="excel-submit-all" type="button" value="导出各区服数据">
    | <input name="excel" id="excel-input" type="hidden" value="0"><input id="excel-submit" type="button" value="下载excel"></td>
</tr>
<tr>
<th rowspan='2' style='text-align: right;'>平台渠道</th>
<td colspan="2" style='text-align:left;'>
<input type='button' id='allSelect' value='选中全部'>&nbsp;
<input type='button' id='cancelAll' value='取消全选'>&nbsp;
<input type='button' id='selectEven' value='游动全选'>&nbsp;
<!--<input type='button' id='selectOdd' value='选中偶数'>&nbsp;-->
<input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
</td>
</tr>
<tr>
<td colspan="2" style='text-align:left;'>
<?php
$tmp = 0;
$you = 0;
$ios = 0;
$checkboxHtml = '';
$youdongHtml = '';
$iosdongHtml = '';
$youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
if(!empty($platformList)){
    foreach ($platformList as $k => $v) {

        if(empty($channels)){
            $channels = array();
        }
        $isChecked = in_array($k, $channels) ? 'checked' : '';
        if (!in_array($k,$youdongPlat['android']) && !in_array($k,$youdongPlat['ios'])){
            $tmp++;
            $brSting = ($tmp%5) ? '' : '<br/>';
            $checkboxHtml .= sprintf("<input type='checkbox' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $brSting);
        }elseif(in_array($k,$youdongPlat['android'])){
            $you++;
            $youSting = ($you%5) ? '' : '<br/>';
            $youdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $youSting);
        }elseif(in_array($k,$youdongPlat['ios'])){
            $ios++;
            $iosSting = ($ios%5) ? '' : '<br/>';
            $iosdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $iosSting);
        }


    }
}
echo $checkboxHtml;
if ($youdongHtml != ''){
    echo '<hr class="hr"/>';
    echo $youdongHtml;
}
if ($iosdongHtml != ''){
    echo '<hr class="hr"/>';
    echo $iosdongHtml;
}
?>
            </td>
        </tr>
    </table>
</form>

    <form name='datalist'>
        <table style="width:100%;">
            <caption>统计信息总览</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>新增注册</th>
                <th>营收</th>
                <th>付费人数</th>
                <th>付费笔数</th>
                <th>新增营收</th>
                <th>新增付费人数</th>
                <th>新增付费率</th>
                <th>ARPPU</th>
                <th>累计注册</th>
                <th>累计营收</th>
                <th>累计LTV</th>
            </tr>
            <?php
            if(!empty($data)){
                $add_reg = 0;
                $add_money = 0;
                foreach ($volist as $k => $v) {
                    echo '<tr>';
                    echo '<td>' . $v['time'] . '</td>';
                    echo '<td>' . $v['reg_pnum'] . '</td>';

                    echo '<td>' . $v['total_money'] . '</td>';
                    echo '<td>' . $v['total_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['rechange_num'] . '</td>';

                    echo '<td>' . $v['new_money'] . '</td>';
                    echo '<td>' . $v['new_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['new_rechange_rate'].'%' . '</td>';
                    echo '<td>' . ($v['total_rechange_pnum'] == 0 ? 0 : (empty($v['total_rechange_pnum']) ? 0 :number_format($v['total_money']/$v['total_rechange_pnum'],2))) . '</td>';
                    echo '<td>' . $add_reg +=$v['reg_pnum'] . '</td>';
                    echo '<td>' . $add_money += $v['total_money'] . '</td>';
                    echo '<td>' . ($add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)) . '</td>';
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
            $("#excel-submit-all").click(function () {
                $("#excel-input-all").val(1);
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

            //选中游动
            $('#selectEven').click(function(){
                $(".youdong").attr('checked', 'true');
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
