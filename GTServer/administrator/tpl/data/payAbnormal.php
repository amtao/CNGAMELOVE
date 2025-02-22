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
    <tr><th colspan="6">条件(金额别太小至少100)</th></tr>
    <tr>
        <th style="text-align:right;width: 150px;">身份不大于:</th>
        <td style="text-align:left;">
            <input type="text" name="level" value="<?php echo $level;?>" />
        </td>
    </tr>
    <tr>
        <th style="text-align:right;width: 150px;">充值金额大于:</th>
        <td style="text-align:left;">
            <input type="text" name="money" value="<?php echo $money;?>" />
        </td>
    </tr>
	 <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<hr class="hr" />
<table style="width: 100%">
    <tr>
        <th colspan="10">玩家充值异常</th>
    </tr>
    <tr>
        <th>序号</th>
        <th>玩家</th>
        <th>名称</th>
        <th>vip</th>
        <th>身份</th>
        <th>金额</th>
        <th>当前元宝</th>
        <th>平台</th>
        <th>注册时间</th>
        <th>最后登录</th>
    </tr>
    <?php if(!empty($user)){ $i = 1; ?>
        <?php foreach ($user as $key => $value){
                $userModel = new UserModel($key);
                if ($userModel->info['level'] < $level && $userModel->info['vip'] < $vip){
                    echo '<tr style="background-color:#f6f9f3;"><td style="text-align:center;">'.$i.'</td>';
                    echo '<td style="text-align:center;"><a href="?sevid='.$SevidCfg['sevid'].'&mod=data&act=userPayInfo&uid='.$key.'">'.$key.'</a></td>';
                    echo '<td style="text-align:center;">'.$userModel->info['name'].'</td>';
                    echo '<td style="text-align:center;">'.$userModel->info['vip'].'</td>';
                    echo '<td style="text-align:center;">'.$userModel->info['level'].'</td>';
                    echo '<td style="text-align:center;">'.$value.'</td>';
                    echo '<td style="text-align:center;">'.$userModel->info['cash'].'</td>';
                    echo '<td style="text-align:center;">'.$platformList[$userModel->info['platform']].'</td>';
                    echo '<td style="text-align:center;">'.date("Y-m-d H:i:s", $userModel->info['regtime']).'</td>';
                    echo '<td style="text-align:center;">'.date("Y-m-d H:i:s", $userModel->info['lastlogin']).'</td></tr>';
                    $i++;
                }

            } ?>
    <?php } ?>
</table>
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