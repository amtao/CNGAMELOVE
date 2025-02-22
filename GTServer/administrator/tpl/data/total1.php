<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="post">
<table style='width: 100%;'>
    <tr>
        <th>日期范围</th>
        <td>
            <input class='Wdate' id='keyword5' type='text'  name='startTime' value='<?php echo $startTime?date('Y-m-d',$startTime):date('Y-m-d'); ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
            <input type="submit" value="查询">
        </td>
    </tr>
</table>
</form>
<hr class="hr"/>
    <form name='datalist'>
        <table style="width:100%;">
            <caption>日总览-总新增-付费</caption>
            <tbody>
            <tr>
                <th>区服</th>
                <th>今日新增注册</th>
                <th>今日登录用户</th>
                <th>今日营收</th>
                <th>今日付费人数</th>
                <th>今日付费笔数</th>
            </tr>
            <?php
             echo '<tr style="background-color:#f6f9f3;">';
             echo '<td>' . '总计'. '</td>';
             echo '<td>' . $zc . '</td>';
             echo '<td>' . $dl . '</td>';
             echo '<td>' . $ys . '</td>';
             echo '<td>' . $rs . '</td>';
             echo '<td>' . $bs . '</td>';
             echo '</tr>';
             if(!empty($total)){
                foreach ($total as $k => $v) {
                	$zc += $v['zc_count'];
                	$dl += $v['dl_count'];
                	$ys += $v['yingshou'];
                	$rs += $v['renshu'];
                	$bs += $v['bishu'];
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td>' . $v['sevid'] .'区'. '</td>';
                    echo '<td>' . $v['zc_count'] . '</td>';
                    echo '<td>' . $v['dl_count'] . '</td>';
                    echo '<td>' . $v['yingshou'] . '</td>';
                    echo '<td>' . $v['renshu'] . '</td>';
                    echo '<td>' . $v['bishu'] . '</td>';
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
