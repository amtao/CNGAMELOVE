<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 7;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="form1" method="post" action="">
<p>区服：
<select name="serverid" id="serverid">
     <?php foreach ($serverList as $key => $value):?>
         <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
     <?php endforeach;?>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="查找" /></p>
</form>

<hr class="hr" />
<table class="mytable">
<tbody><tr>
<th>排名</th>
<th>宫殿ID</th>
<th>宫殿名</th>
<th>宫殿会长名</th>
<th>宫殿等级</th>
<th>宫殿总势力</th>
<th>宫殿资金</th>
<th>宫殿人数</th>
<th>操作</th>
</tr>
<?php foreach($gonghui as $v){?>
<tr style="background-color:#f6f9f3;text-align: center;">
<td><?php echo $v['rid']?></td>
<td><?php echo $v['id']?></td>
<td style="text-align: left;"><a class="aColor" href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=paihang&act=catgh&cid=<?php echo $v['id'];?>"><?php echo $v['name']?></a></td>
<td><?php echo $v['membersDz']?></td>
<td><?php echo $v['level']?></td>
<td><?php echo $v['allShiLi']?></td>
<td><?php echo $v['fund']?></td>
<td><?php echo $v['membersSl']?></td>
    <td><a class="delete" style="border-color: #92799a;background-color: #fcb9b4;" data-name="<?php echo $v['name']?>" data-id="<?php echo $v['id']?>" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=paihang&act=gonghui&id=<?php echo $v['id']?>">删除</a></td>
</tr>
<?php }?>
</tbody></table>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            var name_zh = $(this).data('name');
            var id = $(this).data('id');
            var type = 'gonghui';
            var url = "admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=paihang&act=delete";
            layer.confirm('确认删除<span style="color: red;font-size: 12px;">'+name_zh+"</span>", {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "id="+id+"&type="+type,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,1000);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
    });
</script>