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
	 <tr><th colspan="8">充值查询</th></tr>
	 <tr>
		<th style="text-align:right;width: 50px;">角色ID：</th>
		<td style="text-align:left;" colspan="3">
		  <input type="text" id="uid" name="uid" value="<?php echo $_POST['uid'];?>"/>
		</td>
     <th style="text-align: right;">区服:(全服all,区间如1-2)</th>
     <td style="text-align:left;">
         <input type="text" name="server" value="<?php echo $_POST['server']?$_POST['server']:'all'?>">
     </td>
		<th style="text-align:right;width: 50px;">时间：</th>
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
     
	 <tr><th colspan="8"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<hr class="hr" />
<table>
    <tr>
        <th>日期</th>
        <th>金额</th>
    </tr>
    <?php if(!empty($total)){ ?>
    <?php foreach ($total as $key => $value): ?>
    <tr style="background-color:#f6f9f3;">
        <td style="text-align:center;"><?php echo $key;?></td>
        <td style="text-align:center;"><?php echo $value;?></td>
    </tr>
    <?php endforeach;?>
    <?php } ?>
     <tr style="background-color:#f6f9f3;">
        <td style="text-align:center;">总计</td>
        <td style="text-align:center;"><?php echo $all_money;?></td>
    </tr>
</table>
<br>
<table>
    <?php if($userInfo) :?>
        <?php $Act99Model = Master::getAct99($_POST['uid']);?>
        <tr>
            <th>游戏名</th><th>身份</th><th>VIP</th><th>元宝</th><th>势力</th><th>最后一次登录时间</th><th></th><th></th><th></th>
        </tr>
        <tr>
            <td style="text-align:center;"><?php echo $userInfo->info['name'];?></td><td style="text-align:center;"><?php echo $userInfo->info['level'];?></td><td style="text-align:center;"><?php echo $userInfo->info['vip'];?></td><td style="text-align:center;"><?php echo $userInfo->info['cash'];?></td><td style="text-align:center;"><?php echo array_sum($Act99Model->info['ep']);?></td><td style="text-align:center;"><?php echo date("Y-m-d H:i:s", $userInfo->info['lastlogin']);?></td><td></td><td></td><td></td>
        </tr>
    <?php endif;?>
    <tr>
        <th>序号</th>
        <th>游戏订单号</th>
        <th>角色ID</th>
        <th>openid</th>
        <th>平台标识</th>
        <th>充值金额(元)</th>
        <th>兑换游戏币</th>
        <th>兑换礼包</th>
        <th>支付时间</th>
        <th>支付方式</th>
    </tr>
    <tr>
        <th style="text-align:center;">总计</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="text-align:center;"><?php echo $all_money;?></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    <?php if(!empty($list)){ ?>
    <?php foreach ($list as $k =>  $info): ?>
    <tr>
        <td style="text-align:center;"><?php echo $k+1;?></td>
        <td style="text-align:center;"><?php echo $info['orderid'];?></td>
        <td style="text-align:center;"><?php echo $info["roleid"];?></td>
        <td style="text-align:center;"><?php echo $info["openid"];?></td>
        <td style="text-align:center;"><?php echo $info["platform"];?></td>
        <td style="text-align:center;"><?php echo $info["money"];?></td>
        <td style="text-align:center;"><?php echo $info["diamond"];?></td>
        <td style="text-align:center;"><?php echo $info["diamondType"];?></td>
        <td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$info["ptime"]);?></td>
    	<td style="text-align:center;"><?php echo $info["paytype"];?></td>
    </tr>
    <?php endforeach;?>
    <?php } ?>
    
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
