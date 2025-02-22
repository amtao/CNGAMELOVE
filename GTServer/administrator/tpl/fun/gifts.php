<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<div id='addDiv'>
	<form id='addForm' method='post' action=''>
		<table style='width: 100%;' class="mytable">
			<caption>礼包配置</caption>
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> 礼包类型：</th>
				<td>
					<select id='type' name='type'>
						<option value='1' selected>单用礼包</option>
                        <option value='3'>通用礼包</option>
                        <option value='4'>多用礼包</option>
                        <option value='5'>周礼包</option>
					</select>
                    <span style="color: red;">(单用礼包-一个兑换码只能使用一次; 通用礼包-一个兑换码可以被多人使用; 多用礼包-一个玩家可以使用多个兑换码;周礼包-一个玩家每周可以领取一次; )</span>
				</td>
			</tr>
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> 礼包名称：</th>
				<td>
					<input type='text' id='name' name='name' value='' />  
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			    <th style='text-align: right;'><font color='red'>*</font> 礼包key：</th>
				<td>
					<input type='text' id='act_key' name='act_key' style="background:#CCCCCC" value="<?php echo $key; ?>" />  <input name="add_key" class="input" type="button" value="生成" />
					<label style='color:red'>请点击生成不要手动输入</label>
				</td>
			</tr>
			<tr>
			    <th style='text-align: right;'><font color='red'>*</font> 开始时间：</th>
				<td>
                    <input class='Wdate' id='keyword5' type='text'  name='sTime' value='' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true})" />
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			    <th style='text-align: right;'><font color='red'>*</font> 结束时间：</th>
				<td>
                    <input class='Wdate' id='keyword6' type='text' name='eTime' value='' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01'})" />
					<label style='color:red'></label>
				</td>
			</tr>
			<tr>
			   <th style='text-align: right;'><font color='red'>*</font> 服务器区间：</th>
				<td>
					<input type='text' id='sever' name='sever' value='all' />
					<label style='color:red'>全服:all 单服: 999 区间: 1,999</label>
				</td>
			</tr>
			<tr>
				<th style='text-align: right;'><font color='red'>*</font> 礼包选项：</th>
				<td>
					道具 : <select class="input" name="item">
						<?php foreach ($item as $key => $value):?>
							<option value="<?php echo $key.'-'.$value['name']; ?>"><?php echo $key.' - '.$value['name_cn']; ?></option>
						<?php endforeach;?>
					</select>
					<input name="num" class="input" value="1" />
					<input name="add" class="input" type="button" value="添加" /></br>
                    服装 : <select class="input" name="clothe" style="width:153px ">
                        <?php foreach ($clothe as $key => $value):?>
                            <option value="<?php echo $value['id'].'-'.$value['name']; ?>"><?php echo $value['id'].' - '.$value['name']; ?></option>
                        <?php endforeach;?>
                    </select>
                    <input name="cnum" class="input" value="1" />
                    <input name="add_clothe" class="input" type="button" value="添加" /></br>
                    头像框 : <select class="input" name="blank" style="width:153px ">
                        <?php foreach ($blank as $key => $value):?>
                            <option value="<?php echo $value['id'].'-'.$value['name']; ?>"><?php echo $value['id'].' - '.$value['name']; ?></option>
                        <?php endforeach;?>
                    </select>
                    <input name="cnum" class="input" value="1" />
                    <input name="add_blank" class="input" type="button" value="添加" /></br>
					伙伴 : <select class="input" name="hero">
						<?php foreach ($hero as $key => $value):?>
							<option value="<?php echo $key.'-'.$value['name']; ?>"><?php echo $key.' - '.$value['name']; ?></option>
						<?php endforeach;?>
					<input name="add_hero" class="input" type="button" value="添加" />
				</td>
			</tr>
			<tr><th style='text-align: right;'><font color='red'>*</font> 礼包内容：</th>
				<td id="item">
                    
				</td>
			</tr>
			<tr><th></th><td><input type="submit" class="input" value="提交" /></td></tr>
		</table>
	</form>
	<hr />
    <table style='width: 100%;' class="mytable">
        <caption>礼包详情</caption>
        <tr>
            <th style='text-align: center;'><font color='red'>*</font> 礼包名称：</th>
            <th style='text-align: center;'><font color='red'>*</font> 礼包key：</th>
            <th style='text-align: center;'><font color='red'>*</font> 礼包类型：</th>
            <th style='text-align: center;'><font color='red'>*</font> 类型说明：</th>
            <th style='text-align: center;'><font color='red'>*</font> 适用区间：</th>
            <th style='text-align: center;'><font color='red'>*</font> 生效时间：</th>
            <th>
                <font color='red'>*</font> 礼包数据：
            </th>
            <th>
                <font color='red'>*</font> 操作：
            </th>
        </tr>
        <?php foreach ($data as $key => $value):?>
        <?php if(empty($value['isdel'])):?>
        <tr>
            <td style="width: 100px;text-align: center;"><?php echo $value['name']; ?></td>
            <td style="width: 100px;text-align: center;">
                <?php
                echo $key;
                ?></td>
            <td style="width: 100px;text-align: center;">
            <?php
               switch($value['type']){
                   case 1:
                       echo '<span style="color: #ea1256;">单用礼包</span>';
                       break;
                   case 2:
                       echo '<span style="color: #2A8CD4">单服礼包</span>';
                       break;
                   case 3:
                       echo '<span style="color: #0E0EF6">通用礼包</span>';
                       break;
                   case 4:
                       echo '<span style="color: #9a121e">多用礼包</span>';
                       break;
                   case 5:
                       echo '<span style="color: #b00ae6">周礼包</span>';
                       break;
               }
            ?></td>
            <td style="width: 180px;text-align: center;">
                <?php
                switch($value['type']){
                    case 1:
                        echo '<span style="color: #ea1256;">一个兑换码只能使用一次</span>';
                        break;
                    case 3:
                        echo '<span style="color: #0E0EF6">一个兑换码可以被多人使用</span>';
                        break;
                    case 4:
                        echo '<span style="color: #9a121e">一个玩家可以使用多个兑换码</span>';
                        break;
                    case 5:
                        echo '<span style="color: #b00ae6">一个玩家每周可以领取一次</span>';
                        break;
                }
                ?></td>

            <td style="text-align: center;">
            <?php
               if($value['sever'] == all){
                   echo '全服';
               }else{
                   $start = intval(substr($value['sever'],4,3));
                   $end = intval(substr($value['sever'],1,3));
                   if($end == 0){
                       echo $start;
                   }else{
                       echo $start.','.$end;
                   } 
               }
            ?></td>
            <td style="text-align: center;">
            <?php
               echo date('Y-m-d H:i:s',$value['sTime']).'--'.date('Y-m-d H:i:s',$value['eTime']);
            ?></td>
            <td>
               <?php 
                    foreach ($value['items'] as $k => $v){
                        echo '<span style="border:rosybrown solid 1px;margin:0 2px;padding:0px 3px;background-color: #F6E3DD">';
                        if ($v['kind'] == 7){
                            echo $hero[$v['id']]['name'];
                        }else if($v['kind'] == 95){
                            echo $clothe[$v['id']]['name'];
                        }else if($v['kind'] == 94){
                            echo $blank[$v['id']]['name'];
                        }else{
                            echo $item[$v['id']]['name'];
                        }
                        echo ' | '.$v['count'].'</span>';
                    }
                ?>
            </td>
            <td style="width: 100px;text-align: center;"><a class="delete" href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=gifts&type=delete&key=<?php echo $key;?>">删除</a></td>
        </tr>
        <?php endif;?>
        <?php endforeach;?>
    </table>
</div>
<script type="text/javascript">
	$(function () {
			$(':input[name="add"]').click(function () {
                var rand = Math.random()*Math.random();
				var item = $('[name="item"]').val();
				var num = $('[name="num"]').val();
				var item = '1-' + item + '-' + num;
				var arr = item.split('-');
				str = '<p data-number="'+rand+'"><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>' + arr[2] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><input class="input" onclick="del('+rand+')" type="button" value="删除" / ></p>';
				$('#item').append(str);
			});
			$(':input[name="add_hero"]').click(function () {
                var rand = Math.random()*Math.random();
				var item = $('[name="hero"]').val();
				var num = 1;
				var item = '2-' + item + '-' + num;
				var arr = item.split('-');
				str = '<p data-number="'+rand+'"><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">伙伴 : </b>' + arr[2] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><input class="input" onclick="del('+rand+')" type="button" value="删除" / ></p>';
				$('#item').append(str);
			});
            $(':input[name="add_clothe"]').click(function () {
                var rand = Math.random()*Math.random();
                var item = $('[name="clothe"]').val();
                var num = 1;
                var item = '3-' + item + '-' + num;
                var arr = item.split('-');
                str = '<p data-number="'+rand+'"><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">服饰 : </b>' + arr[2] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><input class="input" onclick="del('+rand+')" type="button" value="删除" / ></p>';
                $('#item').append(str);
            });
            $(':input[name="add_blank"]').click(function () {
                var rand = Math.random()*Math.random();
                var item = $('[name="blank"]').val();
                var num = 1;
                var item = '4-' + item + '-' + num;
                var arr = item.split('-');
                str = '<p data-number="'+rand+'"><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">头像框 : </b>' + arr[2] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><input class="input" onclick="del('+rand+')" type="button" value="删除" / ></p>';
                $('#item').append(str);
            });
			$(':input[name="add_key"]').click(function(){
				$.ajax({
		            type:"POST",
		            url:'?sevid=<?php echo $_GET['sevid'];?>&mod=fun&act=checkKeyAjax',
		            dataType:"json",
		            async:false,
		            success:function(msg){
		                if(msg){
			                $("#act_key").val(msg);
		                }else{
		                    alert("获取失败");
		                }
		            },
		        });
			});
            $(".delete").on('click',function (e) {
                e.preventDefault();
                if (confirm('确定要删除吗?')){
                    var href = $(this).prop('href');
                    window.location.href = href;
                }else{
                    return false;
                }
            });
		});
    function del(rand){
        if (confirm('确认删除?')){
            $('[data-number="'+rand+'"]').remove();
        }else {
            return false;
        }
    }
</script>

<?php include(TPL_DIR.'footer.php');?>
