<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>
<?php
$postServer = isset($_POST['server']) ? trim($_POST['server']) : '';
$oldPostServer = $postServer;

$eqServer = strpos($postServer, '=') !== false;
$postServer = ltrim($postServer, '=');
$postActKey = isset($_POST['act_key']) ? trim($_POST['act_key']) : '';
$postActId = isset($_POST['id']) ? trim($_POST['id']) : '';
?>
<table style='width:100%;' class="mytable">
	<tr>
		<th colspan="9">活动列表（最后修改时间：<?php echo date("Y-m-d H:i:s", $lastChangeVer);?>）</th>
	</tr>
	<tr>
		<td colspan="9">
			<form id="formid" method="POST" action="" >
				<input type="hidden"  name='flag' value='1' />
				查询区服：<input type='text' size='40' id='server' name='server' value='<?php echo $oldPostServer;?>' />
				活动编号：<input type='text' size='40' id='act_key' name='act_key' value='<?php echo $postActKey;?>' />
				活动序号：<input type='text' size='40' id='id' name='id' value='<?php echo $postActId;?>' />
				<input type="submit" value="查询">
			</form>
		</td>
	</tr>
	<tr>
		<th width="5%">ID</th>
		<th width="8%">活动区服</th>
		<th width="8%">活动编号</th>
		<th>HID</th>
		<th>Day开始-结束时间</th>
		<th>Time开始-结束时间</th>
		<th>审核状态</th>
		<th>审核人</th>
		<th>操作</th>
	</tr>
	<tr>
		<td colspan="8" style="text-align: left;">
            <?php
            if ($_GET['auditType']!=1){
                echo '
                <input type="button" name="checkAll" value="全选">
                <input type="button" name="nocheckAll" value="全不选">
				<input type="button" name="check" value="反选">
				<input type="button" name="exportExcel" value="一键导出">';
            }
			?>
			<input type="button" name="delete" value="确认删除">
		</td>
		<td>
			<a data-href="?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=batEditGameAct" value="" id="showTips" style="display: none;"></a>
			<a class='edit_all' href="#">批量编辑</a>
		</td>
	</tr>
	<?php
	$jj = 0;
	foreach ($list as $v) :
		if ($v['audit'] != $_GET['auditType']) {continue;}
		if ($postServer) {
			if ($postServer == 'all' && $v['server'] != 'all') {
				continue;
			}
			if ($eqServer) {
				if ($v['server'] != $postServer) {
					continue;
				}
			} else {
				$postServers = Game::serves_str_arr($postServer);
				$listServers = Game::serves_str_arr($v['server']);
				$sameServers = array_intersect($listServers, $postServers);
				if (empty($sameServers)) {
					continue;
				}
			}
		}
		if ($postActKey && 'huodong_'.$postActKey != $v['act_key']) {
			continue;
		}
		if($postActId && $postActId != $v['id']){
			continue;
		}
		?>
		<?php
		$ii = 0;
		$contentsArrNum = count($v['contentsArr']);
		$style = $jj % 2 == 0 ? '' : ($_GET['auditType']==1?'style="background-color: #ffc18166;"':'style="background-color: #f0f8ff;"');
		foreach ($v['contentsArr'] as $contents):?>
			<tr>
				<?php if ($ii == 0):?>
				<td rowspan="<?php echo ($contentsArrNum);?>" <?php echo $style;?>><input type="checkbox" name="id" value="<?php echo $v['id'];?>"><?php echo $v['id'];?></td>
				<td rowspan="<?php echo $contentsArrNum;?>" <?php echo $style;?>><?php echo $v['server'];?></td>
				<td rowspan="<?php echo $contentsArrNum;?>" <?php echo $style;?>><?php echo $v['act_key'].'<br />'.$v['contentsArr'][0]['info']['title'];?></td>
				<?php endif;?>
				<td <?php echo $style;?>><?php echo $contents['info']['id'];?></td>
				<td <?php echo $style;?>><?php echo $contents['info']['startDay']," 至 ",$contents['info']['endDay'];?></td>
				<td <?php echo $style;?>><?php echo $contents['info']['startTime']," 至 ",$contents['info']['endTime'];?></td>
				<?php if ($ii == 0):?>
				<td rowspan="<?php echo $contentsArrNum;?>" <?php echo $style;?>><?php echo $v['auditNote'];?></td>
				<td rowspan="<?php echo $contentsArrNum;?>" <?php echo $style;?>><?php echo $v['auser'];?></td>
				<td rowspan="<?php echo $contentsArrNum;?>" <?php echo $style;?>>
					<a id='edit_by_code_<?php echo $v['id'];?>' href='?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=editGameAct&id=<?php echo $v['id'];?>' >代码编辑</a><br>
					<a id='view_by_code_<?php echo $v['id'];?>' href='?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=viewGameAct&id=<?php echo $v['id'];?>' >代码查看</a><br>
					<?php if ($v['audit'] == 0):?>
						<input type='button' value='审核通过' onclick="auditPass('<?php echo $v['id'];?>')" /><br>
						<input type='button' value='审核不通过' onclick="auditNoPass('<?php echo $v['id'];?>')" /><br>
					<?php endif;?>
					<input type='button' value='删除' onclick="delSever('<?php echo $v['id'];?>')" />
				</td>
				<?php endif;?>
			</tr>
		<?php
		$ii++;
		endforeach;?>
	<?php
	$jj++;
	endforeach;?>
	<tr>
		<td colspan="8" style="text-align: left;">
			<input type="button" name="checkAll" value="全选">
			<input type="button" name="nocheckAll" value="全不选">
			<input type="button" name="check" value="反选">
			<input type="button" name="exportExcel" value="一键导出">
			<input type="button" name="delete" value="确认删除">
		</td>
		<td>
			<a class='edit_all' href="#">批量编辑</a>
		</td>
	</tr>
</table>

<script type="text/javascript">
	function auditPass(id) {
		if ( window.confirm('确认审核通过?') ) {
			location.href = '?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=passList&auditType=0&audit=1&id=' + id;
		}
	}
	function auditNoPass(id) {
		if ( window.confirm('确认审核不通过?') ) {
			location.href = '?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=passList&auditType=0&audit=2&id=' + id;
		}
	}
	function delSever(id) {
        var sevid = <?php echo $_GET['sevid'];?>;
		if ( window.confirm('确认删除?') ) {
            if (sevid =='999'){
                location.href = '?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=passList&auditType=<?php echo $_GET['auditType'];?>&del=1&id=' + id
            }else {
                var person=prompt("请输入验证密码!");
                if (person!=null && person!=""){
                    location.href = '?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=passList&auditType=<?php echo $_GET['auditType'];?>&del=1&id=' + id +'&password='+ person;
                }
            }

		}
	}

	var jquery = jQuery.noConflict(true);

	jquery(document).ready(function() {
		$(':input[name="check"]').click(function () {
			$(':input[name="id"]').each(function () {
				if ($(this).attr('checked') == true) {
					$(this).attr('checked',false);
				}else {
					$(this).attr('checked',true);
				}
			});
		});
		$(':input[name="checkAll"]').click(function () {
			$(':input[name="id"]').each(function () {
				$(this).attr('checked',true);
			});
		});
		$(':input[name="nocheckAll"]').click(function () {
			$(':input[name="id"]').each(function () {
				$(this).attr('checked',false);
			});
		});
		$(':input[name="exportExcel"]').click(function(){
            var id = getSelectID();
            console.log(id);
            if (id == '') {
                alert('请选择要导出的活动');
                return false;
            }
            if ( window.confirm('一键导出活动?') ) {
                location.href = '?sevid=<?php echo $_GET['sevid']?>&mod=gameAct&act=exportActivity&export=1&id='+id;
            }
        });
		<?php foreach ($list as $value):?>
		jquery("#edit_by_code_<?php echo $value['id'];?>").fancybox({
			'width'				: '95%',
			'height'			: '100%',
			'autoScale'			: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onStart'  : function() {
				$('body').css('overflow', 'hidden');
			},
			'onClosed' : function() {
				$('body').css('overflow', 'scroll');
				//window.location.href = window.location.href;
			}
		});
		jquery("#view_by_code_<?php echo $value['id'];?>").fancybox({
			'width'				: '95%',
			'height'			: '100%',
			'autoScale'			: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onStart'  : function() {
				$('body').css('overflow', 'hidden');
			},
			'onClosed' : function() {
				$('body').css('overflow', 'scroll');
				//window.location.href = window.location.href;
			}
		});
		<?php endforeach;?>
		function getSelectID()
		{
			var id = '';
			$(':input[name="id"]').each(function () {
				if ($(this).attr('checked')) {
					if(id == ''){
						id = $(this).val();
					}else {
						id = id +'-' + $(this).val();
					}
				}
			});
			return id;
		}
		$(':input[name="delete"]').click(function () {
			var id = getSelectID();
			console.log(id);
			if (id == '') {
				alert('请选择要删除的活动');
				return false;
			}
			delSever(id);

		});
		jquery('.edit_all').click(function () {
			var id = getSelectID();
			if (id == '') {
				alert('请选择活动');
				return;
			}
			jquery('#showTips').attr('href', $('#showTips').attr('data-href') + '&id='+id);
			jquery('#showTips').fancybox({
				'width'				: '95%',
				'height'			: '100%',
				'autoScale'			: false,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe',
				'onStart'  : function() {
					$('body').css('overflow', 'hidden');
				},
				'onClosed' : function() {
					$('body').css('overflow', 'scroll');
					//window.location.href = window.location.href;
				}
			});
			jquery('#showTips').click();
		});
	});
</script>
<?php include(TPL_DIR.'footer.php');?>