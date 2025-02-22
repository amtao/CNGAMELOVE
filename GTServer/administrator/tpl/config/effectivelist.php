<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>


	<table class="mytable">
	    <tr><th colspan="20"><?php echo $SevidCfg['sevid'].'区活动生效列表';?></th></tr>
		<tr>
			<th>活动key</th><th>活动名称</th><th>活动类型</th><th>开始时间</th><th>结束时间</th><th>展示时间</th>
		</tr>
		<?php 
		      if(!empty($list)){
		          foreach ($list as $key => $val){
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
		              }
		              echo '<tr>';
		              echo '<td style="text-align:center;">'.$key.'</td>';
		              echo '<td style="text-align:center;">'.$val['title'].'</td>';
		              echo '<td style="text-align:center;">'.$type.'</td>';
		              echo '<td style="text-align:center;">'.date('Y-m-d H:i:s',$val['sTime']).'</td>';
		              echo '<td style="text-align:center;">'.date('Y-m-d H:i:s',$val['eTime']).'</td>';
		              echo '<td style="text-align:center;">'.date('Y-m-d H:i:s',$val['showTime']).'</td>';
		              echo '</tr>';
		          }
		      }else{
		          echo '<tr>';
		          echo '<td style="text-align:center;">暂无充值记录</td>';
		          echo '</tr>';
		      }
		
		?>

	</table>

	<BR>
    <div class="hero_div">
        <?php include(TPL_DIR.'footer.php');?>
    </div>
