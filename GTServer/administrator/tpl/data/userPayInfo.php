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
    <tr><th colspan="6">玩家充值详情查询</th></tr>
    <tr>
        <th style="text-align:right;">UID：</th>
        <td style="text-align:left;">
            <input type="text" name="uid" value="<?php echo $uid;?>" />
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
        </td>
    </tr>
	 <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<hr class="hr" />
<table>
    <tr>
        <th colspan="9">玩家充值详情</th>
    </tr>
    <tr>
        <th>玩家UID</th>
        <th>名称</th>
        <th>VIP</th>
        <th>身份</th>
        <th>订单ID</th>
        <th>订单名称</th>
        <th>金额</th>
        <th>平台</th>
        <th>支付类型</th>
        <th>时间</th>
    </tr>
    <?php if(!empty($data)){ ?>
        <?php foreach ($data as $key => $value): ?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $uid;?></td>
                <td style="text-align:center;"><?php echo $userModel->info['name'];?></td>
                <td style="text-align:center;"><?php echo $userModel->info['vip'];?></td>
                <td style="text-align:center;"><?php echo $guan[$userModel->info['level']]['name'];?></td>
                <td style="text-align:center;"><?php echo $value["orderid"];?></td>
                <td style="text-align:center;"><?php echo $value["gift_bag"];?></td>
                <td style="text-align:center;"><?php echo $value["money"];?></td>
                <td style="text-align:center;"><?php echo $value["platform"];?></td>
                <td style="text-align:center;"><?php echo $value["paytype"];?></td>
                <td style="text-align:center;"><?php echo date('Y-m-d H:i:s', $value["ptime"]);?></td>
            </tr>
        <?php endforeach;?>
    <?php } ?>
</table>
<br>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
