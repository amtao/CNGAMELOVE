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
<tr>
<th style='text-align: right;'>区服</th>
<td colspan="2" style='text-align:left;'>
	<select name="servid" id="servid">
        <?php if (empty($auth['ban']['data']['totalCX'])):?>
            <option value="0">全服</option>
        <?php endif;?>
	     <?php foreach ($serverList as $key => $value):?>
			 <option value="<?php echo $key; ?>" <?php if($_POST['servid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
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

    <form name='datalist'>
        <table style="width:100%;">
            <caption>统计信息总览</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>新增注册</th>
                <th>登录用户</th>
                <th>营收</th>
                <th>付费人数</th>
                <th>付费笔数</th>
                <th>付费率</th>
                <th>新增营收</th>
                <th>新增付费人数</th>
                <th>新增付费率</th>
                <th>ARPPU</th>
                <th>次日留存</th>
                <th>七日留存</th>
                <th>累计注册</th>
                <th>累计营收</th>
                <th>累计LTV</th>
            </tr>
            <?php
            if(!empty($volist)){
                $add_reg = 0;
                $add_money = 0;
                foreach ($volist as $k => $v) {
                    echo '<tr>';
                    echo '<td>' . $v['time'] . '</td>';
                    echo '<td>' . $v['reg_pnum'] . '</td>';
                    echo '<td>' . $v['login_pnum'] . '</td>';
                    echo '<td>' . $v['total_money'] . '</td>';
                    echo '<td>' . $v['total_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['rechange_num'] . '</td>';
                    echo '<td>' . $v['rechange_rate'].'%' . '</td>';
                    echo '<td>' . $v['new_money'] . '</td>';
                    echo '<td>' . $v['new_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['new_rechange_rate'].'%' . '</td>';
                    echo '<td>' . ($v['total_rechange_pnum'] == 0 ? 0 : (empty($v['total_rechange_pnum']) ? 0 :number_format($v['total_money']/$v['total_rechange_pnum'],2))) . '</td>';
                    echo '<td>' . $v['two_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['two_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $v['week_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['week_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $add_reg +=$v['reg_pnum'] . '</td>';
                    echo '<td>' . $add_money += $v['total_money'] . '</td>';
                    echo '<td>' . ($add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)) . '</td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
        <hr class="hr"/>
         <table style="width:100%;">
            <caption>留存详情</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>新增注册</th>
                <th>登录用户</th>
                <th>次日留存</th>
                <th>三日留存</th>
                <th>五日留存</th>
                <th>七日留存</th>
                <th>14日留存</th>
                <th>月留存</th>
            </tr>
            <?php
            if(!empty($volist)){
                foreach ($volist as $k => $v) {
                    echo '<tr>';
                    echo '<td>' . $v['time'] . '</td>';
                    echo '<td>' . $v['reg_pnum'] . '</td>';
                    echo '<td>' . $v['login_pnum'] . '</td>';
                    echo '<td>' . $v['two_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['two_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $v['three_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['three_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $v['five_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['five_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $v['week_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['week_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $v['two_week_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['two_week_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                    echo '<td>' . $v['month_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['month_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
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
