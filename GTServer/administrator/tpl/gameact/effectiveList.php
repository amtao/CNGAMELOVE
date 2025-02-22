<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<form name="form1" method="POST" action="">
	<table style="width: 90%;">
		<tr><th colspan="6">区服活动</th></tr>
		<tr>
			<th style="text-align:right;">服务器ID：</th>
			<td style="text-align:left;">
				<?php
				echo "<select name='selectSevID'><option value='' >选择</option>";
				foreach ($serverList as $val){
					if(is_numeric($_REQUEST['selectSevID']) && $_REQUEST['selectSevID'] == $val['id']) {
						$selected = "selected='selected'";
					}else{
						$selected = "";
					}
					echo "<option value='{$val['id']}' {$selected} >{$val['id']}-{$val['name']['zh']}</option>";
				}
				echo "</select>";
				?>
				&nbsp;&nbsp;&nbsp;<input type="submit" value="确定查询" />
			</td>
		</tr>
	</table>
</form>
<?php if (isset($_REQUEST['selectSevID'])):?>
	<table style="width: 90%;" class="mytable">
		<?php foreach ($lists as $lists_v):
			$list = $lists_v['list'];
			if (empty($list)) {continue;}
			?>
	    <tr><th colspan="20"><?php echo $_REQUEST['selectSevID'].$lists_v['title'].'|起始时间：'.$lists_v['date'];?></th></tr>
		<tr>
			<th>活动key</th><th>活动名称</th><th>活动ID</th><th>活动类型</th><th>开始时间</th><th>结束时间</th><th>展示时间</th><th>生效情况</th><th>展示情况</th><th>覆盖编号</th>
		</tr>
		<?php
              $nowtime =strtotime(date('Y-m-d 23:00:00',$_SERVER['REQUEST_TIME']));
		      if(!empty($list)){
		          foreach ($list as $key => $val){
                      $red = '';
                      if ($val['showTime']>$nowtime){
                          $diff = $val['showTime']-$nowtime;
                          if ($diff<86400){
                              $red = 'color: red;';
                          }

                      }
		              //1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼7:新官上任
		              switch ($val['type']){
		                  case 1:
		                      $type = '普通活动';
		                      break;
                          case 2:
                              $type = '限时活动';
                              break;
                          case 3:
                              $type = '冲榜活动';
                              break;
                          case 4:
                              $type = '充值活动';
                              break;
                          case 5:
                              $type = '奸臣活动';
                              break;
                          case 6:
                              $type = '巾帼活动';
                              break;
                          case 7:
                              $type = '新官上任';
                              break;
						  case 8:
							  $type = '乱党';
							  break;
		              }
		              echo '<tr>';
		              echo '<td style="text-align:center;'.$red.'">'.$key.'</td>';
		              echo '<td style="text-align:center;'.$red.'">'.$val['title'].'</td>';
                      echo '<td style="text-align:center;'.$red.'">'.$val['hid'].'</td>';
		              echo '<td style="text-align:center;'.$red.'">'.$type.'</td>';
		              echo '<td style="text-align:center;'.$red.'">'.date('Y-m-d H:i:s',$val['sTime']).'</td>';
		              echo '<td style="text-align:center;'.$red.'">'.date('Y-m-d H:i:s',$val['eTime']).'</td>';
		              echo '<td style="text-align:center;'.$red.'">'.date('Y-m-d H:i:s',$val['showTime']).'</td>';
					  echo '<td>——</td>';
					  echo '<td>——</td>';
					  echo '<td>——</td>';
		              echo '</tr>';
		          }
		      }else{
		          echo '<tr>';
		          echo '<td style="text-align:center;" colspan="20">暂无记录</td>';
		          echo '</tr>';
		      }
		
		?>
		<?php endforeach;?>
		<tr><th colspan="20"><?php echo $_REQUEST['selectSevID'].'区活动审核生效列表';?></th></tr>
		<tr>
			<th>活动key</th><th>活动名称</th><th>活动ID</th><th>活动类型</th><th>开始时间</th><th>结束时间</th><th>展示时间</th><th>生效情况</th><th>展示情况</th><th>覆盖编号</th>
		</tr>
		<?php
		if(!empty($allList)){
			function getTimeColor($status) {
				$colors = array(
					1 => '<span style="color:red;">时间未到</span>',
					2 => '<span style="color:green;">生效中</span>',
					3 => '<span style="color:gray;">过期</span>',
					4 => '<span style="color:#a2a245;">生效被覆盖</span>',
				);
				return $colors[$status];
			}
			$jj = 0;
			foreach ($allList as $key => $allListVal) {
				$style = $jj % 2 == 0 ? '' : 'background-color: #f0f8ff;';
				foreach ($allListVal as $allListVal_key => $allListVal_val) {
					foreach ($allListVal_val as $allListVal_val_val) {
						$val = $allListVal_val_val['info'];
						//1:普通活动 2:限时活动 3:冲榜活动 4:充值活动 5:奸臣 6:巾帼7:新官上任
						switch ($val['type']) {
							case 1:
								$type = '普通活动';
								break;
							case 2:
								$type = '限时活动';
								break;
							case 3:
								$type = '冲榜活动';
								break;
							case 4:
								$type = '充值活动';
								break;
							case 5:
								$type = '奸臣活动';
								break;
							case 6:
								$type = '巾帼活动';
								break;
							case 7:
								$type = '新官上任';
								break;
							case 8:
								$type = '乱党';
								break;
						}
						echo '<tr>';
						echo '<td style="text-align:center;'.$style.'">' . $key . '</td>';
                        echo '<td style="text-align:center;'.$style.'">'.$val['title'].'</td>';
                        echo '<td style="text-align:center;'.$style.'">'.$val['hid'].'</td>';
						echo '<td style="text-align:center;'.$style.'">' . $type . '</td>';
						echo '<td style="text-align:center;'.$style.'">' . date('Y-m-d H:i:s', $val['sTime']) . '</td>';
						echo '<td style="text-align:center;'.$style.'">' . date('Y-m-d H:i:s', $val['eTime']) . '</td>';
						echo '<td style="text-align:center;'.$style.'">' . date('Y-m-d H:i:s', $val['showTime']) . '</td>';
						echo '<td style="text-align:center;'.$style.'">' . getTimeColor($val['status']) . "（{$allListVal_key}|{$allListVal_val_val['server']}）" . '</td>';
						echo '<td style="text-align:center;'.$style.'">' . getTimeColor($val['showStatus']) . '</td>';
						echo '<td style="text-align:center;'.$style.'">' . implode(',', $val['existsIDs']) . '</td>';
						echo '</tr>';
					}
				}
				$jj++;
			}
		}else{
			echo '<tr>';
			echo '<td style="text-align:center;" colspan="20">暂无记录</td>';
			echo '</tr>';
		}

		?>
	</table>
<?php endif;?>

<?php include(TPL_DIR.'footer.php');?>