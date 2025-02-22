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
<td><input name="excel" id="excel-input" type="hidden" value="0"><input id="excel-submit" type="button" value="下载excel"></td>
</tr>
    </table>
</form>
<hr class="hr"/>
    <form name='datalist'>
        <table style="width:100%;">
            <caption>用户信息</caption>
            <tbody>
            <tr>
                <th>账号</th>
                <th>uid</th>
                <th>名称</th>
                <th>注册时间</th>
                <th>平台</th>
            </tr>
            <?php
            if(!empty($dataInfo)){
                foreach ($dataInfo as $k => $v) {
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td>' . $v['ustr'] . '</td>';
                    echo '<td>' . $k . '</td>';
                    echo '<td>' . $v['name'] . '</td>';
                    echo '<td>' . date('Y-m-d H:i:s',$v['regtime']) . '</td>';
                    echo '<td>' . $v['platform'] . '</td>';
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
