<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>
<tr>
<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='beginDate' value='<?php echo $start; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endDate' value='<?php echo $end; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
<input type="submit" value="查询">
</td>
<td>
<select name="serverid" id="serverid">
			     <option value="0">全服</option>
			     <?php foreach ($serverList as $key => $value):?>
					 <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
				 <?php endforeach;?>
			</select>	
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
            <caption>用户统计</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>未去重新增注册</th>
                <th>去重新增注册</th>
            </tr>
            <?php
            if(!empty($list)){
                $total = 0;
                foreach ($list as $k => $v) {
                    $total +=$v;
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td style="text-align:center;">' . $k . '</td>';
                    echo '<td style="text-align:center;">' .$lists[$k].'</td>';
                    echo '<td style="text-align:center;">' . $v . '</td>';
                    echo '</tr>';
                }
                
                echo '<tr style="background-color:#f6f9f3;">';
                echo '<th style="text-align:center;">总计</td>';
                echo '<td style="text-align:center;">'. $totals.'</td>';
                echo '<td style="text-align:center;">' . $total . '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </form>
<BR>
<form name='datalist'>
    <table style="width:100%;">
        <caption>详情</caption>
        <tbody>
        <tr>
            <th>区服</th>
            <th>uid</th>
            <th>openid</th>
            <th>平台</th>
            <th>注册时间</th>
        </tr>
        <?php
        if(!empty($data)){
            foreach ($data as $k => $v) {
                echo '<tr>';
                echo '<td style="text-align:center;">' . $v['servid'] . '</td>';
                echo '<td style="text-align:center;">' . $v['uid'] . '</td>';
                echo '<td style="text-align:center;">' . $v['openid'] . '</td>';
                echo '<td style="text-align:center;">' . $v['platform'] . '</td>';
                echo '<td style="text-align:center;">' . date('Y-m-d H:i:s', $v['reg_time']) . '</td>';
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
