<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 7;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<table class="mytable">
<tbody><tr>
<th>排名</th>
<th>名字</th>
<th>UID</th>
<th>身份</th>
<th>关卡</th>
    <th>操作</th>
</tr>
<?php foreach($guanka as $v){?>
<tr style="background-color:#f6f9f3;text-align: center;" >
<td><?php echo $v['rid']?></td>

<td style="text-align: left;"><a  class="aColor" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $v['uid']?>"><?php echo $v['name']?></a></td>
<td><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $v['uid']?>"><?php echo $v['uid']?></a></td>

<td><?php echo $v['level']?></td>
<td><?php echo $v['num']?></td>
<td><a class="delete" style="border-color: #92799a;background-color: #fcb9b4;" data-name="<?php echo $v['name']?>" data-id="<?php echo $v['uid']?>" href="">删除</a></td>
</tr>
<?php }?>
</tbody></table>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            var name_zh = $(this).data('name');
            var id = $(this).data('id');
            var type = 'guanka';
            var url = "admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=paihang&act=delete";
            layer.confirm('确认删除<span style="color: red;font-size: 12px;">'+name_zh+"</span>" , {
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