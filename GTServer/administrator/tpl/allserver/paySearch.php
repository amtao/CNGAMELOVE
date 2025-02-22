<?php include(TPL_DIR.'header.php');?>
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
<BR>
<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
	 <tr><th colspan="6">充值查询</th></tr>
	 <tr>
		<th style="text-align:right;">渠道：</th>
		<td style="text-align:left;">
			<select name="platForms" id="platForms">

			</select>	
		</td>
		<th style="text-align:right;">区服：</th>
		<td style="text-align:left;">
			<select name="serverid" id="serverid">
			</select>	
		</td>
		<th style="text-align:right;">时间：</th>
		<td style="text-align:left;">
			<input  class="Wdate" type="text" id="startTime" name="startTime"  value="<?php echo $_POST['startTime'];?>" onFocus="WdatePicker({isShowClear:true,readOnly:true,maxDate:'%y-%M-#{%d}'})"/>
			&nbsp;&nbsp;到&nbsp;&nbsp;
			<input  class="Wdate" type="text" id="endTime" name="endTime" value="<?php echo $_POST['endTime'];?>" onFocus="WdatePicker({isShowClear:true,readOnly:true,maxDate:'%y-%M-#{%d}'})"/>
		</td>
     </tr>
  	 <tr>
		<th style="text-align:right;">角色ID：</th>
		<td style="text-align:left;" colspan="5">
		  <input type="text" id="uid" name="uid" value="<?php echo $_POST['uid'];?>"/>
		</td>
     </tr>
     <tr>
     	<th style="text-align:right;">来源:</th>
     	<td style="text-align:left;" colspan="1">
            <select name="elseFroms">
                <option value="0" <?php if($_POST["elseFroms"]==0):?> selected <?php endif;?>> 全部</option>
                <option value="NOT_ZHICHONG" <?php if($_POST["elseFroms"]=='NOT_ZHICHONG'):?> selected <?php endif;?>> 非直充</option>
                <option value="ADMIN_IN" <?php if($_POST["elseFroms"]=='ADMIN_IN'):?> selected <?php endif;?>> 后台直充（内）</option>
                <option value="ADMIN_OUT" <?php if($_POST["elseFroms"]=='ADMIN_OUT'):?> selected <?php endif;?>> 后台直充（外）</option>
                <option value="GWZHICHONG" <?php if($_POST["elseFroms"]=='GWZHICHONG'):?> selected <?php endif;?>> 官网直充</option>
            </select>
		</td>
		<th style="text-align:right;">支付:</th>
     	<td style="text-align:left;">
			<select name="pay_type">
				<option value="0" <?php if($_POST["pay_type"]==0):?> selected <?php endif;?>> 所有支付</option>
				<option value="1" <?php if($_POST["pay_type"]==1):?> selected <?php endif;?>> 支付宝</option>
				<option value="2" <?php if($_POST["pay_type"]==2):?> selected <?php endif;?>> 微信</option>
				<option value="3" <?php if($_POST["pay_type"]==3):?> selected <?php endif;?>> APPSTORE</option>
			</select>
		</td>
     </tr>
	 <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<BR>
<?php if($searchRecords):?>
	<table>
		<tr>
			<th>日期</th><th>订单号</th><th>平台</th><th>角色ID</th><th>充值金额(美元)</th><th>充值元宝</th><th>充值来源</th>
		</tr>
		<?php foreach($searchRecords as $perSearch):?>
		<tr>
			<td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$perSearch["ctime"]);?></td>
			<td style="text-align:center;"><?php echo $perSearch["orderid"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["platform"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["roleid"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["money"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["diamond"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["paytype"];?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<BR>
<?php else:?>
	<table>
		<tr>
			<td>暂无充值记录</td>
		</tr>
	</table>
	<BR>
<?php endif;?>