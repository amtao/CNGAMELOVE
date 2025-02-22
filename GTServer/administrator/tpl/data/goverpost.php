<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
	<table style='width: 100%;'>
		<tr>
			<th style='text-align: right;'>区服</th>
			<td colspan="2" style='text-align:left;'>
				<select name="servid" id="servid">
					<?php if (empty($auth['ban']['data']['totalCX'])):?>
						<option value="0">全服</option>
					<?php endif;?>
					<?php foreach ($serverList as $key => $value):?>
						<option value="<?php echo $key; ?>" <?php if($_POST['servid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th style='text-align: right;'>缓存查询</th>
			<td colspan="2" style='text-align:left;'>
				<label><input name="type" type="radio" value="1" <?php echo $_POST['type'] == 1 ? 'checked="checked"' : '';?> />缓存查询</label>
				<label><input name="type" type="radio" value="2" <?php echo $_POST['type'] == 2 ? 'checked="checked"' : '';?> />实时查询</label>
				&nbsp;
				缓存KEY:<?php echo $cacheKey;?>，缓存有效期为2个小时
			</td>
		</tr>
		<tr>
			<th rowspan='2' style='text-align: right;'>平台渠道</th>
			<td colspan="2" style='text-align:left;'>
				<input type='button' id='allSelect' value='选中全部'>&nbsp;
				<input type='button' id='cancelAll' value='取消全选'>&nbsp;
				<input type='button' id='selectEven' value='游动全选'>&nbsp;
				<!--<input type='button' id='selectOdd' value='选中偶数'>&nbsp;-->
				<input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2" style='text-align:left;'>
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
			</td>
		</tr>
		<tr><th colspan="2"><input type="submit" value="确定查询" /></th></tr>
	</table>
</form>
<hr class="hr"/>
	<table style="width:100%;">
	    <tr><th colspan="50">身份分布</th></tr>
		<tr>
			<th>服务器</th><th>乞丐</th><th>平民</th><th>三等应钟御史</th><th>二等应钟御史</th><th>一等应钟御史</th><th>三等无射御史</th><th>二等无射御史</th><th>一等无射御史</th>
			<th>三等南吕御史</th><th>二等南吕御史</th><th>一等南吕御史</th><th>三等夷则御史</th><th>二等夷则御史</th><th>一等夷则御史</th><th>三等林钟御史</th><th>二等林钟御史</th>
			<th>一等林钟御史</th><th>三等蕤宾御史</th><th>二等蕤宾御史</th><th>一等蕤宾御史</th><th>三等仲吕御史</th><th>二等仲吕御史</th><th>一等仲吕御史</th><th>三等姑洗御史</th>
			<th>二等姑洗御史</th><th>一等姑洗御史</th><th>三等夹钟御史</th><th>二等夹钟御史</th><th>一等夹钟御史</th><th>三等太簇御史</th><th>二等太簇御史</th><th>一等太簇御史</th>
			<th>三等大吕御史</th><th>二等大吕御史</th><th>一等大吕御史</th><th>三等黄钟御史</th><th>二等黄钟御史</th><th>一等黄钟御史</th><th>三等摄政御史</th><th>二等摄政御史</th><th>一等摄政御史</th>
			<th>暂无</th>
		</tr>
<?php if($dataInfo):?>
		<?php foreach($dataInfo as $dk => $dv):  $total = $dv['total'];?>
		<tr style="background-color:#f6f9f3;">
			<td style="text-align:center;"><?php echo $dk.'区';?></td>
			<td style="text-align:center;"><?php echo (empty($dv[0]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[0].' | <span style="color:#9a121e;">'.number_format($dv[0]*100/$total,2).'%</span>'));?></td>
			<td style="text-align:center;"><?php echo (empty($dv[1]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[1].' | <span style="color:#9a121e;">'.number_format($dv[1]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[2]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[2].' | <span style="color:#9a121e;">'.number_format($dv[2]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[3]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[3].' | <span style="color:#9a121e;">'.number_format($dv[3]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[4]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[4].' | <span style="color:#9a121e;">'.number_format($dv[4]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[5]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[5].' | <span style="color:#9a121e;">'.number_format($dv[5]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[6]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[6].' | <span style="color:#9a121e;">'.number_format($dv[6]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[7]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[7].' | <span style="color:#9a121e;">'.number_format($dv[7]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[8]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[8].' | <span style="color:#9a121e;">'.number_format($dv[8]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[9]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[9].' | <span style="color:#9a121e;">'.number_format($dv[9]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[10]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[10].' | <span style="color:#9a121e;">'.number_format($dv[10]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[11]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[11].' | <span style="color:#9a121e;">'.number_format($dv[11]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[12]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[12].' | <span style="color:#9a121e;">'.number_format($dv[12]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[13]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[13].' | <span style="color:#9a121e;">'.number_format($dv[13]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[14]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[14].' | <span style="color:#9a121e;">'.number_format($dv[14]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[15]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[15].' | <span style="color:#9a121e;">'.number_format($dv[15]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[16]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[16].' | <span style="color:#9a121e;">'.number_format($dv[16]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[17]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[17].' | <span style="color:#9a121e;">'.number_format($dv[17]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[18]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[18].' | <span style="color:#9a121e;">'.number_format($dv[18]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[19]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[19].' | <span style="color:#9a121e;">'.number_format($dv[19]*100/$total,2).'%</span>'));?></td>
			<td style="text-align:center;"><?php echo (empty($dv[20]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[20].' | <span style="color:#9a121e;">'.number_format($dv[20]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[21]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[21].' | <span style="color:#9a121e;">'.number_format($dv[21]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[22]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[22].' | <span style="color:#9a121e;">'.number_format($dv[22]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[23]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[23].' | <span style="color:#9a121e;">'.number_format($dv[23]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[24]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[24].' | <span style="color:#9a121e;">'.number_format($dv[24]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[25]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[25].' | <span style="color:#9a121e;">'.number_format($dv[25]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[26]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[26].' | <span style="color:#9a121e;">'.number_format($dv[26]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[27]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[27].' | <span style="color:#9a121e;">'.number_format($dv[27]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[28]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[28].' | <span style="color:#9a121e;">'.number_format($dv[28]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[29]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[29].' | <span style="color:#9a121e;">'.number_format($dv[29]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[30]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[30].' | <span style="color:#9a121e;">'.number_format($dv[30]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[31]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[31].' | <span style="color:#9a121e;">'.number_format($dv[31]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[32]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[32].' | <span style="color:#9a121e;">'.number_format($dv[32]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[33]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[33].' | <span style="color:#9a121e;">'.number_format($dv[33]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[34]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[34].' | <span style="color:#9a121e;">'.number_format($dv[34]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[35]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[35].' | <span style="color:#9a121e;">'.number_format($dv[35]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[36]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[36].' | <span style="color:#9a121e;">'.number_format($dv[36]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[37]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[37].' | <span style="color:#9a121e;">'.number_format($dv[37]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[38]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[38].' | <span style="color:#9a121e;">'.number_format($dv[38]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[39]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[39].' | <span style="color:#9a121e;">'.number_format($dv[39]*100/$total,2).'%</span>'))?></td>
			<td style="text-align:center;"><?php echo (empty($dv[40]) ? '0 | <span style="color:#9a121e;" >0.00%</span>' : ($dv[40].' | <span style="color:#9a121e;">'.number_format($dv[40]*100/$total,2).'%</span>'))?></td>
            <td style="text-align:center;"><span style="color:#9a121e;">暂无</span>?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<BR>
<?php else:?>
	<table>
		<tr>
			<td>暂无信息</td>
		</tr>
	</table>
	<BR>
<?php endif;?>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
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