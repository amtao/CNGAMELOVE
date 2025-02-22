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
	总计：<?php echo $total.'元';?>
<table style="width:100%;">
	 <tr><th colspan="2">充值查询</th></tr>
	 <tr>
		<th style="text-align:right;width: 50px;">渠道：</th>

		<td style="text-align:left;">
			<input type='button' id='show' value='显示'>&nbsp;
			<hr class="hr"/>
			<div id="channel" style="display: none;">
			 <input type='button' id='allSelect' value='选中全部'>&nbsp;
			 <input type='button' id='cancelAll' value='取消全选'>&nbsp;
			 <input type='button' id='selectEven' value='游动全选'>&nbsp;
			 <!--<input type='button' id='selectOdd' value='选中偶数'>&nbsp;-->
			 <input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
		 <hr class="hr"/>
			<?php
			$tmp = 0;
			$you = 0;
			$ios = 0;
			$checkboxHtml = '';
			$youdongHtml = '';
			$iosdongHtml = '';
			$youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
			if(!empty($platformList)){
				foreach ($platformList as $k => $v) {

					if(empty($channels)){
						$channels = array();
					}
					$isChecked = in_array($k, $channels) ? 'checked' : '';
					if (!in_array($k,$youdongPlat['android']) && !in_array($k,$youdongPlat['ios'])){
						$tmp++;
						$brSting = ($tmp%5) ? '' : '<br/>';
						$checkboxHtml .= sprintf("<input type='checkbox' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $brSting);
					}elseif(in_array($k,$youdongPlat['android'])){
						$you++;
						$youSting = ($you%5) ? '' : '<br/>';
						$youdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $youSting);
					}elseif(in_array($k,$youdongPlat['ios'])){
						$ios++;
						$iosSting = ($ios%5) ? '' : '<br/>';
						$iosdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $iosSting);
					}


				}
			}
			echo $checkboxHtml;
			if ($youdongHtml != ''){
				echo '<hr class="hr"/>';
				echo $youdongHtml;
			}
			if ($iosdongHtml != ''){
				echo '<hr class="hr"/>';
				echo $iosdongHtml;
			}
			?>
			</div>
		</td>
	</tr>
	<tr><th>区服选择*:</th><td><input type="text" name="server" style="width: 400px;" size="120" id="title" value="<?php echo isset($_POST['server']) ? $_POST['server'] : 'all';?>">(默认all表示所有服,连续服用"-"隔开如1-20)</td></tr>
	<tr>
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
  	 <tr>
		<th style="text-align:right;width: 50px;">角色ID：</th>
		<td style="text-align:left;" colspan="3">
		  <input type="text" id="uid" name="uid" value="<?php echo $_POST['uid'];?>"/>
		</td>
	 </tr>
	<tr>
		<th style="text-align:right;width: 50px;">档次:</th>
     	<td style="text-align:left;" colspan="1">
            <select name="item">
                <option value="0"><?php echo '全部'; ?></option>
                    <?php foreach ($item as $key => $value):?>
                        <option value="<?php echo $key; ?>" <?php if($_POST['item'] == $key) echo "selected";?>><?php echo $value['name'].$value['doller']."元"; ?></option>
                    <?php endforeach;?>
            </select>
            (G开头为礼包)
		</td>
     </tr>
     <tr>
        <th style="text-align:right;width: 100px;">查询类型：</th>
        <td style="text-align:left;" colspan="3">
            <input type="radio" name="stype" value="1"<?php if(empty($_POST['stype']) || $_POST['stype'] == 1) echo "checked";?>/> 全部
            <input type="radio" name="stype" value="2"<?php if($_POST['stype'] == 2) echo "checked";?>/> 新增玩家首日消费
        </td>
    </tr>
     
	 <tr><th colspan="2"><input type="submit" name="search" value="确定查询" /><input type="submit" value="下载excel" name="excel" /></th></tr>
</table>
</form>
	<hr class="hr"/>
<?php if($list):?>
	<table style="width:100%;">
        <?php if($userInfo) :?>
            <?php $Act99Model = Master::getAct99($_POST['uid']);?>
            <tr>
                <th>游戏名</th><th>身份</th><th>VIP</th><th>元宝</th><th>势力</th><th>最后一次登录时间</th><th></th><th></th>
            </tr>
            <tr>
                <td><?php echo $userInfo->info['name'];?></td><td><?php echo $userInfo->info['level'];?></td><td><?php echo $userInfo->info['vip'];?></td><td><?php echo $userInfo->info['cash'];?></td><td><?php echo array_sum($Act99Model->info['ep']);?></td><td><?php echo date("Y-m-d H:i:s", $userInfo->info['lastlogin']);?></td><td></td><td></td>
            </tr>
        <?php endif;?>
		<tr>
			<th>日期</th><th>订单号</th><th>平台订单号</th><th>平台</th><th>openID</th><th>角色ID</th><th>充值金额(美元)</th><th>充值元宝</th><th>注册时间</th><th>vip</th><th>总充值金额</th>
		</tr>
		<?php foreach($list as $perSearch):?>
		<tr style="background-color:#f6f9f3;">
			<td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$perSearch["ptime"]);?></td>
			<td style="text-align:center;"><?php echo $perSearch["orderid"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["tradeno"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["platform"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["openid"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["roleid"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["money"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["diamond"];?></td>
			<td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$perSearch["regtime"]);?></td>
			<td style="text-align:center;"><?php echo $perSearch["vip"];?></td>
			<td style="text-align:center;"><?php echo $perSearch["pay"];?></td>
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
<script>
	$(document).ready(function () {
		$("#excel-submit").click(function () {
			$("#excel-input").val(1);
			$("#form-search").submit();
		});
		$("#excel-submit-all").click(function () {
			$("#excel-input-all").val(1);
			$("#form-search").submit();
		});
		// 全选
		$('#allSelect').click(function(){
			$("input[name='channels[]']").attr('checked', 'true');
		});
		// 渠道显示切换
		$('#show').click(function(){
			if ($(this).val() == "显示"){
				$("#channel").show();
				$(this).val("隐藏");
			}else {
				$("#channel").hide();
				$(this).val("显示");
			}
		});

		// 全不选
		$('#cancelAll').click(function(){
			$("input[name='channels[]']").removeAttr('checked');
		});

		//选中游动
		$('#selectEven').click(function(){
			$(".youdong").attr('checked', 'true');
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
