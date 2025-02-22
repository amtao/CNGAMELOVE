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
<form id="form-search" method="POST" action="" >
<table style="width: 100%">
    <tr><th colspan="6">充值排序查询</th></tr>
    <tr>
        <th style="text-align:right;width: 50px;">渠道：</th>
        <td style="text-align:left;">
            <select name="platForms" id="platForms">
                <option value="all" <?php if($_POST['platForms'] == "all") echo "selected";?>>全部</option>
                <?php foreach ($platformList as $key => $value):?>
                    <option value="<?php echo $key; ?>" <?php if($_POST['platForms'] == $key) echo "selected";?>><?php echo $value.'('.$key.')'; ?></option>
                <?php endforeach;?>
            </select>
        </td>
        <th style="text-align:right;width: 50px;">区服：</th>
        <td style="text-align:left;">
            <input type="text" name="serverid" value="<?php echo $serverid?$serverid:'all'; ?>">(单服如1表示1服，区间如1-100,所有填all)
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
        </td>
    </tr>
	 <tr><th colspan="6"><input type="submit" value="确定查询" /> | <input name="excel" id="excel-input" type="hidden" value="0"><input id="excel-submit" type="button" value="下载excel"></th></tr>
</table>
</form>
<BR>

<hr class="hr" />
<table style="width: 100%">
    <tr>
        <th colspan="8">玩家充值排行</th>
    </tr>
    <tr>
        <th>序号</th>
        <th>玩家</th>
        <th>名称</th>
        <th>vip</th>
        <th>身份</th>
        <th>金额</th>
        <th>当前元宝</th>
        <th>最后登录</th>
    </tr>
    <?php if(!empty($momeyInfo)){ $i = 1; ?>

        <?php foreach ($momeyInfo as $key => $value){

            $userModel = new UserModel($key);
            echo '<tr style="background-color:#f6f9f3;"><td style="text-align:center;">'.$i.'</td>';
            echo '<td style="text-align:center;"><a href="?sevid='.$SevidCfg['sevid'].'&mod=data&act=userPayInfo&uid='.$key.'">'.$key.'</a></td>';
            echo '<td style="text-align:center;">'.$value['name'].'</td>';
            echo '<td style="text-align:center;">'.$value['vip'].'</td>';
            echo '<td style="text-align:center;">'.$value['level'].'</td>';
            echo '<td style="text-align:center;">'.$value['money'].'</td>';
            echo '<td style="text-align:center;">'.$value['cash'].'</td>';
            echo '<td style="text-align:center;">'.date("Y-m-d H:i:s",$value['lastlogin']).'</td></tr>';
            $i++;
            } ?>
    <?php } ?>
</table>
<br>
<hr class="hr" />
<table style="width: 100%">
    <tr>
        <th>日期</th>
        <th>金额</th>
    </tr>
    <?php if(!empty($total)){ ?>
        <?php foreach ($total as $key => $value): ?>
            <tr>
                <td style="text-align:center;"><?php echo $key;?></td>
                <td style="text-align:center;"><?php echo $value;?></td>
            </tr>
        <?php endforeach;?>
    <?php } ?>
    <tr>
        <td style="text-align:center;">总计</td>
        <td style="text-align:center;"><?php echo $all_money;?></td>
    </tr>
</table>
<br>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
<script>
    $(document).ready(function () {
        $("#excel-submit").click(function () {
            $("#excel-input").val(1);
            $("#form-search").submit();
        });
    });
</script>