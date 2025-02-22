<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<script type="text/javascript">
	function checkForm(){
		if(document.rechargeSearch.startTime.value.length == 0){
			if(document.rechargeSearch.endTime.value.length > 0 ){
				alert("请选择时间范围");
				document.rechargeSearch.startTime.focus();
				return false;
			}
		}
		if(document.rechargeSearch.startTime.value.length > 0){
			if(document.rechargeSearch.endTime.value.length == 0 ){
				alert("请选择时间范围");
				document.rechargeSearch.endTime.focus();
				return false;
			}
		}
		if( (document.rechargeSearch.startTime.value.length > 0) && (document.rechargeSearch.endTime.value.length > 0 )){
			var startTime = document.rechargeSearch.startTime.value; 
			var startTimeJsArray = startTime.split("-");
			var startTimeJsMake = new Date(startTimeJsArray[0],startTimeJsArray[1],startTimeJsArray[2]);
			var startTimeJs = startTimeJsMake.getTime();

			var endTime = document.rechargeSearch.endTime.value; 
			var endTimeJsArray = endTime.split("-");
			var endTimeJsMake = new Date(endTimeJsArray[0],endTimeJsArray[1],endTimeJsArray[2]);
			var endTimeJs = endTimeJsMake.getTime();

			if( endTimeJs < startTimeJs ){
				alert("结束时间不能小于起始时间,请重新输入");
				document.rechargeStat.endTime.focus();
				return false;     
			}	
		}
		return true;
	}
</script>

<form action="" name="rechargeSearch" method="POST" id="form-search" onsubmit="return checkForm();">
<table style='width: 100%;'>
<tr>
<th>日期范围</th>
    <td>
        <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
               value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
               onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
        &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

        <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
               value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : date('Y-m-d 23:59:59', $endTime);?>'
               onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
        <input type="submit" value="查询">
    </td>
</tr>
<tr>
<th rowspan='2' style='text-align: right;'>平台渠道</th>
<td colspan="2" style='text-align:left;'>
<select name="platForms" id="platForms">
                <option value="0">全部</option>
			     <?php foreach ($platformList as $key => $value):?>
					 <option value="<?php echo $key; ?>" <?php if($_POST['platForms'] == $key) echo "selected";?>><?php echo $value.'('.$key.')'; ?></option>
				 <?php endforeach;?>
			</select>	
</td>
        </tr>
    </table>
</form>
<hr class="hr"/>
    <form name='datalist'>
        <table style="width:100%;">
            <caption>统计报表</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>后台直充</th>
                <th>支付宝(直充)</th>
                <th>微信(直充)</th>
                <th>苹果</th>
                <th>android</th>
                <th>直充统计</th>
                <th>直充比例</th>
                <th>总计</th>
                <th>充值人数</th>
                <th>活跃度</th>
                <th>订单数量</th>
                <th>ARPU</th>
            </tr>
            <?php
            if(!empty($result)){
                foreach ($result as $k => $v) {
                    $zhic = $v['resorce']['houtai'] + $v['resorce']['zfb']+$v['resorce']['wx'];
                    $bili = ($v['total'] == 0 ? 0 : number_format($zhic*100/$v['total'],2));
                    $houtai_t += $v['resorce']['houtai'];
                    $zfb_t += $v['resorce']['zfb'];
                    $wx_t += $v['resorce']['wx'];
                    $appstore_t += $v['resorce']['appstore'];
                    $android_t += $v['resorce']['android'];
                    $zhic_t += $zhic;
                    $bili_t += $bili;
                    $total_t += $v['total'];
                    $pnum_t += $v['pnum'];
                    $hyl_t += empty($login_num) ? 0 : number_format($v['total']/$login_num, 2);
                    $dnum_t += $v['dnum'];
                    $total_tt += (empty($v['pnum']) ? 0 : number_format($v['total']/$v['pnum'],2));
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td>' . $k . '</td>';
                    echo '<td>' . $v['resorce']['houtai'] . '</td>';
                    echo '<td>' . $v['resorce']['zfb'] . '</td>';
                    echo '<td>' . $v['resorce']['wx'] . '</td>';
                    echo '<td>' . $v['resorce']['appstore'] . '</td>';
                    echo '<td>' . $v['resorce']['android'] . '</td>';
                    echo '<td>' . $zhic.'</td>';
                    echo '<td>' . $bili.'%</td>';
                    echo '<td>' . $v['total'] . '</td>';
                    echo '<td>' . $v['pnum'] . '</td>';
                    echo '<td>' . (empty($login_num) ? 0 : number_format($v['total']/$login_num, 2)) . '</td>';
                    echo '<td>' . $v['dnum'] . '</td>';
                    echo '<td>' . (empty($v['pnum']) ? 0 : number_format($v['total']/$v['pnum'],2)) . '</td>';
                    echo '</tr>';
                }
                if(count($result) >1){
                    echo '<tr>';
                    echo '<th>总计</th>';
                    echo '<td>' . $houtai_t . '</td>';
                    echo '<td>' . $zfb_t . '</td>';
                    echo '<td>' . $wx_t . '</td>';
                    echo '<td>' . $appstore_t . '</td>';
                    echo '<td>' . $android_t . '</td>';
                    echo '<td>' . $zhic_t . '</td>';
                    echo '<td>' . number_format($bili_t/count($result)) . '%</td>';
                    echo '<td>' . $total_t . '</td>';
                    echo '<td>' . $pnum_t . '</td>';
                    echo '<td>' . $hyl_t . '</td>';
                    echo '<td>' . $dnum_t . '</td>';
                    echo '<td>' . $total_tt . '</td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
        <br><br>
       
         <table style="width:100%;">
             <?php 
        if(!empty($plat)){
            foreach ($plat as $key => $val) {
                 echo '<tr>';
                 echo '<td>'.$platformList[$key].'('.$key.')'.'</td>';
                 echo '<td>'.$val.'</td>';
            }
         }
        ?>
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
