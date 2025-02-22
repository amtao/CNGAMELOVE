<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<?php include(TPL_DIR.'user/flow_head.php');?>
<hr class="hr"/>
<style type="text/css">
    .green{color:green;}
	.red{color:red;}
	.blue{color:blue;}
	table{width:85%;border-color: #f9f0f0}
</style>
<script type="text/javascript">
	function checkForm(){
		if(document.tradingSearchForm.startTime.value.length == 0){
			alert("请选择起始时间");
			document.tradingSearchForm.startTime.focus();
			return false;
		}
		if(document.tradingSearchForm.endTime.value.length == 0 ){
			alert("请选择结束时间");
			document.tradingSearchForm.endTime.focus();
			return false;
		}
		
		if( (document.tradingSearchForm.startTime.value.length > 0) && (document.tradingSearchForm.endTime.value.length > 0 )){
			var startTime = document.tradingSearchForm.startTime.value; 
			var startTimeJsArray = startTime.split("-");
			var startTimeJsMake = new Date(startTimeJsArray[0],startTimeJsArray[1],startTimeJsArray[2]);
			var startTimeJs = startTimeJsMake.getTime();

			var endTime = document.tradingSearchForm.endTime.value; 
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

<form onsubmit="return checkForm();" action="" method="POST" name="tradingSearchForm">
<input type="hidden" value="1" name="tradingSearch">
<input type="hidden" name="uid" value="<?php echo $_REQUEST['uid'];?>"></input>
<table>
	 <tbody><tr><th colspan="2">玩家流水</th></tr>
	 <tr>
		<th style="text-align:right;">日期：</th>
		<td style="text-align:left;">
		  <input  class="Wdate" type="text" id="startTime" name="startTime"  value="<?php echo $_REQUEST['startTime'];?>" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd',readOnly:true,maxDate:'%y-%M-#{%d}'})"/>
		  &nbsp;&nbsp;到&nbsp;&nbsp;
		  <input  class="Wdate" type="text" id="endTime" name="endTime" value="<?php echo $_REQUEST['endTime'];?>" onFocus="WdatePicker({isShowClear:true,dateFmt:'yyyy-MM-dd',readOnly:true,maxDate:'%y-%M-#{%d}'})"/>
		</td>
     </tr>
 	 <tr>
		<th style="text-align:right;">类型：</th>
		<td style="text-align:left;">
			<select name="type" id="type">
				<option value="">所有</option>
				<?php /*foreach(FlowUserModel::needFlowTypes() as $k => $v): */?><!--
				<option value="<?php /*echo $k;*/?>" <?php /*echo $_REQUEST['type'] == $k ? "selected" : "";*/?>><?php /*echo $v;*/?></option>
				--><?php /*endforeach;*/?>
			</select>
		</td>
     </tr>  
	 <tr><th colspan="2"><input type="submit" value="确定查询"></th></tr>
	</tbody>
</table>
</form>

<br />

<?php if ($_REQUEST):?>
<table>
	<tr><th colSpan="20">流水信息</th></tr>
	<tr>
		<th rowSpan="2">操作者</th><th rowSpan="2">操作来源</th><th rowSpan="2">物品类型</th><th rowSpan="2">增删改</th><th colSpan="3">数量</th><th rowSpan="2">时间</th><th rowSpan="2">IP</th><th rowSpan="2">EXT</th>
	</tr>
	<tr>
		<th class="green">变前</th><th class="red">差值</th><th class="blue">变后</th>
	</tr>
	<?php if (empty($data)) :?>
	<tr><td colSpan="20" align="center">无记录</td></tr>
	<?php endif;?>
	<?php 
	$msgName = Common::getConfig('msg_name');
	foreach ($data as $v):?>
	<tr>
		<td <?php echo $v['user'] === '0' ? '' : 'class="red"';?>><?php echo $v['user'] === '0' ? '玩家' : $v['user'];?></td>
		<td><?php echo $msgName[$v['from']],'(',$v['from'],')';?></td>
		<td><?php echo FlowUserModel::getFlowTypeName($v['type']);?></td>
		<td><?php 
		if ($v['val'] > $v['newVal']) {
			echo "减少";
		} else if ($v['val'] < $v['newVal']) {
			echo "增加";
		} else {
			echo "不变";
		}
		?></td>
		<td class="green"><?php echo $v['val'];?></td>
		<td class="red"><?php echo $v['newVal'] - $v['val'];?></td>
		<td class="blue"><?php echo $v['newVal'];?></td>
		<td><?php echo date("Y-m-d H:i:s", $v['time']);?></td>
		<td><?php echo $v['ip'];?></td>
		<td><a href="?mod=flow&act=flowUserDetail&uid=<?php echo $uid;?>&flowid=<?php echo $v['id'];?>" target="_blank">查看</a></td>
	</tr>
	<?php endforeach;?>
</table>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>
