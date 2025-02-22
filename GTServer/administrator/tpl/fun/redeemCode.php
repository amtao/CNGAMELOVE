<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<div id='addDiv'>
	<form id='addForm' method='post' action=''>
		<table style='width: 100%;'>
			<caption>添加兑换码</caption>
            <tr>
                <th style='text-align: right;'><font color='red'>*</font> 礼包配置：</th>
                <td>
                    <select class="input" name="gifts" id="gifts" >
                        <option value="all">请选择礼包</option>
                        <?php foreach ($gifts as $key => $value):?>
                            <?php if (empty($value['isdel'])):?>
                                <option value="<?php echo $key;?>"><?php echo $value['name'];?></option>
                            <?php endif;?>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
			
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> 兑换码数量：</th>
				<td>
					<input type='text' id='count' name='count' value='1' />
					<label style='color:red'>最多6万个</label>
				</td>
			</tr>
			<tr><th></th><td><input data-button="button" type="submit" class="input" value="发送" /></td></tr>
		</table>
	</form>
	<hr />
    <hr />
    <div id='addDiv1' style="display: none">
		<table style='width: 100%;'>
			<caption>礼包配置</caption>
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> 礼包类型：</th>
				<td>
				    <span id="type"></span>
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> 礼包名称：</th>
				<td>
                    <span id="name"></span>
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			    <th style='text-align: right;'><font color='red'>*</font> 礼包key：</th>
				<td>
                    <span id="act_key"></span>
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			    <th style='text-align: right;'><font color='red'>*</font> 开始时间：</th>
				<td>
                    <span id="sTime"></span>
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			    <th style='text-align: right;'><font color='red'>*</font> 结束时间：</th>
				<td>
                    <span id="eTime"></span>
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			   <th style='text-align: right;'><font color='red'>*</font> 服务器区间：</th>
				<td>
                    <span id="sever">all</span>
					<label style='color:red'>全服:all 单服: 999 区间: 1,999</label>
				</td>
			</tr>
			<tr><th style='text-align: right;'><font color='red'>*</font> 礼包内容：</th>
				<td id="item">
                    <p><span style="width: 80%;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block" id='gif'></span></p>
				</td>
			</tr>
		</table>
</div>
<script type="text/javascript">
	$(function () {
            $(':input[data-button="button"]').click(function (e) {
                e.preventDefault();
                var gift = $('[name="gifts"]').val();
                if (gift == "all"){
                    alert('请选择礼包');
                    return false;
                }else {
                    $('#addForm').submit();
                    $(':input[data-button="button"]').attr('disabled',true);
                }
            });
			$(':input[name="add"]').click(function () {
				var item = $('[name="item"]').val();
				var num = $('[name="num"]').val();
				var item = '1-' + item + '-' + num;
				var arr = item.split('-');
				str = '<p><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>' + arr[2] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><!--<input class="input" type="button" value="删除" / >--></p>';
				$('#item').append(str);
			});
			$(':input[name="add_hero"]').click(function () {
				var item = $('[name="hero"]').val();
				var num = 1;
				var item = '2-' + item + '-' + num;
				var arr = item.split('-');
				str = '<p><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">伙伴 : </b>' + arr[2] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><!--<input class="input" type="button" value="删除" / >--></p>';
				$('#item').append(str);
			});
		})
</script>

<?php include(TPL_DIR.'footer.php');?>
<script>
	var giftsData = <?php echo json_encode($gifts);?>;
	
    $(document).ready(function(){
        $('[name="gifts"]').on('change',function () {
        	var data = giftsData[$('#gifts').val()];
        	console.log(data);
        	if($('#gifts').html() == 'all'){
        		$("#addDiv1").hide();
            }else{
            	$('#name').html(data.name);
            	$("#act_key").html($('#gifts').val());
            	if(data.type == 1){
            		$("#type").html('单用礼包-一个兑换码只能使用一次');
                }else if(data.type == 2){
                	$("#type").html('单服礼包-一个兑换码只能使用一次');
                }else if(data.type == 3){
                	$("#type").html('通用礼包-一个兑换码可以被多人使用');
                }else if(data.type == 4){
                	$("#type").html('多用礼包-一个玩家可以使用多个兑换码');
                }else if(data.type ==5){
                	$("#type").html('周礼包-一个玩家每周可以领取一次');
                }
        		$("#sTime").html(data.sTime);
        		$("#eTime").html(data.eTime);
        		$("#sever").html(data.sever);
        		$("#gif").html(data.items);
            	$("#addDiv1").show();
             }
        });
    });
</script>