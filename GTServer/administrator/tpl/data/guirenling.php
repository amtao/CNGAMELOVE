<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
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


<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
	 <tr><th colspan="6">充值查询</th></tr>
	 <tr>
	 	<th style="text-align:right;width: 50px;">区服：</th>
        <td style="text-align:left;">
            <select name="serverid" id="serverid">
                 <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
                 <?php foreach ($serverList as $key => $value):?>
                     <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
                 <?php endforeach;?>
            </select>
        </td>
		<th style="text-align:right;">时间：</th>
        <td style="text-align:left;">
            <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                    value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
                    onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
             &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

            <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                    value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : date('Y-m-d 23:59:59', $endTime);?>'
                    onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
        </td>
     </tr>
     
	 <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<hr class="hr" />
<?php
echo '总计: '.(empty($people)?0:$people).'人            '.(empty($total)?0:$total).'次';?>
<table>
    <tr>
        <th>日期</th>
        <th>次数</th>
    </tr>
    <?php foreach ($list as $key => $value): ?>
    <tr style="background-color:#f6f9f3;">
        <td style="text-align:center;"><?php echo $key;?></td>
        <td style="text-align:center;"><?php echo $value;?></td>
    </tr>
    <?php endforeach;?>
</table>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
