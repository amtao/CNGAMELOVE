<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>
<tr>
<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='beginDate' value='<?php echo $_POST['beginDate']?$_POST['beginDate']:date("Y-m-d H:i:s", $startTime); ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endDate' value='<?php echo $_POST['endDate']?$_POST['endDate']:date("Y-m-d H:i:s", $endTime); ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
<input type="submit" value="查询">
</td>
<td><input name="excel" id="excel-input" type="hidden" value="0"><input id="excel-submit" type="button" value="下载excel"></td>
</tr>
<tr>
<th style='text-align: right;'>区服</th>
<td colspan="2" style='text-align:left;'>
	<select name="servid" id="servid">
        <?php if (empty($auth['ban']['data']['totalInfomation'])):?>
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
<hr class="hr"/>
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
               <!-- <th>累计新增</th>
                <th>累计充值</th>
                <th>累计ltv</th>-->
            </tr>
            <?php
            if(!empty($data)){
                foreach ($data as $k => $v) {
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td>' . $k . '</td>';
                    echo '<td>' . $v['registerCount'] . '</td>';
                    echo '<td>' . $v['loginCount'] . '</td>';
                    echo '<td>' . $v['money'] . '</td>';
                    echo '<td>' . count($v['payMan']) . '</td>';
                    echo '<td>' . $v['payCount'] . '</td>';
                    echo '<td>';
                    if (!empty($v['payMan']) && !empty($v['loginCount'])){
                        echo number_format(count($v['payMan'])*100/$v['loginCount'], 2);
                    }else{
                        echo 0;
                    }
                    echo '%'.'</td>';
                   /* echo '<td>' . $v['registerAllCount'] . '</td>';
                    echo '<td>' . $v['allMoney'] . '</td>';
                    echo '<td>';
                    if (!empty($v['allMoney']) && !empty($v['registerAllCount'])){
                        echo number_format($v['allMoney']/$v['registerAllCount'], 2);
                    }else{
                        echo 0;
                    }
                    echo '%'.'</td>';*/
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
